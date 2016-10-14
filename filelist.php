<?php 

////AUTO-COMPILED BY ORGANIC ALPHA


include("organic_alpha_lib.php");

 $dir = new organic_source("input","./");

$arr = new organic_source("array",(scandir($dir->exec())));

$injtext = new organic_source("text","  <div class=files>  檔案: <a target='_blank' href='[%name%]'>[%name%]</a><br></div>");

$result = new organic_multiplexer($injtext->exec(),$arr->make_array_inj("name")->exec());

$filelist = new organic_source("output",$result->exec()->combine()->exec());

////輸出所有型別為output的source資料

echo $filelist->exec();



?>