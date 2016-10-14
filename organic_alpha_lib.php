<?
	class organic_sql{
		public $sql;
		function __construct($sql){
			$this->sql=convert_raw($sql);
		}
		function exec(){
			//連結資料庫 
			include("conn.php");
			//準備回傳的結果
			$result=[];
			//進行資料庫操作
			$rs=mysql_query($this->sql);
			if ($rs){
				$num_rows=mysql_num_rows($rs);
				//長度資訊
				for($i=0;$i<$num_rows;$i++){
					$result_array=mysql_fetch_row($rs);
					$colcount=count($result_array);

					//加入每一筆資料
					for($o=0;$o<$colcount;$o++){
						$ffname=mysql_field_name($rs, $o);
						$result[$i]["$ffname"]=$result_array[$o];
					}

				}
			}else{
				$result=null;
			}

			return (new organic_source("array", $result));
		}
	}

	function convert_raw($rawdata){
		if (gettype($rawdata)=="object")
			//if (get_class($rawdata)=="organic_source")
				return $rawdata->exec();	
				
		return $rawdata;
	}

	class organic_injector{
		public $target;
		public $datas;
		function __construct($target,$datas){
			$this->target=convert_raw($target);
			$this->datas=convert_raw($datas);
		}
		function add_filter($newtag,$newdata){
			$this->datas[$newtag]=$newdata;
		}
		function merge_filter($newdata){
			foreach ($newdata as $name => $value) {
				$datas[$name]=$value;
			}
		}
		function exec(){
			$inject_target=$this->target;
			$inject_list=$this->datas;
			$web_raw_data=($inject_target);
			if($inject_list){
				foreach ($inject_list as $name => $value) {
					$string  = $web_raw_data;
					$needle  = "[%".$name."%]";
					$replace = $value;
					$web_raw_data  = str_replace($needle, $replace, $string);
				}
			}
			return (new organic_source ("text",$web_raw_data));
		}
		function make_inj($injkey){
			return $this->exec()->make_inj($injkey);
		}
	}

	class organic_multiplexer{
		public $target;
		public $datas;
		public $results;
		public $dynamic_key;
		function __construct($target,$datas){
			$this->target=convert_raw($target);
			$this->datas=convert_raw($datas);
		}
		function exec(){
			$target_html=$this->target;
			$datas=$this->datas;
			$multiplex_count= count($datas);
			$this->results=[];

			foreach ($datas as $key => $datarow) {
				if ($this->dynamic_key){
					$newkey=(new organic_injector($this->dynamic_key,$datarow))->exec()->exec();
					$this->results[$newkey]=(new organic_injector($target_html,$datarow))->exec()->exec();
				}
				else
					$this->results[$key]=(new organic_injector($target_html,$datarow))->exec()->exec();
			}
			return (new organic_source ("array",$this->results));
		}
		function combine_text(){

			return (new organic_source ("text",($this->exec()->combine())));
		}
		function set_key($key){
			$this->dynamic_key=$key;
		}
		function set_data($datas){
			$this->datas=$datas;
			return $this;
		}
		function sql(){
			$this->results=[];
			$this->exec();

			$sql_results=[];

			foreach ($this->results as $key => $datarow) {
				$sql_results[$key]=(new organic_source ("sql",$datarow))->exec();
			}

			return new organic_source ("array",$sql_results);
		}


	}

	class organic_source{
		public $type;
		public $data;
		function __construct($type,$data){
			$this->type=convert_raw($type);
			$this->data=convert_raw($data);
		}
		function exec(){
			if ($this->type=="organic"){
				
			}
			if ($this->type=="array"){
				return $this->data;
			}
			if ($this->type=="file"){
				return file_get_contents($this->data);
			}
			if ($this->type=="text"){
				return $this->data;
			}
			if ($this->type=="sql"){
				return (new organic_sql($this->data))->exec()->exec();
			}
			if ($this->type=="post"){
				return $_POST[$this->data];
			}
			if ($this->type=="get"){
				return $_GET[$this->data];
			}
			if ($this->type=="number"){
				return $this->data;
			}
			if ($this->type=="injectarray"){
				return $this->data;
			}

			if ($this->type=="cdn"){
				
				if (($this->data)=="jquery" || ($this->data)=="toolbox"){
					$resultcdn.= "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js\"></script>";
				}
				if (($this->data)=="jquery-ui" || ($this->data)=="toolbox"){
					$resultcdn.= "<script type=\"text/javascript\" src=\"http://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js\"></script>";
				}
				if (($this->data)=="bootstrap" || ($this->data)=="toolbox"){
					$resultcdn.= "<link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css\" integrity=\"sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7\" crossorigin=\"anonymous\">
<script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js\" integrity=\"sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS\" crossorigin=\"anonymous\"></script>";
				}
				return $resultcdn;
			}
			if ($this->type=="inject"){
				return $this->data;
			}
			if ($this->type=="output"){
				return $this->data;
			}
			if ($this->type=="input"){
				return $this->data;
			}
			if ($this->type=="counter"){
				$return_counter=[];
				for($i=1;$i<=$this->data;$i++){
					$return_counter[$i]=$i;
				}
				return $return_counter;
			}
		}
		function to_array(){
			if ($this->type=="counter"){
				return new organic_source("array",$this->exec());
			}
			return null;
		}
		function to_text(){
			if ($this->type=="file"){
				return new organic_source("text",file_get_contents($this->data));
			}
			if ($this->type=="post"){
				return new organic_source("text", $_POST[$this->data]);
			}
			if ($this->type=="get"){
				return new organic_source("text", $_GET[$this->data]);
			}
			if ($this->type=="cdn"){
				return new organic_source("text", $this->exec());
			}
			return null;
		}


		function type(){
			return new organic_source("text",$this->type);
		}
		function length(){
			return new organic_source ("number",count($this->data));
		}

		function table(){
			if ($this->type=="array"){
				$rowhtmldata="";
				foreach (($this->data[0]) as $name => $value)
					$rowhtmldata.="<td>[%".$name."%]</td>";
				$tablehhtml="<table><tr>".$rowhtmldata."</tr>".multiplexer("<tr>".$rowhtmldata."</tr>",$this->data)."</table>";
				return (new organic_source("text",$tablehhtml));
			}else{
				return "not valid type table";
			}
		}
		function combine(){
			$result="";
			foreach (($this->data) as $name => $value)
				$result.=$value;
			return new organic_source("text",$result);
			
		}
		function fetch($num){
			if ($this->type=="array"){
				return $this->data[$num];
			}else{
				return "not valid type table";
			}
		}
		function make_inj($needle){
			$dd=[];
			$dd[$needle]=$this->data;
			return new organic_source("inject",$dd);
		}
		function make_array_inj($needle){
			$dd=[];
			foreach (($this->data) as $name => $value)
				$dd[$name][$needle]=$this->data[$name];
			return new organic_source("array",$dd);
		}
		function add_inj($needle,$data){
			$this->data[$needle]=$data;
			return $this;
		}

	}

	class organic_switcher{
		public $judge;
		public $data1;
		public $data2;
		function __construct($judge=0,$data1=null,$data2=null){
			$this->judge=convert_raw($judge);
			$this->data1=$data1;
			$this->data2=$data2;
		}
		function set_judge($judge){
			$this->judge=$judge;
		}
		function set_data1($data1){
			$this->data1=$data1;
		}
		function set_data2($data2){
			$this->data2=$data2;
		}
		function exec(){
			if ($this->judge!=0 && $this->judge!="0")
				return $this->data1;
			else
				return $this->data2;	
		}
	}

	class organic_modifier{
		public $data1;
		public $data2;
		function __construct($data1=null,$data2=null){
			$this->data1=$data1;
			$this->data2=$data2;
		}
		function set_judge($judge){
			$this->judge=$judge;
		}
		function set_data1($data1){
			$this->data1=$data1;
		}
		function set_data2($data2){
			$this->data2=$data2;
		}
		function exec(){
			if ($this->judge!=0 && $this->judge!="0")
				return $this->data1;
			else
				return $this->data2;	
		}
	}

	function sql($sql_temp){
		$unit = new organic_sql($sql_temp);
		return $unit->exec()->exec(); 
	}

	function multiplexer($target,$data){
		$unit = new organic_multiplexer($target,$data);
		return $unit->exec()->combine()->exec(); 
	}

	function injector($target,$data){
		$unit = new organic_injector($target,$data);
		return $unit->exec()->exec(); 
	}

	function injector_tag($target,$tag,$data){
		$injectdata[$tag]=$data;
		$unit = new organic_injector($target,$injectdata);
		return $unit->exec(); 
	}

	function switcher($judge,$data1,$data2){
		$unit = new organic_switcher($judge,$data1,$data2);
		return $unit->exec(); 
	}

	function source($type,$data){
		$unit = new organic_source($type,$data);
		return $unit; 
	}



?>