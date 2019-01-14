<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">直接下单管理 » 下单商品管理</div>

    <div class="mt10">
    </div>
    <div class="mt10 clearfix">
        <div class="l">
            <?php $this->check_u_menu(array('code' => '<a class="but2" href="' . $this->createUrl('packageManage/addPackage?id='.$id) . '" />'."添加下单商品".'</a>', 'auth_tag' => 'packageManage_addPackage')); ?>
            <input type="button" class="but2" value="返回" onclick="window.location='<?php echo $this->createUrl('packageManage/index'); ?>'"/>
        </div>
        <div class="r">
        </div>
    </div>
</div>
<div class="main mbody">
    <table class="tb fixTh">
        <thead>
        <tr>
            <th align='center'>下单商品</th>
            <th class="center">操作</th>
        </tr>
        </thead>
        <tbody>
        <?php
        //判断权限
        $eidt = $this->check_u_menu(array('auth_tag'=>'packageManage_updatePackage'));
        $del = $this->check_u_menu(array('auth_tag'=>'packageManage_delPackage'));
        ?>
        <?php foreach ($page['listdata']['list'] as $k => $v) { ?>
            <tr>
                <td><?php echo $v['name']; ?></td>
                <td>
                    <?php if ($eidt) { ?>
                        <a href="<?php echo $this->createUrl('packageManage/updatePackage?id=' . $v['id']); ?>"  onclick="return dialog_frame(this,400,300,1)">修改</a>&nbsp;&nbsp;&nbsp;
                    <?php }; ?>
                    <?php if ($del) { ?>
                        <a href="<?php echo $this->createUrl('packageManage/delPackage?id=' . $v['id']); ?>"  onclick="return confirm(\'确认删除下单商品【' . $v['name'] . '】吗\')">删除</a>&nbsp;&nbsp;&nbsp;
                    <?php }; ?>
                </td>
            </tr>
        <?php }; ?>
        </tbody>
    </table>
        <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
</div>


