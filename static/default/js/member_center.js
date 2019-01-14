/*添加直播间*/
function update_room(room_id){
    if(room_id>0){
        $.post("?m=show_chat_room&id="+room_id, function(result) {
			try {
				var ret = eval("("+result+")");
				if(ret.list.length==0){
				    alert('直播间不存在！');
					return false;	
				}
				$("#room_id").val(ret.list[0].room_id);
				$("#room_name").val(ret.list[0].room_name);
				$("#room_desc").val(ret.list[0].room_desc);
				$("#cate_id").val(ret.list[0].cate_id);
				if(ret.list[0].room_img){
				    $("#room_img_span").html('<img src="'+ret.list[0].room_img+'" />');	
				}
				$("#room_img").val(ret.list[0].room_img);
				if(ret.list[0].room_bg_img){
				    $("#room_bg_img_span").html('<img src="'+ret.list[0].room_bg_img+'" />');	
				}
				$("#room_bg_img").val(ret.list[0].room_bg_img);
				C.alert.opacty({
					"width": "600",
					"height": "390",
					"title": "编辑直播间",
					"div_tag": "#update_room_box"
				});
			} catch (e) {E.logs(result);
				alert(result + ":e"+e.message);
			}
   		});
    }else{
		$("#resetbtn").click();
        C.alert.opacty({
        "width": "600",
        "height": "390",
        "title": "添加直播间",
        "div_tag": "#update_room_box"
        });
        return false;
    }
}

/*添加图片*/
function update_photo(photo_id){
    if(photo_id>0){
        $.post("?m=show_photo&id="+photo_id, function(result) {
			try {
				var ret = eval("("+result+")");
				if(ret.list.length==0){
				    alert('信息不存在！');
					return false;	
				}
				$("#photo_id").val(ret.list[0].photo_id);
				if(ret.list[0].photo_img){
				    $("#photo_img_span").html('<img src="'+ret.list[0].photo_img+'" />');	
				}
				$("#photo_desc").val(ret.list[0].photo_desc);
				$("#photo_img").val(ret.list[0].photo_img);
				C.alert.opacty({
					"width": "600",
					"height": "280",
					"title": "修改图片",
					"div_tag": "#update_photo_box"
				});
			} catch (e) {E.logs(result);
				alert(result + ":e"+e.message);
			}
   		});
    }else{
		$("#resetbtn").click();
        C.alert.opacty({
        "width": "600",
        "height": "280",
        "title": "添加图片",
        "div_tag": "#update_photo_box"
        });
        return false;
    }
}

function photo_save(){
    var postdata=C.form.get_form("#update_photo_box");
	//alert($.toJSON(postdata));	
	$.post("?m=save_update_photo",postdata,function(restr){
	    try{
			var ret=eval("("+restr+")");
			if(ret.code<1){
			    alert(ret.statewords);
				return false;	
			}else{
			    window.location=window.location.href;	
			}
		}catch(e){alert(e.message+restr);}	
	})
}

function photo_del(){
	try{
	var iarr=get_group_checked('.cklist');
	if(iarr.length<1){
	    alert('至少选中一个');
		return false;	
	}	
	var postdata="ids="+iarr.join(',');
	if(!confirm('确定吗？')) return;
	$.post("?m=del_photo",postdata,function(restr){//alert(restr);
	    try{
			var ret=eval("("+restr+")");
			if(ret.code<1){
			    alert(ret.statewords);
				return false;	
			}else{
			    window.location=window.location.href;	
			}
		}catch(e){alert(e.message+restr);}	
	})
	}catch(e){alert(e.message);}
}

function set_photo_order(){
	try{
	var a=$(".listorders");
	var arr=[];
	for(var i=0;i<a.length;i++){
		var c=a.eq(i).attr("name");
		var d=a.eq(i).val();	
		arr.push('"'+c+'":"'+d+'"');
	}
	var postdata=eval('({'+arr.join(',')+'})');
	//alert('({'+arr.join(',')+'})');return;
	$.post("?m=set_photo_order",postdata,function(restr){//alert(restr);
	    try{
			var ret=eval("("+restr+")");
			if(ret.code<1){
			    alert(ret.statewords);
				return false;	
			}else{
			    window.location=window.location.href;	
			}
		}catch(e){alert(e.message+restr);}	
	})
	}catch(e){alert(e.message);}
}


function room_save(){
    var postdata=C.form.get_form("#update_room_box");
	//alert($.toJSON(postdata));	
	$.post("?m=save_update_room",postdata,function(restr){
	    try{
			var ret=eval("("+restr+")");
			if(ret.code<1){
			    alert(ret.statewords);
				return false;	
			}else{
			    window.location=window.location.href;	
			}
		}catch(e){alert(e.message+restr);}	
	})
}

function save_exchange_vitual_money(){
	var point=$("#usepoint").val();
	if(point.match(/^\d+$/)==null){
	    $("#point_exchange_state").html('<span class="ico ng">必须是整数</span>');
		return false;	
	}
	if(!parseInt(point)){
		$("#point_exchange_state").html('<span class="ico ng">必须大于0</span>');
		return false;	
	}
	if(parseInt(point)>parseInt($('#pointtotal').val())){
		$("#point_exchange_state").html('<span class="ico ng">不能大于当前的积分</span>');
		return false;	
	}
	$("#sub_btn_point_exchange").attr("disabled",true);
    var postdata="usepoint="+point;
	//alert($.toJSON(postdata));	
	$.post("?m=save_exchange_vitual_money",postdata,function(restr){
	    try{
			
			var ret=eval("("+restr+")");
			if(ret.code<1){
				$("#point_exchange_state").html('<span class="ico ng">'+ret.statewords+'</span>');
				$("#sub_btn_point_exchange").attr("disabled",false);
				return false;	
			}else{
				$("#point_exchange_state").html('<span class="ico ok">操作成功</span>');
				setTimeout('window.location=window.location.href;',1000);	
			}
		}catch(e){alert(e.message+restr);}	
	})	
	
}