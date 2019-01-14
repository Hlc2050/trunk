<?php 
$upload_server_arr= Yii::app()->params['upload_server'];
$upload_server=$upload_server_arr['s1'];//echo $upload_server;
$verify_arr=Yii::app()->params['upload_server'];
$secret =$verify_arr['secret'];
$key = $verify_arr['key'];
$verify=helper::encrypt($secret.strtotime(date('Y-m-d H:i:s')),$key);
if(!isset($_GET['params'])) $_GET['params']='';
$params=$_GET['params'];
$params=preg_replace('~(\\\")~','"',$params);
$json=json_decode($params,true);
$style=isset($json['style'])?$json['style']:'';
$btn=isset($json['btn'])?$json['btn']:'本地上传';
$width=isset($json['width'])?$json['width'].'px':'80px';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
body{margin:0px;padding:0px;}
.input-file {overflow:hidden;position:relative;background:#eee;border:1px solid #ccc;cursor:pointer;width:<?php echo $width;?>;height:26px;line-height:26px;font-size:12px;display:inline-block;text-align:center;color:blue;}
.input-file:hover{background:#ddd;cursor:default;}
.input-file-disable{overflow:hidden;position:relative;background: #F3F3F3;border:1px solid #ccc;cursor:pointer;width:<?php echo $width;?>;height:26px;line-height:26px;font-size:12px;display:inline-block;text-align:center;color: #CCC;}
.input-file input{opacity:0;filter:alpha(opacity=0);font-size:100px;position:absolute;top:0;right:0;}
.input-file-disable input{opacity:0;filter:alpha(opacity=0);font-size:100px;position:absolute;top:0;right:0;}

/*自定义样式*/
.photo01{background:#ba88b9;color:#fff;}

</style>
<script type="text/javascript" src="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/js/jquery-1.7.1.min.js"></script> 
<script type="text/javascript"> 
 $(document).ready(function() { 
     //提交上传
    $('#file').bind('change', function() {//alert('测试中..');
		$("#filebtn").removeClass("input-file");
		$("#filebtn").addClass("input-file-disable");
		$("#dian").append('..');
        $('#form').submit();
		$('#file').attr("disabled",true);
    });
}) 
//回调通知
function callback_upload(ret){
	<?php if(isset($json['func'])){?> 
	window.parent.<?php echo($json['func']); ?>(ret);
	<?php }else{?>
	alert('未定义父框架的回调方法');
	<?php }?>
	$("#filebtn").removeClass("input-file-disable");
	$("#filebtn").addClass("input-file");
	$("#dian").html('');
	$('#file').attr("disabled",false);
}
</script>
</head>

<body> 
    <form   action='<?php echo($upload_server); ?>index.php/upload/uploadFile/index?params=<?php echo urlencode($params);?>&v=<?php echo($verify);?>' id="form" name="form" enctype="multipart/form-data" method="post" target="hidden_frame">   
       <a id="filebtn" class="input-file<?php echo $style?' '.$style:'';?>"><?php echo $btn;?><input type="file" id="file" name="file" size="1" style="width:<?php echo $width;?>;cursor:pointer;"><span id="dian"></span></a>
       <iframe name="hidden_frame" id="hidden_frame" frameborder="no" border="0″ marginwidth="0″ marginheight="0" scrolling="no" allowtransparency="yes"></iframe>
   </form> 
</body>
</html>