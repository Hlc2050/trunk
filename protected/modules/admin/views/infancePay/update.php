<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<?php
$is_add = 1;
if (!isset($page['info'])) {
    $pay_id = 0;
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

    $page['channel']['last_pay']['pay_date'] = '--';
    $page['channel']['last_pay']['online_date'] = '--';
    $page['channel']['last_pay']['pay_money'] = '--';
    $page['channel']['last_pay']['fans_input'] = '--';
    $page['channel']['last_pay']['fans_cost'] = '--';
    $page['channel']['last_pay']['roi'] = '--';
    $page['channel']['last_pay']['real_cost'] = '--';
    $page['channel']['last_pay']['real_fans'] = '--';
    $page['channel']['last_pay']['online_day'] = '--';
    $page['channel']['last_pay']['fans_radio'] = '--';
}else{
    $pay_id =  $page['info']['id'];
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
        .last_div{
            border: solid 1px #cccccc;
            margin-left: 10px;
            height: 350px;
            width: 350px;
            display: block;
            float: right;
        }
        .last_div h1{
            padding: 10px;
            text-align: center;
            font-size: 14px;
            background-color: #f6f8fa;

        }
        .last_ul {
            margin-top: 15px;
        }
        .last_ul li{
            height: 35px;
            padding-left: 10px;
            font-size: 14px;
        }
    </style>
    <div class="main mhead">
        <div class="snav">财务管理 » 打款管理</div>
    </div>
    <div class="main mbody" >
        <form method="post" id="form1"
              action="<?php echo $this->createUrl($page['info']['id'] ? 'infancePay/edit' : 'infancePay/add'); ?>?p=<?php echo $_GET['p']; ?>" >
            <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
            <input type="hidden" id="wechatId" name="wechatId" value="<?php echo $page['info']['weixin_group_id']; ?>">
            <input type="hidden" id="url" value="<?php echo $this->createUrl('infancePay/inputtip'); ?>">
            <input type="hidden" id="backurl" name="backurl" value="<?php echo $this->get('url'); ?>">
            <table class="tb3">
                <tr>
                    <th colspan="2" class="alignleft"><?php echo $page['info']['id'] ? '编辑打款' : '添加打款' ?></th>
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
                <tr>
                    <td width="100">收款人</td>
                    <td class="alignleft">
                        <input type="text" class="ipt" id="payee" name="payee" value="<?php echo $page['info']['payee']; ?>">
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
                                                 value="<?php echo $page['info']['unit_price'] ?>">
                        <span id="price_mask"></span>
                    </td>
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
                <tr>
                    <td width="100">预计进粉成本</td>
                    <td class="alignleft"><input class="ipt" name="fans_cost"
                                                 value="<?php echo $page['info']['fans_cost'] ?>"></td>
                </tr>
                <tr>
                    <td width="100">预计进粉量</td>
                    <td class="alignleft"><input class="ipt" name="fans_input"
                                                 value="<?php echo $page['info']['fans_input'] ?>"></td>
                </tr>
                <tr>
                    <td width="100">预计上线天数</td>
                    <td class="alignleft"><input class="ipt" name="online_day"
                                                 value="<?php echo $page['info']['online_day'] ?>"></td>
                </tr>
                <tr>
                    <td width="100">预计每日进粉量</td>
                    <td class="alignleft"><input class="ipt" name="day_fans_input"
                                                 value="<?php echo $page['info']['day_fans_input'] ?>"></td>
                </tr>

            </table>

            <input type="hidden" name="action_tag" value="<?php echo $page['action_tag'];?>">
            <input type="submit" class="but" id="subtn" value="确定"/>
            <input type="button" class="but" value="返回" id="return_btn"
                                                                            onclick="window.location='<?php echo$this->get('url'); ?>'"/>
        </form>
    </div>
    <script type="text/javascript">
        var charge_mask = <?php echo json_encode(vars::$fields['charging_price']);?>;
        $(document).ready(function () {
            var charge_type_select = $("#charging_type").val();
            if (charge_type_select) {
                $("#price_mask").html(charge_mask[charge_type_select]['mask']);
            }
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
                                    data: "partner=" + b.val(),
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
                        'bid': $("#bid").val(),
                        'charging_type': $("#charging_type").val(),
                        'online_date': $("#online_date").val(),
                        'partner_id': $("#partner_id").val(),
                    },
                    success: function (data) {
                        if (data != '') {
                            console.log(data);
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
                    var pay_id = <?php echo $pay_id;?>;
                    jQuery.ajax({
                        'type': 'POST',
                        'url': '/admin/infancePay/getChargingTypes',
                        'data': {'pay_id':pay_id,'bid': $("#channel_id").find('option:selected').attr('data-bid'),'cid':$(this).val()},
                        'cache': false,
                        'success': function (data) {
                            var res = JSON.parse(data);
                            var charge_type = res.charging_type;
                            var html = '';
                            for (var i=0;i<charge_type.length;i++) {
                                html += '<option value="'+charge_type[i]['value']+'">'+charge_type[i]['txt']+'</option>';
                            }
                            if (charge_type[0] != undefined){
                                var sel_type = charge_type[0]['value'];
                                $("#price_mask").text(charge_mask[sel_type]['mask']);
                            }
                            $("#charging_type").html(html);
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
        //计费方式修改
        $("#charging_type").on('change',function () {
            var type_select = $("#charging_type").val();
            if (type_select) {
                $("#price_mask").text(charge_mask[type_select]['mask']);
            }
        });
        //打款金额、预计进粉成本、预计进粉量、预计上线天数值修改
        $("input[name='pay_money']").on('blur',function () {
            changeFansInput();
        });
        $("input[name='fans_cost']").on('blur',function () {
            changeFansInput();
        });
        $("input[name='fans_input']").on('blur',function () {
            changeDayFansInput();
        });
        $("input[name='online_day']").on('blur',function () {
            changeDayFansInput();
        });

        //同步预计进粉量值
        function changeFansInput() {
            var money = $("input[name='pay_money']").val();
            var fans_cost = $("input[name='fans_cost']").val();
            var fans_input = $("input[name='fans_input']");
            if (money=='' || fans_cost=='' || money==0 || fans_cost==0 || money<0) {
                fans_input.val('');
            }else{
                var fans_input_val = parseInt(money/fans_cost);
                fans_input.val(fans_input_val);
            }
            changeDayFansInput();
        }
        //同步预计上线天数值
        function changeDayFansInput() {
            var fans_input = $("input[name='fans_input']").val();
            var online_day = $("input[name='online_day']").val();
            var day_fans_input = $("input[name='day_fans_input']");
            if (fans_input=='' || online_day=='' || fans_input==0 || online_day==0) {
                day_fans_input.val('');
            }else{
                var day_fans_input_val = parseInt(fans_input/online_day);
                day_fans_input.val(day_fans_input_val);
            }
        }
        $("#subtn").click(function () {
            $(this).css('background','#ccc');
            $(this).val('数据保存中...');
            $("#form1").submit();
            $("#return_btn").hide();
            $(this).attr('disabled','disabled');
        });
    </script>
