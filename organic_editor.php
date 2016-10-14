
<html>
		<head>
		    <title> Organic 編輯器 v0.55</title>
			<meta charset="utf-8"> 
			<link rel="shortcut icon" href="http://www.monoame.com/organic_alpha/organic.ico">

	
 		<script type="text/javascript" src="http:///cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	
		</head><body>
		<h2 style="color: white;margin-top: 20px;"> <a href='index.php'> <img src="logouse-02.png" height="60px" /></a> Editor v 0.55</h2>
		<form method='get'>
		
		<input name='file' value=<?php  $file=(isset($_GET["file"])?$_GET["file"]:"script1.organic" ) ; echo $file;?>  />
		<input type='submit' value="開啟" />
		</form> 

<style>
	.sourceviewer{
		background-color: #222;
		color: #fff;
		padding: 30px;
		line-height: 24px;
		display: inline-block;
		width: 43%;
		overflow-y: scroll;
		height: 600px;
		margin: 5px;
	}
	.title::before{
		content: "---------------------";
		color: white;
	}
	.title::after{
		content: "---------------------";
		color: white;
	}
	.openlink{
		border-style: solid;
		border-color: white;
		border-width: 1px;
		color: white;
		padding: 5px;
		display: inline;
	}
	.element{
		border-style:solid;
		border-width:1px;
		border-radius: 4px;
		border-color:#666;
		display: inline;
		padding-left: 5px;
		padding-right: 5px;
		margin-left: 3px;
		margin-right: 3px;
		cursor: pointer;
		position: relative;
		transition-duration: 0.3s;
	}
	.element:hover{
		border-color:#aaa;
	}
	.highlight{
		color: #333;
		background-color: #eee;
	}
	.highlight .elementinfo{
		display: block;
	}
	.highlight2{
		color: #333;
		background-color: #ff7;
	}

	.linetag{
		font-size: 14px;
		color: #999;
		display: inline;
		padding-right: 5px;
	}
	.file{
		color: #abc;
		cursor: pointer;
	}
	.elementinfo{
		position: absolute;
		right: -120px;
		top: -16px;
		display: none;
		background-color: #555;
		color:#fff;
		width: 110px;
		z-index:1000;
		padding-left: 8px;
		border-radius: 3px;
		font-size: 14px;
	}
	.filebox{
		position: absolute;
		right: -240px;
		top: 0px;
		display: none;
		background-color: #eee;
		color:#333;
		width: 250px;
		height: 500px;
		z-index:1000;
		padding-left: 8px;
		border-radius: 3px;
		font-size: 12px;
		overflow: scroll;

	}
	.showfilebox .filebox{
		display: block;
	}
	.injectnode::before{
		content: "Inj-";
		color: #fff;
		background-color: #c33;
		display: inline-block;
		height: 20px;
	}
	.injectnode{
		color: #fff;
		background-color: #c33;
		display: inline-block;
		height: 20px;
	}
	body{
		background-color: #555;
		font-family: 微軟正黑體;
	}

</style>


		<?php

			if ($_POST["save"]=='1'){
				file_put_contents($_POST["filename"], $_POST["filedata"]);
			}

		?>

		<form method='post'>
		
			<input name='filename' style="display: none;"  value=<?php  $file=(isset($_GET["file"])?$_GET["file"]:"script1.organic" ) ; echo $file;?>  />
			<input name='save' value='1' style="display: none;" /><input type='submit' value="儲存" />
			<textarea class='sourceviewer' name='filedata' style="width: 100%;height:600px;"><?php
				echo file_get_contents(htmlentities($file));
				?></textarea>
			
		</form> 

		
		
	</body>

</html>