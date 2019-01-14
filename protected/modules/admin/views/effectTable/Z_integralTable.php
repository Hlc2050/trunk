<!-- 整体效果表 -->
<table id="exportTable" class="tb fixTh" style="width: 100%">
    <thead>
    <tr>
        <th>上线日期</th>
        <th>合作商</th>
        <th>渠道名称</th>
        <th>微信号ID</th>
        <th>业务类型</th>
        <th>计费方式</th>
        <th>推广人员</th>
        <th>客服部</th>
        <th>商品</th>
        <th>投入金额</th>
        <th>预估发货金额</th>
        <th>ROI</th>
        <th>进粉量</th>
        <th>预估发货量</th>
        <th>进粉成本</th>
        <th>渠道转化</th>
        <th>图文转化</th>
        <th>UV转化</th>
        <?php if ($this->get('is_pCount') == 1) echo "<th>同时推广</th>"; ?>
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
        <th><?php echo round($page['total_money'], 2); ?></th>
        <th><?php echo round($page['total_estimate_money'], 2); ?></th>
        <th><?php echo $page['total_ROI'] . '%'; ?></th>
        <th><?php echo $page['total_fans_count']; ?></th>
        <th><?php echo round($page['total_estimate_count'], 2); ?></th>
        <th><?php echo $page['total_fans_cost']; ?></th>
        <th><?php echo $page['total_channel_transform'] . '%'; ?></th>
        <th><?php echo $page['total_article_transform'] . '%'; ?></th>
        <th><?php echo $page['total_uv_transform'] . '%'; ?></th>
        <?php if ($this->get('is_pCount') == 1) echo " <th>-</th>"; ?>

    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($page['list'] as $k => $r) {

        $ROI = $r['money'] ? round($r['estimate_money'] * 100 / $r['money']) : 0; //ROI
        $fans_cost = $r['fans_count'] ? round($r['money'] / $r['fans_count'], 2) : 0;//进粉成本
        $channel_transform = $r['fans_count'] ? round($r['estimate_count'] * 100 / $r['fans_count'], 1) : 0;//渠道转化  预估发货量/粉丝量
        $uv_transform = $r['fans'] != 0 ? round($r['uvs'] * 100 / $r['fans'], 1) : 0;//uv转化 阅读量/粉丝量
        $article_transform = $r['uvs'] != 0 ? round($r['fans_count'] * 100 / $r['uvs'], 1) : 0;//图文转化 粉丝量/阅读数
        //微信号是否同时推广
        if ($this->get('is_pCount') == 1) {
            $promotionCount = StatCostDetail::model()->count("stat_date=" . $r['stat_date'] . " and weixin_id=" . $r['weixin_id'] . " and channel_id!=" . $r['channel_id']);
            $promotionCount = !$promotionCount ? '无' : $promotionCount + 1;
        }
        ?>
        <tr>
            <td style="width:80px;"><?php echo date('Y-m-d', $r['stat_date']); ?></td>
            <td style="width:70px;"><span
                    title="<?php echo $r['partner_name']; ?>"><?php echo helper::cut_str($r['partner_name'], 9); ?></span>
            </td>
            <td style="width:80px;"><?php echo $r['channel_name']; ?></td>
            <td><?php echo $r['wechat_id']; ?></td>
            <td><?php echo $r['bname']; ?></td>
            <td><?php echo vars::get_field_str('charging_type', $r['charging_type']); ?></td>
            <td><?php echo $r['csname_true']; ?></td>
            <td><?php echo $r['cname']; ?></td>
            <td><?php echo $r['goods_name']; ?></td>
            <td><?php echo $r['money']; ?></td>
            <td><?php echo $r['estimate_money']; ?></td>
            <td><?php echo $ROI . "%"; ?></td>
            <td><?php echo $r['fans_count']; ?></td>
            <td><?php echo $r['estimate_count']; ?></td>
            <td><?php echo $fans_cost; ?></td>
            <td><?php echo $channel_transform . "%"; ?></td>
            <td><?php echo $article_transform . "%"; ?></td>
            <td><?php echo $uv_transform . "%"; ?></td>
            <?php if ($this->get('is_pCount') == 1) echo "<td>$promotionCount</td>"; ?>
        </tr>
    <?php }
    ?>
    </tbody>
</table>
<div class="pagebar"><?php echo $page['pageInfo']['pagecode']; ?></div>
<div class="clear"></div>

