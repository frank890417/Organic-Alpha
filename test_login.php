<?php
	$frame="
		<html>
		<head></head><body>
		<form method='post'>
		
		<input name='account'/>
		<input name='password'/>
		<input type='submit'/>
		預設帳密: 12334  /  1233454

		</form> <br><br>登入成功與否:<br> [%yesno%] <br><br><br> 以下是帳號密碼資料清單(如果登入成功顯示)：<br>[%mycontent%] </body>
	";

	include("organic_alpha_lib.php");
	$stage3="";
	$stage5="";
	if (isset($_POST["account"])){
		//sql("INSERT INTO member(id,account,password,time) SELECT max(id)+1,\"".$_POST["account"]."\"".",\"".$_POST["password"]."\"".",now() from member;");
		
		$original_data="SELECT * from member where account = \"".$_POST["account"]."\" and password = \"".$_POST["password"]."\"";
		
		$stage1=new organic_source("sql",$original_data);
		$stage1=$stage1->exec();

		$inject_data1="您的資料: [%id%] - 帳號是 [%account%] 你的註冊時間是[%time%]<br> ";
		$stage2=new organic_injector($inject_data1,$stage1[0]);
		$stage2=$stage2->exec();

		$stage3= new organic_switcher();
		$stage3->set_judge((new organic_source("array",$stage1))->length()->exec());
		$stage3->set_data1("登入成功 ".$stage2->exec());
		$stage3->set_data2("登入失敗");
		$stage3= $stage3->exec();


		$sql_alldata="SELECT * from member";
		$stage4= new organic_source("sql",$sql_alldata);
		$stage4=$stage4->exec();


		$inject_table="<tr><td>[%id%]</td><td>[%account%]</td><td>[%password%]</td><td>[%time%]</td></tr>";
		$stage5= new organic_multiplexer($inject_table,$stage4);
		$stage5= $stage5->exec()->combine();

		$stage5= (new organic_switcher(count($stage1),$stage5->exec(),""))->exec();
		
		/*
		$original_data="SELECT * from member where account = \"".$_POST["account"]."\" and password = \"".$_POST["password"]."\"";
		$rs=sql($original_data);
		$aa=injector("您的資料: %id% - 帳號是 %account% 你的註冊時間是%time%<br> ",$rs[0]);
		$bb=switcher(count($rs),"登入成功 ".$aa,"登入失敗");
		$sql_alldata="SELECT * from member";
		$tablers=multiplexer("<tr><td>%id%</td><td>%account%</td><td>%password%</td><td>%time%</td></tr>",sql($sql_alldata));
		echo injector($frame,array("yesno" =>$bb ,"mycontent"=>"<table> ".$tablers."</table>"));
		*/
	}
	
	$stage6= new organic_injector($frame,[]);
	$stage6->add_filter("yesno",$stage3);
	$stage6->add_filter("mycontent","<table> ".$stage5."</table>");
	echo $stage6->exec()->exec();
	
		
	//echo source("array",sql("SELECT * from member"))->table();
?>