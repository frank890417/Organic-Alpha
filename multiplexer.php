<?
	$raw_data=$_GET["data"];
	$raw_json=json_decode($raw_data,true);

	$target_html=$raw_json["target"];
	$multiplex_count= $raw_json["datas"]["length"];

	echo $raw_data;
	$result="";

	for($i=0;$i<$multiplex_count;$i++){
		$datarow=$raw_json["datas"]["datas"][$i] ;

		$injecter_file="http://www.monoame.com/organic_alpha/injector.php";
		$injecter_data="{\"inject_target\":\"$target_html\",\"inject_list\":[";


		$first=true;
		foreach ($datarow as $name => $value) {
			if ($first)
				$first=false;
			else
				$injecter_data.=",";

			$injecter_data.="{\"tag\":\"$name\",\"data\":\"$value\"}";
		}

		
		$injecter_data.="]}";

		//echo $injecter_file."?data=".$injecter_data."<br>";


		$result.=file_get_contents($injecter_file."?data=".urlencode($injecter_data), false);

	}

	echo $result;



	/*
	

	{
		"target" : "testinject.html",
		"datas" : {"length": 5,"datas": [{"id":"1","name":"person1","msg":"message num 1","time":"2015-12-15 19:47:07","readtime":"2015-12-15 19:47:07"},{"id":"2","name":"person2","msg":"message num 2","time":"2015-12-15 19:47:39","readtime":"2015-12-15 19:47:39"},{"id":"3","name":"person3","msg":"message num 3","time":"2015-12-15 19:47:44","readtime":"2015-12-15 19:47:44"},{"id":"4","name":"person4","msg":"message num 4","time":"2015-12-15 19:47:48","readtime":"2015-12-15 19:47:48"},{"id":"5","name":"person5","msg":"message num 5","time":"2015-12-15 19:47:52","readtime":"2015-12-15 19:47:52"}]}
	}
	
	*/


?>