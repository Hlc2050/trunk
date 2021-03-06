<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<script>
    function exportList() {
        var data = $("#serchForm").serialize();
        var url = "<?php echo $this->createUrl('deliveryNormOrder/export');?>";
        window.location.href = url + '?' + data;
    }
</script>
<div class="main mhead">
    <div class="snav">订单系统 » 订单发货</div>
    <div class="mt10">
        <form action="<?php echo $this->createUrl('deliveryNormOrder/index'); ?>" id="serchForm">
            <div class="mt10 clearfix">
                订单编号：
                <input type="text" id="order_id" name="order_id" class="ipt" style="width:130px;"
                       value="<?php echo $this->get('order_id'); ?>">&nbsp;
                客户姓名：
                <input type="text" id="customer" name="customer" class="ipt" style="width:130px;"
                       value="<?php echo $this->get('customer'); ?>">&nbsp;
                微信号：
                <input type="text" id="wechat_id" name="wechat_id" class="ipt" style="width:130px;"
                       value="<?php echo $this->get('wechat_id'); ?>">&nbsp;
                发货金额：
                <input type="text" id="delivery_money" name="delivery_money" class="ipt" style="width:130px;"
                       value="<?php echo $this->get('delivery_money'); ?>">&nbsp;
                客服部：
                <?php
                helper::getServiceSelect('csid');
                ?>
                &nbsp;&nbsp
                商品：
                <?php
                echo $this->get('csid') ? CHtml::dropDownList('goods_id', $this->get('goods_id'), CHtml::listData(CustomerServiceRelation::model()->getGoodsList($this->get('csid')), 'goods_id', 'goods_name'), array('empty' => '全部')) :
                    CHtml::dropDownList('goods_id', $this->get('goods_id'), CHtml::listData(Goods::model()->findAll(), 'id', 'goods_name'), array('empty' => '全部'))
                ?>&nbsp;&nbsp;
                推广人员:
                <?php
                $promotionStafflist = PromotionStaff::model()->getPromotionStaffList(0);
                echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($promotionStafflist, 'user_id', 'name'),
                    array(
                        'empty' => '推广人员',
                    )
                );
                ?>&nbsp;&nbsp;

            </div>
            <div class="mt10">
                发货日期：
                <input type="text" size="20" class="ipt" style="width:130px;" name="start_delivery_date"
                       id="start_delivery_date"
                       value="<?php echo $this->get('start_delivery_date'); ?>"
                       onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>-
                <input type="text" size="20" class="ipt" style="width:130px;" name="end_delivery_date"
                       id="end_delivery_date"
                       value="<?php echo $this->get('end_delivery_date'); ?>"
                       onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>&nbsp;
                加粉日期：
                <input type="text" size="20" class="ipt" style="width:130px;" name="start_addfan_date"
                       id="start_addfan_date"
                       value="<?php echo $this->get('start_addfan_date'); ?>"
                       onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>-
                <input type="text" size="20" class="ipt" style="width:130px;" name="end_addfan_date"
                       id="end_addfan_date"
                       value="<?php echo $this->get('end_addfan_date'); ?>"
                       onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
                <input type="submit" class="but" value="查询">
            </div>
        </form>
    </div>
    <div class="mt10 clearfix">
        <div class="l" id="container">
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="删除选中" onclick="set_some(\'' . $this->createUrl('deliveryNormOrder/del') . '?ids=[@]\',\'确定删除吗？\');" />', 'auth_tag' => 'deliveryNormOrder_del')); ?>
            &nbsp;
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="模板下载" onclick="location=\'' . $this->createUrl('deliveryNormOrder/template') . '\'" />', 'auth_tag' => 'deliveryNormOrder_template')); ?>
            &nbsp;
        </div>
        <form action="<?php echo $this->createUrl('deliveryNormOrder/import'); ?>" method="post"
              style="display: inline-block;float: left"
              enctype="multipart/form-data">
            <?php $this->check_u_menu(array('code' => '<input type="file"  name="filename"  />', 'auth_tag' => 'deliveryNormOrder_import')); ?>
            &nbsp;
            <?php $this->check_u_menu(array('code' => '<input type="submit" name="submit" class="but2" value="导入" />', 'auth_tag' => 'deliveryNormOrder_import')); ?>
            <?php $this->check_u_menu(array('code' => '<input type="button"  class="but2" value="导出" onclick="exportList()" />', 'auth_tag' => 'deliveryNormOrder_export')); ?>
        </form>

        <form action="<?php echo $this->createUrl('deliveryNormOrder/unDataImport'); ?>" method="post"
              style="display: inline-block;float: left;margin-left: 10px" enctype="multipart/form-data">
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" id="unBelong" value="下载未归属数据" onclick="location=\'' . $this->createUrl('deliveryNormOrder/unDataExport') . '\'" />', 'auth_tag' => 'deliveryNormOrder_unDataExport')); ?>

            <?php $this->check_u_menu(array('code' => '<input type="file"  name="filename" style="margin-left: 20px"  />', 'auth_tag' => 'deliveryNormOrder_unDataImport')); ?>
            &nbsp;
            <?php $this->check_u_menu(array('code' => '<input type="submit" name="submit" class="but2" value="导入未归属数据" />', 'auth_tag' => 'deliveryNormOrder_unDataImport')); ?>
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
                <th style="width:60px;"><?php $this->check_u_menu(array('code' => '<a href="javascript:void(0);"  onclick="check_all(\'.cklist\');">全选/反选</a>', 'auth_tag' => 'deliveryNormOrder_del')); ?></th>
                <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('deliveryNormOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => 'ID', 'field' => 'id')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('deliveryNormOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '发货日期', 'field' => 'order_date')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('deliveryNormOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '客户姓名', 'field' => 'customer')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('deliveryNormOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '订单编号', 'field' => 'order_id')); ?></th>
                <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('deliveryNormOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '微信号ID', 'field' => 'b.wechat_id')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('deliveryNormOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '业务', 'field' => 'd.bid')); ?></th>
                <th>计费方式</th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('deliveryNormOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '客服部', 'field' => 'f.cname')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('deliveryNormOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '商品', 'field' => 'c.goods_name')); ?></th>
                <th>推广人员</th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('deliveryNormOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '发货金额（元）', 'field' => 'a.order_money')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('deliveryNormOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '加粉日期', 'field' => 'a.addfan_date')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('deliveryNormOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '订单状态', 'field' => 'a.delivery_status')); ?></th>
                <th>操作</th>
            </tr>
            <tr>
                <th>-</th>
                <th>-</th>
                <th>-</th>
                <th>-</th>
                <th>-</th>
                <th>-</th>
                <th>-</th>
                <th>-</th>
                <th>-</th>
                <th>-</th>
                <th>合计</th>
                <th><?php echo $page['listdata']['delivery_money']; ?></th>
                <th>-</th>
                <th>-</th>
                <th>-</th>
            </tr>
            </thead>
            <?php $check_del = $this->check_u_menu(array('auth_tag' => 'deliveryNormOrder_del'));
                   $edit = $this->check_u_menu(array('auth_tag' => 'deliveryNormOrder_edit'));
            ?>
            <?php foreach ($page['listdata']['list'] as $key => $val) { ?>
                <tr>
                    <td>
                        <?php if ($check_del) { ?>
                        <input type="checkbox" class="cklist" value="<?php echo $val['id'] ; ?>"/>
                        <?php }; ?>
                    </td>
                    <td><?php echo $val['id']; ?></td>
                    <td><?php echo date('Y-m-d', $val['delivery_date']); ?></td>
                    <td><?php echo $val['customer']; ?></td>
                    <td><?php echo $val['order_id']; ?></td>
                    <td><?php echo $val['wechat_id']; ?></td>
                    <td><?php echo $page['listdata']['bNames'][$val['business_type']] ?></td>
                    <td><?php echo empty($val['charging_type'])? '' : vars::get_field_str('charging_type', $val['charging_type']); ?></td>
                    <td><?php echo $page['listdata']['csNames'][$val['customer_service_id']] ?></td>
                    <td><?php echo $page['listdata']['goodsNames'][$val['goods_id']] ?></td>
                    <td><?php echo $page['listdata']['userNames'][$val['tg_uid']] ?></td>
                    <td><?php echo $val['delivery_money']; ?></td>
                    <td><?php echo date('Y-m-d', $val['addfan_date']); ?></td>
                    <td><?php echo vars::get_field_str('delivery_status', $val['delivery_status']); ?></td>
                    <td>
                        <?php if ($edit) { ?>
                            <a href="<?php echo $this->createUrl('deliveryNormOrder/edit') . '?id=' . $val['id'] . '&url=' . $page['listdata']['url']; ?>">修改</a>
                        <?php }; ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
        <div class="clear"></div>
    </form>
</div>
<script>
    var un_belong = "<?php echo $has_un_belong;?>";
    if (un_belong === '0') {
        $("#unBelong").attr('disabled','disabled');
        $("#unBelong").css({"background-color":"#E0E0E0","border-color":"#E0E0E0","color":"#000"});
    }
</script>
