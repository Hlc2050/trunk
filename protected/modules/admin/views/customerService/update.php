<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<?php
//页面默认值
if (!isset($page['info'])) {
    $page['info']['id'] = '';
    $page['info']['cname'] = '';
    $page['info']['goods_id'] = '';
    $page['info']['estimate_rate'] = '';
    $page['info']['goodsArr'] = array();
}
?>
<style>
    .fix-width {
        display: inline-block;
    }
</style>
<div class="main mhead">
    <div class="snav">基础类别 » 客服部管理 » <?php echo $page['info']['id'] ? '修改客服部' : '添加客服部' ?></div>
</div>
<div class="main mbody">
    <form method="post"
          action="<?php echo $this->createUrl($page['info']['id'] ? 'customerService/edit' : 'customerService/add'); ?>?p=<?php echo $_GET['p']; ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <table class="tb3">
            <tr>
                <th colspan="2" class="alignleft"><?php echo $page['info']['id'] ? '修改客服部' : '添加客服部' ?></th>
            </tr>
            <?php if ($page['info']['id']): ?>
                <tr>
                    <td width="120">ID：</td>
                    <td><?php echo $page['info']['id'] ?></td>
                </tr>
            <?php endif ?>
            <tr>
                <td width="120">客服部：</td>
                <td class="alignleft">
                    <input type="text" class="ipt" id="cname" name="cname"
                           value="<?php echo $page['info']['cname']; ?>"/>
                </td>
            </tr>
            <tr>
                <td width="120">商品：</td>
                <td class="alignleft">
                    <a href="javascript:void(0);" onclick="check_all('.chlist');">全选/反选</a><br/>
                    <?php
                    $i = 0;
                    //形象列表
                    $goodsList = Goods::model()->getGoodsList();
                    foreach ($goodsList as $key => $val) {
                        ?>
                        <input type="checkbox" name="goodsList[]"
                               class="chlist" <?php if (in_array($key, $page['info']['goodsArr'])) echo 'checked'; ?>
                               value="<?php echo $key; ?>"/><span class="fix-width"><?php echo $val; ?></span>
                        <?php
                        if ($i == 4) {
                            $i = 0;
                            ?>  <br/><?php
                        }
                        $i++;
                    }
                    ?>
                </td>
            </tr>

            <tr>
                <td width="120">预计发货率（%）：</td>
                <td class="alignleft">
                    <input type="text" class="ipt" id="estimate_rate" name="estimate_rate"
                           value="<?php echo $page['info']['estimate_rate']; ?>"/>
                </td>
            </tr>
            <?php if (!$page['info']['id']): ?>
                <tr>
                    <td width="120">是否为独立客服部：</td>
                    <td class="alignleft">
                        否&nbsp;<input name="status" type="radio" value="0" checked="checked"/>&nbsp;&nbsp;&nbsp;
                        是&nbsp;<input name="status" type="radio" value="1"/>
                    </td>
                </tr>
            <?php endif; ?>
            <tr>
                <td width="120">合作状态：</td>
                <td class="alignleft">
                    开启&nbsp;<input name="state_cooperation" type="radio" value="0"  <?php if($page['info']['state_cooperation'] == 0 || $page['info']['state_cooperation'] =='')echo "checked";  ?>>&nbsp;&nbsp;&nbsp;
                    暂停&nbsp;<input name="state_cooperation" type="radio" value="1" <?php if($page['info']['state_cooperation'] == 1 || $page['info']['state_cooperation'] =='')echo "checked";  ?>/>
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="alignleft"><input type="submit" class="but" id="subtn" value="确定"/> <input type="button"
                                                                                                      class="but"
                                                                                                      value="返回"
                                                                                                      onclick="window.location='<?php echo $this->createUrl('customerService/index'); ?>?p=<?php echo $_GET['p']; ?>'"/>
                </td>
            </tr>
        </table>
    </form>
</div>

