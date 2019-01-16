<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<!--手机号列表-->
<div class="main mhead">
<div class="mt10 clearfix">
    <div class="l">
        <input class="but2" value="删除选中" onclick="set_some('<?php echo $this->createUrl('blackList/deleteSelectPhone'); ?>?id=[@]','确定删除吗？');"/>&nbsp;&nbsp;
        <a class="but2" href="<?php echo $this->createUrl('blackList/downloadPhoneTemplate'); ?>">模板下载</a>&nbsp;&nbsp;&nbsp;
    </div>
    <form action="<?php echo $this->createUrl('blackList/leadPhoneExcel') ?>" method="post"
          enctype="multipart/form-data">
        <input type="file" name="filename">
        <input type="submit" class="but2" value="导入手机黑名单">
    </form>
</div>
</div>

<div class="main mbody">
<table class="tb fixTh">
    <thead>
    <tr>
        <th width="10%"><a href="javascript:void(0)" onclick="check_all('.cklist')">全选/反选</a></th>
        <th width="30%">手机号</th>
        <th width="30%">备注</th>
        <th width="30%">操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($page['listdata']['list'] as $black_phone) { ?>
        <tr>
            <td><input type="checkbox" class="cklist" value="<?php echo $black_phone['id'] ?>"></td>
            <td><?php echo $black_phone['phone'] ?></td>
            <td><?php echo $black_phone['remark'] ?></td>
            <td><?php $this->check_u_menu(array('code' => '<a onclick="return dialog_frame(this,230,150,1)" href="' . $this->createUrl('blackList/changePhone?id=' . $black_phone['id']) . '" />' . '修改</a>', 'auth_tag' => 'blacklist_changephone')); ?>
                <?php $this->check_u_menu(array('code' => '<a onclick="confirm(\'确认删除吗！\')?location.href=\'' . $this->createUrl('blackList/deletePhone?id=' . $black_phone['id']) . '\':\'\'"/>' . '删除</a>', 'auth_tag' => 'blacklist_deletephone')); ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
</div>

<div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
