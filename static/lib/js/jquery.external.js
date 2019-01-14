//Input的文本框聚焦到输入文字末尾
$.fn.setCursorPosition = function(position){if(this.lengh == 0) return this;return $(this).setSelection(position, position);}
$.fn.setSelection = function(selectionStart, selectionEnd) {if(this.lengh == 0) return this;input = this[0];
if (input.createTextRange) {var range = input.createTextRange();range.collapse(true);range.moveEnd('character', selectionEnd);range.moveStart('character', selectionStart);range.select();} else if (input.setSelectionRange) {input.focus();input.setSelectionRange(selectionStart, selectionEnd);}return this;}
$.fn.focusEnd = function(){this.setCursorPosition(this.val().length);}

//扩展jQuery对json字符串的转换 
jQuery.extend({ 
   /** * @see 将json字符串转换为对象 * @param json字符串 * @return 返回object,array,string等对象 */ 
   evalJSON: function(strJson) { 
     return eval("(" + strJson + ")"); 
   },
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

Date.prototype.format = function(format)
    {
        var o = {
        "M+" : this.getMonth()+1, //month
        "d+" : this.getDate(),    //day
        "h+" : this.getHours(),   //hour
        "m+" : this.getMinutes(), //minute
        "s+" : this.getSeconds(), //second
        "q+" : Math.floor((this.getMonth()+3)/3),  //quarter
        "S" : this.getMilliseconds() //millisecond
        }
        if(/(y+)/.test(format)) format=format.replace(RegExp.$1,
        (this.getFullYear()+"").substr(4 - RegExp.$1.length));
        for(var k in o)if(new RegExp("("+ k +")").test(format))
        format = format.replace(RegExp.$1,
        RegExp.$1.length==1 ? o[k] :
        ("00"+ o[k]).substr((""+ o[k]).length));
        return format;
    }

jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        var path = options.path ? '; path=' + options.path : '; path=/';
        var domain = options.domain ? '; domain=' + options.domain : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};
jQuery.fn.autoZoomLoadImage = function(scaling, width, height, loadPic) {
  if (loadPic == null) loadPic = "/css/default/images/WhiteLoader.gif";
  return this.each(function() {
    var t = $(this);
    var src = $(this).attr("src");
    var img = new Image();
    //alert("Loading")
    img.src = src;
    //自动缩放图片
    var autoScaling = function() {
      if (scaling) {
        if (img.width > 0 && img.height > 0) {
          if (img.width / img.height >= width / height) {
            if (img.width > width) {
              t.width(width);
              t.height((img.height * width) / img.width);
            }
            else {
              t.width(img.width);
              t.height(img.height);
            }
          }
          else {
            if (img.height > height) {
              t.height(height);
              t.width((img.width * height) / img.height);
            }
            else {
              t.width(img.width);
              t.height(img.height);
            }
          }
        }
      }
    }
    //处理ff下会自动读取缓存图片
    if (img.complete) {
      //alert("getToCache!");
      autoScaling();
      return;
    }
    $(this).attr("src", "");
    var loading = $("<img alt=\"加载中\" title=\"图片加载中\" src=\"" + loadPic + "\" />");
    t.hide();
    t.after(loading);
    $(img).load(function() {
      autoScaling();
      loading.remove();
      t.attr("src", this.src);
      t.show();
      //alert("finally!")
    });
  });
}


$(function() { 
   /*  在textarea处插入文本--Start */ 
   (function($) { 
       $.fn 
               .extend({ 
                   insertContent : function(myValue, t) { 
                       var $t = $(this)[0]; 
                       if (document.selection) { // ie  
                           this.focus(); 
                            var sel = document.selection.createRange(); 
                            sel.text = myValue; 
                            this.focus(); 
                            sel.moveStart('character', -l); 
                            var wee = sel.text.length; 
                            if (arguments.length == 2) { 
                                var l = $t.value.length; 
                                sel.moveEnd("character", wee + t); 
                                t <= 0 ? sel.moveStart("character", wee - 2 * t 
                                        - myValue.length) : sel.moveStart( 
                                        "character", wee - t - myValue.length); 
                                sel.select(); 
                            } 
                        } else if ($t.selectionStart 
                                || $t.selectionStart == '0') { 
                            var startPos = $t.selectionStart; 
                            var endPos = $t.selectionEnd; 
                            var scrollTop = $t.scrollTop; 
                            $t.value = $t.value.substring(0, startPos) 
                                    + myValue 
                                    + $t.value.substring(endPos, 
                                            $t.value.length); 
                            this.focus(); 
                            $t.selectionStart = startPos + myValue.length; 
                            $t.selectionEnd = startPos + myValue.length; 
                            $t.scrollTop = scrollTop; 
                            if (arguments.length == 2) { 
                                $t.setSelectionRange(startPos - t, 
                                        $t.selectionEnd + t); 
                                this.focus(); 
                            } 
                        } else { 
                            this.value += myValue; 
                            this.focus(); 
                        } 
                    } 
                }) 
    })(jQuery); 
    /* 在textarea处插入文本--Ending */ 
});
