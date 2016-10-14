<?php 

////AUTO-COMPILED BY ORGANIC ALPHA


include("organic_alpha_lib.php");

 $frame = new organic_source("file", "frame3.html");

$subframe = new organic_source("text", "<div><h3>網頁瀏覽資料:</h3> [%datahere%] </div>");

$singleparentlist = new organic_source("text", "<li class='dropdown'>\n<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>[%displaytext%] <span class='caret'></span></a>\n<ul class='dropdown-menu'>[%drop_[%name%]%]</ul>\n</li>");

$navhtmlfile = new organic_source("file", "nav3.html");

$singlechildlist = new organic_source("text", "<li><a href=\"#[%name%]\">[%displaytext%]</a></li>\n");

$mul_sql_line = new organic_source("text", "select * from webstructure where parentname = \"[%name%]\"");

$sql_nav = new organic_source("sql", "select * from webstructure where parentname = \"\"");

$mul_lists = new organic_multiplexer( $singleparentlist,$sql_nav);

$inj_navtable = new organic_injector( $navhtmlfile,$mul_lists->exec()->combine()->make_inj("mainlink"));

$mul_sublist_sql = new organic_multiplexer( $mul_sql_line,$sql_nav);

$multi_sql_result = new organic_source("array", $mul_sublist_sql->exec());

$mul_sub_list0 = new organic_multiplexer( $singlechildlist,($multi_sql_result ->fetch(0)));

$injnavall = new organic_injector( $inj_navtable,$multi_sql_result);

$mysqla = new organic_source("sql", "select * from webanalysis limit 5");

$array_a = new organic_source("array", $mysqla->exec());

$arrayatext = new organic_source("text", "test");

$inj1 = new organic_injector( $subframe,$arrayatext->exec()->make_inj("datahere"));

$inj2 = new organic_injector( $frame,$inj1->exec()->make_inj("datahere"));

$inj3 = new organic_injector( $inj2,$injnavall->exec()->make_inj("navhere"));

$out1 = new organic_source("output", $inj3->exec());

////輸出所有型別為output的source資料

echo $out1->exec();
?>