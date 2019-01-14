<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">财务管理 » 成本明细 » 编辑</div>
</div>
<div class="main mbody">
    <form method="post" action="<?php echo $this->createUrl('statCostDetail/edit'); ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <input type="hidden" id="backurl" name="backurl" value="<?php echo $this->get('url'); ?>"/>
        <table class="tb3">
            <tr>
                <td width="120">ID：</td>
                <td class="alignleft"><?php echo $page['info']['id']; ?></td>
            </tr>
            <tr>
                <td>上线日期：</td>
                <td class="alignleft"><?php echo $page['info']['stat_date']; ?></td>
            </tr>
            <tr>
                <td>付款日期：</td>
                <td class="alignleft"><?php echo $page['info']['pay_date']; ?></td>
            </tr>
            <tr>
                <td>推广人员：</td>
                <td class="alignleft"><?php echo $page['info']['uname']; ?></td>
            </tr>
            <tr>
                <td>推广小组：</td>
                <td><?php echo $page['info']['tg_group']; ?></td>
            </tr>
            <tr>
                <td>合作商：</td>
                <td class="alignleft"><?php echo $page['info']['partner']; ?></td>
            </tr>
            <tr>
                <td>渠道名称：</td>
                <td class="alignleft"><?php echo $page['info']['channel_name']; ?></td>
            </tr>
            <tr>
                <td>渠道编码：</td>
                <td class="alignleft"><?php echo $page['info']['channel_code']; ?></td>
            </tr>
            <tr>
                <td>客服部：</td>
                <td class="alignleft"><?php echo $page['info']['cname']; ?></td>
            </tr>
            <tr>
                <td>商品：</td>
                <td class="alignleft"><?php echo $page['info']['goods_name']; ?></td>
            </tr>
            <tr>
                <td>业务类型：</td>
                <td class="alignleft">
                    <?php
                    $businessTypes = Dtable::toArr(BusinessTypes::model()->findAll());
                    echo CHtml::dropDownList('business_type', $page['info']['business_type'], CHtml::listData($businessTypes, 'bid', 'bname'),
                        array(
                            'empty' => '选择业务类型',
                            'ajax' => array(
                                'type' => 'POST',
                                'url' => $this->createUrl('weChat/getChargingType'),
                                'update' => '#charging_type',
                                'data' => array('business_type' => 'js:$("#business_type").val()'),
                            )
                        )
                    );
                    ?>
                </td>
            </tr>
            <tr>
                <td width="120">计费方式：</td>
                <td class="alignleft">
                    <?php echo CHtml::dropDownList('charging_type', $page['info']['charging_type'], CHtml::listData($page['info']['chargingTypeList'], 'charging_type', 'cname'), array('--计费方式--')) ?>
                </td>
            </tr>

            <tr>
                <td>友盟金额：</td>
                <td class="alignleft"><input name="money" value="<?php echo $page['info']['money']; ?>"/></td>
            </tr>
<!--            <tr>-->
<!--                <td>第三方金额：</td>-->
<!--                <td class="alignleft"><input name="third_money" value="--><?php //echo $page['info']['third_money']; ?><!--"/>-->
<!--                </td>-->
<!--            </tr>-->
            <tr>
                <td></td>
                <td class="alignleft">
                    <input type="submit" class="but" id="subtn" value="确定"/>
                    <input type="button" class="but" value="返回" onclick="window.location='<?php echo $this->get('url'); ?>'"/>
                </td>
            </tr>
        </table>
    </form>
</div>
