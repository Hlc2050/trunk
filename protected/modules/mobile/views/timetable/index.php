<?php require(dirname(__FILE__) . "/../common/menu.php"); ?>

<style>
    .am-table {
        margin-bottom: 0;
    }

    .timetable > tbody > tr > td {
        border: 0;
    }

    body {
        /*font-size: 1.2rem;*/
    }

</style>
<div class="admin-content">
    <a id="top" name="top"></a>
    <div style="margin-top: 50px;"></div>

    <div class="admin-content-body">
        <div class="am-panel-group" id="accordion">
            <div class="am-panel am-panel-secd">
                <div class="am-panel-hd">
                    <strong class="am-text-default am-text-lg"> <span class="am-icon-calendar am-margin-left-sm"></span>&nbsp;排期表
                        <span class=" am-panel-title am-icon-angle-double-down am-fr am-margin-right"
                              data-am-collapse="{parent: '#accordion', target: '#do-not-say-1'}"></span></strong>
                </div>
                <div id="do-not-say-1" class="am-panel-collapse am-collapse">
                    <div class="am-panel-bd">
                        <form class="am-form" method="get" action="">
                            <div class="am-form-group">
                                <table class="am-table timetable am-table-centered">
                                    <tbody>
                                    <tr>
                                        <td class="am-text-middle">
                                            <small>微信号</small>
                                        </td>
                                        <td><input type="text" name="wechat_id"
                                                   class="am-input-sm am-form-field am-radius" placeholder="微信号id"
                                                   value="<?php echo $this->get('wechat_id') ?>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="am-text-middle" rowspan="2">
                                            <small>查询日期</small>
                                        </td>
                                        <td><input type="text"  class="am-input-sm am-form-field am-radius"
                                                   id="start_date" name="start_date"
                                                   value="<?php echo $date = $this->get('start_date') ? $this->get('start_date') : $page['start_time']; ?>"
                                                   onclick="WdatePicker({disabledDays:[1,2,3,4,5,6],qsEnabled:false,isShowWeek:true,isShowToday:false,firstDayOfWeek:1,dateFmt:'yyyy-MM-dd',
                           onpicked:function() {
                           var time_obj =$dp.$DV($dp.cal.getDateStr(),{d:+6});console.log(time_obj);$dp.$('end_date').value = time_obj.y+'-'+time_obj.M+'-'+time_obj.d;
                      },onclearing:function() {
                        $dp.$('end_date').value = '';
                      }})" readonly unselectable="on" onfocus="this.blur()" /></td>
                                    </tr>

                                    <tr>
                                        <td><input  type="text" class="am-input-sm am-form-field am-radius"
                                                   id="end_date" name="end_date"
                                                   value="<?php echo $date = $this->get('end_date') ? $this->get('end_date') : $page['end_time']; ?>"
                                                    readonly unselectable="on" onfocus="this.blur()"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="am-text-middle">
                                            <small>客服部</small>
                                        </td>
                                        <td>
                                            <?php
                                            $customerServicelist = Dtable::toArr(CustomerServiceManage::model()->findAll());;
                                            echo CHtml::dropDownList('csid', $this->get('csid'), CHtml::listData($customerServicelist, 'id', 'cname'),
                                                array(
                                                    'empty' => '全部',
                                                    'class' => 'am-input-sm',
                                                )
                                            );
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="am-text-middle">
                                            <small>商品</small>
                                        </td>
                                        <td>
                                            <?php echo $this->get('csid') ? CHtml::dropDownList('goods_id', $this->get('goods_id'), CHtml::listData(CustomerServiceRelation::model()->getGoodsList($this->get('csid')), 'goods_id', 'goods_name'), array('empty' => '全部', 'class' => 'am-input-sm')) :
                                                CHtml::dropDownList('goods_id', $this->get('goods_id'), CHtml::listData(Goods::model()->findAll(), 'id', 'goods_name'), array('empty' => '全部', 'class' => 'am-input-sm'))
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="am-text-middle">
                                            <small>推广小组</small>
                                        </td>
                                        <td>
                                            <?php
                                            $promotionGrouplist = $this->getUserPromotionGroup('edit');
                                            echo CHtml::dropDownList('pgid', $this->get('pgid'), CHtml::listData($promotionGrouplist, 'linkage_id', 'linkage_name'),
                                                array(
                                                    'empty' => '全部',
                                                    'class' => 'am-input-sm',

                                                )
                                            );
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="am-text-middle">
                                            <small>推广人员</small>
                                        </td>
                                        <td>
                                            <?php
                                            echo $this->get('pgid') ? CHtml::dropDownList('tg_id', $this->get('tg_id'), CHtml::listData(PromotionStaff::model()->getPromotionStaffByPg($this->get('pgid')), 'user_id', 'user_name'), array('empty' => '全部', 'class' => 'am-input-sm')) :
                                                CHtml::dropDownList('tg_id', $this->get('tg_id'), CHtml::listData(PromotionStaff::model()->getPromotionStaffByPg(), 'user_id', 'user_name'), array('empty' => '全部', 'class' => 'am-input-sm'))
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="am-text-middle">
                                            <small>微信号状态</small>
                                        </td>
                                        <td>
                                            <?php
                                            $weChat_status = vars::$fields['timetable_status'];
                                            echo CHtml::dropDownList('status', $this->get('status'), CHtml::listData($weChat_status, 'value', 'txt'),
                                                array(
                                                    'empty' => '全部',
                                                )
                                            );
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="am-text-middle">
                                            <input type="submit" class="am-btn am-radius am-btn-primary" value="查询"/>

                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="am-g">
            <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
        </div>
        <div class="am-g">
            <?php foreach ($page['wechat_timetable'] as $key => $wechat) {
                $goodsInfo = CustomerServiceRelation::model()->find('cs_id=:cs_id and goods_id=:goods_id', array(':cs_id' => $val['customer_service_id'], ':goods_id' => $val['goods_id']));
                ?>
                <div class="am-panel am-panel-primary">
                    <!--                    <div class="am-panel-hd">微信号：-->
                    <?php //echo $wechat['wechat_id']; ?><!--</div>-->

                    <div class="am-panel-bd">
                        <table class="am-table timetable">
                            <tbody>
                            <tr>
                                <td>
                                    <small><strong>微信号：</strong><?php echo $wechat['wechat_id']; ?></small>
                                </td>
                                <td>
                                    <small><strong>微信号状态：</strong><?php echo $wechat['timetable_status']; ?></small>
                                </td>

                            </tr>
                            <tr>
                                <td>
                                    <small><strong>排期类型：</strong><?php echo $wechat['type_name']; ?></small>
                                </td>
                                <td>
                                    <small><strong>推广人员：</strong><?php echo $wechat['ps_name']; ?></small>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <small><strong>客服部：</strong><?php echo $wechat['cname']; ?></small>
                                </td>
                                <td>
                                    <small><strong>商品：</strong><?php echo $wechat['goods_name']; ?></small>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div>
                            <table class="am-table am-table-centered am-table-compact am-table-bordered  ">
                                <thead>
                                <tr class="am-primary">
                                    <?php
                                    $date_title = PartnerCost::model()->getDateThTwo($page['start_time'], $page['end_time']);
                                    foreach (array_reverse($date_title) as $v) {
                                        echo '<td><small>' . $v . '</small></td>';
                                    }
                                    ?>
                                    <td>
                                        <small>合计</small>
                                    </td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <?php
                                    for ($i = 1; $i <= 7; $i++) {
                                        ?>
                                        <td>
                                            <small><?php echo $count = (!empty($wechat['table_list'][$i]) && $wechat['table_list'][$i]['count'] >= 0) ? $wechat['table_list'][$i]['count'] : '--'; ?></small>
                                        </td>
                                    <?php } ?>
                                    <td>
                                        <small><?php echo $wechat['count_total'] ?></small>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            <?php } ?>


        </div>

    </div>
</div>
<script src="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/My97DatePicker/WdatePicker.js"></script>
<script type="text/javascript">
    $('body').bind('touchmove', function(e) {
        $('#_my97DP').hide();
    });
    $("#csid").change(function () {
        jQuery.ajax({
            'type': 'POST',
            'url': '<?php echo $this->createUrl('weChat/getGoodsByCs')?>',
            'data': {'csid': $("#csid").val()},
            'cache': false,
            'success': function (html) {
                jQuery("#goods_id").html(html);
            }
        });
    });
    $("#pgid").change(function () {
        jQuery.ajax({
            'type': 'POST',
            'url': '<?php echo $this->createUrl('timetable/getPromotionStaffByPg')?>',
            'data': {'pgid': $("#pgid").val()},
            'cache': false,
            'success': function (html) {
                jQuery("#tg_id").html(html);
            }
        });
    })
</script>

</body>
</html>