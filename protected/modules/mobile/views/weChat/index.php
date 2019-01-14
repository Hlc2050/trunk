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
                    <strong class="am-text-default am-text-lg"> <span class="am-icon-wechat am-margin-left-sm"></span>&nbsp;微信号列表
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
                                    $departmentList = $this->toArr(AdminGroup::model()->findAll());
                                    $promotionStaffList = PromotionStaff::model()->getPromotionStaffList(1);
                                    $customerServiceList = $this->toArr(CustomerServiceManage::model()->findAll());
                                    $characterList = Linkage::model()->getCharacterList();
                                    $goodsList = Goods::model()->getGoodsList();
                                    $weChat_status = vars::$fields['weChat_status'];
                                    ?>
                                    <tbody>
                                    <tr>
                                        <td class="am-text-middle">微信号id</td>
                                        <td><input type="text" name="wechat_id"
                                                   class="am-input-sm am-form-field am-radius" placeholder="微信号id"
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
                                        <td class="am-text-middle">商品</td>
                                        <td>
                                            <select name="goods_id" class="am-input-sm">
                                                <option value="">全部</option>
                                                <?php foreach ($goodsList as $k => $r) { ?>
                                                    <option <?php if ($k == $this->get('goods_id')) echo "selected" ?>
                                                            value="<?php echo $k ?>"><?php echo $r ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="am-text-middle">形象</td>
                                        <td>
                                            <select name="character_id" class="am-input-sm">
                                                <option value="">全部</option>
                                                <?php foreach ($characterList as $k => $r) { ?>
                                                    <option <?php if ($k == $this->get('character_id')) echo "selected" ?>
                                                            value="<?php echo $k ?>"><?php echo $r ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="am-text-middle">客服部</td>
                                        <td>
                                            <select name="cs_id" class="am-input-sm">
                                                <option value="">全部</option>
                                                <?php foreach ($customerServiceList as $r) { ?>
                                                    <option <?php if ($r['id'] == $this->get('cs_id')) echo "selected" ?>
                                                            value="<?php echo $r['id'] ?>"><?php echo $r['cname'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="am-text-middle">推广小组</td>
                                        <td>
                                            <select name="dt_id" class="am-input-sm">
                                                <option value="">全部</option>
                                                <?php foreach ($departmentList as $r) { ?>
                                                    <option <?php if ($r['groupid'] == $this->get('dt_id')) echo "selected" ?>
                                                            value="<?php echo $r['groupid'] ?>"><?php echo $r['groupname'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="am-text-middle">推广人员</td>
                                        <td>
                                            <select name="promotion_staff_id" class="am-input-sm">
                                                <option value="">全部</option>
                                                <?php foreach ($promotionStaffList as $r) { ?>
                                                    <option <?php if ($r['user_id'] == $this->get('promotion_staff_id')) echo "selected" ?>
                                                            value="<?php echo $r['user_id'] ?>"><?php echo $r['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="am-text-middle">推广状态</td>
                                        <td>
                                            <select name="status" class="am-input-sm">
                                                <option value="">全部</option>
                                                <?php foreach ($weChat_status as $r) { ?>
                                                    <option <?php if ($this->get('status') != '' && $r['value'] == $this->get('status')) echo "selected" ?>
                                                            value="<?php echo $r['value'] ?>"><?php echo $r['txt'] ?></option>
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
                    <div class="am-panel-hd"><?php echo $val['wechat_id']; ?></div>
                    <div class="am-panel-bd">
                        <table class="am-table am-table-centered">
                            <tbody>
                            <tr>
                                <td><strong>客服部：</strong><?php echo $val['customer_service']; ?></td>
                                <td><strong>商品：</strong><?php echo $val['goods_name']; ?></td>
                                <?php if ($this->check_u_menu(array('auth_tag' => 'wechat_edit','echo'=>0))){ ?>
                                    <td rowspan="4" class="am-text-middle"><a
                                                href="<?php echo $this->createUrl("weChat/edit?id=" . $val['id']) ?>"><span
                                                    class="am-icon-chevron-right"></span></a></td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td><strong>形象：</strong><?php echo $val['character_name']; ?></td>
                                <td><strong>推广人员：</strong><?php echo $val['promotion_staff']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>部门：</strong><?php echo $val['department_name']; ?></td>
                                <td><strong>业务：</strong><?php echo $val['business_type']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>计费方式：</strong><?php echo $val['charging_type']; ?></td>
                                <td><strong>状态：</strong><?php echo $val['status']; ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php } ?>


        </div>

    </div>
</div>
<script type="text/javascript">
    window.onpageshow = function(event){
        if (event.persisted) {
            window.location.reload();
        }
    }
</script>
</body>
</html>