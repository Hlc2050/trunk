<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<?php
//页面默认值
if (!isset($page['info'])) {
    $page['info']['id'] = '';
    $page['info']['wechat_id'] = '';
    $page['info']['goods_id'] = '';
    $page['info']['character_id'] = '';
    $page['info']['customer_service_id'] = '';
    $page['info']['promotion_staff_id'] = '';
    $page['info']['department_id'] = '';
    $page['info']['business_type'] = '';
    $page['info']['status'] = '';
}
?>
<div class="main mhead">
    <div class="snav">渠道管理 » 微信号列表 » <?php echo $page['info']['id'] ? '修改微信号' : '添加微信号' ?></div>
</div>
<div class="main mbody">
    <form method="post"
          action="<?php echo $this->createUrl($page['info']['id'] ? 'weChat/edit' : 'weChat/add'); ?>?p=<?php echo $_GET['p']; ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <input type="hidden" id="backurl" name="backurl" value="<?php echo $this->get('url'); ?>" />

        <table class="tb3">
            <tr>
                <th colspan="2" class="alignleft"><?php echo $page['info']['id'] ? '修改微信号' : '添加微信号' ?></th>
            </tr>
            <tr>
                <td width="120">微信号ID</td>
                <td class="alignleft">
                    <input type="text" class="ipt" id="wechat_id" name="wechat_id"
                           value="<?php echo $page['info']['wechat_id']; ?>"/>
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
                    helper::getServiceSelect('customer_service_id',$params);
                    ?>
                </td>
            </tr>
            <tr>
                <td>商品：</td>
                <td class="alignleft">
                    <?php echo $page['info']['id'] ? CHtml::dropDownList('goods_id', $page['info']['goods_id'], CHtml::listData($page['info']['goodsList'], 'goods_id', 'goods_name'), array('请选择')) :
                        CHtml::dropDownList('goods_id', $page['info']['goods_id'], array('请选择')); ?>
                </td>
            </tr>
            <tr>
                <td>形象：</td>
                <td class="alignleft">
                    <?php echo $page['info']['id'] ? CHtml::dropDownList('character_id', $page['info']['character_id'], CHtml::listData($page['info']['characterList'], 'id', 'name'), array('请选择')) :
                        CHtml::dropDownList('character_id', $page['info']['character_id'], array('请选择')); ?>
                </td>
            </tr>
            <tr>
                <td width="120">推广人员：</td>
                <td class="alignleft">
                    <?php
                    $promotionStafflist = PromotionStaff::model()->getPromotionStaffList(1);
                    echo CHtml::dropDownList('promotion_staff_id', $page['info']['promotion_staff_id'], CHtml::listData($promotionStafflist, 'user_id', 'name'),
                        array(
                            'empty' => '--请选择推广人员-',
                            'user_id' => 'promotion_staff_id',
                            'ajax' => array(
                                'type' => 'POST',
                                'url' => $this->createUrl('weChat/getDepartment'),
                                'update' => '#department_id',
                                'data' => array('promotion_staff_id' => 'js:$("#promotion_staff_id").val()'),
                            )
                        )
                    );
                    ?>
                </td>
            </tr>
            <tr>
                <td width="120">部门：</td>
                <td class="alignleft">
                    <?php echo $page['info']['id'] ? CHtml::dropDownList('department_id', $page['info']['department_id'], CHtml::listData($page['info']['departmentList'], 'groupid', 'groupname'), array('--请选择部门--')) :
                        CHtml::dropDownList('department_id', $page['info']['department_id'], array('--请选择部门--')); ?>
                    <a style="color: red">*选择部门前需要先选择推广人员</a>
                </td>
            </tr>
            <tr>
                <td width="120">业务类型：</td>
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
                    <?php echo $page['info']['id'] ? CHtml::dropDownList('charging_type', $page['info']['charging_type'], CHtml::listData($page['info']['chargingTypeList'], 'charging_type', 'cname'), array('--计费方式--')) :
                        CHtml::dropDownList('charging_type', $page['info']['charging_type'], array('--计费方式--')); ?>
                    <a style="color: red">*选择计费方式前需要先选择业务类型</a>
                </td>
            </tr>
            <tr>
                <td width="120">状态：</td>
                <td class="alignleft">
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
                </td>
            </tr>
            <tr>
                <td width="120">公众号落地页：</td>
                <td class="alignleft">
                    <input type="text" class="ipt" id="land_url" name="land_url"
                           value="<?php echo $page['info']['land_url']; ?>"/>
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="alignleft"><input type="submit" class="but" id="subtn" value="确定"/>
                    <input type="button" class="but" value="返回" onclick="window.location='<?php echo $this->get('url'); ?>'"/>
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
                        thirdChange();
                    }
                });
                return false;
            }

            //商品、形象联动查询
            jQuery(function ($) {
                jQuery('body').on('change', '#goods_id', thirdChange);
            });

            function thirdChange() {
                jQuery.ajax({
                    'type': 'POST',
                    'url': '/admin/weChat/getCharacter',
                    'data': {'goods_id': $("#goods_id").val()},
                    'cache': false,
                    'success': function (html) {
                        jQuery("#character_id").html(html)
                    }
                });
                return false;
            }

//            window.onload = function () {
//                secondChange();
//            };
        </script>
    </form>
</div>

