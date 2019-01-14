<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<script>
    function exportList() {
        var data = $("#serchForm").serialize();
        var url = "<?php echo $this->createUrl('fansInput/export');?>";
        window.location.href = url+'?'+data;
    }
</script>
<div class="main mhead">
    <div class="snav">订单系统 » 粉丝录入</div>
    <div class="mt10 clearfix">
        <form action="<?php echo $this->createUrl('fansInput/index'); ?>" id="serchForm">
            进粉日期：
            <input type="text" size="20" class="ipt" style="width:120px;" name="start_addfan_date" id="start_addfan_date"
                   value="<?php echo $this->get('start_addfan_date'); ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>-
            <input type="text" size="20" class="ipt" style="width:120px;" name="end_addfan_date" id="end_addfan_date"
                   value="<?php  echo $this->get('end_addfan_date'); ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>&nbsp;
            微信号：
            <input type="text" id="wechat_id" name="wechat_id" class="ipt" style="width:130px;"
                   value="<?php echo $this->get('wechat_id'); ?>">&nbsp;
            客服部：
            <?php
            helper::getServiceSelect('csid');
            ?>
            推广人员:
            <?php
            $promotionStafflist = PromotionStaff::model()->getPromotionStaffList(0);
            echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($promotionStafflist, 'user_id', 'name'),
                array(
                    'empty' => '推广人员',
                )
            );
            ?>
            &nbsp;&nbsp
            商品：
            <?php
            echo $this->get('csid') ? CHtml::dropDownList('goods_id', $this->get('goods_id'), CHtml::listData(CustomerServiceRelation::model()->getGoodsList($this->get('csid')), 'goods_id', 'goods_name'), array('empty' => '全部')) :
                CHtml::dropDownList('goods_id', $this->get('goods_id'),CHtml::listData(Goods::model()->findAll(), 'id', 'goods_name'), array('empty' =>'全部'))
            ?>&nbsp;&nbsp;
            <input type="submit" class="but" value="查询">
        </form>
    </div>
    <div class="mt10 clearfix">
        <div class="l" id="container">
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="删除选中" onclick="set_some(\'' . $this->createUrl('fansInput/del') . '?ids=[@]\',\'确定删除吗？\');" />', 'auth_tag' => 'fansInput_del')); ?>
            &nbsp;
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="模板下载" onclick="location=\'' . $this->createUrl('fansInput/template') . '\'" />', 'auth_tag' => 'fansInput_template')); ?>
            &nbsp;
        </div>
        <form action="<?php echo $this->createUrl('fansInput/import'); ?>" method="post"
              enctype="multipart/form-data">
            <?php $this->check_u_menu(array('code' => '<input type="file"  name="filename"  />', 'auth_tag' => 'fansInput_import')); ?>
            &nbsp;
            <?php $this->check_u_menu(array('code' => '<input type="submit" name="submit" class="but2" value="导入" />', 'auth_tag' => 'fansInput_import')); ?>
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="导出" onclick="exportList()" />', 'auth_tag' => 'fansInput_export')); ?>
        </form>
        <div class="r">
        </div>
    </div>
</div>
<div class="main mbody">
    <form>
        <table class="tb fixTh">
            <thead>
            <tr>
                <th style="width:60px;"><?php $this->check_u_menu(array('code' => '<a href="javascript:void(0);"  onclick="check_all(\'.cklist\');">全选/反选</a>', 'auth_tag' => 'fansInput_del')); ?></th>
                <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('fansInput/index') . '?p=' . $_GET['p'] . '', 'field_cn' => 'ID', 'field' => 'id')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('fansInput/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '加粉日期', 'field' => 'order_date')); ?></th>
                <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('fansInput/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '微信号ID', 'field' => 'b.wechat_id')); ?></th>
                <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('fansInput/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '客服部', 'field' => 'f.cname')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('fansInput/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '业务', 'field' => 'd.bid')); ?></th>
                <th>商品</th>
                <th>计费方式</th>
                <th>推广人员</th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('fansInput/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '进粉量', 'field' => 'a.addfan_count')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('fansInput/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '累计粉丝量', 'field' => 'a.total_fans')); ?></th>
                <th>删除拉黑</th>
                <th>刷粉</th>
                <th>性别不符合粉</th>
                <th>不回复粉</th>
                <th>年龄不符合</th>
                <th>疾病粉</th>
                <th>有效粉</th>
                <th>操作</th>
            </tr>
            <tr>
               <th>-</th><th>-</th><th>-</th><th>-</th> <th>-</th><th>-</th><th>-</th><th>-</th><th>合计</th>
                <th><?php echo  $page['listdata']['addfan_count'];?></th><th><?php echo  $page['listdata']['total_fans'];?></th><th><?php echo  $page['listdata']['del_black'];?></th><th><?php echo  $page['listdata']['brush_fans'];?></th><th><?php echo  $page['listdata']['gender_dif_fans'];?></th>
                <th><?php echo  $page['listdata']['not_reply_fans'];?></th><th><?php echo  $page['listdata']['age_dif_fans'];?></th><th><?php echo  $page['listdata']['disease_fans'];?></th><th><?php echo  $page['listdata']['valid_fans'];?></th><th>-</th>
            </tr>
            </thead>
            <?php $del = $this->check_u_menu(array('auth_tag' => 'fansInput_del'));
                  $edit = $this->check_u_menu(array('auth_tag' => 'fansInput_edit'));
            ?>
            <?php foreach ($page['listdata']['list'] as $key => $val) { ?>
                <?php $valid_fans = $val['addfan_count']-$val['del_black']-$val['brush_fans']-$val['gender_dif_fans']- $val['not_reply_fans']-$val['age_dif_fans']-$val['disease_fans']; ?>
                <tr>
                    <td><?php if ($del) { ?>
                            <input type="checkbox" class="cklist" value="<?php echo $val['id']; ?>"/>
                        <?php }; ?>
                    </td>
                    <td><?php echo $val['id']; ?></td>
                    <td><?php echo date('Y-m-d', $val['addfan_date']); ?></td>
                    <td><?php echo $val['wechat_id']; ?></td>
                    <td><?php echo $page['listdata']['csNames'][$val['customer_service_id']] ?></td>
                    <td><?php echo $page['listdata']['bNames'][$val['business_type']] ?></td>
                    <td><?php echo $page['listdata']['goodsNames'][$val['goods_id']] ?></td>
                    <td><?php echo vars::get_field_str('charging_type', $val['charging_type']); ?></td>
                    <td><?php echo $page['listdata']['userNames'][$val['tg_uid']] ?></td>
                    <td><?php echo $val['addfan_count']; ?></td>
                    <td><?php echo $val['total_fans']; ?></td>
                    <td><?php echo $val['del_black']; ?></td>
                    <td><?php echo $val['brush_fans']; ?></td>
                    <td><?php echo $val['gender_dif_fans']; ?></td>
                    <td><?php echo $val['not_reply_fans']; ?></td>
                    <td><?php echo $val['age_dif_fans']; ?></td>
                    <td><?php echo $val['disease_fans']; ?></td>
                    <td><?php echo $valid_fans;?></td>
                    <td><?php if ($edit) { ?>
                           <a href="<?php echo $this->createUrl('fansInput/edit') .'?id='.$val['id'].'&url='.$page['listdata']['url']; ?>">修改</a>
                        <?php }; ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
        <div class="clear"></div>
    </form>
</div>

