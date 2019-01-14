<?php foreach ($page['info']['list'] as $value) {
    $url = $this->createUrl('/mobile/dataMsgUser/groupUserData').'?service_id='.$value['service_group'].'&data_type=1&date='.$page['info']['date'];
?>
<div class="am-panel am-panel-secondary">
    <div class="am-panel-hd"><?php echo $page['service_group'][$value['service_group']];?></div>
    <div class="am-panel-bd">
        <table class="am-table am-table-centered">
            <tbody>
            <tr>
                <td>实际进粉</td>
                <td>计划进粉</td>
                <td class="dif_class_1"><span><?php echo $value['fans_radio'];?></span>%</td>
            </tr>
            <tr>
                <td><?php echo $value['data_fans'];?></td>
                <td><?php echo $value['plan_fans'];?></td>
                <td class="dif_class_11">(<?php echo $value['data_fans']-$value['plan_fans'];?>)</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<?php } ?>