<!-- 个人计划表搜索栏 -->
<?php
$current_month = date("Y-m", strtotime("now"));
$next_month = date('Y-m',strtotime("$current_month +1 months"));
$ret = PlanMonthUser::model()->getSupplement();
$id = Yii::app()->admin_user->uid;
$authority = AdminUser::model()->getUserAuthority($id);
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

        <?php if($authority != 3 || $page['is_super_admin'] == 1){ ?>
        &nbsp;&nbsp;&nbsp;推广人员:
        <?php
        echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($page['promotions_staff'], 'user_id', 'name'),
            array(
                'empty' => '全部',
            )
        );
        ?>
        <?php } ?>
        <input hidden name="group_id" value="1">
        日期：
        <input type="text" size="20" class="ipt" style="width:120px;" name="date_start_person"
               id="date_start_person" value="<?php if($page['date_start_person']){echo $page['date_start_person'];}  ; ?>"
               onclick="WdatePicker({dateFmt:'yyyy-MM'})"/>
        客服部:
        <?php echo helper::getServiceSelect('cs_id'); ?>
        <input class="but" type="submit" value="查询">
    </form>

    <div style="margin-top: 10px;">
        <?php if($page['show_add_user_plan'] == 1) {?>
        <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="添加个人计划" onclick="location=\'' . $this->createUrl('planMonth/add?month='.$next_month) . '\'" />', 'auth_tag' => 'planMonth_add')); ?>
        <?php if ($ret == 0) {?>
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="补写本月计划" onclick="location=\'' . $this->createUrl('planMonth/add?month='.$current_month.'&supplement=1') . '\'" />', 'auth_tag' => 'planMonth_add')); ?>
        <?php }
        }?>
    </div>

    </div>
    <div class="mt10">
        &nbsp;
    </div>


