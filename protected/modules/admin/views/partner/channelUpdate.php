<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<?php
//页面默认值
if (!isset($page['info'])) {
    $page['info']['id'] = '';
    $page['info']['channel_code'] = '';
    $page['info']['channel_name'] = '';
    $page['info']['partner_name'] = '';
    $page['info']['business_type'] = '';
    $page['info']['remark'] = '';
}
?>
    <div class="main mhead">
        <div class="snav">渠道管理 » 合作商列表 » 渠道列表</div>
    </div>
    <div class="main mbody">
        <form method="post"
              action="<?php echo $this->createUrl($page['info']['id'] ? 'partner/channelEdit' : 'partner/channelAdd'); ?>?p=<?php echo $_GET['p']; ?>">
            <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
            <input type="hidden" id="partner_id" name="partner_id" value="<?php echo $page['partnerId']; ?>"/>
            <input type="hidden" id="backurl" name="backurl" value="<?php echo $this->get('url'); ?>" />

            <table class="tb3">
                <tr>
                    <th colspan="2" class="alignleft"><?php echo $page['info']['id'] ? '修改渠道' : '添加渠道' ?></th>
                </tr>
                <tr>
                    <td width="100">合作商：</td>
                    <td class="alignleft">
                        <?php echo $page['partnerName']; ?>
                    </td>
                </tr>
                <tr>
                    <td width="100">渠道编码：</td>
                    <td class="alignleft">
                        <input type="text" class="ipt" id="channel_code" name="channel_code"
                               value="<?php echo $page['info']['channel_code']; ?>"/>
                        <span style="color: red">渠道编码只能数字和字母组合，不能有特殊符号</span>
                    </td>

                </tr>
                <tr>
                    <td width="100">渠道名称：</td>
                    <td class="alignleft">
                        <input type="text" class="ipt" id="channel_name" name="channel_name"
                               value="<?php echo $page['info']['channel_name']; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td width="100">业务类型：</td>
                    <td class="alignleft">
                        <select name="business_type">
                            <?php echo $page['info']['id']? '':'<option>选择业务类型</option>';?>
                            <?php $businessTypesList = Dtable::toArr(BusinessTypes::model()->findAll());
                            foreach ($businessTypesList as $k => $v) {
                                ?>
                                <option value="<?php echo $v['bid'];?>" <?php echo $page['info']['business_type']==$v['bid']?'selected':'';?>><?php echo $v['bname'];?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td width="100">渠道类型：</td>
                    <td class="alignleft">
                        <select name="channel_type">
                            <?php echo $page['info']['type_id']? '':'<option>选择渠道类型</option>';?>
                            <?php $channelTypesList = Dtable::toArr(ChannelType::model()->findAll());
                            foreach ($channelTypesList as $k => $v) {
                                ?>
                                <option value="<?php echo $v['id'];?>" <?php echo $page['info']['type_id']==$v['id']?'selected':'';?>><?php echo $v['type_name'];?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td width="100">渠道描述：</td>
                    <td class="alignleft">
                        <textarea id="remark" name="remark"><?php echo $page['info']['remark']; ?> </textarea>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td class="alignleft">
                        <input type="submit" class="but" id="subtn" value="确定"/>
                        <input type="button" class="but" value="返回"
                               onclick="window.location='<?php echo $this->get('url')?$this->get('url'):$this->createUrl('partner/index'); ?>'"/>
                    </td>
                </tr>
            </table>
        </form>
    </div>
