<table id="exportTable" class="tb fixTh" style="width: 100%">
    <thead>
    <tr>
        <th>进线日期</th>
        <th>渠道</th>
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

    </tr>
    </thead>
    <tbody>
    <?php
    $total_money = $total_in_count = $total_out_count = $total_uv = $total_ip = $total_deliver_money = 0;
    foreach ($allData['list'] as $d){
        $ROI = $d['money'] ? round($d['sdelivery_money']*100 / $d['money'],2) : 0;
        $in_cost = $d['in_count'] ? round($d['money'] / $d['in_count'],2) : 0;
        $output = $d['in_count'] ? round($d['sdelivery_money'] / $d['in_count'],2) : 0;
        $chnanel_transfrom = $d['in_count'] ? round($d['sout_count']*100 / $d['in_count'],2) : 0;
        $article_transfrom = $d['uv'] ? round($d['in_count']*100 / $d['uv'],2) : 0;
        $total_money += $d['money'];
        $total_in_count += $d['in_count'];
        $total_out_count += $d['sout_count'];
        $total_uv += $d['uv'];
        $total_ip += $d['ip'];
        $total_deliver_money += $d['sdelivery_money'];
        ?>
        <tr>
            <td><?php echo date('Y-m-d',$d['stat_date']);?></td>
            <td><?php echo $d['channel_name'];?></td>
            <td><?php echo round($d['money'], 2);?></td>
            <td><?php echo round($d['sdelivery_money'], 2);?></td>
            <td><?php echo $ROI .'%';?></td>
            <td><?php echo $in_cost;?></td>
            <td><?php echo $output;?></td>
            <td><?php echo $d['sout_count'];?></td>
            <td><?php echo $d['in_count'];?></td>
            <td><?php echo $d['ip'];?></td>
            <td><?php echo $d['uv'];?></td>
            <td><?php echo $chnanel_transfrom .'%';?></td>
            <td><?php echo $article_transfrom .'%';?></td>
        </tr>
    <?php } ?>
    <?php
    $total_roi = $total_money ? round($total_deliver_money*100 / $total_money,2) : 0;
    $total_in_cost = $total_in_count ? round($total_money / $total_in_count,2) : 0;
    $total_output = $total_in_count ? round($total_deliver_money / $total_in_count,2) : 0;
    $total_chnanel_transfrom = $total_in_count ? round($total_out_count*100 / $total_in_count,2) : 0;
    $total_article_transfrom = $total_uv ? round($total_in_count*100 / $total_uv,2) : 0;
    ?>
    </tbody>
</table>
<script type="text/javascript">
    $(function () {
        //将合计填数据加上去
        var html = "<th>-</th><th>合计</th> " +
            "<th><?php echo round($total_money, 2);?></th>" +
            "<th><?php echo round($total_deliver_money, 2);?></th>" +
            "<th><?php echo $total_roi .'%';?></th> " +
            "<th><?php echo $total_in_cost ;?></th> "+
            "<th><?php echo $total_output;?></th>"+
            " <th><?php echo $total_out_count;?></th> "+
            " <th><?php echo $total_in_count;?></th> "+
            "<th><?php echo $total_ip;?></th>"+
            " <th><?php echo $total_uv;?></th> " +
            "<th><?php echo $total_chnanel_transfrom . '%';?></th>"+
            " <th><?php echo $total_article_transfrom . '%';?></th>";
        $("#totalInfo").html(html);
    })
</script>