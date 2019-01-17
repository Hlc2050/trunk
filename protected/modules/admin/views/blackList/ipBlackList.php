<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<!--ip列表-->
<div class="main mhead">
<div class="mt10 clearfix">
    <div class="l">
        <input class="but" value="删除选中" onclick="set_some('<?php echo $this->createUrl('blackList/deleteSelectIp'); ?>?id=[@]','确定删除吗？');"/>&nbsp;&nbsp;
        <a class="but2" href="<?php echo $this->createUrl('blackList/downloadIpTemplate'); ?>">模板下载</a>&nbsp;&nbsp;&nbsp;
    </div>
    <form action="<?php echo $this->createUrl('blackList/leadIpExcel') ?>" method="post" enctype="multipart/form-data">
        <input type="file" name="filename">
        <input type="submit" name="submit" class="but2" value="导入ip黑名单">
    </form>
</div>
</div>

<div class="main mbody">
<table class="tb fixTh">
    <thead>
    <tr>
        <th width="10%"><a href="javascript:void(0)" onclick="check_all('.cklist')">全选/反选</a></th>
        <th width="30%">ip地址</th>
        <th width="30%">备注</th>
        <th width="30%">操作</th>
    </tr>
    </thead>
    <tbody>
    <?php $edit = $this->check_u_menu(array('auth_tag' => 'blacklist_changeip'));
          $del = $this->check_u_menu(array('auth_tag' => 'blacklist_deleteip'));
    ?>
    <?php foreach ($page['listdata']['list'] as $black_ip) { ?>
        <tr>
            <td><input type="checkbox" class="cklist" value="<?php echo $black_ip['id'] ?>"></td>
            <td><?php echo $black_ip['ip_adress'] ?></td>
            <td><?php echo $black_ip['remark'] ?></td>
            <td>
                <?php if ($edit) { ?>
                    <a onclick="return dialog_frame(this,200,150,1)" href="<?php echo $this->createUrl('blackList/changeIp?id=' . $black_ip['id']); ?>" />修改</a>&nbsp;&nbsp;
                <?php }; ?>
                <?php if ($del) { ?>
                    <a onclick="confirm('确认删除吗！')?location.href='<?php echo $this->createUrl('blackList/deleteIp?id=' . $black_ip['id']); ?>':'';">删除</a>
                <?php }; ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
</div>

<div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>

