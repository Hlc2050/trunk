<?php require(dirname(__FILE__) . "/../common/head.php");
?>
<script>
    function check_export() {
        var begin_time = $("#startDate").val();
        var end_time = $("#endDate").val();
        if(begin_time == '' || end_time == ''){
            alert('请先选择导出时间段!');
            return false;
        }
        var data = $("form").serialize();
        data += '&start_date='+begin_time+'&end_date='+end_time;
        var url = "<?php echo $this->createUrl('effectInFans/export');?>";
        window.location.href=url+'?'+data;
    }
</script>
<div class="main mhead" xmlns="http://www.w3.org/1999/html">
    <div class="snav">推广效果 » 进粉表</div>

    <div class="mt10">
        <form action="<?php echo $this->createUrl('effectInFans/index'); ?>">

            客服部：
            <?php
            helper::getServiceSelect('csid');
            ?>&nbsp;
            商品：
            <?php
            echo $this->get('csid') ? CHtml::dropDownList('goods_id', $this->get('goods_id'), CHtml::listData(CustomerServiceRelation::model()->getGoodsList($this->get('csid')), 'goods_id', 'goods_name'), array('empty' => '全部')) :
                CHtml::dropDownList('goods_id', $this->get('goods_id'), CHtml::listData(Goods::model()->findAll(), 'id', 'goods_name'), array('empty' => '全部'))
            ?>&nbsp;
            推广人员：
            <?php
            $userArr = $this->toArr(PromotionStaff::model()->getPromotionStaffList());
            echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($userArr, 'user_id', 'name'),
                array('empty' => '请选择'));
            ?>&nbsp;&nbsp;
            业务类型：
            <?php
            $businessTypes = Dtable::toArr(BusinessTypes::model()->findAll());
            echo CHtml::dropDownList('bsid', $this->get('bsid'), CHtml::listData($businessTypes, 'bid', 'bname'),
                array(
                    'empty' => '全部',
                    'ajax' => array(
                        'type' => 'POST',
                        'url' => $this->createUrl('bssOperationTable/getChargingTypes'),
                        'update' => '#chg_id',
                        'data' => array('bsid' => 'js:$("#bsid").val()'),
                    )
                )
            );
            ?>&nbsp;&nbsp;
            计费方式：
            <?php
            echo $this->get('bsid') ? CHtml::dropDownList('chg_id', $this->get('chg_id'), CHtml::listData(BusinessTypeRelation::model()->getChargeTypes($this->get('bsid')), 'value', 'txt'), array('empty' => '全部')) :
                CHtml::dropDownList('chg_id', $this->get('chg_id'), array('全部'))
            ?>
            微信号ID：
            <input type="text" id="wechat_id" name="wechat_id" class="ipt" style="width:130px;"
                   value="<?php echo isset($_GET['wechat_id']) ? $_GET['wechat_id'] : ''; ?>">&nbsp;

            <div class="mt10">
                时间：
                <input type="text" size="20" class="ipt"
                       id="start_date" name="start_date" value="<?php echo $this->get('start_date') == '' ? $page['start_time'] : $this->get('start_date'); ?>" onclick="WdatePicker({disabledDays:[1,2,3,4,5,6],qsEnabled:false,isShowWeek:true,isShowToday:false,firstDayOfWeek:1,dateFmt:'yyyy-MM-dd',
                           onpicked:function() {
                           var time_obj =$dp.$DV($dp.cal.getDateStr(),{d:+6});$dp.$('end_date').value = time_obj.y+'-'+time_obj.M+'-'+time_obj.d;
                      }})" readonly/> -
                <input type="text" size="20" class="ipt"
                       id="end_date" name="end_date" value="<?php echo $this->get('end_date') == '' ? $page['end_time']:$this->get('end_date')  ?>" readonly/>&nbsp;
                <input type="submit" class="but" value="查询">
                <?php
                if($this->check_u_menu(array('auth_tag'=>'effectInFans_export','echo'=>0)))
                {
                    ?>
                    导出时间段：
                    <input type="text" size="20" class="ipt" style="width:80px;font-weight: bold;font-size: 12px"
                           id="startDate" name="export_start_date" value="" onclick="WdatePicker({qsEnabled:false,isShowWeek:true,firstDayOfWeek:1,dateFmt:'yyyy-MM-dd',maxDate:'#F{$dp.$D(\'endDate\',{d:-1})}'})" readonly/> -
                    <input type="text" size="20" class="ipt" style="width:80px;font-weight: bold;font-size: 12px"
                           id="endDate" name="export_end_date" value="" onclick="WdatePicker({qsEnabled:false,isShowWeek:true,firstDayOfWeek:1,dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(\'startDate\',{d:1})}'})" readonly/>
                    <input type="button" class="but2" value="导出" onclick="check_export()">
                    <?php
                }
                ?>
            </div>
        </form>


        <div class="mt10 clearfix">
            <div class="l">
            </div>
            <div class="r">

            </div>
        </div>
    </div>
    <div class="main mbody">
        <form action="<?php echo $this->createUrl('wechatCard/saveOrder'); ?>" name="form_order" method="post">
            <table class="tb fixTh">
                <thead>
                <tr>
                    <th width="100">微信号ID</th>
                    <!--<th width="100">商品</th>-->
                    <?php
                    $date_title = PartnerCost::model()->getDateThTwo($page['start_time'], $page['end_time']);
                    foreach ($date_title as $v) {
                        echo '<th width="80" colspan="2" style="border: solid 1px #d1d5de;border-left:solid 1px #888;border-right:solid 1px #888;">' . $v . '</th>';
                    }
                    ?>
                    <th width="100" colspan="2" style="border: solid 1px #d1d5de;border-left:solid 1px #888;border-right:solid 1px #888;">合计</th>
                </tr>
                </thead>
                <tr>
                    <td>合计</td>
                    <?php
                    foreach ($date_title as $key=>$v) {
                        ?>
                        <td width="40" style="border: solid 1px #e6ebf2;border-left:solid 1px #888;"><?php echo $day_fans_count = isset($page['day_fans_count'][$key]) ? $page['day_fans_count'][$key]:0; ?></td>
                        <td style="border: solid 1px #e6ebf2;border-right:solid 1px #888;" width="40" ><?php echo $day_timetable_count = isset($page['day_timetable_count'][$key]) ? $page['day_timetable_count'][$key]:0; ?></td>
                    <?php
                    }
                    ?>
                    <td width='40' style="border: solid 1px #e6ebf2;border-left:solid 1px #888;"><?php echo $page['total_fans'];?></td>
                    <td width='40' style="border: solid 1px #e6ebf2;border-right:solid 1px #888;"><?php echo $page['total_count'];?></td>
                </tr>
                <?php
                foreach ($page['info'] as $r) {
                    $count = count($r);
                    ?>
                    <tr>
                        <td ><?php echo $r[0]; ?></td>
                        <?php for ($i = 1; $i < $count-2; $i++) {
                            echo "<td style='border: solid 1px #e6ebf2;border-left:solid 1px #888'>" . $r[$i]['fans_count']. "</td><td style='border: solid 1px #e6ebf2;border-right:solid 1px #888;'>".$r[$i]['timetable_count']. "</td>";
                        } ?>
                        <td style='border: solid 1px #e6ebf2;border-left:solid 1px #888;'><?php echo $r[$count-2];?></td>
                        <td style='border: solid 1px #e6ebf2;border-right:solid 1px #888;'><?php echo end($r); ?></td>
                    </tr>
                    <?php
                } ?>

            </table>
            <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
            <div class="clear"></div>
        </form>
    </div>

