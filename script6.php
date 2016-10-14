<?php 

////AUTO-COMPILED BY ORGANIC ALPHA


include("organic_alpha_lib.php");

 $counter1 = new organic_source("counter",5);

$c_array = new organic_source("array",$counter1->to_array()->exec());

foreach (($c_array->exec()) as $name => $value){    $c_array->data[$name] = ($c_array->data[$name]+1);};

foreach (($c_array->exec()) as $name => $value){    $c_array->data[$name] = ($c_array->data[$name].'');};

$numtext = new organic_source("text","The Numbers: %nn%<br>");

$mul1 = new organic_multiplexer($numtext->exec(),$c_array->make_array_inj("nn")->exec());

echo $mul1->exec()->combine()->exec();

////輸出所有型別為output的source資料



?>