<form action="<?php echo $this->createUrl('bssOperationTable/index'); ?>">
    <label>
        客服部：
        <?php
        helper::getServiceSelect('csid');
        ?>
        &nbsp;&nbsp;

        商品：
        <?php
        echo $this->get('csid') ? CHtml::dropDownList('goods_id', $this->get('goods_id'), CHtml::listData(CustomerServiceRelation::model()->getGoodsList($this->get('csid')), 'goods_id', 'goods_name'), array('empty' => '全部')) :
            CHtml::dropDownList('goods_id', $this->get('goods_id'), CHtml::listData(Goods::model()->findAll(), 'id', 'goods_name'), array('empty' => '全部'))
        ?>&nbsp;&nbsp;
        推广小组：
        <?php
        $promotionGrouplist = Linkage::model()->getPromotionGroupList();
        echo CHtml::dropDownList('pgid', $this->get('pgid'), CHtml::listData($promotionGrouplist, 'linkage_id', 'linkage_name'),
            array(
                'empty' => '全部',
                'ajax' => array(
                    'type' => 'POST',
                    'url' => $this->createUrl('bssOperationTable/getPromotionStaffByPg'),
                    'update' => '#tg_id',
                    'data' => array('pgid' => 'js:$("#pgid").val()'),
                )
            )
        );
        ?>&nbsp;&nbsp;
        推广人员：
        <?php
        echo $this->get('pgid') ? CHtml::dropDownList('tg_id', $this->get('tg_id'), CHtml::listData(PromotionStaff::model()->getPromotionStaffByPg($this->get('pgid')), 'user_id', 'user_name'), array('empty' => '全部')) :
            CHtml::dropDownList('tg_id', $this->get('tg_id'), array('全部'))
        ?>&nbsp;&nbsp;
        业务：
        <?php
        $types = array(array('id' => 1, 'name' => '微销'), array('id' => 2, 'name' => '电销'));
        echo CHtml::dropDownList('tid', $this->get('tid'), CHtml::listData($types, 'id', 'name'),
            array(
                'empty' => '全部',
                'ajax' => array(
                    'type' => 'POST',
                    'url' => $this->createUrl('bssOperationTable/getBusinessTypes'),
                    'update' => '#bsid',
                    'data' => array('tid' => 'js:$("#tid").val()'),
                )
            )
        );
        ?>&nbsp;&nbsp;
        业务类型：
        <?php
        echo $this->get('tid') ?
            CHtml::dropDownList('bsid', $this->get('bsid'), CHtml::listData(BusinessTypes::model()->getBsTypesByTid($this->get('tid')), 'bid', 'bname'),
            array('empty' => '全部',
                'ajax' => array(
                    'type' => 'POST',
                    'url' => $this->createUrl('bssOperationTable/getChargingTypes'),
                    'update' => '#chg_id',
                    'data' => array('bsid' => 'js:$("#bsid").val()'),
                )
            )
        ) : CHtml::dropDownList('bsid', '', CHtml::listData(array(0 => array('value' => '', 'text' => '全部')), 'value', 'text'), array(
                'ajax' => array(
                    'type' => 'POST',
                    'url' => $this->createUrl('bssOperationTable/getChargingTypes'),
                    'update' => '#chg_id',
                    'data' => array('bsid' => 'js:$("#bsid").val()'),
                )
            ))
        ?>&nbsp;&nbsp;
        计费方式：
        <?php
        echo $this->get('bsid') ? CHtml::dropDownList('chg_id', $this->get('chg_id'), CHtml::listData(BusinessTypeRelation::model()->getChargeTypes($this->get('bsid')), 'value', 'txt'), array('empty' => '全部')) :
            CHtml::dropDownList('chg_id', '', CHtml::listData(array(0 => array('value' => '', 'text' => '全部')), 'value', 'text')) ?>
        &nbsp;&nbsp;
        <div class="mt10">
            日期：
            <input type="text" size="20" class="ipt" name="start_date"
                   id="start_date"
                   value="<?php echo $page['first_day'] ? $page['first_day'] : date("Y-m-d", mktime(0, 0, 0, date('m'), 1, date('Y'))); ?>"
                   onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>-
            <input type="text" size="20" class="ipt" name="end_date"
                   id="end_date" value="<?php echo $page['last_day'] ? $page['last_day'] : date("Y-m-d", time()); ?>"
                   onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>&nbsp;
            <a href="#"
               onclick="$('#start_date').val('<?php echo date("Y-m-d", mktime(0, 0, 0, date('m'), 1, date('Y'))); ?>');$('#end_date').val('<?php echo date("Y-m-d", mktime(0, 0, 0, date('m'), date('t'), date('Y'))); ?>')">本月</a>&nbsp;
            <?php $a = helper::lastMonth(time()); ?>
            <a href="#"
               onclick="$('#start_date').val('<?php echo date('Y-m-d', strtotime($a[0])); ?>');$('#end_date').val('<?php echo date('Y-m-d', strtotime($a[1])); ?>')">上月</a>&nbsp;
            <a href="#" onclick="$('#start_date').val('');$('#end_date').val('')">清空</a>&nbsp;&nbsp;

            <input type="hidden" id="group_id" name="group_id" value="<?php echo $this->get('group_id'); ?>"/>
            <input type="submit" class="but" value="查询">
        </div>
    </label>
</form>
