<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<?php 
if(!isset($page['info'])){
	foreach($page['model_fields2'] as $f){
		$page['info'][$f['field_name']]=$f['setting']['default_value'];
	}
	$page['info']['id']='';	
	$page['info'][$page['cmodel']['tid_name']]=$_GET['info_id'];
	$page['info'][$page['pmodel']['id_name']]=$_GET[$page['pmodel']['id_name']];
	$page['info']['corder']=50;

}
?>
<div class="main mhead">
    <div class="snav"> 
    <?php foreach($page['parent_model_cates'] as $r){?>
        <?php if($r['parent_model_id']==0){ ?>
         <?php echo $r['model_name']; ?> »  <?php echo isset($page['id_data']['info_title'])?$page['id_data']['info_title'].' » ':''; ?>
		<?php }else{?>
		<a href="?id=<?php echo $_GET['info_id']; ?>&id_model_id=<?php echo $_GET['id_model_id'];?>&cmodel_id=<?php echo $r['model_id']; ?>"><?php echo $r['model_name']; ?></a> » 
        <?php }?>
	<?php $lastmodelid=$r['model_id'];}?>
    <?php echo $page['cmodel']['model_name']; ?>
    
        </div>
   
</div>
<div class="main mbody">
<form method="post" action="<?php $this->createUrl('ChildTable/update'); ?>?p=<?php echo $_GET['p'];  ?>&info_id=<?php echo $_GET['info_id']; ?>&<?php echo $page['pmodel']['model_table_name'].'_id'; ?>=<?php echo $_GET[$page['pmodel']['id_name']]; ?>&id_model_id=<?php echo $_GET['id_model_id'];?>&cmodel_id=<?php echo $_GET['cmodel_id']; ?>">
<input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>" />
<table class="tb3">
    <tr>
    	<th colspan="2" class="alignleft"><?php echo $page['info']['id']?'修改'.$page['cmodel']['model_name']:'添加'.$page['cmodel']['model_name']; ?></th>        
    </tr>
    
    <?php foreach($page['model_fields2'] as $f){?>
    <tr>
        <td  width="100"><?php echo $f['field_txt']; ?>：</td>
        <td style="position:relative;">
        <div style="position:relative;">
         <?php echo form_type_code::get_html(array('type'=>$f['form_type'],'default_value'=>$page['info'][$f['field_name']],'form_name'=>$f['field_name'],'ini_value'=>isset($f['setting']['ini_value'])?$f['setting']['ini_value']:'','linkage_type_id'=>$f['linkage_type_id'])); ?>
         <?php echo $f['tips']; ?>
        </div>
        </td>
    </tr>
    <?php }?>    
    
    <tr>
        <td></td>
        <td  class="alignleft"><input type="submit" class="but" id="subtn" value="确定" /> <input type="button" class="but" value="返回" onclick="window.location='<?php echo $this->createUrl('childTable/index'); ?>?p=<?php echo $_GET['p'];  ?>&info_id=<?php echo $_GET['info_id']; ?>&<?php echo $page['pmodel']['model_table_name'].'_id'; ?>=<?php echo $_GET[$page['pmodel']['id_name']]; ?>&id_model_id=<?php echo $_GET['id_model_id'];?>&cmodel_id=<?php echo $_GET['cmodel_id']; ?>'" /></td>
    </tr>
</table>
</form>
</div>
