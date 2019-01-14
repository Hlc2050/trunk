<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">数据管理 » 问卷统计</div>
    <div class="mt10">
        <label>

    </div>
    <div class="mt10 clearfix">

        <div class="r">
        </div>
    </div>
</div>
<div class="main mbody">
    <form>
        <table class="tb fixTh">
            <thead>
            <tr>
                <th align='center'>ID</th>
                <th>上线日期</th>
                <th>问卷名称</th>
                <th>合作商</th>
                <th>渠道名称</th>
                <th>商品类别</th>
                <th>支持人员</th>
                <th>投票人数</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>

            <?php foreach ($page['listdata']['list'] as $key => $val) {
                $user_id = Questionnaire::model() -> findByPk( $val['vote_id'] ) -> support_staff_id;
                $online_date = date('Y-m-d', InfancePay::model()->findByPk($val['finance_pay_id'])->online_date);//上线日期
                $vote_title = Questionnaire::model()->findByPk($val['vote_id'])->vote_title;//问卷名称
                $partner = Partner::model()->findByPk(InfancePay::model()->findByPk($val['finance_pay_id'])->partner_id)->name;//合作商
                $channelInfo = Channel::model()->findByPk($val['channel_id']);  //渠道信息
                $channel_name = $channelInfo->channel_name;//渠道名称
                $status = vars::get_field_str('vote_status', $val['status']);//状态
                ?>
                <tr>
                    <td><?php echo $val['aid']; ?></td>
                    <td><?php echo $online_date; ?></td>
                    <td><?php echo $vote_title; ?></td>
                    <td><?php echo $partner; ?></td>
                    <td><?php echo $channel_name; ?></td>
                    <td><?php echo Linkage::model()->get_name(Questionnaire::model()->findByPk($val['vote_id'])->cat_id); ?></td>
                    <td><?php echo SupportStaff::model()->findByAttributes( array('user_id' => $user_id) )->name; ?></td>
                    <td><?php echo $val['vote_total']; ?></td>
                    <td><?php echo $status; ?></td>
                    <td>
                        <?php $this->check_u_menu(array('code' => '<a href="' . $this->createUrl('material/editQuestion') .'?id='.$val['vote_id']. '">详情</a>', 'auth_tag' => 'material_editQuestion')); ?>
                        <?php $this->check_u_menu(array('code' => '<a href="' . $this->createUrl('statVoteTotal/delete', array('id' => $val['id'])) . '"  onclick="return confirm(\'确定删除吗\')">删除</a>', 'auth_tag' => 'statVoteTotal_delete')); ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
        <div class="clear"></div>
    </form>
</div>


</script>
