<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">推广管理 » 上线素材列表</div>
    <div class="mt10">
        <form action="<?php echo $this->createUrl('onlineMaterial/index'); ?>" >

            <select id="search_type" name="search_type">
                <option value="partner" <?php echo $this->get('search_type') == 'partner' ? 'selected' : ''; ?>>合作商</option>
                <option value="channel" <?php echo $this->get('search_type') == 'channel' ? 'selected' : ''; ?>>渠道名称</option>
                <option value="channel_code" <?php echo $this->get('search_type') == 'channel_code' ? 'selected' : ''; ?>>渠道编码</option>
                <option value="id" <?php echo $this->get('search_type') == 'id' ? 'selected' : ''; ?>>推广ID</option>
            </select>&nbsp;
            <input style="width:120px;" type="text" id="search_txt" name="search_txt" class="ipt"
                   value="<?php echo isset($_GET['search_txt']) ? $_GET['search_txt'] : ''; ?>">&nbsp;
            图文编码：
            <input style="width:120px;" type="text" name="article_code" class="ipt"
                   value="<?php echo $this->get('article_code'); ?>">
            &nbsp;
            商品类别：
            <select name="cat_id" id="cat_id">
                <option value="" selected>
                    全部
                </option>
                <?php
                //商品类别列表
                $categoryList = Linkage::model()->getGoodsCategoryList();
                foreach ($categoryList as $key => $val) { ?>
                    <option
                            value="<?php echo $key; ?>" <?php echo isset($_GET['cat_id']) && ($key == $_GET['cat_id']) ? 'selected' : ''; ?>>
                        <?php echo $val; ?>
                    </option>
                <?php } ?>
            </select>
            状态：
            <select name="status" id="status">
                <option value="" selected="selected">全部</option>
                <option value="0" <?php if ($_GET['status'] != '' && $_GET['status'] == 0) echo 'selected'; ?>>正常
                </option>
                <option value="1" <?php if ($_GET['status'] == 1) echo 'selected'; ?>>下线</option>
                <option value="2" <?php if ($_GET['status'] == 2) echo 'selected'; ?>>暂停</option>
            </select>
            推广人员： <?php
            $promotionStafflist = PromotionStaff::model()->getPromotionStaffList(0,1);
            echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($promotionStafflist, 'user_id', 'name'),
                array('empty' => '请选择')
            );
            ?>&nbsp;
            推广类型：
            <?php
            $promotion_types = vars::$fields['promotion_types'];
            echo CHtml::dropDownList('promotion_type', $this->get('promotion_type'), CHtml::listData($promotion_types, 'value', 'txt'),
                array('empty' => '全部')
            );
            ?>
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
    <form>
        <table class="tb fixTh">
            <thead>
            <tr>
                <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('onlineMaterial/index') . '?p=' . $_GET['p']  . '', 'field_cn' => '推广ID', 'field' => 'id')); ?></th>
                <th>合作商</th>
                <th>渠道名称</th>
                <th>渠道编码</th>
                <th>图文编码</th>
                <th>推广类型</th>
                <th>标题</th>
                <th>类型</th>
                <th>推广人员</th>
                <th>最后更新</th>
                <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('onlineMaterial/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '状态', 'field' => 'status')); ?></th>
                <th>操作</th>
            </tr>
            </thead>
            <?php $eidt = $this->check_u_menu(array('auth_tag' => 'onlineMaterial_edit'));  ?>
            <?php foreach ($page['listdata']['list'] as $key => $val) { ?>
                <tr>
                    <td><?php echo $val['promotion_id']; ?></td>
                    <td><?php echo $val['partner_name']; ?></td>
                    <td><?php echo $val['channel_name']; ?></td>
                    <td><?php echo $val['channel_code']; ?></td>
                    <td><?php echo $val['article_code']; ?></td>
                    <td><?php echo  $val['promotion_type']==''?'-':vars::get_field_str('promotion_types', $val['promotion_type']); ?></td>
                    <td style="max-width: 350px"><?php echo $val['article_title']; ?></td>
                    <td><?php echo $val['linkage_name']; ?></td>
                    <td><?php echo $val['csname_true']; ?></td>
                    <td><?php echo date('Y-m-d h:i:s', $val['update_time']); ?></td>
                    <td><?php echo vars::get_field_str('promotion_status', $val['status']); ?></td>
                    <td>
                        <?php if ($eidt) { ?>
                            <a href="<?php echo $this->createUrl('onlineMaterial/edit?id=' . $val['id'] . '&url=' . $page['listdata']['url']); ?>">编辑</a>
                        <?php }; ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>

        <div class="clear"></div>
    </form>
</div>
