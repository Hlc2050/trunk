// JavaScript Document
$(document).ready(function(){
	try{	
	//聊天区域大小调整	
	objDiv = document.getElementById('dragLine');
	maindv = document.getElementById('chat_main_area_body');
	fumaindv = document.getElementById('private_chat_area_body');
  	dragline(objDiv,maindv,fumaindv);
	}catch(e){alert(e.message);}
	
	//定时获取消息数据
	get_message_data();
	if(interval_time<2){
		interval_time=4;
	}
	setInterval("get_message_data()",interval_time*1000);	
	
	
	
	//鼠标移动聊天对象显示
	$("#to_uname_box").hover(function(){
	    $("#to_uid_box").show();
	},function(){
		$("#to_uid_box").hide();
	});
	
	//表情
	//创建表情分类
	load_emot_cate();
	//鼠标移动聊天对象显示
	$("#element_show_emot").hover(function(){
	    $("#emot_main_box").show();
	},function(){
		$("#emot_main_box").hide();
	});
	//初始化私聊对象
	init_privateusers();
	
})

//聊天区域大小调整	
function dragline(dv,maindv,fumaindv){
  dv.onmousedown=function(e){
      var d=document;
      e = e || window.event;
      var y= e.pageY;
	  var initheight=$(maindv).height();
	  var initfuheight=$(fumaindv).height();
	  //设置捕获范围
      if(dv.setCapture){
          dv.setCapture();
      }else if(window.captureEvents){
          window.captureEvents(Event.MOUSEMOVE | Event.MOUSEUP);
      }
	  
      d.onmousemove=function(e){
           e= e || window.event;
           if(!e.pageY)e.pageY=e.clientY;
           var ty=e.pageY-y;
		   var height=initheight+ty;
		   var fuheight=initfuheight-ty;
		   if(height<=350 && height>=30){
               maindv.style.height=height+'px';
			   fumaindv.style.height=fuheight+'px';
		   }
      };

      d.onmouseup=function(){
           //取消捕获范围
           if(dv.releaseCapture){
              dv.releaseCapture();
           }else if(window.captureEvents){
              window.captureEvents(Event.MOUSEMOVE|Event.MOUSEUP);
           }
          
          //清除事件
          d.onmousemove=null;
          d.onmouseup=null;
      };
   };
}

//消息集中大处理
function  get_message_data(){
	//请求在线用户、聊天消息 、公告 等
	$.get("/room/?m=get_message_data&room_id="+room_id+"&jsoncallback=?",function(ret){
        try{
		var data=eval("("+ret+")");
		if(data.code==-2){
		    window.location='/member.php?m=show_login&url='+document.URL;
			return;	
		}
		show_add_online(data.onlinedata);    //追加新增用户
		show_del_online(data.leavesdata);    //删除离开用户
		show_public_message(data.message);    //追加公聊消息
		show_private_message(data.private_message);    //追加私聊消息
		show_gift_record(data.gift_record);    //显示礼物赠送
		
		}catch(e){alert(e.message);}		
	})
}

/****************显示各类数据***********************/

//显示在线人数
function show_add_online(onlinedata){
	var liobj=$("#onlineuserlist li");
	var length=liobj.length;
	var inituseridarr=[];
	for(var i=0;i<length;i++){
	    inituseridarr.push(liobj.eq(i).attr("uid"));	
	}
	var code2='';
	for(var i=0;i<onlinedata.length;i++){
		var isexist=0;
		for(var i2=0;i2<inituseridarr.length;i2++){
		    if(inituseridarr[i2]==onlinedata[i].uid&&onlinedata[i].uid>0){
			    isexist=1; 	
			}
			if(inituseridarr[i2]&&inituseridarr[i2]==onlinedata[i].sessionid){
			    isexist=1; 	
			}
		}	
		if(isexist==0){
			if(onlinedata[i].uid>0){
				var code='<li id="onlineuuuu_'+onlinedata[i].uid+'" uid="'+onlinedata[i].uid+'" '+($("#onlineuserlist li").length%2==0?'':'class="bg"')+'>'+onlinedata[i].uname+'</li>';
				$("#onlineuserlist").append(code);
				code2='<div>'+onlinedata[i].uname+' 进入了房间</div>';
				show_user_card("#onlineuuuu_"+onlinedata[i].uid);
			}else{
				var code='<li uid="'+onlinedata[i].sessionid+'" '+($("#onlineuserlist li").length%2==0?'':'class="bg"')+'>游客'+customer_name(onlinedata[i].sessionid)+' </li>';
				$("#onlineuserlist").append(code);
				code2='<div>游客'+customer_name(onlinedata[i].sessionid)+' 进入了房间</div>';	
			}
			
		}
	}
	append_message(code2);
	$("#room_num").html(onlinedata.length);
}
//删除离开用户
function show_del_online(leavesdata){
	try{//alert(leavesdata);
	var liobj=$("#onlineuserlist li");
	var length=liobj.length;
	var code='';
	for(var i=0;i<length;i++){
	    for( var i2=0;i2<leavesdata.length;i2++){
			if(liobj.eq(i).attr("uid")==leavesdata[i2].uid){
			    liobj.eq(i).remove();
                code+='<div>'+leavesdata[i2].uname+' 离开了房间 </div>';
			}
			if(liobj.eq(i).attr("uid")==leavesdata[i2].sessionid){
			    liobj.eq(i).remove();
                code+='<div>游客'+customer_name(leavesdata[i2].sessionid)+' 离开了房间 </div>';
			}
		} 	
	}
	append_message(code);
	}catch(e){alert(e.message);}
}
//显示公聊消息
function show_public_message(messagedata){
    try{
	var code='';
    for(var i=0;i<messagedata.length;i++){
	    var mg=messagedata[i];
		code+='<div>['+C.date.date('H:i:s',mg.create_time)+']<a href="javascript:void;" title="点击私聊" onclick="change_to_uid('+mg.uid+',\''+mg.uname+'\')">'+mg.uname+'</a>  说：'+mg.message_content+'<div>';
	
	}
	append_message(code);
    
	}catch(e){alert(e.message);}
}
//追加信息（为什么单独，因为统一在这里处理控制滚动条等）
function append_message(code,divid){
	code=code.replace(/\[\/(.*?):(.*?)\]/g,'<img src="/css/default/images/emot/$1/$2.gif" />');
	if(!divid) divid='chat_main_area_body';
    var dv=document.getElementById(divid);
	var st=dv.scrollHeight;
    $("#"+divid).append(code);
    var newst=dv.scrollHeight;
    dv.scrollTop+=newst-st;
}
//追加礼物公告栏的礼物
function append_gift_record(code,divid){
	if(!divid) return;
    var dv=document.getElementById(divid);
	var st=dv.scrollHeight;
    $("#"+divid).append(code);
	if($("#"+divid+" div.giftrecordli").length>6){
	    $("#"+divid+" div.giftrecordli:first").remove();	
	}
    var newst=dv.scrollHeight;
    dv.scrollTop+=newst-st;
	
}

//显示私聊消息
function show_private_message(messagedata){
    try{
	var code='';
    for(var i=0;i<messagedata.length;i++){
	    var mg=messagedata[i];
		if(user_info.uid==mg.to_uid){
			code+='<div>['+C.date.date('H:i:s',mg.create_time)+'] <a href="javascript:void;" onclick="change_to_uid('+mg.uid+',\''+mg.uname+'\')">'+mg.uname+'</a> 对我 说：'+mg.message_content+'<div>';
		}else{
		    code+='<div>['+C.date.date('H:i:s',mg.create_time)+'] 我对<a href="javascript:void;" onclick="change_to_uid('+mg.to_uid+',\''+mg.to_uname+'\')">'+mg.to_uname+'</a>   说：'+mg.message_content+'<div>';
		}
	}
	append_message(code,'private_chat_area_body');
	}catch(e){alert(e.message);}
}

//显示赠送礼物
function show_gift_record(gift_recorddata){
    try{
	var code='';
	var giftcode='';
    for(var i=0;i<gift_recorddata.length;i++){
	    var mg=gift_recorddata[i];
		giftcode+='<div class="giftrecordli">['+C.date.date('H:i:s',mg.create_time)+'] <a href="javascript:void;" onclick="change_to_uid('+mg.uid+',\''+mg.uname+'\')">'+mg.uname+'</a> 赠送了 '+mg.to_uname+' '+mg.count+' 个 '+mg.gift_name+'   <div>';	
		if(mg.to_uid==room_info.uid){
		    code+='<div>['+C.date.date('H:i:s',mg.create_time)+'] <a href="javascript:void;" onclick="change_to_uid('+mg.uid+',\''+mg.uname+'\')">'+mg.uname+'</a> 赠送了 '+mg.to_uname+' '+mg.count+' 个 '+mg.gift_name+'   <div>';			
		}
		
	}
	append_gift_record(giftcode,'gift_record_main_body');
	append_message(code,'chat_main_area_body');
	}catch(e){alert(e.message);}
}


//发送消息
function post_message(){
    try{
    $("#chattxt").focus();
	var message_content=$("#chattxt").val();
    if(message_content.match(/^\s*$/g)){
	    return false;
	}
	$("#chattxt").val('');
	
	message_content=encodeURIComponent(message_content);
    $.get("/room/?m=post_message&room_id="+room_id+"&to_uid="+$("#to_uid").val()+"&message="+message_content,function(ret){
	    try{
		var data=eval("("+ret+")");
		if(data.code<1){
			
		    var private_notice='<div class="private_notice">'+data.statewords+'</div>';
			append_message(private_notice,'private_chat_area_body');
			return false;
		}else{
		    //发送成功 
			get_message_data();  
		}
		}catch(e){
			alert(e.message);
		}
	})
	}catch(e){alert(e.message);}
}


//改变聊天对象
function change_to_uid(uid,uname){
    try{
	$("#to_uid").val(uid);	
	$("#to_uname").html(uname);	
	$("#chattxt").focus();
	$("#to_uid_box").hide();
	change_privateusers_cookie(uid,uname);
	}catch(e){alert(e.message);}	
}
//把私聊对象写进cookie
function change_privateusers_cookie(uid,uname){
    try{
	if(!uid)return;
	var privateuserdata=[];
	if($.cookie("privateuserdata")==null){
	    privateuserdata.push({"uid":uid,"uname":uname});
		$.cookie("privateuserdata",encodeURIComponent($.toJSON(privateuserdata)),{ expires: 300 });
		$("#to_uid_box").append('<li class="privateuid_'+uid+'"><span class="pdel r" onclick="del_private_user('+uid+');">删除</span><a href="javascript:void(0);" onclick="change_to_uid('+uid+',\''+uname+'\')">'+uname+'</a></li>');	
	}else{
		var is_exists=0;
		var privateuserdata=eval("("+decodeURIComponent($.cookie("privateuserdata"))+")");
		for(var i=0;i<privateuserdata.length;i++){
		    if(privateuserdata[i].uid==uid){
			    is_exists=1;	
			}
			
		}
		if(is_exists==1){
		    return false;	
		}else{
			privateuserdata.push({"uid":uid,"uname":uname});
			$.cookie("privateuserdata",encodeURIComponent($.toJSON(privateuserdata)),{ expires: 300 });
			$("#to_uid_box").append('<li class="privateuid_'+uid+'"><span class="pdel r" onclick="del_private_user('+privateuserdata[i].uid+');">删除</span><a href="javascript:void(0);" onclick="change_to_uid('+uid+',\''+uname+'\')">'+uname+'</a></li>');	
		}
	}	
	}catch(e){alert(e.message);}
}
//删除私聊对象
function del_private_user(uid){
	try{
	if($.cookie("privateuserdata")){
		var is_exists=0;
		var privateuserdata=eval("("+decodeURIComponent($.cookie("privateuserdata"))+")");
		for(var i=0;i<privateuserdata.length;i++){
		    if(privateuserdata[i].uid==uid){
			   privateuserdata.remove(i);
			   $(".privateuid_"+uid).remove();
			}
		}
		$.cookie("privateuserdata",encodeURIComponent($.toJSON(privateuserdata)));
	}	
	}catch(e){alert(e.message);}
}
//初始化私聊
function init_privateusers(){
    try{
	if($.cookie("privateuserdata")){
		var is_exists=0;
		var privateuserdata=eval("("+decodeURIComponent($.cookie("privateuserdata"))+")");
		for(var i=0;i<privateuserdata.length;i++){
		    $("#to_uid_box").append('<li class="privateuid_'+privateuserdata[i].uid+'"><span class="pdel r" onclick="del_private_user('+privateuserdata[i].uid+');">删除</span><a href="javascript:void(0);" onclick="change_to_uid('+privateuserdata[i].uid+',\''+privateuserdata[i].uname+'\')">'+privateuserdata[i].uname+'</a></li>');	
		}
	}	
	}catch(e){alert(e.message);}
}

//游客的名字处理
function customer_name(str){
	return str.substr(0,6);
}
//加载表情分类并绑定事件
function load_emot_cate(){
	var code='';
    for(var i=0;i<emot_skin.length;i++){
	    code+='<li '+(i==0?'class="current"':'')+' onclick="$(\'.emot_cate_list li\').removeClass(\'current\');$(this).addClass(\'current\')"><a href="javascript:show_emot(\''+i+'\');">'+emot_skin[i].txt+'</a></li>';	
	}	
	$("#emot_cate_list").append(code);
	show_emot(0);
}

//加载表情
function show_emot(i){
	skin=emot_skin[i].name;
    if(emot_skin[i].data==''){
		$.get("/css/default/images/emot/"+skin+"/config.txt?v=1.00002",function(ret){
			try{
			ret=ret.replace("'"+skin+"':",'');
			ret=ret.replace(""+skin+":",'');
			var data=eval("("+ret+")");
			var code='';
			for(var s in data.list){
				code +='<li class="emot_'+skin+'"><a  title="'+data.list[s]+'"  style="width:'+data.width+'px;height:'+data.height+'px;background-image:url(/css/default/images/emot/'+skin+'/'+s+'.gif);" href="javascript:append_emot(\''+skin+':'+s+'\')">&nbsp;</a></li>';	
			}
			emot_skin[i].data=code;
			$(".emot_list").html(emot_skin[i].data);
			}catch(e){alert(e.message+'ret')}
		})	
	}else{
	    $(".emot_list").html(emot_skin[i].data);	
	}
}

//插入表情
function append_emot(emot){
	$("#chattxt").insertContent('[/'+emot+']');
	
}

//清屏
function clear_screen(type){
	if(type=='main'){
		$("#chat_main_area_body").html('');
	}else if(type=="private"){
		$("#private_chat_area_body").html('');
	}
}


Array.prototype.remove=function(dx)
　{
　　if(isNaN(dx)||dx>this.length){return false;}
　　for(var i=0,n=0;i<this.length;i++)
　　{
　　　　if(this[i]!=this[dx])
　　　　{
　　　　　　this[n++]=this[i]
　　　　}
　　}
　　this.length-=1
　}