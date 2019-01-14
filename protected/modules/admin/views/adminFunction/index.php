<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">系统管理 » [<?php echo $this->module->name;?>]功能动作权限管理	</div>
 	
    <div class="mt10 clearfix">
        <div class="l">
           <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="修改排序" onclick="document.form_order.submit();" />','auth_tag'=>'adminFunction_edit')); ?>
           <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="删除选中" onclick="set_some(\''.$this->createUrl('adminFunction/delete').'?module_id='.$this->get('module_id').'&ids=[@]\',\'确定删除吗？\');" />','auth_tag'=>'adminFunction_delete')); ?>
           <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="添加动作/功能" onclick="location=\''.$this->createUrl('adminFunction/add').'?module_id='.$this->get('module_id').'\'" />','auth_tag'=>'adminFunction_add')); ?>
           
        </div> 
        <div class="r">
            
        </div>
    </div>
</div>
<div class="main mbody">
<form action="<?php echo $this->createUrl('adminFunction/saveOrder'); ?>?module_id=<?php echo $this->get('module_id'); ?>" name="form_order" method="post">
<table class="tb">
    <tr>
        <th width="100"><a href="javascript:void(0);" onclick="check_all('.cklist');">全选/反选</a></th>
        <th align='center' width="80"> 排序</th>
        <th width="80"><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('adminFunction/index').'?p='.$_GET['p'].'','field_cn'=>'ID','field'=>'id')); ?>	</th>
        <th  class="alignleft"><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('adminFunction/index').'?p='.$_GET['p'].'','field_cn'=>'名称','field'=>'function_name')); ?></th>
        <th><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('adminFunction/index').'?p='.$_GET['p'].'','field_cn'=>'权限标识','field'=>'authority_id')); ?></th>
        <th>条件</th>
        <th width=200>操作</th>
    </tr>
    
   <?php 
   foreach($page['listdata']['list'] as $r){
   ?>
    <tr>   
        <td><input type="checkbox" class="cklist" value="<?php echo $r['id']; ?>" /></td>
        <td><input type="text" size="2" name="listorders[<?php echo $r['id']; ?>]" value="<?php echo $r['displayorder']; ?>" /></td>
        <td><?php echo $r['id']; ?></td>
        <td class="alignleft"><?php echo $r['function_name']; ?></td>
        <td>
        <?php echo $r['authority_id'];?>
        </td>
        <td>
        	<?php 
        	if($r['param_type']==0){
        		echo '-';
        	}else  if($r['param_type']==3){
                	echo '<font color=red>特殊</font>';
         	 }else{
				if($r['param_type']==1){
                	$gtype='GET';
                }else if($r['param_type']==2){
                	$gtype='POST';
                }
                
                if($r['param_value']==''){
                	echo '$_'.$gtype.'[\''.$r['param_name'].'\']!=\'\'';
                }else{
        			echo '$_'.$gtype.'[\''.$r['param_name'].'\']=='.$r['param_value'];
        		}
			}
        	?>
        </td>
        <td>
        <?php $this->check_u_menu(array('code'=>'<a href="'.$this->createUrl('adminFunction/edit').'?id='.$r['id'].'&module_id='.$this->get('module_id').'&p='.$_GET['p'].'">修改</a>','auth_tag'=>'adminFunction_edit')); ?></td>	
    </tr>
   <?php 
   } ?> 
     
    
</table>
  <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
  <div class="clear"></div>
</form>
</div>
<?php require(dirname(__FILE__)."/../common/foot.php"); ?>
