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
    <div class="main mhead">
        <div class="snav">渠道管理 » 渠道数据表管理</div>
    </div>
    <div class="main mbody" onload="Init()">
        <form name=frm method="post" action="<?php echo $this->createUrl($page['info']['id'] ? 'channelData/edit' : 'channelData/add'); ?>?p=<?php echo $_GET['p']; ?>">
            <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
            <input type="hidden" id="fpay_id" name="fpay_id" value="<?php echo $page['info']['finance_pay_id']; ?>"/>
            <input type="hidden" id="backurl" name="backurl" value="<?php echo $this->get('url'); ?>"/>
            <table class="tb3">
                <?php if (!$page['info']['id']) { ?>
                    <tr>
                        <th colspan="2" class="alignleft">添加渠道数据</th>
                    </tr>
                    <tr>
                        <td width="100">合作商</td>
                        <td>
                            <?php
                            $partnerList = $this->toArr(Partner::model()->findAll());
                            echo CHtml::dropDownList('partnerId_temp', '', CHtml::listData($partnerList, 'id', 'name'),
                                array(
                                    'empty' => '请选择',
                                    'id' => 'partnerId_temp',
                                    'style' => 'display:none'
                                )
                            );
                            ?>
                            <span id="demo">
                        <?php
                        echo CHtml::dropDownList('partnerId', '', '',
                            array(
                                'empty' => '请选择',
                                'id' => 'partnerId',
                            )
                        );
                        ?>
                    </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input class="ipt" style="font-size:small;width: 100px" name="txt" onkeyup="SelectTip(0)"/>
                        </td>
                    </tr>
                    <tr>
                        <td width="120">渠道名称：</td>
                        <td class="alignleft">
                            <?php echo CHtml::dropDownList('channel_id', '', array('请选择')); ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="120">上线日期：</td>
                        <td class="alignleft">
                            <?php echo CHtml::dropDownList('online_date', '', array('请选择')); ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="120">渠道编码：</td>
                        <td class="alignleft">
                            <input type="text" class="ipt" id="channelCode"
                                   disabled/>
                        </td>
                    </tr>
                    <tr>
                        <td width="120">计费方式：</td>
                        <td class="alignleft">
                            <input type="text" class="ipt" id="chgId" disabled/>
                        </td>
                    </tr>
                    <tr>
                        <td width="120">计费单价：</td>
                        <td class="alignleft">
                            <input type="text" class="ipt" id="unitPrice"
                                   disabled/>
                        </td>
                    </tr>
                    <tr>
                        <td width="120">单位：</td>
                        <td class="alignleft">
                            <input type="text" class="ipt" id="unit" value="元/次" disabled/>
                        </td>
                    </tr>
                    <tr>
                        <td width="120">计费公式：</td>
                        <td class="alignleft">
                            <input type="text" class="ipt" id="formula" disabled/>
                        </td>
                    </tr>
                    <tr>
                        <td width="120">微信号小组：</td>
                        <td class="alignleft">
                            <input type="text" class="ipt" id="wechat_group" disabled/>
                        </td>
                    </tr>
                    <tr>
                        <td width="120">业务类型：</td>
                        <td class="alignleft">
                            <input type="text" class="ipt" id="business_type" disabled/>
                        </td>
                    </tr>
                <?php } else { ?>
                    <tr>
                        <th colspan="2" class="alignleft">修改渠道数据</th>
                    </tr>
                    <tr>
                        <td width="120">合作商</td>
                        <td>
                            <?php echo $page['info']['partner']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="120">渠道名称：</td>
                        <td class="alignleft">
                            <?php echo $page['info']['channel_name']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="120">上线日期：</td>
                        <td class="alignleft">
                            <?php echo $page['info']['online_date']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="120">渠道编码：</td>
                        <td class="alignleft">
                            <?php echo $page['info']['channel_code']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="120">计费方式：</td>
                        <td class="alignleft">
                            <?php echo $page['info']['charging_type']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="120">计费单价：</td>
                        <td class="alignleft">
                            <?php echo $page['info']['unit_price']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="120">单位：</td>
                        <td class="alignleft">元/次</td>
                    </tr>
                    <tr>
                        <td width="120">计费公式：</td>
                        <td class="alignleft">
                            <?php echo $page['info']['formula']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="120">微信号小组：</td>
                        <td class="alignleft">
                            <?php echo $page['info']['wechat_group']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="100">业务类型</td>
                        <td class="alignleft">
                            <?php echo $page['info']['business_type']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span style="color: red">提示；修改渠道数据无法改动以上项目</span>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td width="120">粉丝数</td>
                    <td class="alignleft"><input class="ipt" name="fans"
                                                 value="<?php echo $page['info']['fans'] ?>"></td>
                </tr>

                <tr>
                    <td width="120">男粉比例</td>
                    <td class="alignleft"><input class="ipt" name="man_fans"
                                                 value="<?php echo $page['info']['man_fans'] ?>">
                        <span style="color: red">*用小数表示，大于0，小于1,男女粉比例相加小于等于1</span>
                    </td>
                </tr>

                <tr>
                    <td width="120">女粉比例</td>
                    <td class="alignleft"><input class="ipt" name="women_fans"
                                                 value="<?php echo $page['info']['women_fans'] ?>">
                        <span style="color: red">*用小数表示，大于0，小于1,男女粉比例相加小于等于1</span>
                    </td>
                </tr>

                <tr>
                    <td width="120">预计进粉率</td>
                    <td class="alignleft"><input class="ipt" name="add_fans"
                                                 value="<?php echo $page['info']['add_fans'] ?>">
                        <span style="color: red">*用小数表示，大于0，小于1</span>
                    </td>

                </tr>

                <tr>
                    <td width="120">推广前第一天阅读量</td>
                    <td class="alignleft"><input class="ipt" name="first"
                                                 value="<?php echo $page['info']['first'] ?>"></td>
                </tr>

                <tr>
                    <td width="120">推广前第二天阅读量</td>
                    <td class="alignleft"><input class="ipt" name="second"
                                                 value="<?php echo $page['info']['second'] ?>"></td>
                </tr>

                <tr>
                    <td width="120">推广前第三天阅读量</td>
                    <td class="alignleft"><input class="ipt" name="third"
                                                 value="<?php echo $page['info']['third'] ?>"></td>
                </tr>

                <tr>
                    <td width="120">推广前第四天阅读量</td>
                    <td class="alignleft"><input class="ipt" name="fourth"
                                                 value="<?php echo $page['info']['fourth'] ?>"></td>
                </tr>

                <tr>
                    <td width="120">推广前第五天阅读量</td>
                    <td class="alignleft"><input class="ipt" name="fifth"
                                                 value="<?php echo $page['info']['fifth'] ?>"></td>
                </tr>
                <tr>
                    <td width="120">推广阅读量</td>
                    <td class="alignleft"><input class="ipt" name="read_num"
                                                 value="<?php echo $page['info']['read_num'] ?>"></td>
                </tr>
                <tr>
                    <td>图文编码</td>
                    <td>
                        <?php
                        $userArr = $this->toArr(MaterialArticleTemplate::model()->findAll());
                        echo CHtml::dropDownList('material_article_id', $page['info']['material_article_id'], CHtml::listData($userArr, 'id', 'article_code'),
                            array(
                                'empty' => '请选择',
                                'style' => 'width:200px'
                            ));
                        ?>
                    </td>
                </tr>

            </table>
            <input type="submit" class="but" id="subtn" value="确定"/>
            <input type="button" class="but" value="返回"
                   onclick="window.location='<?php echo $this->get('url'); ?>'"/>
        </form>
        <script language="javascript">
            /**
             * 模糊搜索合作商
             * @type {Array}
             */
            var TempArr = [];//存贮option
            function Init() {
                var SelectObj = document.frm.elements["partnerId_temp"];
                /*先将数据存入数组*/
                with (SelectObj)
                    for (i = 0; i < length; i++)TempArr[i] = [options[i].text, options[i].value];
            }
            function SelectTip(flag) {
                var TxtObj = document.frm.elements["txt"];
                console.log(TxtObj.value);
                var SelectObj = document.getElementById("demo");
                var Arr = [];
                with (SelectObj) {
                    var SelectHTML = innerHTML.match(/<[^>]*>/)[0];
                    if (TxtObj.value == '')Arr[Arr.length] = "<option value=''>请选择</option>"
                    else {
                        for (i = 0; i < TempArr.length; i++)
                            if (TempArr[i][0].indexOf(TxtObj.value) != -1 || flag)//若找到以txt的内容，添option。若flag为true,对下拉框初始化
                                Arr[Arr.length] = "<option value='" + TempArr[i][1] + "'>" + TempArr[i][0] + "</option>"
                    }
                    innerHTML = SelectHTML + Arr.join();
                    secondChange();
                }
            }
            window.onload = function () {
                Init();
            };

            //合作商 渠道联动搜索
            jQuery(function ($) {
                jQuery('body').on('change', '#partnerId', secondChange);
            });

            function secondChange() {
                jQuery.ajax({
                    'type': 'POST',
                    'url': '/admin/promotion/getChannelData',
                    'data': {'partnerId': $("#partnerId").val()},
                    'cache': false,
                    'success': function (html) {
                        jQuery("#channel_id").html(html);
                        thirdChange();
                    }
                });
                return false;
            }

            //渠道 上线日期联动查询
            jQuery(function ($) {
                jQuery('body').on('change', '#channel_id', thirdChange);
            });

            function thirdChange() {
                jQuery.ajax({
                    'type': 'POST',
                    'url': '/admin/promotion/getOnlineChannelDate',
                    'data': {'channel_id': $("#channel_id").val()},
                    'cache': false,
                    'success': function (html) {
                        jQuery("#online_date").html(html)
                        fourthChange();
                    }
                });
                return false;
            }

            //上线日期变化 其他数据也出现
            jQuery(function ($) {
                jQuery('body').on('change', '#online_date', fourthChange);
            });

            function fourthChange() {
                jQuery.ajax({
                    'type': 'POST',
                    'url': '/admin/promotion/getOtherData',
                    'data': {'onlineDate': $("#online_date").val(), 'channel_id': $("#channel_id").val()},
                    'cache': false,
                    'success': function (result) {
                        console.log(result);
                        json = eval('(' + result + ')');
                        jQuery("#fpay_id").val(json.fpay_id);//打款id
                        jQuery("#channelCode").val(json.channelCode);//渠道编码
                        jQuery("#chgId").val(json.chgId);//计费方式
                        jQuery("#unitPrice").val(json.unitPrice);//单价
                        jQuery("#formula").val(json.formula);//计费公式
                        jQuery("#wechat_group").val(json.wechat_group);//微信号小组
                        jQuery("#business_type").val(json.business_type);//业务类型
                    }
                });
                return false;
            }

        </script>
    </div>
