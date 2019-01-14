<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<!--修改手机号-->
<div class="main mbody">
    <form name="form" action="<?php echo $this->createUrl('blackList/changePhone'); ?>" method="post" onsubmit="return checkForm()">
        <table class="tb3">
            <input name="id" value="<?php echo $phone['id']; ?>" hidden >
            <tr>
                <td width="150"><p>&nbsp;&nbsp;手机号:</p></td>
                <td><input ip="ipt" type="text" name="phone" value="<?php echo $phone['phone']; ?>"></td>
            </tr>
            <tr>
                <td width="150"><p>&nbsp;&nbsp;备注:&nbsp;</p></td>
                <td><input ip="ipt" type="text" name="remark" value="<?php echo $phone['remark'] ?>"></td>
            </tr>
            <tr>
                <td width="150"></td>
                <td>
                    <input type="submit" class="but" value="提交">
                </td>
            </tr>
        </table>
    </form>
</div>

