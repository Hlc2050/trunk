<!-- 渠道效果表 -->
<table id="exportTable" class="tb fixTh" style="width: 100%">
    <thead>
    <tr>
        <th>上线日期</th>
        <th>渠道</th>
        <th>投入金额</th>
        <th>预估发货金额</th>
        <th>ROI</th>
        <th>进粉量</th>
        <th>预估发货量</th>
        <th>进粉成本</th>
        <th>渠道转化</th>
        <th>图文转化</th>
        <th>UV转化</th>
    </tr>
    <tr id="totalInfo">

    </tr>
    </thead>
    <tbody>
    <?php
    $total_money = $total_estimate_money = $total_uv = $total_fans = $total_estimate_count = $total_fans_count = 0;
    foreach ($page['list'] as $r) {

        $ROI = $r['money'] ? round($r['estimate_money'] * 100 / $r['money']) : 0; //ROI
        $fans_cost = $r['fans_count'] ? round($r['money'] / $r['fans_count'], 2) : 0;//进粉成本
        $channel_transform = $r['fans_count'] ? round($r['estimate_count'] * 100 / $r['fans_count'], 1) : 0;//渠道转化  预估发货量/粉丝量
        $uv_transform = $r['fans'] ? round($r['uvs'] * 100 / $r['fans'], 1) : 0;//uv转化 阅读量/粉丝量
        $article_transform = $r['uvs'] ? round($r['fans_count'] * 100 / $r['uvs'], 1) : 0;//图文转化 粉丝量/阅读数
        $total_money += $r['money'];
        $total_estimate_money += $r['estimate_money'];
        $total_estimate_count += $r['estimate_count'];
        $total_fans_count += $r['fans_count'];
        $total_uv += $r['uvs'];
        $total_fans += $r['fans'];
        ?>
        <tr>
            <td><?php echo date('Y-m-d', $r['stat_date']); ?></td>
            <td><?php echo $r['channel_name']; ?></td>
            <td><?php echo $r['money']; ?></td>
            <td><?php echo $r['estimate_money']; ?></td>
            <td><?php echo $ROI . "%"; ?></td>
            <td><?php echo $r['fans_count']; ?></td>
            <td><?php echo round($r['estimate_count'], 2); ?></td>
            <td><?php echo $fans_cost; ?></td>
            <td><?php echo $channel_transform . "%"; ?></td>
            <td><?php echo $article_transform . "%"; ?></td>
            <td><?php echo $uv_transform . "%"; ?></td>

        </tr>
    <?php }
    $total_ROI = round($total_estimate_money * 100 / $total_money);
    $total_fans_cost = round($total_money / $total_fans_count, 2);
    $total_channel_transform = round($total_estimate_count * 100 / $total_fans_count, 1);
    $total_uv_transform = round($total_uv * 100 / $total_fans, 1);
    $total_article_transform = round($total_fans_count * 100 / $total_uv, 1);

    ?>
    </tbody>
</table>

<script type="text/javascript">
    $(function () {
        //将合计填数据加上去
        var html = "<th>-</th><th>合计</th> " +
            "<th><?php echo round($total_money, 2);?></th> <th><?php echo round($total_estimate_money, 2);?></th> " +
            "<th><?php echo $total_ROI . '%';?></th> <th><?php echo $total_fans_count;?></th> <th><?php echo round($total_estimate_count, 2);?></th> <th><?php echo $total_fans_cost;?></th> <th><?php echo $total_channel_transform . '%';?></th> " +
            "<th><?php echo $total_article_transform . '%';?></th> <th><?php echo $total_uv_transform . '%';?></th>";
        $("#totalInfo").html(html);
    })
</script>