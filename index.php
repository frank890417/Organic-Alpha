<?php 

////AUTO-COMPILED BY ORGANIC ALPHA


include("organic_alpha_lib.php");

 $indexframe = new organic_source("file","indexframe.html");

$arr = new organic_source("array",(scandir('./')));

;

$weblist = new organic_source("sql","select id,text,link,DATE_FORMAT(time,'%Y-%m-%d') as time from indexsourcelist");

$injtext = new organic_source("text","  <div class=files>  檔案: <a target='_blank' href='[%name%]'>[%name%]</a><br></div>");

$result = new organic_multiplexer($injtext->exec(),$arr->make_array_inj("name")->exec());

$injtext2 = new organic_source("text"," <div class=files><a class='mainlink' target='_blank' href='[%link%]'>[%text%]</a><p class='time'>[%time%]</p><br></div>");

$result2 = new organic_multiplexer($injtext2->exec(),$weblist->exec());

$cdns = new organic_source("cdn","jquery");

$indexstage1 = new organic_injector($indexframe->exec(),$result->exec()->combine()->make_inj("filelist")->exec());

$indexstage2 = new organic_injector($indexstage1->exec()->exec(),$cdns->to_text()->make_inj("cdn")->exec());

$indexstage3 = new organic_injector($indexstage2->exec()->exec(),$result2->exec()->combine()->make_inj("resource")->exec());

$page = new organic_source("output",$indexstage3->exec()->exec());

////輸出所有型別為output的source資料

echo $page->exec();



?>