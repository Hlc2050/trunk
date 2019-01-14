<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<!--修改商品组-->
<script type="text/javascript">
    function home() {
        artDialog.close();
    }
</script>
<div class="main mbody">
    <form method="post" action="<?php echo $this->createUrl('packageManage/updatePackGroup') ?>">
        <table class="tb3">
            <tr>
                <td width="150">名称：</td>
                <td>
                    <input type="text" class="ipt" name="group_name" value="<?php echo $group_name; ?>"/>
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="alignleft">
                    <input type="submit" class="but" value="确定"/>
                    <input type="button" class="but2" value="返回" onclick="home()"/>
                </td>
            </tr>
        </table>
    </form>
</div>