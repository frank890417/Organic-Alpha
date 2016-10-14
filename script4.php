<?php 

////AUTO-COMPILED BY ORGANIC ALPHA


include("organic_alpha_lib.php");

 ////取得網頁注入大框架;

$s_frame = new organic_source("file","frame.html");

////設定資料源sql取得cbnctu裡的前五筆貼文;

$s_sql = new organic_source("sql","SELECT * FROM cbnctu order by id desc limit 5");

////設定資料源為檔案 textinject.html;

$s_inj1text = new organic_source("file","testinject.html");

////建立多工器設定資料源與注入資料為sql結果;

$mux1 = new organic_multiplexer($s_inj1text->exec(),$s_sql->exec());

////建立注入器設定資料源為網頁大框架，注入資料為多工器結果(->exec())結合之後的文字源作成新的注射資料標籤為content;

$inj2 = new organic_injector($s_frame->exec(),$mux1->exec()->combine()->make_inj("content")->exec());

////顯示注入器的執行結果(exec());

$out1 = new organic_source("output",$inj2->exec()->exec());

////輸出所有型別為output的source資料

echo $out1->exec();



?>