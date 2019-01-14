<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">效果统计 » 渠道缓存效果</div>
    <div class="mt10">
        <div class="tab_box">
                <?php foreach ($page['params_groups'] as $k => $r) { ?>
                    <a href="<?php $this->createUrl('effectCacheTable/index'); ?>?group_id=<?php echo $r['value']; ?>" <?php if ($this->get('group_id') == $r['value']) echo 'class="current"'; ?>><?php echo $r['txt']; ?></a>
                <?php } ?>
        </div>
</div>

<div class="main mbody">
    <form>
        <?php if ($this->get('group_id') == 0) : ?>
            <?php require(dirname(__FILE__) . "/channelTable.php"); ?>
        <?php elseif ($this->get('group_id') == 1) : ?>
        <?php require(dirname(__FILE__) . "/partnerTable.php"); ?>
        <?php endif ?>
    </form>
</div>