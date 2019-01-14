<!--统计表搜索栏-->
<form action="<?php echo $this->createUrl('csTimetable/promotionGroup'); ?>?group_id=0">
    <input hidden name="group_id" value="0">
    日期：
    <input type="text" size="20" class="ipt" style="width:120px;" name="start_date"
           id="start_date" value="<?php if($page['start_date']){echo date('Y-m-d',$page['start_date']);}  ; ?>"
           onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>-
    <input type="text" size="20" class="ipt" style="width:120px;" name="end_date"
           id="end_date" value="<?php  if($page['end_date']){echo date('Y-m-d',$page['end_date']);} ; ?>"
           onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>&nbsp;&nbsp;

    指标：
    <select name="predict">
        <option value="weChat_num" <?php if ($this->get('predict') == 'weChat_num') echo "selected"; ?>>微信号个数</option>
        <option value="fans_counts" <?php if ($this->get('predict') == 'fans_counts') echo "selected"; ?>>预计进粉</option>
        <option value="outputs" <?php if ($this->get('predict') == 'outputs') echo "selected"; ?>>预计产值</option>
    </select>&nbsp;&nbsp;
    对比数据:
    <?php $promotion_group =AdminGroup::model()->getGroup();
    echo CHtml::dropDownList('promotion_group1',$this->get('promotion_group1'), CHtml::listData($promotion_group, 'groupid', 'groupname'),
        array('empty' => '全部',)
    );
    ?>&nbsp;
    <?php echo 'PK'; ?>&nbsp;
    <?php $promotion_group =AdminGroup::model()->getGroup();
    echo CHtml::dropDownList('promotion_group2', $this->get('promotion_group2'), CHtml::listData($promotion_group, 'groupid', 'groupname'),
        array('empty' => '全部',)
    );
    ?>&nbsp;&nbsp;

    客服部：
    <?php
    helper::getServiceSelect('csid');
    ?>
    <input type="submit" class="but" value="查询">

</form>
