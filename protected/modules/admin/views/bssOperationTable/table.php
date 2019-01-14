<!-- 图文效果表 -->
<table id="exportTable" class="tb fixTh" style="width: 100%">
    <thead>
    <tr>
        <th>日期</th>
        <th>进粉量</th>
        <th>预估发货量</th>
        <th>预估发货金额</th>
        <th>投入金额</th>
        <th>ROI</th>
        <th>订单转化率</th>
        <th>客单价</th>
        <th>进粉成本</th>
        <th>均粉产出</th>
        <th>下单量</th>
        <th>下单金额</th>
    </tr>
    <tr id="totalInfo"></tr>
    </thead>
    <tbody>
    <?php
    $total_money = $total_estimate_money = $total_estimate_count = $total_order_money = $total_order_count = $total_fans_count = 0;

    foreach ($page['info'] as $key => $value) { ?>
        <tr>
            <td><?php echo $value['stat_date']; ?></td>
            <td><?php echo $value['fans_count']; ?></td>
            <td><?php echo round($value['estimate_count']); ?></td>
            <td><?php echo round($value['estimate_money']); ?></td>
            <td><?php echo round($value['money']); ?></td>
            <td><?php echo $value['ROI'] . "%"; ?></td>
            <td><?php echo $value['order_cor'] . "%"; ?></td>
            <td><?php echo $value['unit']; ?></td>
            <td><?php echo $value['fans_cost']; ?></td>
            <td><?php echo $value['fans_avg']; ?></td>
            <td><?php echo $value['order_count']; ?></td>
            <td><?php echo $value['order_money']; ?></td>
        </tr>
        <?php
        $total_fans_count += $value['fans_count'];
        $total_estimate_count += $value['estimate_count'];
        $total_estimate_money += $value['estimate_money'];
        $total_money += $value['money'];
        $total_order_count += $value['order_count'];
        $total_order_money += $value['order_money'];
    }
    $total_ROI = $total_money==0?0:round($total_estimate_money * 100 / $total_money);
    $total_fans_cost = $total_fans_count==0?0:round($total_money / $total_fans_count);
    $total_order_cor = $total_fans_count==0?0:round($total_estimate_count * 100 / $total_fans_count,1);;
    $total_unit = $total_estimate_count==0?0:round($total_estimate_money / $total_estimate_count);
    $total_fans_avg = $total_fans_count==0?0:round($total_estimate_money / $total_fans_count);
    ?>
    </tbody>
</table>

<script type="text/javascript">
    $(function () {
        //将合计填数据加上去
        var html = "<th>合计</th> " +
            "<th><?php echo $total_fans_count;?></th><th><?php echo round($total_estimate_count);?></th> <th><?php echo round($total_estimate_money);?></th>" +
            "<th><?php echo round($total_money);?></th><th><?php echo $total_ROI . "%";?></th> <th><?php echo $total_order_cor . "%";?></th>" +
            "<th><?php echo $total_unit;?></th><th><?php echo $total_fans_cost;?></th> <th><?php echo $total_fans_avg;?></th>" +
            "<th><?php echo $total_order_count;?></th><th><?php echo $total_order_money;?></th>"
        $("#totalInfo").html(html);
    })
</script>
