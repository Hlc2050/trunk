<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<?php
//页面默认值
if (!isset($page['info'])) {
    $page['info']['id'] = '';
    $page['info']['name'] = '';
}
?>
<div class="main mhead">
    <div class="snav">
        <table  class="tb3"><tr><th align="left">修改图片名称</th></tr></table></div>
</div>
<div class="main mbody">
    <form method="post" action="<?php echo $this->createUrl('material/editPicName'); ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <table class="tb3">
            <tr>
                <td style="width: 80px">原有名称：</td>
                <td><?php echo $page['info']['name'] ?></td>
            </tr>
            <tr>
                <td style="width: 80px">修改名称：</td>
                <td>
                    <input style="width: 120px" type="text" class="ipt" id="pic_name" name="pic_name"
                           value="<?php echo $page['info']['name']; ?>"/>
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
