<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<?php
if (!isset($page['info'])) {
    $page['info']['id'] = '';
    $page['info']['stat_date'] = strtotime(date("Y-m-d"));
    $page['info']['name'] = '';
    $page['info']['fixed_cost'] = '';
    $page['info']['remark'] = '';
}

?>
    <style>
        .searchsBox a:hover {
            background: #eee;
        }

    </style>

    <script>
        $(document).ready(function () {
            $('.channel_name').on('keyup focus', function () {
                $('.searchsBox').show();
                var myInput = $(this);
                var key = myInput.val();
                var postdata = {search_type: 'keys', search_txt: key};
                $.getJSON('<?php echo $this->createUrl('partner/channelIndex') ?>?jsoncallback=?', postdata, function (reponse) {
                    try {
                        if (reponse.state < 1) {
                            alert(reponse.msg);
                            return false;
                        }
                        var html = '';
                        for (var i = 0; i < reponse.data.list.length; i++) {
                            html += '<a href="javascript:void(0);" data-id="' + reponse.data.list[i].id + '" data-channel_code="' + reponse.data.list[i].channel_code + '" data-partnerId="' + reponse.data.list[i].partner_id + '" ' +
                                'data-channelName="' + reponse.data.list[i].channel_name + '"  ' +
                                'data-partnerName="' + reponse.data.list[i].partnerName + '"  ' +
                                'onmouseDown="getTipsValue(this);"   style="display:block;font-size:14px;padding:4px 10px;">' + reponse.data.list[i].channel_name + '(' + reponse.data.list[i].partnerName + ')</a>';
                        }
                        var s_height = myInput.height();
                        var top = myInput.offset().top + s_height;
                        var left = myInput.offset().left;
                        var width = myInput.width();
                        $('.searchsBox').remove();
                        $('body').append('<div class="searchsBox" style="position:absolute;top:' + top + 'px;left:' + left + 'px;background:#fff;z-index:2;border:1px solid #ccc;width:' + width + 'px;">' + html + '</div>');
                    } catch (e) {
                        alert(e.message)
                    }
                });

                myInput.blur(function () {
                    $('.searchsBox').hide();
                })
            });
            $('.wechat').on('keyup focus', function () {
                $('.searchsBox').show();
                var myInput = $(this);
                var key = myInput.val();
                var fixeddata = {search_type: 'keys', search_txt: key};
                $.getJSON('<?php echo $this->createUrl('fixedCost/wechatIndex') ?>?jsoncallback=?', fixeddata, function (reponse) {
                    try {
                        if (reponse.state < 1) {
                            alert(reponse.msg);
                            return false;
                        }
                        var html = '';
                        for (var i = 0; i < reponse.data.list.length; i++) {
                            html += '<a href="javascript:void(0);" data-id="' + reponse.data.list[i].id + '" data-wechat_id="' + reponse.data.list[i].wechat_id +
                                '" data-partnerId="' + reponse.data.list[i].partner_id + '" ' +
                                'onmouseDown="getWechatValue(this);"   style="display:block;font-size:14px;padding:4px 10px;">' + reponse.data.list[i].wechat_id + '</a>';
                        }
                        var s_height = myInput.height();
                        var top = myInput.offset().top + s_height;
                        var left = myInput.offset().left;
                        var width = myInput.width();
                        $('.searchsBox').remove();
                        $('body').append('<div class="searchsBox" style="position:absolute;top:' + top + 'px;left:' + left + 'px;background:#fff;z-index:2;border:1px solid #ccc;width:' + width + 'px;">' + html + '</div>');
                    } catch (e) {
                        alert(e.message)
                    }
                });

                myInput.blur(function () {
                    $('.searchsBox').hide();
                })
            })
        });
        function getWechatValue(ele) {
            var myobj = $(ele);
            var id = myobj.attr('data-id');
            var wechat_id = myobj.attr('data-wechat_id');
            $('.wechat').val(wechat_id);
            $('.wechat_id').val(id);
        }
        function getTipsValue(ele) {//alert(ele)
            var myobj = $(ele);
            var id = myobj.attr('data-id');
            var channel_name = myobj.attr('data-channelName');
            var channel_code = myobj.attr('data-channel_code');
            var partner_id = myobj.attr('data-partnerId');
            var partner_name = myobj.attr('data-partnerName');
            $('.channel_name').val(channel_name);
            $('.channel_code').html(channel_code);
            $('#channel_id').val(id);
            $('.partner_id').val(partner_id);
            $('.partner_name').html(partner_name);
        }


    </script>

    <div class="main mhead">
        <div class="snav">财务管理 » 修正成本管理</div>
    </div>
    <div class="main mbody">
        <form method="post"
              action="<?php echo $this->createUrl($page['info']['id'] ? 'fixedCost/edit' : 'fixedCost/add'); ?>">
            <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
            <input type="hidden" id="backurl" name="backurl" value="<?php echo $this->get('url'); ?>"/>

            <table class="tb3">
                <tr>
                    <th colspan="2" class="alignleft"><?php echo $page['info']['id'] ? '编辑修正成本' : '添加修正成本' ?></th>
                </tr>
                <?php $bid = $this->get('bid') ? $this->get('bid') : 2;
                if (!$page['info']['id']) : ?>
                    <div class="tab_box">
                        <?php
                        foreach (vars::$fields['businessTypes'] as $k => $r) { ?>
                            <a href="<?php $this->createUrl('fixedCost/add'); ?>?bid=<?php echo $r['value']; ?>" <?php if ($bid == $r['value']) echo 'class="current"'; ?>><?php echo $r['txt']; ?></a>
                        <?php }
                        ?>
                    </div>
                <?php endif; ?>
                <tr>
                    <td width="100">上线日期：</td>
                    <td class="alignleft">
                        <input type="text" class="ipt" id="stat_date" name="stat_date"
                               value="<?php echo date('Y-m-d', $page['info']['stat_date']); ?>"
                               onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
                    </td>
                </tr>
                <tr>
                    <td width="100">推广人员：</td>
                    <td class="alignleft">
                        <?php
                        $userArr = $this->toArr(PromotionStaff::model()->getPromotionStaffList(1));
                        echo CHtml::dropDownList('tg_uid', $page['info']['tg_uid'], CHtml::listData($userArr, 'user_id', 'name'),
                            array('empty' => '请选择', 'style' => 'width:200px'));
                        ?>
                    </td>
                </tr>
                <tr>
                    <td width="100">归属客服部：</td>
                    <td class="alignleft">
                        <?php
                        $params = array(
                            'select' => $page['info']['customer_service_id'],
                            'htmlOptions' => array(
                                'empty' => '请选择',
                                'style' => 'width:200px'
                            ),
                        );
                        helper::getServiceSelect('customer_service_id',$params);
                        ?>&nbsp;
                    </td>
                </tr>
                <tr>
                    <td width="100">选择渠道：</td>
                    <td class="alignleft">
                        <input type="text" class="ipt channel_name" style="width: 199px;" name="channel_name"
                               value="<?php $data = Channel::model()->findByPk($page['info']['channel_id']);
                               echo $data['channel_name']; ?>" size="30"/>
                        <input type="hidden" class="ipt channel_id" id="channel_id" name="channel_id"
                               value="<?php echo $page['channel']['id']; ?>" size="30"/>
                    </td>
                </tr>
                <tr>
                    <td width="100">合作商：</td>
                    <td class="alignleft">
                        <span
                            class="partner_name"><?php $data = Partner::model()->findByPk($page['info']['partner_id']);
                            echo $data['name']; ?></span>
                        <input type="hidden" class="ipt partner_id" name="partner_id"
                               value="<?php echo $page['channel']['partner_id']; ?>"/>

                    </td>
                </tr>
                <tr>
                    <td width="100">渠道编码：</td>
                    <td class="alignleft">
                        <span
                            class="channel_code"><?php $data = Channel::model()->findByPk($page['info']['channel_id']);
                            echo $data['channel_code']; ?></span>
                        <input type="hidden" class="ipt channel_code" id="channel_code" name="channel_code"
                               value="<?php echo $page['channel']['channel_code']; ?>"/>

                    </td>
                </tr>
                <tr>
                    <td width="100">商品：</td>
                    <td class="alignleft">
                        <?php
                        $userArr = $this->toArr(Goods::model()->findAll());
                        echo CHtml::dropDownList('goods_id', $page['info']['goods_id'], CHtml::listData($userArr, 'id', 'goods_name'),
                            array('empty' => '请选择', 'style' => 'width:200px'));
                        ?>
                    </td>
                </tr>
                <?php if ($page['info']['id']) : ?>
                    <tr>
                        <td width="100">业务类型：</td>
                        <td class="alignleft">
                            <input class="ipt" name="business_type"
                                   value="<?php echo BusinessTypes::model()->findByPk($page['info']['business_type'])->bname ?>"
                                   disabled="true">
                        </td>
                    </tr>
                <?php endif; ?>

                <tr>
                    <td width="100">微信号：</td>
                    <td class="alignleft">
                        <input class="ipt wechat" name="wechat"
                               value="<?php $data = WeChat::model()->findByPk($page['info']['weixin_id']);
                               echo $data['wechat_id'] ?>">
                        <input type="hidden" class="ipt wechat_id" name="wechat_id"
                               value="<?php echo $page['info']['weixin_id'] ?>">
                    </td>
                </tr>
                <tr>
                    <td>计费方式：</td>
                    <td class="alignleft">
                        <?php if ($page['info']['id']) {
                            $chargingTypes = BusinessTypeRelation::model()->getChargingTypes($page['info']['business_type']);
                            ?>
                            <select id="charging_type" name="charging_type" style='width:200px'>
                                <?php foreach ($chargingTypes as $v) { ?>
                                    <option
                                        style='width:200px'
                                        value="<?php echo $v; ?>" <?php echo $page['info']['charging_type'] == $v ? 'selected' : '' ?>><?php echo vars::get_field_str('charging_type', $v) ?></option>
                                    <?php
                                } ?>
                            </select>
                            <?php
                        } else {
                            $chargingTypes = BusinessTypeRelation::model()->getChargingTypes($bid); ?>
                            <select id="charging_type" name="charging_type" style='width:200px'>
                                <?php foreach ($chargingTypes as $v) { ?>
                                    <option
                                        style='width:200px'
                                        value="<?php echo $v; ?>" <?php echo $page['info']['charging_type'] == $v ? 'selected' : '' ?>><?php echo vars::get_field_str('charging_type', $v) ?></option>
                                    <?php
                                } ?>
                            </select>
                            <?php
                        } ?>
                    </td>
                </tr>
                <?php if ($bid == 2) { ?>
                    <input type="hidden" id="business_id" name="business_id" value="2"/>
<!--                    <tr>-->
<!--                        <td width="100">修正第三方金额：</td>-->
<!--                        <td class="alignleft"><input class="ipt" name="fixed_piwik_cost"-->
<!--                                                     value="--><?php //echo $page['info']['fixed_piwik_cost'] ?><!--"></td>-->
<!--                    </tr>-->

                <?php } else { ?>
                    <input type="hidden" id="business_id" name="business_id" value="1"/>
                <?php } ?>
                <tr>
                    <td width="100">修正友盟金额：</td>
                    <td class="alignleft"><input class="ipt" name="fixed_cost"
                                                 value="<?php echo $page['info']['fixed_cost'] ?>"></td>
                </tr>

            </table>
            <input type="submit" class="but" id="subtn" value="确定"/> <input type="button" class="but" value="返回"
                                                                            onclick="window.location='<?php echo $page['info']['id']?$this->get('url'):$this->createUrl('fixedCost/index'); ?>'"/>
        </form>
    </div>
