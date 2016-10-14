<?

	//連結資料庫 
	include("conn.php");

	//驗證身份 取得sql輸入
	$access_token="monoame0302";

	$sql_data=$_GET["data"];
	$sql_raw=json_decode($sql_data, true);

	$sqlinput=$sql_raw["sql"];
	//echo "Input sql: ".$sqlinput."<br>";
	//echo "Request URI: ".$_SERVER['REQUEST_URI']."<br>";

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

		if ($i!=$num_rows-1){
				$result.=",";
			}

	}



	$result.="]}";


	/*
		{
			"sql": "SELECT * FROM message"
			

		}
		

	*/


	//回傳結果
	echo $result;

?>