<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">新增列表>><?php echo $page['info']['id'] ? '修改' : '新增'; ?></div>
</div>
<div class="main mbody">
    <form method="post"
          action="<?php echo $page['info']['id'] ? $this->createUrl('miniAppsManage/edit') : $this->createUrl('miniAppsManage/add'); ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <input type="hidden" id="backurl" name="backurl" value="<?php echo $this->get('url'); ?>"/>

        <table class="tb3">
            <tr>
                <td width="120">
                    小程序名称
                </td>
                <td>
                    <input type="text" class="ipt" id="app_name" name="app_name"
                           value="<?php echo $page['info']['app_name']; ?>"/>
                    <span style="color: red;">*必填</span>
                </td>
            </tr>
            <tr>
                <td width="120">
                    原始ID
                </td>
                <td>
                    <input type="text" class="ipt" id="origin_id" name="origin_id"
                           value="<?php echo $page['info']['origin_id']; ?>"/>
                </td>
            </tr>
            <tr>
                <td width="120">
                    关联公众号名称
                </td>
                <td>
                    <input type="text" class="ipt" id="official_accounts" name="official_accounts"
                           value="<?php echo $page['info']['official_accounts']; ?>"/>
                    <span style="color: red;">*必填，多个请用,隔开</span>
                </td>
            </tr>
            <tr>
                <td width="120">
                    appid
                </td>
                <td>
                    <input type="text" class="ipt" id="appid" name="appid"
                           value="<?php echo $page['info']['appid']; ?>"/>
                </td>
            </tr>
            <tr>
                <td width="120">
                    secret
                </td>
                <td>
                    <input type="text" class="ipt" id="secret" name="secret"
                           value="<?php echo $page['info']['secret']; ?>"/>
                </td>
            </tr>
            <tr>
                <td width="100">打款ID</td>
                <td>
                    <input class="ipt" style="font-size:small;width: 100px" name="fpay_id" id="fpay_id" value="<?php echo $page['info']['fpay_id']; ?>" />
                    &nbsp;&nbsp;&nbsp;
                    <a href="#" id="get_data">确定</a>
                </td>
            </tr>
            <tr>
                <td width="100">微信号小组：</td>
                <td class="alignleft">
                    <input type="text" class="ipt" id="wechat_group_name" name="wechat_group_name" value="<?php echo WeChatGroup::model()->findByPk($page['info']['wechat_group_id'])->wechat_group_name; ?>"
                           disabled/>
                    <input type="hidden" name="wechat_group_id" id="wechat_group_id"
                           value="<?php echo $page['info']['wechat_group_id'] ?>">
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 微信号弹出模式：</span>
                </td>
                <td>
                    <input name="kefu_type" <?php echo $page['info']['kefu_type'] == 0 ? "checked" : ""; ?>
                           onclick="addSth(this.value)" type="radio" value='0'/>图片&nbsp;&nbsp;
                    <input name="kefu_type" <?php echo $page['info']['kefu_type'] == 1 ? "checked" : ""; ?>
                           onclick="addSth(this.value)" type="radio" value='1'/>文字&nbsp;&nbsp;
                </td>
            </tr>
            <tr  class="sth" <?php echo $page['info']['kefu_type'] == 0?"hidden":''?>>
                <td style="vertical-align:middle;width: 80px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 内容：</span>
                </td>
                <td>
                    <textarea style="font-size: small;height: 50px;" class="ipt"
                              name="kefu_content"><?php echo $page['info']['kefu_content']; ?></textarea>
                    <span style="color: red">* 以 {{wechat}} 代替微信号</span>
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="alignleft"><input type="submit" class="but" id="subtn" value="确定"/>
                    <input type="button" class="but" value="返回"
                           onclick="window.location='<?php echo $this->get('url'); ?>'"/>
                </td>
            </tr>
        </table>

    </form>
</div>
<script language="javascript">
    $(function () {
        $('#get_data').on('click',function () {
            fpay_id = $('#fpay_id').val();
            if(fpay_id==null)return false;
            $.ajax({
                type: "GET",
                url: "/admin/infancePay/getDataById",
                data: "fpay_id=" + fpay_id,
                success: function (data) {
                    if (data != ''){
                        json = eval('(' + data + ')');
                        jQuery("#wechat_group_name").val(json.wechat_group_name);//
                        jQuery("#wechat_group_id").val(json.wechat_group_id);//

                    }else {
                        var str = "打款id不存在";
                        jQuery("#wechat_group_name").val(str);//
                        jQuery("#wechat_group_id").val();//
                    }
                    return false;
                }
            });

        })
    })
    function addSth(value) {
        if (value == 0) {
            $(".sth").hide();
        } else if (value == 1) {
            $(".sth").show();
        }
    }

</script>