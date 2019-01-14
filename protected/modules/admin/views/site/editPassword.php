<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<script>
function ck_upass(){
	try{
		$("#msg_old_upass").html('');
		$("#msg_new_upass").html('');
		$("#msg_new_upass2").html('');
		if($("#old_upass").val().replace(/\s*/g,'')==''){
			$("#old_upass").focus()
			$("#msg_old_upass").html('<font color=red> * 请输入原始密码</font>');
			return false;
		}
		if($("#new_upass").val().replace(/\s*/g,'').length<5){
			$("#new_upass").focus();
			$("#msg_new_upass").html('<font color=red> * 密码5-15位，请填写规范</font>');
			return false;
		}	
		if($("#new_upass2").val()!=$("#new_upass").val()){
			$("#new_upass2").focus();
			$("#msg_new_upass2").html('<font color=red> * 两次密码输入不一致</font>');
			return false;
		}
	}catch(e){alert(e.message);return false;}
}
</script>
<div class="main mhead">
    <div class="snav">系统 »  
    修改密码 </div>
</div>
<div class="main mbody">
<form method="post" action="<?php echo $this->createUrl("site/editPassword"); ?>" onSubmit="return ck_upass();">
<table class="tb3">
    <tr>
        <th colspan="2" class="alignleft">修改密码</th>
    </tr>
    
    <tr>
        <td  width="100">帐号：</td>
        <td  class="alignleft">
         <strong><?php echo Yii::app()->admin_user->uname;; ?></strong>
        </td>      
    </tr>
    <tr>
        <td  width="100">原始密码：</td>
        <td  class="alignleft">
        <input type="password"  class="ipt"  id="old_upass"   name="old_upass" />  <span id="msg_old_upass">* 初始密码不能为空</span>
        </td>      
    </tr>
    <tr>
        <td  width="100">新的密码：</td>
        <td  class="alignleft">
        <input type="password"  class="ipt"  id="new_upass"   name="new_upass" />  <span id="msg_new_upass"></span>
        </td>      
    </tr>
    <tr>
        <td  width="100">确认密码：</td>
        <td  class="alignleft">
        <input type="password"  class="ipt"  id="new_upass2"   name="new_upass2" />  <span  id="msg_new_upass2">* 两次密码要一致</span>
        </td>      
    </tr>
    
    
    <tr>
        <td></td>
        <td  class="alignleft"><input type="submit" class="but" id="subtn" value="确定" /></td>
    </tr>
</table>
</form>
</div>
<?php require(dirname(__FILE__)."/../common/foot.php"); ?>