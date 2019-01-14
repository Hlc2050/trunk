<?php require(dirname(__FILE__) . "/../common/head.php"); ?>

<div class="main mhead">
    <div class="snav">订单系统 » 订单下单 » 修改下单信息</div>
</div>
<div class="main mbody">

    <form method="post"
          action="<?php echo $this->createUrl('placeNormOrder/edit'); ?>?id=<?php echo $page['info']['id']; ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <input type="hidden" id="backurl" name="backurl" value="<?php echo $this->get('url'); ?>"/>

        <table class="tb3">
            <tr>
                <td width="150">ID：</td>
                <td><?php echo $page['info']['id']; ?></td>
            </tr>
            <tr>
                <td width="150">下单日期：</td>
                <td class="alignleft">
                    <input type="text" size="20" class="ipt" name="order_date" id="order_date"
                           value="<?php echo date('Y-m-d', $page['info']['order_date']); ?>"
                           onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>&nbsp;
                </td>
            </tr>
            <tr>
                <td>客户姓名：</td>
                <td>
                    <input type="text" class="ipt" name="customer"
                           value="<?php echo $page['info']['customer']; ?>"/>
                </td>
            </tr>
            <tr>
                <td>订单编号：</td>
                <td>
                    <input type="text" class="ipt" name="order_id"
                           value="<?php echo $page['info']['order_id']; ?>"/>
                </td>
            </tr>

            <tr>
                <td>微信号ID：</td>
                <td>
                    <?php echo WeChat::model()->findByPk($page['info']['weixin_id'])->wechat_id; ?>
                </td>
            </tr>
            <tr>
                <td>业务类型：</td>
                <td class="alignleft">
                    <?php
                    $businessTypes = Dtable::toArr(BusinessTypes::model()->findAll());
                    echo CHtml::dropDownList('business_type', $page['info']['business_type'], CHtml::listData($businessTypes, 'bid', 'bname'),
                        array(
                            'empty' => '选择业务类型',
                            'ajax' => array(
                                'type' => 'POST',
                                'url' => $this->createUrl('weChat/getChargingType'),
                                'update' => '#charging_type',
                                'data' => array('business_type' => 'js:$("#business_type").val()'),
                            )
                        )
                    );
                    ?>
                </td>
            </tr>
            <tr>
                <td width="120">计费方式：</td>
                <td class="alignleft">
                    <?php echo CHtml::dropDownList('charging_type', $page['info']['charging_type'], CHtml::listData($page['info']['chargingTypeList'], 'charging_type', 'cname'), array('--计费方式--')) ?>
                </td>
            </tr>
            <tr>
                <td width="120">客服部：</td>

                <td class="alignleft">
                    <?php
                    $params = array(
                        'select' => $page['info']['customer_service_id'],
                        'htmlOptions' => array(
                            'empty' => '请选择客服部',
                            'id' => 'customer_service_id'
                        ),
                    );
                    helper::getServiceSelect('customer_service_id', $params);
                    ?>
                </td>
            </tr>
            <tr>
                <td>商品：</td>
                <td class="alignleft">
                    <?php
                    $goodsList = CustomerServiceRelation::model()->getGoodsList($page['info']['customer_service_id']);
                    echo CHtml::dropDownList('goods_id', $page['info']['goods_id'], CHtml::listData($goodsList, 'goods_id', 'goods_name'), array('请选择')); ?>
                </td>
            </tr>
            <tr>
                <td>订单金额：</td>
                <td>
                    <input type="text" class="ipt" name="order_money"
                           value="<?php echo $page['info']['order_money']; ?>"/>
                </td>
            </tr>
            <tr>
                <td width="150">加粉日期：</td>
                <td class="alignleft">
                    <input type="text" size="20" class="ipt" name="addfan_date" id="addfan_date"
                           value="<?php echo date('Y-m-d', $page['info']['addfan_date']); ?>" readonly/>&nbsp;
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="alignleft"><input type="submit" class="but" id="subtn" value="确定"/>
                    <input type="button"
                           class="but" value="返回"
                           onclick="window.location='<?php echo $this->get('url'); ?>'"/>
                </td>
            </tr>
        </table>
        <script type="text/javascript">

            //客服部 商品联动
            jQuery(function ($) {
                jQuery('body').on('change', '#customer_service_id', secondChange);
            });

            function secondChange() {
                jQuery.ajax({
                    'type': 'POST',
                    'url': '/admin/customerService/getGoods',
                    'data': {'cs_id': $("#customer_service_id").val()},
                    'cache': false,
                    'success': function (html) {
                        jQuery("#goods_id").html(html);
                    }
                });
                return false;
            }

            $("#business_type").attr('disabled', 'disabled');
            $("#charging_type").attr('disabled', 'disabled');
            $("#goods_id").attr('disabled', 'disabled');
            $("#customer_service_id").attr('disabled', 'disabled');
        </script>


    </form>
</div>
