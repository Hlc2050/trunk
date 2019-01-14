<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<?php
$picGroupList = $this->toArr(MaterialPicGroup::model()->findAll());
?>
<div class="main mhead">
    <div class="snav">
        <table  class="tb3"><tr><th align="left">修改图片分组</th></tr></table></div>
</div>
<div class="main mbody">
    <form method="post" action="<?php echo $this->createUrl('material/changePicGroup'); ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <table class="tb3">
            <tr>
                <td style="width: 80px">图片名称：</td>
                <td><?php echo $page['info']['name']; ?></td>

            </tr>
            <tr>
                <td style="width: 80px">原有组别：</td>
                <td><?php echo MaterialPicGroup::model()->findByPk($page['info']['group_id'])->group_name; ?></td>
            </tr>
            <tr>
                <td style="width: 80px">修改组别：</td>
                <td>
                    <label>
                        <select id="picGroupId" name="picGroupId">
                            </option>
                            <?php
                            foreach ($picGroupList as $key => $val) {
                                ?>
                                <option
                                    value="<?php echo $val['id']; ?>" <?php echo $page['info']['group_id'] == $val['id'] ? 'selected' : ''; ?>>
                                    <?php echo $val['group_name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </label>

                </td>
            </tr>
            <tr>
                <td></td>
                <td class="alignleft"><input type="submit" class="but" id="subtn" value="确定"/>
                </td>
            </tr>
        </table>
    </form>
</div>
