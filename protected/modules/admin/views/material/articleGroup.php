<!-- 图文展示 -->

<div class="main mbody">
    <form>
        <table class="tb fixTh">
            <thead>
<!--            <tr>-->
<!--                <th align="left" style="background: white;">-->
<!--                    --><?php //echo helper::field_paixu(array('url' => '' . $this->createUrl('material/index') . '?group_id=0&p=' . $_GET['p'] . '', 'field_cn' => '按时间排序', 'field' => 'create_time')); ?>
<!--                </th>-->
<!--            </tr>-->
            <tr>
                <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('material/index') . '?group_id=0&p=' . $_GET['p'] . '', 'field_cn' => 'ID', 'field' => 'id')); ?></th>
                <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('material/index') . '?group_id=0&p=' . $_GET['p'] . '', 'field_cn' => '组别名称', 'field' => 'group_name')); ?></th>
                <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('material/index') . '?group_id=0&p=' . $_GET['p'] . '', 'field_cn' => '组别编码', 'field' => 'group_code')); ?></th>
                <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('material/index') . '?group_id=0&p=' . $_GET['p'] . '', 'field_cn' => '类型', 'field' => 'cat_id')); ?></th>
                <th align='center'>文章数量</th>
                <th align='center'>操作</th>
            </tr>
            </thead>
            <tbody>

            <?php foreach ( $page['listdata']['list'] as $r){?>
                <tr>
                    <td><?php echo $r['id'];?></td>
                    <td><?php echo $r['group_name'];?></td>
                    <td><?php echo $r['group_code'];?></td>
                    <td><?php echo $r['cat_name'];?></td>
                    <td><?php echo $r['article_count'];?></td>
                    <td>
                        <?php $this->check_u_menu(array('code' => '<a href="' . $this->createUrl('material/index?type=1&gid=' . $r['id'].'&url='.$page['listdata']['url']) . '">进入</a>', 'auth_tag' => 'material_index')); ?>&nbsp;
                        <?php $this->check_u_menu(array('code' => '<a href="' . $this->createUrl('material/deleteArticleGroup?id=' . $r['id'] . '&url=' . $page['listdata']['url']) . '"  onclick="return confirm("是否删除该组别，请谨慎删除")">删除</a>', 'auth_tag' => 'material_deleteArticleGroup')); ?>
                    </td>
                </tr>
            <?php }?>
            </tbody>
        </table>
    </form>
</div>
<br/>
<div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>