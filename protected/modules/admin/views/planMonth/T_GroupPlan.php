<!-- 组计划表搜索栏 -->
<?php
$current_month = date("Y-m", strtotime("now"));
$next_month = date('Y-m',strtotime("$current_month+1 months"));
if ($page['show_add_group_plan'] == 1) {
    $ret = PlanMonthGroupUser::model()->getSupplement();
}
?>
    <div style="margin-top: 10px;">
        <form action="<?php echo $this->createUrl('planMonth/index') ?>">
            状态：
            <select name="status">
                <option value="0" >全部</option>
                <option value="1" <?php if ($this->get('status') == 1) echo 'selected';?>>未审核</option>
                <option value="2" <?php if ($this->get('status') == 2 ) echo 'selected';?>>审核未通过</option>
                <option value="3" <?php if ($this->get('status') == 3) echo 'selected';?>>审核通过</option>
            </select>
            推广组：
            <select name="groupid">
                <option value="">全部</option>
                <?php foreach ($page['manage_group'] as $key=>$value) {?>
                    <option value="<?php echo $key;?>"  <?php if ($this->get('groupid') == $key) echo 'selected';?>><?php echo $value?></option>
                <?php } ?>
            </select>&nbsp;
            <input hidden name="group_id" value="2">
            日期：
            <input type="text" size="20" class="ipt" style="width:120px;" name="date_start_group"
                   id="date_start_group" value="<?php if($data['date_start_group']){echo $data['date_start_group'];}  ; ?>"
                   onclick="WdatePicker({dateFmt:'yyyy-MM'})"/>
            客服部:
            <?php echo helper::getServiceSelect('cs_id'); ?>
            <input class="but" type="submit" value="查询">
        </form>
        <div style="margin-top: 10px;">
            <?php if($page['show_add_group_plan'] == 1) {?>
        <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="添加组计划" onclick="location=\'' . $this->createUrl('planMonth/addGroup?month='.$next_month) . '\'" />', 'auth_tag' => 'planMonth_addGroup')); ?>
        <?php if ($ret == 0) {?>
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="补写本月计划" onclick="location=\'' . $this->createUrl('planMonth/addGroup?month='.$current_month ) . '\'" />', 'auth_tag' => 'planMonth_addGroup')); ?>
        <?php } }?>
        </div>
    </div>

