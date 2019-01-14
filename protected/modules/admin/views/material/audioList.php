<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<table class="tb fixTh" >
    <thead>
    <tr>
        <th>ID</th>
        <th>语音名称</th>
        <th>支持人员</th>
        <th>播放</th>
        <th>语音时长</th>
        <th>语音大小</th>
        <th>下载</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($page['listdata']['list'] as $r) { ?>
        <tr>
            <td><?php echo $r['id']; ?></td>
            <td><?php echo $r['audio_name']; ?></td>
            <td><?php echo AdminUser::model()->findByPk($r['support_staff_id'])->csname_true; ?></td>
            <td>
                <audio  src="<?php echo $r['resource_url'];?>" controls="controls" loop="loop" preload="auto" >
                    Your browser does not support the audio element.
                </audio>
            </td>
            <td><?php echo $r['play_time']; ?></td>
            <td><?php echo $this->sizecount($r['audio_size']) ?></td>
            <td>
                <a href="<?php echo $r['resource_url']; ?>" download="<?php echo $r['o_name']; ?>">
                    <img src="/static/img/Download.png" style="width: 25px;height: 25px;"/>
                </a>
            </td>
            <td>
                <?php $this->check_u_menu(array('code' => '<a onclick="return dialog_frame(this,500,500,1)" href="' . $this->createUrl('material/audioEdit') . '?id=' . $r['id'] . '">编辑</a>', 'auth_tag' => 'material_audioEdit')); ?>
                <?php $this->check_u_menu(array('code' => '<a href="' . $this->createUrl('material/audioDelete') . '?id=' . $r['id'] . '" onclick="return confirm(\'确定删除吗\')">删除</a>', 'auth_tag' => 'material_audioDelete')); ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>



