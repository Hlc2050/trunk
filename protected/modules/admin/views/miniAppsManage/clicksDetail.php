<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">小程序列表-客服访问统计</div>

    <div class="mt10">
        <form action="<?php echo $this->createUrl('miniAppsManage/clicksStatTable'); ?>">
            <span style="font-size: large; font-weight:bold;color:dimgrey">小程序名：<?php echo $page['appsInfo']['app_name']; ?></span>&nbsp;&nbsp;&nbsp;
            <input name="id" value="<?php echo $page['appsInfo']['id']; ?>" hidden>
            查询日期：
            <input type="text" class="ipt" style="width:120px;font-size: 15px;" name="start_date"
                   value="<?php echo $page['start_date']; ?>" placeholder="起始日期"
                   onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/> 一
            <input type="text" class="ipt" style="width:120px;font-size: 15px;" name="end_date"
                   value="<?php echo $page['end_date']; ?>" placeholder="结束日期"
                   onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>&nbsp;
            <input type="submit" class="but" value="查询">&nbsp;&nbsp;&nbsp;
            <input style="font-size: small" type="button" class="but2" value="返回"
                   onclick="window.location='<?php echo $this->get('url') ? $this->get('url') : $this->createUrl('miniAppsManage/index'); ?>'"/>&nbsp;&nbsp;&nbsp;
        </form>
    </div>
    <div class="mt10 clearfix">
        <div class="l">
        </div>
        <div class="r">
        </div>
    </div>
</div>
<div class="main mbody">
    <div id="chart" style="width: 100%;height:550px;"></div>
    <?php
    $xAxis = $series1 = '';
    $num = count($page['data']);

    foreach ($page['data'] as  $value) {
        $xAxis .= '"' . date('m-d',$value['date']). '",';
        $series1 .= '"' . $value['click_num'] . '",';
    }
    $xAxis = rtrim($xAxis, ',');
    $series1 = rtrim($series1, ',');
    ?>
    <br/><br/>

    <script type="text/javascript">
        var myChart = echarts.init(document.getElementById('chart'));

        var option = {
            title: {
                text: '客服访问统计',

            },
            tooltip: {
                trigger: 'axis'
            },
            toolbox: {
                show: true,
                feature: {
                    dataZoom: {
                        yAxisIndex: 'none'
                    },
                    saveAsImage: {show: true}
                }
            },
            calculable: true,
            legend: {
                data: ['访问量']
            },

            xAxis: [
                {
                    type: 'category',
                    splitLine:{show: false},//去除网格线

                    name:'时间段',
                    data: [<?php echo $xAxis?>]
                }
            ],
            yAxis: [
                {
                    type: 'value',
                    name: '访问量',
                    axisLabel: {
                        formatter: '{value}'
                    }
                }

            ],
            series: [
                {
                    name: '访问量',
                    type: 'line',
                    data: [<?php echo $series1?>]
                },

            ]
        };
        myChart.setOption(option);
    </script>

    <div class="clear"></div>
</div>



