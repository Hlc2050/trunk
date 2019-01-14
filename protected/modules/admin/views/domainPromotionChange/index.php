<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">域名管理 » 推广域名替换记录</div>
    <div class="mt10">
        <form action="<?php echo $this->createUrl('DomainPromotionChange/index'); ?>">
            <select id="search_type" name="search_type">
                <option value="keys" <?php echo $this->get('search_type') == 'keys' ? 'selected' : ''; ?>>域名</option>
                <option
                    value="promotion_id" <?php echo $this->get('search_type') == 'promotion_id' ? 'selected' : ''; ?>>
                    推广ID
                </option>
            </select>&nbsp;
            <input type="text" id="search_txt" name="search_txt" class="ipt"
                   value="<?php echo $this->get('search_txt'); ?>">
            <input type="submit" class="but" value="查询">
        </form>
    </div>

</div>
<div class="main mbody">
    <form action="" name="form_order" method="post">
        <table class="tb fixTh">
            <thead>
            <tr>
                <th width="80"><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('DomainPromotionChange/index') . '?p=' . $_GET['p'] . '', 'field_cn' => 'ID', 'field' => 'id')); ?></th>
                <th width="120"><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('DomainPromotionChange/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '新域名', 'field' => 'domain')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('DomainPromotionChange/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '原域名', 'field' => 'from_domain')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('DomainPromotionChange/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '变更时间', 'field' => 'create_time')); ?></th>
                <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('DomainPromotionChange/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '变更原因', 'field' => 'type')); ?></th>
            </tr>
            </thead>

            <?php
            foreach ($page['listdata']['list'] as $r) {
                ?>
                <tr>
                    <td><?php echo $r['id'] ?></td>
                    <td><?php echo $r['domain'] ?></td>
                    <td><?php echo $r['from_domain'] ?></td>
                    <td><?php echo date("Y-m-d H:i:s", $r['create_time']); ?></td>
                    <td><?php echo vars::get_field_str('domain_change_types', $r['type']); ?></td>
                </tr>
                <?php
            } ?>


        </table>
        <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
        <div class="clear"></div>
    </form>
</div>

