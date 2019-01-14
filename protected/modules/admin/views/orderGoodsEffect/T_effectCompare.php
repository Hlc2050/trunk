<table id="exportTable" class="tb fixTh" style="width: 70%">
    <thead>
    <tr>
        <th>商品名称</th>
        <th>进线量</th>
        <th>发货量</th>
        <th>转化率</th>
    </tr>
    </thead>
    <tbody>
    <?php
        $in_count = $out_count = '';
        foreach($allData['list'] as $value) {
        $order_transform = $value['out_count'] ? round($value['in_count']*100 / $value['out_count'] ) : 0;
        $in_count .= '"'.$value['in_count'].'"'.',';
        $out_count .= '"'.$value['out_count'].'"'.',';
        $package_list .= '"'.$value['package_name'].'"'.',';
        ?>
        <tr>
            <td><?php echo $value['package_name'];?></td>
            <td><?php echo $value['in_count'];?></td>
            <td><?php echo $value['out_count'];?></td>
            <td><?php echo $order_transform.'%';?></td>
        </tr>
    <?php }?>

    </tbody>
</table>
<div id="chart" style="width: 800px;height:450px;margin-top: 20px;text-align: center"></div>
<script>
    var myChart = echarts.init(document.getElementById('chart'));
    var option = {
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            }
        },
        legend: {
            data: ['进线量','发货量']
        },
        toolbox: {
            show: true,
            orient: 'vertical',
            feature: {
                saveAsImage: {show: true}
            }
        },
        calculable: true,
        xAxis: [
            {
                type: 'category',
                axisTick: {show: false},
                data : [<?php echo rtrim($package_list,'');?>]
            }
        ],
        yAxis: [
            {
                type: 'value'
            }
        ],
        series: [
            {
                name: '进线量',
                type: 'bar',
                barGap: 0,
                data:[<?php echo rtrim($in_count,'');?>]
            },
            {
                name: '发货量',
                type: 'bar',
                data: [<?php echo rtrim($out_count,'');?>]
            }

        ]
    };

    myChart.setOption(option);
</script>