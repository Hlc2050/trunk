<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<?php
//页面默认值
if (!isset($page['info'])) {
    $page['info']['id'] = '';
    $page['info']['group_name'] = '';
}
?>
<div class="main mhead">
    <div class="snav"><?php echo $page['info']['id'] ? '修改组别' : '添加组别' ?>  </div>
</div>
<div class="main mbody">
    <form method="post"
          action="<?php echo $this->createUrl($page['info']['id'] ? 'material/editPicGroup' : 'material/addPicGroup'); ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <table class="tb3">
            <?php if ($page['info']['id']): ?>
                <tr>
                    <td width="150">组别ID：</td>
                    <td><?php echo $page['info']['id'] ?></td>
                </tr>
            <?php endif ?>
            <tr>
                <td width="150">名称：</td>
                <td>
                    <input type="text" class="ipt" id="group_name" name="group_name"
                           value="<?php echo $page['info']['group_name']; ?>"/>
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="alignleft"><input type="submit" class="but" id="subtn" value="确定"/>
                </td>
            </tr>
        </table>
    </form>
</div>
