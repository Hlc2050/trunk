<?php require(dirname(__FILE__) . "/../common/head.php");
?>
<div class="main mhead" xmlns="http://www.w3.org/1999/html">
    <div class="snav">推广效果 » 微信号预估发货金额</div>

    <div class="mt10">
        <form action="<?php echo $this->createUrl('effectWechatDeliver/index'); ?>">
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
                <input type="text" id="start_date" class="ipt" size="20" name="start_date"
                       value="<?php echo $this->get('start_date') == '' ? date('Y-m-d', strtotime("-9 day")) : $this->get('start_date'); ?>"
                       placeholder="起始日期" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/> 一
                <input type="text" id="end_date" class="ipt" size="20" name="end_date"
                       value="<?php echo $this->get('end_date') == '' ? date('Y-m-d') : $this->get('end_date'); ?>"
                       placeholder="结束日期" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>

                <input type="submit" class="but" value="查询">
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
                    $date_title = PartnerCost::model()->getDateTh($this->get('start_date'), $this->get('end_date'));
                    foreach ($date_title as $v) {
                        echo '<th width="80">' . $v . '</th>';
                    }
                    ?>
                </tr>
                </thead>
                <?php
                foreach ($page['info'] as $r) {
                    $count = count($r);
                    ?>
                    <tr>
                        <td><?php echo $r[0]; ?></td>
                        <!--<td><?php /*echo Goods::model()->findByPk($r[0][1])->goods_name;*/
                        ?></td>-->
                        <?php for ($i = 1; $i < $count; $i++) {
                            echo "<td>" . $r[$i] . "</td>";
                        } ?>
                    </tr>
                    <?php
                } ?>

            </table>
            <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
            <div class="clear"></div>
        </form>
    </div>

