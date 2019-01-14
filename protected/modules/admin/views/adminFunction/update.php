<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<?php 
if(!isset($page['info'])){
	$page['info']['id']='';
	$page['info']['function_name']='';
	$page['info']['authority_id']='';
	$page['info']['param_type']=0;
	$page['info']['param_name']='';
	$page['info']['param_value']='';
	$page['info']['displayorder']=50;
}
?>
<div class="main mhead">
    <div class="snav">系统功能 »  
    动作权限管理 </div>
</div>
<div class="main mbody">
<form method="post" action="<?php echo $this->createUrl($page['info']['id']?'adminFunction/edit':'adminFunction/add'); ?>?module_id=<?php echo $this->get('module_id');?>&p=<?php echo $_GET['p'];?>">
<input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>" />
<table class="tb3">
    <tr>
        <th colspan="2" class="alignleft"><?php echo $page['info']['id']?'修改动作':'添加动作'; ?></th>
    </tr> 
    <tr>
    	<td>菜单/控制器名：</td>
    	<td><?php echo $this->module->name;?></td>
    </tr>
    
    <tr>
        <td  width="100">动作名称：</td>
        <td  class="alignleft">
        <input type="text" class="ipt"  id="function_name"   name="function_name" value="<?php echo $page['info']['function_name']; ?>"/> 
		
        </td>      
    </tr>
    <tr>
        <td  width="100">权限标识：</td>
        <td  class="alignleft">
         <input type="text"  class="ipt"  id="authority_id"   name="authority_id" value="<?php echo $page['info']['authority_id']; ?>"/>
         <span> * 这是唯一的,通常是 控制器名_动作名 </span></td>      
    </tr>
     <tr>
        <td  width="100">参数类型：</td>
        <td  class="alignleft">
        
         <label><input type="radio" class="ipt" id="param_type" name="param_type"  value="0" <?php echo $page['info']['param_type']==0?'checked':''; ?>/> 无参数 </label>
         <label> <input type="radio" class="ipt" id="param_type" name="param_type"  value="1" <?php echo $page['info']['param_type']==1?'checked':''; ?>/> GET</label>
         <label> <input type="radio" class="ipt" id="param_type" name="param_type" value="2" <?php echo $page['info']['param_type']==2?'checked':''; ?>/> POST</label>
         <label><input type="radio" class="ipt" id="param_type" name="param_type"  value="3" <?php echo $page['info']['param_type']==3?'checked':''; ?>/>有相同标识，无参数或参数为空 </label>
         
         <span style="margin-left:20px;"> * 控制器里的一个方法支持多个权限，比如 添加和修改都放到一个方法里，则可以根据参数进行判断  </span></td>      
    </tr>
    
    <tr>
        <td  width="100">参数名：</td>
        <td  class="alignleft">
        <input type="text" size="10" class="ipt"  id="param_name"   name="param_name" value="<?php echo $page['info']['param_name']; ?>"/> 

        </td>      
    </tr>
    
    <tr>
        <td  width="100">参数值：</td>
        <td  class="alignleft">
        <input type="text" size="10" class="ipt"  id="param_value"   name="param_value" value="<?php echo $page['info']['param_value']; ?>"/>  * 单纯文本数字，如果不填写，则非空即符合 

        </td>      
    </tr>
    
    <tr>
        <td  width="100">显示顺序：</td>
        <td  class="alignleft">
        <input type="text" size="10" class="ipt"  id="displayorder"   name="displayorder" value="<?php echo $page['info']['displayorder']; ?>"/> 

        </td>      
    </tr>
    
      
    
    <tr>
        <td></td>
        <td  class="alignleft"><input type="submit" class="but" id="subtn" value="确定" /> <input type="button" class="but" value="返回" onclick="window.location='<?php echo $this->createUrl('adminFunction/index'); ?>?module_id=<?php echo $this->get('module_id');?>&p=<?php echo $_GET['p'];?>'" /></td>
    </tr>
</table>
</form>
</div>