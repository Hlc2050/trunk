<?php require(dirname(__FILE__) . "/../common/head.php"); ?>

    <div class="main mhead">
    <div class="snav">域名管理 » 总统计管理</div>

    <div class="mt10">
        <div class="mt10 clearfix">
            <div class="l">
                <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="添加总统计" onclick="return dialog_frame(this,350,250,1);" href="'.$this->createUrl('cnzzCodeManage/add').'" />', 'auth_tag' => 'cnzzCodeManage_add')); ?>
            </div>
            <div class="r"></div>
        </div>
    </div>
    <div class="main mbody">
        <table class="tb fixTh" style="width:700px;">
            <thead>
            <tr>
                <th width="100">ID</th>
                <th>总统计名称</th>
                <th>已使用域名数量</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php $edit = $this->check_u_menu(array('auth_tag' => 'cnzzCodeManage_edit'));
                  $del = $this->check_u_menu(array('auth_tag' => 'cnzzCodeManage_delete'));
            ?>
            <?php foreach ($page['listdata']['list'] as $r) { ?>
                <tr>
                    <td><?php echo $r['id']?></td>
                    <td><?php echo $r['name']?></td>
                    <td><?php echo $r['domain_num']."/".$r['limit_num']?></td>
                    <td>
                        <?php if ($edit) { ?>
                            <input value="编辑" type="button" class="but1" onclick="return dialog_frame(this,350,250,1)"  href="<?php echo $this->createUrl('cnzzCodeManage/edit?id=' . $r['id']); ?>"/>
                        <?php }; ?>
                        <?php if ($del) { ?>
                            <input type="button" class="but1" value="删除" onclick="confirm('确认删除该总统计吗！')?location.href='<?php echo $this->createUrl('cnzzCodeManage/delete', array('id' => $r['id'])); ?>':''" />
                        <?php } ?>
                    </td>
                </tr>
            <?php }; ?>
            </tbody>
        </table>
    </div>
