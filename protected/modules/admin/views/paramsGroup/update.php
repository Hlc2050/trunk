<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<?php 
if(!isset($page['info'])){
	$page['info']['id']='';
	$page['info']['group_name']='';
	$page['info']['group_param_name']='';
    $page['info']['isshow']=1;
    $page['info']['displayorder']=50;

}
?>

<div class="main mhead">
    <div class="snav">系统管理 »  
    参数分组管理 </div>
</div>
<div class="main mbody">
<form method="post" action="<?php echo $this->createUrl($page['info']['id']?'paramsGroup/edit':'paramsGroup/add'); ?>?p=<?php echo $_GET['p'];?>&search_type=<?php echo $this->get('search_type');?>&search_txt=<?php echo $this->get('search_txt');?>">
<input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>" />
<table class="tb3">
    <tr>
        <th colspan="2" class="alignleft"><?php echo $page['info']['id']?'修改参数':'添加参数' ?></th>
    </tr>
    


    <tr>
        <td  width="100">分组名称：</td>
        <td  class="alignleft">
            <input type="text" class="ipt" id="group_name" name="group_name" value="<?php echo $page['info']['group_name']; ?>"/>
        </td>      
    </tr>
    <tr>
        <td  width="100">调用属性：</td>
        <td  class="alignleft">
            <input type="text" class="ipt" id="group_param_name" name="group_param_name" value="<?php echo $page['info']['group_param_name']; ?>"/>
        </td>
    </tr>
    <tr>
        <td  width="100">是否显示：</td>
        <td  class="alignleft">
            <input type="radio"  name="isshow" value="1" <?php echo $page['info']['isshow']==1?'checked':''; ?>/> 显示
            <input type="radio"  name="isshow" value="0" <?php echo $page['info']['isshow']==0?'checked':''; ?>/> 隐藏
        </td>
    </tr>
    <tr>
        <td  width="100">显示顺序：</td>
        <td  class="alignleft">
            <input type="text" class="ipt" size="5" id="displayorder" name="displayorder" value="<?php echo $page['info']['displayorder']; ?>"/>
        </td>
    </tr>
    
    <tr>
        <td></td>
        <td  class="alignleft">
        <input type="submit" class="but" id="subtn" value="确定" />
         <input type="button" class="but" value="关闭" onclick="art.dialog.open.api.close();" /></td>
    </tr>
</table>
</form>
</div>
<?php require(dirname(__FILE__)."/../common/foot.php"); ?>