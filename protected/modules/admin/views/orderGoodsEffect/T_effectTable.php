<table id="exportTable" class="tb fixTh" style="width: 100%">
    <thead>
    <tr>
        <th>日期</th>
        <th>下单商品</th>
        <th>进线量</th>
        <th>发货量</th>
        <th>发货金额</th>
        <th>订单转化</th>
    </tr>
    <tr id="totalInfo">
        <th>-</th>
        <th>合计</th>
        <th><?php echo $allData['total_in_count'];?></th>
        <th><?php echo $allData['total_out_count'];?></th>
        <th><?php echo $allData['total_delivery_money'];?></th>
        <th><?php echo $allData['total_order_transform'] .'%';?></th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($allData['list'] as $k=>$v) {
            $order_transfrom = $v['in_count'] ? round($v['out_count']*100 / $v['in_count'],2) : 0;
            ?>
        <tr>
            <td><?php echo date('Y-m-d',$v['stat_date']); ?></td>
            <td><?php echo $v['package_name'];?></td>
            <td><?php echo $v['in_count'];?></td>
            <td><?php echo $v['out_count'];?></td>
            <td><?php echo round($v['delivery_money'],2);?></td>
            <td><?php echo $order_transfrom .'%';?></td>
        </tr>
        <?php }?>
    </tbody>
</table>
<div class="pagebar"><?php echo $allData['pagearr']['pagecode'];?></div>