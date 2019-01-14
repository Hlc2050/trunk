<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<table class="tb fixTh" >
    <thead>
    <tr>
        <th>ID</th>
        <th>视频名称</th>
        <th>视频大小</th>
        <th>支持人员</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($page['listdata']['list'] as $r) { ?>
        <tr>
            <td><?php echo $r['id']; ?></td>
            <td><?php echo $r['video_name']; ?></td>
            <td><?php echo $this->sizecount($r['video_size']) ?></td>
            <td><?php echo AdminUser::model()->findByPk($r['support_staff_id'])->csname_true; ?></td>
            <td>
                <?php $this->check_u_menu(array('code' => '<a onclick="return dialog_frame(this,500,500,1)" href="' . $this->createUrl('material/videoEdit') . '?id=' . $r['id'] . '">编辑</a>', 'auth_tag' => 'material_videoEdit')); ?>
                <?php $this->check_u_menu(array('code' => '<a href="' . $this->createUrl('material/videoDelete') . '?id=' . $r['id'] . '" onclick="return confirm(\'确定删除吗\')">删除</a>', 'auth_tag' => 'material_videoDelete')); ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
