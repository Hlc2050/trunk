<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">域名管理 »  监测日志	</div>
     
     <div class="mt10">
    <div class="mt10 clearfix">
        <div class="l">
            <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="列表导出" onclick="location=\''.$this->createUrl('domainLog/export').'\'" />','auth_tag'=>'domainLog_export')); ?>
        </div> 
        <div class="r">
            
        </div>
    </div>
</div>
<div class="main mbody">
<form action="<?php echo $this->createUrl('ad/saveOrder'); ?>" name="form_order" method="post">
<table class="tb fixTh">
    <thead>
        <tr>
            <th width="80" >ID</th>
            <th width="120" >原域名</th>
            <th width="120" >替换域名</th>
            <th width="60" >类型</th>
            <th width="80" >监测内容</th>
            <th width="100" >域名添加时间</th>
            <th width="100">最后更新时间</th>
            <th width=100>使用天数</th>
        </tr>
    </thead>
    
   <?php foreach($data as $key=>$r){ ?>
    <tr>   
        <td><?php echo $r['id'];?></td>
        <td><?php echo $r['domain'];?></td>
        <td><?php echo $r['domain2'];?></td>
        <td><?php echo $r['domain_type']?></td>
        <td><?php echo $r['detection_type'];?></td>
        <td><?php echo $r['create_time'];?></td>
        <td><?php echo $r['update_time']; ?></td>
        <td><?php echo $r['useday']; ?></td>
    </tr>
   <?php }; ?>
     
    
</table>
  <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
  <div class="clear"></div>
</form>
</div>

<?php require(dirname(__FILE__)."/../common/foot.php"); ?>
