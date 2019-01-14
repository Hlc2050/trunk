<?php require(dirname(__FILE__) . "/../common/head.php"); ?>

<div class="main mhead">
    <div class="snav">计划管理 » 客服预估表</div>
    <div class="mt10">
        <div class="tab_box">

        </div>
    </div>
</div>
<form action="<?php echo $this->createUrl('csForecast/index'); ?>" method="post">
    <input type="hidden" name="group_id" value="1"/>
    日期：
    <input type="text" size="20" class="ipt" style="width:120px;" name="end_date"
           id="end_date" value="<?php if($data['time']){echo date('Y-m-d',$data['time']);}  ; ?>"
           onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
    <?php
    $authority = AdminUser::model()->getUserAu();
    $uid = Yii::app()->admin_user->uid;
    $promotionStafflist = PromotionStaff::model()->getPromotionStaffByManager($uid);

    ?>
    <?php if($authority == 0){ ?>
    <?php $promotion_group =AdminGroup::model()->getGroup();
    echo CHtml::dropDownList('promotion_group',$this->get('promotion_group'), CHtml::listData($promotion_group, 'groupid', 'groupname'),
        array('empty' => '推广组',)
    );
    ?>
    <?php } ?>
    <?php if($authority == 0){ ?>
    <input type="text" class="ipt" id="tg" name="tg" value="<?php echo $data['tg']?$data['tg']:$this->get('tg'); ?>">&nbsp;
    <input hidden id="tg_id" name="tg_id">
    <?php }elseif ($authority == 1){ ?>
        <select name="tg_id">
            <option value="">推广人员</option>
            <?php foreach ($promotionStafflist as $value){ ?>
                <option  value="<?php echo $value['user_id'] ?>" <?php if($value['user_id'] == $this->get('tg_id')) echo 'selected'; ?>><?php echo $value['name'] ?></option>
            <?php } ?>
        </select>&nbsp;
    <?php } ?>
    <input type="submit" class="but" value="搜索">&nbsp;
</form>

    <table class="tb3">
        <tr>
            <th colspan="2">客服部</th>
            <th>当日计划</th>
            <th>前一天</th>
            <th>前两天</th>
            <th>前三天</th>
            <th>近三日平均</th>
            <th>近七日平均</th>
            <th>近30日平均</th>
        </tr>
        <?php  ?>
        <?php foreach ($data['list'] as $k=>$value){  ?>
            <?php
            $average_three_days_weChat_num=0;
            $average_three_days_fans = 0;
            $average_three_days_outputs = 0;
            $average_seven_days_weChat_num=0;
            $average_seven_days_fans = 0;
            $average_seven_days_outputs = 0;
            $average_thirty_days_weChat_num =0;
            $average_thirty_days_fans = 0;
            $average_thirty_days_outputs = 0;
            ?>
        <tr>
            <td style="text-align: center">
                <?php echo $value['name']; ?>
            </td>
            <td>微信号个数：<br>进粉：<br>产值：</td>
            <?php  for ($i = 0; $i < 30; $i++) { ?>

                <?php if ($i == 0) { ?>
                    <td style="text-align: center"><?php echo ($value[($data['time'] - 86400 * $i)]['weChat_num']?$value[($data['time'] - 86400 * $i)]['weChat_num']:0).'<br>'?><?php echo ($value[($data['time'] - 86400 * $i)]['fans_counts']?$value[($data['time'] - 86400 * $i)]['fans_counts']:0).'<br>'?><?php echo ($value[($data['time'] - 86400 * $i)]['outputs']?$value[($data['time'] - 86400 * $i)]['outputs']:0);  ?></td>
                <?php } ?>

                <?php if ($i == 1) { ?>
                    <td style="text-align: center"><?php echo ($value[($data['time'] - 86400 * $i)]['weChat_num']?$value[($data['time'] - 86400 * $i)]['weChat_num']:0).'<br>'?><?php echo ($value[($data['time'] - 86400 * $i)]['fans_counts']?$value[($data['time'] - 86400 * $i)]['fans_counts']:0).'<br>'?><?php echo ($value[($data['time'] - 86400 * $i)]['outputs']?$value[($data['time'] - 86400 * $i)]['outputs']:0);  ?></td>
                <?php } ?>

                <?php if ($i == 2) { ?>
                    <td style="text-align: center"><?php echo ($value[($data['time'] - 86400 * $i)]['weChat_num']?$value[($data['time'] - 86400 * $i)]['weChat_num']:0).'<br>'?><?php echo ($value[($data['time'] - 86400 * $i)]['fans_counts']?$value[($data['time'] - 86400 * $i)]['fans_counts']:0).'<br>'?><?php echo ($value[($data['time'] - 86400 * $i)]['outputs']?$value[($data['time'] - 86400 * $i)]['outputs']:0);  ?></td>
                <?php } ?>

                <?php if ($i == 3) { ?>
                    <td style="text-align: center"><?php echo ($value[($data['time'] - 86400 * $i)]['weChat_num']?$value[($data['time'] - 86400 * $i)]['weChat_num']:0).'<br>'?><?php echo ($value[($data['time'] - 86400 * $i)]['fans_counts']?$value[($data['time'] - 86400 * $i)]['fans_counts']:0).'<br>'?><?php echo ($value[($data['time'] - 86400 * $i)]['outputs']?$value[($data['time'] - 86400 * $i)]['outputs']:0);  ?></td>
                <?php } ?>

                <?php if ($i < 3) {
                    $average_three_days_weChat_num += $value[($data['time'] - 86400 * $i)]['weChat_num'];
                    $average_three_days_fans += $value[($data['time'] - 86400 * $i)]['fans_counts'];
                    $average_three_days_outputs += $value[($data['time'] - 86400 * $i)]['outputs'];
               } ?>

                <?php if ($i < 7) {
                    $average_seven_days_weChat_num += $value[($data['time'] - 86400 * $i)]['weChat_num'];
                    $average_seven_days_fans += $value[($data['time'] - 86400 * $i)]['fans_counts'];
                    $average_seven_days_outputs += $value[($data['time'] - 86400 * $i)]['outputs'];
               } ?>

                <?php if ($i < 30) {
                    $average_thirty_days_weChat_num += $value[($data['time'] - 86400 * $i)]['weChat_num'];
                    $average_thirty_days_fans += $value[($data['time'] - 86400 * $i)]['fans_counts'];
                    $average_thirty_days_outputs += $value[($data['time'] - 86400 * $i)]['outputs'];
               } ?>

            <?php } ?>
            <td style="text-align: center"><?php echo round($average_three_days_weChat_num/3).'<br>'.round($average_three_days_fans/3).'<br>'.round($average_three_days_outputs/3);  ?></td>
            <td style="text-align: center"><?php echo round($average_seven_days_weChat_num/7).'<br>'.round($average_seven_days_fans/7).'<br>'.round($average_seven_days_outputs/7);  ?></td>
            <td style="text-align: center"><?php echo round($average_thirty_days_weChat_num/30).'<br>'.round($average_thirty_days_fans/30).'<br>'.round($average_thirty_days_outputs/30);  ?></td>
        </tr>
        <?php } ?>
    </table>



<script>
    $("#promotion_group").change(function () {
        $("#tg").attr('value','');
    })

    $(document).ready(function () {
        $('#tg').on('keyup focus', function () {
            $('.searchsBox').show();
            var myInput = $(this);
            var key = myInput.val();
            var group_id = $("#promotion_group").val();
            var postdata = {search_type: 'keys', search_txt: key , group_id:group_id };
            var html = '';
            $.getJSON('<?php echo $this->createUrl('csTimetable/getTgPeople') ?>?jsoncallback=?', postdata, function (reponse) {
                for (var i = 0; i < reponse.data.length; i++) {
                    html += '<a href="javascript:void(0);" style="display:block;font-size:12px;padding:2px 5px;" onmouseDown="getTValues(this);" + data-id="' + reponse.data[i].sno+ '" + data-name="' + reponse.data[i].csname_true+ '">' + reponse.data[i].csname_true + '</a>'
                }
                var s_height = myInput.height();
                var top = myInput.offset().top + s_height;
                var left = myInput.offset().left;
                var width = myInput.width();
                $('.searchsBox').remove();
                $('body').append('<div class="searchsBox" style="position:absolute;top:' + top + 'px;left:' + left + 'px;background:#fff;z-index:2;border:1px solid #ccc;width:' + width + 'px;">' + html + '</div>');
            });
            myInput.blur(function () {
                $('.searchsBox').hide();
            })
        });
    })

    function getTValues(ele) {
        var myobj = $(ele);
        var id = myobj.attr('data-id');
        var name = myobj.attr('data-name');
        $("#tg_id").val(id);
        $("#tg").val(name);
    }
</script>