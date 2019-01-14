<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<!--添加商品组-->
<script type="text/javascript">
    function home() {
        artDialog.close();
    }
</script>
<div class="main mbody">
    <form name="form" method="post" action="<?php echo $this->createUrl('packageManage/addPackageGroup') ?>" onsubmit="return checkForm()">
        <table class="tb3">
            <tr>
                <td width="150">名称：</td>
                <td><input type="text" class="ipt" name="group_name"/></td>
            </tr>
            <tr>
                <td></td>
                <td class="alignleft">
                    <input type="submit" class="but" value="保存"/>
                    <input type="button" class="but2" value="返回" onclick="home()"/>
                </td>
            </tr>
        </table>
    </form>
</div>
