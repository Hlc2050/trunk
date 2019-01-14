<div id="chart" style="width: 1200px;height:450px;"></div>

    <div class="freezing" style="overflow:scroll;width: 100%;">
<div>
    <b style="font-size: 15px;">汇总数据:</b>
    <a href="#" style="margin-left: 1100px;" onclick="exportTable('汇总数据','export_all')">导出Excel文件</a>
    <table class="tb" style="width: 100%;margin-top: 10px;" id="export_all" >
        <tbody>
        <tr>
            <th colspan="2">客服部/日期</th>
            <?php for ($i=0;$i<=$page['date_difference'];$i++){ ?>
                <th ><?php echo date('m-d',$page['start_date']+$i*86400); ?></th>
            <?php } ?>
        </tr>
        <?php
        $predict = $this->get('predict');
        $promotion_group1 = $this->get('promotion_group1');
        $promotion_group2 = $this->get('promotion_group2');
        $groupName1 = AdminGroup::model()->getGroupName($promotion_group1);
        $groupName2 = AdminGroup::model()->getGroupName($promotion_group2);
        $cs_id = $this->get('csid');
        $line_data1 = '';
        $line_data2 = '';
        ?>

        <?php if($cs_id){ ?>
            <?php foreach ($data as $key=>$value){ ?>
                <tr>
                    <th><?php  echo CustomerServiceManage::model()->getCSName($cs_id);; ?></th>
                    <th>微信号个数：<br>进粉：<br>产值：</th>
                    <?php for ($i=0;$i<=$page['date_difference'];$i++){ ?>
                        <?php
                        $str1 = $value[$cs_id][$promotion_group1][$page['start_date']+$i*86400];
                        $str2 = $value[$cs_id][$promotion_group2][$page['start_date']+$i*86400];
                        ?>
                        <?php if($str1 || $str2){?>
                            <th>
                                <?php echo $str1['weChat_num']+$str2['weChat_num']."<br>"?>
                                <?php echo $str1['fans_counts']+$str2['fans_counts']."<br>"?>
                                <?php echo $str1['outputs']+$str2['outputs']; ?></th>
                            <?php  $line1 =$str1[$predict];
                            ?>
                            <?php  $line2 =$str2[$predict];
                            ?>
                            <?php  $line3 =($line1/($line1+$line2))*100; $line = round($line3,2);
                            $line_data1 .= $line1.',';
                            $line_data2 .= $line2.',';
                            ?>
                        <?php }else{ ?>
                            <th><?php echo '0'."<br>".'0'."<br>".'0'; ?></th>
                            <?php  $line_data1.="0,"; ?>
                            <?php  $line_data2.="0,"; ?>
                        <?php } ?>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
        <?php } ?>
    </table>
</div>
</div>


<?php if($promotion_group1){ ?>
        <div class="freezing" style="overflow:scroll;width: 100%;">
<div style="margin-top: 10px;">
    <b style="font-size: 15px;"><?php echo $groupName1; ?>:</b>
    <a href="#" style="margin-left: 1100px;"  onclick="exportTable(<?php echo "'".$groupName1."'"; ?>,'data_first')">导出Excel文件</a>
    <table class="tb" style="width: 100%;margin-top: 10px;" id="data_first">
        <tr>
            <th colspan="2">客服部/日期</th>
            <?php for ($i=0;$i<=$page['date_difference'];$i++){ ?>
                <th><?php echo date('m-d',$page['start_date']+$i*86400); ?></th>
            <?php } ?>
        </tr>
        <?php if($cs_id){ ?>
            <?php foreach ($data as $value){ ?>
                <tr>
                    <th><?php  echo CustomerServiceManage::model()->getCSName($cs_id); ?></th>
                    <th>微信号个数：<br>进粉：<br>产值：</th>
                    <?php for ($i=0;$i<=$page['date_difference'];$i++){ ?>
                        <?php
                        $str3 = $value[$cs_id][$promotion_group1][$page['start_date']+$i*86400];
                        ?>
                        <?php if($str3){ ?>
                            <th><?php echo $str3['weChat_num']."<br>"?><?php echo $str3['fans_counts']."<br>"?><?php echo $str3['outputs']; ?></th>
                        <?php }else{ ?>
                            <th><?php echo '0'."<br>".'0'."<br>".'0'; ?></th>
                        <?php } ?>
                    <?php } ?>
                </tr>
            <?php } ?>
    <?php } ?>

    </table>
</div>
    <?php } ?>
</div>

    <?php if($promotion_group2){ ?>
        <div class="freezing" style="overflow:scroll;width: 100%;">
        <div style="margin-top: 10px;">
            <b style="font-size: 15px;"><?php echo $groupName2; ?>:</b>
            <a href="#" style="margin-left: 1100px;" onclick="exportTable(<?php echo "'".$groupName2."'"; ?>,'data_second')">导出Excel文件</a>
            <table class="tb" style="width: 100%;margin-top: 10px;" id="data_second">
                <tr>
                    <th colspan="2">客服部/日期</th>
                    <?php for ($i=0;$i<=$page['date_difference'];$i++){ ?>
                        <th><?php echo date('m-d',$page['start_date']+$i*86400); ?></th>
                    <?php } ?>
                </tr>

                <?php if($cs_id){ ?>
                    <?php foreach ($data as $key=>$value){ ?>
                        <tr>
                            <th><?php  echo CustomerServiceManage::model()->getCSName($cs_id);; ?></th>
                            <th>微信号个数：<br>进粉：<br>产值：</th>
                            <?php for ($i=0;$i<=$page['date_difference'];$i++){ ?>
                                <?php
                                $str4 = $value[$cs_id][$promotion_group2][$page['start_date']+$i*86400];
                                ?>
                                <?php if($str4){?>
                                    <th><?php echo $str4['weChat_num']."<br>"?><?php echo $str4['fans_counts']."<br>"?><?php echo $str4['outputs']; ?></th>
                                <?php }else{ ?>
                                    <th><?php echo '0'."<br>".'0'."<br>".'0'; ?></th>
                                <?php } ?>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                <?php } ?>

            </table>
        </div>
    <?php } ?>
</div>


<style>
    th{
        white-space: nowrap;
        padding: 10px;
    }


    tr>:first-child {
        width: 66px;
        position: relative;
    }
</style>

<script>
    function exportTable(filename,id) {
            $("#"+id).table2excel({
                filename: filename //do not include extension
            });
      }

    var myChart = echarts.init(document.getElementById('chart'));
    option = {
        title : {
//            text: '未来一周气温变化',
//            subtext: '纯属虚构'
        },
        tooltip : {
            trigger: 'axis',
            formatter: function(params){
                var percent1 = (params[0]['data']/(params[0]['data']+params[1]['data']))*100;
                var percent2 = (params[1]['data']/(params[0]['data']+params[1]['data']))*100;
                if( params[0]['seriesName'] == ''){
                    params[0]['seriesName'] = 'undefined';
                    params[1]['seriesName'] = 'undefined';
                    params[0]['name'] = 'undefined';
                    params[1]['name'] = 'undefined';
                    percent1 = 0;
                    percent2 = 0;
                }
                var str = '<style>td{padding:5px;}</style><table>';
                str += '<tr><td>部门</td><td>日期</td><td>数值</td><td>整体占比</td></tr>';
                str += '<tr><td>'+params[0]['seriesName']+'</td><td>'+params[0]['name']+'</td><td>'+params[0]['data']+'</td><td>'+percent1.toFixed(2)+'%'+'</td></tr>';
                str += '<tr><td>'+params[1]['seriesName']+'</td><td>'+params[1]['name']+'</td><td>'+params[1]['data']+'</td><td>'+percent2.toFixed(2)+'%'+'</td></tr>';
                str += '</table>';
                return str
            }
        },

        legend: {
            data:['<?php echo $groupName1; ?>','<?php echo $groupName2; ?>']
        },
        toolbox: {
            show : true,
            feature : {
//                mark : {show: true},
//                dataView : {show: true, readOnly: false},
//                magicType : {show: true, type: ['line', 'bar']},
//                restore : {show: true},
//                saveAsImage : {show: true},
            },
        },
        calculable : true,
        xAxis : [
            {
                type : 'category',
                boundaryGap : false,
                data : [<?php for ($i=0;$i<=$page['date_difference'];$i++){
                     echo "'".date('m-d',$page['start_date']+$i*86400)."'".',';} ?>]

            }
        ],
        yAxis : [
            {
                type : 'value',
                axisLabel : {
                    formatter: '{value} '
                }
            }
        ],
        series : [
            {
                name:'<?php echo $groupName1; ?>',
                type:'line',
                data:[<?php echo $line_data1; ?>]
            },
            {
                name:'<?php echo $groupName2; ?>',
                type:'line',
                data:[<?php echo $line_data2; ?>]
            }

        ]
    };
    myChart.setOption(option);
</script>

