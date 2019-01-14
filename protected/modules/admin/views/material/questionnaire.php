<!-- 问卷调查表 -->
<table id="exportTable" class="tb fixTh" style="width: 100%">
    <thead>
    <tr>
        <th>ID</th>
        <th>问卷名称</th>
        <th>投票页名称</th>
        <th>商品类别</th>
        <th>支持人员</th>
        <th>操作</th>
    </tr>
    <tr id="totalInfo">

    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($page['listdata']['list'] as $r) {
        ?>
        <tr>
            <td><?php echo $r['id']; ?></td>
            <td><?php echo $r['vote_title']; ?></td>
            <td><?php echo $r['vote_page']; ?></td>
            <td><?php echo Linkage::model()->get_name($r['cat_id']); ?></td>
            <td><?php echo SupportStaff::model()->findByAttributes( array('user_id' => $r['support_staff_id']) )->name; ?></td>
            <td>
                <?php $this->check_u_menu(array('code'=>'<a href="'.$this->createUrl('material/editQuestion').'?id='.$r['id'].'">编辑</a>','auth_tag'=>'material_editQuestion')); ?>
                <?php $this->check_u_menu(array('code'=>'<a href="'.$this->createUrl('material/deleteQuestion').'?id='.$r['id'].'" onclick="return confirm(\'确定删除吗\')">删除</a>','auth_tag'=>'material_deleteQuestion')); ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
