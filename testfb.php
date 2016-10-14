
<html>
<script src='https://www.google.com/recaptcha/api.js'></script>

	<style>
		body{
		  background-color: #444;
		  width: 100%;
		  height: 768px;
		  margin: 0px;
		  font-family: 微軟正黑體;
		}

		.block{
		  width: 600px;
		   background-color:white;
		  box-shadow:4px 4px 12px -2px rgba(20%,20%,40%,0.5);
		  margin: auto;
		  margin-top: 30px;
		  text-align: center;
		  margin-bottom: 20px;
		  padding: 10px;
		  
		}
		.title{
		  font-size: 40px;
		  margin-auto;
		  padding-top: 20px;
		  color: #777;
		}
		.form {
		  width: 100%;

		}

		.tt{
		  width: 500px;
		  height:300px;
		  margin: 0px;
		  
		}

		.postbtn{
		  width: 500px;
		  height: 50px;
		 
		  border-style: solid;
		  border-radius: 5px;
		  border-color:#333;
		  background-color: transparent;
		  cursor: pointer;
		  transition-duration: 0.4s;

		}

		.postbtn:hover{
		   color: #ccc;
		 
		  
		  background-color:#333;
		}

		.subblock .postbtn{
		  width: 80px;
		  height: 80px;
		  font-size: 35px;
		  display: inline-block; 
		  font-family: 微軟正黑體;  
		}
		.innercontent{
		  width: 80%;
		  display: inline-block;
		}
		.time{
		  color: #aaa;
		    margin-top: -20px;
		}

	</style>

	<head>
			<meta charset="utf-8"> 
	 <script type="text/javascript" src="http:///cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	</head>
	
	<body>

		  <div class="block">
		      <h1 class=title>靠北交大V2.0 </h1>
		    <h3>想要靠北什麼 </h3><a  target="_blank" style="display: inline-block; color: #666;" href="https://www.facebook.com/HateNCTUVerson2" >粉專連結</a>
		   
		    <p style='padding: 5px;color:#888;font-size: 10px;'>靠北交大2.0系統將會在您的貼文達到3個人以上贊同審核時發出<br>當貼文的贊同數達標 則會自動發出， 您可以選擇+1或-1別人與自己的貼文（但不能連續評分)</p>
		      <div class="form">
		         <form method="post">
		          <textarea name="msg" class=tt></textarea>
		          <div style="padding-left: 50px; " class="g-recaptcha" data-sitekey="6Le3BhMTAAAAANA82g1zg6c_kp-i-XIaTRfVQtAp"></div>
		           <input class="postbtn" value="送出" type="submit"></input>
		           <input name="uni" value="1"  style="display: none;"></input>
		        </form>
		      </div>



<?php 
	$usingdatabase="cbnctu";
	$usingworld="靠北";

include("conn.php");
 	function randstr($len=6){ 
	    $chars='abcdefghijklmnopqrstuvwxyz0123456789';
	    #characters to build the password from 
	    mt_srand((double)microtime()*1000000*getmypid());
	    #seed the random number generater (must be done) 
	    $password=''; 
	    while(strlen($password)<$len) 
		        $password.=substr($chars,(mt_rand()%strlen($chars)),1); 
		    return $password; 
	}


 	session_start();
 	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    	$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
	    $ip = $_SERVER['REMOTE_ADDR'];
	}

 	if ($_SESSION[$ip."visit"]){
 	}else{
 		$_SESSION[$ip."visit"]=randstr(15);
 	}
	
	if ($_POST["uni"]==1 && $_POST["msg"]!=""){

		
		$sql="SELECT count(id) from $usingdatabase where ip = \"$ip\" and TIMEDIFF(  NOW(),time) > \"00:00:00\" and TIMEDIFF(  NOW(),time)< \"00:03:00\"";
		
		$rs=mysql_query($sql);
		$bb=mysql_fetch_row($rs);
		if ($bb[0]>4){
			echo "<script>alert('同一個ip請勿連續大量發文 謝謝');</script>";
			exit;
		}

		$sql="SELECT MAX(id) from $usingdatabase";
		$rs=mysql_query($sql);
		$bb=mysql_fetch_row($rs);
		$sql="SELECT count(id) from $usingdatabase where msg = \"".$_POST["msg"]."\" and id between '".($bb[0]-5). "' AND '".($bb[0]+1)."'";
		//echo $sql;
		$rs=mysql_query($sql);
		$bb=mysql_fetch_row($rs);

		if ($bb[0]>0){
			echo "<h4 style='margin-top: 80px;'>請勿連續發相同的文章</h4>";
		
		}else{
			if ($_SESSION[$ip.'postnum'] <4){
	            $_SESSION[$ip.'postnum']+=1;

	            if(isset($_POST['g-recaptcha-response'])){
		          $captcha=$_POST['g-recaptcha-response'];
		        }

	            if(!$captcha){
		          echo '<h2>請勾選認證</h2>';
		          exit;
		        }

		        $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6Le3BhMTAAAAAOwZGKFIF9k_AViUW1XECHtw1R-0&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
		        if($response.success==false)
		        {
		          echo '<h2>請勿大量灌水</h2>';

		        }else
		        {
		         
		           if (strpos($_POST["msg"],"script")){

		            	
			    		echo "<h4 style='margin-top: 80px;'>謝謝您的測試，請勿在內文中注入程式碼，謝謝！</h4>";
		          } else if (strpos($_POST["msg"],"select")){

		            	
			    		echo "<h4 style='margin-top: 80px;'>不要動我的資料庫啦...拜託...</h4>";
		            
		            } else if (strpos($_POST["msg"],"SELECT")){

		            	
			    		echo "<h4 style='margin-top: 80px;'>不要動我的資料庫啦...拜託...</h4>";
		            
		            } else if (strpos($_POST["msg"],"UNION")){

		            	
			    		echo "<h4 style='margin-top: 80px;'>不要動我的資料庫啦...拜託...</h4>";
		            
		            } else if (strpos($_POST["msg"],"union")){

		            	
			    		echo "<h4 style='margin-top: 80px;'>不要動我的資料庫啦...拜託...</h4>";
		            
		            } else if (strpos($_POST["msg"],"div")){

		            	
			    		echo "<h4 style='margin-top: 80px;'>哇你知道這其實沒什麼影響嗎.. 只有網頁變醜醜的而已＝＝</h4>";
		            
		            } else if (strpos($_POST["msg"],"body")){

		            	
			    		echo "<h4 style='margin-top: 80px;'>哇你知道這其實沒什麼影響嗎.. 只有網頁變醜醜的而已＝＝</h4>";
		            
		             } else if (strpos($_POST["msg"],"img")){

		            	
			    		echo "<h4 style='margin-top: 80px;'>哇你知道這其實沒什麼影響嗎.. 只有網頁變醜醜的而已＝＝</h4>";
		             } else if (strpos($_POST["msg"],"p>")){

		            	
			    		echo "<h4 style='margin-top: 80px;'>哇你知道這其實沒什麼影響嗎.. 只有網頁變醜醜的而已＝＝</h4>";
		            
		          
		           }else{

						$sql="INSERT INTO $usingdatabase(id,msg,time,ip) SELECT max(id)+1,\"".htmlentities(str_replace("'","_",$_POST["msg"]))."\",\"".date("Y-m-d H:i:s")."\",\"$ip\" from $usingdatabase";
							


						$rs=mysql_query($sql);
						$sql="SELECT MAX(id) from $usingdatabase";
						$rs=mysql_query($sql);
						$bb=mysql_fetch_row($rs);

						echo "<h4 style='margin-top: 80px;'>貼文 #".$bb[0]."已經排入發文序列</h4>";
					}
		        }

	           
		    }else{
		    	echo "<h4 style='margin-top: 80px;'>請勿短時間連續發表多篇文章</h4>";
		    }

		}
		/*
		$page_access_token = 'CAACEdEose0cBAH8CTcEs0Q4Wrdc6zP1qK3tkH9tp5gp3poidozni1eL60RxpIxzXtfAoGRlahrecVe6pGn5BepfpPOS9QYU3zQ4hJWB1QHivr7GFIDbWdg8HzVS0rlkvKhZAAOZCw6Ufs5N9eI6mykkgk31iRjM3V7OwHaZBiXeoY2Cr5ApZCPPkYBnWKrd33IZAgC1ySRwZDZD';
		$page_id = '796764687100390';
		//$data['picture'] = "https://lh4.googleusercontent.com/Gq2S7XvG95gLL8kGxMeLdBp8TALZ0MVfTYF2KHRFy_s=w895-h553-no";
		//$data['link'] = "http://www.google.com/";
		$data['message'] = "#靠北交大V2_234\nfosdjfigosjdfoigj\nosidfjgiosdjfgoisfj\nsdiofjgoisdfjg";
		//$data['caption'] = "Caption";
		//$data['description'] = "Description";
		$data['access_token'] = $page_access_token;
		$post_url = 'https://graph.facebook.com/'.$page_id.'/feed';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $post_url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$return = curl_exec($ch);
		curl_close($ch);
		*/
	}

?>
  </div>
<?

	$oknum=3;
	$nonum=-6;
	if ($_SESSION[$ip.'accept'.$_POST["acceptid"]] && $_POST["yesbtn"]){
		echo "<script>alert('請勿連續評分(+1)貼文".$_POST["acceptid"]."');";

		$queuescript= "<script>document.getElementById('block".$_POST["acceptid"]."').scrollIntoView();</script>";

	}
	if ($_SESSION[$ip.'decline'.$_POST["acceptid"]] && $_POST["nobtn"]){
		echo "<script>alert('請勿連續評分(-1)貼文".$_POST["acceptid"]."');";

		$queuescript= "<script>document.getElementById('block".$_POST["acceptid"]."').scrollIntoView();</script>";

	}
	if ($_SESSION[$ip.'accept'.$_POST["acceptid"]]==0){
		if  ( $_POST["yesbtn"] && $_POST["acceptid"]){
			$_SESSION[$ip.'accept'.$_POST["acceptid"]]=2;
			$sql = "SELECT confirm from $usingdatabase where id =".$_POST["acceptid"];
			$rs=mysql_query($sql);
			$bb=mysql_fetch_row($rs);

			$sql="UPDATE $usingdatabase set confirm = ".($bb[0]+1)." where id=".$_POST["acceptid"];
			$rs=mysql_query($sql);
			$nowcount=($bb[0]+1);


			$sql = "SELECT msg from $usingdatabase where id =".$_POST["acceptid"];
			$rs=mysql_query($sql);
			$bb=mysql_fetch_row($rs);

			$queuescript= "<script>document.getElementById('block".$_POST["acceptid"]."').scrollIntoView();</script>";

			if ($nowcount==$oknum){
				include("accesstoken.php");
				$page_id = '796764687100390';
				$data['picture'] = "http://www.monoame.com/app/str2pic.php?str=".html_entity_decode($bb[0]);
				$data['link'] = "http://www.monoame.com/app/str2pic.php?str=".html_entity_decode($bb[0]);
				$data['message'] = "#靠北交大V2_第".$_POST["acceptid"]."號靠北\n\n".html_entity_decode($bb[0])."\n\n 匿名發文: http://goo.gl/n60YXG";
				//$data['caption'] = "Caption";
				//$data['description'] = "Description";
				$data['access_token'] = $page_access_token;
				$post_url = 'https://graph.facebook.com/'.$page_id.'/feed';
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $post_url);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$return = curl_exec($ch);

				$sql = "update $usingdatabase set postid=\"".str_replace("\"","'",$return)."\" where id =".$_POST["acceptid"];
				$rs=mysql_query($sql);

				curl_close($ch);
				echo "<script> alert('貼文".$_POST["acceptid"]."經過您的核准後達到門檻 已經發布'); </script>";
			}
		}
	}
	if ($_SESSION[$ip.'decline'.$_POST["acceptid"]]==0){
		if  ( $_POST["nobtn"] && $_POST["acceptid"]){
			$_SESSION[$ip.'decline'.$_POST["acceptid"]]=2;
			$sql = "SELECT confirm from $usingdatabase where id =".$_POST["acceptid"];
			$rs=mysql_query($sql);
			$bb=mysql_fetch_row($rs);

			$sql="UPDATE $usingdatabase set confirm = ".($bb[0]-1)." where id=".$_POST["acceptid"];
			$rs=mysql_query($sql);
			$nowcount=($bb[0]-1);
			echo "<script>document.getElementById('block".$_POST["acceptid"]."').scrollIntoView();</script>";



		}
	}


	include("organic_alpha_lib.php");
	
	$sql="SELECT * FROM cbnctu where confirm > -5 and confirm < 4 limit 5";
	$sqlresult= sql($sql);
	echo multiplexer(file_get_contents("testinject2.html"),$sqlresult);

   if ($queuescript){
   	   echo $queuescript;
   }

?>
		
		  

	</body>
</html>

