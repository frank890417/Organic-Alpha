<?
	class organic_sql{
		public $input;
		function exec(){
				//連結資料庫 
			include("conn.php");
			$sql_data= $this->input;
			//echo $input;
			$sql_raw=json_decode($sql_data, true);
			$sqlinput=$sql_raw["sql"];
			//echo "Input sql: ". $this->sqlinput."<br>";

			//準備回傳的結果
			$result="";
			//進行資料庫操作
			$rs=mysql_query($sqlinput);
			$num_rows=mysql_num_rows($rs);
			//長度資訊
			$result.="{\"length\":$num_rows,\"datas\":[";


			for($i=0;$i<$num_rows;$i++){
				$result_array=mysql_fetch_row($rs);
				$result.="{";
				$colcount=count($result_array);

				//加入每一筆資料
				for($o=0;$o<$colcount;$o++){
					$ffname=mysql_field_name($rs, $o);
					$result.="\"$ffname\":\"".$result_array[$o]."\"";

					if ($o!=$colcount-1){
						$result.=",";
					}
				}

				$result.="}";
				if ($i!=$num_rows-1) $result.=",";

			}

			$result.="]}";
			return  $result;
		}
	}

	class organic_injector{
		public $input;
		function exec(){
			$inject_data=$this->input;

			//echo "inject_data:".$inject_data."<br><br>";
			
			$inject_raw=json_decode($inject_data, true);
			$inject_target=$inject_raw["inject_target"];
			$inject_list=$inject_raw["inject_list"];

			//echo "inject target:".$inject_target;
			$web_raw_data=file_get_contents($inject_target);

			//echo "<br><br><br>inject:<br>";
			//var_dump($inject_raw);
			//echo "<br><br><br>";

			for($i=0;$i<count($inject_list);$i++){

				$string  = $web_raw_data;
				$needle  = "%".$inject_list[$i]["tag"]."%";
				$replace = $inject_list[$i]["data"];

				//echo $needle."->".$replace."<br>";
				$web_raw_data  = str_replace($needle, $replace, $string);
			}


			return $web_raw_data;
		}
	}

	class organic_multiplexer{
		public $input;
		function exec(){
			$raw_data=$this->input;
			$raw_json=json_decode($raw_data,true);

			$target_html=$raw_json["target"];
			$multiplex_count= $raw_json["datas"]["length"];

			//echo $raw_data;
			$result="";

			for($i=0;$i<$multiplex_count;$i++){
				$datarow=$raw_json["datas"]["datas"][$i] ;

				$injecter_data="[";

				$first=true;
				foreach ($datarow as $name => $value) {
					if ($first)
						$first=false;
					else
						$injecter_data.=",";

					$injecter_data.="{\"tag\":\"$name\",\"data\":\"$value\"}";
				}

				
				$injecter_data.="]";
				//echo $injecter_file."?data=".$injecter_data."<br>";
				//echo "<br><br>inject_data:<br>";
				//echo $injecter_data;
				//echo "<br><br>";
				$result.= injector($target_html,$injecter_data);

			}

			return $result;
		}
	}

	function sql($sql_temp){
		$unit = new organic_sql;
		$data="{\"sql\":\"$sql_temp\"}";
		$unit->input=$data;
		return $unit->exec(); 
	}

	function multiplexer($target,$data){
		$unit = new organic_multiplexer;
		$data="{\"target\":\"$target\",\"datas\":".$data."}";
		$unit->input=$data;
		return $unit->exec(); 
	}

	function injector($target,$data){
		$unit = new organic_injector;
		$data="{\"inject_target\":\"$target\",\"inject_list\":".$data."}";
		$unit->input=$data;
		return $unit->exec(); 
	}


	$sql="SELECT * FROM message where id = 1 or id = 2";
	$sqlresult= sql($sql);
	//echo $sqlresult;

	$inject_data="{\"length\":5,\"datas\":[{\"tag\":\"content\",\"data\":\"".htmlentities(str_replace("\n","",multiplexer("testinject.html",$sqlresult)))."\"}]}";
	echo $inject_data;
	echo injector("frame.html",$inject_data);


	

?>