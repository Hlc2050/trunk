<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<script>
</script>
<div class="main mhead">
    <div class="snav"> 
    <?php foreach($page['parent_model_cates'] as $r){?>
        <?php if($r['parent_model_id']==0){ ?>
         <?php echo $r['model_name']; ?> »  <?php echo isset($page['id_data']['info_title'])?$page['id_data']['info_title'].' » ':''; ?>
		<?php }else{?>
		<a href="?id=<?php echo $_GET['id']; ?>&id_model_id=<?php echo $_GET['id_model_id'];?>&cmodel_id=<?php echo $r['model_id']; ?>"><?php echo $r['model_name']; ?></a> » 
        <?php }?>
	<?php $lastmodelid=$r['model_id'];}?>
    <?php echo $page['cmodel']['model_name']; ?>
        </div>   
    <div class="mt10 clearfix">
        <div class="l">
            <input type="button" class="but2" value="修改排序" onclick="document.form_order.submit();" />
            <input type="button" class="but2" value="删除选中" onclick="set_some('<?php echo $this->createUrl('ChildTable/delete');?>?info_id=<?php echo $_GET['info_id']; ?>&<?php echo $page['pmodel']['model_table_name'].'_id'; ?>=<?php echo $_GET[$page['pmodel']['id_name']]; ?>&id_model_id=<?php echo $_GET['id_model_id'];?>&cmodel_id=<?php echo $_GET['cmodel_id']; ?>&ids=[@]','确定删除吗？');" />
            <input type="button" class="but2" value="添加<?php echo $page['cmodel']['model_name']; ?> " onclick="location='<?php echo $this->createUrl('ChildTable/update');?>?p=<?php echo $_GET['p'];  ?>&info_id=<?php echo $_GET['info_id']; ?>&<?php echo $page['pmodel']['model_table_name'].'_id'; ?>=<?php echo $_GET[$page['pmodel']['id_name']]; ?>&id_model_id=<?php echo $_GET['id_model_id'];?>&cmodel_id=<?php echo $_GET['cmodel_id']; ?>'" />
        </div> 
        <div class="r">
            
        </div>
    </div>
</div>
<div class="main mbody">
<form action="<?php echo $this->createUrl('ChildTable/saveOrder'); ?>?p=<?php echo $_GET['p'];  ?>&info_id=<?php echo $_GET['info_id']; ?>&<?php echo $page['pmodel']['model_table_name'].'_id'; ?>=<?php echo $_GET[$page['pmodel']['id_name']]; ?>&id_model_id=<?php echo $_GET['id_model_id'];?>&cmodel_id=<?php echo $_GET['cmodel_id']; ?>" name="form_order" method="post" >
<table class="tb" >
    <tr>
        <th width="80"><a href="javascript:void(0);" onclick="check_all('.cklist');">全选/反选</a></th>
        <th align='center' width="48">排序</th>
        <?php foreach($page['model_fields'] as $f){?>
        <th><?php echo $f['field_txt']; ?></th>
       <?php } ?>
        <th width=200>操作 </th>
    </tr>
    
   <?php 
   foreach($page['listdata']['list'] as $r){
   ?>
    <tr>   

        <td><input type="checkbox" class="cklist" value="<?php echo $r['id']; ?>" /></td>
        <td><input type="text" size="2" name="listorders[<?php echo $r['id']; ?>]" value="<?php echo $r['corder']; ?>" /></td>
        <?php foreach($page['model_fields'] as $f){?>
        <td><?php  echo form_type_code::get_html(array('m'=>'list_show_value','field_value'=>$r[$f['field_name']],'type'=>$f['form_type'],'ini_value'=>$f['setting']['ini_value'],'linkage_type_id'=>$f['linkage_type_id'])) ?></td>
         <?php }?>
        <td>       
         <?php foreach($page['cmodels'] as $r2){ ?>
         <a href="child_table.php?id=<?php echo $_GET['id'];?>&<?php echo $page['cmodel']['model_table_name'].'_id'; ?>=<?php echo $r['id']; ?>&id_model_id=<?php echo $_GET['id_model_id'];?>&cmodel_id=<?php echo $r2['model_id'];?>"><?php echo $r2['model_name'];?></a>
		 <?php }?>
         <a href="<?php echo $this->createUrl('ChildTable/update');?>?p=<?php echo $_GET['p'];  ?>&id=<?php echo $r['id']; ?>&info_id=<?php echo $_GET['info_id']; ?>&<?php echo $page['pmodel']['model_table_name'].'_id'; ?>=<?php echo $_GET[$page['pmodel']['id_name']]; ?>&id_model_id=<?php echo $_GET['id_model_id'];?>&cmodel_id=<?php echo $_GET['cmodel_id']; ?>">修改</a> </td>
        
    </tr>
   <?php } ?> 
     
    
</table>
  <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
  <div class="clear"></div>
</form>
</div>
