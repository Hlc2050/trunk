<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<style>
    .qr_delete{
        font-size: large;
        font-weight: bold;
    }
    .qr_delete:link{
        color: red;
    }
    .qr_delete:visited {
        color: red;
    }
    .qr_delete:active {
        color:black;

    }
</style>
<div class="main mhead">
    <div class="snav">渠道管理 » 微信号管理</div>
    <div class="mt10">
        <form action="<?php echo $this->createUrl('weChat/index'); ?>">
            微信号：
            <textarea id="wechat_id" name="wechat_id" rows="1" cols="15"><?php echo $this->get('wechat_id')?$this->get('wechat_id'):''; ?></textarea>
            商品名称：
            <input type="text" id="goods_name" name="goods_name" class="ipt" style="width:130px;"
                   value="<?php echo $this->get('goods_name'); ?>">&nbsp;
            形象：
            <input type="text" id="character" name="character" class="ipt" style="width:130px;"
                   value="<?php echo $this->get('character'); ?>">&nbsp;
            <?php
            $businessTypes = Dtable::toArr(BusinessTypes::model()->findAll());
            echo CHtml::dropDownList('bs_id', $this->get('bs_id'), CHtml::listData($businessTypes, 'bid', 'bname'),
                array(
                    'empty' => '业务类型',
                )
            );
            ?>&nbsp;


            <?php
            $params['htmlOptions'] = array('empty'=>'客服部');
            helper::getServiceSelect('cs_id',$params);
            ?>&nbsp;
            部门:
            <?php $dpt =AdminUserGroup::model()->get_all_Group(0);
            echo CHtml::dropDownList('dt_id', $this->get('dt_id'), CHtml::listData($dpt, 'groupid', 'groupname'),
            array(
            'empty' => '部门',
            )
            );
            ?>&nbsp;
            推广人员:
            <?php
            $promotionStafflist = PromotionStaff::model()->findAll();
            echo CHtml::dropDownList('promotion_staff_id', $this->get('promotion_staff_id'), CHtml::listData($promotionStafflist, 'user_id', 'name'),
                array(
                    'empty' => '推广人员',
                )
            );
            ?>&nbsp;

            <?php
            $weChat_status = vars::$fields['weChat_status'];
            echo CHtml::dropDownList('status', $this->get('status'), CHtml::listData($weChat_status, 'value', 'txt'),
                array(
                    'empty' => '状态',
                )
            );
            ?>&nbsp;
            <input style="margin-top:13px" type="submit" class="but" value="查询">
        </form>
    </div>
    <div class="mt10 clearfix">
        <div class="l" id="container">
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="添加微信号" onclick="location=\'' . $this->createUrl('weChat/add?url='.$page['listdata']['url']) . '\'" />', 'auth_tag' => 'weChat_add')); ?>
            &nbsp;
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="批量上传二维码" onclick="return dialog_frame(this,500,500,1)" href="' . $this->createUrl('weChat/uploadQRCode') . '" />', 'auth_tag' => 'weChat_uploadQRCode')); ?>
            &nbsp;
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="批量匹配落地页" onclick="return dialog_frame(this,600,120,1)" href="' . $this->createUrl('weChat/importLandUrl') . '" />', 'auth_tag' => 'weChat_importLandUrl')); ?>
            &nbsp;
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="删除选中" onclick="set_some(\'' . $this->createUrl('weChat/del') . '?ids=[@]\',\'确定删除吗？\');" />', 'auth_tag' => 'weChat_del')); ?>
            &nbsp;
            <?php
            $params = "?wechat_id=".$this->get('wechat_id')."&dt_id=".$this->get('dt_id')."&cs_id=".$this->get('cs_id')."&bs_id=".$this->get('bs_id')."&status=".$this->get('status')."&goods_name=".$this->get('goods_name')."&character=".$this->get('character');
            $this->check_u_menu(array('code' => '<input type="button" class="but" value="导出微信号"
            onclick="location=\'' . $this->createUrl('weChat/export') .$params.'\'" />','auth_tag' => 'weChat_export')); ?> &nbsp;
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="模板下载" onclick="location=\'' . $this->createUrl('weChat/template') . '\'" />', 'auth_tag' => 'weChat_template')); ?>
            &nbsp;
        </div>
        <form action="<?php echo $this->createUrl('weChat/import'); ?>" method="post" enctype="multipart/form-data">
            <?php $this->check_u_menu(array('code' => '<input type="file"  name="filename"  />', 'auth_tag' => 'weChat_import')); ?>
            &nbsp;
            <?php $this->check_u_menu(array('code' => '<input type="submit" name="submit" class="but2" value="导入" />', 'auth_tag' => 'weChat_import')); ?>
        </form>&nbsp;

        <div class="r">
        </div>
    </div>
</div>
<div class="main mbody">
    <form>
        <table class="tb fixTh">
            <thead>
            <tr>
                <th style="width:60px;"><?php $this->check_u_menu(array('code' => '<a href="javascript:void(0);"  onclick="check_all(\'.cklist\');">全选/反选</a>', 'auth_tag' => 'weChat_del')); ?></th>
                <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('weChat/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '微信号ID', 'field' => 'wechat_id')); ?></th>
                <th>客服部</th>
                <th style="width: 120px">商品</th>
                <th>形象</th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('weChat/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '业务', 'field' => 'business_type')); ?></th>
                <th>计费方式</th>
                <th>推广人员</th>
                <th>部门</th>
                <th>二维码</th>
                <th>落地页</th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('weChat/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '修改时间', 'field' => 'update_time')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('weChat/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '状态', 'field' => 'status')); ?></th>
                <th>操作</th>
            </tr>
            </thead>
            <?php
            foreach ($page['listdata']['list'] as $val) {
                //判断商品在不在客服部中，如果不在则提示
                $goodsInfo = CustomerServiceRelation::model()->find('cs_id=:cs_id and goods_id=:goods_id', array(':cs_id' => $val['customer_service_id'], ':goods_id' => $val['goods_id']));
                ?>
                <tr>
                    <td><?php $this->check_u_menu(array('code' => '<input type="checkbox" class="cklist" value="' . $val['id'] . '"/>', 'auth_tag' => 'weChat_del')); ?></td>
                    <td><?php echo $val['wechat_id']; ?></td>
                    <td><?php echo $val['customer_service']; ?></td>
                    <td><?php if (!$goodsInfo) echo '<s title="客服部找不到该商品" style="color:red;">'; ?><?php echo $val['goods_name'];
                        if (!$goodsInfo) echo '</s>'; ?></td>
                    <td><?php if (!$goodsInfo) echo '<s title="客服部找不到该商品" style="color:red;">'; ?><?php echo $val['character_name'];
                        if (!$goodsInfo) echo '</s>'; ?></td>
                    <td><?php echo $val['business_type']; ?></td>
                    <td><?php echo $val['charging_type']; ?></td>
                    <td><?php echo $val['promotion_staff']; ?></td>
                    <td><?php echo $val['department_name']; ?></td>
                    <td>
                        <?php if ($val['qrcode_id'] != 0): ?>
                            <img src="<?php echo $val['qrcode_img']; ?>" style="width: 25px;height: 25px;"
                                 onclick="return dialog_frame(this,350,400,0)"
                                 href="<?php echo $this->createUrl('weChat/showQRCode', array('id' => $val['id'])); ?>"/>&nbsp;
                            <a href="<?php echo $this->createUrl('weChat/deleteQR', array('id' => $val['id'])); ?>" onclick="return confirm('确认删除该二维码吗')" title="删除二维码" class="qr_delete" data-val="<?php echo $val['id'];?>">X</a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($val['land_url'] != ''){ ?>
                            <a href="#"
                               onclick="dialog({title:'公众号落地页',content:$(this).attr('data-clipboard-text')}).showModal();"
                               data-clipboard-text="<?php echo $val['land_url']; ?>">点击查看</a>
                        <?php }else echo "无" ?>
                    </td>
                    <td><?php echo $val['update_time']; ?></td>
                    <td><?php echo $val['status']; ?></td>
                    <td>
                        <?php $this->check_u_menu(array('code' => '<a onclick="return dialog_frame(this,700,500)" href="' . $this->createUrl('weChatChangeLog/index', array('weixin_id' => $val['id'])) . '">修改记录</a>', 'auth_tag' => 'weChatChangeLog_index')); ?>
                        <br/>
                        <?php $this->check_u_menu(array('code' => '<a href="' . $this->createUrl('weChat/edit?id='.$val['id'].'&url='.$page['listdata']['url']) . '" target="_blank" class="edit_a">修改</a>', 'auth_tag' => 'weChat_edit')); ?>
                        &nbsp;
                        <?php $this->check_u_menu(array('code' => '<a href="' .$this->createUrl('weChat/delete?id='.$val['id'].'&url='.$page['listdata']['url']) . '"  onclick="return confirm(\'确认删除吗\')">删除</a>', 'auth_tag' => 'weChat_delete')); ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
        <div class="clear"></div>
    </form>
</div>
<script>
    $(".edit_a").click(function () {
        $(this).css('color','red');
    });
</script>


