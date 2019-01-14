<!-- 评论表 -->
<table id="exportTable" class="tb fixTh" style="width: 100%">
    <thead>
    <tr>
        <th>ID</th>
        <th>评论名称</th>
        <th>评论类型</th>
        <th>支持人员</th>
        <th><?php $this->check_u_menu(array('code'=>'操作','auth_tag'=>'material_editReview')); ?></th>
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
            <td><?php echo $r['review_title']; ?></td>
            <td><?php 
                if($r['review_type']==1){
                    echo '论坛';
                }else if($r['review_type']==2){
                    echo '精选留言';
                }else{
                    echo Linkage::model()->get_name($r['review_type']);
                }
                ?></td>
            <td><?php echo SupportStaff::model()->findByAttributes( array('user_id' => $r['support_staff_id']) )->name; ?></td>
            <td>
                <?php
                if($r['review_type']==1)
                    $this->check_u_menu(array('code'=>'<a href="'.$this->createUrl('material/editForumReview').'?id='.$r['id'].'">编辑</a>','auth_tag'=>'material_editReview'));
                else if($r['review_type']==2)
                    $this->check_u_menu(array('code'=>'<a href="'.$this->createUrl('material/editSelectReview').'?id='.$r['id'].'">编辑</a>','auth_tag'=>'material_editReview'));
                else
                    $this->check_u_menu(array('code'=>'<a href="'.$this->createUrl('material/editReview').'?id='.$r['id'].'">编辑</a>','auth_tag'=>'material_editReview')); ?>
                <?php $this->check_u_menu(array('code'=>'<a href="'.$this->createUrl('material/deleteReview').'?id='.$r['id'].'" onclick="return confirm(\'确定删除吗\')">删除</a>','auth_tag'=>'material_deleteReview')); ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>