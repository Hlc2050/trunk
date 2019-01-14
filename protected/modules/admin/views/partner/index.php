<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
    <div class="main mhead">
        <div class="snav">渠道管理 » 合作商列表</div>

        <div class="mt10">
            <select id="search_type">
                <option value="keys" <?php echo $this->get('search_type') == 'keys' ? 'selected' : ''; ?>>合作商名称</option>
                <option value="id" <?php echo $this->get('search_type') == 'id' ? 'selected' : ''; ?>>ID</option>
                <option value="channel" <?php echo $this->get('search_type') == 'channel' ? 'selected' : ''; ?>>渠道名称</option>
            </select>&nbsp;
            <input type="text" id="search_txt" class="ipt"
                   value="<?php echo isset($_GET['search_txt']) ? $_GET['search_txt'] : ''; ?>">&nbsp;<input
                type="button" class="but" value="查询"
                onclick="window.location='<?php echo $this->createUrl('partner/index'); ?>?search_txt='+$('#search_txt').val()+'&search_type='+$('#search_type').val();">
        </div>
        <div class="mt10 clearfix">
            <div class="l">
                <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="添加合作商" onclick="location=\'' . $this->createUrl('partner/add?url='.$page['listdata']['url']) . '\'" />', 'auth_tag' => 'partner_add')); ?>
                &nbsp;
                <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="模板下载" onclick="location=\'' . $this->createUrl('partner/template') . '\'" />', 'auth_tag' => 'partner_template')); ?>
                &nbsp;
            </div>
            <div style="float: left;">
                <form action="<?php echo $this->createUrl('partner/import'); ?>" method="post"
                      enctype="multipart/form-data">
                    <?php $this->check_u_menu(array('code' => '<input type="file"  name="filename"  />', 'auth_tag' => 'partner_import')); ?>
                    &nbsp;
                    <?php $this->check_u_menu(array('code' => '<input type="submit" name="submit" class="but2" value="导入" />', 'auth_tag' => 'partner_import')); ?>
                </form>

            </div>
            <div style="float: left;">
                <form action="<?php echo $this->createUrl('partner/export'); ?>" method="post"
                      enctype="multipart/form-data">

                    <?php $this->check_u_menu(array('code' => '<input type="submit" style="margin-left: 20px;" name="submit"  class="but2" value="导出" />', 'auth_tag' => 'partner_export')); ?>
                </form>
            </div>
            <div >
                <form action="<?php echo $this->createUrl('partner/typeImport'); ?>" method="post"
                      enctype="multipart/form-data">
                    <?php $this->check_u_menu(array('code' => '<input type="file" style="margin-left: 10px;"  name="typename"  />', 'auth_tag' => 'partner_typeImport')); ?>
                    &nbsp;
                    <?php $this->check_u_menu(array('code' => '<input type="submit" style="margin-left: 20px;" name="submit"  class="but2" value="渠道类型导入" />', 'auth_tag' => 'partner_typeImport')); ?>
                </form>
            </div>


            <div class="r">
            </div>
        </div>
    </div>
    <div class="main mbody">
        <form>
            <table class="tb fixTh" style="width: 800px;">
                <thead>
                <tr>
                    <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('partner/index') . '?p=' . $_GET['p'] . '', 'field_cn' => 'ID', 'field' => 'id')); ?></th>
                    <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('partner/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '合作商（收款人）', 'field' => 'name')); ?></th>
                    <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('partner/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '修改时间', 'field' => 'update_time')); ?></th>
                    <th class="alignleft">操作</th>
                </tr>
                </thead>
                <?php
                foreach ($page['listdata']['list'] as $r) {
                    ?>
                    <tr>
                        <td><?php echo $r['id']; ?></td>
                        <td><?php echo $r['name']; ?></td>
                        <td><?php echo date('Y-m-d', $r['update_time']); ?></td>
                        <td class="alignleft">
                            <?php $this->check_u_menu(array('code' => '<a href="' . $this->createUrl('partner/channelIndex', array('partner_id' => $r['id'])) . '" onclick="return dialog_frame(this)">渠道</a>', 'auth_tag' => 'partner_channelIndex')); ?>
                            &nbsp;&nbsp;
                            <?php $this->check_u_menu(array('code' => '<a href="' . $this->createUrl('partner/edit', array('partner_id' => $r['id'])) . '">修改</a>', 'auth_tag' => 'partner_edit')); ?>
                            &nbsp;&nbsp;
                            <?php $this->check_u_menu(array('code' => '<a href="' . $this->createUrl('partner/delete', array('partner_id' => $r['id'])) . '"  onclick="return confirm(\'确定删除吗\')">删除</a>', 'auth_tag' => 'partner_delete')); ?>
                        </td>
                    </tr>
                    <?php
                } ?>
            </table>
            <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
            <div class="clear"></div>
        </form>
    </div>
