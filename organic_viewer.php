<html>
<head>
			<meta charset="utf-8"> 
			<title> Organic 注入點檢視 v0.55</title>
			<link rel="shortcut icon" href="http://www.monoame.com/organic_alpha/organic.ico">
	
	 <script type="text/javascript" src="http:///cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	</head>
	<body>
				<form method='get'>
		
		<input name='file' value=<?php  $file=(isset($_GET["file"])?$_GET["file"]:"testinject2.html" ) ; echo $file;?>  />
		<input type='submit'/>
		</form> 
<style>
	.injectnode::before{
		content: "Inject:";
		color: #fff;
		background-color: #c33;
		width: 50px;
		display: inline-block;
		height: 20px;
	}
	.injectnode{
		color: #fff;
		background-color: #c33;
		display: inline-block;
		height: 20px;
	}
	.sourceviewer{
		background-color: #222;
		color: #fff;
	}
</style>

<?php
	echo "<div style='width: 45%; display:inline;'>";
	$subject = file_get_contents($file);
	$pattern = '/(\%[a-zA-Z\_]*\%)/i';
	preg_match_all($pattern, $subject, $matches);
	//var_dump( $matches);
	foreach ( $matches[0] as $name => $value) {
		$string  = $subject;
		$needle  = $value;
		$replace = "[Inject:".str_replace("%","",$value)."]";
		$subject  = str_replace($needle, $replace, $string);
	}
	echo $subject;
	echo "</div>";


	echo "<div style='width: 45%; display:inline;'>";
	$subject =str_replace("\n","<br>",htmlentities( file_get_contents($file)));
	$pattern = '/(\%[a-zA-Z\_]*\%)/i';
	preg_match_all($pattern, $subject, $matches);
	//var_dump( $matches);
	foreach ( $matches[0] as $name => $value) {
		$string  = $subject;
		$needle  = $value;
		$replace = "<p class=injectnode>".str_replace("%","",$value)."</p>";
		$subject  = str_replace($needle, $replace, $string);
	}
	echo "<br><br>Source:<br><div contenteditable=true class=sourceviewer>".$subject."</div>";
	echo "</div>";
?>
</body>
</html>