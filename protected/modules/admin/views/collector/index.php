<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">内容中心 »  采集器管理	</div>
 	<div class="mt10">
 		<form action="<?php echo $this->createUrl('collector/index'); ?>">
	    <select id="search_type" name="search_type">
	        <option value="keys" <?php echo $this->get('search_type')=='keys'?'selected':''; ?>>关键字</option>
	        <option value="id" <?php echo $this->get('search_type')=='id'?'selected':''; ?>>ID</option>
	    </select>&nbsp;
	    <input type="text" id="search_txt" name="search_txt" class="ipt" value="<?php echo $this->get('search_txt'); ?>" >
	    <input type="submit" class="but" value="查询"  >
    	</form>
    </div>
    <div class="mt10 clearfix">
        <div class="l">
           <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="修改排序" onclick="document.form_order.submit();" />','auth_tag'=>'collector_edit')); ?>
           <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="删除选中" onclick="set_some(\''.$this->createUrl('collector/delete').'?ids=[@]\',\'确定删除吗？\');" />','auth_tag'=>'collector_del')); ?>
           <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="添加采集器" onclick="location=\''.$this->createUrl('collector/update').'\'" />','auth_tag'=>'collector_add')); ?>
        </div> 
        <div class="r">
            
        </div>
    </div>
</div>
<div class="main mbody">
<form action="<?php echo $this->createUrl('collector/saveOrder'); ?>" name="form_order" method="post">
<table class="tb">
    <tr>
        <th width="100"><a href="javascript:;" onclick="check_all('.cklist');">全选/反选</a></th>
        <th width="48">排序</th>
        <th width="80"><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('collector/index').'?p='.$_GET['p'].'','field_cn'=>'ID','field'=>'id')); ?>	</th>
        <th width="300"  class="alignleft"><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('collector/index').'?p='.$_GET['p'].'','field_cn'=>'名称','field'=>'name')); ?></th>
        <th width="120"><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('collector/index').'?p='.$_GET['p'].'','field_cn'=>'采集归类','field'=>'cate_id')); ?>	</th>
        <th width="80"><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('collector/index').'?p='.$_GET['p'].'','field_cn'=>'运行次数','field'=>'runtimes')); ?>	</th>
        <th width="80"><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('collector/index').'?p='.$_GET['p'].'','field_cn'=>'总页数','field'=>'pagenums')); ?>	</th>
        <th width="80"><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('collector/index').'?p='.$_GET['p'].'','field_cn'=>'当前页数','field'=>'pagenums')); ?>	</th>
        <th width="80"><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('collector/index').'?p='.$_GET['p'].'','field_cn'=>'采集数量','field'=>'totals')); ?>	</th>
        <th width="120"><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('collector/index').'?p='.$_GET['p'].'','field_cn'=>'最后运行','field'=>'create_time')); ?>	</th>
        <th width="120"><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('collector/index').'?p='.$_GET['p'].'','field_cn'=>'创建时间','field'=>'create_time')); ?>	</th>
        <th width=120>操作</th>
    </tr>
    
   <?php 
   foreach($page['listdata']['list'] as $r){
   ?>
    <tr>   
        <td><input type="checkbox" class="cklist" value="<?php echo $r['id']; ?>" /></td>
        <td><input type="text" size="2" name="listorders[<?php echo $r['id']; ?>]" value="<?php echo $r['displayorder']; ?>" /></td>
        <td><?php echo $r['id']; ?></td>
        <td class="alignleft"><?php echo $r['name']; ?></td>
     	<td><?php echo isset(Cms::model()->categorys[$r['cate_id']])?Cms::model()->categorys[$r['cate_id']]['cname']:''; ?></td>
     	<td><?php echo $r['runtimes']; ?></td>
     	<td><?php echo $r['pagenums']; ?></td>
     	<td><?php echo $r['nowpage']; ?></td>
     	<td><?php echo $r['totals']; ?></td>
     	<td><?php echo $r['last_time']?date('Y-m-d H:i:s',$r['last_time']):'从未运行'; ?></td>
     	<td><?php echo date('Y-m-d H:i:s',$r['create_time']); ?></td>
        <td>
        <?php $this->check_u_menu(array('code'=>'<a onclick="return dialog_frame(this,700,500);"  href="'.$this->createUrl('collector/start').'?id='.$r['id'].'&p='.$_GET['p'].'">运行</a>','auth_tag'=>'collector_run')); ?>	
        <?php $this->check_u_menu(array('code'=>'<a href="'.$this->createUrl('collector/copy').'?id='.$r['id'].'&p='.$_GET['p'].'" onclick="return confirm(\'确定复制吗？复制之后请记得更改名称、页数、当前页、列表url等参数噢\')">复制</a>','auth_tag'=>'collector_edit')); ?>
        <?php $this->check_u_menu(array('code'=>'<a href="'.$this->createUrl('collector/update').'?id='.$r['id'].'&p='.$_GET['p'].'">修改</a>','auth_tag'=>'collector_edit')); ?></td>	
    </tr>
   <?php 
   } ?> 
     
    
</table>
  <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
  <div class="clear"></div>
</form>
</div>
