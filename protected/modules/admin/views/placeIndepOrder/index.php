<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<script>
    function exportList() {
        var data = $("#serchForm").serialize();
        var url = "<?php echo $this->createUrl('placeIndepOrder/export');?>";
        window.location.href=url+'?'+data;
    }
</script>
<div class="main mhead">
    <div class="snav">订单系统 » 独立订单下单</div>
    <div class="mt10">
        <form action="<?php echo $this->createUrl('placeIndepOrder/index'); ?>" id="serchForm">
            下单日期：
            <input type="text" size="20" class="ipt" style="width:120px;" name="start_order_date" id="start_order_date"
                   value="<?php echo $this->get('start_order_date'); ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>-
            <input type="text" size="20" class="ipt" style="width:120px;" name="end_order_date" id="end_order_date"
                   value="<?php echo $this->get('end_order_date'); ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>&nbsp;
            微信号：
            <input type="text" id="wechat_id" name="wechat_id" class="ipt" style="width:130px;"
                   value="<?php echo $this->get('wechat_id'); ?>">&nbsp;
            下单金额：
            <input type="text" id="order_money" name="order_money" class="ipt" style="width:130px;"
                   value="<?php echo $this->get('order_money'); ?>">&nbsp;
            单量：
            <input type="text" id="order_count" name="order_count" class="ipt" style="width:130px;"
                   value="<?php echo $this->get('order_count'); ?>">&nbsp;
            客服部：
            <?php
            helper::getServiceSelect('csid');
            ?>
            &nbsp;
            商品：
            <?php
            echo $this->get('csid') ? CHtml::dropDownList('goods_id', $this->get('goods_id'), CHtml::listData(CustomerServiceRelation::model()->getGoodsList($this->get('csid')), 'goods_id', 'goods_name'), array('empty' => '全部')) :
                CHtml::dropDownList('goods_id', $this->get('goods_id'), CHtml::listData(Goods::model()->findAll(), 'id', 'goods_name'), array('empty' => '全部'))
            ?>
            推广人员:
            <?php
            $promotionStafflist = PromotionStaff::model()->getPromotionStaffList(0);
            echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($promotionStafflist, 'user_id', 'name'),
                array(
                    'empty' => '推广人员',
                )
            );
            ?>
            <input type="submit" class="but" value="查询">
        </form>
    </div>
    <div class="mt10 clearfix">
        <div class="l" id="container">
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="删除选中" onclick="set_some(\'' . $this->createUrl('placeIndepOrder/del') . '?ids=[@]\',\'确定删除吗？\');" />', 'auth_tag' => 'placeIndepOrder_del')); ?>
            &nbsp;
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="模板下载" onclick="location=\'' . $this->createUrl('placeIndepOrder/template') . '\'" />', 'auth_tag' => 'placeIndepOrder_template')); ?>
            &nbsp;
        </div>
        <form action="<?php echo $this->createUrl('placeIndepOrder/import'); ?>" method="post"
              enctype="multipart/form-data">
            <?php $this->check_u_menu(array('code' => '<input type="file"  name="filename"  />', 'auth_tag' => 'placeIndepOrder_import')); ?>
            &nbsp;
            <?php $this->check_u_menu(array('code' => '<input type="submit" name="submit" class="but2" value="导入" />', 'auth_tag' => 'placeIndepOrder_import')); ?>
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="导出" onclick="exportList()" />', 'auth_tag' => 'placeIndepOrder_export')); ?>
        </form>
        <div class="r">
        </div>
    </div>
</div>
<div class="main mbody">
    <form>
        <table class="tb fixTh">
            <thead>
            <tr>
                <th style="width:60px;"><?php $this->check_u_menu(array('code' => '<a href="javascript:void(0);"  onclick="check_all(\'.cklist\');">全选/反选</a>', 'auth_tag' => 'placeIndepOrder_del')); ?></th>
                <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('placeIndepOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => 'ID', 'field' => 'id')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('placeIndepOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '下单日期', 'field' => 'order_date')); ?></th>
                <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('placeIndepOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '微信号ID', 'field' => 'b.wechat_id')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('placeIndepOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '客服部', 'field' => 'f.cname')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('placeIndepOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '业务', 'field' => 'd.bid')); ?></th>
                <th>计费方式</th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('placeIndepOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '商品', 'field' => 'c.goods_name')); ?></th>
                <th>推广人员</th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('placeIndepOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '单量', 'field' => 'order_count')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('placeIndepOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '下单金额（元）', 'field' => 'order_money')); ?></th>
                <th>操作</th>
            </tr>
            <tr>
                <th>-</th><th>-</th><th>-</th><th>-</th><th>-</th> <th>-</th><th>-</th><th>-</th><th>合计</th>
                <th><?php echo  $page['listdata']['order_count'];?></th>
                <th><?php echo  $page['listdata']['order_money'];?></th>
                <th>-</th>
            </tr>
            </thead>
            <?php foreach ($page['listdata']['list'] as $key => $val) { ?>
                <tr>
                    <td><?php $this->check_u_menu(array('code' => '<input type="checkbox" class="cklist" value="' . $val['id'] . '"/>', 'auth_tag' => 'placeIndepOrder_del')); ?></td>
                    <td><?php echo $val['id']; ?></td>
                    <td><?php echo date('Y-m-d', $val['order_date']); ?></td>
                    <td><?php echo $val['wechat_id']; ?></td>
                    <td><?php echo $page['listdata']['bNames'][$val['business_type']] ?></td>
                    <td><?php echo empty($val['charging_type'])? '' : vars::get_field_str('charging_type', $val['charging_type']); ?></td>
                    <td><?php echo $page['listdata']['csNames'][$val['customer_service_id']] ?></td>
                    <td><?php echo $page['listdata']['goodsNames'][$val['goods_id']] ?></td>
                    <td><?php echo $page['listdata']['userNames'][$val['tg_uid']] ?></td>
                    <td><?php echo $val['order_count']; ?></td>
                    <td><?php echo $val['order_money']; ?></td>
                    <td><?php $this->check_u_menu(array('code' => '<a href="' . $this->createUrl('placeIndepOrder/edit').'?id='.$val['id'].'&url='.$page['listdata']['url'].'">修改</a>', 'auth_tag' => 'placeIndepOrder_edit')); ?></td>
                </tr>
            <?php } ?>
        </table>
        <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
        <div class="clear"></div>
    </form>
</div>

