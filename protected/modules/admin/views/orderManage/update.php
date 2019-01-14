<?php require(dirname(__FILE__) . "/../common/head.php"); ?>

<div class="main mhead">
    <div class="snav">直接下单管理 » 下单列表 » 修改订单</div>
</div>
<div class="main mbody">
    <form method="post"
          action="<?php echo $this->createUrl('orderManage/edit'); ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <input type="hidden" id="backurl" name="backurl" value="<?php echo $this->get('url'); ?>"/>

        <table class="tb3">
            <tr>
                <td width="120">ID：</td>
                <td class="alignleft"><?php echo $page['info']['id']; ?></td>
            </tr>
            <tr>
                <td width="120">下单时间：</td>
                <td class="alignleft"><?php echo date('Y-m-d H:i:s', $page['info']['add_time']); ?></td>
            </tr>
            <tr>
                <td width="120">订单号：</td>
                <td class="alignleft"><?php echo $page['info']['order_code']; ?></td>
            </tr>

            <tr>
                <td width="120">下单商品：</td>
                <td class="alignleft">
                    <?php echo $page['goods']['package_name']; ?>
                </td>
            </tr>
            <tr>
                <td width="120">下单价格：</td>
                <td class="alignleft">
                    <?php echo $page['goods']['package_price'] . "元"; ?>
                </td>
            </tr>

            <tr>
                <td width="120">客户姓名：</td>
                <td class="alignleft">
                    <?php echo $page['info']['real_name']; ?>
                </td>
            </tr>
            <tr>
                <td width="120">联系方式：</td>
                <td class="alignleft">
                    <?php echo $page['info']['mobile']; ?>
                </td>
            </tr>
            <tr>
                <td width="120">地址：</td>
                <td class="alignleft">
                    <?php echo $page['assistant']['province'] . ' ' . $page['assistant']['city'] . ' ' . $page['assistant']['region'] . '<br/>' . $page['assistant']['detail_area']; ?>
                </td>
            </tr>
            <tr>
                <td width="120">方便联系时间：</td>
                <td class="alignleft">
                    <?php echo vars::get_field_str('best_time', $page['info']['address']); ?>
                </td>
            </tr>

            <tr>
                <td width="120">文案编码：</td>
                <td class="alignleft">
                    <?php echo $page['assistant']['article_code']; ?>
                </td>
            </tr>
            <tr>
                <td width="120">渠道编码：</td>

                <td class="alignleft">
                    <?php
                    $channelList = Channel::model()->getChannelCodeList();
                    echo CHtml::dropDownList('channel_id', $page['info']['channel_id'], CHtml::listData($channelList, 'id', 'channel_code'),
                        array(
                            'empty' => '请选择'
                        , 'disabled' => 'disabled'
                        )
                    );
                    ?>
                </td>
            </tr>
            <tr>
                <td width="120">客服部：</td>

                <td class="alignleft">
                    <?php
                    $customerServiceList = CustomerServiceManage::model()->getCustomerServiceList();
                    echo CHtml::dropDownList('customer_service_id', $page['info']['customer_service_id'], CHtml::listData($customerServiceList, 'id', 'cname'),
                        array(
                            'empty' => '请选择客服部',
                            'id' => 'customer_service_id'
                        , 'disabled' => 'disabled'
                        )
                    );
                    ?>
                </td>
            </tr>

            <?php if ($page['info']['is_back'] == 1) { ?>
                <tr>
                    <td width="120">实际金额：</td>
                    <td class="alignleft">
                        <input type="text" class="ipt" id="real_price" name="real_price"
                               value="<?php echo $page['goods']['real_price']; ?>"/>元
                    </td>
                </tr>
                <tr>
                    <td width="120">客服订单号：</td>
                    <td class="alignleft">
                        <input type="text" class="ipt" id="corder_code" name="corder_code"
                               value="<?php echo $page['info']['corder_code']; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td width="120">订单状态：</td>
                    <td class="alignleft">
                        <select name="order_status">
                            <?php
                            foreach (vars::$fields['order_status'] as $key => $val) {
                                ?>
                                <option
                                        value="<?php echo $val['value']; ?>" <?php echo $val['value'] == $page['info']['order_status'] ? 'selected' : ''; ?>>
                                    <?php echo $val['txt']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>发货日期：</td>
                    <td>
                        <input type="text" id="delivery_date" class="ipt" name="delivery_date"
                               value="<?php echo $page['info']['delivery_date'] ? date('Y-m-d', $page['info']['delivery_date']) : ''; ?>"
                               onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <td></td>
                <td class="alignleft">
                    <?php if ($page['info']['is_export'] == 1) { ?>
                        <input type="submit" class="but" id="subtn" value="确定"/>
                    <?php } ?>
                    <input type="button" class="but" value="返回"
                           onclick="window.location='<?php echo $this->get('url'); ?>'"/>
                </td>
            </tr>
        </table>

    </form>
</div>