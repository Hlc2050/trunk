<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<script>
    function exportList() {
        var data = $("#serchForm").serialize();
        var url = "<?php echo $this->createUrl('domainChannelStats/export');?>";
        window.location.href = url + '?' + data;
    }
</script>
<div class="main mhead">
    <div class="snav">域名管理 » 渠道域名统计</div>
    <div class="mt10">
        <form id="serchForm" action="<?php echo $this->createUrl('domainChannelStats/index') ?>" method="get">
            推广ID:&nbsp;&nbsp;<input type="text" class="ipt" name="promotion_id" value="<?php echo $this->get('promotion_id')?$this->get('promotion_id'):''; ?>">&nbsp;&nbsp;
            推广人员:&nbsp;&nbsp;
            <?php
            $promotionStafflist = PromotionStaff::model()->getPromotionStaffList(0);
            echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($promotionStafflist, 'user_id', 'name'),
                array(
                    'empty' => '推广人员',
                )
            );
            ?>
            &nbsp;&nbsp;
            渠道名称:&nbsp;&nbsp;<input type="text" class="ipt" name="channel_name" value="<?php echo $this->get('channel_name')?$this->get('channel_name'):''; ?>">&nbsp;&nbsp;
            状态:&nbsp;&nbsp;
            <select name="status">
                <option value=" ">请选择</option>
                <option value="0" <?php if($this->get('status') == 0 && $this->get('status') != '') echo 'selected'; ?>>正常</option>
                <option value="1" <?php if($this->get('status') == 1) echo 'selected'; ?>>下线</option>
                <option value="2" <?php if($this->get('status') == 2) echo 'selected'; ?>>暂停</option>
            </select>
            &nbsp;&nbsp;
            <input type="submit" value="搜索" class="but">
            <input type="button" class="but2" value="导出" onclick="exportList()">
        </form>
        <table class="tb">
            <tr>
                <th>推广ID</th>
                <th>渠道名称</th>
                <th>渠道编码</th>
                <th>状态</th>
                <th>推广人员</th>
                <th>总替换域名个数</th>
                <th>今日替换域名个数</th>
                <th>昨日替换域名个数</th>
                <th>掉备案域名</th>
            </tr>
        <?php foreach ($list['listdata']['list'] as $key => $value) { ?>
            <tr>
                <td><?php echo $value['promotion_id'] ?></td>
                <td><?php echo $value['channel_name'] ?></td>
                <td><?php echo $value['channel_code'] ?></td>
                <td><?php echo $value['status'] ?></td>
                <td><?php echo $value['name'] ?></td>
                <td><?php echo $value['all_num'] ?></td>
                <td><?php echo $value['today_num'] ?></td>
                <td><?php echo $value['yseterday_num'] ?></td>
                <td><?php echo $value['detection'] ?></td>
            </tr>
        <?php } ?>
        </table>
    </div>
    <div class="pagebar"><?php echo $list['pageInfo']['pagecode']; ?></div>
</div>

