<!-- 组计划 -->
<form action="<?php echo $this->createUrl('planWeek/index'); ?>">
    <div class="mt10" style="margin-bottom: 10px">
            状态：
            <select name="status">
                <option value="" >全部</option>
                <option value="1" <?php if ($this->get('status') == 1) echo 'selected';?>>已审核</option>
                <option value="0" <?php if (isset($_REQUEST['status']) && $this->get('status') !='' && $this->get('status') == 0) echo 'selected';?>>待审核</option>
                <option value="2" <?php if ($this->get('status') == 2) echo 'selected';?>>审批未通过</option>
            </select>
            推广组：
            <select name="group_id">
                <option value="">全部</option>
                <?php foreach ($page['manage_group'] as $key=>$value) {?>
                    <option value="<?php echo $key;?>"  <?php if ($this->get('group_id') == $key) echo 'selected';?>><?php echo $value?></option>
                <?php } ?>
            </select>&nbsp;
        日期：
        <input type="text" size="20" class="ipt" style="width:80px;font-weight: bold;font-size: 12px"
               id="date_start_group" name="date_start_group" value="<?php echo $date = $this->get('date_start_group') ? $this->get('date_start_group'):$page['info']['date_start_group'];?>" onclick="WdatePicker({disabledDays:[1,2,3,4,5,6],qsEnabled:false,isShowWeek:true,isShowToday:false,firstDayOfWeek:1,dateFmt:'yyyy-MM-dd',
                           onpicked:function() {
                           var time_obj =$dp.$DV($dp.cal.getDateStr(),{d:+6});console.log(time_obj);
                      },onclearing:function() {
                        $dp.$('date_start_group').value = '';
                      }})" readonly/>
        客服部:
        <?php echo helper::getServiceSelect('csids'); ?>
        <input type="submit" class="but" value="查询"> &nbsp;

    </div>
    <div class="l" id="container">
        <?php if ($page['show_add_group_plan'] == 1) { ?>
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="添加组计划" onclick="location=\'' . $this->createUrl('planWeek/addGroupPlan?url=' . $page['listdata']['url']) . '\'" />', 'auth_tag' => 'planWeek_addGroupPlan')); ?>
            <?php if ($page['hide_add_curent'] == 0) { ?>
                <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="补写本周组计划" onclick="location=\'' . $this->createUrl('planWeek/addGroupPlan?type=2&url=' . $page['listdata']['url']) . '\'" />', 'auth_tag' => 'planWeek_addGroupPlan')); ?>
            <?php }
        } ?>
    </div>
    <input type="hidden" name="tab_id" value="<?php echo $page['tab_id'] ;?>">
</form>

