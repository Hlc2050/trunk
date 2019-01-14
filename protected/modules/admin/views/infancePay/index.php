<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead" xmlns="http://www.w3.org/1999/html">
    <div class="snav">财务管理 » 打款</div>

    <div class="mt10">
        <form action="<?php echo $this->createUrl('infancePay/index'); ?>">
            <input type="hidden" id="url" value="<?php echo $this->createUrl('infancePay/inputtip'); ?>">
            <div class="mt10">
                业务类型：
                <?php
                $businessTypes = Dtable::toArr(BusinessTypes::model()->findAll());
                echo CHtml::dropDownList('business_type', $this->get('business_type'), CHtml::listData($businessTypes, 'bid', 'bname'),
                    array(
                        'empty' => '全部',
                    )
                );
                ?>&nbsp;
                计费方式：
                <?php
                $chargeList = vars::$fields['charging_type'];
                echo CHtml::dropDownList('charging_type', $this->get('charging_type'), CHtml::listData($chargeList, 'value', 'txt'),
                    array('empty' => '全部')
                ); ?>
                打款类型：
                <select name="fpay_type">
                    <option value="" <?php echo $this->get('fpay_type') == '' ? 'selected' : ''; ?>>全部</option>
                    <option value="0" <?php echo $this->get('fpay_type') == '0' ? 'selected' : ''; ?>>打款</option>
                    <option value="1" <?php echo $this->get('fpay_type') == '1' ? 'selected' : ''; ?>>特殊</option>
                    <option value="2" <?php echo $this->get('fpay_type') == '2' ? 'selected' : ''; ?>>续费</option>
                </select>&nbsp;
                推广人员： <?php
                $promotionStafflist = AdminUser::model()->get_all_user();
                echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($promotionStafflist, 'csno', 'csname_true'),
                    array('empty' => '全部')
                );
                ?>
                <select id="search_type" name="search_type">
                    <option value="partner_name" <?php echo $this->get('search_type') == 'partner_name' ? 'selected' : ''; ?>>
                        合作商
                    </option>
                    <option value="channel_name" <?php echo $this->get('search_type') == 'channel_name' ? 'selected' : ''; ?>>
                        渠道名称
                    </option>
                    <option value="channel_code" <?php echo $this->get('search_type') == 'channel_code' ? 'selected' : ''; ?>>
                        渠道编码
                    </option>
                </select>&nbsp;
                <input type="text" id="search_txt" name="search_txt" class="ipt" style="width: 120px"
                       value="<?php echo $this->get('search_txt'); ?>">
                收款人：
                <input type="text" id="payee" name="payee" class="ipt" style="width: 120px"
                       value="<?php echo $this->get('payee'); ?>">
            </div>
            <div class="mt10">
                微信号小组：
                <input type="text" id="wechat_group_name" name="wechat_group_name" class="ipt" style="width: 120px"
                       value="<?php echo $this->get('wechat_group_name'); ?>">
                上线日期:
                <input type="text" id="stat_date_s" class="ipt" style="width: 120px;font-size: 15px;" name="stat_date_s"
                       value="<?php echo $this->get('stat_date_s'); ?>" placeholder="起始日期"
                       onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>~
                <input type="text" id="stat_date_e" class="ipt" style="width: 120px;font-size: 15px;" name="stat_date_e"
                       value="<?php echo $this->get('stat_date_e'); ?>" placeholder="结束日期"
                       onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
                付款日期:
                <input type="text" id="pay_date_s" class="ipt" style="width: 120px;font-size: 15px;" name="pay_date_s"
                       value="<?php echo $this->get('pay_date_s'); ?>" placeholder="起始日期"
                       onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>~
                <input type="text" id="pay_date_e" class="ipt" style="width: 120px;font-size: 15px;" name="pay_date_e"
                       value="<?php echo $this->get('pay_date_e'); ?>" placeholder="结束日期"
                       onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>


                <input type="submit" class="but" value="查询">
            </div>

            <div class="mt10">
            </div>
        </form>
        <div class="mt10 clearfix">
            <div class="l">
                <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="添加打款" onclick="location=\'' . $this->createUrl('infancePay/add?url='.$page['listdata']['url']) . '\'" />', 'auth_tag' => 'infancePay_add')); ?>
                <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="添加特殊打款" onclick="location=\'' . $this->createUrl('infancePay/add?url='.$page['listdata']['url']) . '&spcl=1\'" />', 'auth_tag' => 'infancePay_spclAdd')); ?>
                <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="列表导出" onclick="location=\'' . $this->createUrl('infancePay/export') . '?stat_date_s=' . $this->get('stat_date_s') . '&stat_date_e=' . $this->get('stat_date_e') . '&pay_date_s=' . $this->get('pay_date_s') . '&pay_date_e=' . $this->get('pay_date_e') . '&payee='.$this->get('payee').'&charging_type=' . $this->get('charging_type') . '&search_type=' . $this->get('search_type') . '&search_txt=' . $this->get('search_txt'). '&user_id=' . $this->get('user_id') . '\'" />', 'auth_tag' => 'infancePay_export')); ?>
            </div>
            <div class="r">
            </div>
        </div>
    </div>
    <div class="main mbody">
        <form action="<?php echo $this->createUrl('infancePay/saveOrder'); ?>" name="form_order" method="post">
            <table class="tb fixTh">
                <thead>
                <tr>
                    <th width="30">ID</th>
                    <th width="100">上线日期</th>
                    <th width="100">合作商</th>
                    <th width="80">收款人</th>
                    <th width="80">渠道名称</th>
                    <th width="80">渠道编码</th>
                    <th width="60">业务类型</th>
                    <th width="60">计费方式</th>
                    <th width="80">打款金额</th>
                    <th width="60">计费单价</th>
                    <th width="80">微信号小组</th>
                    <th width="100">付款日期</th>
                    <th width="80">推广人员</th>
                    <th width="60">打款方式</th>
                    <th width="130">操作</th>
                </tr>
                </thead>
                <tr>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>合计</td>
                    <td><?php echo $page['listdata']['money']; ?></td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <?php $edit = $this->check_u_menu(array('auth_tag' => 'infancePay_edit'));
                       $del = $this->check_u_menu(array('auth_tag' => 'infancePay_delete'));
                       $renew = $this->check_u_menu(array('auth_tag' => 'infancePay_renew'));
                ?>
                <?php foreach ($page['listdata']['list'] as $r) {?>
                    <tr>
                        <td><?php echo $r['id'] ?></td>
                        <td><?php echo date('Y-m-d', $r['online_date']) ?></td>
                        <td><?php echo $r['name'] ?></td>
                        <td><?php echo $r['payee'] ?></td>
                        <td><?php echo $r['channel_name'] ?></td>
                        <td><?php echo $r['channel_code'] ?></td>
                        <td><?php echo $r['bname']; ?></td>
                        <td><?php echo vars::get_field_str('charging_type', $r['charging_type']); ?></td>
                        <td><?php echo $r['pay_money'] ?></td>
                        <td><?php echo $r['unit_price'] ?></td>
                        <td><?php echo $r['wechat_group_name'] ?></td>
                        <td><?php echo date('Y-m-d', $r['pay_date']) ?></td>
                        <td><?php echo $r['csname_true'] ?></td>
                        <td><?php echo vars::get_field_str('fancePay_types', $r['type']); ?></td>
                        <td>
                            <a onclick="return dialog_frame(this,800,500,0)" href=" <?php echo $this->createUrl('infancePay/print') ?>?id=<?php echo  $r['id'];?>" >打印</a>
                            <?php if ($edit && $r['type'] == 1) { ?>
                                <a href="<?php echo $this->createUrl('infancePay/edit') . '?id=' . $r['id'] . '&spcl=1&url='.$page['listdata']['url'];  ?>">编辑</a>
                            <?php }else{ ?>
                                <a href="<?php echo $this->createUrl('infancePay/edit') . '?id=' . $r['id'] . '&url='.$page['listdata']['url'];  ?>">编辑</a>
                            <?php }; ?>
                            <?php if ($del) { ?>
                                <a href="<?php echo $this->createUrl('infancePay/delete') . '?id=' . $r['id'] . '&url='.$page['listdata']['url']; ?>" onclick="return confirm('确定删除吗')">删除</a>
                            <?php }; ?>
                            <?php if ($renew && $r['is_down'] == 0) { ?>
                                <a href="<?php echo $this->createUrl('infancePay/renew') . '?id=' . $r['id'] . '&url='.$page['listdata']['url']; ?>">续费</a>
                            <?php }; ?>
                        </td>
                    </tr>
                    <?php
                } ?>

            </table>
            <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
            <div class="clear"></div>
        </form>
    </div>

