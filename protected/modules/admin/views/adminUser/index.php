<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">内容中心 »  系统用户管理	</div>
 	<div class="mt10">
	    <select id="search_type">
	        <option value="keys" <?php echo $this->get('search_type')=='keys'?'selected':''; ?>>关键字</option>
	        <option value="id" <?php echo $this->get('search_type')=='id'?'selected':''; ?>>ID</option>
	    </select>&nbsp;
	    <input type="text" id="search_txt" class="ipt" value="<?php echo $this->get('search_txt'); ?>" onkeyup="if(event.keyCode==13){window.location='<?php echo $this->createUrl('adminUser/index');?>?search_txt='+$('#search_txt').val()+'&search_type='+$('#search_type').val();}"  >&nbsp;<input type="button" class="but" value="查询" onclick="window.location='<?php echo $this->createUrl('adminUser/index');?>?search_txt='+$('#search_txt').val()+'&search_type='+$('#search_type').val();" >&nbsp;
    </div>
    <div class="mt10 clearfix">
        <div class="l">
           <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="删除选中" onclick="set_some(\''.$this->createUrl('adminUser/delete').'?ids=[@]\',\'确定删除吗？\');" />','auth_tag'=>'adminUser_delete')); ?>
           <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="添加系统用户" onclick="location=\''.$this->createUrl('adminUser/update').'\'" />','auth_tag'=>'adminUser_update')); ?>
            <input type="button" class="but2" value="冻结帐号" onclick="set_some('<?php echo $this->createUrl('adminUser/changeState'); ?>?ids=[@]&ustate=1','确定冻结吗？');" />
            <input type="button" class="but2" value="启用帐号" onclick="set_some('<?php echo $this->createUrl('adminUser/changeState'); ?>?ids=[@]&ustate=0','确定启用吗？');" />
        </div>
        <div class="r">
            
        </div>
    </div>
</div>
<div class="main mbody">
<form action="<?php echo $this->createUrl('adminUser/saveOrder'); ?>" name="form_order" method="post">
<table class="tb">
    <tr>
        <th width="100"><a href="javascript:void(0);" onclick="check_all('.cklist');">全选/反选</a></th>
        <th align='center' width="80"><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('adminUser/index').'?p='.$_GET['p'].'','field_cn'=>'ID','field'=>'csno')); ?>	</th>
        <th width="120"  class="alignleft"><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('adminUser/index').'?p='.$_GET['p'].'','field_cn'=>'账号','field'=>'csname')); ?></th>
        <th>名字</th>
        <th><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('adminUser/index').'?p='.$_GET['p'].'','field_cn'=>'联系电话','field'=>'csmobile')); ?></th>
        <th><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('adminUser/index').'?p='.$_GET['p'].'','field_cn'=>'部门','field'=>'groupid')); ?></th>
        <th><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('adminUser/index').'?p='.$_GET['p'].'','field_cn'=>'岗位','field'=>'groupid')); ?></th>
        <th><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('adminUser/index').'?p='.$_GET['p'].'','field_cn'=>'状态','field'=>'csstatus')); ?></th>
        <th width=60>操作</th>
    </tr>
    
   <?php
   foreach($page['listdata']['list'] as $r){
       $css22=$r['csstatus']?' style="color:#999;" ':'';
   ?>
    <tr>
        <?php if($r['service'] == 0){ ?>
        <td <?php echo $css22;  ?>><input type="checkbox" class="cklist" value="<?php echo $r['csno']; ?>" /></td>
        <td <?php echo $css22;  ?>><?php echo $r['csno']; ?></td>
        <td <?php echo $css22;  ?> class="alignleft"><?php echo $r['csname']; ?></td>

        <td <?php echo $css22;  ?>><?php echo $r['csname_true']; ?></td>
        <td <?php echo $css22;  ?>><?php echo $r['csmobile']; ?></td>
        <td <?php echo $css22;  ?>>

            <?php $groups=AdminUser::model()->get_user_group($r['csno']);
             foreach($groups as $r2){echo '['.$r2['groupname'].']';} ?>


        </td>
        <td <?php echo $css22;  ?>>
            <?php $roles=AdminUser::model()->get_user_role($r['csno']);
            foreach($roles as $r2){echo '['.$r2['role_name'].']';} ?>

        </td <?php echo $css22;  ?>>
        <td><?php echo $r['csstatus']?'<font color=red>已被冻结</font>':'正常使用'; ?></td>
        <td <?php echo $css22;  ?>>
         <?php if($r['csno']!=Yii::app()->params['management']['super_admin_id']){ ?>
        <?php $this->check_u_menu(array('code'=>'<a href="'.$this->createUrl('adminUser/update').'?id='.$r['csno'].'&p='.$_GET['p'].'">修改</a>','auth_tag'=>'adminUser_update')); ?></td>
    	<?php }else{?>
    	-
    	<?php }?>
    
    </tr>
       <?php }?>
   <?php 
   } ?> 
     
    
</table>
  <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
  <div class="clear"></div>
</form>
</div>
