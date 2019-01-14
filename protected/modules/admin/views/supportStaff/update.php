<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<?php
//页面默认值
if(!isset($page['info'])){
	$page['info']['id']='';
	$page['info']['name']='';
    $page['info']['user_id']='';
    $page['info']['support_group_id']='';
}
?>
<div class="main mhead">
    <div class="snav">基础类别 » 支持人员管理 » <?php echo $page['info']['id']?'修改支持人员':'添加支持人员' ?>  </div>
</div>
<div class="main mbody">
<form method="post" action="<?php echo $this->createUrl($page['info']['id']?'supportStaff/edit':'supportStaff/add'); ?>">
<input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>" />
<table class="tb3">
    <tr>
        <th colspan="2" class="alignleft"><?php echo $page['info']['id']?'修改支持人员':'添加支持人员' ?></th>
    </tr>
<?php if($page['info']['id']):?>
    <tr>
        <td  width="150">ID：</td>
        <td><?php echo $page['info']['id'] ?></td>
    </tr>
<?php endif?>
    <tr>
        <td  width="150">支持人员：</td>
        <td>
                <?php
                $userList = $this->toArr(AdminUser::model()->findAll());
                echo CHtml::dropDownList('user_id', $page['info']['user_id'], CHtml::listData($userList, 'csno', 'csname_true'),
                    array(
                        'empty' => '--请选择关联用户--',
                        'id' => 'user_id',
                        )
                );
                ?>
        </td>
    </tr>
    <tr>
        <td  width="150">支持小组：</td>
        <td  class="alignleft">
            <select name="support_group_id">
                <option value="" selected>
                    请选择
                </option>
                <?php
                $categoryList=Linkage::model()->getSupportGroupList();
                foreach ($categoryList as $key => $val) {
                    ?>
                    <option
                        value="<?php echo $key; ?>" <?php echo $key==$page['info']['support_group_id'] ? 'selected' : ''; ?>>
                        <?php echo $val;?>
                    </option>
                <?php } ?>
            </select>
        </td>
    </tr>
    <tr>
        <td></td>
        <td  class="alignleft"><input type="submit" class="but" id="subtn" value="确定" /> <input type="button" class="but" value="返回" onclick="window.location='<?php echo $this->createUrl('supportStaff/index'); ?>'" /></td>
    </tr>
</table>
</form>
</div>
