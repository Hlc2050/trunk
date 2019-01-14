<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">新增列表>>导航页内容设置</div>
</div>
<div class="main mbody">
    <div class="tab">
        <a href="#" onclick="changeNav(this,1)" class="current ">首页</a>
        <a href="#" onclick="changeNav(this,2)">客服反馈</a>
        <a href="#" onclick="changeNav(this,3)">联系我们</a>
    </div>
    <form method="post"
          action="<?php $this->createUrl('miniAppsManage/content'); ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <input type="hidden" id="cdn_url"  value="<?php echo Yii::app()->params['miniApps']['img_url'];; ?>"/>
        <input type="hidden" id="backurl" name="backurl" value="<?php echo $this->get('url'); ?>"/>

        <table class="tb3">
            <tr  class="nav_1">
                <td colspan="2" style="position:relative;">
                    <span style="vertical-align:middle;font-size: large;color: dimgrey;font-weight:bold">
                        内容设置：
                    </span>
                    <br/>
                    <div style="position:relative;">
                        <textarea style="width:100%; height: 450px" id="content_one"
                                  name="content_one"><?php echo $page['info']['content_one'] ? $page['info']['content_one'] : ''; ?></textarea>
                        <script>
                            var content_one = $("#content_one").xheditor({
                                plugins: allplugin,
                                internalScript: true,
                                tools: "mfull",
                                skin: "nostyle",

                            });
                        </script>
                        <span class="downhttpimgbtn" id="downbtn_content_one">
                            <a onclick="return dialog_frame(this,500,580,false)"
                               href="<?php echo $this->createUrl('material/addMaterialPics?id=content_one'); ?>">插入素材库图片</a>&nbsp;&nbsp;
                        </span>

                    </div>
                </td>
            </tr>
            <tr  class="nav_1">
                <td style="vertical-align:middle;width: 110px" >
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 是否开启客服咨询：</span>
                </td>
                <td>
                    <input name="is_consult_one" <?php echo $page['info']['is_consult_one'] == 0 ? "checked" : ""; ?>
                          type="radio" value='0'/>否&nbsp;&nbsp;
                    <input name="is_consult_one" <?php echo $page['info']['is_consult_one'] == 1 ? "checked" : ""; ?>
                           type="radio" value='1'/>是&nbsp;&nbsp;
                </td>
            </tr>
            <tr  class="nav_2" hidden>
                <td colspan="2" style="position:relative;">
                    <span style="vertical-align:middle;font-size: large;color: dimgrey;font-weight:bold">
                        内容设置：
                    </span>
                    <br/>
                    <div style="position:relative;">
                        <textarea style="width:100%; height: 450px" id="content_two"
                                  name="content_two"><?php echo $page['info']['content_two'] ? $page['info']['content_two'] : ''; ?></textarea>
                        <script>
                            var content_two = $("#content_two").xheditor({
                                plugins: allplugin,
                                internalScript: true,
                                tools: "mfull",
                                skin: "nostyle",

                            });
                        </script>
                        <span class="downhttpimgbtn" id="downbtn_content_two">
                            <a onclick="return dialog_frame(this,500,580,false)"
                               href="<?php echo $this->createUrl('material/addMaterialPics?id=content_two'); ?>">插入素材库图片</a>&nbsp;&nbsp;
                        </span>

                    </div>
                </td>
            </tr>
            <tr  class="nav_2" hidden>
                <td style="vertical-align:middle;width: 110px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 是否开启客服咨询：</span>
                </td>
                <td>
                    <input name="is_consult_two" <?php echo $page['info']['is_consult_two'] == 0 ? "checked" : ""; ?>
                           type="radio" value='0'/>否&nbsp;&nbsp;
                    <input name="is_consult_two" <?php echo $page['info']['is_consult_two'] == 1 ? "checked" : ""; ?>
                           type="radio" value='1'/>是&nbsp;&nbsp;
                </td>
            </tr>
            <tr  class="nav_3" hidden>
                <td colspan="2" style="position:relative;">
                    <span style="vertical-align:middle;font-size: large;color: dimgrey;font-weight:bold">
                        内容设置：
                    </span>
                    <br/>
                    <div style="position:relative;">
                        <textarea style="width:100%; height: 450px" id="content_three"
                                  name="content_three"><?php echo $page['info']['content_three'] ? $page['info']['content_three'] : ''; ?></textarea>
                        <script>
                            var content_three = $("#content_three").xheditor({
                                plugins: allplugin,
                                internalScript: true,
                                tools: "mfull",
                                skin: "nostyle",
                            });
                        </script>
                        <span class="downhttpimgbtn" id="downbtn_content_three">
                            <a onclick="return dialog_frame(this,500,580,false)"
                               href="<?php echo $this->createUrl('material/addMaterialPics?id=content_three'); ?>">插入素材库图片</a>&nbsp;&nbsp;
                        </span>

                    </div>
                </td>
            </tr>
            <tr  class="nav_3" hidden>
                <td style="vertical-align:middle;width: 110px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 是否开启客服咨询：</span>
                </td>
                <td>
                    <input name="is_consult_three" <?php echo $page['info']['is_consult_three'] == 0 ? "checked" : ""; ?>
                           type="radio" value='0'/>否&nbsp;&nbsp;
                    <input name="is_consult_three" <?php echo $page['info']['is_consult_three'] == 1 ? "checked" : ""; ?>
                           type="radio" value='1'/>是&nbsp;&nbsp;
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

<style>
    .tab a.current {
        background: #f6f8fa;
        color: #000;
        border: 1px solid #d1d5de;
        border-bottom: none;
        position: relative;
        top: 2px;
        border-top: 2px solid #2fa4e7;
    }

    .tab a {
        background: #2fa4e7;
        display: inline-block;
        padding: 10px 20px;
        color: #fff;
        font-size: 14px;
    }
</style>
<script>

    function changeNav(e, a) {
        $('.current').removeClass('current');
        $(e).addClass('current');
        $('.nav_1').hide();
        $('.nav_2').hide();
        $('.nav_3').hide();
        $('.nav_'+a).show();

    }

    //添加素材库图片交互
    function addImg(url, sign) {
        if (!sign) sign = 'info_body';
        cdn_url = $("#cdn_url").val();
        var html = "<p><img src='"+cdn_url + url + "' _xhe_src='" + url + "' /></p>";
        $("#" + sign).xheditor().pasteHTML(html);
    }


</script>
