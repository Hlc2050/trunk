<?php foreach ($page['info']['list'] as $value) {
    $url = $this->createUrl('/mobile/dataMsgUser/groupUserData').'?service_id='.$value['service_group'].'&data_type=2&date='.$page['info']['date'];
?>
<div class="am-panel am-panel-secondary">
    <div class="am-panel-hd"><?php echo $page['service_group'][$value['service_group']];?></div>
    <div class="am-panel-bd">
        <table class="am-table am-table-centered">
            <tbody>
            <tr>
                <td>实际产值</td>
                <td>计划产值</td>
                <td class="dif_class_2"><span><?php echo $value['output_radio'];?></span>%</td>
            </tr>
            <tr>
                <td><?php echo $value['data_output'];?></td>
                <td><?php echo $value['plan_output'];?></td>
                <td class="dif_class_21">(<?php echo $value['data_output']-$value['plan_output'];?>)</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<?php } ?>