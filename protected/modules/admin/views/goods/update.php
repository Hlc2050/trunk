<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<?php
//页面默认值
if(!isset($page['info']))
{
	$page['info']['id']='';
    $page['info']['characters']='';
	$page['info']['goods_name']='';
    $page['info']['remark']='';
    $page['info']['cat_id']='';
    $page['info']['service_group'] = 0;
}
?>
<div class="main mhead">
    <div class="snav">基础类别 » 商品列表  » <?php echo $page['info']['id']?'修改商品':'添加商品' ?></div>
</div>
<div class="main mbody">
<form method="post" action="<?php echo $this->createUrl($page['info']['id']?'goods/edit':'goods/add'); ?>?p=<?php echo $_GET['p'];?>">
<input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>" />
<table class="tb3">
    <tr>
        <th colspan="2" class="alignleft"><?php echo $page['info']['id']?'修改商品':'添加商品' ?></th>
    </tr>
    <tr>
        <td  width="120">商品</td>
        <td  class="alignleft">
            <input type="text" class="ipt"  id="goods_name"   name="goods_name" value="<?php echo $page['info']['goods_name'];?>"/>
        </td>
    </tr>
    <tr>
        <td  width="120">形象：</td>
        <td  class="alignleft">
            <a href="javascript:void(0);" onclick="check_all('.chlist');">全选/反选</a><br/>
            <?php
            $i=0;
            //形象列表
            $charactersArr = explode(',',$page['info']['characters']);
            $characterList=Linkage::model()->getCharacterList();
            foreach ($characterList as $key => $val) {
                ?>
                <input type="checkbox" name="character_list[]" class="chlist" <?php if(in_array($key, $charactersArr)) echo 'checked'; ?>  value="<?php echo $key; ?>" /><?php echo $val ?>&nbsp;&nbsp;
                <?php
                if ($i == 2) {
                    $i = 0;
                    ?>  <br/><?php
                }
                $i++;
            }
            ?>
        </td>
    </tr>
    <tr>
    <tr>
        <td  width="120">商品类别：</td>
        <td  class="alignleft">
            <select name="cat_id">
                <option value="" selected>
                    请选择类别
                </option>
                <?php
                //商品类别列表
                $categoryList=Linkage::model()->getGoodsCategoryList();
                foreach ($categoryList as $key => $val) {
                    ?>
                    <option
                        value="<?php echo $key; ?>" <?php echo $key==$page['info']['cat_id'] ? 'selected' : ''; ?>>
                        <?php echo $val;?>
                    </option>
                <?php } ?>
            </select>
        </td>
    </tr>
    <tr>
        <td  width="120">性别特征：</td>
        <td  class="alignleft">
            <select name="service_group">
                <?php
                foreach ( vars::$fields['sex'] as $key => $val) {
                    ?>
                    <option
                        value="<?php echo $val['value']; ?>" <?php echo $val['value']==$page['info']['service_group'] ? 'selected' : ''; ?>>
                        <?php echo $val['txt'];?>
                    </option>
                <?php } ?>
            </select>
        </td>
    </tr>
    <tr>
        <td  width="120">商品描述：</td>
        <td  class="alignleft">
            <textarea id="remark"   name="remark"><?php echo $page['info']['remark']; ?> </textarea>
        </td>
    </tr>
    <tr>
        <td></td>
        <td  class="alignleft"><input type="submit" class="but" id="subtn" value="确定" /> <input type="button" class="but" value="返回" onclick="window.location='<?php echo $this->createUrl('goods/index'); ?>?p=<?php echo $_GET['p'];?>'" /></td>
    </tr>
</table>
</form>
</div>
