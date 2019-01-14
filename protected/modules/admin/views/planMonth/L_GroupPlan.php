<?php require(dirname(__FILE__) . "/../common/head.php"); ?>

    <?php foreach($data['list'] as $key => $value){
        $update_mask = '';
        if ($value['through_time'] != 0 && $value['through_time']<$value['update_time']) {
            $update_mask = '(计划变更)';
        }
        ?>
        <table class="tb" style="width: 800px;border: solid 1px #CCCCCC;">
        <tr>
            <td> <span style="float: left;<?php if( $value['status'] == 1 || $value['status'] == 3){echo "color:red";} ?>"><?php echo vars::get_field_str('check_status',$value['status']); ?></span></td>
            <td colspan="2">
                <span><?php echo $value['name'] . '-';?></span>
                <span id="month"><?php echo date('Y-m',$value['month']); ?></span>
                <span><?php echo '月'.$update_mask.'-'.'进粉计划'; ?></span>
            </td>
            <?php if(in_array($value['name'],$page['manage_group'])){ ?>
            <td><a href="<?php echo $this->createUrl('planMonth/editGroup?id='.$value['id']) ?>" style="float: right" class="but2">修改</a></td>
            <?php }else{ ?>
                <td></td>
            <?php } ?>
        </tr>
        <tr>
            <th style="width: 200px;">客服部</th>
            <th style="width: 200px;">微信号个数</th>
            <th style="width: 200px;">计划进粉</th>
            <th style="width: 200px;">计划产值</th>
        </tr>
        <?php for ($i=0;$i<$value['num'];$i++){ ?>
            <tr>
                <td style="text-align: center">
                    <?php
                    echo helper::getServiceSelect2($value['data'][$i]['cs_id'],1);
                    ?>
                </td>
                <td style="text-align: center">
                    <span  name="weChat_num[]"><?php echo $value['data'][$i]['weChat_num'];  ?></span>
                </td>
                <td style="text-align: center">
                    <span  name="fans_plan[]"><?php echo $value['data'][$i]['fans_plan'];  ?></span>
                </td>
                <td style="text-align: center">
                    <span name="output_plan[]"><?php echo $value['data'][$i]['output_plan'];  ?></span>
                </td>
            </tr>
        <?php } ?>
        <tr >
            <td colspan="4"  style="text-align: left"><span><?php echo $value['remark']; ?></span></td>
        </tr>
        <?php if($value['unthrough_msg'] && $value['status'] !=4){ ?>
             <tr><td colspan="4" style="text-align: left;" name="unthrough_msg">拒绝理由:<?php echo $value['unthrough_msg']; ?></td></tr>
        <?php } ?>

            <div style="height: 10px;"></div>
        </table>
    <?php } ?>


