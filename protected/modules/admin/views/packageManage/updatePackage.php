<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<!--修改下单商品-->
<script type="text/javascript">
    function home() {
        artDialog.close();
    }
</script>
<div class="main mbody">
    <form name="form" method="post" action="<?php echo $this->createUrl('packageManage/updatePackage') ?>" onsubmit="return checkForm()">
        <table class="tb3">
            <tr>
                <td width="150">名称：</td>
                <td>
                    <input hidden name="id" value="<?php echo $id; ?>">
                    <input type="text" class="ipt" name="new_name" value="<?php echo $name?$name:''; ?>"/>
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
