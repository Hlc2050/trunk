<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<!--修改ip-->
<script type="text/javascript">
    $()
</script>
<div class="main mbody">
<form name="form" action="<?php echo $this->createUrl('blackList/changeIp'); ?>" method="post" onsubmit="return checkForm()">
    <table class="tb3">
        <input name="id" value="<?php echo $ip['id']; ?>" hidden>
        <tr>
            <td>ip：</td>
            <td><input ip="ipt" type="text" name="ip"  value="<?php echo $ip['ip_adress'] ?>"></td>
        </tr>
        <tr>
            <td>备注：</td>
            <td><input ip="ipt" type="text" name="remark" value="<?php echo $ip['remark']; ?>"></td>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" class="but" value="提交"></td>
        </tr>
    </table>
</form>
</div>
