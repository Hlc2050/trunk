<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav"><?php echo $page['info']['id'] ? '修改图文组别' : '添加图文组别' ?>  </div>
</div>
<div class="main mbody">
    <form method="post"
          action="<?php echo $this->createUrl($page['info']['id'] ? 'material/editArticleGroup' : 'material/addArticleGroup'); ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <table class="tb3">
            <?php if ($page['info']['id']): ?>
                <tr>
                    <td width="150">组别ID：</td>
                    <td><?php echo $page['info']['id'] ?></td>
                </tr>
            <?php endif ?>
            <tr>
                <td width="150">组别编码：</td>
                <td width="150">
                    <input type="text" class="ipt" id="group_code" name="group_code"
                           value="<?php echo $page['info']['group_code']; ?>"/>
                </td>
              
            </tr>
            <tr>
                <td width="150">组别名称：</td>
                <td>
                    <input type="text" class="ipt" id="group_name" name="group_name"
                           value="<?php echo $page['info']['group_name']; ?>"/>
                </td>
            </tr>
            <tr>
                <td width="150">商品类别：</td>
                <td>
                    <?php
                    $categoryList = Linkage::model()->get_linkage_data(20);
                    echo CHtml::dropDownList('cat_id', $page['info']['cat_id'], CHtml::listData($categoryList, 'linkage_id', 'linkage_name'),
                        array(
                            'empty' => '请选择'
                        )
                    );
                    ?>
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
