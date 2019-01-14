<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">客服数据监测 » 数据推送</div>
    <div class="mt10">
        <form action="<?php echo $this->createUrl('/admin/dataPush/push'); ?>">
            <?php if ($page['update_all'] == 1) {?>
                <input type="submit" class="disabled" value="一键推送已更新数据"  disabled>
            <?php } else {?>
                <input type="submit" class="but" value="一键推送已更新数据">
            <?php }?>
        </form>
    </div>
</div>
<div class="main mbody">
    <form>
        <table class="tb fixTh" style="width: 800px">
            <thead>
            <tr>
                <th>客服部</th>
                <th>数据状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <?php
            foreach ($page['service'] as $key=>$val) {
            ?>
                <tr>
                    <td><?php echo $val['cname'];?></td>
                    <td><?php echo $val['is_update']==1 ?'今日已更新':'今日未更新';?></td>
                    <td>
                        <?php if ($val['is_push'] == 0 && $val['is_update']== 1) { ?>
                            <input type="button" class="but" value="推送" onclick="location.href='<?php echo $this->createUrl('/admin/dataPush/push?service_id='.$key)?>'">
                        <?php }?>
                        <?php if ($val['is_push'] == 1 && $val['is_update']== 1)  { echo '已推送'; }?>
                        <?php if ($val['is_update'] == 0) { echo '未更新'; }?>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
        <div class="clear"></div>
    </form>
</div>


