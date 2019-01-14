<!-- 整体效果表搜索栏 -->
<form action="<?php echo $this->createUrl('orderEffectTable/index')?>">

    <input type="hidden" name="group_id" value="<?php echo $this->get('group_id');?>"/>
    上线日期：
    <input type="text" size="20" class="ipt" style="width:120px;" name="start_online_date"
           id="start_online_date" value="<?php echo $allData['cache_start_date']; ?>"
           onclick="WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'#F{$dp.$D(\'end_online_date\',{d:0})}'})"/>-
    <input type="text" size="20" class="ipt" style="width:120px;" name="end_online_date"
           id="end_online_date" value="<?php echo $allData['cache_end_date']; ?>"
           onclick="WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(\'start_online_date\',{d:0})}'})"/>
    <a href="#"
       onclick="$('#start_online_date').val('<?php echo date("Y-m-d", mktime(0, 0, 0, date('m'), 1, date('Y'))); ?>');$('#end_online_date').val('<?php echo date("Y-m-d", mktime(0, 0, 0, date('m'), date('t'), date('Y'))); ?>')">本月</a>
    &nbsp;
    合作商：
    <input type="text" id="partner_name" name="partner_name" class="ipt" style="width:130px"
           value="<?php echo $this->get('partner_name'); ?>">
    &nbsp;
    渠道名称：
    <input type="text" id="channel_name" name="channel_name" class="ipt" style="width:130px"
           value="<?php echo $this->get('channel_name'); ?>">
    &nbsp;
    微信号ID：
    <input type="text" id="wechat_id" name="wechat_id" class="ipt" style="width:130px"
           value="<?php echo $this->get('wechat_id'); ?>">
    <div class="mt10">
        客服部：
        <?php
        helper::getServiceSelect('csid');
        ?>
        &nbsp;
        商品：
        <?php
        echo $this->get('csid') ? CHtml::dropDownList('goods_id', $this->get('goods_id'), CHtml::listData(CustomerServiceRelation::model()->getGoodsList($this->get('csid')), 'goods_id', 'goods_name'), array('empty' => '全部')) :
            CHtml::dropDownList('goods_id', $this->get('goods_id'), CHtml::listData(Goods::model()->findAll(), 'id', 'goods_name'), array('empty' => '全部'))
        ?>
        &nbsp;
        推广人员：
        <?php
        $promotionStafflist = PromotionStaff::model()->getPromotionStaffList();
        echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($promotionStafflist, 'user_id', 'name'),
            array('empty' => '请选择')
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
        &nbsp;
        <input type="submit" class="but" value="查询">
        &nbsp; &nbsp;
        <?php $this->check_u_menu(array('code' => '<input type="button" class="but" onclick="exportTable(\'电销推广人员效果表\');" value="导出推广人员效果表"  />', 'auth_tag' => 'orderEffectTable_export')); ?>


    </div>
</form>

