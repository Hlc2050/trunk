<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
    <div class="main mhead">
        <div class="snav">渠道管理 » 合作商列表 » 渠道列表</div>
        <div class="mt10">
            <form>
                <select id="search_type">
                    <option value="keys" <?php echo $this->get('search_type') == 'keys' ? 'selected' : ''; ?>>渠道名称
                    </option>
                    <option value="id" <?php echo $this->get('search_type') == 'id' ? 'selected' : ''; ?>>渠道编码</option>
                </select>&nbsp;
                <input type="text" id="search_txt" class="ipt"
                       value="<?php echo isset($_GET['search_txt']) ? $_GET['search_txt'] : ''; ?>">&nbsp;
                <input type="button" class="but" value="查询"
                       onclick="window.location='<?php echo $this->createUrl('partner/channelIndex'); ?>?partner_id=<?php echo $page['partnerId']; ?>&search_type='+$('#search_type').val()+'&search_txt='+$('#search_txt').val();"/>
            </form>
        </div>
        <div class="mt10 clearfix">
            <div class="l">
                <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="添加渠道" onclick="location=\'' . $this->createUrl('partner/channelAdd?partner_id='.$page['partnerId']."&url=".$page['listdata']['url']) . '\'" />', 'auth_tag' => 'partner_channelAdd')); ?>
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
                    <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('partner/channel') . '?p=' . $_GET['p'] . '', 'field_cn' => 'ID', 'field' => 'id')); ?></th>
                    <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('partner/channel') . '?p=' . $_GET['p'] . '', 'field_cn' => '渠道编码', 'field' => 'channel_code')); ?></th>
                    <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('partner/channel') . '?p=' . $_GET['p'] . '', 'field_cn' => '渠道名称', 'field' => 'channel_name')); ?></th>
                    <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('partner/channel') . '?p=' . $_GET['p'] . '', 'field_cn' => '渠道类型', 'field' => 'channel_type')); ?></th>
                    <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('partner/channel') . '?p=' . $_GET['p'] . '', 'field_cn' => '合作商', 'field' => 'partner_id')); ?></th>
                    <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('partner/channel') . '?p=' . $_GET['p'] . '', 'field_cn' => '业务', 'field' => 'business_type')); ?></th>
                    <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('partner/channel') . '?p=' . $_GET['p'] . '', 'field_cn' => '修改时间', 'field' => 'update_time')); ?></th>
                    <th class="alignleft">操作</th>
                </tr>
                </thead>
                <?php
                foreach ($page['listdata']['list'] as $r) {
                    ?>
                    <tr>
                        <td><?php echo $r['id']; ?></td>
                        <td><?php echo $r['channel_code']; ?></td>
                        <td><?php echo $r['channel_name']; ?></td>
                        <td><?php echo $r['type_name']; ?></td>
                        <td><?php echo $r['partnerName']; ?></td>
                        <td><?php echo BusinessTypes::model()->findByPk($r['business_type'])->bname; ?></td>
                        <td><?php echo date('Y-m-d', $r['update_time']); ?></td>
                        <td class="alignleft">
                            <?php $this->check_u_menu(array('code' => '<a href="' . $this->createUrl('partner/channelEdit?partner_id='.$page['partnerId']."&id=".$r['id']."&url=".$page['listdata']['url']) . '">修改</a>', 'auth_tag' => 'partner_channelEdit')); ?>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <?php $this->check_u_menu(array('code' => '<a href="' . $this->createUrl('partner/channelDelete?partner_id='.$page['partnerId']."&id=".$r['id']."&url=".$page['listdata']['url']) . '"  onclick="return confirm(\'确定删除吗\')">删除</a>', 'auth_tag' => 'partner_channelDelete')); ?>
                        </td>
                    </tr>
                    <?php
                } ?>

            </table>
            <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
            <div class="clear"></div>
        </form>
    </div>


