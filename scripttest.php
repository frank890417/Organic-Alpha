<?php 

////AUTO-COMPILED BY ORGANIC ALPHA


include("organic_alpha_lib.php");

 $sframe = new organic_source("file","frame.html");

$ssql = new organic_source("sql","SELECT * FROM cbnctu order by id desc limit 5");

$sinj1text = new organic_source("file","testinject.html");

$mux1 = new organic_multiplexer($sinj1text->exec(),$ssql->exec());

$inj2 = new organic_injector($sframe->exec(),$mux1->exec()->combine()->make_inj("content")->exec());

$soutput = new organic_source("output",$inj2->exec()->exec());

////輸出所有型別為output的source資料

echo $soutput->exec();



?>