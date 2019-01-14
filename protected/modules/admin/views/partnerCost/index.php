<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead" xmlns="http://www.w3.org/1999/html">
    <div class="snav">财务管理 » 合作商费用日志</div>

    <div class="mt10">
        <form action="<?php echo $this->createUrl('partnerCost/index'); ?>">
            日期：
            <input type="text" id="pay_date_s" class="ipt"  style="width:120px;" name="date_s"
                   value="<?php echo $this->get('date_s'); ?>" placeholder="起始日期"
                   onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
            <input type="text" id="pay_date_e" class="ipt" style="width:120px;" name="date_e"
                   value="<?php echo $this->get('date_e'); ?>" placeholder="结束日期"
                   onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>&nbsp;&nbsp;
            推广人员：
            <?php
            $promotionStafflist = AdminUser::model()->get_all_user();
            echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($promotionStafflist, 'csno', 'csname_true'),
                array('empty' => '全部')
            );
            ?>&nbsp;&nbsp;
            计费方式：
            <select id="chgId" name="chgId">
                <option value=" ">请选择</option>
                <?php $chargeList = vars::$fields['charging_type'];?>
                <?php  foreach ($chargeList as $key => $val) { ?>
                    <option value="<?php echo $val['value']; ?>" <?php if ($_GET['chgId'] != '' && $_GET['chgId'] == $val['value']) echo 'selected'; ?>><?php echo $val['txt']; ?></option>
                <?php } ?>
            </select>&nbsp;&nbsp;
            <select id="search_type" name="search_type">
                <option value="channel_name" <?php echo $this->get('search_type') == 'channel_name' ? 'selected' : ''; ?>>渠道名称</option>
                <option value="channel_code" <?php echo $this->get('search_type') == 'channel_code' ? 'selected' : ''; ?>>渠道编码</option>
            </select>&nbsp;
            <input style="width:100px;" type="text" id="search_txt" name="search_txt" class="ipt"
                   value="<?php echo $this->get('search_txt'); ?>">&nbsp;&nbsp;
            合作商：
            <input style="width:100px;" type="text" id="partner_name" name="partner_name" class="ipt"
                   value="<?php echo $this->get('partner_name'); ?>">&nbsp;&nbsp;
            推广类型：
            <?php
            $promotion_types = vars::$fields['promotion_types'];
            echo CHtml::dropDownList('promotion_type', $this->get('promotion_type'), CHtml::listData($promotion_types, 'value', 'txt'),
                array('empty' => '全部')
            );
            ?>&nbsp;&nbsp;
            <input type="submit" class="but" value="查询">
        </form>
        <div class="mt10 clearfix">
            <div class="l">
                <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="删除" onclick="set_some(\'' . $this->createUrl('partnerCost/delete') . '?ids=[@]\',\'确定删除吗？\');" />', 'auth_tag' => 'partnerCost_delete')); ?>
                <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="列表导出" onclick="location=\'' . $this->createUrl('partnerCost/export') . '?user_id=' .$this->get('user_id'). '&date_s=' .$this->get('date_s'). '&date_e=' . $this->get('date_e'). '&partner_name=' . $this->get('partner_name').'&promotion_type=' .$this->get('promotion_type') . '&chgId=' .$this->get('chgId') . '&search_type=' . $this->get('search_type') . '&search_txt=' . $this->get('search_txt') . '\'" />', 'auth_tag' => 'partnerCost_export')); ?>
            </div>
            <div class="r">

            </div>
        </div>
    </div>
    <div class="main mbody">
        <form action="<?php echo $this->createUrl('partnerCost/index'); ?>" name="form_order" method="post">
            <table class="tb fixTh">
                <thead>
                <tr>
                    <th width="40" class="cklist"><a href="javascript:void(0);" class="cklist" onclick="check_all('.cklist');">全选/反选</a></th>
                    <th width="40">ID</th>
                    <th width="80">日期</th>
                    <th width="100">合作商</th>
                    <th width="80">渠道名称</th>
                    <th width="80">渠道编码</th>
                    <th width="80">计费方式</th>
                    <th width="80">打款金额</th>
                    <th width="100">合作商提供费用</th>
                    <th width="100">友盟生成费用</th>
                    <th width="100">费用相比（相减）</th>
                    <th width="100">渠道余额</th>
                    <th width="60">类型</th>
                    <th width="60">推广人员</th>
                    <th width="80">操作</th>
                </tr>
                <tr>
                    <th>-</th><th>-</th><th>-</th> <th>-</th><th>-</th><th>-</th><th>合计</th>
                    <th><?php echo  $page['listdata']['pay_moneys'];?></th>
                    <th><?php echo  $page['listdata']['partner_cost'];?></th>
                    <th><?php echo  $page['listdata']['system_cost'];?></th>
                    <th><?php echo  round($page['listdata']['partner_cost']-$page['listdata']['system_cost'], 2)?>
                    </th>
                    <th><?php echo  round($page['listdata']['pay_moneys']-$page['listdata']['partner_cost'], 2)?></th>
                    <th>-</th><th>-</th><th>-</th>
                </tr>
                </thead>
                <tr id="totalInfo"></tr>

                <?php $edit = $this->check_u_menu(array('auth_tag'=> 'partnerCost_edit'));  ?>
                <?php
                foreach ($page['listdata']['list'] as $r) { ?>
                    <tr>
                        <td><input type="checkbox" class="cklist" value="<?php echo $r['id']; ?>"/></td>
                        <td><?php echo $r['id'] ?></td>
                        <td><?php echo date('Y-m-d', $r['date']) ?></td>
                        <td><?php echo $r['name'] ?></td>
                        <td><?php echo $r['channel_name'] ?></td>
                        <td><?php echo $r['channel_code'] ?></td>
                        <td><?php echo vars::get_field_str('charging_type', $r['charging_type']) ?></td>
                        <td><?php echo $r['pay_money']; ?></td>
                        <td><?php echo $r['partner_cost'] ?></td>
                        <td><?php echo $r['system_cost'] ?></td>
                        <td><?php echo $r['partner_cost'] - $r['system_cost'] ?></td>
                        <td><?php echo $r['channel_balance']?$r['channel_balance']:0 ?></td>
                        <td><?php echo  vars::get_field_str('promotion_types', $r['promotion_type']) ?></td>
                        <td><?php echo  $r['csname_true'] ?></td>
                        <td>
                            <?php if ($edit) { ?>
                                <a href="<?php echo $this->createUrl('partnerCost/edit') . '?id=' . $r['id'] . '&url='. $page['listdata']['url']; ?>">编辑</a>
                            <?php }else if($r['update_time'] == 0 && !$edit){ ?>
                                    <a href="<?php echo $this->createUrl('partnerCost/toEdit') ?>?id=<?php echo $r['id'] ?>&url=<?php echo $page['listdata']['url'] ?>">编辑</a>
                            <?php }; ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
            <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
            <div class="clear"></div>
        </form>

    </div>
</div>

