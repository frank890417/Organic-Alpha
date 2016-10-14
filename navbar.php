<?php 

////AUTO-COMPILED BY ORGANIC ALPHA


include("organic_alpha_lib.php");

 ////母導覽列按鈕框架;

$singleparentlist = new organic_source("text","<li class='dropdown'>\n<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>[%displaytext%] <span class='caret'></span></a>\n<ul class='dropdown-menu'>[%drop_[%name%]%]</ul>\n</li>");

////導覽列用的html;

$navhtmlfile = new organic_source("file","nav3.html");

////子導覽列按鈕框架;

$singlechildlist = new organic_source("text","<li><a href=\"#[%name%]\">[%displaytext%]</a></li>\n");

////動態產生子導覽列sql檢索語句框架;

$mul_sql_line = new organic_source("text","select * from webstructure where parentname = \"[%name%]\"");

////檢索所有母導覽列的sql;

$sql_nav = new organic_source("sql","select * from webstructure where parentname = \"\"");

////將母導覽列的sql檢索結果多工插入到母導覽列按鈕框架;

$mul_lists = new organic_multiplexer($singleparentlist->exec(),$sql_nav->exec());

////將母導覽列多工產生之html插入到網頁大框架的"mainlink"部分;

$inj_navtable = new organic_injector($navhtmlfile->exec(),$mul_lists->exec()->combine()->make_inj("mainlink")->exec());

////將母導覽列的sql檢索結果多工插入到子導覽列sql檢索語句框架;

$mul_sublist_sql = new organic_multiplexer($mul_sql_line->exec(),$sql_nav->exec());

////子導覽列sql檢索語句的多工器設置動態key值為drop_[%name%];

$mul_sublist_sql->set_key("drop_[%name%]");

////執行sql多工，將結果放到新的source array;

$multi_sql_result = $mul_sublist_sql->sql();

////設定一個新的多工器，其原始使用框架為子導覽列按鈕框架;

$mul_sub_list0 = new organic_multiplexer($singlechildlist->exec(),($multi_sql_result ->fetch(0)));

////對每一個多工器的子元素進行操作，設定多工注射用資料為每個子元素並執行多工，結果放回原本的子元素;

foreach (($multi_sql_result->exec()) as $name => $value){    $multi_sql_result->data[$name] = ($mul_sub_list0->set_data($multi_sql_result->data[$name])->exec()->combine()->exec());};

////將多工器的結果注射到 注射過"mainlink"的網頁而產生動態注射點的整體nav框架;

$injnavall = new organic_injector($inj_navtable->exec()->exec(),$multi_sql_result->exec());

$output_navbar = new organic_source("output",$injnavall->exec()->exec());

////輸出所有型別為output的source資料

echo $output_navbar->exec();



?>