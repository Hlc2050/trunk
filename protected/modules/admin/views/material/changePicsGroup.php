<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<?php
$picGroupList = $this->toArr(MaterialPicGroup::model()->findAll());
?>
<div class="main mhead">
    <div class="snav">
        <table  class="tb3"><tr><th align="left">修改图片分组</th></tr></table></div>
</div>
<div class="main mbody">
    <form method="post" action="<?php echo $this->createUrl('material/changePicsGroup'); ?>">
        <input type="hidden" id="ids" name="ids" value="<?php echo $page['ids']; ?>"/>
        <table class="tb3">
            <tr style="width=400px;text-align:center;vertical-align:middle;">
                <td style="width: 200px;text-align:center;vertical-align:middle;">将以下图片移动至：
                    <label>
                        <select id="picGroupId" name="picGroupId">
                            </option>
                            <?php
                            foreach ($picGroupList as $key => $val) {
                                ?>
                                <option
                                    value="<?php echo $val['id']; ?>">
                                    <?php echo $val['group_name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </label>

                </td>
                <td style="width: 200px;" class="alignleft"><input type="submit"  class="but" id="subtn" value="确定"/>
                </td>
            </tr>
            <tr>
                <th>图片名称</th>
                <th>原有分组</th>
            </tr>
            <?php foreach ($page['info'] as $key=>$val){?>
            <tr style="text-align:center;vertical-align:middle;">
                <td style="text-align:center;vertical-align:middle;"><?php echo $val['name'];?></td>
                <td style="text-align:center;vertical-align:middle;"><?php echo MaterialPicGroup::model()->findByPk($val['group_id'])->group_name;?></td>
            </tr>
            <?php }?>


        </table>

    </form>
</div>
