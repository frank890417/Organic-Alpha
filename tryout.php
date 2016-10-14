<!DOCTYPE html>
<html>
<body>

<h2>version 0</h2>

<script>
	var input = "php.http://www.monoame.com/organic_alpha/sql.php?sql=SELECT+%2A+FROM+message -> multi.length -> main.<123123>";  /* input commend */
	var json = "name msg num"; /*the data you want to get */
	
	var splits = input.split(" ");
	var Json = json.split(" ");

	var tmp = splits[0].split(".");
	var php = tmp[1]; 

	var tmp1 = splits[2].split(".");
	var multi = tmp[1]; 
	
	var tmp = splits[4].split(".");
	var main = tmp[1]; 
	
	var jsontext = '{"length":"3","datas":[{"id":"1","name":"person1","msg":"message1},{"id":"2","name":"person2","msg":"message2},{"id":"3","name":"person3","msg":"message3},{"id":"4","name":"person4","msg":"message4},{"id":"5","name":"person5","msg":"message5}]';
	var text = JSON.parse(jsontext);
	
	function Merge() {
		var output="";
		
		
		for(var i=0;i<5;i++){
			output += "[";
			for(var j=0;j<Json.length;j++){
				output += "{\"tag\" : " + Json[j] + "\"data\" : " + text[i].Json[j] + "}";
			}
			output += "]"
		}
		
		alert(output);
	}
	
</script>

<button onclick="Merge()">merge </button>

<p id="demo_code"></p>

	
	


</body>
</html>


<!--
[   {"tag": "name"     , "data" : "test1"},
	{"tag": "time"     , "data" : "2015/12/15"},
	{"tag": "msg"      , "data" : "message%201"},
	{"tag": "readtime" , "data" : "12:00"}       
]
-->

