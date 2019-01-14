<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">内容中心 »  页面权限管理	</div>
    <div class="mt10 clearfix">
        <div class="l">
           <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="修改排序" onclick="document.form_order.submit();" />','auth_tag'=>'adminModules_edit')); ?>
           <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="添加菜单页面" onclick="location=\''.$this->createUrl('adminModules/add').'\'" />','auth_tag'=>'adminModules_add')); ?>
        </div> 
        <div class="r">
            
        </div>
    </div>
</div>
<div class="main mbody">
<form action="<?php echo $this->createUrl('adminModules/saveOrder'); ?>" name="form_order" method="post">
<table class="tb">
    <tr>
        <th width="48">排序</th>
        <th width="80"><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('adminModules/index').'?p='.$_GET['p'].'','field_cn'=>'ID','field'=>'id')); ?>	</th>
        <th  class="alignleft">名称</th>
        <th>模块或控制器</th>
        <th>URL</th>
        <th>显示</th>
        <th width=200>操作</th>
    </tr>
    
   <?php echo $page['categorys']; ?>
     
    
</table>
  <div class="clear"></div>
</form>
</div>
<?php require(dirname(__FILE__)."/../common/foot.php"); ?>