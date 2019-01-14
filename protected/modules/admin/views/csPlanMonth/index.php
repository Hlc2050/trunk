<?php require(dirname(__FILE__) . "/../common/head.php"); ?>

<div class="main mhead">
    <div class="snav">计划管理 » 客服部月目标</div>
    <div class="mt10">
        <div class="tab_box">
            <?php foreach ($page['params_groups'] as $k => $r) { ?>
                    <a href="<?php echo $this->createUrl('planMonth/index'); ?>?group_id=<?php echo $r['value']; ?>" <?php if ($this->get('group_id') == $r['value']) echo 'class="current"'; ?>><?php echo $r['txt']; ?></a>
                <?php } ?>
        </div>
    </div>

    <!--搜索栏-->
    <div class="mt10">
        <?php
        if ($this->get('group_id') == 0) : //推广组 ?>
            <?php require(dirname(__FILE__) . "/T_overall.php"); ?>
        <?php endif ?>
    </div>
</div>

<!--数据 -->
<div class="main mbody">
    <form>
        <?php if ($this->get('group_id') == 0) : //推广组  ?>
            <?php require(dirname(__FILE__) . "/overall.php"); ?>
        <?php endif ?>
    </form>
</div>