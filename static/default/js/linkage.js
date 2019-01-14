var http_url="";
function get_child_tr(id,linkage_type_id){
    try{//alert($("#linkage_tr_"+id+" > div").attr("display"));
	   if($("#linkage_tr_"+id+" > div").html()!=null){
		   if($("#linkage_tr_"+id+" > div").css("display")=='none'){
		       $("#linkage_tr_"+id+" > div").css({"display":""});
		   }else{
			   $("#linkage_tr_"+id+" > div").css({"display":"none"});   
		   }
		   return false;
	   }
	   $.getJSON(http_url+"/index.php/post/getChildLinkageForList/?linkage_type_id="+linkage_type_id+"&linkage_id="+id,function(data){
		     $("#linkage_tr_"+id).append(data);  	   	  
	   })
			
	}catch(e){alert(e.message);}	
}
function cg_sele(cid,mid,eobj,select_name,span_id,linkage_type_id){  //联级菜单，无限极
try{
    var which_sel=0; //对象是第几个下拉菜单
	var is=false;
	var span_id="#"+span_id;
	var load_span=span_id+"_load";
	if(eobj && getSelectedText(eobj)=='--选择--'){
		return false;
	}
	var sb=span_id.replace('#','');
	var selects=$("#"+sb+" > select");
	s_a=selects.length;
	for(var j=0;j<s_a.length;j++){
	    if(selects[j]==eobj){
			is=true;
		   which_sel=j;
		}
		if(is==true && j>which_sel){  //把后面的select 删掉
		   selects[which_sel+1].outerHTML='';
		}
	}
	if(mid=='0' || mid=='last_parent'){
        return false;	   	
	}
	var act=mid?"getChildClass":"getCateChildClass";
	$(load_span).html('<img src="'+http_url+'/static/default/images/loading8.gif">');
	var ur=http_url+"/index.php/post/"+act+"/?linkage_type_id="+linkage_type_id+"&parent_id="+mid+"&select_name="+select_name+"&span_id="+span_id.replace('#','')+'&jsoncallback=?';
	$.getJSON(ur,function (json){
		$(load_span).html(' '); 
		if(json.data==''){
		   return false;
		}
		$(span_id).append(json.data);				
	})
}catch(e){alert(e.message);}
}


function cg_sele_cc(mid,eobj,select_name,span_id,linkage_type_id,selectnum,attr_extern,evalcode){  //联级菜单，无限极
    var which_sel=0; //对象是第几个下拉菜单
	var is=false;
	var span_id="#"+span_id;  //放置 select 的 对象
	var load_span=span_id+"_load";  //loading小图片
	var sb=span_id.replace('#','');//无聊
	var selects=$("#"+sb+" > select");
	s_a=selects.length;
	for(var j=0;j<s_a;j++){  //遍历
	    if(selects[j]==eobj){ //如果属于发生动作的对象
			is=true;  // is 设为 真
		   which_sel=j;  //which_sel  为  这个对象的 键次
		  //alert(which_sel);
		  
		   if((selects[j-1] && selects[j].value==selects[j-1].value)||(selects[which_sel].value=='0')){//alert('aa');
			   for(var j02=which_sel+1;j02<s_a;j02++){
			       selects[j02].outerHTML='';
			   }
			   return false;
		   }else{//alert(22);
			   for(var j02=which_sel+1;j02<s_a;j02++){
			       selects[j02].outerHTML='';
			   }
			   break;
		   }
		}
		if(is==true && j>which_sel){  //把后面的select 删掉
		   selects[j+1].outerHTML='';
		}
			   
		
	}
	
	try{
	    eval(evalcode);		
	}catch(e){}
	if(mid=='0' || mid=='last_parent'){alert('sto');
        return false;	   	
	}//alert('kk');
	if(selectnum && $(span_id+" select").length==selectnum){
	    return false;	
	}
	var act="getCateChildClass";
	$(load_span).html('<img src="'+http_url+'/static/default/images/loading8.gif">');
	var ur=http_url+"/index.php/post/"+act+"/?is_child=1&linkage_type_id="+linkage_type_id+"&parent_id="+mid+"&select_name="+select_name+"&span_id="+span_id.replace('#','')+"&selectnum="+selectnum+"&attr_extern="+attr_extern+"&evalcode="+evalcode+'&jsoncallback=?';
	$.getJSON(ur,function (json){
		$(load_span).html(' ');
		if(json.data==''){
		   return false;
		}
		$(span_id).append(json.data);				
	})
	
}
function cg_edit_sele_cc(id,select_name,span_id,linkage_type_id,parent_id,selectnum,attr_extern,evalcode){   //修改的时候初始化 联级菜单  栏目
    var span_id="#"+span_id;
	var load_span=span_id+"_load";
    $(load_span).html('<img src="'+http_url+'/static/default/images/loading8.gif">');
	$.getJSON(http_url+"/index.php/post/getSelectClassForEdit/?parent_id="+parent_id+"&linkage_type_id="+linkage_type_id+"&linkage_id="+id+"&select_name="+select_name+"&span_id="+span_id.replace('#','')+"&selectnum="+selectnum+"&attr_extern="+attr_extern+"&evalcode="+evalcode+'&jsoncallback=?',function (json){//alert(data);
		$(load_span).html(' ');
		$(span_id).append(json.data);
		try{
		    eval(evalcode);		
		}catch(e){}
	})
}