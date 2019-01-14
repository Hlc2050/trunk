//全局类：安全验证
var S={
	"casu":function(){
		$.get("auth.php?m=c", function(data){
			try{
				var ret=$.evalJSON(data);
				if(ret.code=='100') window.location.reload();
			}catch(e){E.logs(e.message);}
		});
	}
}

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

//实例类：切换卡
tab=function(params){
	var __params={
		"tab_c":"#navbtnbox",	// TAB页的导航层
		"tab":"a", //TAB的导航区分标记，用于JQ中find查找
		"tab_selected":"",//选中的选项卡，导航层和内容层共有标识字符
		"tab_selected_class_suffix":"__selected", // 选中按钮的CSS样式后缀
		"page_c":"#content", // TAB页的内容层
		"page":"div", //TAB页的内容区分标记，用于JQ中find查找
		"url":"" //如果内容层有 iframe 则指定URL
	};
	//初始化
	if(arguments[0]){
		__set(params);
	}
	//设置参数
	function __set(params){
		for(var param in params){
			__params[param]=params[param];
		}
	}
	//获取参数
	this.params=function(){
		return $.toJSON(__params);
	}
	//单击动作
	this.click=function(params){
		__set(params);
		try{
			//操作导航层
			$(__params.tab_c).find(__params.tab).each(function(){
				var nav_id=$(this).attr('id');
				//改变样式：传入序号相同则改变该序号为选中样式，否则移除选中样式
				if(nav_id.indexOf(__params.tab_selected)>=0){
					$(this).addClass(nav_id+__params.tab_selected_class_suffix);
				}else{
					$(this).removeClass(nav_id+__params.tab_selected_class_suffix);
				}
			});
			//操作内容层
			$(__params.page_c).find(__params.page).each(function(){
				var con_id=$(this).attr('id');
				var ifr=null;
				var ifr_id=null;
				if(con_id==null && __params.url!=''){
					ifr=$(this).find('iframe');
					ifr_id=ifr.attr('id');
				}
				//显示内容层
				if((con_id!=null && con_id.indexOf(__params.tab_selected)>=0) || (ifr_id!=null && ifr_id.indexOf(__params.tab_selected)>=0)){
					$(this).css({'display':'block'});
				}else{
					$(this).css({'display':'none'});
				}
				//如果有iframe，则同时载入指定页面
				if(__params.url!=''){
					if(ifr_id!=null && ifr_id.indexOf(__params.tab_selected)>=0){
						if(ifr.attr('src')=='') ifr.attr({'src':__params.url});
					}
				}
			});
		}catch(e){E.logs(e.message);}
	}
}

//设置Cookie
function setCookie(name,value){var exp = new Date();exp.setTime(exp.getTime() + 60*60*1000);document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();}

//读取Cookie
function getCookie(name){var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");if(arr=document.cookie.match(reg)){return unescape(arr[2]);}else{return null;}}

//Input的文本框聚焦到输入文字末尾
$.fn.setCursorPosition = function(position){if(this.lengh == 0) return this;return $(this).setSelection(position, position);}
$.fn.setSelection = function(selectionStart, selectionEnd) {if(this.lengh == 0) return this;input = this[0];
if (input.createTextRange) {var range = input.createTextRange();range.collapse(true);range.moveEnd('character', selectionEnd);range.moveStart('character', selectionStart);range.select();} else if (input.setSelectionRange) {input.focus();input.setSelectionRange(selectionStart, selectionEnd);}return this;}
$.fn.focusEnd = function(){this.setCursorPosition(this.val().length);}

//加入收藏夹
function addfav(tit,url){try{window.external.addFavorite(url, tit);}catch(e){try{window.sidebar.addPanel(tit,url,'');}catch(e){alert('您可以尝试通过快捷键' + ctrl + ' + D 加入到收藏夹~');}}}

//扩展jQuery对json字符串的转换
//JSNO字符串和对象自转换 evalJSON ,toJSON
jQuery.extend({
   /** * @see 将json字符串转换为对象 * @param json字符串 * @return 返回object,array,string等对象 */
   evalJSON: function(strJson) {
     return eval("(" + strJson + ")");
   }
});
jQuery.extend({
   /** * @see 将javascript数据类型转换为json字符串 * @param 待转换对象,支持object,array,string,function,number,boolean,regexp * @return 返回json字符串 */
   toJSON: function(object) {
     var type = typeof object;
     if ('object' == type) {
       if (Array == object.constructor) type = 'array';
       else if (RegExp == object.constructor) type = 'regexp';
       else type = 'object';
     }
     switch (type) {
     case 'undefined':
     case 'unknown':
       return;
       break;
     case 'function':
     case 'boolean':
     case 'regexp':
       return object.toString();
       break;
     case 'number':
       return isFinite(object) ? object.toString() : 'null';
       break;
     case 'string':
       return '"' + object.replace(/(\\|\")/g, "\\$1").replace(/\n|\r|\t/g, function() {
         var a = arguments[0];
         return (a == '\n') ? '\\n': (a == '\r') ? '\\r': (a == '\t') ? '\\t': ""
       }) + '"';
       break;
     case 'object':
       if (object === null) return 'null';
       var results = [];
       for (var property in object) {
         var value = jQuery.toJSON(object[property]);
         if (value !== undefined) results.push(jQuery.toJSON(property) + ':' + value);
       }
       return '{' + results.join(',') + '}';
       break;
     case 'array':
       var results = [];
       for (var i = 0; i < object.length; i++) {
         var value = jQuery.toJSON(object[i]);
         if (value !== undefined) results.push(value);
       }
       return '[' + results.join(',') + ']';
       break;
     }
   }
});

//选项卡切换
function tab_div(eobj,parent1,cname,my_element,t_element) {
try{
    my_element=my_element?my_element:'a';
    t_element=t_element?t_element:'div';
    var liobj=eobj.parentNode.getElementsByTagName(my_element);
	var k=null;
    for(var j=0;j<liobj.length;j++){
	 liobj[j].className='';
	 if(eobj==liobj[j]){
	     k=j;
	 }
    }
    liobj[k].className=cname

    var obja=document.getElementById(parent1).getElementsByTagName(t_element)
    for(var j=0;j<obja.length;j++){
        obja[j].style.display='none'
    }
    obja[k].style.display='';
}catch(e){alert(e.message);}
}

//显示半透明层
function show_opacity_div(div_tag,width,height){
	var w=$(document).width();
	var h=$(window).height();
	var h_doc=$(document).height();
	var h_scroll = $(document).scrollTop();
	$('.opacty').remove();
	$('.close_opacity_div').remove();
	$(div_tag).prepend('<div class="close_opacity_div"><span class="l">温馨提示</span><span class="r close"  onclick="close_opacity_div(\''+div_tag+'\');"></span></div>');
	$(div_tag).css({'display':'block','left':((w-width)/2-8)+'px','top':((h-height)/2+h_scroll-8)+'px','width':width+'px','height':height+'px','position':'absolute','z-index':'100','background':'#fff','border':'5px solid #888'});
	var self=$(div_tag).find('.content');var cmtop=(height-$('.close_opacity_div').height()-self.height()-20)/2;if(cmtop<0) cmtop=0;
	self.css({'text-align':'center','margin-top':cmtop+'px'});
	$(div_tag).focus();
	$("<div class=\"opacity\"></div>").insertBefore(div_tag);
	$('.opacity').css({'display':'block','position':'absolute','width':'100%','height':h_doc+'px','background':'#000','top':'0px','left':'0px','filter':'alpha(opacity=20)','opacity':'.2',"z-index":"1000"});

}
//关闭半透明层
function close_opacity_div(div_tag){
	$('.opacity').remove();
	$('.close_opacity_div').remove();
	$(div_tag).css({'display':'none'});
}

//将时间戳转为时间格式
function getLocalTime(nS) {
    var dstr=new Date(parseInt(nS) * 1000).toLocaleString().replace(/:\d{1,2}$/,' ');
	dstr=dstr.replace('年','-').replace('月','-').replace('日','');
	return dstr;
}

//全选反选勾选框
//name 为 $('') 选择符号
function check_all(name){
	$(name).each(function(){
		if($(this).attr('checked')=='checked'){$(this).attr({'checked':false});}else{$(this).attr({'checked':true});}
	});
}

//全选
//name 为 $('') 选择符号
function all_check(name){
    // var str = "";
    // $("input[class='cklist']:checked").each(function () {
    //     str += $(this).val() + ",";
    // });
    // str = str.substring(0, str.length - 1);
    // num = str.split(",").length;
    //     if(num == $(name).length){$(name).attr("checked",false);}else{
    //         $(name).attr("checked",true);
    //     }
    $(name).attr("checked",true);
}

//全不选
function all_uncheck(name){
        $(name).attr("checked",false);
}


//选中某行的checkbox
function select_row(a,name){
	var row=$(a);
	var chk_box=row.find(name);
	if(chk_box.attr('checked')=='checked'){
		chk_box.attr({'checked':false});
	}else{
		chk_box.attr({'checked':true});
	}
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

//后台搜索提交函数，参数url 搜索提交的url（包括模块函数）
function search_submit(url){
	var search_type=$('#search_type').val();
	var search_txt=$('#search_txt').val();
	if(search_txt==''){
		$('#show_tips>.content').html('查询内容不能为空！');
		show_opacity_div('#show_tips','300','150');
		setTimeout(function(){close_opacity_div('#show_tips');},1000);
		$('#search_txt').focus();
		return false;
	}
	var url = url+'&search_type='+search_type+'&search_txt='+encodeURI(search_txt);
	window.location.href=url;
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
function callback_upload2(ret){
	callback_upload(ret);
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

//通用 提交 选中 的 checkbox 到指定页面
//url 中  用 [@]  代替  需要传入的 id 的集合，words 是 提示的文字，默认是 "确定吗"，如果传入的是none，的话，为不提示
function set_some(url,words){
    var idarr=get_group_checked('.cklist');
	words=words?words:'确定吗';
	if(idarr.length==0){alert('请选中至少一个');return false;}
	if(words.match(/none/)==null){
		if(!confirm(words)){
			return false;
		}
	}
	var idarrs=idarr.join(',');
	var url=url.replace('[@]',idarrs);
	// alert(url);
	window.location=url;
}
function set_url(element,width,height,isrefresh,url,words){
	var idarr=get_group_checked('.cklist');
	words=words?words:'确定吗';
	if(idarr.length==0){alert('请选中至少一个');return false;}
	if(words.match(/none/)==null){
		if(!confirm(words)){
			return false;
		}
	}
	var idarrs=idarr.join(',');
	var url=url.replace('[@]',idarrs);
	try{
		if(!width||!height){
			width='90%';
			height='90%';
		}
		art.dialog.open(url,{
			title:$(element).html(),
			width:width,
			height:height,
			lock:true,
			close:function(){
				if(isrefresh==1) {
					window.location.reload(true);
				}
			}
		});
	}catch(e){alert(e.message);}
	return false;
}
//url 中  用 [@]  代替  需要传入的 id 的集合，words 是 提示的文字，默认是 "确定吗"，如果传入的是none，的话，为不提示
function set_addspecial(){
    var idarr=get_group_checked('.cklist');

	if(idarr.length==0){alert('请选中至少一个');return false;}

	var idarrs=idarr.join(',');
	show_opacity_div('#html_addspecial','520','250');
        $("#hid_special_id").val(idarrs);
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
		if(json.files[0].original.match(/(\.gif)|(\.png)|(\.jpg)|(\.bmp)/i)){
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


//编辑器的 上传初始化
function  load_editor_upload(textarea_id){
    create_upload_iframe('{"func":"editor_callback_upload22","textarea_id":"'+textarea_id+'","inner_box":"#upbtn_box","btn_skin":"editor","thumb":{"width":"300","height":"300"},"water":1}');
}

//编辑器下载远程图片
function download_http_img(editorid){
	var editor=eval(editorid);
	var code=editor.getSource();
	var data="code="+encodeURIComponent(code);
	if($("#downbtn_"+editorid).html().match(/\.\./)){
		return false;
	}
	$("#downbtn_"+editorid).append("..");
	$.post('/index.php/admin/post/saveHttpImg',data,function(jsonstr){
		var json=eval("("+jsonstr+")");
		if(json.statecode<=0){
			alert(json.msgwords);
		}else{
			$("#"+editorid).val(json.code);
		}
		$("#downbtn_"+editorid).html($("#downbtn_"+editorid).html().replace('..',''));
	})

}

//提取标签
function get_tags(kw,innerElement){
	if(kw.length<4) return false;
	$.post('post.php?m=get_tags&kw='+encodeURIComponent(kw),function(jsonstr){
		try{
		var json=eval("("+jsonstr+")");
		if(json.statecode<=0){
			alert(json.msgwords);
		}else{
			var tags=json.list.join(',');
			$(innerElement).val(tags);
		}
		}catch(e){alert(e.message+'jsonstr');return false;}
	})
}

function dialog_frame(element,width,height,isrefresh){
	try{
	if(!width||!height){
		width='90%';
		height='90%';
	}
	art.dialog.open($(element).attr("href"),{
		title:$(element).html(),
		width:width,
		height:height,
		lock:true,
		close:function(){
			if(isrefresh==1) {
				window.location.reload(true);
			}
		}
	});
	}catch(e){alert(e.message);}
	return false;
}
/*封面快速裁剪
 * @params table string  表名
 * @params info_id int  表里的id
 * @params idField string  id的字段名
 * @params img   图片的地址                   
 * @params imgField string  图片的字段名
*/
function info_cover_crop(table,id,idField,img,imgField){
	var jcropapi;
	var postdata={x:0,y:0,x2:0,y2:0,img:"",id:id,table:table,idField:idField,imgField:imgField};
	img=img.replace("thumb_",'');
	postdata.img=img;
	art.dialog({"title":"裁剪图片","content":"<img src="+img+" id=\"cutimage\"><div class='mt10 size-box'>宽：<input size=5  class='crop-size-w' >px ，高：<input size=5  class='crop-size-h' >px</div>","lock":true,
		button:[
		        {
			        "name":"裁剪并保存",
			        "focus":true,
			        "callback":function(){
				        if(postdata.x2-postdata.x<10){
					    	alert('请选择图片区域');
					    	return false;
					    }
					    $.post("/admin/post/cutImage",postdata,function(jsonstr){
							//alert(jsonstr);
							try{
						    var json=eval("("+jsonstr+")");
						    if(json.state>0){
								art.dialog({"icon":"succeed",content:"裁剪成功！",lock:true});
								window.location.reload();
								window.location=window.location.href;
						    }else{
							    alert(json.msgwords);
							}
							}catch(e){alert(e.message+jsonstr);}
						})
						return false;
					 }
			    }
		]
	});

	$('#cutimage').load(function(){
		jcropapi=$.Jcrop("#cutimage",{
			onChange:showCoords
			//onSelect:showCoords //当选中区域的时候，执行对应的回调函数
		});
	});


	function showCoords(c) {  //参数有 x,y,x2,y2,w,h
		//alert(c.x);//
		$(".crop-size-w").val((c.x2-c.x));
		$(".crop-size-h").val((c.y2-c.y));
		postdata.x=c.x;//得到选中区域左上角横坐标
		postdata.y=c.y;// 得到选中区域左上角纵坐标
		postdata.x2=c.x2;//得到选中区域右下角横坐标
		postdata.y2=c.y2;//得到选中区域右下角纵坐标
	}
	//创建选框
	$(".crop-size-w,.crop-size-h").change(function(){
		var x2=parseInt($('.crop-size-w').val());
		var y2=parseInt($('.crop-size-h').val());
		jcropapi.setSelect([0,0,x2,y2]);

	});
}

//表单提交不为空判断
function checkForm() {
    for(var i=0;i<document.form.elements.length-1;i++)
    {
        if(document.form.elements[i].value=="")
        {
        	console.log(document.form.elements[i]);
            alert("请填写完整数据");
            document.form.elements[i].focus();
            event.preventDefault();
            return false;
        }
    }

}