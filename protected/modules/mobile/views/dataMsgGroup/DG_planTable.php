<?php foreach ($page['info']['list'] as $value) {?>
    <div class="am-panel am-panel-secondary">
        <div class="am-panel-hd"><?php echo $page['service_group'][$value['service_group']];?></div>
        <div class="am-panel-bd">
            <table class="am-table am-table-centered">
                <tbody>
                <tr>
                    <td style="width: 30%;padding: 0"></td>
                    <td style="width: 35%">进粉</td>
                    <td  style="width: 35%"> 产值</td>
                </tr>
                <tr>
                    <td style="width: 30%;padding: 0">今日计划</td>
                    <td><?php echo $value['plan_fans'];?></td>
                    <td><?php echo $value['plan_output'];?></td>
                </tr>
                <?php foreach ($value['pre_data'] as $pre){?>
                    <tr>
                        <td style="width: 30%;padding: 0"><?php echo $pre['date_str'];?>实际</td>
                        <td><?php echo $pre['date_fans'];?></td>
                        <td><?php echo $pre['date_output'];?></td>
                    </tr>
                <?php }?>
                <tr>
                    <td style="width: 30%;padding: 0">三日平均</td>
                    <td style="color: red"><?php echo $value['fans_avg'];?></td>
                    <td style="color: red"><?php echo $value['output_avg'];?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php } ?>