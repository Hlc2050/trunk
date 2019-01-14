<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<?php
//页面默认值
if(!isset($page['info'])){
	$page['info']['id']='';
	$page['info']['name']='';
	$page['info']['openid']='';
}
?>
<div class="main mhead">
    <div class="snav">域名管理 » 微信通知管理 » <?php echo $page['info']['id']?'修改人员':'添加人员' ?>  </div>
</div>
<div class="main mbody">
<form method="post" action="<?php echo $this->createUrl($page['info']['id']?'domainNoticeManage/edit':'domainNoticeManage/add'); ?>?domainNoticeManage_id=<?php echo $page['info']['id'];?>">
<input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>" />
<table class="tb3">
<?php if($page['info']['id']):?>
    <tr>
        <td  width="100">ID：</td>
        <td><?php echo $page['info']['id'] ?></td>
    </tr>
<?php endif?>
    <tr>
    <td  width="100">姓名：</td>
        <td  class="alignleft">
        <input style="width: 320px" type="text" class="ipt"  id="name"   name="name" value="<?php echo $page['info']['name']; ?>"/>
        </td>
    </tr>
    <tr>
        <td  width="100">openid：</td>
        <td  class="alignleft">
            <input style="width: 320px" type="text" class="ipt"  id="openid"   name="openid" value="<?php echo $page['info']['openid']; ?>"/>
        </td>
    </tr>
    <tr>
        <td  width="100">系统用户：</td>
        <td  class="alignleft">
            <?php
            $all_user = AdminUser::model()->get_all_user(1);
            echo CHtml::dropDownList('system_user', $page['info']['system_user'], CHtml::listData($all_user, 'csno', 'csname_true'),
                array(
                    'empty' => '请选择',
                )
            );
            ?>

        </td>
    </tr>
    <tr>
        <td></td>
        <td  class="alignleft"><input type="submit" class="but" id="subtn" value="确定" /> <input type="button" class="but" value="返回" onclick="window.location='<?php echo $this->createUrl('domainNoticeManage/index'); ?>'" /></td>
    </tr>
    <tr>
        <th colspan="2" align="left">&nbsp;&nbsp;获取openid方法</th>
    </tr>
    <tr>

        <td colspan="2" align="left">
            &nbsp;&nbsp;1.关注微信公众号:厦门搜罗公司<br/>
            &nbsp;&nbsp;2.点击公众号下方"获取openid"菜单<br/>
            &nbsp;&nbsp;3.页面显示的字符串就是对应的openid
        </td>
    </tr>

</table>
</form>
</div>
