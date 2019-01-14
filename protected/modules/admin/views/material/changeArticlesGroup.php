<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<?php
$ArticleGroupList = $this->toArr(MaterialArticleGroup::model()->findAll());
?>
<div class="main mhead">
    <div class="snav">
        <table  class="tb3"><tr><th align="left">修改图文分组</th></tr></table></div>
</div>
<div class="main mbody">
    <form method="post" action="<?php echo $this->createUrl('material/changeArticlesGroup'); ?>">
        <input type="hidden" id="ids" name="ids" value="<?php echo $page['ids']; ?>"/>
        <table class="tb3">

            <tr style="width=400px;text-align:center;vertical-align:middle;">
                <td colspan="2"  class="alignleft">
                    将以下图文移至分组：
                        <select id="group_id" name="group_id">
                            <?php
                            foreach ($ArticleGroupList as $key => $val) {
                                ?>
                                <option
                                    value="<?php echo $val['id']; ?>">
                                    <?php echo $val['group_name']."(".$val['group_code'].")"; ?>
                                </option>
                            <?php } ?>
                        </select>
                </td>
                <td class="alignleft"><input type="submit"  class="but" id="subtn" value="确定"/>
                </td>
            </tr>
            <tr>
                <th style="width: 150px">图文名称</th>
                <th style="width: 150px">图文编码</th>
                <th>原有分组</th>
            </tr>
            <?php foreach ($page['info'] as $key=>$val){?>
            <tr style="text-align:center;vertical-align:middle;">
                <td style="text-align:center;vertical-align:middle;"><?php echo $val['article_title'];?></td>
                <td style="text-align:center;vertical-align:middle;"><?php echo $val['article_code'];?></td>
                <td style="text-align:center;vertical-align:middle;"><?php echo $val['group_id']?MaterialArticleGroup::model()->findByPk($val['group_id'])->group_name:"未分组";?></td>
            </tr>
            <?php }?>

        </table>

    </form>
</div>
