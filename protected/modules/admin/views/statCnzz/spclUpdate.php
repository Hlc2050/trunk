<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<?php
//页面默认值
if (!isset($page['info'])) {
    $page['info']['id'] = '';
    $page['info']['stat_date'] = time();
    $page['info']['domain_id'] = '';
    $page['info']['finance_pay_id'] = '';
    $page['info']['domain'] = '';
    $page['info']['ip'] = '';
    $page['info']['uv'] = '';
    $page['info']['pv'] = '';
}
?>
<div class="main mhead">
    <div class="snav">第三方统计 » 友盟统计</div>
</div>
<div class="main mbody" onload="Init()">
    <form name=frm method="post"
          action="<?php echo $this->createUrl($page['info']['id'] ? 'statCnzz/edit' : 'statCnzz/add'); ?>?p=<?php echo $_GET['p']; ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <input type="hidden" id="fpay_id" name="fpay_id" value="<?php echo $page['info']['finance_pay_id']; ?>"/>
        <input type="hidden" id="tg_id" name="tg_id" value="<?php echo $page['info']['promotion_staff_id']; ?>"/>
        <table class="tb3">

            <tr>
                <th colspan="2" class="alignleft">添加特殊统计</th>
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
                    <input type="text" class="ipt" name="wechat_group" id="wechat_group" disabled/>
                </td>
            </tr>
            <tr>
                <th colspan="2" align="left">流量统计</th>

            </tr>
            <tr>
                <td width="100">日期：</td>
                <td class="alignleft">
                    <input type="text" class="ipt" id="stat_date" name="stat_date"
                           value="<?php echo date('Y-m-d', $page['info']['stat_date']); ?>"
                           onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>

                </td>
            </tr>

            <tr>
                <td width="100">受访域名：</td>
                <td class="alignleft">
                    <input type="text" class="ipt" id="domain" name="domain" value="特殊" readonly/>
                </td>
            </tr>
            <tr>
                <td width="100">IP：</td>
                <td class="alignleft">
                    <input type="text" class="ipt" id="ip" name="ip" value="<?php echo $page['info']['ip']; ?>"/>
                </td>
            </tr>
            <tr>
                <td width="100">uv：</td>
                <td class="alignleft">
                    <input type="text" class="ipt" id="uv" name="uv" value="<?php echo $page['info']['uv']; ?>"/>

                </td>
            </tr>
            <tr>
                <td width="100">pv：</td>
                <td class="alignleft">
                    <input type="text" class="ipt" id="pv" name="pv" value="<?php echo $page['info']['pv']; ?>"/>

                </td>
            </tr>
            <tr>
                <td></td>
                <td class="alignleft"><input type="submit" class="but" id="subtn" value="确定"/> <input type="button"
                                                                                                      class="but"
                                                                                                      value="返回"
                                                                                                      onclick="window.location='<?php echo $this->createUrl('statCnzz/index'); ?>?p=<?php echo $_GET['p']; ?>'"/>
                </td>
            </tr>

        </table>
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
                    'url': '/admin/promotion/getChannel',
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
                    'url': '/admin/promotion/getOnlineDate',
                    'data': {'channel_id': $("#channel_id").val(), 'spcl': 1},
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
                        json = eval('(' + result + ')');
                        jQuery("#fpay_id").val(json.fpay_id);//打款id
                        jQuery("#channelCode").val(json.channelCode);//渠道编码
                        jQuery("#chgId").val(json.chgId);//计费方式
                        jQuery("#unitPrice").val(json.unitPrice);//单价
                        jQuery("#formula").val(json.formula);//计费公式
                        jQuery("#wechat_group").val(json.wechat_group);//微信号小组
                        jQuery("#tg_id").val(json.tg_id);//推广人员
                    }
                });
                return false;
            }
        </script>
    </form>
</div>
