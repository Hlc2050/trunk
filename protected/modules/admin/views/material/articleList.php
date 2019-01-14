<!-- 图文展示 -->
<div id="layout">

    <table style="width: 100%">
        <tr>
            <th align="left">
                <?php echo helper::field_paixu(array('url' => '' . $this->createUrl('material/index') . '?group_id=0&type=1&gid='. $page['listdata']['gid'].'&p=' . $_GET['p'] . '', 'field_cn' => '按时间排序', 'field' => 'create_time')); ?>
            </th>
        </tr>
        <tr class="level1-tr">

            <?php foreach ($page['listdata']['list'] as $key => $val) {
                ?>
                <td class="level1-td" style="width: 20%">
                    <table>
                        <tr>
                            <td colspan="4" align="left" style="font-size: medium;color: dimgrey;font-weight: bold;line-height: 29px;">
                                <span><?php echo $val['article_code'] . " (" . date("Y-m-d", $val['update_time']) . ")"; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align:center;vertical-align:middle;">
                                <label for="<?php echo $val['id']; ?>">
                                    <img class="aimg"
                                         src="<?php echo $val['cover_url'] ? $val['cover_url'] : '/uploadfile/materialImgs/empty.png'; ?>"/>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4"
                                style=" height: 60px;text-align:center;vertical-align:middle; color: dimgrey">
                                <input type="checkbox" class="cklist" id="<?php echo $val['id']; ?>"
                                       value="<?php echo $val['id']; ?>"/>
                                <span title="<?php echo $val['article_title']; ?>" style="font-weight: bold;line-height: 24px;">  <?php echo helper::cut_str($val['article_title'], 10);?></span>
                                <br/> <?php echo AdminUser::model()->findByPk($val['support_staff_id'])->csname_true;
                                if ($val['article_type'] == 0) {
                                    echo "（标准图文）";
                                } elseif ($val['article_type'] == 1) {
                                    echo "（语音问卷）";
                                } elseif ($val['article_type'] == 2) {
                                    echo "（论坛问答）";
                                }elseif($val['article_type'] == 3){
                                    echo "（微信图文）";
                                }
                                ?>
                                <br/>
                                文案备注：<a title="<?php echo $val['article_info']; ?>" href="#" style="color: dimgrey;line-height: 26px;"><?php echo helper::cut_str($val['article_info'], 11);?></a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php $this->check_u_menu(array('code' => '<input value="编辑" type="button" class="but1" onclick="location.href =\'' . $this->createUrl('material/editArticle?id=' . $val['id'] . '&url=' . $page['listdata']['url']) . '\'"/>', 'auth_tag' => 'material_editArticle')); ?>
                            </td>
                            <td>

                                <input value="预览" type="button" class="but1" onclick="window.open('<?php echo $this->createUrl('../site/showPreview?id=' . $val['id']);?>','_blank')">
                            </td>
                            <td>
                                <?php $this->check_u_menu(array('code' => '<input value="删除" type="button" class="but1" onclick="confirm(\'确认删除图文【' . $val['article_title'] . '】吗！\')?location.href=\'' . $this->createUrl('material/deleteArticle?id=' . $val['id'] . '&url=' . $page['listdata']['url']) . '\':\'\'"/>', 'auth_tag' => 'material_deleteArticle')); ?>
                            </td>
                            <td>
                                <?php
                                if($page['listdata']['gid']!=0) {
                                    $this->check_u_menu(array('code' => '<input value="信息" type="button" class="but1"  onclick="return dialog_frame(this,350,400,false)"  href="' . $this->createUrl('material/addMessage?article_id=' . $val['id']) . '"/>', 'auth_tag' => 'material_addMessage'));
                                }?>
                            </td>
                        </tr>
                    </table>
                </td>
            <?php } ?>
        </tr>
    </table>
</div>
<br/>
<div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>