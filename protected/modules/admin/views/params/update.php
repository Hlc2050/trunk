<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<?php 
if(!isset($page['info'])){
	$page['info']['id']='';
	$page['info']['group_id']=$this->get('group_id');
	$page['info']['param_name']='';
	$page['info']['param_value']='';
	$page['info']['param_desc']='';

}
?>

<div class="main mhead">
    <div class="snav">系统管理 »  
    参数管理 </div>
</div>
<div class="main mbody">
<form method="post" action="<?php echo $this->createUrl($page['info']['id']?'params/edit':'params/add'); ?>?p=<?php echo $_GET['p'];?>&search_type=<?php echo $this->get('search_type');?>&search_txt=<?php echo $this->get('search_txt');?>">
<input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>" />
<table class="tb3">
    <tr>
        <th colspan="2" class="alignleft"><?php echo $page['info']['id']?'修改参数':'添加参数' ?></th>
    </tr>
    
    <tr>
        <td  width="100">参数分组：</td>
        <td  class="alignleft">
            <input type="hidden" name="group_id" value="<?php echo $page['info']['group_id']; ?>" >

        <?php $a=ParamsGroup::model()->getAll(); ?>
        <?php foreach($a as $r){ ?>
            <?php if($page['info']['group_id']==$r['id']){ echo $r['group_name'];} ?>
        <?php }?>

        </td>      
    </tr>
    

    <tr>
        <td  width="100">参数名称：</td>
        <td  class="alignleft">
            <input type="text" class="ipt" id="param_desc" name="param_desc" value="<?php echo $page['info']['param_desc']; ?>"/>
        </td>      
    </tr>
    <tr>
        <td  width="100">调用属性：</td>
        <td  class="alignleft">
            <input type="text" class="ipt" id="param_name" name="param_name" value="<?php echo $page['info']['param_name']; ?>"/>
        </td>
    </tr>
    <tr>
        <td  width="100">参数值：</td>
        <td  class="alignleft">
            <textarea class="ipt" id="param_value" name="param_value" style="width:300px;height:50px;"><?php echo $page['info']['param_value']; ?></textarea>
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