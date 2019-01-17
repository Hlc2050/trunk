<div class="main mhead">
    <form action="<?php echo $this->createUrl('effectCacheTable/index?'); ?>" method="get">
        <input type="hidden" name="condition" value="channel_id"/>
        <input type="hidden" name="group_id" value="0"/>
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
            推广人员:
            <?php
            $promotionStafflist = PromotionStaff::model()->getPromotionStaffList(0);
            echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($promotionStafflist, 'user_id', 'name'),
                array(
                    'empty' => '推广人员',
                )
            );
            ?>

            商品：
            <?php
            echo $this->get('csid') ? CHtml::dropDownList('goods_id', $this->get('goods_id'), CHtml::listData(CustomerServiceRelation::model()->getGoodsList($this->get('csid')), 'goods_id', 'goods_name'), array('empty' => '全部')) :
                CHtml::dropDownList('goods_id', $this->get('goods_id'), CHtml::listData(Goods::model()->findAll(), 'id', 'goods_name'), array('empty' => '全部'))
            ?>
        </div>

        <div class="mt10">

            合作商：
            <input type="text" id="partner_name" name="partner_name" class="ipt" style="width:130px"
                   value="<?php echo $this->get('partner_name'); ?>">

            渠道名称：
            <input type="text" id="channel_name" name="channel_name" class="ipt" style="width:130px"
                   value="<?php echo $this->get('channel_name'); ?>">

            业务类型：
            <?php
            $businessTypes = BusinessTypes::model()->getDXBusinessTypes();
            echo CHtml::dropDownList('bsid', $this->get('bsid'), CHtml::listData($businessTypes, 'bid', 'bname'),
                array(
                    'empty' => '请选择',
                )
            );
            ?>

            计费方式：
            <select id="chgId" name="chgId">
                <option value="">请选择</option>
                <?php $chargeList = vars::$fields['charging_type'];
                foreach ($chargeList as $key => $val) { ?>
                    <option
                            value="<?php echo $val['value']; ?>" <?php if ($this->get('chgId') != '' && $this->get('chgId') == $val['value']) echo 'selected'; ?>><?php echo $val['txt']; ?></option>
                <?php } ?>
            </select>
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
                <th>渠道名称</th>
                <th>业务类型</th>
                <th>计费方式</th>
                <th>商品</th>
                <th>
                    <?php echo helper::field_paixu(array('url' => '' . $this->createUrl('effectCacheTable/index') . '?p=' . $_GET['p']. $page['field_option'].'&condition=channel_id'. '', 'field_cn' => '发货量', 'field' => 'delivery_count')); ?>
                </th>
                <th>
                    <?php echo helper::field_paixu(array('url' => '' . $this->createUrl('effectCacheTable/index') . '?p=' . $_GET['p']. $page['field_option'].'&condition=channel_id'. '', 'field_cn' => '发货金额', 'field' => 'delivery_money')); ?>
                </th>
                <th>
                    <?php echo helper::field_paixu(array('url' => '' . $this->createUrl('effectCacheTable/index') . '?p=' . $_GET['p']. $page['field_option'].'&condition=channel_id'. '', 'field_cn' => '单项占整体比例', 'field' => 'delivery_money')); ?>
                </th>
            </tr>
            <tr>
                <th>合计</th>
                <th>-</th>
                <th>-</th>
                <th>-</th>
                <th>-</th>
                <th><?php echo $page['listdata']['delivery_count'] ? $page['listdata']['delivery_count'] : "-"; ?></th>
                <th><?php echo $page['listdata']['delivery_money'] ? $page['listdata']['delivery_money'] : "-"; ?></th>
                <th>-</th>
            </tr>
            </thead>

            <?php foreach ($page['listdata']['list'] as $k => $r) { ?>
                <tr>
                    <td><?php echo $page['listdata']['partner_name'][$r['partner_id']]; ?></td>
                    <td><?php echo $page['listdata']['channel_name'][$r['channel_id']] ?></td>
                    <td><?php echo $page['listdata']['business_name'][$r['business_type']]; ?></td>
                    <td><?php echo $r['charging_type'] ? vars::get_field_str('charging_type', $r['charging_type']) : null; ?></td>
                    <td><?php echo $page['listdata']['goods_name'][$r['goods_id']]; ?></td>
                    <td><?php echo $r['delivery_count']; ?></td>
                    <td><?php echo $r['delivery_money']; ?></td>
                    <td><?php if ($r['delivery_money'] != '') {
                            echo (round($r['delivery_money'] / $page['listdata']['delivery_money'], 5) * 100) . '%';
                        } ?></td>
                </tr>
            <?php } ?>

        </table>
    </form>
</div>

