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
                    <strong class="am-text-default am-text-lg"> <span class="am-icon-calendar am-margin-left-sm"></span>&nbsp;微信效果表
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
                                        <td class="am-text-middle"> <small>微信号</small></td>
                                        <td><input type="text" name="wechat_id"
                                                   class="am-input-sm am-form-field am-radius" placeholder="微信号id"
                                                   value="<?php echo $this->get('wechat_id') ?>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="am-text-middle" rowspan="2"> <small>查询日期</small></td>
                                        <td>  <input type="text"  class="am-input-sm am-form-field am-radius" name="start_date"
                                                     id="start_date"
                                                     value="<?php echo $page['first_day'] ? $page['first_day'] : date("Y-m-d", mktime(0, 0, 0, date('m'), 1, date('Y'))); ?>"
                                                     onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" readonly unselectable="on" onfocus="this.blur()" /></td>
                                    </tr>

                                    <tr>
                                        <td>  <input type="text"  class="am-input-sm am-form-field am-radius" name="end_date"
                                                     id="end_date"
                                                     value="<?php echo $page['last_day'] ? $page['last_day'] : date("Y-m-d", time()); ?>"
                                                     onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" readonly unselectable="on" onfocus="this.blur()" />
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="am-text-middle"> <small>客服部</small></td>
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
                                        <td class="am-text-middle"> <small>商品</small></td>
                                        <td>
                                            <?php   echo $this->get('csid') ? CHtml::dropDownList('goods_id', $this->get('goods_id'), CHtml::listData(CustomerServiceRelation::model()->getGoodsList($this->get('csid')), 'goods_id', 'goods_name'), array('empty' => '全部', 'class' => 'am-input-sm')) :
                                                CHtml::dropDownList('goods_id', $this->get('goods_id'),CHtml::listData(Goods::model()->findAll(), 'id', 'goods_name'), array('empty' =>'全部', 'class' => 'am-input-sm'))
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="am-text-middle"> <small>推广人员</small></td>
                                        <td>
                                            <?php
                                            echo CHtml::dropDownList('tg_id', $this->get('tg_id'), CHtml::listData(PromotionStaff::model()->getPromotionStaffByPg(), 'user_id', 'user_name'), array('empty' => '全部', 'class' => 'am-input-sm'))
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
            <?php foreach ($page['info'] as $key => $val) {
                $goodsInfo = CustomerServiceRelation::model()->find('cs_id=:cs_id and goods_id=:goods_id', array(':cs_id' => $val['customer_service_id'], ':goods_id' => $val['goods_id']));
                ?>
                <div class="am-panel am-panel-secondary">
                    <div class="am-panel-hd"><?php echo $val['wechat_id']; ?></div>
                    <div class="am-panel-bd">
                        <table class="am-table timetable">
                            <tbody>
                            <tr>
                                <td>
                                    <small><strong>预估发货金额：</strong><?php echo $val['estimate_money']; ?></small>
                                </td>
                                <td>
                                    <small><strong>投入金额：</strong><?php echo $val['money']; ?></small>
                                </td>

                            </tr>
                            <tr>
                                <td>
                                    <small><strong>进粉量：</strong><?php echo $val['fans_count']; ?></small>
                                </td>
                                <td>
                                    <small><strong>均粉产出：</strong><?php echo $val['fans_avg']; ?></small>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <small><strong>平均进粉成本：</strong><?php echo $val['fans_cost']; ?></small>
                                </td>

                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php } ?>


        </div>


    </div>
</div>
<script src="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/My97DatePicker/WdatePicker.js" ></script>
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
    })
</script>


</body>
</html>