<div id="chart" style="width: 1200px;height:450px;"></div>
<?php
$date_list = $in_count = $out_count = $order_transform = $item='';
if($allData['type'] == 1){
    $type = "客服部";
    $text = $allData['text'];
    $name = "cname";
}elseif ($allData['type'] == 2){
    $type = "下单商品";
    $text = $allData['text'];
    $name = "package_name";
}elseif ($allData['type'] == null){
    $type = "";
}
foreach ($allData['list'] as $v)  {
    $date_list .= '"'.$v[$name].'"'.',';
    $in_count .= $v['in_count'].',';
    $out_count .= $v['out_count'].',';
    $order_transform .= $v['order_transform'].',';
}
$date_list = rtrim($date_list,',');
$in_count = rtrim($in_count,',');
$out_count = rtrim($out_count,',');
$order_transform = rtrim($order_transform,',');
?>

<script>
    var myChart = echarts.init(document.getElementById('chart'));
    var option = {
        title : {
            text: '<?php echo $text;?>',
//            subtext: '<?php //echo $type; ?>//',
            x:'50px',
//            y:'362px',
            subtextStyle:{ color: 'black',},
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            }
        },
        legend: {
            data: ['进线量','发货量','转化率']
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
                data : [<?php echo $date_list;?>]
            }
        ],
        yAxis: [
            {
                type: 'value',
                name: '进线数(发货量)',
                axisLabel: {
                    formatter: '{value}'
                }
            },
            {
                type: 'value',
                name: '转化率',
                axisLabel: {
                    formatter: '{value} %'
                }
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
            },
            {
                name:'转化率',
                type:'line',
                yAxisIndex: 1,
                data:[<?php echo $order_transform;?>]
            }
        ]
    };

    myChart.setOption(option);
</script>