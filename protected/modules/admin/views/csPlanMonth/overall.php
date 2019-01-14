<?php
$today = strtotime(date('Y-m-01'),time());
if($today > $page['start_date']){
    $percent = 100;
}else if ($today == $page['start_date']) {
    $today= date('j',time());
    $month = date('t',$page['start_date']);
    $percent  = round($today/$month,2)*100;
} else{
    $percent = 0;
}

?>

<table class="tb3" style="margin-top: 10px">
    <div style="background-color: white;height: 50px;width:  100%;border: solid 1px;border-color:#CCCCCC;text-align: center;font-size: 20px; position: relative;">
        <div style="position: absolute; z-index: 1;top: 10px;left: 700px;">本月时间进度 : <?php echo $percent; ?>%</div>
        <div style="background-color: yellow;height:100%;width:   <?php echo $percent; ?>%;position: absolute;"></div>
    </div>
    <tr>
        <th>客服部</th>
        <th>计划进粉</th>
        <th>实际进粉</th>
        <th>完成率</th>
        <th>计划产值</th>
        <th>实际产值</th>
        <th>完成率</th>
        <th>操作</th>
    </tr>
    <?php foreach ($page['list'] as $key=>$value){ ?>
        <tr>
            <td style="text-align: center"><?php echo $value['service_name']; ?></td>
            <td style="text-align: center"><?php echo $value['fans']; ?></td>
            <td style="text-align: center"><?php echo $value['data_fans']; ?></td>
            <td style="text-align: center" class="dif_class_1"><?php echo $value['data_fans']-$value['fans']?>
                (<span><?php echo $value['fans_radio']; ?></span>%)</td>
            <td style="text-align: center"><?php echo $value['output']; ?></td>
            <td style="text-align: center"><?php echo $value['date_output']; ?></td>
            <td style="text-align: center" class="dif_class_2"><?php echo $value['date_output']-$value['output'];?>
            (<span><?php echo $value['output_radio'];?></span>%)
            </td>
            <td style="text-align: center">
                <input type="button" value="偏差定位" onclick="return dialog_frame(this,950,600,1)"
                       href="<?php echo $this->createUrl('csPlanMonth/getServiceDetail');?>?service_id=<?php echo  $value['service_group'];?>&start_date=<?php echo date('Y-m',$page['start_date']);?>">
            </td>
        </tr>
    <?php } ?>
    <tr>

    </tr>
</table>
<?php
$remind = Yii::app()->params['remind'];
$high_fans = $remind['fans_up'];
$low_fans = $remind['Fans_down'];
//产值数据浮动提醒配置
$high_output = $remind['output_up'];
$low_output = $remind['output_down'];
?>
<script type="text/javascript">
    var high_fans = <?php echo $high_fans;?>;
    var low_fans = <?php echo $low_fans;?>;
    var low_output = <?php echo $low_output;?>;
    var high_output = <?php echo $high_output;?>;
    $(".dif_class_1").each(function (key,obj) {
        var val = $(this).find('span').text();
        if (val > high_fans) {
            $(this).css({color:"red"})
        }
        if (val <low_fans) {
            $(this).css({color:"blue"})
        }
    });
    $(".dif_class_2").each(function (key,obj) {
        var val = $(this).find('span').text();
        if (val > high_output) {
            $(this).css({color:"red"})
        }
        if (val <low_output) {
            $(this).css({color:"blue"})
        }
    })

</script>