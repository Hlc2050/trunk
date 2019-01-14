<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">直接下单管理 » 下单商品列表</div>

    <div class="mt10">
    </div>
    <div class="mt10 clearfix">
        <div class="l">
            <a class="but2"
               href="<?php echo $this->createUrl('packageManage/addPackage?id=' . $id . '&url=' . $page['listdata']['url']) ?>">添加下单商品</a>
            <input type="button" class="but2" value="返回" onclick="window.location='<?php echo $this->get('url'); ?>'"/>
        </div>
        <div class="r">
        </div>
    </div>
</div>
<div class="main mbody">
    <table class="tb">
        <tr>
            <th align='center'>下单商品</th>
            <th class="center">操作</th>
        </tr>
        <?php foreach ($page['listdata']['list'] as $k => $v) { ?>
            <tr>
                <td><?php echo $v['name']; ?></td>
                <td>
                    <?php $this->check_u_menu(array('code' => '<a href="' . $this->createUrl('packageManage/updatePackage?id=' . $v['id']) . '" onclick="return dialog_frame(this,400,300,1)" >修改</a>', 'auth_tag' => 'packageManage_updatePackage')); ?>
                    &nbsp;&nbsp;&nbsp;
                    <?php $this->check_u_menu(array('code' => '<a href="' . $this->createUrl('packageManage/delPackage?id=' . $v['id']) . '"  onclick="return confirm(\'确认删除下单商品【' . $v['name'] . '】吗\')">删除</a>', 'auth_tag' => 'packageManage_delPackage')); ?>
                </td>
            </tr>
        <?php }; ?>
    </table>
        <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
</div>


