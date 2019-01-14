<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<style>
    .width_500{
        width:500px !important;
    }
</style>
<div class="main mhead">
    <div class="snav">排期管理 » 排期表 </div>
    <div class="mt10">
        <form method="get" action="<?php echo $this->createUrl('timetable/index')?>" id="serchFrom" >
            <div class="mt10">
                查询日期:
                <input type="text" size="20" class="ipt" style="width:80px;font-weight: bold;font-size: 12px"
                       id="start_date" name="start_date" value="<?php echo $date = $this->get('start_date') ? $this->get('start_date'):$page['start_time'];?>" onclick="WdatePicker({disabledDays:[1,2,3,4,5,6],qsEnabled:false,isShowWeek:true,isShowToday:false,firstDayOfWeek:1,dateFmt:'yyyy-MM-dd',
                           onpicked:function() {
                           var time_obj =$dp.$DV($dp.cal.getDateStr(),{d:+6});console.log(time_obj);$dp.$('end_date').value = time_obj.y+'-'+time_obj.M+'-'+time_obj.d;
                      },onclearing:function() {
                        $dp.$('end_date').value = '';
                      }})" readonly/> -
                <input type="text" size="20" class="ipt" style="width:80px;font-weight: bold;;font-size: 12px"
                       id="end_date" name="end_date" value="<?php echo $date = $this->get('end_date') ? $this->get('end_date'):$page['end_time'];?>" readonly/>&nbsp;
                微信号:
                <textarea  id="wechat_id"  name="wechat_id" class="ipt width_500" ><?php echo $this->get('wechat_id'); ?></textarea>

                <div class="mt10">
                    客服部：
                    <?php
                    helper::getServiceSelect('csid');
                    ?>
                    &nbsp;&nbsp
                    商品：
                    <?php
                    echo $this->get('csid') ? CHtml::dropDownList('goods_id', $this->get('goods_id'), CHtml::listData(CustomerServiceRelation::model()->getGoodsList($this->get('csid')), 'goods_id', 'goods_name'), array('empty' => '全部')) :
                        CHtml::dropDownList('goods_id', $this->get('goods_id'),CHtml::listData(Goods::model()->findAll(), 'id', 'goods_name'), array('empty' =>'全部'))
                    ?>&nbsp;&nbsp;
                    推广小组：
                    <?php
                    $promotionGrouplist = $this->getUserPromotionGroup('edit');
                    echo CHtml::dropDownList('pgid', $this->get('pgid'), CHtml::listData($promotionGrouplist, 'linkage_id', 'linkage_name'),
                        array(
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
                    if(count($promotionGrouplist) == 1) $pgid = array_keys($promotionGrouplist);
                    $select_pgid = isset($pgid) ? $pgid[0] : $this->get('pgid');
                    echo $select_pgid ? CHtml::dropDownList('tg_id', $this->get('tg_id'), CHtml::listData(PromotionStaff::model()->getPromotionStaffByPg($select_pgid), 'user_id', 'user_name'), array('empty' => '全部')) :
                        CHtml::dropDownList('tg_id', $this->get('tg_id'),CHtml::listData(PromotionStaff::model()->getPromotionStaffByPg(), 'user_id', 'user_name'), array('empty' => '全部'))
                    ?>&nbsp;&nbsp;
                    微信号状态：
                    <?php
                    $weChat_status = vars::$fields['timetable_status'];
                    echo CHtml::dropDownList('status', $this->get('status'), CHtml::listData($weChat_status, 'value', 'txt'),
                        array(
                            'empty' => '全部',
                        )
                    );
                    ?>&nbsp;
                    <input type="submit" class="but2" value="查询"/>
                </div>
            </div>
        </form>
    </div>
    <div class="mt10 clearfix">
        <div class="l" id="container">
            <?php
            if($add_auth == 'timetable_allAdd'){
                ?>
                <input type="button" class="but2" value="创建全部排期表" onclick="location='<?php echo $this->createUrl('timetable/allAdd') ?>'">
                <?php
            }elseif ($add_auth == 'timetable_comAdd') {
                ?>
                <input type="button" class="but2" value="创建排期表" onclick="location='<?php echo $this->createUrl('timetable/comAdd') ?>'">
                <?php
            }
            ?>
            <?php
            if($edit_auth == 'timetable_editList'){
                ?>
                <input type="button" class="but2"  value="批量编辑" onclick="set_some('<?php echo $this->createUrl('timetable/editList').'?wechat_id=[@]&start_time='.$page['start_time'].'&end_time='.$page['end_time'].'&backurl='.$page['listdata']['url'].'&p=1';?>','none')">
                <?php
            }elseif ($edit_auth == 'timetable_comEdit') {
                ?>
                <input type="button" class="but2" id="comEdit" value="批量编辑" onclick="set_some('<?php echo $this->createUrl('timetable/comEdit').'?wechat_id=[@]&start_time='.$page['start_time'].'&end_time='.$page['end_time'].'&backurl='.$page['listdata']['url'];?>','none')">
                <?php
            }
            ?>
            <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="批量删除" onclick="set_some(\''.$this->createUrl('timetable/delete').'?wechat_id=[@]&start_time='.$page['start_time'].'&end_time='.$page['end_time'].'&url='.$page['listdata']['url'].'\',\'确定删除吗？\');" />','auth_tag'=>'timetable_delete')); ?>
            <?php
            if($this->check_u_menu(array('auth_tag'=>'timetable_export','echo'=>0)))
            {
                ?>
                导出时间段：
                <input type="text" size="20" class="ipt" style="width:80px;font-weight: bold;font-size: 12px"
                       id="startDate" name="start_date" value="" onclick="WdatePicker({qsEnabled:false,isShowWeek:true,firstDayOfWeek:1,dateFmt:'yyyy-MM-dd',maxDate:'#F{$dp.$D(\'endDate\',{d:-1})}'})" readonly/> -
                <input type="text" size="20" class="ipt" style="width:80px;font-weight: bold;font-size: 12px"
                       id="endDate" name="end_date" value="" onclick="WdatePicker({qsEnabled:false,isShowWeek:true,firstDayOfWeek:1,dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(\'startDate\',{d:1})}'})" readonly/>
                <input type="button" class="but2" value="导出" onclick="check_export()">
                <?php
            }
            ?>
        </div>
        <div class="r">
        </div>
    </div>
</div>
<div class="main mbody">
    <form>
        <table class="tb fixTh" style="margin: 0 0">
            <thead>
            <tr>
                <th style="text-align:left;width: 60px;"><a href="javascript:void(0);" onclick="check_all('.cklist');">全选/反选</a></th>
                <th>微信号</th>
                <th>微信号状态</th>
                <th>排期类型</th>
                <th>推广人员</th>
                <th>客服部</th>
                <th>商品</th>
                <?php
                $date_title = PartnerCost::model()->getDateThTwo($page['start_time'], $page['end_time']);
                foreach (array_reverse($date_title) as $v) {
                    echo '<th style="border:1px solid #d1d5de;width: 60px;">' . $v . '</th>';
                }
                ?>
                <th>合计</th>
            </tr>
            <tr>
                <th colspan="6"></th>
                <th>合计</th>
                <?php
                foreach (array_reverse($date_title,true) as $k=>$v) {
                    echo '<th  style="border:1px solid #d1d5de;width: 60px;">' .$page['day_count'][$k] . '</th>';

                }
                ?>
                <th><?php echo $page['count_total'] ;?></th>
            </tr>
            </thead>
            <?php
            foreach($page['wechat_timetable'] as $key=>$wechat){
                ?>
                <tr>
                    <td style="text-align:left"><input class="cklist" value="<?php echo $wechat['id'];?>" type="checkbox" name="wechat_id"><?php echo $wechat['id'];?></td>
                    <td><?php echo $wechat['wechat_id']?></td>
                    <td><?php echo $wechat['timetable_status']?></td>
                    <td><?php echo $wechat['type_name']?></td>
                    <td><?php echo $wechat['ps_name']?></td>
                    <td><?php echo $wechat['cname']?></td>
                    <td><?php echo $wechat['goods_name']?></td>
                    <?php
                    for($i=1;$i<=7;$i++){
                        ?>
                        <td style="border:1px solid #d1d5de"><?php echo  $count = (!empty($wechat['table_list'][$i])  && $wechat['table_list'][$i]['count'] >= 0) ?$wechat['table_list'][$i]['count']:'--';?></td>
                        <?php
                    }
                    ?>
                    <td><?php echo $wechat['count_total']?></td>
                </tr>
                <?php
            }
            ?>
        </table>
        <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
        <div class="clear" style="height: 20px"></div>
    </form>
</div>
<script>
    function check_export() {
        var begin_time = $("#startDate").val();
        var end_time = $("#endDate").val();
        if(begin_time == '' || end_time == ''){
            alert('请先选择导出时间段!');
            return false;
        }
        var data = $("#serchFrom").serialize();
        data += '&start_date='+begin_time+'&end_date='+end_time;
        var url = "<?php echo $this->createUrl('timetable/export');?>";
        window.location.href = url+'?'+data;
    }

</script>
