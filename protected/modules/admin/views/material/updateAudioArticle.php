<link href="/static/lib/color/css/main.css" rel="stylesheet" type="text/css"/>
<script src="/static/lib/color/js/script.js"></script>
<div class="main mbody">
    <form method="post"
          action="<?php echo $this->createUrl($page['info']['id'] ? 'material/editArticle' : 'material/addArticle'); ?>?article_type=1&p=<?php echo $_GET['p']; ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <input type="hidden" id="backurl" name="backurl" value="<?php echo $this->get('url') ?>"/>
        <input type="hidden" id="article_type" name="article_type" value="1"/>

        <table class="tb3">
            <tbody>
            <tr>
                <td colspan="2" style="vertical-align:middle;">
                    <span
                        style="vertical-align:middle;font-size: large;color: dimgrey;font-weight:bold"> 标题：</span><br/>
                    <input style="width: 500px;height:30px;font-size: large;" type="text" class="ipt" id="articleTitle"
                           name="articleTitle"
                           value="<?php echo $page['info']['article_title'] ? $page['info']['article_title'] : ""; ?>"/>
                    <span style="color: red">*必填</span>
                </td>
            </tr>
            <tr>
                <td><span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 所在组别：</span></td>
                <td class="alignleft">
                    <?php
                    $articleGroups = MaterialArticleGroup::model()->getArticleGroups();
                    echo CHtml::dropDownList('group_id', $page['info']['group_id'], CHtml::listData($articleGroups, 'id', 'group_name'),
                        array(
                            'empty' => '选择组别',
                        )
                    );
                    ?>
                    <span style="color: red">*必填</span>
                </td>
            </tr>
            <tr>
                <td><span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 支持人员：</span></td>
                <td class="alignleft">
                    <?php
                    $supportStafflist = SupportStaff::model()->getSupportStaffList();
                    echo CHtml::dropDownList('support_staff_id', $page['info']['support_staff_id'], CHtml::listData($supportStafflist, 'user_id', 'name'),
                        array(
                            'empty' => '选择支持人员',
                        )
                    );
                    ?>
                    <span style="color: red">*必填</span>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="position:relative;">
                    <span style="vertical-align:middle;font-size: large;color: dimgrey;font-weight:bold">
                        正文：
                    </span>

                    <br/>
                    <div style="position:relative;">
                        <textarea style="width:100%; height: 400px" id="info_body"
                                  name="info_body"><?php echo $page['info']['content'] ? $page['info']['content'] : ''; ?></textarea>
                        <script>
                            var info_body = $("#info_body").xheditor({
                                plugins: allplugin,
                                internalScript: true,
                                tools: "mfull",
                                skin: "nostyle"
                            });
                        </script>
                        <span class="downhttpimgbtn" id="downbtn_info_body">
                            <a href="#" onclick="addWeChatSign()">添加微信号标识</a>&nbsp;&nbsp;
                            <a href="#" onclick="addWeChatImg()">添加微信号图片标识</a>&nbsp;&nbsp;
                            <a href="#" onclick="addXingXiangSign()">添加形象标识</a><br/>
                            <a onclick="return dialog_frame(this,500,580,false)"
                               href="<?php echo $this->createUrl('material/addMaterialPics'); ?>">插入素材库图片</a>&nbsp;&nbsp;
                            <a onclick="return dialog_frame(this,500,580,false)"
                               href="<?php echo $this->createUrl('material/addMaterialVideos'); ?>">插入素材库视频</a><br/>
                        </span>
                        <span class="upbtn_box" id="upbtn_box">
                            <script>load_editor_upload("info_body");</script>
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 分享链接小图：</span>
                </td>
                <td class="alignleft">
                    <input type="hidden" id="cover_url" name="cover_url" value="<?php echo $page['info']['cover_url'] ?>"/>
                    <img
                            id="cover_show" <?php if ($page['info']['cover_url']) echo "style='width: 100px;height: 100px;'"; ?>
                            src="<?php echo $page['info']['cover_url'] ?>">
                    <a class="but1" onclick="return dialog_frame(this,450,580,false)"
                       href="<?php echo $this->createUrl('material/addMaterialPics'); ?>?add_type=3">选择素材库图片</a>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 顶部背景图：</span>
                </td>
                <td class="alignleft">
                    <input type="hidden" id="top_img" name="top_img" value="<?php echo $page['info']['top_img'] ?>"/>
                    <img
                        id="top_show" <?php if ($page['info']['top_img']) echo "style='width: 100px;height: 100px;'"; ?>
                        src="<?php echo $page['info']['top_img'] ?>">
                    <a class="but1" onclick="return dialog_frame(this,450,580,false)"
                       href="<?php echo $this->createUrl('material/addMaterialPics'); ?>?add_type=1">选择素材库图片</a>
                    <span style="color: red">*必填</span>
                </td>
            </tr>


            <tr>
                <td style="vertical-align:middle;">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 头像：</span>
                </td>
                <td class="alignleft">
                    <input type="hidden" id="avater_img" name="avater_img"
                           value="<?php echo $page['info']['avater_img'] ?>"/>
                    <img
                        id="avater_show" <?php if ($page['info']['avater_img']) echo "style='width: 80px;height: 80px;'"; ?>
                        src="<?php echo $page['info']['avater_img'] ?>">
                    <a class="but1" onclick="return dialog_frame(this,450,580,false)"
                       href="<?php echo $this->createUrl('material/addMaterialPics'); ?>?add_type=2">选择素材库图片</a>
                    <span style="color: red">*必填</span>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="checkbox" <?php echo $page['info']['avater_tag'] == 1 ? "checked" : ""; ?> value='1'
                           type="checkbox" name="avater_tag"/>&nbsp;头像加V标识
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 语音1：</span>
                </td>
                <td class="alignleft">
                    <?php
                    $audiolist = MaterialAudio::model()->getAudioList();
                    echo CHtml::dropDownList('first_audio', $page['info']['first_audio'], CHtml::listData($audiolist, 'id', 'audio_name'),
                        array(
                            'empty' => '语音1',
                        )
                    );
                    ?>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 语音2：</span>
                </td>
                <td class="alignleft">
                    <?php
                    $audiolist = MaterialAudio::model()->getAudioList();
                    echo CHtml::dropDownList('second_audio', $page['info']['second_audio'], CHtml::listData($audiolist, 'id', 'audio_name'),
                        array(
                            'empty' => '语音2',
                        )
                    );
                    ?>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 语音3：</span>
                </td>
                <td class="alignleft">
                    <?php
                    $audiolist = MaterialAudio::model()->getAudioList();
                    echo CHtml::dropDownList('third_audio', $page['info']['third_audio'], CHtml::listData($audiolist, 'id', 'audio_name'),
                        array(
                            'empty' => '语音3',
                        )
                    );
                    ?>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;width: 100px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 商品类型：</span>
                </td>
                <td>
                    <?php
                    $categoryList = Linkage::model()->get_linkage_data(20);
                    echo CHtml::dropDownList('cat_id', $page['info']['cat_id'], CHtml::listData($categoryList, 'linkage_id', 'linkage_name'),
                        array(
                            'empty' => '请选择',
                            'ajax' => array(
                                'type' => 'POST',
                                'url' => $this->createUrl('material/getPSQByCatId'),
                                'update' => '#psq_id',
                                'data' => array('cat_id' => 'js:$("#cat_id").val()'),
                            )
                        )
                    );
                    ?>
                    <span style="color: red">*必选</span>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;width: 100px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 问卷：</span>
                </td>
                <td>
                    <?php
                    if (!$page['info']['id']) {
                        echo CHtml::dropDownList('psq_id', $page['info']['psq_id'], array('请选择'));
                    } else {
                        echo CHtml::dropDownList('psq_id', $page['info']['psq_id'], CHtml::listData(Questionnaire::model()->getPSQByCatId($page['info']['cat_id']), 'id', 'vote_title'), array('empty' => '请选择'));
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;width: 110px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 是否开启投票页：</span>
                </td>
                <td>
                    <input name="is_vote" <?php echo $page['info']['is_vote'] == 0 ? "checked" : ""; ?>
                           onclick="addSth(this.value)" type="radio" value='0'/>否&nbsp;&nbsp;
                    <input name="is_vote" <?php echo $page['info']['is_vote'] == 1 ? "checked" : ""; ?>
                           onclick="addSth(this.value)" type="radio" value='1'/>是&nbsp;&nbsp;
                </td>
            </tr>
            <tr class="sth" <?php echo $page['info']['is_vote'] == 0?"hidden":''?>>
                <td>
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold">投票页名称：</span>
                </td>
                <td>
                    <input type="text" class="ipt vote_page" style="width: 199px;" name="vote_page"
                           value="<?php $data = Questionnaire::model()->findByPk($page['info']['vote_id']);
                           echo $data->vote_page; ?>" size="30"/>
                    <input type="hidden" class="ipt vote_id" id="vote_id" name="vote_id"
                           value="<?php echo $page['info']['vote_id']; ?>" size="30"/>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;width: 100px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 评论：</span>
                </td>
                <td>
                    <input type="text" class="ipt review_title" style="width: 199px;" name="review_title"
                           value="<?php $data = MaterialReview::model()->findByPk($page['info']['review_id']);
                           echo $data->review_title; ?>" size="30"/>
                    <input type="hidden" class="ipt review_id" id="review_id" name="review_id"
                           value="<?php echo $page['info']['review_id']; ?>" size="30"/>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;width: 100px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 顶部字体颜色：</span>
                </td>
                <td>
                    <div class="preview"
                         style="background-color: <?php echo $page['info']['top_color'] ? $page['info']['top_color'] : 'rgb(255, 102, 153)'; ?>">
                    </div>
                    <div class="colorpicker" style="display:none">
                        <canvas id="picker" var="1" width="200" height="200"></canvas>
                        <div class="color-controls">
                            <div><label>R</label> <input type="text" id="rVal"/></div>
                            <div><label>G</label> <input type="text" id="gVal"/></div>
                            <div><label>B</label> <input type="text" id="bVal"/></div>
                            <div><label>RGB</label> <input type="text" id="rgbVal"/></div>
                            <div><label>HEX</label> <input type="text" id="hexVal"/></div>
                        </div>
                    </div>
                </td>
            </tr>
            <input type="hidden" id="top_color" name="top_color"
                   value="<?php echo $page['info']['top_color'] ? $page['info']['top_color'] : '#FF679A' ?>"/>
            <tr>
                <td style="vertical-align:middle;">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 身份：</span>
                </td>
                <td class="alignleft">
                    <input style="font-size: small;width: 300px" type="text" class="ipt" id="tag" name="idintity"
                           value="<?php echo $page['info']['idintity'] ? $page['info']['idintity'] : ""; ?>"/>
                    <span style="color: red">*必填</span>

                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="checkbox" <?php echo $page['info']['level_tag'] == 1 ? 'checked' : ''; ?> value="1"
                           type="checkbox" name="level_tag"/>&nbsp;等级标识
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 顶部按钮描述：</span>
                </td>
                <td>
                    <input style="font-size: small;width: 300px" type="text" class="ipt" id="top_text" name="top_text"
                           value="<?php echo $page['info']['top_text'] ? $page['info']['top_text'] : ""; ?>"/>
                    <span style="color: red">*必填(六个字最佳)</span>
                </td>
            </tr>

            <tr>
                <td style="vertical-align:middle;width: 80px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 浏览器Title：</span>
                </td>
                <td>
                    <input style="font-size: small;width: 300px" type="text" class="ipt" id="tag" name="tag"
                           value="<?php echo $page['info']['tag'] ? $page['info']['tag'] : ""; ?>"/>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;width: 80px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 形象：</span>
                </td>
                <td>
                    <input class="ipt" id="xingxiang"
                           name="xingxiang" value="<?php echo $page['info']['xingxiang']; ?>">
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;width: 80px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 文案备注：</span>
                </td>
                <td>
                    <textarea style="font-size: small;width: 290px;height: 50px;" class="ipt" id="article_info"
                              name="article_info"><?php echo $page['info']['article_info']; ?></textarea>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;width: 80px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 描述语句：</span>
                </td>
                <td>
                    <textarea style="font-size: small;width: 290px;height: 50px;" class="ipt" id="descriptive_statement"
                              name="descriptive_statement"><?php echo $page['info']['descriptive_statement']; ?></textarea>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;width: 100px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 添加固底悬浮：</span>
                </td>
                <td>
                    <input name="bottom_type" checked
                           onclick="changebottom(this.value)" type="radio" value='1'/>头像样式&nbsp;&nbsp;
                    <input name="bottom_type" <?php echo $page['info']['bottom_type'] == 2 ? "checked" : ""; ?>
                           onclick="changebottom(this.value)" type="radio" value='2'/>无&nbsp;&nbsp;
                </td>
            </tr>
            <tr class="bottom_2" <?php echo $page['info']['bottom_type'] == 2 ? "hidden" : ""; ?>>
                <td style="vertical-align:middle;">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 固定悬浮描述：</span>
                </td>
                <td>
                    <input style="font-size: small;width: 300px" type="text" class="ipt" id="suspension_text"
                           name="suspension_text"
                           value="<?php echo $page['info']['suspension_text'] ? $page['info']['suspension_text'] : ""; ?>"/>
                    <span style="color: red">*必填(十个字以内为佳)</span>&nbsp;
                </td>
            </tr>
            <tr class="bottom_2" <?php echo $page['info']['bottom_type'] == 2 ? "hidden" : ""; ?> >
                <td style="vertical-align:middle;">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 添加好友：</span>
                </td>
                <td class="alignleft">
                    二维码&nbsp;<input name="addfans_type" type="radio" value="0" checked="checked"/>&nbsp;&nbsp;&nbsp;
                    微信号&nbsp;<input name="addfans_type" type="radio"
                                    value="1" <?php if ($page['info']['addfans_type'] == 1) echo 'checked'; ?>/>
                </td>
            </tr>
            <tr class="bottom_2" <?php echo $page['info']['bottom_type'] == 2 ? "hidden" : ""; ?>>
                <td style="vertical-align:middle;">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 添加好友描述：</span>
                </td>
                <td>
                    <input style="font-size: small;width: 300px" type="text" class="ipt" id="addfans_text"
                           name="addfans_text"
                           value="<?php echo $page['info']['addfans_text'] ? $page['info']['addfans_text'] : ""; ?>"/>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 弹出聊天框时间：</span>
                </td>
                <td>
                    <input style="font-size: small;width: 50px" type="text" class="ipt" id="pop_time" name="pop_time"
                           value="<?php echo $page['info']['pop_time'] ? $page['info']['pop_time'] : ""; ?>"/>&nbsp;秒 &nbsp;&nbsp; （进入立即出现填0，放空或者<0表示不弹窗）
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 人物介绍：</span>
                </td>
                <td>
                    <input style="font-size: small;width: 300px" type="text" class="ipt" id="char_intro" name="char_intro"
                           value="<?php echo $page['info']['char_intro'] ? $page['info']['char_intro'] : ""; ?>"/>
                    <span style="color: red">*最多8个字</span>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 聊天内容：</span>
                </td>
                <td>
                    <input style="font-size: small;width: 300px" type="text" class="ipt" id="chat_content" name="chat_content"
                           value="<?php echo $page['info']['chat_content'] ? $page['info']['chat_content'] : ""; ?>"/>
                    <span style="color: red">最多16个字</span>
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="alignleft">
                    <input style="font-size: medium" type="submit" class="but" id="subtn" value="保存"/>&nbsp;&nbsp;&nbsp;
                    <?php $this->check_u_menu(array('code' => '<input value="另存为" style="font-size: medium" type="button" class="but" onclick="return save_as(this,300,150,false)" href="'.$this->createUrl('material/saveAs').'"/>', 'auth_tag' => 'material_saveAs')); ?>&nbsp;&nbsp;&nbsp;
                    <input style="font-size: medium" type="button" class="but" value="返回"
                           onclick="window.location='<?php echo $this->get('url') ? $this->get('url') : $this->createUrl('material/index'); ?>'"/>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('.vote_page').on('keyup focus', function () {
            $('.searchsBox').show();
            var myInput = $(this);
            var key = myInput.val();
            var postdata = {search_type: 'keys', search_txt: key};
            $.getJSON('<?php echo $this->createUrl('material/getVotePage') ?>?jsoncallback=?', postdata, function (reponse) {
                try {
                    if (reponse.state < 1) {
                        alert(reponse.msg);
                        return false;
                    }
                    var html = '';
                    console.log(reponse);
                    for (var i = 0; i < reponse.data.list.length; i++) {
                        html += '<a href="javascript:void(0);" data-id="' + reponse.data.list[i].id + '" ' +
                            'data-votePage="' + reponse.data.list[i].vote_page + '"  ' +
                            'onmouseDown="getTipsValues(this);"   style="display:block;font-size:12px;padding:2px 5px;">' + reponse.data.list[i].vote_page + '</a>';
                    }
                    var s_height = myInput.height();
                    var top = myInput.offset().top + s_height;
                    var left = myInput.offset().left;
                    var width = myInput.width();
                    $('.searchsBox').remove();
                    $('body').append('<div class="searchsBox" style="position:absolute;top:' + top + 'px;left:' + left + 'px;background:#fff;z-index:2;border:1px solid #ccc;width:' + width + 'px;">' + html + '</div>');
                } catch (e) {
                    alert(e.message)
                }
            });
            myInput.blur(function () {
                $('.searchsBox').hide();
            })
        });
    });
    function getTipsValues(ele) {
        var myobj = $(ele);
        var id = myobj.attr('data-id');
        var vote_page = myobj.attr('data-votePage');
        $('.vote_id').val(id);
        $('.vote_page').val(vote_page);
    }
    $(document).ready(function () {
        $('.review_title').on('keyup focus', function () {
            $('.searchsBox').show();
            var myInput = $(this);
            var key = myInput.val();
            var postdata = {search_type: 'keys', search_txt: key};
            $.getJSON('<?php echo $this->createUrl('material/getReviewList') ?>?jsoncallback=?', postdata, function (reponse) {
                try {
                    if (reponse.state < 1) {
                        alert(reponse.msg);
                        return false;
                    }
                    var html = '';
                    console.log(reponse);
                    for (var i = 0; i < reponse.data.list.length; i++) {
                        html += '<a href="javascript:void(0);" data-id="' + reponse.data.list[i].id + '" ' +
                            'data-reviewName="' + reponse.data.list[i].review_title + '"  ' +
                            'onmouseDown="getTipsValue(this);"   style="display:block;font-size:12px;padding:2px 5px;">' + reponse.data.list[i].review_title +'('+ reponse.data.list[i].reviewType+')</a>';
                    }
                    var s_height = myInput.height();
                    var top = myInput.offset().top + s_height;
                    var left = myInput.offset().left;
                    var width = myInput.width();
                    $('.searchsBox').remove();
                    $('body').append('<div class="searchsBox" style="position:absolute;top:' + top + 'px;left:' + left + 'px;background:#fff;z-index:2;border:1px solid #ccc;width:' + width + 'px;">' + html + '</div>');
                } catch (e) {
                    alert(e.message)
                }
            });
            myInput.blur(function () {
                $('.searchsBox').hide();
            })
        });
    });

    function getTipsValue(ele) {
        var myobj = $(ele);
        var id = myobj.attr('data-id');
        var review_title = myobj.attr('data-reviewName');
        $('.review_id').val(id);
        $('.review_title').val(review_title);
    }

    function addSth(value) {
        if (value == 0) {
            $(".sth").hide();
        } else if (value == 1) {
            $(".sth").show();
        }
    }

    //添加素材库图片交互
    function addImg(url,is_jump) {
        if(is_jump==1) {
            var html = "<p><img class='lazy img2wx jump' src='" + url + "'  _xhe_src='" + url + "' /></p>";
        }else{
            var html = "<p><img class='lazy' src='" + url + "'  _xhe_src='" + url + "' /></p>";
        }
        $("#info_body").xheditor().pasteHTML(html);
        var cover = $("#cover");
        if (cover.val() == '') {
            cover.val(url);
        }
    }
    //添加一张图片 顶部背景图和头像
    function addOneImg(url, type) {
        if (type == 1) {
            var obj = $("#top_img");
            $("#top_show").attr('src', url);
            $("#top_show").css({width: 100, height: 100})
        } else if (type == 2) {
            var obj = $("#avater_img");
            $("#avater_show").attr('src', url);
            $("#avater_show").css({width: 80, height: 80})
        }else if(type==3){
            var obj =  $("#cover_url");
            $("#cover_show").attr('src',url);
            $("#cover_show").css({width:80,height:80})
        }
        $(obj).val(url);

    }
    function clearCover() {
        document.getElementById("cover").value = "";
    }
    function addWeChatSign() {
        var html = "<span>{{weixin}}</span>";
        $("#info_body").xheditor().pasteHTML(html);
    }
    function addXingXiangSign() {
        var html = "<span>{{xingxiang}}</span>";
        $("#info_body").xheditor().pasteHTML(html);
    }
    function addWeChatImg() {
        var html = "<span>{{weixin_img}}</span>";
        $("#info_body").xheditor().pasteHTML(html);
    }
    //添加视频
    function addOneVideo(url) {
        var html = "<p  style='margin:5%;text-align: center;'><video style='max-width: 90%' src='" + url + "' _xhe_src='" + url + "' controls></p><br/>";
        $("#info_body").xheditor().pasteHTML(html);

    }
    $("input[name='avater_tag']").click(function () {
        if ($("input[name='avater_tag']").attr("checked")) {
            console.log(5435)
        }
    })
    //切换底部类型
    function changebottom(value) {
        if (value == 1) {
            $(".bottom_2").show();
        } else {
            $(".bottom_2").hide();
        }
    }
 
</script>

