<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">进线下单商品观察表</div>

    <div class="mt10">
        <span style="font-size: large; font-weight:bold;color:dimgrey">渠道编码：<?php echo Channel::model()->findByPk($this->get('channel_id'))->channel_code; ?></span>&nbsp;&nbsp;&nbsp;
        <span style="font-size: large; font-weight:bold;color:dimgrey">客服部：<?php echo CustomerServiceManage::model()->findByPk($this->get('csid'))->cname; ?></span>
    </div>
    <div class="mt10 clearfix">
        <div class="l">
            <input style="font-size: medium" type="button" class="but2" value="返回"
                   onclick="window.location='<?php echo $this->get('url') ? $this->get('url') : $this->createUrl('orderObserveTable/index'); ?>'"/>&nbsp;&nbsp;&nbsp;
            <input style="font-size: medium" type="button" class="but2" value="切换展示渠道详情"
                   onclick="window.location='<?php echo $this->createUrl('orderObserveTable/channelDetail?channel_id=' . $this->get('channel_id') . '&csid=' . $this->get('csid') . '&url=' . $this->get('url')); ?>'"/>
        </div>
        <div class="r">
        </div>
    </div>
</div>
<div class="main mbody">
    <div style="width: 30%;float: left">
        <table class="tb fixTh">
            <thead>
            <tr>
                <th align='center'>时间段</th>
                <?php
                $pak=array();
                $count=count($page['packages']);
                foreach ($page['packages'] as $v) {
                    $pak[$v]['name']=PackageManage::model()->findByPk($v)->name;
                    ?>
                    <th align='center'><?php echo $pak[$v]['name'] ?></th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>合计</td>
                <?php  foreach ($page['packages'] as $v) { ?>
                <td><?php echo  $page['total_count'][$v];?></td>
                <?php } ?>
            </tr>
            <?php
            for ($i = 0; $i < 24; $i++) { ?>
                <tr>
                    <td><?php echo $i . ':00~' . ($i + 1) . ":00"; ?></td>
                    <?php
                    foreach ($page['packages'] as $v) {
                        if($i>0)$pak[$v]['data'].=',';
                        $pak[$v]['data'] .= $page['info'][$i][$v]['in_count'];
                        echo '<td>'.$page['info'][$i][$v]['in_count'].'</td>';
                    } ?>
                </tr>
            <?php } ?>

            </tbody>
        </table>

    </div>
    <div style="float:left;width:70%;">
        <?php
        $strs=$xAxis = $series1 = '';
        $num = count($page['info']);
        foreach ($page['packages'] as $k=>$v){
            if($k>0)$strs.=',';
            $strs .= "'" .  $pak[$v]['name'] . "'";
        }
        foreach ($page['info'] as $key => $value) {
            $xAxis .= '"' . $key.':00~'.($key+1) . ':00",';
        }
        $symbol=['circle', 'triangle', 'diamond', 'rect', 'roundRect', 'pin', 'arrow'];
        $xAxis = rtrim($xAxis, ',');
        ?>
        <br/><br/>
        <div id="chart" style="width: 100%;height:550px;"></div>
        <script type="text/javascript">
            var myChart = echarts.init(document.getElementById('chart'));
            option = {
                title: {
                    text: '下单商品进线观察表',
                    subtext: '<?php echo date('Y-m-d',time())?>',
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data:[<?php echo $strs?>],
                },
                toolbox: {
                    show: true,
                    feature: {
                        dataZoom: {
                            yAxisIndex: 'none'
                        },
                        saveAsImage: {}
                    }
                },
                xAxis:  {
                    type: 'category',
                    splitLine:{show: false},//去除网格线
                    name:'时间段',
                    data: [<?php echo $xAxis?>]
                },
                yAxis: {
                    type: 'value',
                    name: '进线量',

                    axisLabel: {
                        formatter: '{value}'
                    }
                },
                series: [
                <?php foreach ($page['packages'] as $k=>$v){?>
                    {
                        name:'<?php echo  $pak[$v]['name'];?>',
                        type:'line',
                        data:[<?php echo  $pak[$v]['data']?>],
                        symbol:'<?php echo $symbol[$k]?>',
                        symbolSize:12,
                        markPoint: {
                            data: [
                                {type: 'max', name: '最大值'},
                                {type: 'min', name: '最小值'}
                            ]
                        }
                    },
                <?php }?>

                ]
            };

            myChart.setOption(option);
        </script>
    </div>

    <div class="clear"></div>
</div>
