<?php require(dirname(__FILE__) . "/../common/head.php"); ?>

<div class="main mhead">
    <div class="snav">计划管理 » 每月进粉计划</div>
    <div class="mt10">
        <div class="tab_box">
            <?php foreach ($page['params_groups'] as $k => $r) { ?>
                <a href="<?php $this->createUrl('planMonth/index'); ?>?group_id=<?php echo $r['value']; ?>" <?php if ($page['group_id'] == $r['value'] ||  $r['current_page'] == "Yes") echo 'class="current"'; ?>><?php echo $r['txt']; ?></a>
            <?php } ?>
        </div>
        <?php
        $id = Yii::app()->admin_user->uid;
        $authority = AdminUser::model()->getUserAuthority($id);
        ?>
        <?php if ($page['group_id'] == 0) : //待我审核  ?>
        <?php elseif ($page['group_id'] == 1) : // 个人计划?>
            <?php require(dirname(__FILE__) . "/T_PersonalPlan.php"); ?>
        <?php elseif ($page['group_id'] == 2) : // 组计划?>
            <?php require(dirname(__FILE__) . "/T_GroupPlan.php"); ?>
        <?php elseif ($page['group_id'] == 3) : // 提交记录?>
        <?php endif ?>

    </div>
</div>

<!--数据 -->
<div class="main mbody">
    <form>
        <?php if ($page['group_id'] == 0) : //待我审核  ?>
            <?php require(dirname(__FILE__) . "/Wait_Review.php"); ?>
        <?php elseif ($page['group_id'] == 1) : // 个人计划?>
            <?php require(dirname(__FILE__) . "/L_PersonalPlan.php"); ?>
        <?php elseif ($page['group_id'] == 2) : // 组计划?>
            <?php require(dirname(__FILE__) . "/L_GroupPlan.php"); ?>
        <?php elseif ($page['group_id'] == 3) : // 提交记录?>
            <?php require(dirname(__FILE__) . "/Submit_Log.php"); ?>
        <?php endif ?>
    </form>
</div>

