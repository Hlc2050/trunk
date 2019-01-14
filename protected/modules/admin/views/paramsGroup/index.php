<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">系统管理 »  参数分组管理	</div>
     
     <div class="mt10">

 
    <div class="mt10 clearfix">
        <div class="l">
           <input type="button" class="but2" value="修改排序" onclick="document.form_order.submit();" />
           <input type="button" class="but2" value="删除选中" onclick="set_some('<?php echo $this->createUrl('paramsGroup/delete')?>?ids=[@]' ,'确定删除吗？');" />
		   <input type="button" class="but2" value="添加分组" onclick="return dialog_frame(this,500,500,1)" href="<?php echo $this->createUrl('paramsGroup/add').'?group_id='.$this->get('group_id').'&search_type='.$this->get('search_type').'&search_txt='.$this->get('search_txt'); ?>" />
        </div> 
        <div class="r">
            
        </div>
    </div>
</div>
<div class="main mbody">
<form action="<?php echo $this->createUrl('paramsGroup/saveOrder'); ?>" name="form_order" method="post">




<table class="tb">
    <tr>
        <th width="100"><a href="javascript:void(0);" onclick="check_all('.cklist');">全选/反选</a></th>
        <th width="80">排序</th>
        <th ><?php echo helper::fieldPaixu(array('a.group_name','分组名称')); ?></th>
        <th width="100"  ><?php echo helper::fieldPaixu(array('a.group_param_name','分组属性')); ?></th>
        <th  ><?php echo helper::fieldPaixu(array('a.group_param_name','是否显示')); ?></th>
        <th width=200>操作</th>
    </tr>
    
   <?php 
   foreach($page['listdata']['list'] as $r){
   ?>
    <tr>   
        <td><input type="checkbox" class="cklist" value="<?php echo $r['id']; ?>" /></td>
        <td><input type="text" size="2" name="listorders[<?php echo $r['id']; ?>]" value="<?php echo $r['displayorder']; ?>" /></td>

        <td><?php echo $r['group_name']; ?> </td>
        <td  style="font-size:14px;font-weight:bold;"><?php echo $r['group_param_name']; ?>
        <td><?php echo $r['isshow']?'显示':'<font color=red>隐藏</font>'; ?> </td>
        <td>
          <a onclick="return dialog_frame(this,500,500,1)" href="<?php echo $this->createUrl('paramsGroup/edit').'?id='.$r['id'].'&p='.$_GET['p'].'&search_type='.$this->get('search_type').'&search_txt='.$this->get('search_txt'); ?>">修改</a>
        </td>

    </tr>
   <?php 
   } ?> 
     
    
</table>
</form>
</div>


<?php require(dirname(__FILE__)."/../common/foot.php"); ?>
