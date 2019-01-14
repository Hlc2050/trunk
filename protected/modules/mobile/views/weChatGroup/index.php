<?php require(dirname(__FILE__) . "/../common/menu.php"); ?>
<style>
    .am-table {
        margin-bottom: 0;
    }

    .am-table > tbody > tr > td {
        border: 0;
    }

</style>
<div class="admin-content">
    <a id="top" name="top"></a>
    <div style="margin-top: 50px;"></div>

    <div class="admin-content-body">
        <div class="am-panel-group" id="accordion">
            <div class="am-panel am-panel-secd">
                <div class="am-panel-hd">
                    <strong class="am-text-default am-text-lg"> <span class="am-icon-group am-margin-left-sm"></span>&nbsp;微信小组列表
                        <span class=" am-panel-title am-icon-angle-double-down am-fr am-margin-right"
                              data-am-collapse="{parent: '#accordion', target: '#do-not-say-1'}"></span></strong>
                </div>
                <div id="do-not-say-1" class="am-panel-collapse am-collapse">
                    <div class="am-panel-bd">
                        <form class="am-form" method="get" action="">
                            <div class="am-form-group">
                                <table class="am-table am-table-centered">
                                    <?php
                                    $businessTypes = $this->toArr(BusinessTypes::model()->findAll());
                                    $result = $this->data_authority();
                                    if ($result != 0) {
                                        $where .= " csno in ($result) ";
                                    }else $where='';
                                    $userArr = $this->toArr(AdminUser::model()->findAll($where)); ?>
                                    <tbody>
                                    <tr>
                                        <td class="am-text-middle">微信小组名称</td>
                                        <td><input type="text" name="wechat_group_name"
                                                   class="am-input-sm am-form-field am-radius" placeholder="微信小组名称"
                                                   value="<?php echo $this->get('wechat_group_name') ?>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="am-text-middle">微信号</td>
                                        <td><input type="text" name="wechat_id"
                                                   class="am-input-sm am-form-field am-radius" placeholder="微信号"
                                                   value="<?php echo $this->get('wechat_id') ?>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="am-text-middle">业务类型</td>
                                        <td>
                                            <select name="bs_id" class="am-input-sm">
                                                <option value="">全部</option>
                                                <?php foreach ($businessTypes as $r) { ?>
                                                    <option <?php if ($r['bid'] == $this->get('bs_id')) echo "selected" ?>
                                                            value="<?php echo $r['bid'] ?>"><?php echo $r['bname'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>


                                    <tr>
                                        <td class="am-text-middle">操作用户</td>
                                        <td>
                                            <select name="user_id" class="am-input-sm">
                                                <option value="">全部</option>
                                                <?php foreach ($userArr as $r) { ?>
                                                    <option <?php if ($r['csno'] == $this->get('user_id')) echo "selected" ?>
                                                            value="<?php echo $r['csno'] ?>"><?php echo $r['csname_true'] ?></option>
                                                <?php } ?>
                                            </select>
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
            <?php foreach ($page['listdata']['list'] as $val) {
                $goodsInfo = CustomerServiceRelation::model()->find('cs_id=:cs_id and goods_id=:goods_id', array(':cs_id' => $val['customer_service_id'], ':goods_id' => $val['goods_id']));
                ?>
                <div class="am-panel am-panel-secondary">
                    <div class="am-panel-hd"><?php echo $val['wechat_group_name']; ?></div>
                    <div class="am-panel-bd">
                        <table class="am-table">
                            <tbody>
                            <tr>
                                <td><strong>业务：</strong><?php echo $val['bname']; ?></td>
                                <td><strong>计费方式：</strong><?php echo $val['cname']; ?></td>
                                <?php if ($this->check_u_menu(array('auth_tag' => 'wechatgroup_edit', 'echo' => 0))) { ?>
                                    <td rowspan="4" class="am-text-middle"><a
                                                href="<?php echo $this->createUrl("weChatGroup/edit?id=" . $val['id']) ?>"><span
                                                    class="am-icon-chevron-right"></span></a></td>
                                <?php } ?>                            </tr>
                            <tr>
                                <td><strong>状态：</strong><?php echo $val['status']; ?></td>

                                <td><strong>操作用户：</strong><?php echo $val['operator']; ?></td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>修改时间：</strong><?php echo $val['update_time']; ?></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <strong>微信号：</strong><?php echo helper::cut_str($val['wechatList'], 20); ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php } ?>


        </div>

    </div>
</div>
</body>
</html>