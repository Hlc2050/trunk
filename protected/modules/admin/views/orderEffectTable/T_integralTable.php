<table id="exportTable" class="tb fixTh" style="width: 100%">
    <thead>
    <tr>
        <th>上线日期</th>
        <th>合作商</th>
        <th>渠道名称</th>
        <th>渠道编码</th>
        <th>微信号ID</th>
        <th>计费方式</th>
        <th>推广人员</th>
        <th>客服部</th>
        <th>商品</th>
        <th>投入金额</th>
        <th>发货金额</th>
        <th>ROI</th>
        <th>进线成本</th>
        <th>均线产出</th>
        <th>发货量</th>
        <th>进线量</th>
        <th>IP</th>
        <th>UV</th>
        <th>渠道转化</th>
        <th>图文转化</th>
    </tr>
    <tr id="totalInfo">
        <th>-</th>
        <th>-</th>
        <th>-</th>
        <th>-</th>
        <th>-</th>
        <th>-</th>
        <th>-</th>
        <th>-</th>
        <th>合计</th>
        <th><?php echo $allData['total_money'];?></th>
        <th><?php echo $allData['total_deliver_money'];?></th>
        <th><?php echo $allData['total_ROI'] .'%';?></th>
        <th><?php echo $allData['total_in_cost'];?></th>
        <th><?php echo $allData['total_output'];?></th>
        <th><?php echo $allData['total_out_count'];?></th>
        <th><?php echo $allData['total_in_count'];?></th>
        <th><?php echo $allData['total_ip'];?></th>
        <th><?php echo $allData['total_uv'];?></th>
        <th><?php echo $allData['total_channel_transform'] .'%';?></th>
        <th><?php echo $allData['total_article_transform'] .'%';?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ( $allData['list'] as $key=>$value) {
        $ROI = $value['money'] ? round($value['sdelivery_money'] * 100 / $value['money'],2) : 0; // ROI
        $in_cost = $value['in_count'] ? round($value['money'] / $value['in_count'],2) : 0; // 进线成本
        $channel_transform = $value['in_count'] ? round($value['sout_count'] * 100 / $value['in_count'],2) : 0; // 渠道转化
        $output = $value['in_count'] ? round($value['sdelivery_money'] / $value['in_count'],2) : 0; // 均线产出
        $article_transform = $value['uv'] ? round($value['in_count'] * 100 / $value['uv'],2) : 0; // 图文转化
        ?>
        <tr>
            <td style="width:80px;"><?php echo date('Y-m-d',$value['stat_date']);?></td>
            <td style="width:70px;"><span title="<?php echo $value['partner_name'];?>"><?php echo $value['partner_name'];?></span></td>
            <td style="width:80px;"><?php echo $value['channel_name'];?></td>
            <td style="width:80px;"><?php echo $value['channel_code'];?></td>
            <td><?php echo $value['wechat_id'];?></td>
            <td><?php echo vars::get_field_str('charging_type', $value['charging_type']);?></td>
            <td><?php echo $value['csname_true']; ?></td>
            <td><?php echo $value['cname']; ?> </td>
            <td><?php echo $value['goods_name']; ?></td>
            <td><?php echo $value['money']; ?></td>
            <td><?php echo $value['sdelivery_money']; ?></td>
            <td><?php echo $ROI .'%'?></td>
            <td><?php echo $in_cost;?></td>
            <td><?php echo $output;?></td>
            <td><?php echo $value['sout_count'];?></td>
            <td><?php echo $value['in_count'];?></td>
            <td><?php echo $value['ip'];?></td>
            <td><?php echo $value['uv'];?></td>
            <td><?php echo $channel_transform .'%'?></td>
            <td><?php echo $article_transform .'%'?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
<div class="pagebar"><?php echo $allData['pageInfo']['pagecode'];?></div>