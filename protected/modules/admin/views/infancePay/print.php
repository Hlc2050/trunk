<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<style>
    h2{
        width:  100%;
        font-size: 16px;
        text-align: center;
    }
    table {
        width: 90%;
        margin-top: 20px;
        margin-left: 5%;
    }
    table td {
        border: 1px solid #e6ebf2;
        padding: 7px;
    }
    p {
        text-align: right;
        width: 90%;
        margin-left: 5%;
        /*font-size: 14px;*/
    }
</style>
<style media="print">
    @page {
        size: auto;  /* auto is the initial value */
        margin: 0; /* this affects the margin in the printer settings */
    }
    table {
        font-size: 12px;
        border-spacing: 0;
        border-collapse: collapse;
    }
    table td {
        border: 1px solid #e6ebf2;
        padding: 4px;
    }
</style>
<div class="main mbody" style="padding-bottom: 30px">
    <p><button style="padding: 3px  6px" onclick="preview()" id="print_btn">打印</button></p>
    <p>生成时间:<?php echo date('Y-m-d',$page['create_time']);?></p>
    <h2 style="margin-top: 5px;"><?php echo $page['pay_info']['partner'].'-'.$page['pay_info']['channel'];?>-收款人:<?php echo $page['last_info']['payee']?$page['last_info']['payee']:'无' ?>-打款明细</h2>
    <table>
        <thead>
        <tr>
            <td colspan="2" style="border-right: none">计划信息</td>
            <td colspan="2" style="border-left: none;border-right: none"></td>
            <td colspan="2" style="border-left: none" align="right">推广人员：<?php echo $page['pay_info']['user_name']?></td>
        </tr>
        </thead>
        <tr>
            <td><strong>付款日期</strong></td><td><?php echo $page['pay_info']['pay_date'];?></td>
            <td><strong>上线日期</strong></td><td><?php echo $page['pay_info']['online_date'];?></td>
            <td><strong>打款金额</strong></td><td><?php echo $page['pay_info']['pay_money'];?></td>
        </tr>
        <tr>
            <td><strong>合作商</strong></td><td><?php echo $page['pay_info']['partner'];?></td>
            <td><strong>渠道名称</strong></td><td><?php echo $page['pay_info']['channel'];?></td>
            <td><strong>客服部</strong></td><td><?php echo $page['pay_info']['service_names'];?></td>
        </tr>
        <tr>
            <td><strong>业务类型</strong></td><td><?php echo $page['pay_info']['business_type'];?></td>
            <td><strong>渠道类型</strong></td><td><?php echo $page['pay_info']['channel_type'];?></td>
            <td><strong>计费方式</strong></td><td><?php echo $page['pay_info']['charging_type'];?></td>
        </tr>
        <tr>
            <td><strong>预计进粉成本</strong></td><td><?php echo $page['pay_info']['fans_cost'];?></td>
            <td><strong>预计进粉量</strong></td><td><?php echo $page['pay_info']['fans_input'];?></td>
            <td><strong>预计上线天数</strong></td><td><?php echo $page['pay_info']['online_day'];?></td>
        </tr>
        <tr>
            <td><strong>预计每日进粉量</strong></td>
            <td><?php echo $page['pay_info']['day_fans_input'];?></td>
            <td><strong>上线微信号个数</strong></td>
            <td><?php echo $page['pay_info']['wechat_count'];?></td>
            <td colspan="3"></td>
        </tr>
        <tbody>
        </tbody>
    </table>
    <table>
        <tr>
            <td colspan="3"><strong>余额信息</strong></td>
            <td>渠道余额</td><td><?php echo $page['pay_info']['channel_balance'];?></td>
        </tr>
        <tbody>
        </tbody>
    </table>
    <table>
        <thead>
        <tr>
            <td colspan="16">渠道效果汇总</td>
        </tr>
        <tr>
            <td colspan="4">渠道类型</td>
            <td colspan="4"><?php echo $page['channel_type']['type_name'];?></td>
            <td colspan="4">进粉成本</td>
            <td colspan="4"><?php echo $page['channel_type']['fans_input'];?></td>
        </tr>
        <?php $rules = unserialize($page['channel_type']['type_rule']) ?>
        <tr>
            <?php for($i=0;$i<16;$i++){ ?>
                <td><?php echo $i+1;?>天</td>
            <?php }?>
        </tr>
        <tr>
            <?php for($i=0;$i<16;$i++){ ?>
                <td><?php echo $rules[$i]?$rules[$i]:0;?>%</td>
            <?php }?>
        </tr>
        <tr>
            <?php for($i=16;$i<30;$i++){ ?>
                <td><?php echo $i+1;?>天</td>
            <?php }?>
            <td>40天</td>
            <td>60天</td>
        </tr>
        <tr>
            <?php for($i=16;$i<30;$i++){ ?>
                <td><?php echo $rules[$i]?$rules[$i]:0;?>%</td>
            <?php }?>
            <td><?php echo $rules[30]?$rules[30]:0;?>%</td>
            <td><?php echo $rules[31]?$rules[31]:0;?>%</td>
        </tr>
        </thead>
        <?php if($page['print_channel_data']){ ?>
            <tbody>
            <tr>
                <td colspan="6"><strong>/</strong></td>
                <td colspan="5"><strong>本月</strong></td>
                <td colspan="5"><strong>最近两个月</strong></td>
            </tr>
            <tr>
                <td colspan="6"><strong>投入金额</strong></td>
                <td colspan="5"><?php echo round($page['print_channel_data']['current']['money'],2);?></td>
                <td colspan="5"><?php echo round($page['print_channel_data']['last']['money'],2);?></td>
            </tr>
            <tr>
                <td colspan="6"><strong>预估发货金额</strong></td>
                <td colspan="5"><?php echo $page['print_channel_data']['current']['order_money'];?></td>
                <td colspan="5"><?php echo $page['print_channel_data']['last']['order_money'];?></td>
            </tr>
            <tr>
                <td colspan="6"><strong>ROI</strong></td>
                <td colspan="5"><?php echo $page['print_channel_data']['current']['ROI'];?>%</td>
                <td colspan="5"><?php echo $page['print_channel_data']['last']['ROI'];?>%</td>
            </tr>
            <tr>
                <td colspan="6"><strong>进粉量</strong></td>
                <td colspan="5"><?php echo $page['print_channel_data']['current']['fans_input'];?></td>
                <td colspan="5"> <?php echo $page['print_channel_data']['last']['fans_input'];?></td>
            </tr>
            <tr>
                <td colspan="6"><strong>发货量</strong></td>
                <td colspan="5"><?php echo $page['print_channel_data']['current']['order_count'];?></td>
                <td colspan="5"><?php echo $page['print_channel_data']['last']['order_count'];?></td>
            </tr>
            <tr>
                <td colspan="6"><strong>进粉成本</strong></td>
                <td colspan="5"><?php echo $page['print_channel_data']['current']['fans_cost'];?></td>
                <td colspan="5"><?php echo $page['print_channel_data']['last']['fans_cost'];?></td>
            </tr>
            <tr>
                <td colspan="6"><strong>渠道转化</strong></td>
                <td colspan="5"><?php echo $page['print_channel_data']['current']['channel_rate'];?>%</td>
                <td colspan="5"><?php echo $page['print_channel_data']['last']['channel_rate'];?>%</td>
            </tr>
            <tr>
                <td colspan="6"><strong>上线天数</strong></td>
                <td colspan="5"><?php echo $page['print_channel_data']['current']['online_days'];?></td>
                <td colspan="5"><?php echo $page['print_channel_data']['last']['online_days'];?></td>
            </tr>
            </tbody>
        <?php }else{ ?>
            <tr>
                <td colspan="16">无渠道效果汇总数据</td>
            </tr>
        <?php }?>

    </table>
    <table>
        <thead>
        <tr>
            <td colspan="4">合作商效果</td>
        </tr>
        </thead>
        <?php if($page['print_partner_data']){ ?>
            <tbody>
            <tr>
                <td><strong>/</strong></td>
                <td><strong>本月</strong></td>
                <td><strong>最近两个月</strong></td>
            </tr>
            <tr>
                <td><strong>投入金额</strong></td>
                <td><?php echo $page['print_partner_data']['current']['money'];?></td>
                <td><?php echo $page['print_partner_data']['last']['money'];?></td>
            </tr>
            <tr>
                <td><strong>预估发货金额</strong></td>
                <td><?php echo $page['print_partner_data']['current']['order_money'];?></td>
                <td><?php echo $page['print_partner_data']['last']['order_money'];?></td>
            </tr>
            <tr>
                <td><strong>ROI</strong></td>
                <td><?php echo $page['print_partner_data']['current']['ROI'];?>%</td>
                <td><?php echo $page['print_partner_data']['last']['ROI'];?>%</td>
            </tr>
            <tr>
                <td><strong>进粉量</strong></td>
                <td><?php echo $page['print_partner_data']['current']['fans_input'];?></td>
                <td><?php echo $page['print_partner_data']['last']['fans_input'];?></td>
            </tr>
            <tr>
                <td><strong>发货量</strong></td>
                <td><?php echo $page['print_partner_data']['current']['order_count'];?></td>
                <td><?php echo $page['print_partner_data']['last']['order_count'];?></td>
            </tr>
            <tr>
                <td><strong>进粉成本</strong></td>
                <td><?php echo $page['print_partner_data']['current']['fans_cost'];?></td>
                <td><?php echo $page['print_partner_data']['last']['fans_cost'];?></td>
            </tr>
            <tr>
                <td><strong>渠道转化</strong></td>
                <td><?php echo $page['print_partner_data']['current']['channel_rate'];?>%</td>
                <td><?php echo $page['print_partner_data']['last']['channel_rate'];?>%</td>
            </tr>
            </tbody>
        <?php }else{ ?>
            <tr>
                <td colspan="4">无合作商效果数据</td>
            </tr>
        <?php }?>
    </table>
</div>

<script>
    function preview() {
        $("#print_btn").hide();
        $(document).attr("title","<?php echo $page['pay_info']['partner'].'-'.$page['pay_info']['channel'];?>-打款明细-<?php echo date('Ymd',$page['create_time']);?>");
        window.print();
        $("#print_btn").show();
        return false;
    }
</script>

