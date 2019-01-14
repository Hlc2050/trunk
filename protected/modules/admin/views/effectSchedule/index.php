<?php require(dirname(__FILE__)."/../common/head.php"); ?>
    <div class="main mhead">
        <div class="snav">推广效果 » 进度表	</div>

        <div class="mt10 clearfix">
            <form action="<?php echo $this->createUrl('effectSchedule/index'); ?>">
                日期：
                <input type="text" size="20" class="ipt" style="width:120px;" name="start_plan_date" id="start_plan_date"
                       value="<?php echo $this->get('start_plan_date'); ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>-
                <input type="text" size="20" class="ipt" style="width:120px;" name="end_plan_date" id="end_plan_date"
                       value="<?php echo $this->get('end_plan_date'); ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>&nbsp;
                <a href="#"
                   onclick="$('#start_plan_date').val('<?php echo date("Y-m-d", mktime(0, 0, 0, date('m'), 1, date('Y'))); ?>');$('#end_plan_date').val('<?php echo date("Y-m-d", mktime(0, 0, 0, date('m'), date('t'), date('Y'))); ?>')">本月</a>

                <?php $a = lastMonth(time()); ?>
                <a href="#"
                   onclick="$('#start_plan_date').val('<?php echo $a[0]; ?>');$('#end_plan_date').val('<?php echo $a[1]; ?>')">上月</a>

                目标部门：
                <select name="schedule_id">
                    <option value="" selected>
                        选择对象
                    </option>
                    <?php
                    $scheduleList = Linkage::model() -> getScheduleList();
                    $goodsList = Linkage::model() -> getGoodsCategoryList();
                    $scheduleList = $scheduleList + $goodsList;
                    foreach ($scheduleList as $key => $val) {
                        ?>
                        <option
                            value="<?php echo $key; ?>" <?php echo $_GET['schedule_id'] == $key ? 'selected' : ''; ?>>
                            <?php echo $val;?>
                        </option>
                    <?php } ?>
                </select>
                <input type="submit" class="but" value="查询">
            </form>
        </div>
    </div>
    <div class="main mbody">
        <form>
            <table class="tb fixTh">
                <thead>
                <tr>
                    <th width="150"><?php $mouth = $this->get('start_plan_date') ? date('Y-m',strtotime($this->get('start_plan_date'))) : ' '; echo $mouth ?></th>
                    <th>A目标</th>
                    <th>B目标</th>
                    <th>C目标</th>
                </tr>
                </thead>
                <tr>
                    <td>发货金额</td>
                    <td><?php echo $info_cost['delivery_money'] ?></td>
                    <td><?php echo $info_cost['delivery_money'] ?></td>
                    <td><?php echo $info_cost['delivery_money'] ?></td>
                </tr>
                <tr>
                    <td>投入金额</td>
                    <td><?php echo $info_cost['input_money'] ?></td>
                    <td><?php echo $info_cost['input_money'] ?></td>
                    <td><?php echo $info_cost['input_money'] ?></td>
                </tr>
                <tr>
                    <td>ROI</td>
                    <td><?php echo round($info_cost['delivery_money']/$info_cost['input_money']*100)."%" ?></td>
                    <td><?php echo round($info_cost['delivery_money']/$info_cost['input_money']*100)."%" ?></td>
                    <td><?php echo round($info_cost['delivery_money']/$info_cost['input_money']*100)."%" ?></td>
                </tr>
                <tr>
                    <td>目标投产比</td>
                    <td><?php
                            echo $putinfo->target_type == 0 ? $putinfo->target_a : $putinfo->target_a."%";
                        ?>
                    </td>
                    <td><?php
                        echo $putinfo->target_type == 0 ? $putinfo->target_b : $putinfo->target_b."%";
                        ?>
                    </td>
                    <td><?php
                        echo $putinfo->target_type == 0 ? $putinfo->target_c : $putinfo->target_c."%";
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>目标发货金额</td>
                    <td><?php
                        echo $deliveinfo->target_type == 0 ? $deliveinfo->target_a : $deliveinfo->target_a."%";
                        ?>
                    </td>
                    <td><?php
                        echo $deliveinfo->target_type == 0 ? $deliveinfo->target_b : $deliveinfo->target_b."%";
                        ?>
                    </td>
                    <td><?php
                        echo $deliveinfo->target_type == 0 ? $deliveinfo->target_c : $deliveinfo->target_c."%";
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>实际进度</td>
                    <td><?php echo round($info_cost['delivery_money']/$deliveinfo->target_a*100)."%" ?></td>
                    <td><?php echo round($info_cost['delivery_money']/$deliveinfo->target_b*100)."%" ?></td>
                    <td><?php echo round($info_cost['delivery_money']/$deliveinfo->target_c*100)."%" ?></td>
                </tr>
                <tr>
                    <td>时间进度</td>
                    <td><?php echo $dateInfo['sc_days']."%"; ?></td>
                    <td><?php echo $dateInfo['sc_days']."%"; ?></td>
                    <td><?php echo $dateInfo['sc_days']."%"; ?></td>
                </tr>
                <tr>
                    <td>日均期望</td>
                    <td><?php echo $dateInfo['sc_t']==0?0:round(($deliveinfo->target_a - $info_cost['delivery_money'])/$dateInfo['sc_t'])?></td>
                    <td><?php echo $dateInfo['sc_t']==0?0:round(($deliveinfo->target_b - $info_cost['delivery_money'])/$dateInfo['sc_t'])?></td>
                    <td><?php echo $dateInfo['sc_t']==0?0:round(($deliveinfo->target_c - $info_cost['delivery_money'])/$dateInfo['sc_t'])?></td>
                </tr>

            </table>
            <div class="clear"></div>
        </form>
    </div>

<?php
function lastMonth($ts)
{
    $ts = intval($ts);

    $oneMonthAgo = mktime(0, 0, 0, date('n', $ts) - 1, 1, date('Y', $ts));
    $year = date('Y', $oneMonthAgo);
    $month = date('n', $oneMonthAgo);
    return array(
        date('Y-m-1', strtotime($year . "-{$month}-1")),
        date('Y-m-t', strtotime($year . "-{$month}-1"))
    );
}
?>
