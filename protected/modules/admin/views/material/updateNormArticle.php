<div class="main mbody">
    <form method="post"
          action="<?php echo $this->createUrl($page['info']['id'] ? 'material/editArticle' : 'material/addArticle'); ?>?article_type=0&p=<?php echo $_GET['p']; ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <input type="hidden" id="backurl" name="backurl" value="<?php echo $this->get('url') ?>"/>
        <input type="hidden" id="article_type" name="article_type" value="0"/>
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
                                skin: "nostyle",

                            });
                        </script>
                        <span class="downhttpimgbtn" id="downbtn_info_body">
                            <a href="#" onclick="addWeChatSign()">添加微信号标识</a>&nbsp;&nbsp;
                            <a href="#" onclick="addWeChatImg()">添加微信号图片标识</a>&nbsp;&nbsp;
                            <a href="#" onclick="addXingXiangSign()">添加形象标识</a>
                            <a href="#" onclick="addQuestionSign()">&nbsp;&nbsp;插入问卷</a><br/>
                            <a onclick="return dialog_frame(this,650,400,false)"
                                href="<?php echo $this->createUrl('material/addReceiveStyle'); ?>">插入领取人数样式</a>&nbsp;
                            <a onclick="return dialog_frame(this,500,580,false)"
                               href="<?php echo $this->createUrl('material/addMaterialPics'); ?>">插入素材库图片</a>&nbsp;&nbsp;
                            <a onclick="return dialog_frame(this,500,580,false)"
                               href="<?php echo $this->createUrl('material/addMaterialVideos'); ?>">插入素材库视频</a>
                            <br/>

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
                    <input type="hidden" id="cover_url" name="cover_url"
                           value="<?php echo $page['info']['cover_url'] ?>"/>
                    <img
                            id="cover_show" <?php if ($page['info']['cover_url']) echo "style='width: 100px;height: 100px;'"; ?>
                            src="<?php echo $page['info']['cover_url'] ?>">
                    <a class="but1" onclick="return dialog_frame(this,450,580,false)"
                       href="<?php echo $this->createUrl('material/addMaterialPics'); ?>?add_type=3">选择素材库图片</a>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 顶部图片：</span>
                </td>
                <td class="alignleft">
                    <input type="hidden" id="top_img" name="top_img" value="<?php echo $page['info']['top_img'] ?>"/>
                    <img
                            id="top_show" <?php if ($page['info']['top_img']) echo "style='width: 100px;height: 100px;'"; ?>
                            src="<?php echo $page['info']['top_img'] ?>">
                    <a class="but1" onclick="return dialog_frame(this,450,580,false)"
                       href="<?php echo $this->createUrl('material/addMaterialPics'); ?>?add_type=1">选择素材库图片</a>
                    <input type="checkbox" <?php echo $page['info']['level_tag'] == 1 ? 'checked' : ''; ?> value="1"
                           type="checkbox" name="level_tag"/>&nbsp;显示
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
                <td style="vertical-align:middle;width: 110px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 整页铺满：</span>
                </td>
                <td>
                    <input name="is_fill" <?php echo $page['info']['is_fill'] == 0 ? "checked" : ""; ?>
                           type="radio" value='0'/>否&nbsp;&nbsp;
                    <input name="is_fill" <?php echo $page['info']['is_fill'] == 1 ? "checked" : ""; ?>
                           type="radio" value='1'/>是&nbsp;&nbsp;
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;width: 110px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 隐藏标题：</span>
                </td>
                <td>
                    <input name="is_hide_title" <?php echo $page['info']['is_hide_title'] == 0 ? "checked" : ""; ?>
                            type="radio" value='0'/>否&nbsp;&nbsp;
                    <input name="is_hide_title" <?php echo $page['info']['is_hide_title'] == 1 ? "checked" : ""; ?>
                            type="radio" value='1'/>是&nbsp;&nbsp;
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;width: 80px">
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
            <tr class="sth" <?php echo $page['info']['is_vote'] == 0 ? "hidden" : '' ?>>
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
                    <input name="bottom_type" <?php echo $page['info']['bottom_type'] == 0 ? "checked" : ""; ?>
                           onclick="changebottom(this.value)" type="radio" value='0'/>标准样式&nbsp;&nbsp;
                    <input name="bottom_type" <?php echo $page['info']['bottom_type'] == 1 ? "checked" : ""; ?>
                           onclick="changebottom(this.value)" type="radio" value='1'/>头像样式&nbsp;&nbsp;
                    <input name="bottom_type" <?php echo $page['info']['bottom_type'] == 2 ? "checked" : ""; ?>
                           onclick="changebottom(this.value)" type="radio" value='2'/>无&nbsp;&nbsp;
                    <input name="bottom_type" <?php echo $page['info']['bottom_type'] == 3 ? "checked" : ""; ?>
                           onclick="changebottom(this.value)" type="radio" value='3'/>标准样式2&nbsp;&nbsp;
                    <input name="bottom_type" <?php echo $page['info']['bottom_type'] == 4 ? "checked" : ""; ?>
                           onclick="changebottom(this.value)" type="radio" value='4'/>标准样式3
                </td>
            </tr>

            <tr class="bottom_2" hidden>
                <td style="vertical-align:middle;">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 头像：</span>
                </td>
                <td class="alignleft">
                    <input type="hidden" id="avater_img" name="avater_img"
                           value="<?php echo $page['info']['avater_img'] ?>"/>
                    <img id="avater_show" <?php if ($page['info']['avater_img']) echo "style='width: 80px;height: 80px;'"; ?>
                         src="<?php echo $page['info']['avater_img'] ?>">
                    <a class="but1" onclick="return dialog_frame(this,450,580,false)"
                       href="<?php echo $this->createUrl('material/addMaterialPics'); ?>?add_type=2">选择素材库图片</a>
                    <span style="color: red">*必填</span>
                </td>
            </tr>
            <tr class="bottom_1" hidden>
                <td style="vertical-align:middle;width: 80px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 固定悬浮描述：</span>
                </td>
                <td>
                    <textarea style="font-size: small;width: 290px;height: 50px;" class="ipt" id="suspension_text"
                              name="suspension_text"><?php echo $page['info']['suspension_text']; ?> </textarea>
                    <span style="color: red">*必填</span>
                </td>
            </tr>
            <tr>
                <td>发布时间</td>
                <td>&nbsp;<input name="release_date" type="radio" value="0"  <?php if($page['info']['release_date'] == 0){echo  "checked";} ?>>&nbsp;昨天
                    &nbsp;<input name="release_date" type="radio" value="1" <?php if($page['info']['release_date'] == 1){echo  "checked";} ?>>&nbsp;今天
                   &nbsp;<input name="release_date" type="radio" value="2" <?php if($page['info']['release_date'] == 2){echo  "checked";} ?>>&nbsp;&nbsp;三天前
                  &nbsp;<input name="release_date" type="radio" value="3" <?php if(helper::checkReleaseDate($page['info']['release_date'])){echo  "checked";} ?>>&nbsp;
                    <input type="text" id="release_date" class="ipt Wdate" style="font-size: small;width: 150px;background-color: #DDDDDD;" placeholder="请选择日期" name="release_date1" value="<?php echo helper::checkReleaseDate($page['info']['release_date']) ? date("Y-m-d",$page['info']['release_date']) : ""; ?>"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
                </td>
            </tr>
            <tr class="bottom_2" hidden>
                <td style="vertical-align:middle;">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 添加好友：</span>
                </td>
                <td class="alignleft">
                    二维码&nbsp;<input name="addfans_type" type="radio" value="0" checked="checked"/>&nbsp;&nbsp;&nbsp;
                    微信号&nbsp;<input name="addfans_type" type="radio"
                                    value="1" <?php if ($page['info']['addfans_type'] == 1) echo 'checked'; ?>/>
                </td>
            </tr>
            <tr class="bottom_2" hidden>
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
                <td style="vertical-align:middle;width: 110px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 是否开启下单框：</span>
                </td>
                <td>
                    <input name="is_order" <?php echo $page['info']['is_order'] == 0 ? "checked" : ""; ?>
                           onclick="addOrder(this.value)" type="radio" value='0'/>否&nbsp;&nbsp;
                    <input name="is_order" <?php echo $page['info']['is_order'] == 1 ? "checked" : ""; ?>
                           onclick="addOrder(this.value)" type="radio" value='1'/>是&nbsp;&nbsp;
                </td>
            </tr>
            <tr class="order" <?php echo $page['info']['is_order'] == 0 ? "hidden" : '' ?>>
                <td>
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold">下单模板：</span>
                </td>
                <td>
                    <input type="text" class="ipt order_title" style="width: 199px;" name="order_title"
                           value="<?php $data = OrderTemplete::model()->findByPk($page['info']['order_id']);
                           echo $data->order_title; ?>" size="30"/>
                    <input type="hidden" class="ipt order_id" id="order_id" name="order_id"
                           value="<?php echo $page['info']['order_id']; ?>" size="30"/>
                </td>
            </tr>
            <tr class="order" <?php echo $page['info']['is_order'] == 0 ? "hidden" : '' ?>>
                <td>
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold">支付方式：</span>
                </td>
                <td>
                    <?php
                    $payment = vars::$fields['payment'];
                    $payments=helper::decbin_digit($page['info']['payment'],count($payment));

                    foreach ($payment as $key => $value) {
                        ?>
                        <input id='<?php echo $value['txt']; ?>' value='1' type='checkbox' name='payment_<?php echo $value['value'];?>' <?php echo  1==$payments[$value['value']]?'checked':''?> style='width: 20px'
                               >
                        <label for="<?php echo $value['txt']; ?>">
                            <span> <?php echo $value['txt']; ?></span>
                        </label>
                        &nbsp;&nbsp;
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 弹出聊天框时间：</span>
                </td>
                <td>
                    <input style="font-size: small;width: 50px" type="text" class="ipt" id="pop_time" name="pop_time"
                           value="<?php echo $page['info']['pop_time'] ? $page['info']['pop_time'] : ""; ?>"/>&nbsp;秒
                    &nbsp;&nbsp; （进入立即出现填0，放空或者<0表示不弹窗）
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 人物介绍：</span>
                </td>
                <td>
                    <input style="font-size: small;width: 300px" type="text" class="ipt" id="char_intro"
                           name="char_intro"
                           value="<?php echo $page['info']['char_intro'] ? $page['info']['char_intro'] : ""; ?>"/>
                    <span style="color: red">*最多8个字</span>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 聊天内容：</span>
                </td>
                <td>
                    <input style="font-size: small;width: 300px" type="text" class="ipt" id="chat_content"
                           name="chat_content"
                           value="<?php echo $page['info']['chat_content'] ? $page['info']['chat_content'] : ""; ?>"/>
                    <span style="color: red">最多16个字</span>
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="alignleft">
                    <input style="font-size: medium" type="submit" class="but" id="subtn" value="保存"/>&nbsp;&nbsp;&nbsp;
                    <?php $this->check_u_menu(array('code' => '<input value="另存为" style="font-size: medium" type="button" class="but" onclick="return save_as(this,300,150,false)" href="' . $this->createUrl('material/saveAs') . '"/>', 'auth_tag' => 'material_saveAs')); ?>
                    &nbsp;&nbsp;&nbsp;
                    <input style="font-size: medium" type="button" class="but" value="返回"
                           onclick="window.location='<?php echo $this->get('url'); ?>'"/>
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
                            'onmouseDown="getTipsValue(this);"   style="display:block;font-size:12px;padding:2px 5px;">' + reponse.data.list[i].review_title + '(' + reponse.data.list[i].reviewType + ')</a>';
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

    $(document).ready(function () {
        $('.order_title').on('keyup focus', function () {
            $('.searchsBox').show();
            var myInput = $(this);
            var key = myInput.val();
            var postdata = {search_type: 'keys', search_txt: key};
            console.log(postdata);
            $.getJSON('<?php echo $this->createUrl('orderTemplete/getOrderTempletes') ?>?jsoncallback=?', postdata, function (reponse) {
                try {
                    if (reponse.state < 1) {
                        alert(reponse.msg);
                        return false;
                    }
                    var html = '';
                    console.log(reponse);
                    for (var i = 0; i < reponse.data.list.length; i++) {
                        html += '<a href="javascript:void(0);" data-id="' + reponse.data.list[i].id + '" ' +
                            'data-orderTitle="' + reponse.data.list[i].order_title + '"  ' +
                            'onmouseDown="getOrderValue(this);"   style="display:block;font-size:12px;padding:2px 5px;">' + reponse.data.list[i].order_title + '</a>';
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

    function getOrderValue(ele) {
        var myobj = $(ele);
        var id = myobj.attr('data-id');
        var order_title = myobj.attr('data-orderTitle');
        $('.order_id').val(id);
        $('.order_title').val(order_title);
    }

    //添加素材库图片交互
    function addImg(url, is_jump) {
        if (is_jump == 1) {
            var html = "<p><img class='lazy img2wx jump' src='" + url + "'  _xhe_src='" + url + "' /></p>";
        } else {
            var html = "<p><img class='lazy' src='" + url + "'  _xhe_src='" + url + "' /></p>";
        }
        $("#info_body").xheditor().pasteHTML(html);
//        var cover = $("#cover");
//        if (cover.val() == '') {
//            cover.val(url);
//        }
    }
    function addText(text) {
        $("#info_body").xheditor().pasteHTML(text);
    }
    //添加一张图片 顶部背景图和头像
    function addOneImg(url, type) {
        if (type == 1) {
            var obj = $("#top_img");
            $("#top_show").attr('src', url);
            $("#top_show").css({width: 160, height: 50})
        } else if (type == 2) {
            var obj = $("#avater_img");
            $("#avater_show").attr('src', url);
            $("#avater_show").css({width: 80, height: 80})
        } else if (type == 3) {
            var obj = $("#cover_url");
            $("#cover_show").attr('src', url);
            $("#cover_show").css({width: 80, height: 80})
        }
        $(obj).val(url);

    }

    function addSth(value) {
        if (value == 0) {
            $(".sth").hide();
        } else if (value == 1) {
            $(".sth").show();
        }
    }

    function addOrder(value) {
        if (value == 0) {
            $(".order").hide();
        } else if (value == 1) {
            $(".order").show();
        }
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

    function addQuestionSign() {
        var html = "{{question}}";
        $("#info_body").xheditor().pasteHTML(html);
    }

    //添加视频
    function addOneVideo(url) {
        var html = "<p  style='margin:5%;text-align: center;'><video style='max-width: 90%' src='" + url + "' _xhe_src='" + url + "' controls></p><br/>";
        $("#info_body").xheditor().pasteHTML(html);

    }

    //切换底部类型
    function changebottom(value) {
        if (value == 0) {
            $(".bottom_1").show();
            $(".bottom_2").hide();
        } else if (value == 1) {
            $(".bottom_1").show();
            $(".bottom_2").show();
        }else if (value == 3) {
            $(".bottom_1").show();
            $(".bottom_2").hide();
        }else if(value == 4) {
            $(".bottom_1").show();
            $(".bottom_2").hide();
        } else {
            $(".bottom_1").hide();
            $(".bottom_2").hide();
        }
    }


    window.onload = function () {
        var radio = document.getElementsByName("bottom_type");
        for (i = 0; i < radio.length; i++) {
            if (radio[i].checked) {
                val = radio[i].value;
            }
        }

        if (val == 0) {
            $(".bottom_1").show();
            $(".bottom_2").hide();
        } else if (val == 1) {
            $(".bottom_1").show();
            $(".bottom_2").show();
        } else if (val == 3) {
            $(".bottom_1").show();
            $(".bottom_2").hide();
        } else if(val == 4) {
            $(".bottom_1").show();
            $(".bottom_2").hide();
        }else {
            $(".bottom_1").hide();
            $(".bottom_2").hide();
        }
    }

    $("#release_date").click(function () {
        $(" input[type:radio][name=release_date][value=3]").attr('checked',true);
    })
    $(" input[type:radio][name=release_date]").click(function () {
        $("#release_date").attr('value','');
    })
</script>

