<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <table class="tb">
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
        </tr>
        <tr>
            <th><?php echo $page['service']['date']?></th>
            <th><?php echo $page['service']['service_name']?></th>
            <th><?php echo $page['service']['plan_fans']?></th>
            <th><?php echo $page['service']['data_fans']?></th>
            <th class="dif_class_1"><?php echo $page['service']['data_fans']-$page['service']['plan_fans'];?>(<span><?php echo $page['service']['fans_radio']?></span>%)</th>
            <th><?php echo $page['service']['plan_output']?></th>
            <th><?php echo $page['service']['data_output']?></th>
            <th class="dif_class_2"><?php echo $page['service']['data_output']-$page['service']['plan_output'];?>(<span><?php echo $page['service']['output_radio']?></span>%)</th>
        </tr>
        </thead>
        <tbody>

        <?php foreach ($page['group'] as $key=>$group) { ?>
            <tr>
                <td></td>
                <td><?php echo $page['group_name'][$key]; ?></td>
                <td><?php echo $group['plan_fans']; ?></td>
                <td><?php echo $group['data_fans']; ?></td>
                <td class="dif_class_1"><?php echo $group['data_fans']-$group['plan_fans']; ?>(<span><?php echo $group['fans_radio']; ?></span>%)</td>
                <td><?php echo $group['plan_output']; ?></td>
                <td><?php echo $group['date_output']; ?></td>
                <td class="dif_class_2"><?php echo $group['date_output'] - $group['plan_output']; ?>(<span><?php echo $group['output_radio']; ?></span>%)
                </td>
            </tr>
            <?php foreach ($page['user'][$key] as $uid=>$user) { ?>
                <tr>
                    <td></td>
                    <td><?php echo $page['user_name'][$uid]; ?></td>
                    <td><?php echo $user['plan_fans']; ?></td>
                    <td><?php echo $user['data_fans']; ?></td>
                    <td class="dif_class_1"><?php echo $user['data_fans'] - $user['plan_fans']; ?>(<span><?php echo $user['fans_radio']; ?></span>%)</td>
                    <td><?php echo $user['plan_output']; ?></td>
                    <td><?php echo $user['date_output']; ?></td>
                    <td class="dif_class_2"><?php echo $user['date_output'] - $user['plan_output']; ?>(<span><?php echo $user['output_radio']; ?></span>%)
                    </td>
                </tr>
            <?php }?>
        <?php } ?>
        </tbody>
    </table>

</div>
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
