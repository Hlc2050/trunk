<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">系统管理 » 管理员角色管理	</div>
 	<div class="mt10">
	    <select id="search_type">
	        <option value="keys" <?php echo isset($_GET['search_type'])&&$_GET['search_type']=='keys'?'selected':''; ?>>关键字</option>
	        <option value="id" <?php echo isset($_GET['search_type'])&&$_GET['search_type']=='id'?'selected':''; ?>>ID</option>
	    </select>&nbsp;
	    <input type="text" id="search_txt" class="ipt" value="<?php echo isset($_GET['search_txt'])?$_GET['search_txt']:''; ?>" onkeyup="if(event.keyCode==13){window.location='<?php echo $this->createUrl('adminAclog/index');?>?search_txt='+$('#search_txt').val()+'&search_type='+$('#search_type').val();}"  >&nbsp;<input type="button" class="but" value="查询" onclick="window.location='<?php echo $this->createUrl('adminAclog/index');?>?search_txt='+$('#search_txt').val()+'&search_type='+$('#search_type').val();" >&nbsp;
    </div>

</div>
<div class="main mbody">
<form action="?m=save_order" name="form_order" method="post">
<table class="tb fixTh">
    <thead>
    <tr>
        <th align='center'><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('adminAclog/index').'?p='.$_GET['p'].'','field_cn'=>'ID','field'=>'log_id')); ?></th>
        <th><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('adminAclog/index').'?p='.$_GET['p'].'','field_cn'=>'操作员','field'=>'b.csname')); ?></th>
        <th  class="alignleft">操作细节</th>
        <th><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('adminAclog/index').'?p='.$_GET['p'].'','field_cn'=>'IP','field'=>'log_ip')); ?></th>
        <th><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('adminAclog/index').'?p='.$_GET['p'].'','field_cn'=>'时间','field'=>'log_time')); ?></th>
    </tr>
    </thead>
    
   <?php 
   foreach($page['listdata']['list'] as $r){
   ?>
    <tr>   
        <td><?php echo $r['log_id']; ?></td>
        <td><?php echo $r['csname']; ?></td>
        <td  class="alignleft" width=400>
        	<div style="width:400px;line-height:18px;word-wrap:break-word;">
        	<?php echo helper::cut_str($r['log_details'],80); ?>
        	</div>
        	</td>
        <td><?php echo $r['log_ip']; ?></td>
        <td><?php echo date('m-d H:i:s',$r['log_time']); ?></td>	
    </tr>
   <?php 
   } ?> 
     
    
</table>
  <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
  <div class="clear"></div>
</form>
</div>
<?php require(dirname(__FILE__)."/../common/foot.php"); ?>

