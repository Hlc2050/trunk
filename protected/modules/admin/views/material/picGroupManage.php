<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">
        <table class="tb3"><tr><th style="font-size: medium;" align="left">图片组别管理</th></tr></table>
    </div>
    <div class="mt10"  id="container">
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="添加组别" onclick="return dialog_frame(this,300,300,1)" href="' . $this->createUrl('material/addPicGroup') . '" />', 'auth_tag' => 'material_addPicGroup')); ?>&nbsp;
    </div>
</div>
<div class="main mbody">
    <div>
        <table class="tb3">
            <tr  align="left">
                <th >组别ID</th>
                <th >组别名称</th>
                <th >操作</th>
            </tr>
            <?php foreach ($page['listdata']['list'] as $key => $val){ ?>
            <tr>
                <td> <?php echo $val['id'];?></td>
                <td> <?php echo $val['group_name'];?></td>
                <td>
                    <?php $this->check_u_menu(array('code' => '<a onclick="return dialog_frame(this,300,200,1)" href="' . $this->createUrl('material/editPicGroup', array('id' => $val['id'])) . '">修改</a>', 'auth_tag' => 'material_editPicGroup')); ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <?php $this->check_u_menu(array('code' => '<a href="' . $this->createUrl('material/deletePicGroup', array('id' => $val['id'])) . '"  onclick="return confirm(\'确认删除组别【'.$val['group_name'].'】吗\')">删除</a>', 'auth_tag' => 'material_deletePicGroup')); ?>
                </td>
            </tr>
            <?php }?>
        </table>
    </div>

</div>

