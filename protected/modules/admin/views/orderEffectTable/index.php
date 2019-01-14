<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">效果统计 » 电销渠道效果</div>
    <div class="mt10">
        <div class="tab_box">
            <?php
            foreach ($allData['params_groups'] as $group) { ?>
                <a href="<?php echo $this->createUrl('orderEffectTable/index').'?group_id='.$group['value'];?>" <?php if($group['value'] == $this->get('group_id')) echo "class='current'" ?>><?php echo $group['txt'];?></a>
            <?php } ?>
            &nbsp; &nbsp; &nbsp;
             &nbsp;
            <?php
            if ($this->check_u_menu(array('auth_tag' => 'orderEffectTable_cache', 'echo' => 0))) {
            ?>&nbsp;&nbsp;&nbsp;&nbsp;
            <input size="20" class="ipt" style="width:120px;font-weight: bold" id="cache_start_date" value="<?php echo $allData['cache_start_date']; ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" type="text">-
            <input size="20" class="ipt" style="width:120px;font-weight: bold" id="cache_end_date" value="<?php echo $allData['cache_end_date'] ? $allData['cache_end_date'] : date("Y-m-d", time()); ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" type="text">&nbsp;
            <span style="color: #069" onclick="$('#cache_start_date').val('<?php echo date("Y-m-d", mktime(0, 0, 0, date('m'), 1, date('Y'))); ?>');$('#cache_end_date').val('<?php echo date("Y-m-d", mktime(0, 0, 0, date('m'), date('t'), date('Y'))); ?>')">本月</span>&nbsp;
            <input style="margin-top: 7px" onclick="cacheData();" class="but" value="点击缓存" type="button">
            <span id="loading" hidden=""><img style="height: 35px" src="/static/img/loading.gif" alt=""></span>
            <span><b><?php echo $allData['date']?></b></span>
            <?php } ?>
        </div>
    </div>
    <!--搜索栏-->
    <div class="mt10">
        <?php if ($this->get('group_id') == 0) : //整体表 ?>
            <?php require(dirname(__FILE__) . "/T_integralTableSearchBar.php"); ?>
        <?php elseif ($this->get('group_id') == 1) : //推广人员效果表?>
            <?php require(dirname(__FILE__) . "/T_pSTableSearchBar.php"); ?>
        <?php elseif ($this->get('group_id') == 2) : //合作商效果表?>
            <?php require(dirname(__FILE__) . "/T_partnerTableSearchBar.php"); ?>
        <?php elseif ($this->get('group_id') == 3) : //渠道效果表?>
            <?php require(dirname(__FILE__) . "/T_channelTableSearchBar.php"); ?>
        <?php elseif ($this->get('group_id') == 4) : //客服部效果表?>
            <?php require(dirname(__FILE__) . "/T_cSTableSearchBar.php"); ?>
        <?php elseif ($this->get('group_id') == 5) : //计费方式效果表?>
            <?php require(dirname(__FILE__) . "/T_cTTableSearchBar.php"); ?>
        <?php elseif ($this->get('group_id') == 6) : //图文效果表?>
            <?php require(dirname(__FILE__) . "/T_articleTableSearchBar.php"); ?>
        <?php endif ?>
    </div>

</div>

<!--数据 -->
<div class="main mbody">
    <form>
        <?php

        ?>
        <?php if ($this->get('group_id') == 0) : ?>
            <?php require(dirname(__FILE__) . "/T_integralTable.php"); ?>
        <?php elseif ($this->get('group_id') == 1) : ?>
            <?php require(dirname(__FILE__) . "/T_pSTable.php"); ?>
        <?php elseif ($this->get('group_id') == 2) : ?>
            <?php require(dirname(__FILE__) . "/T_partnerTable.php"); ?>
        <?php elseif ($this->get('group_id') == 3) : ?>
            <?php require(dirname(__FILE__) . "/T_channelTable.php"); ?>
        <?php elseif ($this->get('group_id') == 4) : ?>
            <?php require(dirname(__FILE__) . "/T_cSTable.php"); ?>
        <?php elseif ($this->get('group_id') == 5) : ?>
            <?php require(dirname(__FILE__) . "/T_cTTable.php"); ?>
        <?php elseif ($this->get('group_id') == 6) : ?>
            <?php require(dirname(__FILE__) . "/T_articleTable.php"); ?>
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
            'url': '/admin/orderEffectTable/cache',
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
