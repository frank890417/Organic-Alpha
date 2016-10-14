<?php 

////AUTO-COMPILED BY ORGANIC ALPHA


include("organic_alpha_lib.php");

 ////取得網頁大框架原始碼作為注入目標;

$s_frame = new organic_source("file","frame2.html");

////取得POST中account/passrword兩項參數;

$s_account = new organic_source("post","account");

$s_password = new organic_source("post","password");

////設定sql來源資料，執行後存到array型別中;

$s_sql1 = new organic_source("sql",("SELECT * from member where account = \"".$s_account->exec()."\" and password = \"".$s_password->exec()."\""));

$s_res = new organic_source("array",$s_sql1->exec());

////設定inj1注射資料，建立新的注入器，注入剛剛sql第0筆結果(登入成功用);

$s_inj1text = new organic_source("text","您的資料: [%id%] - 帳號是 [%account%] 你的註冊時間是%time%<br> ");

$inj1 = new organic_injector($s_inj1text->exec(),$s_sql1->exec()[0]);

////設定inj4注射資料，建立新的注入器，注入剛剛輸入的帳號(登入失敗用);

$s_inj4text = new organic_source("text","您輸入的帳號是 [%account%] ，並沒有找到您的資料<br> ");

$injneedle_4 = $s_account->to_text();

$inj4 = new organic_injector($s_inj4text->exec(),$injneedle_4->make_inj("account")->exec());

////建立判斷標準為s_res(剛剛sql執行轉存的array)之長度;

$s_sw1_judge = $s_res->length();

////製作兩個分歧選擇，分別文登入成功跟失敗的;

$s_sw1_data1 = new organic_source("text",("登入成功: ".$inj1->exec()->exec()));

$s_sw1_data2 = new organic_source("text",("登入失敗: ".$inj4->exec()->exec()));

////建立新的選擇器，輸入判斷條件與成立/不成立時回傳的結果(括號會使元素不被取值);

$s_swi_1 = new organic_switcher($s_sw1_judge->exec(),($s_sw1_data1),($s_sw1_data2));

////sql取得所有會員;

$s_sql2 = new organic_source("sql","SELECT * from member");

////設定多工器使用之框架;

$s_inj2 = new organic_source("text","<tr><td>[%id%]</td><td>[%account%]</td><td>[%password%]</td><td>[%time%]</td></tr>");

////建立新的多工器，將框架以所有會員的sql(s_sql2)多工注入;

$mul1 = new organic_multiplexer($s_inj2->exec(),$s_sql2->exec());

////建立空白的文字源;

$empty = new organic_source("text","");

////設定其為多工器執行結果之結合(型別為text);

$inject_tbl = $mul1->exec()->combine();

////建立選擇器，使用有沒有檢索到輸入帳號的數量當判斷條件，如果成立回傳剛剛製作的表格，如果沒有則回傳空白;

$s_swi_2 = new organic_switcher($s_sw1_judge->exec(),($inject_tbl),($empty));

////建立新的注入器 注入網頁大框架，使用的注射資料為剛剛的swi2執行結果(->exec()) 做成標籤[mycontent]的注射資料集;

$inj2 = new organic_injector($s_frame->exec(),$s_swi_2->exec()->make_inj("mycontent")->exec());

////新增注射資料集，內容為第一個選擇器的(登入成功與否)的執行結果(->exec());

$inj2 ->add_filter("yesno",$s_swi_1->exec()->exec());

////輸出注射後之執行結果(exec());

echo $inj2->exec()->exec();

////輸出所有型別為output的source資料



?>