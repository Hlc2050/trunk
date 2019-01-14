<?php require(dirname(__FILE__) . "/../common/menu.php"); ?>
<div class="admin-content">
    <a id="top" name="top"></a>
    <div style="margin-top: 50px;"></div>
    <div class="admin-content-body">
        <div class="am-u-sm-12 am-u-md-8 am-u-md-pull-4">
            <h3>微信号：<?php echo $page['info']['wechat_id'] ?></h3>
            <form class="am-form" method="post" action="<?php echo $this->createUrl('weChat/edit'); ?>">
                <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
                <input type="hidden" id="referer" name="referer" value="<?php echo $_SERVER['HTTP_REFERER']; ?>"/>

                <div class="am-g am-margin-top">
                    <div class="am-u-sm-4 am-u-md-2 am-text-right">
                        微信号ID
                    </div>
                    <div class="am-u-sm-8 am-u-md-4">
                        <input type="text" class="am-input-sm" id="wechat_id" name="wechat_id"
                               value="<?php echo $page['info']['wechat_id']; ?>">
                    </div>
                </div>
                <div class="am-g am-margin-top">
                    <div class="am-u-sm-4 am-u-md-2 am-text-right">客服部</div>
                    <div class="am-u-sm-8 am-u-md-10">
                        <?php
                        $customerServicelist = Dtable::toArr(CustomerServiceManage::model()->findAll());;
                        echo CHtml::dropDownList('customer_service_id', $page['info']['customer_service_id'], CHtml::listData($customerServicelist, 'id', 'cname'), array());
                        ?>

                    </div>
                </div>
                <div class="am-g am-margin-top">
                    <div class="am-u-sm-4 am-u-md-2 am-text-right">商品</div>
                    <div class="am-u-sm-8 am-u-md-10">
                        <?php echo $page['info']['id'] ? CHtml::dropDownList('goods_id', $page['info']['goods_id'], CHtml::listData($page['info']['goodsList'], 'goods_id', 'goods_name'), array('请选择')) :
                            CHtml::dropDownList('goods_id', $page['info']['goods_id'], array('请选择')); ?>
                    </div>
                </div>
                <div class="am-g am-margin-top">
                    <div class="am-u-sm-4 am-u-md-2 am-text-right">形象</div>
                    <div class="am-u-sm-8 am-u-md-10">
                        <?php echo $page['info']['id'] ? CHtml::dropDownList('character_id', $page['info']['character_id'], CHtml::listData($page['info']['characterList'], 'id', 'name'), array('请选择')) :
                            CHtml::dropDownList('character_id', $page['info']['character_id'], array('请选择')); ?>
                    </div>
                </div>
                <div class="am-g am-margin-top">
                    <div class="am-u-sm-4 am-u-md-2 am-text-right">推广人员</div>
                    <div class="am-u-sm-8 am-u-md-10">
                        <?php
                        $promotionStafflist = PromotionStaff::model()->getPromotionStaffList(1);
                        echo CHtml::dropDownList('promotion_staff_id', $page['info']['promotion_staff_id'], CHtml::listData($promotionStafflist, 'user_id', 'name'),
                            array(
                                'empty' => '请选择推广人员',
                                'class' => 'am-input-sm',
                            )
                        );
                        ?>
                    </div>
                </div>
                <div class="am-g am-margin-top">
                    <div class="am-u-sm-4 am-u-md-2 am-text-right">部门</div>
                    <div class="am-u-sm-8 am-u-md-10">
                        <?php echo $page['info']['id'] ? CHtml::dropDownList('department_id', $page['info']['department_id'], CHtml::listData($page['info']['departmentList'], 'groupid', 'groupname'), array('empty' => '请选择部门', 'class' => 'am-input-sm')) :
                            CHtml::dropDownList('department_id', $page['info']['department_id'], array('empty' => '请选择部门', 'class' => 'am-input-sm')); ?>
                    </div>
                </div>
                <div class="am-g am-margin-top">
                    <div class="am-u-sm-4 am-u-md-2 am-text-right">业务类型</div>
                    <div class="am-u-sm-8 am-u-md-10">
                        <?php
                        $businessTypes = Dtable::toArr(BusinessTypes::model()->findAll());
                        echo CHtml::dropDownList('business_type', $page['info']['business_type'], CHtml::listData($businessTypes, 'bid', 'bname'),
                            array(
                                'empty' => '选择业务类型',
                                'class' => 'am-input-sm',
                            )
                        );
                        ?>
                    </div>
                </div>
                <div class="am-g am-margin-top">
                    <div class="am-u-sm-4 am-u-md-2 am-text-right">计费方式</div>
                    <div class="am-u-sm-8 am-u-md-10">
                        <?php echo $page['info']['id'] ? CHtml::dropDownList('charging_type', $page['info']['charging_type'], CHtml::listData($page['info']['chargingTypeList'], 'charging_type', 'cname'), array('empty' => '计费方式', 'class' => 'am-input-sm')) :
                            CHtml::dropDownList('charging_type', $page['info']['charging_type'], array('empty' => '计费方式', 'class' => 'am-input-sm')); ?>
                    </div>
                </div>
                <div class="am-g am-margin-top">
                    <div class="am-u-sm-4 am-u-md-2 am-text-right">状态</div>
                    <div class="am-u-sm-8 am-u-md-10">
                        <select name="status">
                            <?php
                            foreach (vars::$fields['weChat_status'] as $key => $val) {
                                ?>
                                <option
                                        value="<?php echo $val['value']; ?>" <?php echo $val['value'] == $page['info']['status'] ? 'selected' : ''; ?>>
                                    <?php echo $val['txt']; ?>
                                </option>
                            <?php } ?>


                        </select>


                    </div>
                </div>
                <div class="am-g am-margin-top">
                    <div class="am-u-sm-4 am-u-md-2 am-text-right">
                        公众号落地页
                    </div>
                    <div class="am-u-sm-8 am-u-md-4">
                        <input type="text" class="am-input-sm" id="land_url" name="land_url"
                               value="<?php echo $page['info']['land_url']; ?>">
                    </div>
                </div>
                <div class="am-g am-margin-top">
                    <div class="am-u-sm-6 am-u-md-2 am-text-right">
                        <input type="button" class="am-btn am-radius am-btn-primary" value="返回"
                               onclick="javascript:window.history.go(-1)"/>
                    </div>
                    <div class="am-u-sm-6 am-u-md-4">
                        <input type="submit" class="am-btn am-radius am-btn-primary" value="保存"/>
                    </div>
                </div>
                <br>
            </form>
        </div>
        <div class="am-scrollable-horizontal">
            <p>修改记录</p>
            <table class="am-table am-text-nowrap am-scrollable-horizontal am-table-centered">
                <thead>
                <tr>
                    <th>时间</th>
                    <th>操作细节</th>
                    <th>操作用户</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($page['info']['change_logs'] as $val) { ?>
                    <tr>
                        <td class="am-text-middle"><?php echo date('Y-m-d H:i:s', $val['log_time']); ?></td>
                        <td><?php echo $val['log_details']; ?></td>
                        <td class="am-text-middle"><?php echo $val['csname_true']; ?></td>
                    </tr>
                <?php } ?>

                </tbody>
            </table>
        </div>
        <hr data-am-widget="divider" style="" class="am-divider am-divider-default"/>

    </div>

</div>

<script type="text/javascript">

    //客服部 商品联动
    $("#customer_service_id").change(secondChange);

    function secondChange() {
        jQuery.ajax({
            'type': 'POST',
            'url': 'getGoods',
            'data': {'cs_id': $("#customer_service_id").val()},
            'cache': false,
            'success': function (html) {
                jQuery("#goods_id").html(html);
                thirdChange();
            }
        });
        return false;
    }

    //商品、形象联动查询
    jQuery('body').on('change', '#goods_id', thirdChange);

    function thirdChange() {
        jQuery.ajax({
            'type': 'POST',
            'url': 'getCharacter',
            'data': {'goods_id': $("#goods_id").val()},
            'cache': false,
            'success': function (html) {
                jQuery("#character_id").html(html)
            }
        });
        return false;
    }

    $("#promotion_staff_id").change(function () {
        jQuery.ajax({
            'type': 'POST',
            'url': 'getDepartment',
            'data': {'promotion_staff_id': $("#promotion_staff_id").val()},
            'cache': false,
            'success': function (html) {
                jQuery("#department_id").html(html);
            }
        });
    });
    $("#business_type").change(function () {
        jQuery.ajax({
            'type': 'POST',
            'url': 'getChargingType',
            'data': {'business_type': $("#business_type").val()},
            'cache': false,
            'success': function (html) {
                jQuery("#charging_type").html(html);
            }
        });
    })

</script>
</body>
</html>