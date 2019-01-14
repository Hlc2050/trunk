<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">客服部数据监测 » 客服部数据统计</div>
    <div class="mt10">
        <div class="tab_box">
            <?php foreach ($page['params_groups'] as $k => $r) { ?>
                <a href="<?php $this->createUrl('effectTable/index'); ?>?tab_id=<?php echo $r['value']; ?>" <?php if ($page['tab_id'] == $r['value']) echo 'class="current"'; ?>><?php echo $r['txt']; ?></a>
            <?php } ?>
        </div>
    </div>
    <!--搜索栏-->
    <div class="mt10">
        <?php if ($page['tab_id'] == 0) : //客服部 ?>
            <?php require(dirname(__FILE__) . "/DSS_serviceTap.php"); ?>
        <?php elseif ($page['tab_id'] == 1) : //推广组?>
            <?php require(dirname(__FILE__) . "/DSG_groupTap.php"); ?>
        <?php elseif ($page['tab_id'] == 2) : //个人?>
            <?php require(dirname(__FILE__) . "/DSU_userTap.php"); ?>
        <?php endif ?>
    </div>

</div>

<!--数据 -->
<div class="main mbody">
    <form>
        <?php

        ?>
        <?php if ($page['tab_id'] == 0) : ?>
            <?php require(dirname(__FILE__) . "/DSS_serviceTable.php"); ?>
        <?php elseif ($page['tab_id'] == 1) : ?>
            <?php require(dirname(__FILE__) . "/DSG_groupTable.php"); ?>
        <?php elseif ($page['tab_id'] == 2) : ?>
            <?php require(dirname(__FILE__) . "/DSU_userTable.php"); ?>
        <?php endif ?>
        <?php

        ?>
    </form>
</div>
<script type="application/javascript">

</script>
