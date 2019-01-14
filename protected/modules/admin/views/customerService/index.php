<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
    <div class="main mhead">
        <div class="snav">基础类别 » 客服部管理</div>
        <div class="mt10">
            <form action="<?php echo $this->createUrl('customerService/index'); ?>" id="searchForm">
                <select name="state_cooperation">
                    <option value="">合作状态</option>
                    <option value="0" <?php echo $this->get('state_cooperation')==0 &&$this->get('state_cooperation') != null?'selected':'';?>>启用</option>
                    <option value="1" <?php echo $this->get('state_cooperation')==1?'selected':'';?>>停止</option>
                </select>&nbsp;
                <input type="submit" class="but" value="查询">
                <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="新增" onclick="location=\'' . $this->createUrl('customerService/add') . '\'" />', 'auth_tag' => 'customerService_add')); ?>
            </form>
        </div>
    </div>
    <div class="main mbody">
        <form>
            <table class="tb fixTh">
                <thead>
                <tr>
                    <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('customerService/index') . '?p=' . $_GET['p'] . '', 'field_cn' => 'ID', 'field' => 'id')); ?></th>
                    <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('customerService/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '客服部', 'field' => 'cname')); ?></th>
                    <th>商品</th>
                    <th align='center'>合作状态</th>
                    <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('customerService/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '预计发货率（%）', 'field' => 'estimate_rate')); ?></th>
                    <th align='center'>操作</th>
                </tr>
                </thead>
                <?php
                foreach ($page['listdata']['list'] as $r) {
                    ?>
                    <tr>
                        <td><?php echo $r['id']; ?></td>
                        <td><?php echo $r['cname'];
                            if ($r['status'] == 1): ?>
                                <a style="color: red">(独立)</a>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $r['goodsList']; ?></td>
                        <td><?php echo $r['state_cooperation']==0?"启用":"停止"; ?></td>
                        <td><?php echo $r['estimate_rate']; ?></td>
                        <td align='center'>
                            <?php $this->check_u_menu(array('code' => '<a href="' . $this->createUrl('customerService/edit', array('id' => $r['id'])) . '">修改</a>', 'auth_tag' => 'customerService_edit')); ?>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <?php $this->check_u_menu(array('code' => '<a href="' . $this->createUrl('customerService/delete', array('id' => $r['id'])) . '"  onclick="return confirm(\'确定删除吗\')">删除</a>', 'auth_tag' => 'customerService_delete')); ?>
                        </td>
                    </tr>
                    <?php
                } ?>
            </table>
            <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
            <div class="clear"></div>
        </form>
    </div>


