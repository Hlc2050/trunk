<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">计划管理 » 推广周计划表</div>
    <div class="mt10">
        <form action="<?php echo $this->createUrl('promotionWeekPlan/index'); ?>" name="form_order" method="post">
        日期:
            <input type="text" size="20" class="ipt" style="width:80px;font-weight: bold;font-size: 12px" id="date_start" name="date_start" value="<?php echo $this->get('date_start') ? $this->get('date_start'):'';?>" onclick="WdatePicker({disabledDays:[1,2,3,4,5,6],qsEnabled:false,isShowWeek:true,isShowToday:false,firstDayOfWeek:1,dateFmt:'yyyy-MM-dd',onpicked:function() {var time_obj =$dp.$DV($dp.cal.getDateStr(),{d:+6});console.log(time_obj);;},onclearing:function() {$dp.$('date_start').value = ''; }})" readonly="">
        &nbsp;&nbsp;
        客服部：
        <?php
        helper::getServiceSelect('csid');
        ?>
        &nbsp;&nbsp;
        推广人员：
        <?php
        $promotionStafflist = PromotionStaff::model()->getPromotionStaffList();
        echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($promotionStafflist, 'user_id', 'name'),
            array('empty' => '请选择')
        );
        ?>&nbsp;
        <input type="submit" value="查询" class="but">
        </form>
    </div>

</div>

<div class="main mbody">

        <table class="tb fixTh">
            <thead>
            <tr>
                <th>客服部</th>
                <th>推广人员</th>
                <th></th>
                <?php foreach ($month_day as $value){ ?>
                    <th><?php echo $value ?></th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $value){ ?>
                    <tr>
                        <td rowspan="2"><?php echo $value['service_name'] ?></td>
                        <td rowspan="2"><?php echo $value['sno'] ?></td>
                        <td>微信号个数</td>
                        <?php foreach ($date as $v){ ?>
                                <td><?php echo $value['date'][$v]['num'] ?></td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td>计划进粉</td>
                        <?php foreach ($date as $v){ ?>
                            <td><?php echo $value['date'][$v]['fans'] ?></td>
                        <?php } ?>
                    </tr>


            <?php } ?>
            </tbody>
        </table>

</div>
