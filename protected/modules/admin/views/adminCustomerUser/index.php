<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">系统功能 » 客服账号管理</div>
    <div class="mt10">
        <form action="<?php echo $this->createUrl('adminCustomerUser/index'); ?>">
            <div>
                客服部：<?php helper::getServiceSelect('csid'); ?>
                <input type="submit" class="but" value="搜索">
            </div>
        </form>
    </div>
    <div>
        <input type="button" class="but2" value="新建账号"
               onclick="location='<?php echo $this->createUrl('adminCustomerUser/add') . '?url=' . $page['listdata']['url']; ?>'"/>
    </div>
</div>
<div class="main mbody">
    <table class="tb fixTh">
        <tr>
            <th>ID</th>
            <th>客服账号</th>
            <th>客服部</th>
            <th>操作</th>
        </tr>
        <?php foreach ($page['listdata']['list'] as $v) { ?>
                <tr>
                    <td>
                        <a <?php if ($v['csstatus'] == 1) echo 'style="color:#808080"' ?>>
                            <?php echo $v['csno']; ?>
                        </a>
                    </td>
                    <td><a <?php if ($v['csstatus'] == 1) echo 'style="color:#808080"' ?>>
                            <?php echo $v['csname']; ?>
                        </a>
                    </td>
                    <td>
                        <a <?php if ($v['csstatus'] == 1) echo 'style="color:#808080"' ?>>
                            <?php echo $v['cname']; ?>
                        </a>
                    </td>
                    <td>
                        <a <?php if ($v['csstatus'] == 1) echo 'style="color:#808080"' ?> href="<?php if ($v['csstatus'] == 1) {echo 'javascript:return false';}else{echo $this->createUrl('adminCustomerUser/update') . '?url=' . $page['listdata']['url'] . '&csname=' . $v['csname'];} ?>">修改密码</a>
                        &nbsp;
                        <a href="<?php echo $this->createUrl('adminCustomerUser/switch') . '?state=' . $v['csstatus'] . '&csname=' . $v['csname']; ?>"><?php echo $v['csstatus'] == 0 ? "停用" : "启用"; ?></a>
                    </td>
                </tr>
        <?php }; ?>
    </table>
    <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
</div>



