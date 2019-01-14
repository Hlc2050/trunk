<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<script>
    function exportList() {
        var data = $("#serchForm").serialize();
        var url = "<?php echo $this->createUrl('fansInputStatic/export');?>";
        window.location.href = url+'?'+data;
    }
</script>
<div class="main mhead">
    <div class="snav">订单系统 » 进粉观察表</div>
    <div class="mt10 clearfix">
        <form action="<?php echo $this->createUrl('fansInputStatic/index'); ?>" id="serchForm">
            查询日期：
            <input type="text" size="20" class="ipt" style="width:120px;" name="start_date" id="start_date"
                   value="<?php echo $this->get('start_date'); ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>-
            <input type="text" size="20" class="ipt" style="width:120px;" name="end_date" id="end_date"
                   value="<?php echo $this->get('end_date'); ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>&nbsp;
            推广人员： <?php
            $promotionStafflist = AdminUser::model()->get_all_user();
            echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($promotionStafflist, 'csno', 'csname_true'),
                array('empty' => '全部')
            );
            ?>
            微信号：
            <input type="text" id="wechat_id" name="wechat_id" class="ipt" style="width:130px;"
                   value="<?php echo $this->get('wechat_id'); ?>">&nbsp;
            客服部：
            <?php
            helper::getServiceSelect('csid');
            ?>
            &nbsp;&nbsp
            商品：
            <?php
            echo $this->get('csid') ? CHtml::dropDownList('goods_id', $this->get('goods_id'), CHtml::listData(CustomerServiceRelation::model()->getGoodsList($this->get('csid')), 'goods_id', 'goods_name'), array('empty' => '全部')) :
                CHtml::dropDownList('goods_id', $this->get('goods_id'),CHtml::listData(Goods::model()->findAll(), 'id', 'goods_name'), array('empty' =>'全部'))
            ?>&nbsp;&nbsp;
            <input type="submit" class="but" value="查询">
        </form>
    </div>
    <div class="mt10 clearfix">
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="导出" onclick="exportList()" />', 'auth_tag' => 'fansInputStatic_export')); ?>
        <div class="r">
        </div>
    </div>
</div>
<div class="main mbody">
    <form>
        <table class="tb fixTh">
            <thead>
            <tr>
                <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('fansInputStatic/index') . '?p=' . $_GET['p'] . '&start_date='.$start_date.'&end_date='.$end_date, 'field_cn' => '推广人员', 'field' => 'tg_uid')); ?></th>
                <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('fansInputStatic/index') . '?p=' . $_GET['p'] . '&start_date='.$start_date.'&end_date='.$end_date, 'field_cn' => '进粉量', 'field' => 'addfan_count')); ?></th>
                <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('fansInputStatic/index') . '?p=' . $_GET['p'] .  '&start_date='.$start_date.'&end_date='.$end_date, 'field_cn' => '删除拉黑', 'field' => 'del_black')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('fansInputStatic/index') . '?p=' . $_GET['p'] . '&start_date='.$start_date.'&end_date='.$end_date, 'field_cn' => '刷粉', 'field' => 'brush_fans')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('fansInputStatic/index') . '?p=' . $_GET['p'] . '&start_date='.$start_date.'&end_date='.$end_date, 'field_cn' => '性别不符合粉', 'field' => 'gender_dif_fans')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('fansInputStatic/index') . '?p=' . $_GET['p'] . '&start_date='.$start_date.'&end_date='.$end_date, 'field_cn' => '不回复粉', 'field' => 'not_reply_fans')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('fansInputStatic/index') . '?p=' . $_GET['p'] . '&start_date='.$start_date.'&end_date='.$end_date, 'field_cn' => '年龄不符合粉', 'field' => 'age_dif_fans')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('fansInputStatic/index') . '?p=' . $_GET['p'] . '&start_date='.$start_date.'&end_date='.$end_date, 'field_cn' => '疾病粉', 'field' => 'disease_fans')); ?></th>
                <th>有效粉</th>
                <th>删除拉黑占比</th>
                <th>刷粉占比</th>
                <th>性别不符合粉占比</th>
                <th>不回复粉占比</th>
                <th>年龄不符合占比</th>
                <th>疾病粉占比</th>
                <th>有效粉占比</th>
            </tr>
            <tr>
                <th>合计</th>
                <th><?php echo $page['total']['addfan_count'];?></th>
                <th><?php echo $page['total']['del_black'];?></th>
                <th><?php echo $page['total']['brush_fans'];?></th>
                <th><?php echo $page['total']['gender_dif_fans'];?></th>
                <th><?php echo $page['total']['not_reply_fans'];?></th>
                <th><?php echo $page['total']['age_dif_fans'];?></th>
                <th><?php echo $page['total']['disease_fans'];?></th>
                <th><?php echo $page['total']['valid_fans'];?></th>
                <th><?php echo $page['total']['black_rate'];?>%</th>
                <th><?php echo $page['total']['brush_rate'];?>%</th>
                <th><?php echo $page['total']['gender_rate'];?>%</th>
                <th><?php echo $page['total']['not_reply_rate'];?>%</th>
                <th><?php echo $page['total']['age_dif_rate'];?>%</th>
                <th><?php echo $page['total']['disease_rate'];?>%</th>
                <th><?php echo $page['total']['valid_rate'];?>%</th>
            </tr>
            </thead>
            <?php foreach ($page['listdata']['list'] as $key => $val) { ?>
                <tr>
                    <td><?php echo $val['csname_true']; ?></td>
                    <td><?php echo $val['addfan_count']; ?></td>
                    <td><?php echo $val['del_black']; ?></td>
                    <td><?php echo $val['brush_fans']; ?></td>
                    <td><?php echo $val['gender_dif_fans']; ?></td>
                    <td><?php echo $val['not_reply_fans']; ?></td>
                    <td><?php echo $val['age_dif_fans']; ?></td>
                    <td><?php echo $val['disease_fans']; ?></td>
                    <td><?php echo $val['valid_fans']; ?></td>
                    <td><?php echo $val['black_rate']; ?>%</td>
                    <td><?php echo $val['brush_rate']; ?>%</td>
                    <td><?php echo $val['gender_rate']; ?>%</td>
                    <td><?php echo $val['not_reply_rate']; ?>%</td>
                    <td><?php echo $val['age_dif_rate']; ?>%</td>
                    <td><?php echo $val['disease_rate']; ?>%</td>
                    <td><?php echo $val['valid_rate']; ?>%</td>
                </tr>
            <?php } ?>
        </table>
        <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
        <div class="clear"></div>
    </form>
</div>

