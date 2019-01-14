<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">效果统计 » 下单商品统计</div>
    <div class="mt10">
        <div class="tab_box">
            <?php foreach($allData['params_groups'] as $value) {?>
                <a href="?group_id=<?php echo $value['value'] ?>" <?php if($value['value'] == $this->get('group_id')) echo "class='current'";?>><?php echo $value['txt'] ?></a>
            <?php } ?>
        </div>
    </div>
    <!--搜索栏-->
    <div class="mt10">
        <?php
        if ($this->get('group_id') == 0) : ?>
            <?php require(dirname(__FILE__) . "/T_effectTableSearchBar.php"); ?>
        <?php elseif ($this->get('group_id') == 1) : // 统计图?>
            <?php require(dirname(__FILE__) . "/T_effectChartSearchBar.php"); ?>
        <?php elseif ($this->get('group_id') == 2) : // 对比图?>
            <?php require(dirname(__FILE__) . "/T_effectCompareSearchBar.php"); ?>
        <?php endif ?>
    </div>
</div>
<!--数据 -->
<div class="main mbody">
    <form>
        <?php if ($this->get('group_id') == 0) : ?>
            <?php require(dirname(__FILE__) . "/T_effectTable.php"); ?>
        <?php elseif ($this->get('group_id') == 1) : ?>
            <?php require(dirname(__FILE__) . "/T_effectChart.php"); ?>
        <?php elseif ($this->get('group_id') == 2) : ?>
            <?php require(dirname(__FILE__) . "/T_effectCompare.php"); ?>
        <?php endif ?>
    </form>
</div>

