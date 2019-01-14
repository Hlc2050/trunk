<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">计划管理 » 每周进粉计划</div>
    <div class="mt10">
        <div class="tab_box">
            <?php foreach ($page['params_groups'] as $k => $r) { ?>
                <a href="<?php $this->createUrl('effectTable/index'); ?>?tab_id=<?php echo $r['value']; ?>" <?php if ($page['tab_id'] == $r['value']) echo 'class="current"'; ?>><?php echo $r['txt']; ?></a>
            <?php } ?>
        </div>
    </div>
    <!--搜索栏-->
    <div class="mt10">
        <?php if ($page['tab_id'] == 0) : //待审核计划(个人) ?>
            <?php require(dirname(__FILE__) . "/PWU_unAuditTap.php"); ?>
        <?php elseif ($page['tab_id'] == 1) : //个人计划?>
            <?php require(dirname(__FILE__) . "/PWU_userTap.php"); ?>
        <?php elseif ($page['tab_id'] == 2) : //待审核计划(组)?>
            <?php require(dirname(__FILE__) . "/PWU_unAuditGroupTap.php"); ?>
        <?php elseif ($page['tab_id'] == 3) : //组计划?>
            <?php require(dirname(__FILE__) . "/PWU_groupTap.php"); ?>
        <?php elseif ($page['tab_id'] == 4) : //提交记录?>
            <?php require(dirname(__FILE__) . "/PWL_planLogTap.php"); ?>
        <?php endif ?>
    </div>

</div>

<!--数据 -->
<div class="main mbody">
    <form>
        <?php

        ?>
        <?php if ($page['tab_id'] == 0) : //待审核计划(个人) ?>
            <?php require(dirname(__FILE__) . "/PWU_unAuditTable.php"); ?>
        <?php elseif ($page['tab_id'] == 1) : //个人计划 ?>
            <?php require(dirname(__FILE__) . "/PWU_userTable.php"); ?>
        <?php elseif ($page['tab_id'] == 2) : //待审核计划(组) ?>
            <?php require(dirname(__FILE__) . "/PWU_unAuditGroupTable.php"); ?>
        <?php elseif ($page['tab_id'] == 3) : //组计划 ?>
            <?php require(dirname(__FILE__) . "/PWU_groupTable.php"); ?>
        <?php elseif ($page['tab_id'] == 4) : //提交记录?>
            <?php require(dirname(__FILE__) . "/PWL_planLog.php"); ?>
        <?php endif ?>
        <?php

        ?>
    </form>
</div>
<script type="application/javascript">
    function cacheData() {
        jQuery.ajax({
            async: true,
            beforeSend: function () {
                $("#loading").show();
            },
            complete: function () {
                $("#loading").hide();
            },
            'type': 'POST',
            'url': '/admin/effectTable/cache',
            'data': {'cache_start_date': $("#cache_start_date").val(), 'cache_end_date': $("#cache_end_date").val()},
            'cache': false,
            'success': function (result) {
                if(result=="noData"){
                    alert("未选择缓存区间！");
                }else {
                    alert("缓存成功！");
                    console.log(result);
                }


                window.location.reload();//刷新当前页面.
            }
        });
        return false;
    }
</script>
