<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<script>
    function exportList() {
        var data = $("#serchForm").serialize();
        var url = "<?php echo $this->createUrl('deliveryIndepOrder/export');?>";
        window.location.href=url+'?'+data;
    }
</script>
<div class="main mhead">
    <div class="snav">订单系统 » 独立订单发货</div>
    <div class="mt10 clearfix">
        <form action="<?php echo $this->createUrl('deliveryIndepOrder/index'); ?>" id="serchForm">
            发货日期：
            <input type="text" size="20" class="ipt" style="width:120px;" name="start_delivery_date" id="start_delivery_date"
                   value="<?php echo $this->get('start_delivery_date'); ?>"
                   onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>-
            <input type="text" size="20" class="ipt" style="width:120px;" name="end_delivery_date" id="end_delivery_date"
                   value="<?php echo $this->get('end_delivery_date'); ?>"
                   onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
            微信号：
            <input type="text" id="wechat_id" name="wechat_id" class="ipt" style="width:130px;"
                   value="<?php echo $this->get('wechat_id'); ?>">&nbsp;
            发货金额：
            <input type="text" id="delivery_money" name="delivery_money" class="ipt" style="width:130px;"
                   value="<?php echo $this->get('delivery_money'); ?>">&nbsp;
            单量：
            <input type="text" id="delivery_count" name="delivery_count" class="ipt" style="width:130px;"
                   value="<?php echo $this->get('delivery_count'); ?>">&nbsp;
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
            推广人员:
            <?php
            $promotionStafflist = PromotionStaff::model()->getPromotionStaffList(0);
            echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($promotionStafflist, 'user_id', 'name'),
                array(
                    'empty' => '推广人员',
                )
            );
            ?>&nbsp;&nbsp;

            <input type="submit" class="but" value="查询">
            </form>
    </div>
    <div class="mt10 clearfix">
        <div class="l" id="container">
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="删除选中" onclick="set_some(\'' . $this->createUrl('deliveryIndepOrder/del') . '?ids=[@]\',\'确定删除吗？\');" />', 'auth_tag' => 'deliveryIndepOrder_del')); ?>
            &nbsp;
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="模板下载" onclick="location=\'' . $this->createUrl('deliveryIndepOrder/template') . '\'" />', 'auth_tag' => 'deliveryIndepOrder_template')); ?>
            &nbsp;
        </div>
        <form action="<?php echo $this->createUrl('deliveryIndepOrder/import'); ?>" method="post"
              enctype="multipart/form-data">
            <?php $this->check_u_menu(array('code' => '<input type="file"  name="filename"  />', 'auth_tag' => 'deliveryIndepOrder_import')); ?>
            &nbsp;
            <?php $this->check_u_menu(array('code' => '<input type="submit" name="submit" class="but2" value="导入" />', 'auth_tag' => 'deliveryIndepOrder_import')); ?>
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="导出" onclick="exportList()" />', 'auth_tag' => 'deliveryIndepOrder_export')); ?>
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
                <th style="width:60px;"><?php $this->check_u_menu(array('code' => '<a href="javascript:void(0);"  onclick="check_all(\'.cklist\');">全选/反选</a>', 'auth_tag' => 'deliveryIndepOrder_del')); ?></th>
                <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('deliveryIndepOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => 'ID', 'field' => 'id')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('deliveryIndepOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '发货日期', 'field' => 'delivery_date')); ?></th>
                <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('deliveryIndepOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '微信号ID', 'field' => 'b.wechat_id')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('deliveryIndepOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '客服部', 'field' => 'f.cname')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('deliveryIndepOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '业务', 'field' => 'd.bid')); ?></th>
                <th>计费方式</th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('deliveryIndepOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '商品', 'field' => 'c.goods_name')); ?></th>
                <th>推广人员</th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('deliveryIndepOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '单量', 'field' => 'delivery_count')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('deliveryIndepOrder/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '发货金额（元）', 'field' => 'delivery_money')); ?></th>
                <th>操作</th>
            </tr>
            <tr>
                <th>-</th><th>-</th><th>-</th><th>-</th><th>-</th> <th>-</th><th>-</th><th>-</th><th>合计</th>
                <th><?php echo  $page['listdata']['delivery_count'];?></th>
                <th><?php echo  $page['listdata']['delivery_money'];?></th>
                <th>-</th>
            </tr>
            </thead>
            <?php $del = $this->check_u_menu(array('auth_tag' => 'deliveryIndepOrder_del'));
                  $edit = $this->check_u_menu(array('auth_tag' => 'deliveryIndepOrder_del'));
            ?>
            <?php foreach ($page['listdata']['list'] as $key => $val) { ?>
                <tr>
                    <td><?php if ($del) { ?>
                            <input type="checkbox" class="cklist" value="<?php echo $val['id']; ?>"/>
                        <?php }; ?>
                    </td>
                    <td><?php echo $val['id']; ?></td>
                    <td><?php echo date('Y-m-d', $val['delivery_date']); ?></td>
                    <td><?php echo $val['wechat_id']; ?></td>
                    <td><?php echo $page['listdata']['bNames'][$val['business_type']] ?></td>
                    <td><?php echo empty($val['charging_type'])? '' : vars::get_field_str('charging_type', $val['charging_type']); ?></td>
                    <td><?php echo $page['listdata']['csNames'][$val['customer_service_id']] ?></td>
                    <td><?php echo $page['listdata']['goodsNames'][$val['goods_id']] ?></td>
                    <td><?php echo $page['listdata']['userNames'][$val['tg_uid']] ?></td>
                    <td><?php echo $val['delivery_count']; ?></td>
                    <td><?php echo $val['delivery_money']; ?></td>
                    <td><?php if ($edit) { ?>
                            <a href="<?php echo $this->createUrl('deliveryIndepOrder/edit').'?id='.$val['id'].'&url='.$page['listdata']['url']; ?>">编辑</a>
                        <?php }; ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
        <div class="clear"></div>
    </form>
</div>

