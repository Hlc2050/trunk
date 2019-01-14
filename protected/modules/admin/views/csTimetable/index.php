<?php require(dirname(__FILE__) . "/../common/head.php"); ?>

<div class="main mhead">
    <div class="snav">计划管理 » 客服部排期表</div>
    <div class="mt10">
        <div class="tab_box">
            <a href="<?php echo $this->createUrl('csTimetable/promotionGroup'); ?>?group_id=0" <?php if ($page['group_id'] == 0) echo 'class="current"'; ?>>推广组</a>
            <a href="<?php echo $this->createUrl('csTimetable/promotioner'); ?>?group_id=1" <?php if ($page['group_id'] == 1) echo 'class="current"'; ?>>推广人员</a>
        </div>
    </div>
            <!--搜索栏-->
            <div class="mt10">
                <?php
                if ($page['group_id'] == 0 ) : //推广组 ?>
                    <?php require(dirname(__FILE__) . "/T_promotion_group.php"); ?>
                <?php elseif ($page['group_id'] == 1) : // 推广人员?>
                    <?php require(dirname(__FILE__) . "/T_promotioner.php"); ?>
                <?php endif ?>
            </div>
</div>
    <!--数据 -->
<div class="main mbody">
  <form>
    <?php if ($page['group_id'] == 0 ) : //推广组  ?>
        <?php require(dirname(__FILE__) . "/promotion_group.php"); ?>
    <?php elseif ($page['group_id'] == 1) : // 推广人员?>
        <?php require(dirname(__FILE__) . "/promotioner.php"); ?>
    <?php endif ?>
   </form>
</div>

