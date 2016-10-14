<?php 

////AUTO-COMPILED BY ORGANIC ALPHA


include("organic_alpha_lib.php");

 $uselist = new organic_source("sql","select * from indexsourcelist");

$injecttext = new organic_source("text","項目: [%text%]<br>");

$mux1 = new organic_multiplexer($injecttext->exec(),$uselist->exec());

$out1 = new organic_source("output",$mux1->exec()->combine()->exec());

////輸出所有型別為output的source資料

echo $out1->exec();



?>