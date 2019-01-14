<?php require(dirname(__FILE__) . "/../common/menu.php"); ?>
<style>
    .am-table {
        margin-bottom: 0;
    }

    .am-table > tbody > tr > td {
        border: 0;
    }

</style>

<form action="<?php echo $this->createUrl('weChatQuery/search') ?>" method="post">
<div class="admin-content">
    <a id="top" name="top"></a>
    <div style="margin-top: 50px;"></div>
        <table class="am-table">
            <thead>
            <tr>
                <th colspan="2">微信号-推广人员查询</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="2"><textarea style="width: 100%;margin-left: 1px;height: 300px;" name="content"><?php echo $this->get('contnet') ?></textarea></td>
            </tr>
            <tr>
                <td colspan="2">多个微信号请使用换行</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center"><button type="submit" class="am-btn am-btn-default">查询</button></td>
            </tr>
            <?php if($page){ ?>
            <tr>
                <th>微信号</th>
                <th>推广人员</th>
            </tr>
            <?php foreach ($page as $value){ ?>
                <tr>
                    <th><?php echo $value['id'] ?></th>
                    <th><?php echo $value['name'] ?></th>
                </tr>

            <?php } ?>
            <?php } ?>

            </tbody>
        </table>
</div>
</form>



