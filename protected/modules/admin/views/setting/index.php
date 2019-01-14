<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<script>
$(document).ready(function(){
	C.tabs(
	{"style":{		//选项卡样式
	"sclass":"current"	//选中
	},
	"params":[{"nav":"#tabbtn01","con":"#tab001"},{"nav":"#tabbtn03","con":"#tab003"},{"nav":"#tabbtn04","con":"#tab004"},{"nav":"#tabbtn05","con":"#tab005"},{"nav":"#tabbtn06","con":"#tab006"},{"nav":"#tabbtn07","con":"#tab007"},{"nav":"#tabbtn08","con":"#tab008"}]}
	)
})
</script>
</head>
<div class="main mhead">
    <div class="snav">系统功能  »  参数设置 </div>
</div>
<div class="main mbody">

<div class="tab_table">
  <div class="title01">系统参数</div>
   <div class="btn_box">
        <a href="javascript:void(0)" id="tabbtn01" class="current" >网站设置</a>
         <a href="javascript:void(0)" id="tabbtn05" >邮件设置</a>
        <a href="javascript:void(0)" id="tabbtn03" >邮件通知 </a>
         <a href="javascript:void(0)" id="tabbtn06" >水印设置</a>
         <a href="javascript:void(0)" id="tabbtn07" >后台设置</a>
   </div>
</div>
<form method="post" action="<?php echo $this->createUrl('setting/save'); ?>" >



<table class="tb3" id="tab001" width="100%">
    
    <tr>
        <td  width="100">网站名称：</td>
        <td  class="alignleft">
        <input type="text"  class="ipt"  id="old_upass" autocomplete="off"  value="<?php echo Yii::app()->params['basic']['sitename'];?>" name="basic[sitename]" />  <span id="msg_old_upass"></span>
        </td>      
    </tr>
    <tr>
        <td  width="100">备案号</td>
        <td  class="alignleft" >
    	  <input type="text"  class="ipt"  autocomplete="off" name="basic[icp]" value="<?php echo Yii::app()->params['basic']['icp']?>" /> 
         <span id="msg_old_upass"></span>
        </td>      
    </tr>
    <tr>
        <td  width="100">统计代码</td>
        <td  class="alignleft" >
    	  <textarea type="text"  class="ipt"  style="width:350px;height:80px;" autocomplete="off" name="basic[stat_code]"  ><?php echo  htmlspecialchars( str_replace("\'", "'", urldecode(Yii::app()->params['basic']['stat_code'])));?></textarea>
         <span id="msg_old_upass"></span>
        </td>      
    </tr>
     <tr>
        <td  width="100">首页SEO标题</td>
        <td  class="alignleft" >
         <textarea type="text"  class="ipt"  style="width:350px;height:40px;" autocomplete="off" name="basic[seo_title]"  ><?php echo  htmlspecialchars( str_replace("\'", "'", urldecode(Yii::app()->params['basic']['seo_title'])));?></textarea>
         <span id="msg_old_upass"></span>
        </td>      
    </tr>
     <tr>
        <td  width="100">首页SEO关键字</td>
        <td  class="alignleft" >
    	 <textarea type="text"  class="ipt"  style="width:350px;height:40px;" autocomplete="off" name="basic[seo_keyword]"  ><?php echo  htmlspecialchars( str_replace("\'", "'", urldecode(Yii::app()->params['basic']['seo_keyword'])));?></textarea>
         <span id="msg_old_upass"></span>
        </td>      
    </tr>
     <tr>
        <td  width="100">首页SEO描述</td>
        <td  class="alignleft" >
    	  <textarea type="text"  class="ipt"  style="width:350px;height:40px;" autocomplete="off" name="basic[seo_description]"  > <?php echo  htmlspecialchars( str_replace("\'", "'", urldecode(Yii::app()->params['basic']['seo_description'])));?></textarea>
         <span id="msg_old_upass"></span>
        </td>      
    </tr>
</table>

<table class="tb3" style="display:none;" id="tab003" >
    
    <tr>
        <td  width="100">是否开启通知：</td>
        <td  class="alignleft" >
            开启 <input type="radio" value="1" name="mail[notice]" <?php if (Yii::app()->params['mail']['isopen']==1){echo "checked='ture'";}?> />&nbsp;&nbsp;
    	    关闭  <input type="radio" value="0"  name="mail[notice]" <?php if (Yii::app()->params['mail']['isopen']==0){echo "checked='ture'";}?>/>
         <span id="msg_old_upass"></span>
        </td>      
    </tr>
    
      <tr>
        <td  width="100">接收邮箱：</td>
        <td  class="alignleft">
        <input type="text"  class="ipt"  id="old_upass" autocomplete="off" name="mail[reciveemail]" value="<?php echo Yii::app()->params['mail']['reciveemail']?>" />  <span id="msg_old_upass"> SMTP发信设置正确的话，所有自建表单有新信息，该邮箱能收到邮件</span>
        </td>      
    </tr>
    
</table>

<table class="tb3" style="display:none;" id="tab005" >
    <tr>
        <td  width="100">发件邮箱：</td>
        <td  class="alignleft">
        <input type="text"  class="ipt"  id="old_upass" autocomplete="off" name="mail[username]"  value="<?php echo Yii::app()->params['mail']['username']?>" />  <span id="msg_old_upass"> 如 xxx@126.com</span>
        </td>      
    </tr>
      <tr>
        <td  width="100">邮箱密码：</td>
        <td  class="alignleft">
        <input type="text"  class="ipt"  id="old_upass" autocomplete="off" name="mail[password]" value="<?php echo Yii::app()->params['mail']['password']?>"  />  <span id="msg_old_upass"></span>
        </td>      
    </tr>
      <tr>
        <td  width="100">邮件服务器：</td>
        <td  class="alignleft">
        <input type="text"  class="ipt"  id="old_upass" autocomplete="off" name="mail[host]" value="<?php echo Yii::app()->params['mail']['host']?>"  />  <span id="msg_old_upass"> 如 smtp.126.com</span>
        </td>      
    </tr>
      <tr>
        <td  width="100">邮件服务器端口：</td>
        <td  class="alignleft">
        <input type="text"  class="ipt"  id="old_upass" autocomplete="off" name="port" value="<?php echo Yii::app()->params['mail']['port']?>"  />  <span id="msg_old_upass"> 一般邮件服务器发信端口是25</span>
        </td>      
    </tr>
    </table>
    
    <table class="tb3" style="display:none;" id="tab006" >
        
        <tr>
            <td  width="100">开启水印：</td>
            <td  class="alignleft">
            开启 <input type="radio" value="1" name="water_mark[watermarkenabled]" <?php if (Yii::app()->params['water_mark']['watermarkenabled']==1){echo "checked='ture'";}?> />&nbsp;&nbsp;
    	 关闭  <input type="radio" value="0"  name="water_mark[watermarkenabled]" <?php if (Yii::app()->params['water_mark']['watermarkenabled']==0){echo "checked='ture'";}?>/>
            </td>      
        </tr>
        <tr>
            <td  width="100">打印方式：</td>
            <td  class="alignleft">
            文字 <input type="radio" value="0" name="water_mark[watermarktype]" <?php if (Yii::app()->params['water_mark']['watermarktype']==0){echo "checked='ture'";}?> />&nbsp;&nbsp;
    	 图片  <input type="radio" value="1"  name="water_mark[watermarktype]" <?php if (Yii::app()->params['water_mark']['watermarktype']==1){echo "checked='ture'";}?>/>
            </td>      
        </tr>
        <tr>
            <td  width="100">水印添加条件：</td>
            <td  class="alignleft">
            <input type="text" class="ipt size50" name="water_mark[watermarkwidth]" value="<?php echo Yii::app()->params['water_mark']['watermarkwidth'];?>" />px(宽)
            <input type="text" class="ipt size50" name="water_mark[watermarkheight]"  value="<?php echo Yii::app()->params['water_mark']['watermarkheight'];?>" />px(高)
            * 少于该尺寸则不加水印
           </td>      
        </tr>
        <tr>
            <td  width="100">水印图片路径：</td>
            <td  class="alignleft">
            <input type="text" class="ipt" id="watermarkimg" name="water_mark[watermarkimg]" value="<?php echo Yii::app()->params['water_mark']['watermarkimg'];?>" />
            <span id="watermarkimg_span">
            <?php 
			if(Yii::app()->params['water_mark']['watermarkimg']){
				echo '<img src="'.Yii::app()->params['water_mark']['watermarkimg'].'" />';
				}
			?>
            </span>
            <script>create_upload_iframe('{"func":"callback_upload","id_val":"watermarkimg"}');</script>
            * 您可上传图片，以达到不同的水印效果，支持PNG和GIF
            
           </td>      
        </tr>
        <tr>
            <td  width="100">水印文字：</td>
            <td  class="alignleft">
            <input type="text" class="ipt" name="water_mark[watermarktext]" value="<?php echo Yii::app()->params['water_mark']['watermarktext'];?>" />
           </td>      
        </tr>
        <tr>
            <td  width="100">水印字体：</td>
            <td  class="alignleft">
            <select  name="water_mark[water_mark_font_family]">
            <option value="C:/Windows/fonts/msyh.ttf" <?php if (Yii::app()->params['water_mark']['water_mark_font_family']=='C:/Windows/fonts/msyh.ttf'){echo "selected";}?>>微软雅黑</option>
            <option value="C:/Windows/fonts/msyhbd.ttf" <?php if (Yii::app()->params['water_mark']['water_mark_font_family']=='C:/Windows/fonts/msyhbd.tt'){echo "selected";}?>>微软雅黑</option>
            <option value="C:/Windows/fonts/simfang.ttf" <?php if (Yii::app()->params['water_mark']['water_mark_font_family']=='C:/Windows/fonts/simfang.ttf'){echo "selected";}?>>仿宋</option>
            <option value="C:/Windows/fonts/simhei.ttf" <?php if (Yii::app()->params['water_mark']['water_mark_font_family']=='C:/Windows/fonts/simhei.ttf'){echo "selected";}?>>黑体</option>
            <option value="C:/Windows/fonts/simkai.ttf" <?php if (Yii::app()->params['water_mark']['water_mark_font_family']=='C:/Windows/fonts/simkai.ttf'){echo "selected";}?>>楷体</option>
            </select>
           </td>      
        </tr>
        <tr>
            <td  width="100">水印文字大小：</td>
            <td  class="alignleft">
            <input type="text" class="ipt" name="water_mark[watermarkfontsize]" value="<?php echo Yii::app()->params['water_mark']['watermarkfontsize'];?>" />
           </td>      
        </tr>
        <tr>
            <td  width="100">水印文字颜色：</td>
            <td  class="alignleft">
            <input type="text" class="ipt" name="water_mark[watermarkfontcolor]" value="<?php echo Yii::app()->params['water_mark']['watermarkfontcolor'];?>" />
           </td>      
        </tr>
        <tr>
            <td  width="100">水印透明度：</td>
            <td  class="alignleft">
            <input type="text" class="ipt" name="water_mark[watermarkpct]" value="<?php echo Yii::app()->params['water_mark']['watermarkpct'];?>" />
           </td>      
        </tr>
        <tr>
            <td  width="100">JPEG水印图像质量：：</td>
            <td  class="alignleft">
            <input type="text" class="ipt" name="water_mark[watermarkquality]" value="<?php echo Yii::app()->params['water_mark']['watermarkquality'];?>" />
           </td>      
        </tr>
        <tr>
            <td  width="100">水印位置：</td>
            <td  class="alignleft">
            <input type="radio" class="ipt size50" name="water_mark[watermarkpos]"  value="0" <?php if (Yii::app()->params['water_mark']['watermarkpos']==0){echo "checked='ture'";}?>/>随机
            <input type="radio" class="ipt size50" name="water_mark[watermarkpos]"  value="1" <?php if (Yii::app()->params['water_mark']['watermarkpos']==1){echo "checked='ture'";}?>/>左上角
            <input type="radio" class="ipt size50" name="water_mark[watermarkpos]"  value="2" <?php if (Yii::app()->params['water_mark']['watermarkpos']==2){echo "checked='ture'";}?>/>右上角
            <input type="radio" class="ipt size50" name="water_mark[watermarkpos]"  value="3" <?php if (Yii::app()->params['water_mark']['watermarkpos']==3){echo "checked='ture'";}?>/>右下角
            <input type="radio" class="ipt size50" name="water_mark[watermarkpos]"  value="4" <?php if (Yii::app()->params['water_mark']['watermarkpos']==4){echo "checked='ture'";}?>/>左下角
            <input type="radio" class="ipt size50" name="water_mark[watermarkpos]"  value="5" <?php if (Yii::app()->params['water_mark']['watermarkpos']==5){echo "checked='ture'";}?>/>居中
            
           </td>      
        </tr>
      
    </table>
    
    <table class="tb3" style="display:none;" id="tab007" >
        <tr>
            <td  width="100">后台名称：</td>
            <td  class="alignleft">
            <input type="text"  class="ipt"  id="management" autocomplete="off"  value="<?php echo Yii::app()->params['management']['name'];?>" name="management[name]" />  <span id="msg_old_upass"></span>
            </td>      
        </tr>
    </table>
    <table class="tb3" >
    <tr>
        <td width="100"></td>
        <td  class="alignleft"><input type="submit" class="but" id="subtn" value="保存设置" /></td>
    </tr>
    </table>

</form>

</div>
<?php require(dirname(__FILE__)."/../common/foot.php"); ?>