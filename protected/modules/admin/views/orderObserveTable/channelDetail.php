<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">进线渠道观察表</div>

    <div class="mt10">
        <span style="font-size: large; font-weight:bold;color:dimgrey">渠道编码：<?php echo Channel::model()->findByPk($this->get('channel_id'))->channel_code; ?></span>&nbsp;&nbsp;&nbsp;
        <span style="font-size: large; font-weight:bold;color:dimgrey">客服部：<?php echo CustomerServiceManage::model()->findByPk($this->get('csid'))->cname; ?></span>
    </div>
    <div class="mt10 clearfix">
        <div class="l">
            <input style="font-size: medium" type="button" class="but2" value="返回"
                   onclick="window.location='<?php echo $this->get('url') ? $this->get('url') : $this->createUrl('orderObserveTable/index'); ?>'"/>&nbsp;&nbsp;&nbsp;
            <input style="font-size: medium" type="button" class="but2" value="切换展示商品详情"
                   onclick="window.location='<?php echo $this->createUrl('orderObserveTable/packageDetail?channel_id=' . $this->get('channel_id') . '&csid=' . $this->get('csid') . '&url=' . $this->get('url')); ?>'"/>
        </div>
        <div class="r">
        </div>
    </div>
</div>
<div class="main mbody">
    <div style="width: 25%;float: left">
        <table class="tb fixTh">
            <thead>
            <tr>
                <th align='center'>时间段</th>
                <th align='center'>进线量</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>合计</td>
                <td><?php echo  $page['total_count']?></td>
            </tr>
            <?php for ($i = 0; $i < 24; $i++) { ?>
                <tr>
                    <td><?php echo $i . ':00~' . ($i + 1) . ":00"; ?></td>
                    <td><?php echo $page['info'][$i]['in_count']?></td>
                </tr>
            <?php } ?>

            </tbody>
        </table>

    </div>
    <div style="float:left;width:75%;">
        <?php
        $xAxis = $series1 = '';
        $num = count($page['info']);

        foreach ($page['info'] as $key => $value) {
            $xAxis .= '"' . $key.':00~'.($key+1) . ':00",';
            $series1 .= '"' . $value['in_count'] . '",';
        }
        $xAxis = rtrim($xAxis, ',');
        $series1 = rtrim($series1, ',');
        ?>
        <br/><br/>
        <div id="chart" style="width: 100%;height:550px;"></div>
        <script type="text/javascript">
            var myChart = echarts.init(document.getElementById('chart'));

            var option = {
                title: {
                    text: '渠道进线观察表',
                    subtext: '<?php echo date('Y-m-d',time())?>'

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
                    data: ['进线量']
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
                        name: '进线量',
                        axisLabel: {
                            formatter: '{value}'
                        }
                    }

                ],
                series: [
                    {
                        name: '进线量',
                        type: 'line',
                        data: [<?php echo $series1?>]
                    },

                ]
            };
            myChart.setOption(option);
        </script>
    </div>

    <div class="clear"></div>
</div>
