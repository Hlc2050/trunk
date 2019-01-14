<?php require(dirname(__FILE__) . "/../common/head.php"); ?>

<div class="main mhead">
    <div class="snav">系统功能 » 客服账号管理 » 修改密码</div>
    <div class="mt10">
        <form name="form" action="<?php $this->get('adminCustomerUser/update') ?>" method="post" onsubmit="return checkForm()">
            <input hidden value="<?php echo $csname; ?>" name="name">
            <table class="tb3">
                <tr>
                    <td>请输入旧密码：<input type="password" name="old_pwd" class="ipt"></td>
                </tr>
                <tr>
                    <td>请输入新密码：<input type="password" name="new_pwd" class="ipt"></td>
                </tr>
                <tr>
                    <td><input type="submit" value="提交" class="but">
                        <input type="button" class="but" value="返回"
                               onclick="window.location='<?php echo $this->createUrl('adminCustomerUser/index'); ?>'"/></td>
                </tr>
            </div>
            </table>
        </form>
    </div>
</div>


