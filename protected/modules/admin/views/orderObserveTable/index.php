<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
    <div class="main mhead">
        <div class="snav">进线观察表</div>

        <div class="mt10">
            <form action="<?php echo $this->createUrl('orderObserveTable/index'); ?>">
                合作商名称：
                <input type="text" id="partner_name" name="partner_name" class="ipt"
                       value="<?php echo $this->get('partner_name'); ?>">
                &nbsp;&nbsp;
                <select id="search_type" name="search_type">
                    <option
                            value="channel_code" <?php echo $this->get('search_type') == 'channel_code' ? 'selected' : ''; ?>>
                        渠道编码
                    </option>
                    <option
                            value="channel_name" <?php echo $this->get('search_type') == 'channel_name' ? 'selected' : ''; ?>>
                        渠道名称
                    </option>

                </select>&nbsp;
                <input type="text" id="search_txt" name="search_txt" class="ipt"
                       value="<?php echo $this->get('search_txt'); ?>">
                &nbsp;
                客服部：
                <?php
                $customerServiceList = Dtable::toArr(CustomerServiceManage::model()->findAll());
                echo CHtml::dropDownList('csid', $this->get('csid'), CHtml::listData($customerServiceList, 'id', 'cname'),
                    array('empty' => '请选择',)
                );
                ?>
                推广小组：
                <?php
                $promotionGrouplist = Linkage::model()->getPromotionGroupList();
                echo CHtml::dropDownList('pgid', $this->get('pgid'), CHtml::listData($promotionGrouplist, 'linkage_id', 'linkage_name'),
                    array(
                        'empty' => '全部',
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
                echo $this->get('pgid') ? CHtml::dropDownList('tg_id', $this->get('tg_id'), CHtml::listData(PromotionStaff::model()->getPromotionStaffByPg($this->get('pgid')), 'user_id', 'user_name'), array('empty' => '全部')) :
                    CHtml::dropDownList('tg_id', $this->get('tg_id'), array('全部'))
                ?>&nbsp;&nbsp;
                <input type="submit" class="but" value="查询">

            </form>
        </div>
        <div class="mt10 clearfix">
            <div class="l">
            </div>
            <div class="r">
            </div>
        </div>
    </div>
    <div class="main mbody">
        <table class="tb fixTh" style="width:700px;">
            <thead>
            <tr>
                <th align='center'>合作商</th>
                <th align='center'>渠道</th>
                <th align='center'>渠道编码</th>
                <th align='center'>推广人员</th>
                <th align='center'>客服部</th>
                <th align='center'>进线量</th>
                <th align='center'>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($page['listdata']['list'] as $k=>$r){
                ?>
                <tr>
                    <td><?php echo $page['listdata']['partnerNames'][$r['partner_id']];?></td>
                    <td><?php echo $page['listdata']['channelNames'][$r['channel_id']];?></td>
                    <td><?php echo $page['listdata']['channelCodes'][$r['channel_id']];?></td>
                    <td><?php echo $page['listdata']['tgNames'][$r['promotion_staff_id']];?></td>
                    <td><?php echo $page['listdata']['csNames'][$r['customer_service_id']];?></td>
                    <td><?php echo $r['in_count'];?></td>
                    <td>
                        <a href="<?php echo $this->createUrl('orderObserveTable/channelDetail?url='.$page['listdata']['url'].'&csid='.$r['customer_service_id'].'&channel_id='.$r['channel_id']) ?>">详情</a>
                    </td>

                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
        <div class="clear"></div>
    </div>
