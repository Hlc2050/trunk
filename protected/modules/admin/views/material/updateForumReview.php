<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<style>
    .review {
        height: 40px;
        line-height: 40px;
        width: 602px;
        vertical-align: middle;
        font-size: large;
        color: black;
        background-color: #CCCCCC;
    }

    .fewer {
        float: right;
        margin-right: 15px;
        color: blue;
        font-weight: normal;
        font-size: 13px;
    }

    .mybtn {
        position: absolute;
        top: 2px;
        right: 2px
    }

    .deleteTr {
        float: right;
        margin-right: 15px;
        color: blue;
        font-weight: normal;
        font-size: 13px;
    }
    .deleteTd {
        float: right;
        margin-right: 15px;
        color: red;
        font-weight: bold;
        font-size: 13px;
    }
</style>
<div class="main mhead">
    <div class="snav">素材管理 » 评论管理 » <?php echo $page['info']['id'] ? '修改论坛评论' : '添加论坛评论' ?></div>
</div>
<div class="main mbody">
    <form method="post"
          action="<?php echo $this->createUrl($page['info']['id'] ? 'material/editForumReview' : 'material/addForumReview'); ?> ">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <input type="hidden" id="review_type" name="review_type" value="论坛"/>
        <table class="tb3" id="ForumReview">
            <tbody>
            <tr>
                <td style="vertical-align:middle;">
                    评论标题：
                    <input style="width: 340px;height:30px;font-size: large;" type="text" class="ipt"
                           name="review_title"
                           value="<?php echo $page['info']['review_title'] ? $page['info']['review_title'] : ""; ?>"/>
                    <span style="color: red">*必填</span>
                </td>
            </tr>
            <tr>
                <td>评论类型：论坛</td>
            </tr>
            <tr>
                <td>支持人员：
                    <?php
                    //支持人员列表哦
                    $supportStafflist = SupportStaff::model()->getSupportStaffList();
                    echo CHtml::dropDownList('support_staff_id', $page['info']['support_staff_id'], CHtml::listData($supportStafflist, 'user_id', 'name'),
                        array(
                            'empty' => '选择支持人员',
                        )
                    );
                    ?>
                    <span style="color: red">*必选</span>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">
                    楼主名称：
                    <input style="width: 340px;height:30px;font-size: large;" type="text" class="ipt" name="landlord" value="<?php echo $page['info']['landlord'] ? $page['info']['landlord'] : ""; ?>"/>
                </td>
            </tr>
            <tr>
                <td>楼主头像：
                    <input type="hidden" id="avater_img" name="avater_img" value="<?php echo $page['info']['avatar_url']?>" />
                    <img id="avater_show" <?php if($page['info']['avatar_url']) echo "style='width: 80px;height: 80px;'";?>  src="<?php echo $page['info']['avatar_url']?>">
                    <a class="but1" onclick="return dialog_frame(this,450,580,false)"
                       href="<?php echo $this->createUrl('material/addMaterialPics'); ?>?add_type=2">选择素材库图片</a>
                    <span style="color: red">*必填</span>
                </td>
            </tr>
            <tr>
                <td>头像素材库：
                    <select name="avatar_id">
                        <option value="" <?php echo $page['info']['avatar_id'] == '' ? 'selected' : ''; ?>>
                        </option>
                        <?php
                        $picGroupList = $this->toArr(MaterialPicGroup::model()->findAll());
                        foreach ($picGroupList as $key => $val) { ?>
                            <option
                                value="<?php echo $val['id']; ?>" <?php echo $page['info']['avatar_id'] == $val['id'] ? 'selected' : ''; ?>>
                                <?php echo $val['group_name']; ?>
                            </option>
                        <?php } ?>
                    </select>&nbsp;&nbsp;
                    <span style="color: red">*必选</span>
                </td>
            </tr>
            <tr>
                <td>是否分页：
                    <input type="radio" name="is_page" value="1" <?php echo ($page['info']['is_page'] == 1 ||!$page['info']['id']) ? 'checked' : ''; ?>/>否&nbsp;&nbsp;
                    <input type="radio" name="is_page"
                           value="0" <?php echo $page['info']['is_page'] == 0 ? 'checked' : ''; ?>/>是&nbsp;&nbsp;
                    <input name="page_size" style="width: 40px" value="<?php echo $page['info']['page_size'] ?>"/>楼&nbsp;&nbsp;


                </td>
            </tr>
            <?php if (!$page['info']['id']) { ?>
                <tr>
                    <td>
                        <div class="review">&nbsp;&nbsp;2楼：<span class="fewer" onclick="showHide(this)">收起</span></div>
                        <div id="review1" style="border: 1px solid black;height: 240px;width: 600px;margin: 0;padding: 0;">
                            <table style="margin: 0 20px;border: none;">
                                <tr>
                                    <td width="70px">名称</td>
                                    <td>
                                        <input hidden name="reply_to[1][]" value="-1"/>
                                        <input style="width: 200px;height:30px;font-size: large;" type="text"
                                               class="ipt" name="review_name[1][]" value=""/>
                                        <span style="color: red">*必填</span>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>回复内容</td>
                                    <td>
                                        <div style="position:relative;">
                                        <textarea style="width:360px; height: 110px" id="info_body_1"
                                                  name="review_content[1][]"></textarea>
                                            <script>
                                                $("#info_body_1").xheditor({
                                                    plugins: allplugin,
                                                    internalScript: true,
                                                    tools: "FontSize,Fontface,Bold,FontColor,Img",
                                                    skin: "nostyle"
                                                });
                                            </script>
                                        <span class="mybtn" id="downbtn_info_body">
                                            <a onclick="addWeChatSign('info_body_1')">微信号</a>&nbsp;
                                            <a onclick="addWeChatImg('info_body_1')">二维码</a>&nbsp;
                                            <a onclick="addXingXiangSign('info_body_1')">形象</a>&nbsp;
                                            <a onclick="return dialog_frame(this,500,580,false)"
                                               href="<?php echo $this->createUrl('material/addMaterialPics?id=info_body_1'); ?>">插入图片</a><br/>
                                        </span>
                                        </div>
                                    </td>
                                    <td><input type="button" class="but" id="reply" data-val="1" data-user="0"
                                               onclick="addReply(this)"
                                               value="回复"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>楼层时间</td>
                                    <td>
                                        <input style="width: 200px;height:30px;font-size: large;" type="text"
                                               class="ipt" name="review_date[1][]" value=""/>
                                        <span style="color: red">*必填</span></td>
                                    <td></td>
                                </tr>
                                <tr class="info_body_1_"></tr>

                            </table>

                        </div>
                    </td>
                </tr>
            <?php } else {
                foreach ($page['review_data'] as $key => $val) {
                    $count = count($val);
                    //第一层评论?>
                    <tr>
                        <td>
                            <div class="review">&nbsp;&nbsp;<?php echo($val[0]['floor'] + 1); ?>楼：<?php if($val[0]['floor']!=1){?><span onclick="deleteTr(this);" data-val="<?php echo ($val[0]['floor'] + 1);?>" class="deleteTr" >删除</span><?php }?><span class="fewer" onclick="showHide(this)">收起</span>
                            </div>
                            <div id="review<?php echo $val[0]['floor']; ?>"
                                 style="border: 1px solid black;height: <?php echo $count * 240; ?>px;width: 600px;margin: 0;padding: 0;">
                                <table style="margin: 0 20px;border: none;">
                                    <tr>
                                        <td width="70px">名称</td>
                                        <td>
                                            <input hidden name="reply_to[<?php echo $val[0]['floor']; ?>][]" value="-1"/>
                                            <input style="width: 200px;height:30px;font-size: large;" type="text"
                                                   class="ipt" name="review_name[<?php echo $val[0]['floor']; ?>][]"
                                                   value="<?php echo $val[0]['review_name']; ?>"/>
                                            <span style="color: red">*必填</span>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>回复内容</td>
                                        <td>
                                            <div style="position:relative;">
                                        <textarea style="width:360px; height: 110px" id="info_body_<?php echo $val[0]['floor']; ?>"
                                                  name="review_content[<?php echo $val[0]['floor']; ?>][]"><?php echo $val[0]['review_content'] ?></textarea>
                                                <script>
                                                    $("#info_body_"+<?php echo $val[0]['floor']; ?>).xheditor({
                                                        plugins: allplugin,
                                                        internalScript: true,
                                                        tools: "FontSize,Fontface,Bold,FontColor,Img",
                                                        skin: "nostyle"
                                                    });
                                                </script>
                                        <span class="mybtn" id="downbtn_info_body">
                                            <a onclick="addWeChatSign('info_body_<?php echo $val[0]['floor']; ?>')">微信号</a>&nbsp;
                                            <a onclick="addWeChatImg('info_body_<?php echo $val[0]['floor']; ?>')">二维码</a>&nbsp;
                                            <a onclick="addXingXiangSign('info_body_<?php echo $val[0]['floor']; ?>')">形象</a>&nbsp;
                                            <a onclick="return dialog_frame(this,500,580,false)"
                                               href="<?php echo $this->createUrl('material/addMaterialPics?id=info_body_' . $val[0]['floor']); ?>">插入图片</a><br/>
                                        </span>
                                            </div>
                                        </td>
                                        <td><input type="button" class="but" id="reply" data-val="<?php echo $val[0]['floor']; ?>"
                                                   data-user="0"
                                                   onclick="addReply(this)"
                                                   value="回复"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>楼层时间</td>
                                        <td>
                                            <input style="width: 200px;height:30px;font-size: large;" type="text"
                                                   class="ipt" name="review_date[<?php echo $val[0]['floor']; ?>][]"
                                                   value="<?php echo $val[0]['review_date']; ?>"/>
                                            <span style="color: red">*必填</span></td>
                                        <td></td>
                                    </tr>
                                    <?php if ($count > 1) {
                                        for($i=1;$i<$count;$i++){
                                            $floor = $i;
                                            $user = $val[$i]['reply_to'] == 0 ? '层主' : $val[$i]['reply_to'];
                                            $sign = "reply" . $val[$i]['floor'];
                                            $tag = "info_body_" . $val[$i]['floor'] . "_" . $floor;
                                            $review_id = $val[$i]['floor'] . "_" . $floor;
                                            ?>
                                            <tr class="<?php echo $review_id?>">
                                                <td colspan="3"
                                                    style="background-color: #CCCCCC;"> <?php echo $floor . "、回复" . $user; ?><span onclick="deleteTd(this);" data-floor="<?php echo $val[$i]['floor'];?>" data-val="<?php echo $floor; ?>" title="删除回复" class="deleteTd">X</span></td>
                                            </tr>
                                            <tr class="<?php echo $sign." ".$review_id; ?>">
                                                <td width="70px">
                                                    <input hidden name="reply_to[<?php echo $val[$i]['floor']; ?>][]"
                                                           value="<?php echo $val[$i]['reply_to']; ?>"/>
                                                    回复名称
                                                </td>
                                                <td>
                                                    <input style="width: 200px;height:30px;font-size: large;"
                                                           type="text" class="ipt"
                                                           name="review_name[<?php echo $val[$i]['floor']; ?>][]"
                                                           value="<?php echo $val[$i]['review_name']; ?>"/>
                                                    <span style="color: red">*必填</span>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr class="<?php echo $review_id?>">
                                                <td>
                                                    回复内容
                                                </td>
                                                <td>
                                                    <div style="position:relative;">
                                                        <textarea style="width:360px; height: 110px" id="<?php echo $tag; ?>" name="review_content[<?php echo $val[$i]['floor']; ?>][]" ><?php echo $val[$i]['review_content'];?></textarea>
                                                        <script>
                                                            $("#<?php echo $tag; ?>").xheditor({
                                                                plugins: allplugin,
                                                                internalScript: true,
                                                                tools: "FontSize,Fontface,Bold,FontColor,Img",
                                                                skin: "nostyle"
                                                            });
                                                        </script>
                                                        <span class="mybtn" id="downbtn_info_body">
                                                            <a onclick="addWeChatSign('<?php echo $tag; ?>')">微信号</a>&nbsp;
                                                            <a onclick="addWeChatImg('<?php echo $tag; ?>')">二维码</a>&nbsp;
                                                            <a onclick="addXingXiangSign('<?php echo $tag; ?>')">形象</a>&nbsp;
                                                            <a onclick="return dialog_frame(this,500,580,false)" href="<?php echo $this->createUrl('material/addMaterialPics?id=' . $tag); ?>">插入图片</a><br/>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="button" class="but" data-user="<?php echo $floor; ?>" data-val="<?php echo $val[$i]['floor']; ?>" onclick="addReply(this)" id="reply" value="回复"/></td>
                                            </tr>
                                            <tr class="<?php echo $review_id?>">
                                                <td>
                                                    楼层时间
                                                </td>
                                                <td>
                                                    <input style="width: 200px;height:30px;font-size: large;" type="text" class="ipt" name="review_date[<?php echo $val[$i]['floor']; ?>][]" value="<?php echo $val[$i]['review_date']; ?>"/>
                                                    <span style="color: red">*必填</span>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <?php
                                        }
                                    } ?>
                                    <tr class="info_body_<?php echo ($key+1); ?>_"></tr>

                                </table>

                            </div>
                        </td>
                    </tr>
                    <?php
                }
            } ?>
            <tr class="a"></tr>
            <tr></tr>
            <tr>
                <td>
                    <div id="add_review" onclick="" style="text-align: center;height:40px;line-height: 40px;"
                         class="review">+添加楼层
                    </div>
                </td>
            </tr>
            <tr>
                <td class="alignleft">
                    <input style="font-size: medium" type="submit" class="but" id="subtn" value="保存"/>&nbsp;&nbsp;&nbsp;
                    <input style="font-size: medium" type="button" class="but" value="返回"
                           onclick="window.location='<?php echo $this->createUrl('material/index'); ?>?group_id=5&p=<?php echo $_GET['p']; ?>'"/>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>
<script>
    //添加一张图片 顶部背景图和头像
    function addOneImg(url) {
            var obj =  $("#avater_img");
            $("#avater_show").attr('src',url);
            $("#avater_show").css({width:80,height:80})
        $(obj).val(url);

    }
    
    //添加素材库图片交互
    function addImg(url, sign) {
        if (!sign) sign = 'info_body';
        var html = "<p><img src='" + url + "' _xhe_src='" + url + "' /></p>";
        $("#" + sign).xheditor().pasteHTML(html);
    }
    function addWeChatSign(sign) {
        if (!sign) sign = 'info_body';
        var html = "<span>{{weixin}}</span>";
        $("#" + sign).xheditor().pasteHTML(html);
    }
    function addXingXiangSign(sign) {
        if (!sign) sign = 'info_body';
        var html = "<span>{{xingxiang}}</span>";
        $("#" + sign).xheditor().pasteHTML(html);
    }
    function addWeChatImg(sign) {
        var html = "<span>{{weixin_img}}</span>";
        $("#" + sign).xheditor().pasteHTML(html);
    }
    function showHide(n) {
        var ns = $(n).parent().next('div');
        ns.slideToggle();
        var text = $(n).text();
        var text = text == '收起' ? '编辑' : '收起';
        $(n).text(text);
    }
    function deleteTr(nowTr) {
        var floor_num = nowTr.getAttribute("data-val");
        var b = $(".review").size();
        if(floor_num<b){
            alert("不能删除中间楼层")
        }else if(floor_num==b){
             $(nowTr).parent().parent().remove();
        }else
            alert("参数错误");
    }
    function deleteTd(nowTd) {
        var floor_num = nowTd.getAttribute("data-floor");
        var review_num = nowTd.getAttribute("data-val");
        var sign = "reply" + floor_num;
        var e = floor_num+"_" + review_num;
        var b = $("."+sign).size();
        var css = "review" + floor_num;
        var width = b * 240;
        if(review_num<b){
            alert("不能删除中间评论")
        }else if(review_num==b){
            $("."+e).remove();
            $("#" + css).css({"height": width + "px" });
        }else
            alert("参数错误");
    }

    function addReply(e) {
        var floor_num = e.getAttribute("data-val");
        var data_user = e.getAttribute("data-user");
        var sign = "reply" + floor_num;
        var c = $("." + sign).size();
        var tag = "info_body_" + floor_num + "_" + (c + 1);
        var tag_str = "'" + tag + "'";
        var css = "review" + floor_num;
        var width = (c + 2) * 240;
        var review_id = floor_num+"_"+(c + 1);
        if (data_user == 0) {
            user = "层主";
        } else  user = data_user;

        var addHtml = '<tr class="'+ review_id +'">' +
            '<td  colspan="3" style="background-color: #CCCCCC;">' + (c + 1) + '、回复' + user + '<span onclick="deleteTd(this);" data-floor="'+floor_num+'" data-val="' + (c + 1) + '" title="删除回复" class="deleteTd">X</span></td></tr>' +
            '<tr class="' + sign + ' '+ review_id +'">' +
            '<td width="70px">' +
            '<input hidden name="reply_to[' + floor_num + '][]" value="' + data_user + '"  />回复名称</td><td> <input style="width: 200px;height:30px;font-size: large;" type="text" class="ipt"name="review_name[' + floor_num + '][]"value="<?php echo $page['info']['review_name[]'] ? $page['info']['review_name[]'] : ""; ?>"/> <span style="color: red">*必填</span> </td> <td></td></tr>' +
            '<tr class="'+ review_id +'"><td>回复内容</td><td>' +
            '<div style="position:relative;">' +
            '<textarea style="width:360px; height: 110px" id="' + tag + '" name="review_content[' + floor_num + '][]"></textarea> ' +
            ' <span class="mybtn" id="downbtn_info_body"> ' +
            '<a onclick="addWeChatSign(' + tag_str + ')">微信号</a>&nbsp; ' +
            '<a onclick="addWeChatImg(' + tag_str + ')">二维码</a>&nbsp; ' +
            '<a onclick="addXingXiangSign(' + tag_str + ')">形象</a>&nbsp; ' +
            '<a onclick="return dialog_frame(this,500,580,false)" href="<?php echo $this->createUrl('material/addMaterialPics'); ?>?id=' + tag + '">插入图片</a><br/>' +
            '</span> </div></td>' +
            '<td>  <input type="button" class="but" data-user="' + (c + 1) + '" data-val="' + floor_num + '" onclick="addReply(this)" id="reply" value="回复"/></td>' +
            '</tr>' +
            '<tr class="'+ review_id +'">' +
            '<td>楼层时间</td>' +
            '<td>' +
            ' <input style="width: 200px;height:30px;font-size: large;" type="text" class="ipt" ' +
            ' name="review_date[' + floor_num + '][]" ' +
            ' value="<?php echo $page['info']['review_date[]'] ? $page['info']['review_date[]'] : ""; ?>"/>  ' +
            '<span style="color: red">*必填</span></td><td></td></tr>';
        $(".info_body_" + floor_num + "_").before(addHtml);
        $("#" + css).css({"height": width + "px" });
        $("#" + tag).xheditor({
            plugins: allplugin,
            internalScript: true,
            tools: "FontSize,Fontface,Bold,FontColor,Img",
            skin: "nostyle"
        });
    }

    $(function () {
        $("#add_review").click(function () {
            var b = $(".review").size();
            var id = "info_body_" + b;
            var sign = "'info_body_" + b + "'";
            var addHtml = '<tr><td>' +
                '<div class="review">&nbsp;&nbsp;' + (b + 1) + '楼：<span onclick="deleteTr(this);" data-val="' + (b + 1) + '" class="deleteTr" >删除</span><span class="fewer" onclick="showHide(this);" >编辑</span></div> ' +
                '<div id="review' + b + '" style="border: 1px solid black;height: 240px;width: 600px;margin: 0;padding: 0;display:none">' +
                '<table style="margin: 0 20px;border: none">' +
                '<tr>' +
                '<td width="70px">名称</td>' +
                '<td>' +
                '<input hidden name="reply_to[' + b + '][]" value="-1"  />' +
                '<input style="width: 200px;height:30px;font-size: large;" type="text" class="ipt" name="review_name[' + b + '][]" ' +
                'value="<?php echo $page['info']['review_title[]'] ? $page['info']['review_title[]'] : ""; ?>"/>' +
                '<span style="color: red">*必填</span></td><td></td>' +
                '</tr><tr>' +
                '<td>回复内容</td>' +
                '<td>' +
                '<div style="position:relative;">' +
                '<textarea style="width:360px; height: 90px" id="' + id + '" name="review_content[' + b + '][]"></textarea> ' +
                ' <span class="mybtn" id="downbtn_info_body"> ' +
                '<a onclick="addWeChatSign(' + sign + ')">微信号</a>&nbsp; ' +
                '<a onclick="addWeChatImg(' + sign + ')">二维码</a>&nbsp; ' +
                '<a onclick="addXingXiangSign(' + sign + ')">形象</a>&nbsp; ' +
                '<a onclick="return dialog_frame(this,500,580,false)" href="<?php echo $this->createUrl('material/addMaterialPics'); ?>?id=' + id + '">插入图片</a><br/>' +
                '</span> </div></td>' +
                '<td>  <input type="button" class="but" id="reply" data-val="' + b + '" data-user="0"onclick="addReply(this)"value="回复"/></td>' +
                '</tr><tr>' +
                '<td>楼层时间</td>' +
                '<td>' +
                ' <input style="width: 200px;height:30px;font-size: large;" type="text" class="ipt" ' +
                ' name="review_date[' + b + '][]" ' +
                ' value="<?php echo $page['info']['review_date[]'] ? $page['info']['review_date[]'] : ""; ?>"/>  ' +
                '<span style="color: red">*必填</span></td><td></td></tr>' +
                '<tr class="info_body_' + b + '_"></tr>' +
                '</table>' +
                '</div> </td></tr>';
            $(".a").append(addHtml)
            $("#info_body_" + b).xheditor({
                plugins: allplugin,
                internalScript: true,
                tools: "FontSize,Fontface,Bold,FontColor,Img",
                skin: "nostyle"
            });
        });

    });


</script>

