<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">域名管理 »  微信实时监测	</div>
     
     <div class="mt10">
    <div class="mt10 clearfix">

        <div class="l">

            监测时间：<?php echo date('Y-m-d H:i',time())?>
        </div>
    </div>
</div>
<div class="main mbody">
<form action="<?php echo $this->createUrl('ad/saveOrder'); ?>" name="form_order" method="post">
<table class="tb fixTh">
    <thead>
        <tr>
            <th width="80" class="cklist">ID</th>
            <th width="100" >域名</th>
            <th width="80" >添加时间</th>
            <th width="80" class="center">状态</th>
        </tr>
    </thead>
    
   <?php
   foreach($page['listdata']['list'] as $r){
   ?>
    <tr>   
        <td><?php echo $r['id']?></td>
        <td><?php echo $r['domain']?></td>
        <td><?php echo date('Y-m-d',$r['create_time'])?></td>
        <td><?php echo vars::get_field_str('domain_status', $r['status']);?></td>
    </tr>
   <?php
   } ?>
     
    
</table>
  <!--<div class="pagebar"><?php /*echo $page['listdata']['pagearr']['pagecode']; */?></div>-->
  <div class="clear"></div>
</form>
</div>

