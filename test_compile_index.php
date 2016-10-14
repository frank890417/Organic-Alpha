<?php include("organic_alpha_lib.php");

 $arr = new organic_source("array",(scandir('./')));

$indexframe = new organic_source("file","indexframe.html");

$injtext = new organic_source("text","   檔案: <a target='_blank' href='[%name%]'>[%name%]</a><br>");

$result = new organic_multiplexer($injtext->exec(),$arr->make_array_inj("name")->exec());

$indexpage = new organic_injector($indexframe->exec(),$result->exec()->combine()->make_inj("filelist")->exec());

$page = new organic_source("output",$indexpage->exec()->exec());

////輸出所有型別為output的source資料

echo $page->exec();



?>