<!-- 特殊打款-->
<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<?php
if (!isset($page['info'])) {
    $page['info']['id'] = '';
    $page['info']['pay_date'] = strtotime(date("Y-m-d"));
    $page['info']['online_date'] = strtotime(date("Y-m-d"));
    $page['info']['partner_name'] = '';
    $page['info']['channel_code'] = '';
    $page['info']['weixin_group_id'] = '';
    $page['info']['unit_price'] = '';
    $page['info']['name'] = '';
    $page['info']['partner_id'] = '';
    $page['info']['pay_money'] = '';
    $page['info']['wechat_group_name'] = '';
    $page['info']['charging_type'] = '';
}

?>
    <style>
        .searchsBox a:hover {
            background: #eee;
        }

        #partner_tip {
            position: absolute;
            z-index: 1;
            overflow: hidden;
            left: 127px;
            top: 175px;
            border: 1px solid #4c5a5f;
            border-top: none;
            background-color: white;
            display: none;
        }

        .line {
            font-size: 12px;
            background: #aed34f;
            padding: 0 2px;
        }

        #wechat_tip {
            position: absolute;
            z-index: 1;
            overflow: hidden;
            left: 127px;
            top: 431px;
            border: 1px solid #4c5a5f;
            border-top: none;
            background-color: white;
            display: none;
        }

        .partner_i {
            width: 184px;
        }
    </style>
    <div class="main mhead">
        <div class="snav">财务管理 » 打款管理</div>
    </div>
    <div class="main mbody">
        <form method="post"
              action="<?php echo $this->createUrl($page['info']['id'] ? 'infancePay/edit' : 'infancePay/add'); ?>?spcl=1&p=<?php echo $_GET['p']; ?>">
            <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
            <input type="hidden" id="wechatId" name="wechatId" value="<?php echo $page['info']['weixin_group_id']; ?>">
            <input type="hidden" id="url" value="<?php echo $this->createUrl('infancePay/inputtip'); ?>">
            <table class="tb3">
                <tr>
                    <th colspan="2" class="alignleft"><?php echo $page['info']['id'] ? '编辑特殊打款' : '添加特殊打款' ?></th>
                </tr>
                <tr>
                    <td width="100">付款日期</td>
                    <td class="alignleft">
                        <input type="text" class="ipt" id="pay_date" name="pay_date"
                               value="<?php echo date('Y-m-d', $page['info']['pay_date']); ?>"
                               onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
                    </td>
                </tr>
                <tr>
                    <td width="100">上线日期</td>
                    <td class="alignleft">
                        <input type="text" class="ipt" id="online_date" name="online_date"
                               value="<?php echo date('Y-m-d', $page['info']['online_date']); ?>"
                               onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
                    </td>
                </tr>
                <tr>
                    <td width="100">合作商</td>
                    <td class="alignleft">
                        <input type="text" class="ipt" id="partner"
                               value="<?php $data = Partner::model()->findByPk($page['info']['partner_id']);
                               echo $data['name']; ?>">
                        <div id="partner_tip" class="line"></div>
                        <input type="hidden" name="partner_id" id="partner_id"
                               value="<?php echo $page['info']['partner_id'] ?>">
                    </td>
                </tr>
                <tr>
                    <td width="100">渠道名称</td>
                    <td class="alignleft">
                        <select name="channel_id" id="channel_id" style="width: 200px;border: 1px solid #888" onchange="clearWechatGroup();">
                            <?php
                            if (isset($channel)) {
                                foreach ($channel as $k => $v) {
                                    if ($channel[$k]['id'] == $page['info']['channel_id']) {
                                        echo "<option value='" . $channel[$k]['id'] . "' selected>" . $channel[$k]['channel_name'] . "</option>";
                                    } else {
                                        echo "<option value='" . $channel[$k]['id'] . "'>" . $channel[$k]['channel_name'] . "</option>";
                                    }
                                }
                            } else {
                                echo "<option>请选择</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td width="100">渠道编码</td>
                    <td class="alignleft">
                        <input class="ipt" name="channel_code" id="channel_code"
                               value="<?php $data = Channel::model()->findByPk($page['info']['channel_id']);
                               echo $data['channel_code']; ?>" disabled="true">
                    </td>
                </tr>
                <input name="bid" id="bid"
                       value="<?php echo $page['info']['id'] ? $page['info']['business_type'] : ''; ?>" type="hidden"/>
                <tr>
                    <td width="100">业务类型</td>
                    <td class="alignleft">
                        <input class="ipt" name="business_type" id="business_type"
                               value="<?php $business = BusinessTypes::model()->findByPk($data['business_type']);
                               echo $business['bname'] ?>" disabled="true">
                    </td>
                </tr>
                <tr>
                    <td>计费方式：</td>
                    <td class="alignleft">
                        <?php if ($page['info']['id']) {
                            $chargingTypes = BusinessTypeRelation::model()->getChargingTypes($page['info']['business_type']);
                            ?>
                            <select id="charging_type" name="charging_type" onchange="clearWechatGroup();">
                                <?php foreach ($chargingTypes as $v) { ?>
                                    <option
                                        value="<?php echo $v; ?>" <?php echo $page['info']['charging_type'] == $v ? 'selected' : '' ?>><?php echo vars::get_field_str('charging_type', $v) ?></option>
                                    <?php
                                } ?>
                            </select>
                            <?php
                        } else {
                            echo CHtml::dropDownList('charging_type', '', '',
                                array(
                                    'empty' => '请选择',
                                    'style' => "width: 200px;border: 1px solid #888",
                                    'onchange' => "clearWechatGroup();"
                                ));
                        } ?>
                    </td>
                </tr>
                <tr>
                    <td width="100">打款金额</td>
                    <td class="alignleft"><input class="ipt" name="pay_money"
                                                 value="<?php echo $page['info']['pay_money'] ?>"></td>
                </tr>

                <tr>
                    <td width="100">计费单价</td>
                    <td class="alignleft"><input class="ipt" name="unit_price"
                                                 value="<?php echo $page['info']['unit_price'] ?>"></td>
                </tr>
                <tr>
                    <td width="100">微信号小组</td>
                    <td class="alignleft">
                        <input type="text" class="ipt" id="wechat"
                               value="<?php $data = WeChatGroup::model()->findByPk($page['info']['weixin_group_id']);
                               echo $data['wechat_group_name'] ?>">
                        <div id="wechat_tip" class="line"></div>
                        <input type="hidden" name="weixin_group_id" id="wechat_id"
                               value="<?php echo $page['info']['weixin_group_id'] ?>">
                    </td>
                </tr>

            </table>
            <input type="submit" class="but" id="subtn" value="确定"/> <input type="button" class="but" value="返回"
                                                                            onclick="window.location='<?php echo $this->createUrl('infancePay/index'); ?>?p=<?php echo $_GET['p']; ?>'"/>
        </form>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            //合作商查询选择
            $('#partner').on('keyup focus', function () {
                var tip = $(this);
                var a = $('#partner_tip');
                var b = $('#partner_id');
                var c = $('#channel_id');
                a.show();
                $.ajax({
                    type: "GET",
                    url: $('#url').val(),
                    data: "search_txt=" + $('#partner').val(),
                    success: function (data) {
                        if (data != '') {
                            data = JSON.parse(data);
                            var layer;
                            layer = "<table>";
                            $.each(data, function (k, v) {
                                layer += "<tr><td class='partner_i'>" + data[k]['name'] + "<input type='hidden' value='" + data[k]['id'] + "'></td></tr>"
                            });
                            layer += "</table>";
                            a.empty();
                            a.append(layer);
                            $('.partner_i').click(function () {
                                $('#partner').val($(this).text());
                                b.val($(this).find('input').val());
                                a.hide();
                                c.empty();
                                $.ajax({
                                    type: "GET",
                                    url: $('#url').val(),
                                    data: "partner=" + b.val()+"&bid=2",
                                    success: function (data) {
                                        var channel_name;
                                        channel_name = "<option value=''>请选择</option>";
                                        data = JSON.parse(data);
                                        $.each(data, function (k, v) {
                                            channel_name += "<option   value='" + data[k]['id'] + "' data-code='" + data[k]['channel_code'] + "' data-business='" + data[k]['bname'] + "' data-bid='" + data[k]['bid'] + "'>" + data[k]['channel_name'] + "</option>"
                                        });
                                        c.append(channel_name);
                                    }
                                })
                            });
                        }
                    }
                });
                tip.blur(function () {
                    setTimeout(function () {
                        a.hide()
                    }, 3000)
                });

            });

            //微信号小组查找选择
            $('#wechat').on('keyup focus', function () {
                var myInput = $(this);
                var x = $('#wechat_tip');
                var y = $('#wechat_id');
                x.show();
                $.ajax({
                    type: "POST",
                    url: '/admin/infancePay/inputtip',
                    data: {
                        'id': $("#id").val(),
                        'search_wechat_txt': $("#wechat").val(),
                        'bid': 2,
                        'charging_type': $("#charging_type").val(),
                        'online_date': $("#online_date").val(),
                        'partner_id': $("#partner_id").val(),
                        'status':4
                    },
                    success: function (data) {
                        if (data != '') {
                            data = JSON.parse(data);
                            var layer2;
                            layer2 = "<table>";
                            $.each(data, function (k, v) {
                                layer2 += "<tr><td class='partner_i'>" + data[k]['wechat_group_name'] + "<input type='hidden' value='" + data[k]['id'] + "'></td></tr>"
                            });
                            layer2 += "</table>";
                            x.empty();
                            x.append(layer2);
                            $('.partner_i').click(function () {
                                $('#wechat').val($(this).text());
                                y.val($(this).find('input').val());
                                x.hide();
                            });
                        } else {
                            $(".partner_i").remove()
                        }
                    }
                });
                myInput.blur(function () {
                    setTimeout(function () {
                        x.hide()
                    }, 3000)
                });

            });

            //选择渠道自动生成 渠道编码 业务类型和计费方式
            jQuery(function ($) {
                jQuery('body').on('change', '#channel_id', function () {
                    jQuery.ajax({
                        'type': 'POST',
                        'url': '/admin/infancePay/getChargingTypes',
                        'data': {'bid': $("#channel_id").find('option:selected').attr('data-bid')},
                        'cache': false,
                        'success': function (html) {
                            console.log(html);
                            jQuery("#charging_type").html(html)
                        }
                    });
                    $('#channel_code').val($(this).find('option:selected').attr('data-code'));
                    $('#business_type').val($(this).find('option:selected').attr('data-business'));
                    $('#bid').val($(this).find('option:selected').attr('data-bid'))
                });
            });
        })
        function clearWechatGroup(){
            $obj =$('#weixin_group_id');
            if($obj.val()!=''){
                $obj.val('');
                $('#wechat').val('');
            }
        }
    </script>
