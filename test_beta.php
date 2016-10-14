<?
	include("organic_alpha_lib.php");

	$sql="SELECT * FROM cbnctu order by id desc limit 5";
	$stage1=source("sql",$sql)->exec();
	$sqlresult=$stage1;
	
	$stage2=multiplexer(file_get_contents("testinject.html"),$sqlresult);
	$mulresult = injector_tag(file_get_contents("frame.html"),"content",$stage2);

	echo $mulresult;


	

?>