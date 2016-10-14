<?php
	class organic_ad_nav{
		public $sublistframe;
		public $mainlistframe;
		public $navframe;

		function __construct($sublistframe,$mainlistframe,$navframe){
			$this->sublistframe=$sublistframe;
			$this->mainlistframe=$mainlistframe;
			$this->navframe=$navframe;
		}

		function exec(){

			$frame = new organic_source("file","frame3.html");

			$sql_nav = new organic_source("sql","select * from webstructure where parentname = \"\"");

			$array_nav = new organic_source("array",$sql_nav->exec());

			$singleparentlist = new organic_source("text","<li class=\"active\"><a href=\"#%name%\">%displaytext% %drop_%name%%</a></li>");

			$singlechildlist = new organic_source("text","<li class=\"active\"><a href=\"#%name%\">%displaytext%</a></li>");

			$tablehtml = new organic_source("file","nav3.html");

			$mul_lists = new organic_multiplexer($singleparentlist->exec(),$sql_nav->exec());

			$inj_navtable = new organic_injector($tablehtml->exec(),$mul_lists->exec()->combine()->make_inj("mainlink")->exec());

			$mul_sql_line = new organic_source("text","select * from webstructure where parentname = \"%name%\"");

			$mul_sublist_sql = new organic_multiplexer($mul_sql_line->exec(),$sql_nav->exec());

			$mul_sublist_sql->set_key("%name%");

			$multi_sql_result = $mul_sublist_sql->sql();
			var_dump($multi_sql_result );

			$mul_sub_list0 = new organic_multiplexer($singlechildlist->exec(),($multi_sql_result->fetch(0)));

			foreach (($multi_sql_result->exec()) as $name => $value){    $multi_sql_result->data[$name] = ($mul_sub_list0->set_data($multi_sql_result->data[$name])->exec()->combine()->exec());};

			$subframe = new organic_source("text","<div><h3>網頁瀏覽資料:</h3> %datahere% </div>");

			$mysqla = new organic_source("sql","select * from webanalysis limit 5");

			$array_a = new organic_source("array",$mysqla->exec());

			$inj1 = new organic_injector($subframe->exec(),$array_a->table()->make_inj("datahere")->exec());

			$inj2 = new organic_injector($frame->exec(),$inj1->exec()->make_inj("datahere")->exec());

			$inj3 = new organic_injector($inj2->exec()->exec(),$inj_navtable->exec()->make_inj("navhere")->exec());

			return $inj_navtable->exec();

		}


?>