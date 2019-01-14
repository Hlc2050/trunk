<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<script>

    function exportList() {
        var data = $("#serchForm").serialize();
        var url = "<?php echo $this->createUrl('orderManage/export');?>";
        window.location.href = url + '?' + data;
    }

    function exportCheckList() {
        var str = "";
        $("input[class='cklist']:checked").each(function () {
            str += $(this).val() + ",";
        });
        str = str.substring(0, str.length - 1);
        var url = "<?php echo $this->createUrl('orderManage/export');?>";
        window.location.href = url + '?ids=' + str;
    }


</script>
<div class="main mhead">
    <div class="snav">直接下单管理 » 进线列表</div>
    <form action="<?php echo $this->createUrl('orderManage/index'); ?>" id="serchForm">
        <div class="mt10">
            下单日期：
            <input type="text" id="start_date" class="ipt" style="width:150px;font-size: 15px;" name="start_date"
                   value="<?php echo $this->get('start_date') ? $this->get('start_date') : date('Y-m-d 00:00', time()); ?>"
                   placeholder="起始日期"
                   onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'})"/> 一
            <input type="text" id="end_date" class="ipt" style="width:150px;font-size: 15px;" name="end_date"
                   value="<?php echo $this->get('end_date') ? $this->get('end_date') : date('Y-m-d 00:00', time() + 86400); ?>"
                   placeholder="结束日期"
                   onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'})"/>
            &nbsp;&nbsp;
            <select id="search_type" name="search_type">
                <option
                        value="channel_code" <?php echo $this->get('search_type') == 'channel_code' ? 'selected' : ''; ?>>
                    渠道编码
                </option>
                <option
                        value="article_code" <?php echo $this->get('search_type') == 'article_code' ? 'selected' : ''; ?>>
                    文案编码
                </option>
                <option value="order_code" <?php echo $this->get('search_type') == 'order_code' ? 'selected' : ''; ?>>
                    订单号
                </option>
                <option value="corder_code" <?php echo $this->get('search_type') == 'corder_code' ? 'selected' : ''; ?>>
                    客服订单号
                </option>
            </select>&nbsp;
            <input type="text" id="search_txt" style="width:120px;" name="search_txt" class="ipt"
                   value="<?php echo $this->get('search_txt'); ?>">
            &nbsp;
            <select id="search_type2" name="search_type2">
                <option
                        value="real_name" <?php echo $this->get('search_type2') == 'real_name' ? 'selected' : ''; ?>>
                    客户姓名
                </option>
                <option
                        value="mobile" <?php echo $this->get('search_type2') == 'mobile' ? 'selected' : ''; ?>>
                    客户电话
                </option>

            </select>&nbsp;
            <input type="text" id="search_txt2" style="width:120px;" name="search_txt2" class="ipt"
                   value="<?php echo $this->get('search_txt2'); ?>">

            客服部：
            <?php
            $customerServiceList = Dtable::toArr(CustomerServiceManage::model()->findAll());
            if ($_SERVER['HTTP_HOST'] == yii::app()->params['customer_config']['domain']) {
                $uid = Yii::app()->admin_user->uid;
                $ret = AdminUser::model()->find('csno=' . $uid);
                $name = CustomerServiceManage::model()->find('id=' . $ret['csdepartment']);
                ?>
                <select>
                    <option><?php echo $name['cname'] ?></option>
                </select>
            <?php } else {
                echo CHtml::dropDownList('csid', $this->get('csid'), CHtml::listData($customerServiceList, 'id', 'cname'),
                    array('empty' => '请选择',)
                );
            };
            ?>

            订单状态：
            <select id="is_export2" name="is_export2">
                <option>全部</option>
                <option value="export1" <?php echo $this->get('is_export2') == 'export1' ? 'selected' : ''; ?>>导出
                </option>
                <option value="unexport1" <?php echo $this->get('is_export2') == 'unexport1' ? 'selected' : ''; ?>>未导出
                </option>
            </select>

            <input type="checkbox" id="ckunexport"
                   name="ckunexport" <?php echo $this->get('ckunexport') ? "checked" : "" ?>>筛选未导出订单

        </div>
        <div class="mt10">
            发货日期：
            <input type="text" class="ipt" style="width:120px;font-size: 15px;" name="de_sdate"
                   value="<?php echo $this->get('de_sdate'); ?>" placeholder="起始日期"
                   onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/> 一
            <input type="text" class="ipt" style="width:120px;font-size: 15px;" name="de_edate"
                   value="<?php echo $this->get('de_edate'); ?>" placeholder="结束日期"
                   onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>&nbsp;&nbsp;
            商品：
            <input type="text" id="goods_name" style="width:120px;" name="goods_name" class="ipt"
                   value="<?php echo $this->get('goods_name'); ?>">&nbsp;&nbsp;
            下单商品：
            <input type="text" id="package_name" style="width:120px;" name="package_name" class="ipt"
                   value="<?php echo $this->get('package_name'); ?>"> &nbsp; &nbsp;

            支付方式：
            <select>
                <option>全部</option>
                <option>货到付款</option>
                <option>微信</option>
                <option>支付宝</option>
            </select>

            支付状态：
            <select>
                <option>全部</option>
                <option>已支付</option>
                <option>未支付</option>
                <option>退款</option>
            </select>

            <input type="submit" class="but" value="查询">
            &nbsp; &nbsp;
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="列表导出" onclick="exportList()" />', 'auth_tag' => 'orderManage_export')); ?>


        </div>
    </form>

    <div class="mt10 clearfix">
        <div class="l">
        </div>
        <form action="<?php echo $this->createUrl('orderManage/import'); ?>" method="post"
              enctype="multipart/form-data">
            <?php $this->check_u_menu(array('code' => '<input type="file"  name="filename"  />', 'auth_tag' => 'orderManage_import')); ?>
            &nbsp;
            <?php $this->check_u_menu(array('code' => '<input type="submit" name="submit" class="but2" value="回传导入" />', 'auth_tag' => 'orderManage_import')); ?>
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="导出勾选订单" onclick="exportCheckList()" />', 'auth_tag' => 'orderManage_ckexport')); ?>
        </form>
        <div class="r">
        </div>
    </div>
</div>
<div class="main mbody">
    <table class="tb fixTh">
        <thead>
        <tr>
            <th style="width:60px;"><?php $this->check_u_menu(array('code' => '<a href="javascript:void(0);"  onclick="all_check(\'.cklist\');">全选/</a>', 'auth_tag' => 'orderManage_del')); ?>
                <?php $this->check_u_menu(array('code' => '<a  href="javascript:void(0);"  onclick="all_uncheck(\'.cklist\');">反选</a>', 'auth_tag' => 'orderManage_del')); ?>
            </th>
            <th align='center'>ID</th>
            <th align='center'>下单时间</th>
            <th align='center'>订单号</th>
            <th align='center'>下单商品</th>
            <th align='center'>商品</th>
            <th align='center'>下单金额</th>
            <th align='center'>实际金额</th>
            <th align='center'>客户姓名</th>
            <th align='center'>电话</th>
            <th align='center'>地址</th>
            <th align='center'>留言</th>
            <th align='center'>渠道编码</th>
            <th align='center'>客服部</th>
            <th align='center'>文案编码</th>
            <th align='center'>客服订单号</th>
            <th align='center'>方便联系时间</th>
            <th align='center'>发货时间</th>
            <th align='center'>订单状态</th>
            <th align='center'>导出时间</th>
            <th align='center'>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php $edit = $this->check_u_menu(array('auth_tag' => 'orderManage_edit')); ?>
        <?php
        foreach ($page['listdata']['list'] as $k => $v) {
            ?>
            <tr>
                <td><?php $this->check_u_menu(array('code' => '<input type="checkbox" class="cklist" value="' . $v['id'] . '"/>', 'auth_tag' => 'orderManage_del')); ?></td>
                <td><?php echo $v['id'] ?></td>
                <td><?php echo date("m-d H:i:s", $v['add_time']) ?></td>
                <td><?php echo $v['order_code'] ?></td>
                <td><?php echo $page['listdata']['packageNames'][$v['package_id']] ?></td>
                <td><?php echo $page['listdata']['goodsNames'][$v['goods_id']] ?></td>
                <td><?php echo $v['package_price'] ?></td>
                <td><?php echo $v['real_price'] ?></td>
                <td><?php echo $v['real_name'] ?></td>
                <td><?php echo $v['mobile'] ?></td>
                <td><a href="#"
                       onclick="dialog({title:'收货地址',content:$(this).attr('data-clipboard-text')}).showModal();"
                       data-clipboard-text="<?php echo $v['province'] . ' ' . $v['city'] . ' ' . $v['region'] . '<br/>' . $v['detail_area']; ?>">点击查看</a>
                </td>
                <td><a href="#"
                       onclick="dialog({title:'留言',content:$(this).attr('data-clipboard-text')}).showModal();"
                       data-clipboard-text="<?php echo $v['remark']; ?>">点击查看</a>
                </td>
                <td><?php echo $page['listdata']['channelCodes'][$v['channel_id']]; ?></td>
                <td><?php echo $page['listdata']['csNames'][$v['customer_service_id']]; ?></td>
                <td><?php echo $v['article_code'] ?></td>
                <td><?php echo $v['corder_code'] ?></td>
                <td><?php echo vars::get_field_str('best_time', $v['best_time']) ?></td>
                <td><?php echo $v['delivery_date'] > 1509465600 ? date('m-d', $v['delivery_date']) : '-' ?></td>
                <td><?php echo vars::get_field_str('order_status', $v['order_status']) ?></td>
                <td><?php if ($v['is_export'] == 1) {
                        if ($v['export_date'] != 0) {
                            echo date('Y-m-d H:i', $v['export_date']);
                        } else {
                            echo "已导";
                        };
                    } else {
                        echo "<span style='color:red;'>未导</span>";
                    } ?></td>
                <td>
                    <?php if ($v['is_export'] == 1 && $edit) { ?>
                        <a href="<?php echo  $this->createUrl('orderManage/edit?id=' . $v['id'] . '&url=' . $page['listdata']['url']); ?>">编辑</a>
                    <?php }; ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
    <div class="clear"></div>

</div>
