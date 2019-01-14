<?php
/**
 * Created by PhpStorm.
 * User: fang
 * Date: 2016/12/13
 * Time: 9:16
 */
require(dirname(__FILE__)."/../common/head.php");?>
    <div class="main mhead">
        <div class="snav">财务管理 » 打款管理</div>
    </div>
    <div class="main mbody">
        <form method="post" action="<?php echo $this->createUrl('infancePay/renew'); ?>?p=<?php echo $_GET['p'];?>" id="form1">
            <input type="hidden" name="id" value="<?php echo $page['info']['id']; ?>" />
            <input type="hidden" name="weixin_group_id" value="<?php echo $page['info']['weixin_group_id']; ?>" />
            <input type="hidden" name="charging_type" value="<?php echo $page['info']['charging_type']; ?>" />
            <input type="hidden" name="business_type" value="<?php echo $page['info']['business_type']; ?>" />
            <input type="hidden" name="unit_price" value="<?php echo $page['info']['unit_price']; ?>" />
            <input type="hidden" name="partner_id" value="<?php echo $page['info']['partner_id']; ?>" />
            <input type="hidden" name="channel_id" value="<?php echo $page['info']['channel_id']; ?>" />
            <input type="hidden" name="type" value="<?php echo $page['info']['type']; ?>" />
            <input type="hidden" id="backurl" name="backurl" value="<?php echo $this->get('url'); ?>">

            <table class="tb3">
                <tr>
                    <th colspan="2" class="alignleft">添加续费</th>
                </tr>
                <tr>
                    <td width="100">付款日期</td>
                    <td class="alignleft">
                        <input type="text" class="ipt"  id="pay_date"   name="pay_date" value="<?php echo date('Y-m-d',time()); ?>"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
                    </td>
                </tr>
                <tr>
                    <td width="100">上线日期</td>
                    <td class="alignleft">
                        <input type="text" class="ipt"  id="online_date"   name="online_date" value="<?php echo date('Y-m-d',time()); ?>"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
                    </td>
                </tr>
                <tr>
                    <td width="100">合作商</td>
                    <td class="alignleft">
                        <input type="text" class="ipt" name="" value="<?php $data=Partner::model()->findByPk($page['info']['partner_id']); echo $data['name'] ?>" disabled="true">
                    </td>
                </tr>
                <tr>
                    <td width="100">渠道名称</td>
                    <td class="alignleft">
                        <input class="ipt" name="" value="<?php $data=Channel::model()->findByPk($page['info']['channel_id']); echo $data['channel_name'] ?>" disabled="true">
                    </td>
                </tr>
                <tr>
                    <td width="100">收款人</td>
                    <td class="alignleft" >
                        <input class="ipt" name="payee" value="<?php  echo $page['info']['payee'] ?>" >
                    </td>
                </tr>
                <tr>
                    <td width="100">渠道编码</td>
                    <td class="alignleft" >
                        <input class="ipt" name="" value="<?php $data=Channel::model()->findByPk($page['info']['channel_id']); echo $data['channel_code'] ?>" disabled="true">
                    </td>
                </tr>
                <tr>
                    <td width="100">续费金额</td>
                    <td class="alignleft"><input class="ipt" name="pay_money" value=""></td>
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
            <input type="submit" class="but" id="subtn" value="确定" />
            <input type="button" class="but" value="返回" id="return_btn" onclick="window.location='<?php echo $this->createUrl('infancePay/index'); ?>?p=<?php echo $_GET['p'];?>'" />
        </form>
    </div>

<script>
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
        if (money=='' || fans_cost=='' || money==0 || fans_cost==0 || money<=0) {
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

