<div class="main mhead">
    <form action="<?php echo $this->createUrl('effectCacheTable/index?'); ?>">
        <input type="hidden" name="condition" value="partner_id"/>
        <input type="hidden" name="group_id" value="1"/>
        <div class="mt10">
            发货日期：
            <input type="text" size="20" class="ipt" style="width:130px;" name="start_delivery_date"
                   id="start_delivery_date"
                   value="<?php echo $page['start_delivery_date']; ?>"
                   onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>-
            <input type="text" size="20" class="ipt" style="width:130px;" name="end_delivery_date"
                   id="end_delivery_date"
                   value="<?php echo $page['end_delivery_date']; ?>"
                   onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>&nbsp;

            加粉日期：
            <input type="text" size="20" class="ipt" style="width:130px;" name="start_addfan_date"
                   id="start_addfan_date"
                   value="<?php echo $page['start_addfan_date']; ?>"
                   onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>-
            <input type="text" size="20" class="ipt" style="width:130px;" name="end_addfan_date"
                   id="end_addfan_date"
                   value="<?php echo $page['end_addfan_date']; ?>"
                   onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>


            推广人员：
            <?php
            $promotionStafflist = PromotionStaff::model()->getPromotionStaffList();
            echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($promotionStafflist, 'user_id', 'name'),
                array('empty' => '请选择')
            );
            ?>

            商品：
            <?php
            echo $this->get('csid') ? CHtml::dropDownList('goods_id', $this->get('goods_id'), CHtml::listData(CustomerServiceRelation::model()->getGoodsList($this->get('csid')), 'goods_id', 'goods_name'), array('empty' => '全部')) :
                CHtml::dropDownList('goods_id', $this->get('goods_id'), CHtml::listData(Goods::model()->findAll(), 'id', 'goods_name'), array('empty' => '全部'))
            ?>
            <input type="submit" class="but" value="查询">
        </div>
    </form>
</div>

<div class="main mbody">
    <form>
        <table class="tb fixTh">
            <thead>
            <tr>
                <th>合作商</th>
                <th>商品</th>
                <th>
                    <a href="<?php
                    $group_id = 1;
                    $field = 'delivery_count';
                    $field_name = $this->get('field');
                    if ($field_name == "delivery_count") {
                        if (1 == $this->get('delivery_count_asc')) {
                            $icon = '▼';
                            $delivery_count_asc = 2;
                        } elseif (2 == $this->get('delivery_count_asc')) {
                            $delivery_count_asc = 0;
                            $icon = '▲';
                        } else {
                            $delivery_count_asc = 1;
                        }
                    } else {
                        $delivery_count_asc = 1;
                    }

                    if ($field_name == "delivery_money") {
                        if (1 == $this->get('delivery_money_asc')) {
                            $icon = '▼';
                            $delivery_money_asc = 2;
                        } elseif (2 == $this->get('delivery_money_asc')) {
                            $delivery_money_asc = 0;
                            $icon = '▲';
                        }else{
                            $delivery_money_asc = 1;
                        }
                    } else {
                        $delivery_money_asc = 1;
                    }
                    echo $this->createUrl('effectCacheTable/index?field=' . $field . '&delivery_count_asc=' . $delivery_count_asc . $page['field_option'] . '&condition=partner_id');

                    ?>">发货单量<?php if ($this->get('field') == "delivery_count") {
                            echo $icon;
                        } ?></a>
                </th>
                <th>
                    <a>
                        <a href="<?php
                        $field = 'delivery_money';
                        echo $this->createUrl('effectCacheTable/index?field=' . $field . '&delivery_money_asc=' . $delivery_money_asc . $page['field_option'] . '&condition=partner_id');
                        ?>">实际发货金额<?php if ($this->get('field') == "delivery_money") {
                                echo $icon;
                            } ?></a>
                </th>
                <th>
                    <a href="<?php
                    $field = 'delivery_money';
                    echo $this->createUrl('effectCacheTable/index?field=' . $field . '&delivery_money_asc=' . $delivery_money_asc . $page['field_option'] . '&condition=partner_id');
                    ?>">占比<?php if ($this->get('field') == "delivery_money") {
                            echo $icon;
                        } ?></a>
                </th>
            </tr>
            <tr>
                <th>合计</th>
                <th>-</th>
                <th><?php echo $page['listdata']['delivery_count'] ? $page['listdata']['delivery_count'] : "-"; ?></th>
                <th><?php echo $page['listdata']['delivery_money'] ? $page['listdata']['delivery_money'] : "-"; ?></th>
                <th>-</th>
            </tr>
            </thead>
            <?php foreach ($page['listdata']['list'] as $k => $r) { ?>
                <tr>
                    <td><?php echo $page['listdata']['partner_name'][$r['partner_id']]; ?></td>
                    <td><?php echo $page['listdata']['goods_name'][$r['goods_id']]; ?></td>
                    <td><?php echo $r['delivery_count']; ?></td>
                    <td><?php echo $r['delivery_money']; ?></td>
                    <td><?php if ($r['delivery_count'] != null) {
                            echo (round($r['delivery_money'] / $page['listdata']['delivery_money'], 5) * 100) . '%';
                        } ?></td>
                </tr>
            <?php } ?>
        </table>
    </form>
</div>