<!-- 个人周排期 -->
<table class="tb fixTh">
    <thead>
    <tr>
        <th>日期</th>
        <th>客服部</th>
        <th>计划进粉</th>
        <th>实际进粉</th>
        <th>进粉偏差</th>
        <th>计划产值</th>
        <th>实际产值</th>
        <th>产值偏差</th>
        <th>操作</th>
    </tr>
    <tr>
        <th>-</th>
        <th>总计</th>
        <th><?php echo $page['info']['total']['plan_fans']; ?></th>
        <th><?php echo $page['info']['total']['data_fans']; ?></th>
        <th class="dif_class_1"><?php echo $page['info']['total']['data_fans'] - $page['info']['total']['plan_fans']; ?>
            (<span><?php echo $page['info']['total']['fans_radio']; ?></span>%)
        </th>
        <th><?php echo $page['info']['total']['plan_output']; ?></th>
        <th><?php echo $page['info']['total']['data_output']; ?></th>
        <th class="dif_class_2"><?php echo $page['info']['total']['data_output'] - $page['info']['total']['plan_output']; ?>
            (<span><?php echo $page['info']['total']['output_radio']; ?></span>%)
        </th>
        <th>-</th>
    </tr>
    </thead>
    <tbody>

    <?php foreach ($page['info']['list'] as $service) {
        $w = date('w',$service['date_time']);
     ?>
        <tr>
            <td><?php echo $service['date']; ?>(<?php echo vars::get_field_str('week_day',$w); ?>)</td>
            <td><?php echo $service['service_name']; ?></td>
            <td><?php echo $service['plan_fans']; ?></td>
            <td><?php echo $service['data_fans']; ?></td>
            <td class="dif_class_1"><?php echo $service['data_fans'] - $service['plan_fans']; ?>(<span><?php echo $service['fans_radio']; ?></span>%)</td>
            <td><?php echo $service['plan_output']; ?></td>
            <td><?php echo $service['date_output']; ?></td>
            <td class="dif_class_2"><?php echo $service['date_output'] - $service['plan_output']; ?>(<span><?php echo $service['output_radio']; ?></span>%)
            </td>
            <td>
                <input type="button" value="偏差定位" onclick="return dialog_frame(this,950,600,1)"
                       href="<?php echo $this->createUrl('dataStatistic/serviceDetail');?>?service_id=<?php echo $service['service_group'];?>&date=<?php echo $service['date_time'];?>">
            </td>
        </tr>
    <?php } ?>
    </tbody>
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