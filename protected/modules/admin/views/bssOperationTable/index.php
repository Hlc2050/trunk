<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
    <div class="main mhead">
        <div class="snav">效果统计 » 业务运营表</div>
        <div class="mt10">
            <!--搜索栏-->
            <div class="mt10">
                <?php require(dirname(__FILE__) . "/searchBar.php"); ?>
            </div>
            <?php $params="&csid=".$this->get('csid')."&goods_id=".$this->get('goods_id')."&pgid=".$this->get('pgid')."&tg_id=".$this->get('tg_id')."&bsid=".$this->get('bsid')."&chg_id=".$this->get('chg_id')."&start_date=".$this->get('start_date')."&end_date=".$this->get('end_date')?>
            <div class="tab_box">
                <a href="<?php $this->createUrl('effectTable/index'); ?>?group_id=0<?php echo $params;?>" <?php if ($this->get('group_id') == 0) echo 'class="current"'; ?>>
                    业务运营表 </a>
                <a href="<?php $this->createUrl('effectTable/index'); ?>?group_id=1<?php echo $params;?>" <?php if ($this->get('group_id') == 1) echo 'class="current"'; ?>>
                    业务运营图 </a>
            </div>
        </div>
    </div>
    <!--数据 -->
    <div class="main mbody">
        <?php if ($this->get('group_id') == 0) : ?>
            <?php require(dirname(__FILE__) . "/table.php"); ?>
        <?php elseif ($this->get('group_id') == 1) : ?>
            <?php require(dirname(__FILE__) . "/chart.php"); ?>
        <?php endif ?>
      
    </div>
