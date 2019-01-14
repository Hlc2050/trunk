<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">效果统计 » 微信效果表</div>
    <div class="mt10">
        <!--搜索栏-->
        <div class="mt10">
            <form action="<?php echo $this->createUrl('weChatEffect/index'); ?>">
                微信号：
                <input type="text" id="wechat_id" name="wechat_id" class="ipt" style="width:150px;"
                       value="<?php echo $this->get('wechat_id'); ?>">&nbsp;&nbsp;
                客服部：
                <?php
                helper::getServiceSelect('csid');
                ?>
                &nbsp;&nbsp;
                商品：
                <?php
                echo $this->get('csid') ? CHtml::dropDownList('goods_id', $this->get('goods_id'), CHtml::listData(CustomerServiceRelation::model()->getGoodsList($this->get('csid')), 'goods_id', 'goods_name'), array('empty' => '全部')) :
                    CHtml::dropDownList('goods_id', $this->get('goods_id'),CHtml::listData(Goods::model()->findAll(), 'id', 'goods_name'), array('empty' => '全部'))
                ?>&nbsp;&nbsp;
                推广人员:
                <?php
                $promotionStafflist = PromotionStaff::model()->getPromotionStaffList(0,1);
                echo CHtml::dropDownList('tg_id', $this->get('tg_id'), CHtml::listData($promotionStafflist, 'user_id', 'name'),
                    array(
                        'empty' => '推广人员',
                    )
                );
                ?>

                <div class="mt10">
                    日期：
                    <input type="text" size="20" class="ipt" name="start_date"
                           id="start_date"
                           value="<?php echo $page['first_day'] ? $page['first_day'] : date("Y-m-d", mktime(0, 0, 0, date('m'), 1, date('Y'))); ?>"
                           onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>-
                    <input type="text" size="20" class="ipt" name="end_date"
                           id="end_date"
                           value="<?php echo $page['last_day'] ? $page['last_day'] : date("Y-m-d", time()); ?>"
                           onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>&nbsp;
                    <a href="#"
                       onclick="$('#start_date').val('<?php echo date("Y-m-d", mktime(0, 0, 0, date('m'), 1, date('Y'))); ?>');$('#end_date').val('<?php echo date("Y-m-d", mktime(0, 0, 0, date('m'), date('t'), date('Y'))); ?>')">本月</a>&nbsp;
                    <?php $a = helper::lastMonth(time()); ?>
                    <a href="#"
                       onclick="$('#start_date').val('<?php echo date('Y-m-d', strtotime($a[0])); ?>');$('#end_date').val('<?php echo date('Y-m-d', strtotime($a[1])); ?>')">上月</a>&nbsp;
                    <a href="#" onclick="$('#start_date').val('');$('#end_date').val('')">清空</a>&nbsp;
                    <input type="submit" class="but" value="查询">
                </div>
            </form>
        </div>
    </div>
</div>
<!--数据 -->
<div class="main mbody">
    <table id="exportTable" class="tb fixTh" style="width: 100%">
        <thead>
        <tr>
            <th>微信号ID</th>
            <th>预估发货金额</th>
            <th>投入金额</th>
            <th>进粉量</th>
            <th>均粉产出</th>
            <th>平均进粉成本</th>
        </tr>
        <tr id="totalInfo"></tr>
        </thead>
        <tbody>
        <?php
        $total_money = $total_estimate_money = $total_fans_count = 0;

        foreach ($page['info'] as $key => $value) { ?>
            <tr>
                <td><?php echo $value['wechat_id']; ?></td>
                <td><?php echo round($value['estimate_money']); ?></td>
                <td><?php echo round($value['money']); ?></td>
                <td><?php echo $value['fans_count']; ?></td>
                <td><?php echo $value['fans_avg']; ?></td>
                <td><?php echo $value['fans_cost']; ?></td>
            </tr>
            <?php
            $total_fans_count += $value['fans_count'];
            $total_estimate_money += $value['estimate_money'];
            $total_money += $value['money'];

        }
        $total_fans_cost = $total_fans_count==0?0:round($total_money / $total_fans_count);
        $total_fans_avg = $total_fans_count==0?0:round($total_estimate_money / $total_fans_count);
        ?>
        </tbody>
    </table>

    <script type="text/javascript">
        $(function () {
            //将合计填数据加上去
            var html = "<th>合计</th> " +
                "<th><?php echo round($total_estimate_money);?></th><th><?php echo round($total_money);?></th> <th><?php echo $total_fans_count;?></th>" +
                "<th><?php echo $total_fans_avg;?></th><th><?php echo $total_fans_cost;?></th>";

            $("#totalInfo").html(html);
        })
    </script>
</div>
