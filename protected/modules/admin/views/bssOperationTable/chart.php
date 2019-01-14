PS:整体运营表不需要筛选筛选客服部和商品，客服部运营表必须筛选客服部，商品运营表必须筛选商品表。
<br>

<br><br>
<?php
$xAxis = $series1 = $series2 = '';
$num = count($page['info']);

foreach ($page['info'] as $key => $value) {
    $xAxis .= '"' . $value['stat_date'] . '",';
    $series1 .= '"' . $value['estimate_money'] . '",';
    $series2 .= '"' . $value['ROI'] . '",';
}
$xAxis = rtrim($xAxis, ',');
$series1 = rtrim($series1, ',');
$series2 = rtrim($series2, ',');
?>
<div id="chart" style="width: 1200px;height:450px;"></div>
<script type="text/javascript">
    var myChart = echarts.init(document.getElementById('chart'));

    var option = {
        tooltip: {
            trigger: 'axis'
        },
        toolbox: {
            show: true,
            feature: {
                saveAsImage: {show: true}
            }
        },
        calculable: true,
        legend: {
            data: ['预估发货金额', 'ROI']
        },

        xAxis: [
            {
                type: 'category',
                data: [<?php echo $xAxis?>]
            }
        ],
        yAxis: [
            {
                type: 'value',
                name: '预估发货金额（元）',
                axisLabel: {
                    formatter: '{value}元'
                }
            },
            {
                type: 'value',
                name: 'ROI(%)',
                axisLabel: {
                    formatter: '{value}%'
                }
            }
        ],
        series: [
            {
                name: '预估发货金额',
                type: 'bar',
                data: [<?php echo $series1?>]
            },
            {
                name: 'ROI',
                type: 'line',
                yAxisIndex: 1,
                data: [<?php echo $series2?>]
            }
        ]
    };
    myChart.setOption(option);
</script>