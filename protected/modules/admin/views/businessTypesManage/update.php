<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<?php
//页面默认值
if (!isset($page['info'])) {
    $page['info']['bid'] = '';
    $page['info']['bname'] = '';
    $page['info']['chargingTypeArr'] =array();
}
?>
<style>
    .fix-width {
        display: inline-block;
    }
</style>
<div class="main mhead">
    <div class="snav">基础类别 » 业务类型管理 » <?php echo $page['info']['bid'] ? '修改业务类型' : '新增业务类型' ?></div>
</div>
<div class="main mbody">
    <form method="post"
          action="<?php echo $this->createUrl($page['info']['bid'] ? 'businessTypesManage/edit' : 'businessTypesManage/add'); ?>?p=<?php echo $_GET['p']; ?>">
        <input type="hidden" id="bid" name="bid" value="<?php echo $page['info']['bid']; ?>"/>
        <table class="tb3">
            <tr>
                <th colspan="2" class="alignleft"><?php echo $page['info']['bid'] ? '修改业务类型' : '新增业务类型' ?></th>
            </tr>
            <?php if ($page['info']['bid']): ?>
                <tr>
                    <td width="120">ID：</td>
                    <td><?php echo $page['info']['bid'] ?></td>
                </tr>
            <?php endif ?>
            <tr>
                <td width="120">业务类型：</td>
                <td class="alignleft">
                    <input type="text" class="ipt" name="bname" id="bname"
                           value="<?php echo $page['info']['bname'] ? $page['info']['bname'] : ''; ?>"/>
                </td>
            </tr>
            <tr>
                <td width="120">计费方式：</td>
                <td colspan="2" id="chargingTypes" ">
                    <?php
                    foreach (vars::$fields['charging_type'] as $k => $v) { ?>
                        <input name="chargingTypes[]"  style="width: 20px" type="checkbox" id="<?php echo $v['txt']; ?>" value="<?php echo $v['value']; ?>" <?php if(in_array($v['value'], $page['info']['chargingTypeArr'])) echo 'checked';?>/>
                        <label for="<?php echo $v['txt']; ?>">
                          <span id="<?php echo $v['value']; ?>">
                        <?php echo $v['txt']; ?>
                          </span>
                        </label>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="alignleft"><input type="submit" class="but" id="subtn" value="确定"/> <input type="button"
                                                                                                      class="but"
                                                                                                      value="返回"
                                                                                                      onclick="window.location='<?php echo $this->createUrl('businessTypesManage/index'); ?>?p=<?php echo $_GET['p']; ?>'"/>
                </td>
            </tr>
        </table>
    </form>
</div>


