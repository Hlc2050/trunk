<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav"><span style="font-size: x-large;font-weight: bold; color: dimgrey">文章分块</span></div>
    <div class="mt10 clearfix">
        <div class="l" id="container">
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="添加文章分块" onclick="location=\'' . $this->createUrl('articleBlock/add') . '\'" />', 'auth_tag' => 'articleBlock_add')); ?>
            &nbsp;
        </div>
        <div class="r">
        </div>
    </div>
</div>

<div class="main mbody">
    <form>
        <table class="tb fixTh">
            <thead>
            <tr>
                <th  align='center'>ID</th>
                <th  align='center'>文章名称</th>
                <th  align='center'>分块详情</th>
                <th  align='center'>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($page['listdata']['list'] as $k => $v) {

            ?>
            <tr>
                <td><?php echo $v['id']; ?></td>
                <td><?php echo $v['block_name']; ?></td>
                <td>
                    <?php
                    $str = "<b>[空白显示内容]</b>=>".$v['blank_content'].
                        "<br /><b>[分块一]</b>=>".$v['block_one'].
                        "<br /><b>[分块二]</b>=>".$v['block_two'].
                        "<br /><b>[分块三]</b>=>".$v['block_three'].
                        "<br /><b>[分块四]</b>=>".$v['block_four'].
                        "<br /><b>[分块五]</b>=>".$v['block_five']; ?>
                    <a href="#"
                       onclick="dialog({width:'500px',title:'分块详情',content:$(this).attr('data-clipboard-text')}).showModal();"
                       data-clipboard-text="<?php echo htmlentities($str, ENT_QUOTES); ?>">点击查看</a></td>
                <td>
                    <?php $this->check_u_menu(array('code' => '<a href="' . $this->createUrl('articleBlock/edit?id='.$v['id'].'&url='.$page['listdata']['url']) . '">编辑</a>', 'auth_tag' => 'articleBlock_edit')); ?>
                    &nbsp;
                    <?php $this->check_u_menu(array('code' => '<a href="' .$this->createUrl('articleBlock/delete?id='.$v['id'].'&url='.$page['listdata']['url']) . '"  onclick="return confirm(\'确认删除吗\')">删除</a>', 'auth_tag' => 'articleBlock_delete')); ?>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
        <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
    </form>
</div>


