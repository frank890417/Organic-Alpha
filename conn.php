<?
	$web_site_title = "Organic";
	$lang_flag = true;

	$mysql_host_port = 'localhost'; 
	$mysql_user = 'vhost93722';
	$mysql_password = 'monoame0302';
	$db_name = 'vhost93722';
				
 	$link = mysql_connect($mysql_host_port,$mysql_user,$mysql_password) or die("Could not connect");
	//$serverset = "character_set_connection='utf8', character_set_results='utf8', character_set_client='binary'";
    //mysql_query("SET $serverset");
	mysql_select_db("$db_name") or die("Could not select database");

	$web_url = $_SERVER['PHP_SELF'];
	$web_url = strrchr($web_url,"/"); 
	$web_url = str_replace("/","",$web_url);
	
	$host_url = "http://" . $_SERVER["HTTP_HOST"] . str_replace("/$web_url","",$_SERVER['PHP_SELF']) . "/";
	
?>