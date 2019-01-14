<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<?php
//页面默认值
if (!isset($page['info'])) {
    $page['info']['id'] = '';
    $page['info']['name'] = '';
}
?>
    <div class="main mhead">
        <div class="snav">渠道管理 » 合作商列表 » <?php echo $page['info']['id'] ? '修改合作商' : '添加合作商' ?>  </div>
    </div>
    <div class="main mbody">
        <form method="post"
              action="<?php echo $this->createUrl($page['info']['id'] ? 'partner/edit' : 'partner/add'); ?>?partner_id=<?php echo $page['info']['id']; ?>">
            <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
            <input type="hidden" id="backurl" name="backurl" value="<?php echo $this->get('url'); ?>"/>

            <table class="tb3">
                <tr>
                    <th colspan="2" class="alignleft"><?php echo $page['info']['id'] ? '修改合作商' : '添加合作商' ?></th>
                </tr>
                <?php if ($page['info']['id']): ?>
                    <tr>
                        <td width="150">ID：</td>
                        <td><?php echo $page['info']['id'] ?></td>
                    </tr>
                <?php endif ?>
                <tr>
                    <td width="150">合作商（收款人）：</td>
                    <td class="alignleft">
                        <input type="text" class="ipt" id="name" name="name"
                               value="<?php echo $page['info']['name']; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td class="alignleft"><input type="submit" class="but" id="subtn" value="确定"/> <input type="button"
                                                                                                          class="but"
                                                                                                          value="返回"
                                                                                                          onclick="window.location='<?php echo $this->get('url')?$this->get('url'):$this->createUrl('partner/index'); ?>'"/>
                    </td>
                </tr>
            </table>
        </form>
    </div>
