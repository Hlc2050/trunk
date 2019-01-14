//********************************************************************************************************************************
//实例类，需要 new 修饰符

//实例类：原型
obj=function(params){
	//私有变量，用__开头
	var __params={};
	//初始化
	if(arguments[0]){
		__set(params);
	}
	//私有方法，设置参数
	function __set(params){
		for(var param in params){
			__params[param]=params[param];
		}
	}
	//公有方法，获取参数
	this.params=function(){
		return $.toJSON(__params);
	}
}


//创建上传iframe
function create_upload_iframe(params){
	var a=$.evalJSON(params);
	a['domain']=document.domain;
	params=$.toJSON(a);
	var width=82;
	if(a.width){
		width=a.width;
	}
	var frame_code='<iframe style="margin-left:5px;" src=\'/index.php/upload/uploadForm?params='+encodeURIComponent(params)+'\' width="'+width+'" height="28" frameborder="no" border="0″ marginwidth="0″ marginheight="0" scrolling="no" allowtransparency="yes"></iframe>';
	if(a.inner_box){
		$(a.inner_box).html(frame_code);
	}else{
	    document.write(frame_code);
	}
}
//上传后回调通知函数，可自定义，在创建时参数func带自定义的函数名称
function callback_upload(ret){
	try{
		var json=$.evalJSON(ret);
		if(json.files.length<=0) {
			alert('上传失败');
			return false;
		}
		//设置返回的原始图片地址
		if(json.params.Eid!=''){
			$("#"+json.params.Eid).val(json.files[0].original);
			$("#"+json.params.Eid+"_span").html('<img src="'+json.files[0].original+'" width=24 height=24 />');
		}
	}catch(e){
		alert('err:'+ret);
	}
}

//编辑器的 上传初始化
function  load_editor_upload(textarea_id){
  create_upload_iframe('{"func":"editor_callback_upload22","textarea_id":"'+textarea_id+'","inner_box":"#upbtn_box","btn_skin":"editor","thumb":{"width":"300","height":"300"},"water":1}');
}
//编辑器的上传回调处理
function editor_callback_upload22(ret){
	try{
		var json=$.evalJSON(ret);
		if(json.files.length<=0) {
			alert('上传失败');
			return false;
		}
		eval("var editor="+json.params.textarea_id);
		if(json.files[0].original.match(/(\.gif)|(\.png)|(\.jpg)|(\.bmp)/)){
			editor.pasteHTML('<img src="'+json.files[0].original+'">');
		}else if(json.files[0].original.match(/(\.swf)/)){
			var code='<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fp'+
			'download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="400" height="300" id="flashvars" align="center">'+
            '<param name="allowscriptAccess" value="sameDomain" />'+
'<param name="movie" value="'+json.files[0].original+'" />'+
'<param name="quality" value="high" /><param name="bgcolor" value="#ffffff" />'+
'<embed quality="high" bgcolor="#ffffff" width="400" src="'+json.files[0].original+'" height="300" allowScriptAccess="never" allowNetworking="internal" autostart="0" name="flashvars" align="center" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />'+
'</object>';
			editor.pasteHTML(code);		
		}else{
			var size=(json.files[0].size/1000);
			size=size.toString().replace(/\.\d+/,'');
			var code='<div style="background:#F2EEDD;padding:5px 10px;"><a href="'+json.files[0].original+'" target=_blank>'+json.files[0].original+'</a> <span>文件大小：'+size+' kb</span> <a href="'+json.files[0].original+'" target="_blank">点击下载附件</a> </div>';
			//alert(code);
			//$("#test_inner").html(code);
			editor.pasteHTML(code);
		}
		
		
	}catch(e){
		alert('err:'+e.message);
	}
}


function ck_reg(connect_type,callback){
    try{
	$("#sub01").attr("disabled",true);
	var data="uname="+$("#uname").val()+"&email="+$("#email").val()+"&upass="+$("#upass").val()+"&upass2="+$("#upass2").val()+"&rancode="+$("#reg_rancode").val();
	$.post('/member.php?m=reg',data,function(data){
		//alert(data);
		var jsondata=$.evalJSON(data);
	    if(jsondata.statecode<1){
			$("#sub01").attr("disabled",false);
		    $("#reg_state").html('<span class="ico ng">'+jsondata.words+'</span>');	
		}else{
		    $("#reg_state").html('<span class="ico ok">'+jsondata.words+'</span>');	
			if(!callback){
				setTimeout(function(){
					window.location='/member.php?m=reg_ok';
				},1000);
			}else{
				eval(callback+'()');
				return false;	
			}
			
		}
	})
	return false;
	}catch(e){alert(e.message);return false;}	
}
function ck_login(connect_type,callback){
    try{
	$("#sublogin").attr("disabled",true);
	var data="uname="+$("#login_uname").val()+"&upass="+$("#login_upass").val()+"&rancode="+$("#login_rancode").val();
	$.post('/member.php?m=login&connect_type='+connect_type,data,function(data){
		//alert(data);
		var jsondata=$.evalJSON(data);
	    if(jsondata.statecode<1){
			$("#sublogin").attr("disabled",false);
		    $("#login_state").html('<span class="ico ng">'+jsondata.words+'</span>');	
		}else{
		    $("#login_state").html('<span class="ico ok">'+jsondata.words+'</span>');	
			setTimeout(function(){
				if($("#history_url").val()==''){
					if(!callback){
				    	window.location='/member_center.php';
					}else{
						callback=callback.replace(/__/g,"'");
					    eval(callback);
						return false;	
					}
				}else{
					window.location=$("#history_url").val();
				}
			},1000);
		}
	})
	return false;
	}catch(e){alert(e.message);return false;}	
}

//企业会员登录
function ck_company_login(connect_type,callback){
    try{
	$("#sublogin").attr("disabled",true);
	var data="uname="+$("#login_uname").val()+"&upass="+$("#login_upass").val()+"&rancode="+$("#login_rancode").val();
	$.post('/company/company_member.php?m=login&connect_type='+connect_type,data,function(data){
		//alert(data);
		var jsondata=$.evalJSON(data);
	    if(jsondata.statecode<1){
			$("#sublogin").attr("disabled",false);
		    $("#login_state").html('<span class="ico ng">'+jsondata.words+'</span>');	
		}else{
		    $("#login_state").html('<span class="ico ok">'+jsondata.words+'</span>');	
			setTimeout(function(){
				if($("#history_url").val()){
					window.location=$("#history_url").val();
				}else{
					if(!callback){
				    	window.location='/company/company_manager.php';
					}else{
						callback=callback.replace(/__/g,"'");
					    eval(callback);
						return false;	
					}
					
				}
			},1000);
		}
	})
	return false;
	}catch(e){alert(e.message);return false;}	
}

//绑定QQ openid
function bind_connect(connect_name){
    window.location='/'+connect_name+'_login_callback.php?m=bind';	
}


function refresh_rancode(imgid){
	if(!imgid) return;
    $(imgid).attr("src",$(imgid).attr("src")+'&');	
}
function ck_forgetpassword(){

	var email=$("#forgetmail");
	var randcode=$("#forancode");
	var sub_btn=$("#forgetpassword_sub");
	
	var mail_state_inner=$("#mail_forget_state_inner");
	var rancode_state_inner=$("#racode_forget_state_inner");
	var state_inner=$("#forget_state_inner");
	
	sub_btn.attr("disabled",true);
	rancode_state_inner.html('');
	state_inner.html('');
	mail_state_inner.html('');
	if(email.val().match(/[\w]*@[\w]*\.[\w]*/)==null){
		mail_state_inner.html('<span class="ico ng">邮箱格式不正确</span>');
		sub_btn.attr("disabled",false);
		return false;
	}
    if(randcode.val().replace(/\s*/g,'')==''){
		rancode_state_inner.html('<span class="ico ng">验证码不能为空</span>');
		sub_btn.attr("disabled",false);
		return false;
	}
	var postdata='rancode='+randcode.val()+'&email='+email.val();
	$.post('member.php?ajax=json&m=ck_forgetpassword',postdata,function(jsondata){
		var re=$.evalJSON(jsondata);
		if(re.statecode==0){
			sub_btn.attr("disabled",false);
			state_inner.html('<span class="ico ng">'+re.words+'</span>'); 	
		}else if(re.statecode==1){
			
			sub_btn.attr("disabled",true);
			state_inner.html('<span class="ico ok">'+re.words+'</span>'); 
			setTimeout(function (){
			    window.location='member.php?m=forgetpassword_sendemail_ok';	
			},1000)
			
			
		}
		
	});
	  
	 
	
	 
  	return false;
}

function ck_reset_password(){
	var resetpassword=$("#resetpassword");
	var resetpassword2=$("#resetpassword2");
	var sub_btn=$("#resetpassword_sub");
	
	var resetpassword_state_inner=$("#resetpassword_msg_inner");
	var resetpassword2_state_inner=$("#resetpassword2_msg_inner");
	var state_inner=$("#resetpassword_state_inner");
	
	sub_btn.attr("disabled",true);
	resetpassword_state_inner.html('');
	resetpassword2_state_inner.html('');
	state_inner.html('');
	
	if(resetpassword.val().match(/^(\w){6,20}$/)==null){
		resetpassword_state_inner.html('<span class="ico ng">密码格式不正确</span>');
		sub_btn.attr("disabled",false);
		return false;
	}
    if(resetpassword.val()!=resetpassword2.val()){
		resetpassword2_state_inner.html('<span class="ico ng">两次密码输入不一致</span>');
		sub_btn.attr("disabled",false);
		return false;
	}
	
	var postdata='&forget_pass_code='+$("#forget_pass_code").val()+'&member_id='+$("#member_id").val()+'&password='+resetpassword.val();
	$.post('member.php?ajax=json&m=save_reset_password',postdata,function(jsondata){
		var re=$.evalJSON(jsondata);
		if(re.statecode==0){
			sub_btn.attr("disabled",false);
			state_inner.html('<span class="ico ng">'+re.words+'</span>'); 	
		}else if(re.statecode==1){
			
			sub_btn.attr("disabled",true);
			state_inner.html('<span class="ico ok">'+re.words+'</span>'); 
			setTimeout(function (){
			    window.location='member.php?m=reset_password_ok';	
			},1000)
			
			
		}
		
	});
	return false;	
}
function send_activation_code(){
	$.get("member_center.php?m=send_activation_code",function(data){
		 $("#activation_box").html('<span class="ico ok">已发送到您的邮箱，请注意查收</span>');		
	});
	
}

var vote={
	"boxid":"",
	"sub":function(subject_id){
		try{
		vote.boxid="#voteformbox_"+subject_id;
		vote.disabled();
		var a=$(vote.boxid+"  .veteoption");
		 //alert(a.attr("type"));  //表单类型
		 
		var v=$(vote.boxid+"  .veteoption:checked");
		if(typeof(v.val())=='undefined'){
			alert('请选择！'); 
			vote.disabled(1);
			return false;        		
		}
		var s=[];
		for(var i=0;i<v.length;i++){
		    s.push(v.eq(i).val());	
		}
		var url="/post.php?m=save_vote&subject_id="+subject_id+"&data="+$.toJSON(s)+"&jsoncallback=?";
		$.getJSON(url,function(json){
			try{
			if(json.code==1){
				alert(json.statewords);
				vote.disabled(2);
			}else{
				vote.disabled(1);
				alert(json.statewords);	
			}
		    }catch(e){alert(e.message);}
		})
		
	    }catch(e){alert(e.message);}
	},
	"disabled":function(type){
		if(!type){
	        $(vote.boxid+" .votesub").attr("disabled",true);
		    $(vote.boxid+" .votesub").val($(vote.boxid+" .votesub").val()+"...");      	
		}else if(type==1){
			$(vote.boxid+" .votesub").attr("disabled",false);
		    $(vote.boxid+" .votesub").val($(vote.boxid+" .votesub").val().replace(/\./g,''));  
		}else if(type==2){
			$(vote.boxid+" .votesub").attr("disabled",true);
		    $(vote.boxid+" .votesub").val('已投票');  
		}
		
	}
}
function callback_upload22(ret){
	try{
		//alert(ret);
		var json=$.evalJSON(ret);
		if(json.files.length<=0) {
			alert('上传失败');
			return false;
		}
		var eid=json.params.Eid;
		$("#"+eid).val(json.files[0].original);
		$("#"+eid+"_span").html('<img src="'+json.files[0].original+'"/>');
	}catch(e){
		alert('err:'+e.message);
	}
}
function ck_edit_info(){
   try{
	   $("#sub01").attr("disabled",true);
	   var data="uname_true="+$("#uname_true").val()+
				"&sex="+$("#sex:checked").val()+
			    "&uphone="+$("#uphone").val()+
				"&tou_img="+$("#tou_img").val()
				;
	  $.post("/member_center.php?m=save_edit_info",data,function(data){//alert(data);
		  try{
			  var json=$.evalJSON(data);
			  if(json.code<1){
				  $("#statebox").html('<span class="ico ng">'+json.words+'</span>');
				  $("#sub01").attr("disabled",false);
			  }else{
				  $("#statebox").html('<span class="ico ok">'+json.words+'</span>');
				  $("#sub01").attr("disabled",false);
				  //window.location='/';
			  }	
		  }catch(e){alert(e.message);$("#sub01").attr("disabled",false);}	
	  });
	  return false;
   }catch(e){alert(e.message);return false;}  
}
function ck_edit_password(){
	var old_password=$("#old_password");
	var new_password=$("#new_password");
	var new_password2=$("#new_password2");
	var sub_btn=$("#editpassword_sub");	
	var state_inner=$("#editpassword_state_inner");
	
	sub_btn.attr("disabled",true);
	state_inner.html('');
	
	if(old_password.val().replace(/\s*/,'')==''){
		old_password.focus();
		state_inner.html('请输入您的密码');
		sub_btn.attr("disabled",false);
		return false;
	}
	if(new_password.val().match(/^(\w){6,20}$/)==null){
		new_password.focus();
		state_inner.html('新的密码格式不正确');
		sub_btn.attr("disabled",false);
		return false;
	}
    if(new_password.val()!=new_password2.val()){
		new_password2.focus();
		state_inner.html('确认密码输入不一致');
		sub_btn.attr("disabled",false);
		return false;
	}
	var postdata='old_password='+old_password.val()+'&new_password='+new_password.val();
	$.post('member_center.php?m=save_edit_password',postdata,function(jsondata){
		var re=$.evalJSON(jsondata);
		if(re.statecode==0){
			sub_btn.attr("disabled",false);
			state_inner.html(re.words); 	
		}else if(re.statecode==1){
			
			sub_btn.attr("disabled",true);
			state_inner.html('<span class="ico ng">'+re.words+'</span>'); 
			
			
		}
		
	});
	return false;	
}

function ck_search(){
    try{
	var v=$("#search_txt").val();
	v=v.replace(/'|\"/g,'');
	window.location='/goods-caidan-0-0-0-0-'+v+'-1.html';
	return false;
	}catch(e){alert(e.message);return false;}
}
function add_address(){
	
	
}







//删除收藏
function dele_collect(collect_id){
	if(!confirm('确定删除吗？')){
		return false;	
	}	
	
	$.get("/member_center.php?m=dele_collect&collect_id="+collect_id,function(data){
	try{
	 var json=$.evalJSON(data);
	 if(json.code>0){
		window.location.reload();	 
	}else{
		alert(json.statewords);	
	}
	}catch(e){alert(e.message);}															  																  											
	})
}

//加入收藏
function add_collect(goods_id){
	$.get("/member_center.php?m=save_add_collect&goods_id="+goods_id,function(data){
		try{
		var json=$.evalJSON(data);
		if(json.code>0){
			//window.location='/member_center.php?m=show_collect';
			art.dialog({content:'收藏成功',time:2,cancel:false,icon: 'succeed'});
		}else{
			art.dialog({content:json.statewords,time:2,cancel:false,icon: 'warning'});
		}
		}catch(e){
			//alert(e.message);
			//alert('请登录后使用收藏功能');
			window.location='/member.php?url='+document.URL;
		}	
	})
}
//手机验证码发送
function sent_cellphone_message(postfile){
	var cellphone=$("#cellphone").val();
	var cellphone_rancode=$("#cellphone_rancode").val();
	if(cellphone.match(/^1\d{10}$/)==null){
	    $("#cellstate").html('<span style="color:red;">请输入正确的手机号码</span> ');
		return false;	
	}
	$("#clickgetbtn01").attr("disabled",true);
	var data="cellphone="+cellphone+"&cellphone_rancode="+cellphone_rancode;
    if(!postfile){
	    postfile='member_center.php';
	}
	$.post("/"+postfile+"?m=sent_cellphone_message",data,function(data){
		try{
		var json=$.evalJSON(data);
		if(json.code>0){
			$("#cellstate").html('<span class="ico ok"> 发送成功</span> ');
			try{
			var n=60;
			var trysendtime=setInterval(function(){
				n--;	
				$("#trysendtimebox").html(n+" 秒后可再次发送");
				if(n==0){
				    $("#clickgetbtn01").attr("disabled",false);
					$("#trysendtimebox").html('');
					clearInterval(trysendtime);	
				}
			},1000);
			}catch(e){alert(e.message);return false;}
			return;
		}else{
			$("#clickgetbtn01").attr("disabled",false);
			$("#cellstate").html('<span style="color:red;">'+json.statewords+'</span> ');
			return false;
		}
		}catch(e){
			alert(e.message);
			//alert('请登录后使用收藏功能');
			//window.location='/member.php?url='+document.URL;
		}	
	})
	return false;
}

//检查手机验证码是否正确以及绑定手机
function bind_cellphone(postfile){
	var cellphone=$("#cellphone").val();
	var cellphone_rancode=$("#cellphone_rancode").val();
	var cellphone_verify_code=$("#cellphone_verify_code").val();
	if(cellphone.match(/^1\d{10}$/)==null){
	    $("#verifystate").html('<span style="color:red;">请输入正确的手机号码</span> ');
		return false;	
	}
	if(cellphone_rancode.match(/^[\w]{4}$/)==null){
	    $("#verifystate").html('<span style="color:red;">请输入图片验证码</span> ');
		return false;	
	}
	if(cellphone_verify_code.match(/^[\w]{6}$/)==null){
	    $("#verifystate").html('<span style="color:red;">请输入六位数的短信验证码</span> ');
		return false;	
	}
	var data="cellphone="+cellphone+"&cellphone_rancode="+cellphone_rancode+"&cellphone_verify_code="+cellphone_verify_code;
    if(!postfile){
	    postfile='member_center.php';
	}
	$.post("/"+postfile+"?m=bind_cellphone",data,function(data){
		try{
		var json=$.evalJSON(data);
		if(json.code>0){
			$("#verifystate").html('<span class="ico ok">验证成功！</span>  ');
			if(postfile!='member_center.php'){
				window.location='/company/company_manager.php';
			}else{
				window.location='/member_center.php';
			}
			return;
		}else{
			$("#verifystate").html('<span style="color:red;">'+json.statewords+'</span> ');
			return false;
		}
		}catch(e){
			alert(e.message);
			//alert('请登录后使用收藏功能');
			//window.location='/member.php?url='+document.URL;
		}	
	})
	return false;
	
}
//手机验证码发送到旧手机
function sent_old_cellphone_message(postfile){
	var cellphone_rancode=$("#old_cellphone_rancode").val();
	$("#old_clickgetbtn01").attr("disabled",true);
	var data="cellphone_rancode="+cellphone_rancode;
    if(!postfile){
	    postfile='member_center.php';
	}
	$.post("/"+postfile+"?m=sent_old_cellphone_message",data,function(data){
		try{
		var json=$.evalJSON(data);
		if(json.code>0){
			$("#old_cellstate").html('<span class="ico ok"> 发送成功</span> ');
			try{
			var n=60;
			var trysendtime=setInterval(function(){
				n--;	
				$("#old_trysendtimebox").html(n+" 秒后可再次发送");
				if(n==0){
				    $("#old_clickgetbtn01").attr("disabled",false);
					$("#old_trysendtimebox").html('');
					clearInterval(trysendtime);	
				}
			},1000);
			}catch(e){alert(e.message);return false;}
			return;
		}else{
			$("#old_clickgetbtn01").attr("disabled",false);
			$("#old_cellstate").html('<span style="color:red;">'+json.statewords+'</span> ');
			return false;
		}
		}catch(e){
			alert(e.message);
			//alert('请登录后使用收藏功能');
			//window.location='/member.php?url='+document.URL;
		}	
	})
	return false;
}
//检查手机验证码是否正确以及绑定手机
function verify_old_cellphone(postfile){
	try{
	var cellphone_rancode=$("#old_cellphone_rancode").val();
	var cellphone_verify_code=$("#old_cellphone_verify_code").val();
	if(cellphone_rancode.match(/^[\w]{4}$/)==null){
	    $("#old_verifystate").html('<span style="color:red;">请输入图片验证码</span> ');
		return false;	
	}
	if(cellphone_verify_code.match(/^[\w]{6}$/)==null){
	    $("#old_verifystate").html('<span style="color:red;">请输入六位数的短信验证码</span> ');
		return false;	
	}
	var data="cellphone_rancode="+cellphone_rancode+"&cellphone_verify_code="+cellphone_verify_code;
    if(!postfile){
	    postfile='member_center.php';
	}
	$.post("/"+postfile+"?m=verify_old_cellphone",data,function(data){
		try{
		var json=$.evalJSON(data);
		if(json.code>0){
			$("#old_verifystate").html('<span class="ok_iconv"> 验证成功！</span> ');
			setTimeout('C.alert.opacty_close("#old_verifybox");',1000);
			//window.location=window.location.href;
			return;
		}else{
			$("#old_verifystate").html('<span style="color:red;">'+json.statewords+'</span> ');
			return false;
		}
		}catch(e){
			alert(e.message);
			//alert('请登录后使用收藏功能');
			//window.location='/member.php?url='+document.URL;
		}	
	})
	}catch(e){alert(e.message);return false;}
	return false;	
}

//余额支付
function balance_pay(order_id){
	if(!confirm('确定支付吗？')){
		return false;	
	}	
	var data="order_id="+order_id;
    $.post("/member_center.php?m=balance_pay",data,function(data){
		try{
		var json=$.evalJSON(data);
		if(json.code>0){
			alert('恭喜你，支付成功！');
			window.location='/member_center.php?m=show_user_order';
		}else{
			alert(json.statewords);	
		}
		}catch(e){
			//alert(e.message);
			alert('请重新登录');
			window.location='/member_center.php';
		}	
	})	
}
//货到付款
function set_cash_pay(order_id){
	var data="order_id="+order_id;
	$.post("/member_center.php?m=cash_pay",data,function(data){
		try{
		var json=$.evalJSON(data);
		if(json.code>0){
			
			window.location='/member_center.php?m=show_user_order';
		}else{

			alert(json.statewords);	
		}
		}catch(e){
			//alert(e.message);
			alert('请重新登录');
			window.location='/member_center.php';
		}	
	})	
    	
}
//余额+优惠券 支付
function balance_coupons_pay(order_id){
	if(!confirm('确定支付吗？')){
		return false;	
	}	
	var data="order_id="+order_id+"&coupons_id="+$("#coupons_id").val();
    $.post("/member_center.php?m=balance_coupons_pay",data,function(data){
		try{
		var json=$.evalJSON(data);
		if(json.code>0){
			alert('恭喜你，支付成功！');
			window.location='/member_center.php?m=show_user_order';
		}else{
			alert(json.statewords);	
		}
		}catch(e){
			//alert(e.message);
			alert('请重新登录');
			window.location='/member_center.php';
		}	
	})	
}
//非会员货到付款
function set_session_cash_pay(order_id){
	var data="order_id="+order_id;
	$.post("/session_order.php?m=cash_pay",data,function(data){
		try{
		var json=$.evalJSON(data);
		if(json.code>0){
			art.dialog({content:'成功提交订单！',time:2,cancel:false,icon: 'succeed'});			
			 setInterval(function(){window.location='/session_order.php?m=show_user_order';},2000);
		}else{
			alert(json.statewords);	
		}
		}catch(e){
			alert(e.message);
			//alert('请重新登录');
			//window.location='/member_center.php';
		}	
	})	
    	
}




//添加充值
function ck_add_pay(){
    try{
	    var money=$("#pay_money").val();
		if(money.match(/^\d+$/)==null){
		    alert('只能大于0的填写整数');
			return false;	
		}
	}catch(e){alert(e.message);return false;}
}

//fei写的幻灯，参考 izfei.com
function slidefei(json){
    try{
    var mjson={"eobj":"","width":"400","height":"400","swidth":"50","sheight":"50","showtext":0,"auto_play":1};
	for(var r in json){
		mjson[r]=json[r];
	}
	//样式
	$(mjson.eobj).css({"width":mjson.width+"px","height":mjson.height+"px","position":"relative","overflow":"hidden","clear":"both","background":""});
    $(mjson.eobj+" .slideshowmainbox").css({"width":mjson.width+"px","height":(mjson.height-mjson.sheight-5)+"px","position":"relative","overflow":"hidden"});
	
    $(mjson.eobj+"  .slidebtnbox a img").css({"width":mjson.swidth+"px","height":mjson.sheight+"px"});
	$(mjson.eobj+"  .slidebtnbox").css({"position":"relative","clear":"both","width":mjson.width+"px"});
	$(mjson.eobj+"  .coverbox").css({"width":mjson.swidth+"px","height":mjson.sheight+"px","position":"absolute","border":"2px solid #F5740C","display":"block","z-index":"2"})
	$(mjson.eobj+"  .slidebtnbox a").css({"border":"1px solid #ccc","display":"block","float":"left","margin-right":"2px"})
	//$(mjson.eobj+" .coverbox").css({"left":"0px"});
	//return false;
    $(mjson.eobj+"  .slidebtnbox a").hover(function(){
		var imgurl=$(this).find("img").attr("src");
		var imgtitle=$(this).find("img").attr("alt");
		var infourl=$(this).attr("href");
		$(mjson.eobj+" .slideshowmainbox").stop().animate({"opacity":"0"},200,function(){
			$(mjson.eobj+" .slideshowmainbox").html('<a href="'+infourl+'" target="_blank"><img src="'+imgurl+'"  /></a><div class="txtboxdesc">'+imgtitle+'</div>');
			$(mjson.eobj+" .slideshowmainbox img").css({"position":"absolute","width":"100%","height":"100%"});
			if(mjson.showtext==1){
				$(mjson.eobj+" .slideshowmainbox .txtboxdesc").css({"position":"absolute","opacity":"0.5","background":"#000","color":"#fff","width":"100%","line-height":"26px","height":"30px","text-indent":"10px","top":mjson.height-mjson.sheight-30});
			}
			$(this).stop().animate({"opacity":"1"},200);
		});


	    var top=$(this).position().top;
		var left=$(this).position().left;//alert(left);
		//$(mjson.eobj+" .coverbox").css({"left":left});
		$(mjson.eobj+" .coverbox").stop().animate({"left":left,"top":top},500);
        $(mjson.eobj+" .slidebtnbox a").stop().animate({"opacity":"0.6"},500);
		$(this).stop().animate({"opacity":"1"},500);

	},function(){});
	var eobjlength=$(mjson.eobj+"  .slidebtnbox a").length;
	if(eobjlength==0) return;
    var i=0;
	$(mjson.eobj+"  .slidebtnbox a").eq(0).mouseover();
	if(mjson.auto_play==1){
		setInterval(function(){
		   i++;
		   if(i>=eobjlength){
			   i=0;
		   }
		   $(mjson.eobj+"  .slidebtnbox a").eq(i).mouseover(); 
		  
		},mjson.speed);
	}
	}catch(e){alert(e.message);}
	

}


function use_coupons(){
	try{
	var order_money=parseInt($("input[name=order_money]").val());  //订单的钱
	var user_money=$("input[name=user_money]").val();   //账户余额
		
    if($("#coupons_id").val()==''){
		$("#needmoney03").html(order_money-user_money);
		$("#paybox2333").show();
		$("#paybtn0033").hide();
	    return false;	
	}	
	
	var coupons_money=$("#coupons_id option:selected").attr("money"); //优惠券的钱
	var newtotal=order_money-user_money-coupons_money;
	newtotal=newtotal.toString().replace(/(\.\d{2})(.*)/,'$1');
	$("#needmoney03").html(newtotal);
	if(newtotal<=0){
		//alert(newtotal);
	    $("#paybox2333").hide();
		$("#paybtn0033").show();
	}else{
		$("#paybox2333").show();
		$("#paybtn0033").hide();
	}
	
	}catch(e){alert(e.message);}
}




 //缩放图片
function resizeImage(obj, MaxW, MaxH){
	var imageObject = obj;
    var oldImage = new Image();
    oldImage.src = imageObject.src;
    var dW = oldImage.width; 
    var dH = oldImage.height;
    if(dW>MaxW || dH>MaxH) 
   {
        a = dW/MaxW; b = dH/MaxH;
        if(b>a)
		 { 
		 a=b;
		 }
        dW = dW/a; 
		dH = dH/a;
    }
    if(dW > 0 && dH > 0) 
	{
			imageObject.width = dW;
		   imageObject.height = dH;
	}
}

//全选反选勾选框
//name 为 $('') 选择符号
function check_all(name){
	$(name).each(function(){
		if($(this).attr('checked')=='checked'){$(this).attr({'checked':false});}else{$(this).attr({'checked':true});}
	});
}
//获取一组checkbox选中值，返回选中值的数组
function get_group_checked(name){
	var list=new Array();
	$(name).each(function(){
		if($(this).attr('checked')=='checked'){
			list.push($(this).val());
		}
	});
	return list;
}



function changeTwoDecimal_f(x){
	var f_x = parseFloat(x);
	if (isNaN(f_x)){
		//alert('function:changeTwoDecimal->parameter error');
		return false;
	}
	var f_x = Math.round(x*100)/100;
	var s_x = f_x.toString();
	var pos_decimal = s_x.indexOf('.');
	if (pos_decimal < 0){
		pos_decimal = s_x.length;
		s_x += '.';
	}
	while (s_x.length <= pos_decimal + 2){
		s_x += '0';
	}
	return s_x;
}

//控制商品选购，如颜色，规格选择时候的系列动作
var change_attr_price={
	"init":function(){
		//默认选中第一个属性
		//原始价钱
		$(".goods_attr_type_list").each(function(){
			var now_price=parseFloat($(".goods_now_price_num").html());
			$(this).find(".attr_single_li a").eq(0).addClass("current");
			var datavalue_str=decodeURIComponent($(this).find(".attr_single_li").eq(0).attr("data-value"));
			//alert(datavalue_str);
			var datavalue=eval("("+datavalue_str+")");
			$(this).find(".attr_value").val(datavalue.id);
		 	$(this).find(".attr_value").attr("addmoney",datavalue.attr_price);
			$(this).find(".attr_value").attr("attr_name",datavalue.attr_name);
			$(".goods_now_price_num").html(changeTwoDecimal_f(now_price+parseFloat(datavalue.attr_price)));
		})
		$(".attr_single_li").click(function(){
			var now_price=parseFloat($(".goods_now_price_num").html());
			$(this).parent().find(".attr_single_li a").removeClass("current");
			$(this).find("a").eq(0).addClass("current");
			var datavalue_str=decodeURIComponent($(this).attr("data-value"));
			//alert(datavalue_str);
			var datavalue=eval("("+datavalue_str+")");
			$(this).parent().find(".attr_value").val(datavalue.id);
			var a=parseFloat($(this).parent().find(".attr_value").attr("addmoney"));
			$(this).parent().find(".attr_value").attr("attr_name",datavalue.attr_name);
			$(".goods_now_price_num").html(changeTwoDecimal_f(now_price+parseFloat(datavalue.attr_price)-a));
			$(this).parent().find(".attr_value").attr("addmoney",parseFloat(datavalue.attr_price));
		})
		
			
	}
}




function lxfEndtime(){
	$(".lxftime").each(function(){
		  var lxfday=$(this).attr("lxfday");//用来判断是否显示天数的变量
		  var endtime = new Date($(this).attr("endtime")).getTime();//取结束日期(毫秒值)
		  var nowtime = new Date().getTime();        //今天的日期(毫秒值)
		  var youtime = endtime-nowtime;//还有多久(毫秒值)
		  var seconds = youtime/1000;
		  var minutes = Math.floor(seconds/60);
		  var hours = Math.floor(minutes/60);
		  var days = Math.floor(hours/24);
		  var CDay= days ;
		  var CHour= hours % 24;
		  var CMinute= minutes % 60;
		  var CSecond= Math.floor(seconds%60);//"%"是取余运算，可以理解为60进一后取余数，然后只要余数。
		  if(endtime<=nowtime){
		  	  $(this).html("已过期")//如果结束日期小于当前日期就提示过期啦
		  }else{
			  if($(this).attr("lxfday")=="no"){
			      $(this).html("<i>剩余时间：</i><span>"+CHour+"</span>时<span>"+CMinute+"</span>分<span>"+CSecond+"</span>秒");          //输出没有天数的数据
			  }else{
	  $(this).html("<i>剩余时间：</i><span>"+days+"</span><em>天</em><span>"+CHour+"</span><em>时</em><span>"+CMinute+"</span><em>分</em><span>"+CSecond+"</span><em>秒</em>");          //输出有天数的数据
			  }
		  }
	});
   setTimeout("lxfEndtime()",1000);
};

