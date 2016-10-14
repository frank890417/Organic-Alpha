<html>
<head>
	<meta charset="utf-8"> 
	<title> Organic 圖形編輯器 v0.55</title>
    <link rel="shortcut icon" href="http://www.monoame.com/organic_alpha/organic.ico">
	<link rel="stylesheet" href="http://www.monoame.com/organic_alpha/organic_graphic.css">
	
	 <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
</head>
<body>
	<h2 style="color: white;margin-top: 20px;"> <a href='index.php'> <img src="logouse-02.png" height="60px" /></a> Graphic v 0.55</h2>  
	  <form method='get'style='display:inline;'>		
		    <select name="file"  value=<?php  $file=(isset($_GET["file"])?$_GET["file"]:"script1.organic" ) ; echo $file;?> >
			<?php
				$dir    = './';
				$files1 = scandir($dir);

				foreach ($files1 as $key => $value) {
					if (substr($value,-strlen(".organic"))==".organic"){
						echo "<option ".($value==$file?"selected":"")." value='$value'>$value</option>";
					}
				}

			?>
			
			</select>
			<input  style='display:inline;' type='submit' value="開啟"/>
			<p style='color:white; display:inline;'>現在開啟: <?php echo $file; ?>
		</form>
	   <p id="renderbox" style="color:white;display:none;"></p>
		<div id="result"></div>
		<select id="typesel">
			<option value="source">source</option>
			<option value="injector">injector</option>
			<option value="multiplexer">multiplexer</option>
			<option value="switcher">switcher</option>

		</select>
		<input id="namemoduleinputbox" style="width:100px; display:inline-block;" placeholder="Add Module Box" value="$out2" />
		
		<input id="newmoduleinputbox" style="width: 800px; display:inline-block;" placeholder="Add Module Box" value="output $target;" />
		<div id="addbtn" onclick="dynamic_add_block($( '#typesel' ).val()+' ' +$( '#namemoduleinputbox' ).val()+' ' + $( '#newmoduleinputbox' ).val() );$('#newmoduleinputbox').val('');" style="border-style:solid;border-color:#fff;border-width:1px;border-radius:5px;color:white;padding-top:1px;text-align:center;width:150px;height: 20px;display:inline-block;cursor:pointer;">Dynamic Add</div>
		<textarea id="inputcode" class="comviewer1">
			<?php
				$test=file_get_contents($file);
				echo htmlentities($test);
			?>
		</textarea>
	  <textarea class='comviewer2' id='compiledcode'> </textarea>
	  <textarea class='comviewer3' id='compiledcode3'> </textarea>
	  </body>

	<script>

  var modulewidth=250;
  var moduleheight=150;
  var maxdepth=0;
  stack=[];
  
  page_mouseX=0;
  page_mouseY=0;
  mouse_temp_linknode=null;
  renderset=false;
  rendertime=0;
  selfrendertime=5;

  dragablelist=[];
  appendlist=[];
  htmldivlist=[];
  linelist=[];
  allelementtext="";

  //CLASS-INNODE
	function INNODE(tag,data,postfix,specificfunc){
	   this.tag=tag;
	   this.data=data;
       this.specificfunc=specificfunc;
	   this.postfix=postfix;
	}

	INNODE.prototype.tag = function tag() {
	    return this.tag;
	};

	INNODE.prototype.data = function data() {
	    return this.data;
	};


	INNODE.prototype.postfixtext = function postfixtext() {
	    tempp=this.postfix;
		if (typeof tempp != "undefined" ){
			if (tempp.length!=0){
			    temprtext="";
			    for(tempindex=0;tempindex<tempp.length;tempindex++){
				 	temppp=tempp[tempindex];
				    temprtext+="->"+temppp;	
				}
			}else{
				return "";
			}
	        return "->exec()"+temprtext;	
		}else{
			return "";
		}

	};


  //CLASS-MODULE_CLASS
	function MODULE_CLASS(modulename,name,type){
	   // Add object properties like this
	   //this.x = x;
	   //this.y = y;
	   this.modulename= modulename;
	   this.name = name;
	   this.type = type;
	   this.depth=0;
	   this.treeheight=240;
	   this.filter=new Array();
	   this.innodelist=new Array();
	   this.outnodelist=new Array();
	  
	   if (modulename=='source'){
	     
	     // this.innodelist.push(new INNODE ("data",null));
	   }
	  
	   //this.constructor();
	}

	MODULE_CLASS.prototype.settitle = function settitle(title) {
	    this.title=title;
	};

	MODULE_CLASS.prototype.find_innode = function find_innode(tag) {
	    for(dataindex=0;dataindex<this.innodelist.length;dataindex++){
			if ( this.innodelist[dataindex]!="undefined")
				if (this.innodelist[dataindex].tag==tag){
					return this.innodelist[dataindex];
				}
			
		}
	};

	MODULE_CLASS.prototype.setdata = function setdata(tag,data,postfix,specificfunc) {
		flag=false;matchdataindex=null;


		if (typeof this.innodelist != "undefined"){
			for(dataindex=0;dataindex<this.innodelist.length;dataindex++){
				if (typeof this.innodelist[dataindex]!="undefined")
				if (this.innodelist[dataindex].tag==tag){
					flag=true;
					matchdataindex=dataindex;
				}
			}
		}

		

		if (flag){
			this.innodelist[matchdataindex].data=data;
      if (typeof specificfunc!="undefined") this.innodelist[matchdataindex].specificfunc=specificfunc;
		}else{
			this.innodelist.push(new INNODE (tag,data,postfix,specificfunc));
	   }
	    return this;
	};

	MODULE_CLASS.prototype.exec = function exec() {
	  if (this.modulename=='source'){
	     if (this.type=='sql'){
	         return (new MODULE_CLASS('source','array').setdata('sqlresultarray!'));
	     }
	  }
	};




	//當加入節點的時候，以此為標準更新每個節點的深度(位置排列用)
	
	function updata_depth(stemp,treeeightupdata){
	  
		stemp.depth++;
		if (stemp.depth>maxdepth){
			maxdepth=stemp.depth;
		}
	  var totalheight=stemp.treeheight;
	  var tth=treeeightupdata;
	  var totalcount=0;
	  var maxdepth_treeheight=0;
	  var tempinnodelist=stemp.innodelist;
	        for (var index = 0; index < tempinnodelist.length; ++index) {

	              tempinnode=tempinnodelist[index];
	            
	             if (typeof(tempinnode.data)=="object"){  
	                 if (tth==1){
	                    if (tempinnode.data.depth>maxdepth_treeheight){
	                        totalheight=stemp.treeheight+tempinnode.data.treeheight;
	                        maxdepth_treeheight=tempinnode.data.depth;
	                        totalcount=1;
	                    }else if (tempinnode.data.depth==maxdepth_treeheight){
	                       totalheight+=tempinnode.data.treeheight;
	                       totalcount++;
	                    }
	                 }
	             	   updata_depth(tempinnode.data,0);

	                 
	              }else{
	                　
	              }
	        }
	   if (tth==1){
	     stemp.treeheight=totalheight;
	   }

	}

	//以名稱檢所模組清單
	function get_element_by_name(findname){
		//console.log(name);
		var nameindex=0;
		for(nameindex=0;nameindex<stack.length;nameindex++){
			if (stack[nameindex].name==findname){

				return stack[nameindex];
			}
		}
	}

	//轉換收到的文字變成純文字 或是模組以供連結
	function transform_element(sss){
		if (typeof sss !="undefined"){
			sss=sss.trim();
      specificfunc=null;

			if (sss[0]=="$" || sss[0]=="("){
        var elementname="";

				if (sss[0]=="("){
					var matches=sss.match(/\$[a-zA-Z\_]*\-/);
					specificfunc=sss;
					if (matches){


					if (matches.length>0){
						elementname=matches[0];
	          
        	}
        	}
        }
				if (sss[0]=="$")
					elementname=sss.split("-")[0].trim();

				var element = get_element_by_name(elementname);
       
        if (typeof element !="undefined"){
            return Array(element,sss,specificfunc);
        }else{
        	element=sss;
          return Array(element,sss,specificfunc);
        }
				


			}else{
				element=sss;
				return Array(element,sss,specificfunc);
			}

		}

	}

	//取得輸入中文字->之後的執行程式(後墜)
	function get_postfix(sssdata){
		sssdatasplit=sssdata.split("->");
		postfixarray=[];
		for(var i=1;i<sssdatasplit.length;i++){
			if (sssdatasplit.length>1 && i==1 && sssdatasplit[i]=="exec()"){

			}else{
				postfixarray.push(sssdatasplit[i]);
			}
		}
		return postfixarray;
	}
 
	//分析程式碼產生所有的模組
	function parsecode(){
		var inputcode=$('<div/>').html($('#inputcode').html()).text();
		var inputline=spliter(";",inputcode);
		for(i=0;i<inputline.length;i++){
			generateblock(inputline[i]);
		}
		savestack=stack;
		cstack=stack;
	}

	//分析單行文字產生模組
	function generateblock(inputlinecode){
			nowline=inputlinecode;
			lineelement=spliter(" ",nowline);
		    console.log(lineelement);
		    temp="";
			if (lineelement[0]=="source"){
				tempname=lineelement[1];
				temptype=lineelement[2];
				tempdata=transform_element(lineelement[3]);
				var temp = new MODULE_CLASS('source',tempname,temptype);
				temp.setdata('data',tempdata[0],get_postfix(lineelement[2]),tempdata[2]);
			}
			if (lineelement[0]=="multiplexer"){
				tempname=lineelement[1];
				temptarget=transform_element(lineelement[2]);
				tempinject=transform_element(lineelement[3]);
			
				var temp = new MODULE_CLASS('multiplexer',tempname);
				temp.setdata('target', temptarget[0] ,get_postfix(lineelement[2]) , temptarget[2]);
				temp.setdata('injectdata',tempinject[0] ,get_postfix(lineelement[3]) , tempinject[2]);
			
			}
			if (lineelement[0]=="injector"){
				tempname=lineelement[1];
				temptarget=transform_element(lineelement[2]);
				tempinject=transform_element(lineelement[3]);
				var temp = new MODULE_CLASS('injector',tempname);
				temp.setdata('target', temptarget[0] ,get_postfix(lineelement[2]) , temptarget[2]);
				temp.setdata('injectdata',tempinject[0] ,get_postfix(lineelement[3]) , tempinject[2]);
				
			}
			if (lineelement[0]=="switcher"){
				tempname=lineelement[1];
				tempjudge=transform_element(lineelement[2]);
				tempdataa=transform_element(lineelement[3]);
				tempdatab=transform_element(lineelement[4]);
				var temp = new MODULE_CLASS('switcher',tempname);
				temp.setdata('judge', tempjudge[0]  ,get_postfix(lineelement[2]) , tempjudge[2]);
				temp.setdata('yes', tempdataa[0]  ,get_postfix(lineelement[3]), tempdataa[2] );
				temp.setdata('no', tempdatab[0]  ,get_postfix(lineelement[4]), tempdatab[2] );
				
				
			}

			if (temp!=""){
				stack.push(temp);
				updata_depth(temp,1);
			}
			return temp;
	}


	function generate_title(stemp){
	   title=stemp.modulename+((stemp.type?"-"+stemp.type:"") + "("+stemp.depth+")");
	    if (stemp.modulename=="source"){
	       title="⊙"+title;
	    }
	    if (stemp.modulename=="injector"){
	       title="▽"+title;
	    }
	    if (stemp.modulename=="multiplexer"){
	       title="∈"+title;
	    }
	    if (stemp.modulename=="switcher"){
	       title="◈"+title;
	    }
	    return "<p class='bigtitle title'>"+title+"</p>";
	}

	function generate_line(title,text){
	    if (title.indexOf("source") > -1){
	       title="⊙"+title;
	    }
	    return "<p class=linetitle><b>"+title+": </b></p>"+ "<p class=linetext> "+text+"</p><br>"
	}

	function generate_innode_line(title,text){
	    return "<p class=linetitle id=''><b>"+title+": </b></p>"+ "<p class=linetext> "+text+"</p><br>"
	}

	function linedraw(ax,ay,bx,by,lineid)
	{

	    if (ay<by){
	    	tempax=ax;
	    	tempay=ay;
	    	ax=bx; ay=by;
	    	bx=tempax;by=tempay;
	    }
	    var calc=Math.atan2((by-ay),(bx-ax));
	    calc=calc*180/Math.PI-90;
	    if (ay<by){
	    	calc=-calc;
	    }
	    
	    var length=Math.sqrt((ax-bx)*(ax-bx)+(ay-by)*(ay-by));


	    if ($("#"+lineid).length!=0){
	        //console.log("update"+line_id);
	        $("#"+lineid).css("height",length);
	        $("#"+lineid).css("left",ax);
	        $("#"+lineid).css("top",ay);
	        $("#"+lineid).css("transform","rotate(" + calc + "deg");

	    }else
	        $('body').append("<div class='line linecomeout' id='"+lineid+"' style='height:" + length + "px;width:2px;background-color:white;position:absolute;top:" + (ay) + "px;left:" + (ax) + "px;transform:rotate(" + calc + "deg);-ms-transform:rotate(" + calc + "deg);transform-origin:0% 0%;-moz-transform:rotate(" + calc + "deg);-moz-transform-origin:0% 0%;-webkit-transform:rotate(" + calc  + "deg);-webkit-transform-origin:0% 0%;-o-transform:rotate(" + calc + "deg);-o-transform-origin:0% 0%;'></div>");
	}


	function findTotalOffset(id) {
		obj=document.getElementById(id);
	  var ol = ot = 0;
	  if (obj.offsetParent) {
	    do {
	      ol += obj.offsetLeft;
	      ot += obj.offsetTop;
	    }while (obj = obj.offsetParent);
	  }
	  return {left : ol, top : ot};
	}

	

	$( document ).on( "mousemove", function( event ) {

	  if (renderset==false){
	      setTimeout(renderline,13); 
	      renderset=true;
	  }
	  page_mouseX=event.pageX;
	  page_mouseY=event.pageY;
	 
	  //$( "#log" ).text( "pageX: " + event.pageX + ", pageY: " + event.pageY );

	});
	$( document ).on( "mouseclick", function( event ) {
	  if (renderset==false){
	      setTimeout(renderline,13); 
	      renderset=true;
	  }

	  //$( "#log" ).text( "pageX: " + event.pageX + ", pageY: " + event.pageY );

	});
	
	function renderline(){
	  rendertime++;
	  $("#renderbox").html(rendertime);
	  renderset=false;

		  for (index = 0; index < linelist.length; ++index) {
			line=linelist[index];
			line_id="line_"+line[0]+"_"+line[1];
			aa=findTotalOffset(line[0]);
			bb=findTotalOffset(line[1]);
			linedraw(aa.left+13,aa.top+13,bb.left+13,bb.top+13,line_id);

	      


		}
	  $( ".tempmouse" ).remove();

	   $('body').append("<div class='line nodecircle linecomeout' style='display:none;position:absolute;left:" +  (page_mouseX -15) + "px;top:" + (page_mouseY-15) + "px;'></div>");
	   if (mouse_temp_linknode){
	      line=linelist[index];
	      
	      aa={left: page_mouseX,top: page_mouseY};
	      bb=findTotalOffset(mouse_temp_linknode);

	      linedraw(aa.left+5,aa.top+5,bb.left+13,bb.top+13,"tempmouse");



	   }
	   if ((selfrendertime--) >0){
	      setTimeout(renderline(),500);
	   }
	   
	}

	function spliter(delimiter,line){
			ll=line.length;
			result_array=new Array();
			temp="";
			flag_slash = false;
			inflag_dq = false;
			inflag_pare = false;

			
			for(ind=0;ind<ll;ind++){
				cur_char=line[ind];
				//echo "nowchar: cur_char <br>";
				if (flag_slash){
					temp+=cur_char;
					flag_slash=false;
				}else if (cur_char=='\\'){
					temp+='\\';
					flag_slash=true;
				}else if (cur_char=='"'){
					temp+='"';
					if (inflag_dq) {
						inflag_dq=false;
					}else
						inflag_dq=true;
				}else if ((cur_char=='(' || cur_char==')') && inflag_dq==false){
					if (cur_char=='('){
						temp+='(';
						inflag_pare =true;
					}else if (cur_char==')'){
						temp+=')';
						inflag_pare =false;
					}
				}else if (cur_char==delimiter && inflag_dq==false && flag_slash==false && inflag_pare==false){
					if (temp.trim()!=""){
						temp=temp.trim();
						result_array.push(temp);
					}
					temp="";
				}else{
				
					temp+=cur_char;

					
				}
			}
			if (temp.trim()!=""){
						temp=temp.trim();
						result_array.push(temp);
			}
			temp="";
			//var_dump(result_array);
			return result_array;

		}


	function compile(){
	    var compiledcode="";
	     for (index = 0; index < stack.length; ++index) {
	         stemp=stack[index];
	        compiledcode += stemp.modulename;
	        compiledcode +=" "+ stemp.name;
	         if (stemp.modulename=="source"){
	            compiledcode +=" "+ stemp.type;
	         }
	        tempinnodelist=stemp.innodelist;
	            for (index2 = 0; index2< tempinnodelist.length; ++index2) {
	              tempinnode=tempinnodelist[index2];
	              if (typeof(tempinnode.data)=="object"){  
	                      compiledcode +=" "+tempinnode.data.name+tempinnode.postfixtext();
	                     
	              }else{
	                   compiledcode +=" "+tempinnode.data+"";
	              }
	         }
	         compiledcode += ";\n";
	     }
	    return compiledcode;
	}

	function php_node2code(node,postfix){
		tempinnode=node;
        if (typeof(tempinnode.data)=="object"){  
	        return tempinnode.data.name+tempinnode.postfixtext()+postfix;       
	    }else{
	        return tempinnode.data;
	    }

              
	}


  function compile_php(){
      var compiledcode="\<\?php \n\n////AUTO-COMPILED BY ORGANIC ALPHA\n\n\ninclude(\"organic_alpha_lib.php\");\n\n ";
      var outputcode="";
       for (index = 0; index < stack.length; ++index) {
          stemp=stack[index];
           if (stemp.modulename=="source"){
              compiledcode +=stemp.name+" = new organic_source(\""+stemp.type+"\","
              tempinnode=stemp.find_innode("data");
              compiledcode +=" "+php_node2code(tempinnode,"->exec()");
              compiledcode +=")";
			  if (stemp.type=="output"){
			  	 outputcode+="echo "+ stemp.name+ "->exec();\n";
			  }
           }
           if (stemp.modulename=="injector"){
              compiledcode +=stemp.name+" = new organic_injector(";
              tempinnode=stemp.find_innode("target");
              compiledcode +=" "+php_node2code(tempinnode,"");
              tempinnode=stemp.find_innode("injectdata");
              compiledcode +=","+php_node2code(tempinnode,"");
              compiledcode +=")";
           }
           if (stemp.modulename=="multiplexer"){
              compiledcode +=stemp.name+" = new organic_multiplexer(";
              tempinnode=stemp.find_innode("target");
              compiledcode +=" "+php_node2code(tempinnode,"");
              tempinnode=stemp.find_innode("injectdata");
              compiledcode +=","+php_node2code(tempinnode,"");
              compiledcode +=")";
           }
           if (stemp.modulename=="switcher"){
              compiledcode +=stemp.name+" = new organic_switcher(";
              tempinnode=stemp.find_innode("judge");
              compiledcode +=" "+php_node2code(tempinnode,"");
              tempinnode=stemp.find_innode("yes");
              compiledcode +=","+php_node2code(tempinnode,"");
              tempinnode=stemp.find_innode("no");
              compiledcode +=","+php_node2code(tempinnode,"");
              compiledcode +=")";
           }
          compiledcode += ";\n\n";
       }
       compiledcode +="////輸出所有型別為output的source資料\n\n"+outputcode;
       compiledcode +="?>";
      return compiledcode;
  }



	function render_all_block(){
		for (i=0;i<stack.length;i++){
			stemp=stack[stack.length-i-1];  
			renderhtml=render_block(stemp);
			allelementtext=renderhtml+allelementtext;
		}
	}

	function render_block(stemp){
     
	    htmldivlist.push(stemp.name);
	    echotext="<div class='element dragable depth"+stemp.depth+" organic_"+stemp.modulename+"' id='"+stemp.name.substr(1)+"'>";
	    dragablelist.push(stemp.name.substr(1));
	    echotext+=generate_title(stemp);
	  
	   //+generate_line("MODULE TYPE",stemp.modulename+(stemp.type?"-"+stemp.type:"")) ;
	    echotext+=generate_line("MODULE NAME",stemp.name) ;

	    elementnodeid="element_outnode_"+stemp.name.substr(1);
	    if (typeof(stemp)=="object"){ 
	        tempinnodelist=stemp.innodelist;
	        for (index = 0; index < tempinnodelist.length; index++) {

	           tempinnode=tempinnodelist[index];
	             nodeid="innode_"+stemp.name.substr(1)+"_"+tempinnode.tag;
	             echotext+="<div class=nodecircle id='"+nodeid+"'></div>";
	             if (typeof(tempinnode.data)=="object"){  
	                target_element_node="element_outnode_"+tempinnode.data.name.substr(1);
	             	  linelist.push(new Array(target_element_node,nodeid));
	                  echotext+=generate_line("IN-["+tempinnode.tag+"]","[MOD]"+tempinnode.data.name) ;
	                  if ( $.inArray(tempinnode.data.name,htmldivlist)==false)
	                      stack.push(tempinnode.data);
	                  appendlist.push(tempinnode.data.name.substr(1)+";"+"(innode of "+stemp.name+")"+tempinnode.postfixtext());
	              }else{
	                　echotext+=generate_line("IN-["+tempinnode.tag+"]", $('<div/>').text(tempinnode.data).html()) ;
	              }
	        }
	    }
	    
	    echotext+="<div class='element_outnode' id='"+elementnodeid+"'></div>";
	    echotext+="</div>";

	    return echotext;
	 
	
	}

  //排列模組方塊
	function arrangeblocks(){
		var middledisplay=300;
		
		//排列不同深度的物件
		for (dd=1;dd<=maxdepth;dd++){
			leftspace=(maxdepth-dd)*(modulewidth+50)+30+"px";
		    lld=$(".depth"+dd).length-1;
		    lasttopvalue=-1;

		    $(".depth"+dd).each(function( index ) {
		      treeh=get_element_by_name("$"+$(".depth"+dd)[index].id).treeheight;
		      eleheight=0;
		      ntopvalue=eleheight+(index)*(moduleheight)+150+treeh*0.25;
		      topvalue=ntopvalue;

		      if (lasttopvalue==-1){
		      	lasttopvalue=topvalue;
		      }else{
		      	if (topvalue>lasttopvalue){
		      		if (topvalue-lasttopvalue>moduleheight*1.5){
			      		topvalue=lasttopvalue+moduleheight*1.5;
			      	}
			      	if (topvalue-lasttopvalue<moduleheight*1.2){
			      		topvalue=lasttopvalue+moduleheight*1.2;
			      	}
		      	}
		      	if (topvalue<lasttopvalue){
		      		if (lasttopvalue-topvalue>moduleheight*1.5){
			      		topvalue=lasttopvalue-moduleheight*1.5;
			      	}
			      	if (lasttopvalue-topvalue<moduleheight*1.2){
			      		topvalue=lasttopvalue-moduleheight*1.2;
			      	}
		      	}
		      	lasttopvalue=topvalue;
		      	
		      }

		    	topspace=topvalue+"px";
		     $( this ).css("left",leftspace);
			   $( this ).css("top",topspace);

		     $( this ).css("animation-name","blockout");
		     $( this ).css("animation-timing-function","ease-out");
		     $( this ).css("animation-duration",(maxdepth-dd)*0.4+"s");
		     
			});

		}

	}
	
	function remove_line_by_name(name){
		for (index = 0; index < linelist.length;index ++) {
			if (linelist[index][0]==name || linelist[index][1]==name){
				linelist.splice(index,1);
			}
		}
	}
	function active_nodes(){
		$(".nodecircle").click(function(event) {
		      if (mouse_temp_linknode!=""){
		        if (mouse_temp_linknode!=(event.target.id)){
		          mouse_temp_linknode=(event.target.id);
		        }
		      }else{
		          mouse_temp_linknode=(event.target.id);
		     }
		 
		    });

		$(".element_outnode").click(function(event) {
		     if (mouse_temp_linknode!=""){
		        if (mouse_temp_linknode!=(event.target.id) && (mouse_temp_linknode.substr(0,7)=="element" ^ event.target.id.substr(0,7)=="element" )){
		           
		           linkfrom=mouse_temp_linknode;
		           linkto=event.target.id;

		           if (linkfrom.substr(0,7)!="element"){
		               templinkto=linkfrom;
		               linkfrom=linkto;
		               linkto=templinkto;
		           }

		           linkfromid=linkfrom.substr(8);
		           linktoid=linkto.substr(7);
		 			console.log("remove : "+linkto);

		           remove_line_by_name(linkto);
		           
		           temppair=new Array(linkfrom,linkto);
		           linelist.push(temppair);
		           console.log("link: "+linkfromid+ " , " +linktoid);

		           elementname=linkfromid.replace("outnode_","");
		           innodeelementname=linktoid.substr(0,linktoid.lastIndexOf("_"));
		           innodetag=linktoid.substr(linktoid.lastIndexOf("_")+1);


		            var temp = get_element_by_name("$"+innodeelementname);
		            temp.setdata(innodetag,get_element_by_name("$"+elementname) );

		            compile();


		          $('#compiledcode').html(compile);
		           //elementfrom= get_element_by_name("");
		           //elementto=;

		           mouse_temp_linknode="";

		           $( ".line" ).remove();
		            renderline();
		            mouse_temp_linknode="";
		        }else{
		        	remove_line_by_name(linkto);
		        	$( ".line" ).remove();
		        	mouse_temp_linknode="";
		        	 //mouse_temp_linknode="";$( ".line" ).remove();
		        }
		      }else{
		          mouse_temp_linknode=(event.target.id);
		     }
		});
	}

  function dynamic_add_block(tempinputcode){
    $('#result').append(render_block(generateblock(tempinputcode)));
    renderline();
    $(".dragable").draggable(); 
     arrangeblocks();
     active_nodes();

  }

  
  parsecode();
  render_all_block();
  $('#result').append(allelementtext);
  $('#compiledcode').html(compile);
  $('#compiledcode3').html(compile_php());


   for (index = 0; index < appendlist.length; ++index) {
      nowappend=appendlist[index];
      appendname=nowappend.split(";")[0];
      appendcontent=nowappend.split(";")[1];
      $("#"+appendname).append(appendcontent+"<br>"); 
    }

  $( document ).ready(function() {
      renderline();
  });

  $(".dragable").draggable(); 
   arrangeblocks();
   active_nodes();



	</script>
</html>

