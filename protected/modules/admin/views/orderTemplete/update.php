<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<script src="https://cdn.bootcss.com/bootstrap-colorpicker/2.5.1/js/bootstrap-colorpicker.min.js"></script>
<link href="https://cdn.bootcss.com/bootstrap-colorpicker/2.5.1/css/bootstrap-colorpicker.min.css" rel="stylesheet">
<style>
    .colorpicker {
        background-color: #222222;
        border-radius: 5px 5px 5px 5px;
        box-shadow: 2px 2px 2px #444444;
        color: #FFFFFF;
        font-size: 12px;
        position: absolute;
        width: 135px;
    }
</style>
<div class="main mhead">
    <div class="snav">直接下单管理 » 下单设置管理 » <?php echo $page['info']['id'] ? '编辑下单项目' : '添加下单项目' ?>  </div>
</div>
<div class="main mbody">
    <form method="post"
          action="<?php echo $this->createUrl($page['info']['id'] ? 'orderTemplete/edit' : 'orderTemplete/add'); ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <input type="hidden" id="backurl" name="backurl" value="<?php echo $this->get('url'); ?>"/>

        <table class="tb3">
            <?php if ($page['info']['id']): ?>
                <tr>
                    <td width="150">ID：</td>
                    <td><?php echo $page['info']['id'] ?></td>
                </tr>
            <?php endif ?>
            <tr>
                <td width="150">下单标题：</td>
                <td class="alignleft" width="150" colspan="2">
                    <input type="text" class="ipt" id="order_title" name="order_title"
                           value="<?php echo $page['info']['order_title']; ?>"/>
                </td>
            </tr>
            <tr>
                <td width="150">下单框样式：</td>
                <td class="alignleft" width="150" colspan="2">
                    <select name="goods_templete">
                        <option value="0" <?php if($page['info']['goods_templete'] == 0) echo 'selected';?>>默认下单框样式</option>
                        <option value="1" <?php if($page['info']['goods_templete'] == 1) echo 'selected';?>>下单框样式1</option>
                        <option value="2" <?php if($page['info']['goods_templete'] == 2) echo 'selected';?>>免费领取下单样式</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="150">商品组：</td>
                <td class="alignleft" width="150" colspan="2">
                    <?php
                    $packageGroups = PackageGroupManage::model()->findAll();
                    echo CHtml::dropDownList('package_gid', $page['info']['package_gid'], CHtml::listData($packageGroups, 'id', 'group_name'),
                        array(
                            'empty' => '请选择',
                            'ajax' => array(
                                'type' => 'POST',
                                'url' => $this->createUrl('orderTemplete/getPackages'),
                                'update' => '#package',
                                'data' => array('group_id' => 'js:$("#package_gid").val()'),
                            ),
                        )
                    );
                    ?>
                </td>
            </tr>
            <tr>
                <td width="150">下单商品：</td>
                <td class="alignleft" colspan="2">
                    <table>
                        <thead>
                        <tr>
                            <td width="200">下单商品名</td>
                            <td>
                                <div id="goods_color" class="input-group colorpicker-component">
                                    <input name="goods_color" type="text"
                                           value="<?php echo $page['info']['goods_color'] ? $page['info']['goods_color'] : "#ee4c95"; ?>"
                                           class="form-control" hidden/>
                                    <span class="input-group-addon"><i></i></span>
                                </div>
                            </td>
                            <td width="100">下单商品价格</td>
                            <td>
                                <div id="price_color" class="input-group colorpicker-component">
                                    <input name="price_color" type="text"
                                           value="<?php echo $page['info']['price_color'] ? $page['info']['price_color'] : "#ee4c95"; ?>"
                                           class="form-control" hidden/>
                                    <span class="input-group-addon"><i></i></span>
                                </div>
                            </td>
                        </tr>
                        </thead>
                        <?php if (!$page['info']['id']) { ?>
                            <tbody id="package"></tbody>
                        <?php } else { ?>
                            <tbody id="package">
                            <input hidden value="<?php echo $page['info']['id'] ?>" name="templete_id">

                            <?php foreach ($page['packages'] as $v) { ?>
                                <tr>
                                    <td><?php echo $v['package_name'] ?><input hidden value="<?php echo $v['package_id'] ?>"
                                                                       name="package_id[]">
                                    </td>
                                    <td><?php echo $v['package_price'] ?></td>
                                    <td><input value="1" <?php echo $v['recommend'] == 1 ? 'checked' : '' ?>
                                               type="checkbox" name="recommend_<?php echo $v['package_id'] ?>"/>&nbsp;推荐
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        <?php } ?>
                    </table>
                </td>

            </tr>
            <tr>
                <td width="150">下单提交按钮内容：</td>
                <td class="alignleft" width="150" colspan="2">
                    <input type="text" class="ipt" id="obtn_text" name="obtn_text"
                           value="<?php echo $page['info']['obtn_text']; ?>"/>
                </td>

            </tr>
            <tr>
                <td width="150">提示信息：</td>
                <td class="alignleft" colspan="2">
                    <div style="position:relative;">
                        <textarea style="width:500px; height: 150px" id="order_tips"
                                  name="order_tips"><?php echo $page['info']['order_tips'] ? $page['info']['order_tips'] : ''; ?></textarea>
                        <script>
                            var order_tips = $("#order_tips").xheditor({
                                plugins: allplugin,
                                internalScript: true,
                                tools: "FontSize,Fontface,Bold,FontColor,Img",
                                skin: "nostyle"
                            });
                        </script>
                        <span style="position: absolute;left: 400px;" id="downbtn_info_body">

                            <a onclick="return dialog_frame(this,500,580,false)"
                               href="<?php echo $this->createUrl('material/addMaterialPics'); ?>">插入素材库图片</a>&nbsp;&nbsp;
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <td width="150">下单成功信息：</td>
                <td class="alignleft" colspan="2">
                    <div style="position:relative;">
                        <textarea style="width:500px; height: 120px" id="success_info"
                                  name="success_info"><?php echo $page['info']['success_info'] ? $page['info']['success_info'] : ''; ?></textarea>
                        <script>
                            var success_info = $("#success_info").xheditor({
                                plugins: allplugin,
                                internalScript: true,
                                tools: "FontSize,Fontface,Bold,FontColor",
                                skin: "nostyle"
                            });
                        </script>

                    </div>
                </td>
            </tr>
            <tr>
                <td width="150">下单失败信息：</td>
                <td class="alignleft" colspan="2">
                    <div style="position:relative;">
                        <textarea style="width:500px; height: 120px" id="fail_info"
                                  name="fail_info"><?php echo $page['info']['fail_info'] ? $page['info']['fail_info'] : ''; ?></textarea>
                        <script>
                            var fail_info = $("#fail_info").xheditor({
                                plugins: allplugin,
                                internalScript: true,
                                tools: "FontSize,Fontface,Bold,FontColor",
                                skin: "nostyle"
                            });
                        </script>

                    </div>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;width: 110px">
                    底部悬浮是否开启：
                </td>
                <td colspan="2">
                    <input name="is_suspend" <?php echo $page['info']['is_suspend'] == 0 ? "checked" : ""; ?>
                           onclick="addSth(this.value)" type="radio" value='0'/>否&nbsp;&nbsp;
                    <input name="is_suspend" <?php echo $page['info']['is_suspend'] == 1 ? "checked" : ""; ?>
                           onclick="addSth(this.value)" type="radio" value='1'/>是&nbsp;&nbsp;
                </td>
            </tr>
            <tr class="sth" <?php echo $page['info']['is_suspend'] == 0 ? "hidden" : '' ?>>
                <td>底部悬浮图片</td>
                <td colspan="2">
                    <input type="hidden" id="suspend_img" name="suspend_img"
                           value="<?php echo $page['info']['suspend_img'] ?>"/>
                    <img
                            id="suspend_show" <?php if ($page['info']['suspend_img']) echo "style='width: 414px;height: 80px;'"; ?>
                            src="<?php echo $page['info']['suspend_img'] ?>">
                    <a class="but1" onclick="return dialog_frame(this,450,580,false)"
                       href="<?php echo $this->createUrl('material/addMaterialPics'); ?>?add_type=2">选择素材库图片</a>

                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;width: 110px">
                    浮标是否开启：
                </td>
                <td colspan="2">
                    <input name="is_dobber" <?php echo $page['info']['is_dobber'] == 0 ? "checked" : ""; ?>
                           onclick="addSth1(this.value)" type="radio" value='0'/>否&nbsp;&nbsp;
                    <input name="is_dobber" <?php echo $page['info']['is_dobber'] == 1 ? "checked" : ""; ?>
                           onclick="addSth1(this.value)" type="radio" value='1'/>是&nbsp;&nbsp;
                </td>
            </tr>
            <tr class="sth1" <?php echo $page['info']['is_dobber'] == 0 ? "hidden" : '' ?>>
                <td>浮标图片</td>
                <td colspan="2">
                    <input type="hidden" id="dobber_img" name="dobber_img"
                           value="<?php echo $page['info']['dobber_img'] ?>"/>
                    <img
                            id="dobber_show" <?php if ($page['info']['dobber_img']) echo "style='width: 80px;height: 80px;'"; ?>
                            src="<?php echo $page['info']['dobber_img'] ?>">
                    <a class="but1" onclick="return dialog_frame(this,450,580,false)"
                       href="<?php echo $this->createUrl('material/addMaterialPics'); ?>?add_type=3">选择素材库图片</a>

                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;width: 110px">
                    轮播信息是否开启：
                </td>
                <td colspan="2">
                    <input name="is_carousel" <?php echo $page['info']['is_carousel'] == 0 ? "checked" : ""; ?>
                           type="radio" value='0'/>否&nbsp;&nbsp;
                    <input name="is_carousel" <?php echo $page['info']['is_carousel'] == 1 ? "checked" : ""; ?>
                           type="radio" value='1'/>是&nbsp;&nbsp;
                </td>
            </tr>

            <tr>
                <td>整体字体颜色</td>
                <td colspan="2">
                    <div id="font_color" class="input-group colorpicker-component">
                        <input name="font_color" type="text"
                               value="<?php echo $page['info']['font_color'] ? $page['info']['font_color'] : "#ffffff"; ?>"
                               class="form-control" hidden/>
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>整体颜色</td>
                <td colspan="2">
                    <div id="overall_color" class="input-group colorpicker-component">
                        <input name="overall_color" type="text"
                               value="<?php echo $page['info']['overall_color'] ? $page['info']['overall_color'] : "#ee4d96"; ?>"
                               class="form-control" hidden/>
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </td>
            </tr>

            <tr>
                <td></td>
                <td class="alignleft">
                    <input type="submit" class="but" id="subtn" value="确定"/>
                    <input type="button" class="but" value="返回"
                           onclick="window.location='<?php echo $this->get('url'); ?>'"/>
                </td>
            </tr>
        </table>
    </form>
</div>
<script>
    $(function () {
        $('#price_color').colorpicker();
        $('#overall_color').colorpicker();
        $('#font_color').colorpicker();
        $('#goods_color').colorpicker();

    });

    function addSth(value) {
        if (value == 0) {
            $(".sth").hide();
        } else if (value == 1) {
            $(".sth").show();
        }
    }

    function addSth1(value) {
        if (value == 0) {
            $(".sth1").hide();
        } else if (value == 1) {
            $(".sth1").show();
        }
    }

    //添加素材库图片交互
    function addImg(url) {
        var html = "<p><img  src='" + url + "' _xhe_src='" + url + "' /></p>";
        $("#order_tips").xheditor().pasteHTML(html);
    }

    //添加一张图片 顶部背景图和头像
    function addOneImg(url, type) {
        if (type == 1) {
            var obj = $("#top_img");
            $("#top_show").attr('src', url);
            $("#top_show").css({width: 100, height: 100})
        } else if (type == 2) {
            var obj = $("#suspend_img");
            $("#suspend_show").attr('src', url);
            $("#suspend_show").css({width: 414, height: 80})
        } else if (type == 3) {
            var obj = $("#dobber_img");
            $("#dobber_show").attr('src', url);
            $("#dobber_show").css({width: 80, height: 80})
        }
        $(obj).val(url);

    }

</script>
