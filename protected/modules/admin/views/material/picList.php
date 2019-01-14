<!-- 图片展示 -->
<div id="layout">
    <table style="width: 100%;">
        <tr>
            <th align="left">
                <?php echo helper::field_paixu(array('url' => '' . $this->createUrl('material/index') . '?group_id=1&p=' . $_GET['p'] . '', 'field_cn' => '按时间排序', 'field' => 'create_time')); ?>
            </th>
        </tr>
        <tr class="level1-tr">
            <?php foreach ($page['listdata']['list'] as $key => $val) {
                $imgUrl = Resource::model()->findByPk($val['img_id'])->resource_url;
                ?>
                <td class="level1-td" style="width: 20%">
                    <table>
                        <tr>
                            <td colspan="3" style="text-align:center;vertical-align:middle;">
                                <label for="<?php echo $val['id']; ?>"> <img class="bimg"
                                                                             src="<?php echo $imgUrl; ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3"
                                style=" height: 25px;text-align:center;vertical-align:middle;">
                                    <input type="checkbox" class="cklist" id="<?php echo $val['id']; ?>"
                                           value="<?php echo $val['id']; ?>"/>
                                    <?php echo $val['name']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php
                                $this->check_u_menu(array('code' => '<input value="编辑" type="button" class="but1"  onclick="return dialog_frame(this,250,250,false)"  href="' . $this->createUrl('material/editPicName?id=' . $val['id']) . '"/>', 'auth_tag' => 'material_editPicName'));
                                ?>
                            </td>
                            <td>
                                <?php
                                $this->check_u_menu(array('code' => '<input value="分组" type="button" class="but1"  onclick="return dialog_frame(this,400,250,false)"  href="' . $this->createUrl('material/changePicGroup?id=' . $val['id']) . '"/>', 'auth_tag' => 'material_changePicGroup'));
                                ?>
                            </td>
                            <td>
                                <?php $this->check_u_menu(array('code' => '<input value="删除" type="button" class="but1" onclick="confirm(\'确认删除图片【' . $val['name'] . '】吗！\')?location.href=\'' . $this->createUrl('material/deletePic?id=' . $val['id'] . '&url=' . $page['listdata']['url']) . '\':\'\'"/>', 'auth_tag' => 'material_deletePic')); ?>
                            </td>
                        </tr>
                    </table>
                </td>
            <?php } ?>
        </tr>
    </table>
</div>
<div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>