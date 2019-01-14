<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">效果统计 » 渠道效果</div>
    <div class="mt10">
        <div class="tab_box">
            <?php foreach ($page['params_groups'] as $k => $r) { ?>
                <a href="<?php $this->createUrl('effectTable/index'); ?>?group_id=<?php echo $r['value']; ?>" <?php if ($this->get('group_id') == $r['value']) echo 'class="current"'; ?>><?php echo $r['txt']; ?></a>
            <?php } ?>
            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
            <input type="text" size="20" class="ipt" style="width:120px;font-weight: bold"
                   id="cache_start_date" value="<?php echo $page['cache_start_date']; ?>"
                   onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" />-
            <input type="text" size="20" class="ipt" style="width:120px;font-weight: bold"
                   id="cache_end_date"
                   value="<?php echo $page['cache_end_date'] ? $page['cache_end_date'] : date("Y-m-d", time()); ?>"
                   onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" />&nbsp;
            <span style="color: #069"
                  onclick="$('#cache_start_date').val('<?php echo date("Y-m-d", mktime(0, 0, 0, date('m'), 1, date('Y'))); ?>');$('#cache_end_date').val('<?php echo date("Y-m-d", mktime(0, 0, 0, date('m'), date('t'), date('Y'))); ?>')">本月</span>&nbsp;
            <input style="margin-top: 7px" onclick="cacheData();" type="button" class="but" value="点击缓存">
            <span id="loading" hidden><img  style="height: 35px" src="/static/img/loading.gif" alt="" /></span>
            <span><b><?php echo $page['date']?></b></span>

        </div>
    </div>
    <!--搜索栏-->
    <div class="mt10">
        <?php if ($this->get('group_id') == 0) : //整体表 ?>
            <?php require(dirname(__FILE__) . "/Z_integralTableSearchBar.php"); ?>
        <?php elseif ($this->get('group_id') == 1) : //推广人员效果表?>
            <?php require(dirname(__FILE__) . "/Z_pSTableSearchBar.php"); ?>
        <?php elseif ($this->get('group_id') == 2) : //合作商效果表?>
            <?php require(dirname(__FILE__) . "/Z_partnerTableSearchBar.php"); ?>
        <?php elseif ($this->get('group_id') == 3) : //渠道效果表?>
            <?php require(dirname(__FILE__) . "/Z_channelTableSearchBar.php"); ?>
        <?php elseif ($this->get('group_id') == 4) : //客服部效果表?>
            <?php require(dirname(__FILE__) . "/Z_cSTableSearchBar.php"); ?>
        <?php elseif ($this->get('group_id') == 5) : //计费方式效果表?>
            <?php require(dirname(__FILE__) . "/Z_cTTableSearchBar.php"); ?>
        <?php elseif ($this->get('group_id') == 6) : //图文效果表?>
            <?php require(dirname(__FILE__) . "/Z_articleTableSearchBar.php"); ?>
        <?php endif ?>
    </div>

</div>

<!--数据 -->
<div class="main mbody">
    <form>
        <?php

        ?>
        <?php if ($this->get('group_id') == 0) : ?>
            <?php require(dirname(__FILE__) . "/Z_integralTable.php"); ?>
        <?php elseif ($this->get('group_id') == 1) : ?>
            <?php require(dirname(__FILE__) . "/Z_pSTable.php"); ?>
        <?php elseif ($this->get('group_id') == 2) : ?>
            <?php require(dirname(__FILE__) . "/Z_partnerTable.php"); ?>
        <?php elseif ($this->get('group_id') == 3) : ?>
            <?php require(dirname(__FILE__) . "/Z_channelTable.php"); ?>
        <?php elseif ($this->get('group_id') == 4) : ?>
            <?php require(dirname(__FILE__) . "/Z_cSTable.php"); ?>
        <?php elseif ($this->get('group_id') == 5) : ?>
            <?php require(dirname(__FILE__) . "/Z_cTTable.php"); ?>
        <?php elseif ($this->get('group_id') == 6) : ?>
            <?php require(dirname(__FILE__) . "/Z_articleTable.php"); ?>
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
