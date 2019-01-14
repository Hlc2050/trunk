<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<script>
    function edit(id) {
        var url = '<?php echo $this->createUrl('channelTypeManage/edit') ?>';
        window.location.href = url + '?id=' + id;
    }

    function del(id) {
        var judge = confirm('确定删除吗');
        if (judge == true) {
            var url = '<?php echo $this->createUrl('channelTypeManage/del') ?>';
            window.location.href = url + '?id=' + id;
        } else {
            return false;
        }
    }
</script>

<div class="main mhead">
    <div class="snav">市场管理 » 渠道类型设置</div>
    <div class="mt10">
        <a class="but2" href="<?php echo $this->createUrl('channelTypeManage/add') ?>">新增渠道类型</a>
        <div class="main mbody">
            <table class="tb fixTh">
                <thead>
                <tr>
                    <th width="10%">渠道类型</th>
                    <th width="10%">进粉成本</th>
                    <th width="60%">渠道标准</th>
                    <th width="20%">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data as $value) { ?>
                    <tr>
                        <td><?php echo $value['type_name']; ?></td>
                        <td><?php echo $value['fans_input']; ?></td>
                        <td><?php echo $value['type_rule']; ?></td>
                        <td><a onclick="edit(<?php echo $value['id'] ?>)">修改</a>
                            <a style="margin-left: 5px;" onclick="del(<?php echo $value['id'] ?>)">删除</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

