<!-- 组计划 -->
<?php $manage_groups = array_keys($page['info']['manage_group']);?>
<div style="height: 550px;width:100%;overflow-y: scroll">
    <?php  foreach ($page['info']['listdata'] as $value) { ?>
        <?php foreach ($page['info']['detail_plan'][$value['id']] as $key=>$groups) { ?>
            <table class="tb" style="width: 900px;margin-top: 10px;margin-bottom: 10px;">
                <thead>
                <tr>
                    <td colspan="1" style="text-align: left;<?php if( $value['status'] == 2){echo "color:red";}?>">
                        <?php if ($value['status'] == 2) {
                            echo '审核未通过';
                        } else if ($value['status'] == 1) {
                            echo '审核通过';
                        }else {
                            echo '待审核';
                        }?>

                    </td>
                    <td colspan="6" style="text-align: center"><?php echo $page['groups'][$key]?>-<?php echo date('Y-m-d',$value['start_date']);?>至<?php echo date('Y-m-d',$value['start_date']+6*24*60*60);?>-排期计划</td>
                    <td colspan="2" style="text-align: right">
                        <?php if (in_array($value['group_id'],$manage_groups)){?>
                        <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="修改" onclick="location=\'' . $this->createUrl('planWeek/editGroupPlan?week_id='. $value['id'].'&url=' . $page['url']) . '\'" />', 'auth_tag' => 'planWeek_editGroupPlan')); ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <th colspan="2">客服部/日期</th>
                    <?php for ($i=0;$i<7;$i++){ ?>
                        <th style="width: 90px;border: solid 1px #cccccc"><?php echo date('m-d',$value['start_date']+$i*24*60*60);?></th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($groups as $key=>$service) {?>
                    <tr>
                        <td rowspan="3">
                            <?php echo $page['service_group'][$key];?>
                        </td>
                        <td style="border: solid 1px #cccccc">微信号个数</td>
                        <?php foreach ($service as $k=>$plan) { ?>
                            <td style="border: solid 1px #cccccc">
                                <?php echo $plan['weChat_num'];?></td>
                        <?php }?>
                    </tr>
                    <tr>
                        <td style="border: solid 1px #cccccc">进粉</td>
                        <?php foreach ($service as $k=>$plan) { ?>
                            <td style="border: solid 1px #cccccc">
                                <?php echo $plan['fans_count'];?></td>
                        <?php }?>
                    </tr>
                    <tr>
                        <td style="border: solid 1px #cccccc">产值</td>
                        <?php foreach ($service as $k=>$plan) { ?>
                            <td style="border: solid 1px #cccccc"><?php echo $plan['output'];?></td>
                        <?php }?>
                    </tr>
                <?php }?>
                <tr><td colspan="9" style="text-align: left"><?php echo $value['mask'];?></td></tr>
                <?php if($value['unthrough_msg'] && $value['status'] == 2){?>
                    <tr><td colspan="9" style="text-align: left">拒绝理由:<?php echo $value['unthrough_msg'];?></td></tr>
                <?php } ?>
                </tbody>
            </table>
        <?php } ?>
    <?php } ?>

</div>


<script type="text/javascript">

</script>