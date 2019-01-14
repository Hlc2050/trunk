<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">渠道管理 » 微信号变更记录	</div>
 	<div class="mt10">
	    <select id="search_type">
	        <option value="keys" <?php echo isset($_GET['search_type'])&&$_GET['search_type']=='keys'?'selected':''; ?>>关键字</option>
	        <option value="weixin_id" <?php echo isset($_GET['search_type'])&&$_GET['search_type']=='weixin_id'?'selected':''; ?>>微信ID</option>
            <option value="id" <?php echo isset($_GET['search_type'])&&$_GET['search_type']=='id'?'selected':''; ?>>ID</option>
	    </select>&nbsp;
	    <input type="text" id="search_txt" class="ipt" value="<?php echo isset($_GET['search_txt'])?$_GET['search_txt']:''; ?>" onkeyup="if(event.keyCode==13){window.location='<?php echo $this->createUrl('weChatChangeLog/index');?>?search_txt='+$('#search_txt').val()+'&search_type='+$('#search_type').val();}"  >&nbsp;
        操作用户：
        <?php
        $userArr = $this->toArr(AdminUser::model()->findAll());
        echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($userArr, 'csno', 'csname_true'),
            array('empty' => '请选择'));
        ?>
        <input type="hidden" id="weixin_id" name="weixin_id" value="<?php echo $_GET['weixin_id']; ?>"/>
        <input type="button" class="but" value="查询" onclick="window.location='<?php echo $this->createUrl('weChatChangeLog/index');?>?search_txt='+$('#search_txt').val()+'&search_type='+$('#search_type').val()+'&user_id='+$('#user_id').val()+'&weixin_id='+$('#weixin_id').val();" >&nbsp;

    </div>

</div>
<div class="main mbody">
<form action="?m=save_order" name="form_order" method="post">
<table class="tb fixTh">
    <thead>
    <tr>
        <th align='center'><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('weChatChangeLog/index').'?p='.$_GET['p'].'','field_cn'=>'ID','field'=>'log_id')); ?></th>
        <th align='center'><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('weChatChangeLog/index').'?p='.$_GET['p'].'','field_cn'=>'微信ID','field'=>'weixin_id')); ?></th>
        <th align='center'>操作细节</th>
        <th><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('weChatChangeLog/index').'?p='.$_GET['p'].'','field_cn'=>'操作用户','field'=>'b.csname')); ?></th>
        <th><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('weChatChangeLog/index').'?p='.$_GET['p'].'','field_cn'=>'时间','field'=>'log_time')); ?></th>
    </tr>
    </thead>
    
   <?php 
   foreach($page['listdata']['list'] as $r){
   ?>
    <tr>   
        <td><?php echo $r['log_id']; ?></td>
        <td><?php echo $r['weixin_id']; ?></td>
        <td style=" word-break:break-all;max-width: 500px">
        	<div>
        	<?php echo helper::cut_str($r['log_details'],150); ?>
        	</div>
        </td>
        <td><?php echo $r['csname_true']; ?></td>
        <td><?php echo date('Y-m-d H:i:s',$r['log_time']); ?></td>
    </tr>
   <?php 
   } ?> 
     
    
</table>
  <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
  <div class="clear"></div>
</form>
</div>

