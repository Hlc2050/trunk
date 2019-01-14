<!--统计表搜索栏-->
<form action="<?php echo $this->createUrl('orderGoodsEffect/index'); ?>">
    <input type="hidden" name="group_id" value="0"/>
    日期：
    <input type="text" size="20" class="ipt" style="width:120px;" name="start_online_date"
           id="start_online_date" value="<?php echo $allData['start_online_date']; ?>"
           onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>-
    <input type="text" size="20" class="ipt" style="width:120px;" name="end_online_date"
           id="end_online_date" value="<?php echo $allData['end_online_date']; ?>"
           onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
    <a href="#"
       onclick="$('#start_online_date').val('<?php echo date("Y-m-d", mktime(0, 0, 0, date('m'), 1, date('Y'))); ?>');$('#end_online_date').val('<?php echo date("Y-m-d", mktime(0, 0, 0, date('m'), date('t'), date('Y'))); ?>')">本月</a>
    &nbsp;下单商品：
    <input type="text" class="ipt" name="package" value="<?php echo $this->get('package');?>">
    &nbsp;
    客服部：
    <?php
    helper::getServiceSelect('csid');
    ?>
    <input type="submit" class="but" value="查询">
    <?php $this->check_u_menu(array('code' => '<input type="button" class="but" value="导出统计表"
                     onclick="location=\''.$this->createUrl('orderGoodsEffect/export').'?start_online_date='.$allData['start_online_date'].'&end_online_date='.$allData['end_online_date'].'&package='.$this->get('package').'\'" />', 'auth_tag' => 'orderGoodsEffect_export')); ?>

</form>
