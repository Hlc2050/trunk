<?php require(dirname(__FILE__)."/../common/head.php");?>
<div class="main mhead">
    <div class="snav">财务管理 » 合作商费用日志管理</div>
</div>
<div class="main mbody">
<form method="post" action="<?php echo $this->createUrl('partnerCost/toEdit'); ?>">
<input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>" />
<input type="hidden" id="backurl" name="backurl" value="<?php echo $this->get('url'); ?>" />
<table class="tb3">
    <tr>
        <td width="100">合作商提供费用</td>
        <td class="alignleft" >
            <input class="ipt" name="partner_cost" id="partner_cost" value="<?php echo $page['info']['partner_cost'] ?>">
        </td>
    </tr>


</table>
    <input type="submit" class="but" id="subtn" value="确定" /> <input type="button" class="but" value="返回" onclick="window.location='<?php echo $this->get('url'); ?>'" />
</form>
</div>
