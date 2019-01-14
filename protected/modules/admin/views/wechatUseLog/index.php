<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">渠道管理 » 微信号使用记录</div>
    <form action="<?php echo $this->createUrl('wechatUseLog/index'); ?>">
        <div class="mt10">
            微信号：
            <input type="text" id="wechat_id" name="wechat_id" class="ipt" style="width:130px;"
                   value="<?php echo $this->get('wechat_id'); ?>">&nbsp;

            客服部：
            <?php
            helper::getServiceSelect('csid');
            ?>
            &nbsp;&nbsp
            商品：
            <?php
            if ($this->get('csid')) $goods_list = CustomerServiceRelation::model()->getGoodsList($this->get('csid'));
            else $goods_list = Goods::model()->findAll();
            echo CHtml::dropDownList('goods_id', $this->get('goods_id'), CHtml::listData($goods_list, 'id', 'goods_name'), array(
                'empty' => '全部',
                'ajax' =>
                    array(
                        'type' => 'POST',
                        'url' => $this->createUrl('/admin/wechatUseLog/getCharacter'),
                        'update' => '#character_id',
                        'data' => array('goods_id' => 'js:$("#goods_id").val()'),
                    ),
            ))
            ?>&nbsp;&nbsp;
            形象：
            <?php
            $character = array();
            echo $this->get('goods_id') ? CHtml::dropDownList('character_id', $this->get('character_id'), CHtml::listData($this->getCharacterByGoodId($this->get('goods_id')), 'linkage_id', 'linkage_name'), array('empty' => '全部')) :
                CHtml::dropDownList('character_id', $this->get('character_id'), CHtml::listData(Linkage::model()->get_linkage_data(19), 'linkage_id', 'linkage_name'), array('empty' => '全部'))
            ?>&nbsp;&nbsp;
            <?php
            $businessTypes = Dtable::toArr(BusinessTypes::model()->findAll());
            echo CHtml::dropDownList('bs_id', $this->get('bs_id'), CHtml::listData($businessTypes, 'bid', 'bname'),
                array(
                    'empty' => '业务类型',
                    'ajax' =>
                        array(
                            'type' => 'POST',
                            'url' => $this->createUrl('/admin/wechatUseLog/getChargeType'),
                            'update' => '#charge_id',
                            'data' => array('bs_id' => 'js:$("#bs_id").val()'),
                        ),
                )
            );
            ?>&nbsp;
            <?php
            if ($this->get('bs_id') ) $chargeTypes = $this->getChargeByBid($this->get('bs_id'));
            else {
                $chargeTypes = vars::$fields['charging_type'];
            }
            foreach ($chargeTypes as $key=>$value) {
                $chargeTypes[$key]['charging_type'] = array_key_exists('charging_type',$value) !== false ? $value['charging_type'] : $value['value'];
                $chargeTypes[$key]['charging_name'] = vars::get_field_str('charging_type', $value['value']);
            }
            echo CHtml::dropDownList('charge_id', $this->get('charge_id'), CHtml::listData($chargeTypes, 'charging_type', 'charging_name'),
                array(
                    'empty' => '计费方式',
                )
            );
            ?>&nbsp;
            <?php
            $departmentList = $this->toArr(AdminGroup::model()->findAll());
            echo CHtml::dropDownList('dt_id', $this->get('dt_id'), CHtml::listData($departmentList, 'groupid', 'groupname'),
                array(
                    'empty' => '部门',
                )
            );
            ?>&nbsp;
            <?php
            $promotionStafflist = PromotionStaff::model()->getPromotionStaffList(1);
            echo CHtml::dropDownList('promotion_staff_id', $this->get('promotion_staff_id'), CHtml::listData($promotionStafflist, 'user_id', 'name'),
                array(
                    'empty' => '推广人员',
                )
            );
            ?>
        </div>
        <div class="mt10">
            使用时间：
            <input type="text" size="20" class="ipt" style="width:120px;" name="start_date"
                   id="start_date" value="<?php echo $this->get('start_date'); ?>"
                   onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>-
            <input type="text" size="20" class="ipt" style="width:120px;" name="end_date"
                   id="end_date" value="<?php echo $this->get('end_date'); ?>"
                   onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
            <input type="submit" class="but" value="查询">
        </div>
    </form>


</div>
<div class="main mbody">
    <form action="?m=save_order" name="form_order" method="post">
        <table class="tb fixTh">
            <thead>
            <tr>
                <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('wechatUseLog/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '记录id', 'field' => 'id')); ?></th>
                <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('wechatUseLog/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '微信ID', 'field' => 'wx_id')); ?></th>
                <th align='center'>微信号</th>
                <th align='center'>客服部</th>
                <th align='center'>商品</th>
                <th align='center'>形象</th>
                <th align='center'>业务</th>
                <th align='center'>计费方式</th>
                <th align='center'>推广部门</th>
                <th align='center'>推广人员</th>
                <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('wechatUseLog/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '开始时间', 'field' => 'begin_time')); ?></th>
                <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('wechatUseLog/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '结束时间', 'field' => 'end_time')); ?></th>
            </tr>
            </thead>

            <?php
            foreach ($page['listdata']['list'] as $r) {
                ?>
                <tr>
                    <td><?php echo $r['id']; ?></td>
                    <td><?php echo $r['wx_id']; ?></td>
                    <td><?php echo $r['wechat_id']; ?></td>
                    <td><?php echo $r['customer_service']; ?></td>
                    <td><?php echo $r['goods_name']; ?></td>
                    <td><?php echo $r['character_name']; ?></td>
                    <td><?php echo $r['business_type']; ?></td>
                    <td><?php echo vars::get_field_str('charging_type', $r['charging_type']); ?></td>
                    <td><?php echo $r['department_name']; ?></td>
                    <td><?php echo $r['promotion_staff']; ?></td>
                    <td><?php echo $date = empty($r['begin_time']) ? '--' : date('Y-m-d', $r['begin_time']); ?></td>
                    <td><?php echo $date = empty($r['end_time']) ? '--' : date('Y-m-d', $r['end_time']); ?></td>
                </tr>
                <?php
            } ?>


        </table>
        <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
        <div class="clear"></div>
    </form>
</div>

