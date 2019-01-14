<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">下单黑名单 » 黑名单列表</div>
    <div class="mt10 clearfix">
        <a class="but2"
           href="<?php echo $this->createUrl('blackList/addIp') ?>">新增ip黑名单</a>
        <a class="but2"
           href="<?php echo $this->createUrl('blackList/addPhone') ?>">新增手机号黑名单</a>
    </div>
</div>
<div class="main mbody">
    <div class="mt10">
        <div class="tab_box">
            <?php foreach ($page['params_groups'] as $k => $r) { ?>
                <a href="<?php $this->createUrl('blackList/index'); ?>?group_id=<?php echo $r['value']; ?>" <?php if ($this->get('group_id') == $r['value']) echo 'class="current"'; ?>><?php echo $r['txt']; ?></a>
            <?php } ?>
        </div>
    </div>
    <div class="mt10">
        <?php if ($this->get('group_id') == 0) : //ip黑名单 ?>
            <?php require('ipBlackList.php'); ?>
        <?php elseif ($this->get('group_id') == 1) : //phone黑名单?>
            <?php require('phoneBlackList.php'); ?>
        <?php endif ?>
    </div>
</div>
