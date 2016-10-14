

<html>
		<head>
			<meta charset="utf-8"> 
			<title> Organic 編譯器 v0.55</title>
			<link rel="shortcut icon" href="http://www.monoame.com/organic_alpha/organic.ico">
			<link rel="stylesheet" href="http://www.monoame.com/organic_alpha/organic_script.css">
 		<script type="text/javascript" src="http:///cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		</head>
		<body>
			<h2 style="color: white;margin-top: 20px;"> <a href='index.php'><img src="logouse-02.png" height="60px" /></a> Compiler v 0.55</h2>
			<form method='get'style='display:inline;'>
		    <select name="file"  value=<?php  $file=(isset($_GET["file"])?$_GET["file"]:"script1.organic" ) ; echo $file;?> >
			<?php
				$dir    = './';
				$files1 = scandir($dir);

				foreach ($files1 as $key => $value) {
					if (substr($value,-strlen(".organic"))==".organic"){
						echo "<option ".($value==$file?"selected":"")." value='$value'>$value</option>";
					}
				}

			?>
		</select>
		<input  style='display:inline;' type='submit' value="開啟"/>
		<p style='color:white; display:inline;'>現在開啟: <?php echo $file; ?>

	</form> 
	<form  method='get' action='organic_editor.php' target="_blank">
		
		<input style='display:none;' name='file' value=<?php echo $file;?>  />
		<input type='submit' value="編輯" />

	</form> 
	<form  method='get' action='organic_graphic.php' target="_blank">
		
		<input style='display:none;' name='file' value=<?php echo $file;?>  />
		<input type='submit' value="圖形分析" />

	</form> 


<?php

	$parser_code="";
	$elements=[];
	$filelist=[];
	$outputvarlist=[];
	$filecount=0;
	$outputcount=0;

	//設定特定文字顏色
	function html_organic_utility($ss,$prefix=""){
		$ss=str_replace("\n", "<br>" ,htmlentities($ss));
		//$ss=setcolor("exec()","#989", $ss);
		$ss=setcolor($prefix."multiplexer","#3f3", $ss);
		$ss=setcolor("combine_text()","#5f5", $ss);
		$ss=setcolor("set_key","#5f5", $ss);
		//$ss=setcolor("sql()","#5f5", $ss);

		$ss=setcolor($prefix."injector","#ff6", $ss);
		$ss=setcolor("add_filter","#ff8", $ss);
		$ss=setcolor("make_inj","#ff8", $ss);

		$ss=setcolor($prefix."switcher","#6af", $ss);

		$ss=setcolor($prefix."source","#f33", $ss);
		//$ss=setcolor("length()","#f55", $ss);
		//$ss=setcolor("combine()","#f55", $ss);
		$ss=setcolor("to_text()","#f55", $ss);
		$ss=setcolor(" text ","#f55", $ss);
		$ss=setcolor(" sql ","#f55", $ss);
		$ss=setcolor(" counter ","#f55", $ss);
		$ss=setcolor(" array ","#f55", $ss);
		$ss=setcolor(" number ","#f55", $ss);
		$ss=setcolor(" post ","#f55", $ss);
		$ss=setcolor(" get ","#f55", $ss);
		$ss=setcolor(" file ","#f55", $ss);
		$ss=setcolor(" output ","#f55", $ss);

		$ss=setcolor("&quot;text&quot;","#f55", $ss);
		$ss=setcolor("&quot;sql&quot;","#f55", $ss);
		$ss=setcolor("&quot;counter&quot;","#f55", $ss);
		$ss=setcolor("&quot;array&quot;","#f55", $ss);
		$ss=setcolor("&quot;number&quot;","#f55", $ss);
		$ss=setcolor("&quot;post&quot;","#f55", $ss);
		$ss=setcolor("&quot;get&quot;","#f55", $ss);
		$ss=setcolor("&quot;file&quot;","#f55", $ss);
		$ss=setcolor("&quot;output&quot;","#f55", $ss);


		$ss=setbackcolor("echo","#aaa", $ss);
		$ss=setbackcolor("set","#aaa", $ss);
		$ss=setbackcolor("equal","#aaa", $ss);
		$ss=setbackcolor("applychild","#aaa",$ss);
		return $ss;
	}

	//設定文字顏色
	function setcolor($identifier,$color,$source){
		$symbol="";$tagtext="";
		if (strpos($identifier,"source")!==false){
			$symbol="⊙";
			$tagtext="資料來源: 名稱/種類/資料";
		}else if (strpos($identifier,"injector")!==false){
			$symbol="▽";
			$tagtext="注入器: 名稱/注入目標/標的對應資料";
		}else if (strpos($identifier,"multiplexer")!==false){
			$symbol="∈";
			$tagtext="多工器: 名稱/注入目標/標的對應陣列資料";
		}else if (strpos($identifier,"switcher")!==false){
			$symbol="◈";
			$tagtext="選擇器: 名稱/判斷基準/成立結果/不成立結果";
		}else if (strpos(trim($identifier),"sql")!==false && strpos(trim($identifier),"sql()")===false){
			$symbol="Ð";
			$tagtext="source-sql: 執行從DB存取資料";
		}else if (strpos(trim($identifier),"post")!==false){
			$symbol="︿";
			$tagtext="source-post: 執行後從網頁$_POST存取資料";
		}else if (strpos(trim($identifier),"get")!==false){
			$symbol="﹀";
			$tagtext="source-get: 執行後從網頁$_GET存取資料";
		}else if (strpos(trim($identifier),"text")!==false){
			$symbol="▦";
			$tagtext="source-text: 執行後是純文字資料";
		}else if (strpos(trim($identifier),"array")!==false  && strpos(trim($identifier),"to_array()")===false){
			$symbol="〣";
			$tagtext="source-array: 執行後是純陣列資料";
		}else if (strpos(trim($identifier),"number")!==false){
			$symbol="Φ";
			$tagtext="source-number: 執行後是純數字資料";
		}else if (strpos(trim($identifier),"file")!==false){
			$symbol="⌸";
			$tagtext="source-file: 執行後從檔案存取資料";
		}
		return str_replace($identifier, " <span  style='color: $color;' ".($tagtext!=""?"title= '".$tagtext."' ":"")."> ".$symbol.$identifier." </span >",$source);
	}

	//用到的js函數
	echo "<script>
		keepname='nothing';
		function highlight(element){
			$('.'+$(element).attr('class').split(' ')[0]).addClass('highlight');
		}
		function unhighlight(element){
			$('.'+$(element).attr('class').split(' ')[0]).removeClass('highlight');
			$(keepname).addClass('highlight2');
		}
		function keephighlight(element){
			if (keepname=='.'+$(element).attr('class').split(' ')[0]){
				keepname='nothing';
				$('.'+$(element).attr('class').split(' ')[0]).removeClass('highlight2');
			}else{
				$(keepname).removeClass('highlight2');
				keepname='.'+$(element).attr('class').split(' ')[0];
			}
		}
		function openfilebox(element){
			$('.'+$(element).attr('class').split(' ')[0]).addClass('showfilebox');
		}
		function closefilebox(element){
			$('.'+$(element).attr('class').split(' ')[0]).removeClass('showfilebox');
		}
	</script>";

	//取代元素名稱使其可以顯示型別
	function setelement($identifier,$type,$source){
		return str_replace($identifier, " <span onclick='keephighlight(this);' onmouseover='highlight(this);' onmouseout='unhighlight(this);' class='element_".substr($identifier,1)." element' > ".$identifier."<p class=elementinfo>".$type."</p> </span >",$source);
	}

	//設定有背景顏色的文字
	function setbackcolor($identifier,$color,$source){
		return str_replace($identifier, " <span style='padding-left:5px;padding-right:5px;background-color: $color;color:#111;' > ".$identifier." </span >",$source);
	}

	//設定注入點的顏色
	function setinjectnodecolor($source){
		$subject = $source;
		$pattern = '/(\[\%[a-zA-Z\_]{1,50}\%\])/i';
		preg_match_all($pattern, $subject, $matches);
		//var_dump( $matches);
		foreach ( $matches[0] as $name => $value) {
			$string  = $subject;
			$needle  = $value;
			$replace = "<span class=injectnode  title='"."注入點".str_replace("%", "" , $value)."' >".str_replace("%", "" , $value)."</span>";
			$subject  = str_replace($needle, $replace, $string);
		}
		return $subject;
	}

	//放置檔案預覽
	function setfilepreview($filelist,$source){
		$tempss=$source;
		
		foreach ($filelist as $key => $value) {
			$classname='file'.str_replace(".","_",$value);
			$tempss=str_replace("&quot;".$value."&quot;", " <a target='_blank' style='position:relative;' onmouseover='openfilebox(this);' onmouseout='closefilebox(this);' href='organic_viewer.php?file=".$value."' id='file_".$value."' class='".$classname." file' style=' display: inline;' > &quot;".$value."&quot; <p class=filebox> ".setinjectnodecolor(htmlentities(file_get_contents($value)))."</p> </a>",$tempss);
			
		}
		return $tempss;
	}

	//為特定函數標註顏色
	function mark_function_new($source){
		$subject = $source;
		$pattern = '~([a-z0-9A-Z_/"]*\([a-z0-9A-Z_/"]*\))~';
		preg_match_all($pattern, $subject, $matches);
		//var_dump( $matches);
		$color="#fff";
		$colorset["exec"]="#989";
		$colorset["combine_text"]="#5f5";
		$colorset["sql"]="#5f5";
		$colorset["length"]="#5f5";
		$colorset["combine"]="#f55";
		$colorset["to_text"]="#f55";
		$colorset["make_inj"]="#ff8";
		$colorset["set_key"]="#5f5";
		$colorset["set_data"]="#5f5";
		
		foreach ( $matches[0] as $name => $value) {
			foreach ($colorset as $colortar => $cc) {
				if (strpos($value,$colortar)!==false){
					$color=$cc;
				}
			}
			
			//echo $value;
			$string  = $subject;
			$needle  = $value;
			$replace = "<span style='font-style: italic;color: $color'>".str_replace("%", "" , $value)."</span>";
			$subject  = str_replace($needle, $replace, $string);
		}
		return $subject;
	}

	//文字優先順序的split
	function spliter($delimiter,$line){
		$ll=strlen($line);
		$result_array=[];
		$temp="";
		$flag_slash = false;
		$inflag_dq = false;
		$inflag_pare = false;

		
		for($ind=0;$ind<$ll;$ind++){
			$cur_char=$line[$ind];
			//echo "nowchar: $cur_char <br>";
			if ($flag_slash){
				$temp.=$cur_char;
				$flag_slash=false;
			}else if ($cur_char=='\\'){
				$temp.='\\';
				$flag_slash=true;
			}else if ($cur_char=='"'){
				$temp.='"';
				if ($inflag_dq) {
					$inflag_dq=false;
				}else
					$inflag_dq=true;
			}else if (($cur_char=='(' || $cur_char==')') && $inflag_dq==false){
				if ($cur_char=='('){
					$temp.='(';
					$inflag_pare =true;
				}else if ($cur_char==')'){
					$temp.=')';
					$inflag_pare =false;
				}
			}else if ($cur_char==$delimiter && $inflag_dq==false && $flag_slash==false && $inflag_pare==false){
				if (trim($temp)!=""){
					$temp=trim($temp);
					array_push($result_array,$temp);
				}
				$temp="";
			}else{
			
				$temp.=$cur_char;

				
			}
		}
		if (trim($temp)!=""){
					$temp=trim($temp);
					array_push($result_array,$temp);
		}
		$temp="";
		//var_dump($result_array);
		return $result_array;

	}

	//解析程式碼
	function parse($delimiter,$line){
		$ll=strlen($line);
		$result_array=[];
		$temp="";
		$flag_slash = false;
		$inflag_dq = false;
		$inflag_pare = false;
		
		for($ind=0;$ind<$ll;$ind++){
			$cur_char=$line[$ind];
			//echo "nowchar: $cur_char <br>";
			if ($flag_slash){
				$temp.=$cur_char;
				$flag_slash=false;
			}else if ($cur_char=='\\'){
				$temp.='\\';
				$flag_slash=true;
			}else if ($cur_char=='"'){
				$temp.='"';
				if ($inflag_dq) {
					$inflag_dq=false;
				}else
					$inflag_dq=true;
			}else if (($cur_char=='(' || $cur_char==')') && $inflag_dq==false){
				if ($cur_char=='('){
					$temp.='(';
					$inflag_pare =true;
				}else if ($cur_char==')'){
					$temp.=')';
					$inflag_pare =false;
				}
			}else if ($cur_char==$delimiter && $inflag_dq==false && $flag_slash==false && $inflag_pare==false){
				if (trim($temp)!=""){
					$temp=trim($temp);
					//echo $temp[0]=='"' ;
					if ($temp[0]=='$' && count($result_array)!=1 && count($result_array)!=0 ){
						if (explode("[",$temp)[1]){
							$temp="".explode("[",$temp)[0]."->exec()[".explode("[",$temp)[1];
						}
						else{
							$temp="".$temp."->exec()";
						}
						
					}
					array_push($result_array,$temp);
				}
				$temp="";
			}else{
			
				$temp.=$cur_char;

				
			}
		}
		if (trim($temp)!=""){
			$temp=trim($temp);
			//echo $temp[0]=='"' ;
			if ($temp[0]=='$' ){
				if (explode("[",$temp)[1]){
					$temp="".explode("[",$temp)[0]."->exec()[".explode("[",$temp)[1];
				}
				else{

					
					$temp="".$temp."->exec()";
				}
				
			}
			array_push($result_array,$temp);
		}
		$temp="";
		//var_dump($result_array);
		return $result_array;
	}

	//編譯傳入的所有原始程式
	function compile_all($script){
		$scriptline=spliter(";", $script);
		$temp_compiled_code="";
		for($i=0;$i<count($scriptline);$i++){
			$temp_compiled_code.=compileline($scriptline[$i],"");
		}
		return $temp_compiled_code;
	}

	//編譯單行
	function compileline($currentline,$nameprefix){
		global $outputvarlist,$parser_code,$filelist,$filecount,$outputcount,$elements;

  		$current_element=parse(' ',$currentline);
		$type=trim($current_element[0]);
		$input1=trim($current_element[1]);
		$input2=trim($current_element[2]);
		$input3=trim($current_element[3]);
		$input4=trim($current_element[4]);
		$compiled_code_line="";



		if ($type=="source"){
			$compiled_code_line.="".$nameprefix.$input1." = new organic_source(\"".$input2."\",".$input3.")";
			$elements[$input1]["unit"]="unit";
			$elements[$input1]["type"]="source";
			$elements[$input1]["datatype"]=$input2;
			if ($input2=="file"){
				$filelist[$filecount++]=substr($input3,1,-1);
			}
			if ($input2=="output"){
				$outputvarlist[$outputcount++]=($input1);
			}
		}else
		if ($type=="injector"){
			$compiled_code_line.="".$input1." = new organic_injector(".$input2.",".$input3.")";
			$elements[$input1]["unit"]="unit";
			$elements[$input1]["type"]="injector";
		}else
		if ($type=="multiplexer"){
			$compiled_code_line.="".$input1." = new organic_multiplexer(".$input2.",".$input3.")";
			$elements[$input1]["unit"]="unit";
			$elements[$input1]["type"]="multiplexer";
		}else
		if ($type=="switcher"){
			$compiled_code_line.="".$input1." = new organic_switcher(".$input2.",".$input3.",".$input4.")";
			$elements[$input1]["unit"]="unit";
			$elements[$input1]["type"]="multiplexer";
		}else
		if ($type=="echo"){
			$compiled_code_line.="echo ".$input1."";
			$elements[$input1]["unit"]="unit";
			$elements[$input1]["type"]="multiplexer";
		}else
		if ($type=="set"){
			$compiled_code_line.="$input1 = $input2";
			$elements[$input1]["unit"]="unit";
		}else
		if ($type=="repeat"){
			$compiled_code_line.="$input1 = $input2";
			$elements[$input1]["unit"]="unit";
		}else
		if ($type=="organic"){
			$compiled_code_line.=compile_all(file_get_contents($input1),explode(".",$input1)[0]."_");
		}else
		if ($type=="applychild"){
			$compiled_code_line.= "foreach ((".$input1.'->exec()) as $name => $value){';
			$compiled_code_line.= "    ".$input1.'->data[$name] = '.str_replace('$$', $input1.'->data[$name]', $input2).";";
			$compiled_code_line.= "}";
		}else
		if ($type=="equal"){
			if (substr($input2, -8)=="->exec()")
				$compiled_code_line.="$input1 = ".substr($input2, 0,-8);
			else
				$compiled_code_line.="$input1 = ".$input2;
			$elements[$input1]["unit"]="unit";

			$evallist=[];
			$evallist["to_text()"]["type"]="source";
			$evallist["to_text()"]["datatype"]="text";
			$evallist["length()"]["type"]="source";
			$evallist["length()"]["datatype"]="number";
			$evallist["combine()"]["type"]="source";
			$evallist["combine()"]["datatype"]="text";
			$evallist["sql()"]["type"]="source";
			$evallist["sql()"]["datatype"]="m-array";
			$evallist["array()"]["type"]="source";
			$evallist["array()"]["datatype"]="array";
			foreach ($evallist as $evaltypetext => $resulttype) {
				if (substr($currentline,-strlen($evaltypetext))==$evaltypetext){
					
					$elements[$input1]["type"]=$resulttype["type"];
					$elements[$input1]["datatype"]=$resulttype["datatype"];
				}
			}
			

		}else
		if ($type=="type"){
			$compiled_code_line.="echo (".$input1."->type()->exec()".")";
		}else
		if ($currentline[0]=="$"){
			$compiled_code_line.=$currentline;
			if ($elements[$type]["unit"]!="unit"){
				$ermsg= "Organic Source - ".$type. "not defined!!!";
			}

		}else{
		    if (substr($currentline,0,2)=="//")
			$compiled_code_line.="//".$currentline;

		}
		$compiled_code_line.=";\n\n";

		$parser_code.= "---line $i--- \n type: $type \n input1: $input1 \n input2: $input2 \n input3: $input3 \n other:　$ermsg \n\n";
		return $compiled_code_line;
	}

	//將要顯示的文字做處理
	function organic_toolbox_utility($textsss,$prefix){
		global $elements,$filelist;
		$textsss=html_organic_utility($textsss,$prefix);
		$textsss=mark_function_new($textsss);
		$textsss=setinjectnodecolor($textsss);
		foreach ($elements as $key => $value) {
			if (trim($key)!="")
				$textsss=setelement($key,$elements[$key]["type"].($elements[$key]["datatype"]?"-":"").$elements[$key]["datatype"],$textsss);
		}

		$textsss_linearray=explode("<br>",$textsss);
		$textsss="";
		foreach ($textsss_linearray as $key => $textsssl) {
			$textsss.="<span class=linetag>".($key+1).":</span> ".$textsssl."<br>";
		}
		$textsss=setfilepreview($filelist,$textsss);
		return $textsss;
	}


	
	$source_code=file_get_contents($file);
	$compiled_code="<?php \n\n////AUTO-COMPILED BY ORGANIC ALPHA\n\n\ninclude(\"organic_alpha_lib.php\");\n\n ";
	$compiled_code.=compile_all($source_code);
	$compiled_code.="////輸出所有型別為output的source資料\n\n";

	for($i=0;$i<$outputcount;$i++)
			$compiled_code.="echo ".$outputvarlist[$i]."->exec();\n\n";

	$compiled_code.="\n\n?>";
	
	$sss=$source_code;
	$sss=organic_toolbox_utility($sss,"");

	$ppp=$compiled_code;
	$ppp=organic_toolbox_utility($ppp,"");
	
	$compiled_file_name=explode(".",$file)[0].".php";

	file_put_contents($compiled_file_name,$compiled_code );

	echo "<div class=sourceviewer contenteditable='true' id=sourcebox> <h3 class=title>原始organic程式碼</h3>".$sss." </div>";
	echo "<div class=sourceviewer>  <h3 class=title>編譯過的php程式碼  <a class=openlink target='_blank' href='".$compiled_file_name."'>開啟網頁</a></h3>".$ppp." </div>";
	echo "<div class=sourceviewer>  <h3 class=title>編譯器資料</h3>".html_organic_utility($parser_code)." </div>";

?>

 </body>
 </html>