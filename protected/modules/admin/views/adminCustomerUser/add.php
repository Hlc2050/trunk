<?php require(dirname(__FILE__) . "/../common/head.php"); ?>

<div class="main mhead">
    <div class="snav">系统功能 » 客服账号管理 » 新建</div>
</div>
<div class="main mbody">
    <form name="form" action="<?php $this->get('adminCustomerUser/add') ?>" method="post" onsubmit="return checkForm()">
        <table class="tb3">
            <tr>
                <td>客服账号:<input class="ipt" type="search"  name="service_name" autocomplete="new-password">
                </td>
            </tr>
            <tr>
                <td>客服部：&nbsp;<?php helper::getServiceSelect('csid'); ?></td>
            </tr>
            <tr>
                <td> 密码：&nbsp;&nbsp;&nbsp;&nbsp;<input class="ipt" type="password" name="serviece_pwd" autocomplete="new-password"></td>
            </tr>
            <tr>
                <td><input type="submit" value="提交" class="but">
                <input type="button" class="but" value="返回"
                           onclick="window.location='<?php echo $this->createUrl('adminCustomerUser/index'); ?>'"/></td>
            </tr>
        </table>
    </form>
</div>


