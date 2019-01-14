//全局类：常用方法
var C={
	/*
      半透明提示层
      支持：1）直接创建提示；2）页面内隐藏层变半透明显示
      width 提示层宽度
      height 提示层高度
      title 提示层标题内容
      content 提示层 提示内容
      timeout 友情提示 （错误提示） 显示时间， top 友情提示 上边距
      alert_style 设置 错误消息提示样式
      border 设置弹窗层边框粗细
      border_radius 设置边框圆角幅度
    */
    "alert":{
        "params":{
          "width":"300","height":"100","title":"温馨提示","content":"","timeout":"1000","top":"9","alert_style":{'border':'1px solid #ddd','color':'red'},'border':'8','border_radius':'5',
            //如果是取某个隐藏层内容显示到这里，则传入此标签
            "div_tag":""
        },
        "init":function(){
            C.alert.params={
              "width": "300", "height": "100", "title": "温馨提示", "content": "", "timeout": "1000", "top": "9", "alert_style": { 'border': '1px solid #ddd', 'color': 'red' }, 'border': '8', 'border_radius': '5',
            //如果是取某个隐藏层内容显示到这里，则传入此标签
            "div_tag":""};
        },
        //设置参数
        "set_params":function(params){
            for(var param in params){
                C.alert.params[param]=params[param];
            }
        },
        /*
            提示层，content 提示内容，支持HTML；top 顶边距 ;timeout 延迟关闭时间;
            必填：C.alert.tips({'content':'删除成功'});
            选填：C.alert.tips({'content':'删除成功','width':100,'top':100,'timeout':3000,'alert_style':{'border':'1px solid #000','color':'blue'}}); 参考C.alert.params 内部参数
        */
        "tips":function(params){
            if($('#tips_20130528').length>0) {return;}//存在则返回
            if(arguments[0]){C.alert.set_params(params);}
            var height=C.alert.params.height;//高度
            var content=C.alert.params.content;//内容
            var top=C.alert.params.top;//顶边距
            var timeout=C.alert.params.timeout;//延迟时间，毫秒
            var alert_style=C.alert.params.alert_style;//设置消息提示样式
            var tips_div_opacity='<div id="tips_20130528_opacity" style="width:100%;height:100%;position:absolute;background:#fff;left:0;top:0;z-index:19000;filter:Alpha(opacity=10);opacity:0.1;"></div>';
            var ntop=parseInt($(document).scrollTop())+parseInt(top);//alert(ntop);
            var tips_div='<div id="tips_20130528" style="-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;background:#fffbeb;padding:8px 40px 8px 40px;position:absolute;z-index:20000;top:'+ntop+'px;'+'">'+content+'</div>';
            $('body').append(tips_div_opacity+tips_div);//动态创建层
            parseInt(params.width) > 0 ? $('#tips_20130528').css('width',params.width+'px') : $('#tips_20130528').css('width','auto'); //设置宽度
            params.alert_style==undefined ? $('#tips_20130528').css(alert_style) : $('#tips_20130528').css(params.alert_style) ;//设置消息提示样式
            $('#tips_20130528').css({'left':(($(document).width()-$('#tips_20130528').width())/2-40)+'px'});//设置层显示左右居中
            setTimeout(function(){$('#tips_20130528_opacity').remove();$('#tips_20130528').remove();},timeout);//延时移除层
            C.alert.init();
        },
        //显示透明层提示
        "opacty":function(params){
            if(arguments[0]){
                C.alert.set_params(params);
            }
            //内部参数
            var width=C.alert.params.width;
            var height=C.alert.params.height;
            var title=C.alert.params.title;
            var content=C.alert.params.content;
            var div_tag=C.alert.params.div_tag;
            var border = C.alert.params.border;
            var border_radius = C.alert.params.border_radius;
            var border_style = params.border != undefined ? params.border : border; //判断是否传入 border
            var border_radius_style = params.border_radius != undefined ? params.border_radius : border_radius; //判断是否传入 border_radius
            //计算位置
            var w=$(document).width();
            var h=$(window).height();
            var h_doc=$(document).height();
            var h_scroll = $(document).scrollTop();
            var opacty_width = $(window).width();
          //样式
            class_opacty={'display':'block','position':'absolute','width': opacty_width +'px','height':h_doc - 4+'px','background':'#000','top':'0px','left':'0px','filter':'Alpha(opacity=20)','opacity':'0.3'};//透明层
            class_tips = { 'position': 'absolute', 'left': ((w - width) / 2 - 8) + 'px', 'top': ((h - height) / 2 + h_scroll - 8) + 'px', 'background': '#fff', 'width': width + 'px', 'height': height + 'px', border: '' + border_style + 'px solid #555' };//提示层
            class_tit = { 'cursor': 'move', 'height': '30px', 'background': '#ddd' };//标题栏
            class_tit_left={'font-weight':'bold','float':'left','margin-left':'8px','_margin-right':'3px','line-height':'30px'};//标题栏左侧
            class_tit_right={'float':'right','cursor':'pointer'};//标题栏右侧
            //透明层HTML
            var div_opacty='<div class="opacty" style="z-index:9999;"></div>';
            //提示层标题栏HTML
            var tips_tit = '<div class="tips_tit"><div class="drag-tit"><span class="tit_left">' + title + '</span></div><span class="tit_right"><input type=button value=x class="close_a" style="background:none;border:none;font-size:18px;color:#888;font-weight:600;display:inline-block;width:40px; height30px; line-height:28px; text-align:center;curson:pointer;" onclick="C.alert.opacty_close(\'' + div_tag + '\');" /></span><span style="clear:both;"></span></div>';
			if ($(".opacty").length > 0) { return false; }
			
             //1)如果传入URL
            if(params.url != undefined ){
                $('body').append(div_opacty);//插入透明层
                $('.opacty').css(class_opacty);
                var div_tips = '<div class="div_tips" style="overflow:hidden;-moz-border-radius:' + border_radius_style + 'px;-webkit-border-radius:' + border_radius_style + 'px;border-radius:' + border_radius_style + 'px;">' + tips_tit + '<iframe name="iframe_info" scrolling="no" class="iframe_info" src="' + params.url + '" ></iframe></div>'; //内容
                $('body').append(div_tips);
                var iframe_style = {'overflow':'hidden','margin':'0','padding':'0','border':'0','width':'100%','height':height - $('.tips_tit').height() - 16 + 'px'};//iframe 元素样式
                var opacty_zindex=10001;
                $('.div_tips').css({'z-index':(opacty_zindex+3),'overflow':'hidden'});
                $('.tips_tit').css(class_tit);$('.tit_left').css(class_tit_left);$('.tit_right').css(class_tit_right);
                $('.div_tips').css(class_tips);//内容层 插入样式
                $('.div_tips').css('z-index','10099'); //内容显示深度
                $('.iframe_info').css(iframe_style); //iframe 插入样式
                drag_disCenter();
                return false;
            }
            //2）如果传入的是层标签
            if(div_tag!=''){
                //层外部前面插入透明层
                $(div_opacty).insertBefore(div_tag);
                var opacty_zindex=10000;
                //层内容插入标题栏
                $(div_tag).prepend(tips_tit);
                $('.opacty').css(class_opacty);
                $('.tips_tit').css(class_tit);$('.tit_left').css(class_tit_left);$('.tit_right').css(class_tit_right);
                //重定义层位置和样式
                $(div_tag).css({ 'display': 'block', 'z-index': (opacty_zindex + 10), 'position': 'absolute', 'left': ((w - width) / 2 - 8) + 'px', 'top': ((h - height) / 2 + h_scroll - 8) + 'px', 'background': '#fff', 'width': width + 'px', 'height': height + 'px', border: '' + border_style + 'px solid #555', '-moz-border-radius': +border_radius_style + 'px', '-webkit-border-radius': +border_radius_style + 'px', 'border-radius': +border_radius_style + 'px' });
                $(".close_a").hover(function () { $(this).css({ 'color': '#e2041b', 'text-decoration': 'none' }) }, function () { $(this).css({ 'color': '#888' }) }); //设置A 连接样式
                if ($('#addrecommend_html').length > 0) { $('#addrecommend_html').css({'overflow-x':'hidden'}); } //调用 编辑器时内容溢出，去除内容溢出
                drag_disCenter(div_tag);
                return false;
            }
            //3）如果只是显示传入的内容
            //创建透明层
            if(params.content != undefined){
                $('body').append(div_opacty);
                $('.opacty').css(class_opacty);
                //创建提示层
                var div_tips = '<div class="div_tips" style="-moz-border-radius:' + border_radius_style + 'px;-webkit-border-radius:' + border_radius_style + 'px;border-radius:' + border_radius_style + 'px;">' + tips_tit + '<div class="tips_content"><table cellspacing="0" cellpadding="0" width="100%" height="60%" border="0"><tr><td align="center">' + content + '</td></tr></table><div></div>';
                $('body').append(div_tips);
                var opacty_zindex=10001;
                $('.div_tips').css(class_tips);
                $('.div_tips').css({'z-index':(opacty_zindex+3)});
                $('.tips_tit').css(class_tit);$('.tit_left').css(class_tit_left);$('.tit_right').css(class_tit_right);
                $('.tips_content').css({ 'line-height': '170%', 'text-align': 'center' });
                drag_disCenter();
            }

            //调用 拖拽及弹出层居中显示方法
            function drag_disCenter(div_tag){
              if(!arguments[0]) div_tag='.div_tips';
              C.alert.display_center(div_tag); //弹出层缩放滚动同步
              C.alert.display_opacty('.op');//透明层自增长
			  $('.drag-tit').css({'width': $('.tips_tit').width() - $('.tit_right').width() ,'height': $('.tips_tit').height(),'float':'left'}); //拖拽条样式
              C.alert.draw_alert(div_tag, '.drag-tit'); //拖拽
              $(".close_a").hover(function () { $(this).css({ 'color': '#e2041b', 'text-decoration': 'none' }) }, function () { $(this).css({ 'color': '#888' }) }); //设置A 连接样式
              $('.tb3').css({'margin':'5px 0 0 10px'});
              //实现ESC键关闭弹出层
			  $(window).on('keydown',function(event){
				 if(event.keyCode== 27 && $('.opacty').is(':visible')){C.alert.opacty_close(C.alert.params.div_tag);}
			  });
           }
        },
        /*
         拖拽
         obj 移动容器，title 标题位置为拖动位置
        */
        "draw_alert": function (Drag, Title) {
            if(!arguments[1]) Title='.drag-tit';
            var objDarg = $(Drag);
            var objTitle = $(Title);
            var posX = posY = 0;
            var _move = false; //移动状态
            var opacity;
            objTitle.mousedown(function (e) {
              _move = true;
              opacity = 0.2;
              posX = e.pageX - $(this).offset().left; //获取鼠标坐标
              posY = e.pageY - $(this).offset().top;
            });
          $(document).mousemove(function (e) {
              var e = e || window.event;
              var maxW = $(window).width() - objDarg.get(0).offsetWidth; //可移动最大宽度
              var maxH = $(window).height() - objDarg.get(0).offsetHeight - 10;
              if (_move) {
                var _x = e.pageX - posX;
                var _y = e.pageY - posY;
                if (_x < 0) _x = 0; else if (_x > maxW) _x = maxW;
                if (_y < 0) _y = 0; else if (_y > maxH) _y = maxH;
              }
              objDarg.css({ left: _x, top: _y, 'opacity': opacity });
            }).mouseup(function () {
              _move = false;
              opacity = 1;
            });
          },
        /*
         始终显示在屏幕中间 obj:始终居中的弹出层对象
        */
          "display_center": function (obj) {
            if (arguments[0]) alert_obj = '.div_tips';
            //弹出层居中
            function setMask() {
              var top = parseInt(($(window).scrollTop() + ($(window).height() - $(obj).height()) / 2)) + "px";
              var left = parseInt(($(window).scrollLeft() + ($(window).width() - $(obj).width()) / 2)) + "px";
              $(obj).css({ top: top, left: left });
			  $(".opacty").css({"width":"100%"});
            }
            //缩放滚动同步
            $(window).bind('resize', setMask);
            $(window).bind('scroll', setMask);
          }
          ,
          /*
            opacty_obj 透明层自增长（高度）
          */
          "display_opacty":function (opacty_obj) {
            if (arguments[0]) opacty_obj = '.opacty';
            //透明层 自增长高度
            function set_opacty_mask() {
              var opacty_height = $(window).height() + $(window).scrollTop();
              $(opacty_obj).height(opacty_height);
            }
            //透明层缩放滚动同步
            $(window).bind('resize', set_opacty_mask);
            $(window).bind('scroll', set_opacty_mask);
         }
        ,
        //关闭透明层提示
        "opacty_close":function(div_tag){
			try{
			if(!arguments[0]) div_tag = '';
            //删除透明层
            $('.opacty').remove();
            if(div_tag==''){
                //删除提示层
                $('.div_tips').remove();
            }else{
                //隐藏提示层
                $(".tips_tit").remove();
				$(div_tag).css({'display':'none'});
                
			}
            C.alert.init();
			}catch(e){alert(e.message);}
        }
    }
	//cookie读写
	,"cookie":{
		//设置Cookie
		"set":function(name,value,hours){
			var __hours=1;
			if(arguments[2]){__hours=hours;}
			var exp = new Date();exp.setTime(exp.getTime() + __hours*60*60*1000);
			document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
		},
		//读取Cookie
		"get":function(name){
			var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
			if(arr=document.cookie.match(reg)){return unescape(arr[2]);}else{return null;}
		}
	}
	//加入收藏夹
	,"fav":function(tit,url){
		try{
			window.external.addFavorite(url, tit);
		}catch(e){
			try{
				window.sidebar.addPanel(tit,url,'');
			}catch(e){
				alert('您可以尝试通过快捷键' + ctrl + ' + D 加入到收藏夹~');
			}
		}
	}
	//日期时间
	,"date":{
		"localtime":function(nS){
			var dstr=new Date(parseInt(nS) * 1000).toLocaleString().replace(/:\d{1,2}$/,' ');
			dstr=dstr.replace('年','-').replace('月','-').replace('日',''); 
			return dstr;  
		}
		,"new_date":function(str){
			str=str.replace(/(\/)|(年)|(月)/g,'-');
			str=str.replace(/(日)/g,'');str=str.split(' ')[0];
			var newstr = str.split('-');
			var date = new Date();
			date.setUTCFullYear(newstr[0], newstr[1] - 1, newstr[2]);
			date.setUTCHours(0, 0, 0, 0);
			return date;
		} 
		/**
		 * 和PHP一样的时间戳格式化函数
		 * @param  {string} format    格式
		 * @param  {int}    timestamp 要格式化的时间 默认为当前时间
		 * @return {string}           格式化的时间字符串
		 */
		,"date":function(format, timestamp){ 
			var a, jsdate=((timestamp) ? new Date(timestamp*1000) : new Date());
			var pad = function(n, c){
				if((n = n + "").length < c){
					return new Array(++c - n.length).join("0") + n;
				} else {
					return n;
				}
			};
			var txt_weekdays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
			var txt_ordin = {1:"st", 2:"nd", 3:"rd", 21:"st", 22:"nd", 23:"rd", 31:"st"};
			var txt_months = ["", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]; 
			var f = {
				// Day
				d: function(){return pad(f.j(), 2)},
				D: function(){return f.l().substr(0,3)},
				j: function(){return jsdate.getDate()},
				l: function(){return txt_weekdays[f.w()]},
				N: function(){return f.w() + 1},
				S: function(){return txt_ordin[f.j()] ? txt_ordin[f.j()] : 'th'},
				w: function(){return jsdate.getDay()},
				z: function(){return (jsdate - new Date(jsdate.getFullYear() + "/1/1")) / 864e5 >> 0},
			   
				// Week
				W: function(){
					var a = f.z(), b = 364 + f.L() - a;
					var nd2, nd = (new Date(jsdate.getFullYear() + "/1/1").getDay() || 7) - 1;
					if(b <= 2 && ((jsdate.getDay() || 7) - 1) <= 2 - b){
						return 1;
					} else{
						if(a <= 2 && nd >= 4 && a >= (6 - nd)){
							nd2 = new Date(jsdate.getFullYear() - 1 + "/12/31");
							return date("W", Math.round(nd2.getTime()/1000));
						} else{
							return (1 + (nd <= 3 ? ((a + nd) / 7) : (a - (7 - nd)) / 7) >> 0);
						}
					}
				},
			   
				// Month
				F: function(){return txt_months[f.n()]},
				m: function(){return pad(f.n(), 2)},
				M: function(){return f.F().substr(0,3)},
				n: function(){return jsdate.getMonth() + 1},
				t: function(){
					var n;
					if( (n = jsdate.getMonth() + 1) == 2 ){
						return 28 + f.L();
					} else{
						if( n & 1 && n < 8 || !(n & 1) && n > 7 ){
							return 31;
						} else{
							return 30;
						}
					}
				},
			   
				// Year
				L: function(){var y = f.Y();return (!(y & 3) && (y % 1e2 || !(y % 4e2))) ? 1 : 0},
				//o not supported yet
				Y: function(){return jsdate.getFullYear()},
				y: function(){return (jsdate.getFullYear() + "").slice(2)},
			   
				// Time
				a: function(){return jsdate.getHours() > 11 ? "pm" : "am"},
				A: function(){return f.a().toUpperCase()},
				B: function(){
					// peter paul koch:
					var off = (jsdate.getTimezoneOffset() + 60)*60;
					var theSeconds = (jsdate.getHours() * 3600) + (jsdate.getMinutes() * 60) + jsdate.getSeconds() + off;
					var beat = Math.floor(theSeconds/86.4);
					if (beat > 1000) beat -= 1000;
					if (beat < 0) beat += 1000;
					if ((String(beat)).length == 1) beat = "00"+beat;
					if ((String(beat)).length == 2) beat = "0"+beat;
					return beat;
				},
				g: function(){return jsdate.getHours() % 12 || 12},
				G: function(){return jsdate.getHours()},
				h: function(){return pad(f.g(), 2)},
				H: function(){return pad(jsdate.getHours(), 2)},
				i: function(){return pad(jsdate.getMinutes(), 2)},
				s: function(){return pad(jsdate.getSeconds(), 2)},
				//u not supported yet
			   
				// Timezone
				//e not supported yet
				//I not supported yet
				O: function(){
					var t = pad(Math.abs(jsdate.getTimezoneOffset()/60*100), 4);
					if (jsdate.getTimezoneOffset() > 0) t = "-" + t; else t = "+" + t;
					return t;
				},
				P: function(){var O = f.O();return (O.substr(0, 3) + ":" + O.substr(3, 2))},
				//T not supported yet
				//Z not supported yet
			   
				// Full Date/Time
				c: function(){return f.Y() + "-" + f.m() + "-" + f.d() + "T" + f.h() + ":" + f.i() + ":" + f.s() + f.P()},
				//r not supported yet
				U: function(){return Math.round(jsdate.getTime()/1000)}
			};
			   
			return format.replace(/[\\]?([a-zA-Z])/g, function(t, s){
				if( t!=s ){
					// escaped
					ret = s;
				} else if( f[s] ){
					// a date function exists
					ret = f[s]();
				} else{
					// nothing special
					ret = s;
				}
				return ret;
			});
		}




	}
	//输入框历史痕迹
	,"sug_history":{
		//显示
		"show":function(obj){
			var sug_id=$(obj).attr('id');
			var sug_list=E.ini.one('history_sug',sug_id);
			if(sug_list=='') {sug_list=E.ini.one('history_sug',sug_id,'[]');return false;}
			var json=$.evalJSON(sug_list);
			var top=$(obj).offset().top;
			var left=$(obj).offset().left;
			var w=$(obj).width();var h=$(obj).height();
			var line_height=24;
			var line=json.length;
			if(line>0){
				$('#history_sug_div').remove();
				var sug_html='<div id="history_sug_div" style="z-index:1000;border:1px solid #1f7ad7;position:absolute;width:'+(w-2)+'px;height:'+((line+1)*line_height)+'px;line-height:'+line_height+'px;background:#fff;left:'+left+'px;top:'+(top+h-1)+'px;">';
				for(var i=0;i<json.length;i++){
					var sug_word=json[i].k;
					sug_html+='<a href="javascript:void(0);" style="height:'+(line_height-1)+'px;line-height:'+(line_height-1)+'px;" title="'+decodeURIComponent(sug_word)+'"><span class="sug_k" style="width:'+(w-40)+'px;overflow:hidden;">'+decodeURIComponent(sug_word)+'</span>';
					sug_html+='<span class="sug_del" onclick="C.sug_history.del(\''+sug_id+'\',\''+sug_word+'\');">删除&nbsp;</span></a>';
				}
				sug_html+='<a href="javascript:void(0);" style="height:'+(line_height-1)+'px;line-height:'+(line_height-1)+'px;"><span class="sug_del" onclick="C.sug_history.del(\''+sug_id+'\',\'\');">全部删除&nbsp;</span></a>';
				sug_html+='</div>';
				
				$(obj).parent().append(sug_html);
				$('#history_sug_div>a>.sug_k').click(function(){
					var v=$(this).text();
					$(obj).val(v);
					$('#history_sug_div').remove();
				});
			}
		},
		//增加
		"insert":function(obj,sug_word){
			sug_word=encodeURIComponent(sug_word.toLowerCase());
			var sug_list=E.ini.one('history_sug',obj);
			if(sug_list=='') E.ini.one('history_sug',obj,'[]');
			var json=$.evalJSON(sug_list);
			var repeat=false;
			for(var i=0;i<json.length;i++){
				if(json[i].k==sug_word){repeat=true;break;}
			}
			if(!repeat) json.unshift({"k":sug_word});
			if(json.length>10) json.pop();
			E.ini.one('history_sug',obj,$.toJSON(json));
		},
		//删除
		"del":function(obj,sug_word){
			if(sug_word=='') {E.ini.one('history_sug',obj,'[]');return false;}
			var sug_list=E.ini.one('history_sug',obj);
			if(sug_list=='') E.ini.one('history_sug',obj,'[]');
			var json=$.evalJSON(sug_list);
			for(var i=0;i<json.length;i++){
				if(json[i].k==sug_word){json.splice(i,1);}
			}
			E.ini.one('history_sug',obj,$.toJSON(json));
		}
	}
	//字符串处理
	,"string":{
		//字符串换行清除
		"clear_line":function(str){
			return str.replace(/\n/g,'').replace(/\r/g,'').replace(/\t/g,'');
		}
		//转移SQL中的单撇号
		,"sql_filter":function(str){
			return str.replace(/(\')/g,"''");
		}
		//判断字符串长度
		,"length":function(s){
			var char_length = 0;
			for (var i = 0; i < s.length; i++){
				var son_char = s.charAt(i);
				if(encodeURI(son_char).length > 3){
					char_length += 2;
				}else{
					char_length += 1;
				}
			}
			return char_length;
		}
		//判断是否标点符号
		,"bd_char":function(str){
			if(str.indexOf(',')>=0) return 1;
			if(str.indexOf('，')>=0) return 1;
			return 0;
		}
		//替换逗号
		,"repair_char":function(m){
			var str=$(m).val();
			$(m).val(str.replace(/(，)|(\|)/g,','));
		}
		//全角数字转半角
		,"dsbc":function(str,flag) {
			var i;
			var result='';
			if (str.length<=0) {return '';}
			for(i=0;i<str.length;i++)
			{ str1=str.charCodeAt(i);
			if(str1<125&&!flag)
			result+=String.fromCharCode(str.charCodeAt(i)+65248);
			else
			result+=String.fromCharCode(str.charCodeAt(i)-65248);
			}
			return result;
		}
	}
	/*
	tabs 方法参数说明：JSON格式
		{
			"selected":"#n3" // 选中的选项卡，没有此参数默认选中第一个
			,"event":"mouseover" //切换触发动作 click，mouseover
			,"style":{		//选项卡样式
				"sclass":"selected"	//选中
				,"nclass":"noselected" //未选中
			}
			,"extern_attr":"", //附加函数，执行完动作的时候调用， 此处为 代码，  用 eval 运行的
			,"params": //选项卡组
			[
				{"nav":"#n1","con":"#c1","sclass":"selected2","nclass":"noselecte2"}, //nav：选项卡ID，con：选项卡对应内容层ID，sclass,nclass，自定义样样式，没有则默认为上级的 style参数中定义
				{"nav":"#n2","con":"#c2"},
				{"nav":"#n3","con":"#c3"}
			]
		}
	简写方式：C.tabs({"params":[{"nav":"#n1","con":"#c1"},{"nav":"#n2","con":"#c2"},{"nav":"#n3","con":"#c3"}]});
	*/
	,"tabs":function(__params){
		//默认选中
		var selected=__params.selected;
		if(__params.selected){selected=__params.selected}else{selected=__params.params[0].nav;}
		//切换动作
		var event=__params.event;
		if(__params.event){event=__params.event}else{event='click';}
		//默认样式选中和不选中
		if(!__params.style) __params.style={"sclass":"selected","nclass":"selected_no"};
		//切换卡参数
		var params=__params.params;
		//遍历切换卡参数
		for(var i=0;i<params.length;i++){
			var tab=params[i];
			//选项卡自定义了样式
			var sclass=__params.style.sclass;if(tab.sclass) sclass=tab.sclass;
			var nclass=__params.style.nclass;if(tab.nclass) nclass=tab.nclass;
			//判断选中选项卡
			if(selected==tab.nav){
				$(tab.nav).removeClass(nclass);
				$(tab.nav).addClass(sclass);
				$(tab.con).css({'display':''});
				if(__params.extern_attr){
				    try{
						eval(__params.extern_attr);
					}catch(e){alert(e.message);}	
				}
				
			}else{
				$(tab.nav).removeClass(sclass);
				$(tab.nav).addClass(nclass);
				$(tab.con).css({'display':'none'});
			}//alert(event);
			//绑定事件
			$(tab.nav).unbind(event);
			$(tab.nav).bind(event,function(){
				C.tabs({"selected":"#"+$(this).attr('id'),"event":event,"style":__params.style,"params":params,"extern_attr":__params.extern_attr});
				
			});
		}
	}
	//播放声音
	,"play_sound":function(){
		var sounds_code='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="165" height="37" id="niftyPlayer1" align=""><param name=movie value="css/img/niftyplayer.swf?file=css/img/msg.mp3&as=1" /> <!--自动播放as=1--><param name=quality value=high /><param name=bgcolor value=#FFFFFF /><embed src="css/img/niftyplayer.swf?file=css/img/msg.mp3&as=1" quality=high bgcolor=#FFFFFF width="165" height="37" id="niftyPlayer1" name="niftyPlayer1" align="" type="application/x-shockwave-flash" swliveconnect="true" pluginspage="http://www.macromedia.com/go/getflashplayer"> </embed></object>';
		$(parent.window.document).find("#sound_box").html('aaaa'+sounds_code);
	}
	//输入框默认文本
	,"input_default":function(input,value){
		if($(input).val()=='') $(input).val(value);
		$(input).mousedown(function(){
			if($(this).val()==value) $(this).val('');
		}).blur(function(){
			if($(this).val()=='') $(this).val(value);
		});
	}
	/*表单操作方法
        params=["#a",".b",["#abc","abc"]] 数组中有两种类型，字符串和对象
        字符串统一赋空值或者默认第一个的值（select,checkbox,radio）
        对象则赋指定的值
    */
    ,"form": {
		  //新表单初始化或者给表单赋默认值
		  "init": function (params) {
			//遍历需要初始化的表单
			for (var i = 0; i < params.length; i++) {
			  var pname = params[i];
			  //判断文本还是对象
			  if (typeof (pname) == 'string') {
				//类初始化
				if (pname.substr(0, 1) == '.') {
				  $(pname).each(function (index) {
					C.form.setval($(this));
				  });
				}
				//id初始化
				if (pname.substr(0, 1) == '#') {
				  C.form.setval($(pname));
				}
			  } else if (typeof (pname) == 'object') {
				//符合数组格式
				if (pname.length == 2) {//alert(pname[0]);
				  //类初始化
				  if (pname[0].substr(0, 1) == '.') {
					$(pname[0]).each(function (index) {
					  C.form.setval($(this), pname[1]);
					});
				  }
				  //id初始化
				  if (pname[0].substr(0, 1) == '#') {
					C.form.setval($(pname[0]), pname[1]);
				  }
				}
			  }
			}
		  },
		  /*
		   *获取表单 元素值
		   *通过formID 获取该form 下所有表单元素值
		   */
		  "get_form": function (formID) {
			var value = [];
			formID = formID.indexOf('#') >= 0 ? formID : '#' + formID + ''; //判断参数是否带有#
	
			$(formID).find('input').each(function (index) {
			  var inp_type = this.type;
			  if (inp_type == 'text' || inp_type == 'hidden' || inp_type == 'password') {
				value.push('"' + this.id + '":"' + encodeURIComponent(this.value) + '"');
			  }
			  if (inp_type == 'radio' || inp_type == 'checkbox') {
				if ($(this).prop('checked')) {
					value.push('"' + this.name + '":"' + encodeURIComponent($("input[name='"+this.name+"']:checked").val()) + '"');
				}
			  }
			});
			$(formID).find('select').each(function () {//下拉
			  value.push('"' + this.id + '":"' + encodeURIComponent($(this).find("option:selected").val()) + '"');
			});
			$(formID).find('textarea').each(function () {//多行文本
			  value.push('"' + this.id + '":"' + encodeURIComponent($(this).val()) + '"');
			});
			//alert("{" + value.join(',') + "}");
			return $.evalJSON("{" + value.join(',') + "}");//转换json格式
		  },
		  /*
		   *通过formID 赋值 给该form 下ID对应的表单元素
		   *data 数据源
		   */
		  "set_form": function (formID, data) {
				formID = formID.indexOf('#') >= 0 ? formID : '#' + formID + ''; //判断参数是否带有#
				var value = [];
				for (var getID in data) {
				  value.push(['#' + getID, data[getID]]);
				}
				C.form.init(value);
		  },
		  //给表单设值，自动判断类型
		  "setval": function (obj, str) {
			  if (!arguments[1]) var str = '';
			  //文本框，密码框，直接赋值
			  if (obj.is('input') && (obj.attr('type') == 'text' || obj.attr('type') == 'password' || obj.attr('type') == 'hidden')) {
				obj.val(str);
			  }
			  //单选框，复选框
			  if (obj.is('input') && (obj.attr('type') == 'radio' || obj.attr('type') == 'checkbox')) {
				for(var i=0;i<obj.length;i++){
					if (obj.eq(i).attr('value') == str){
						obj.attr('checked', 'true');
					    return;
					}
				}
			  }
			  //下拉框，多行文本框
			  if (obj.is('select') || obj.is('textarea')) {
				obj.val(str);
			  }
	
		  }
	}
};

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




