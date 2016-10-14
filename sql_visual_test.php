
<html>
	<head>
		
	</head>
	<body>
		<form method = "post">
			<input name="sql" value="select * from cbnctu where confirm < 3" width="100%">
			<input type="submit">
		</form>
	</body>

</html>

<?php
	include("organic_alpha_lib.php");
	$sql=isset($_POST["sql"])?$_POST["sql"]:"select * from cbnctu where confirm < 3";
	$datas=sql($sql);


	$multiplex_count= count($datas);



	for($i=0;$i<$multiplex_count;$i++){
		foreach ($datas[$i] as $name => $value) {
			echo "%".$name."% -> ". $value . " <br>";
		}

		echo "<br><br>";
	}

	//echo injector("%name%: %msg% (%time%)",sql("select * from message where id = 1")[0]);
	//echo multiplexer("%name%: %msg% (%time%)<br>",sql("select * from message"));
	//echo multiplexer(file_get_contents("testinject2.html"),sql("select * from message"));



?>