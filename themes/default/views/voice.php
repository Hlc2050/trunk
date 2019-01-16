<!DOCTYPE html>
<html>
<head>
    <?php ob_flush();
    flush(); ?>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <link media="all" href="<?php echo $page['info']['css_cdn_url']; ?>/static/front/css/index.min.css?201807231514" type="text/css" rel="stylesheet">
    <script src="<?php echo $page['info']['css_cdn_url']; ?>/static/lib/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/jquery.min-1.11.3.js"></script>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/swiper-3.4.1.min.js"></script>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/audio-preview.min.js?201807311540"></script>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/audio.min.js?175"></script>
    <script src="<?php echo $page['info']['css_cdn_url']; ?>/static/lib/ZeroClipboard/clipboard.min.js"></script>
    <?php if ($page['info']['is_lazy'] == 1) { ?>
        <script src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/jquery.lazyload.min.js"></script>
    <?php } ?>
    <script async="" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/winpop.min.js"></script>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/nativeShare.js?v=123"></script>
    <script type="text/javascript">
        $(document).bind('mobileinit', function () {
            $.mobile.autoInitializePage = false;
        });
    </script>
    <title><?php echo $page['info']['tag']; ?></title>
</head>
<body style="-webkit-overflow-scrolling:touch;">

<?php
$f_flag = $page['info']['first_audio'] == 0 ? 0 : 1;
$s_flag = $page['info']['second_audio'] == 0 ? 0 : 1;
$v_flag = $page['info']['third_audio'] == 0 ? 0 : 1;
$psq_flag = $page['info']['psq_id'] == 0 ? 0 : 1;
$r_flag = $page['info']['review_id'] == 0 ? 0 : 1;
$r_type = $page['r_type'];

if ($f_flag == 1) $first_audio = $page['first_audio'];
if ($s_flag == 1) $second_audio = $page['second_audio'];
if ($v_flag == 1) $third_audio = $page['third_audio'];

if ($psq_flag == 1) {
    $psqList = $page['psqList'];
    $psqNum = $page['psqNum'];
}
if ($r_flag == 1) {
    $reviewDetailList = $page['reviewDetailList'];
    $reviewNum = $page['reviewNum'];
}
?>

<div class="main pr">
    <input id="addfans_type" value="<?php echo $page['info']['addfans_type']; ?>" hidden>
    <input id="wxid" name="wxid" type="text" hidden/>

    <img src="<?php echo $page['info']['cdn_url'] . $page['info']['top_img'] ?>" align="absmiddle" class="top_img">
    <div class="top_box1 clf">
        <div class="top_l jump">
            <img src="<?php echo $page['info']['cdn_url'] . $page['info']['avater_img'] ?>" class="star_head">
            <?php if ($page['info']['avater_tag'] == 1) { ?>
                <img src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/vicon.png"
                     style="position: absolute;left: 70%;width: 26px;top: 65%;">
            <?php } ?>
        </div>
        <div class="top_c">
            <p class="clf">
				<span class="fl" style="color:<?php echo $page['info']['top_color'] ?>;">
				<span class="wx-name"><?php echo $page['info']['article_title'] ?></span></span></p>
            <p class="clf">
                <span class="fl"
                      style="color:<?php echo $page['info']['top_color'] ?>;"><?php echo $page['info']['idintity'] ?></span>
                <?php if ($page['info']['level_tag'] == 1) { ?>
                    <img src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/vip.png"
                         style="width: 45px;margin: 2px 4%;">
                <?php } ?>


            </p>
            <?php if ($f_flag == 1) { ?>
                <div class="voice">
                    <audio src="<?php echo $first_audio['url'] ?>" id="top_voice">你的浏览器不支持mp3播放！</audio>
                    <img src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/voice.png" class="voice_pic">
                    <span><?php echo $first_audio['time'] ?>"</span>
                    <i></i>
                    <img src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/border.png" class="border">
                </div>
            <?php } ?>
        </div>
        <div class="top_r jump">
            <div class="top_r_box clf">
                <a href="javascript:void(0);">
                    <div class="add_top">+</div>
                    <span><?php echo $page['info']['top_text'] ?></span>
                </a>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <div class="thame_pic top" id="lazy_img">
        <div class="change clf">
            <div id="case">
                <?php if ($psq_flag == 1) { ?>
                    <input type="hidden" id="psq_num" value="<?php echo $psqNum; ?>">
                    <div id="swiper_container">
                        <div class="swiper-container swiper-container-horizontal">
                            <div class="swiper-wrapper notmove" style="transition-duration: 0ms;">
                                <div class="swiper-slide swiper-slide-active" style="width: 586px;">
                                    <img src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/test.png">
                                    <div class="first" id="first">
                                        <div class="tips"<?php if ($psqNum == 1) echo "style='height:2rem'" ?>>
                                            剩<?php $psqNum = $psqNum - 1;
                                            echo $psqNum; ?>题
                                        </div>
                                        <h3 style="color: rgb(136, 194, 11); margin-top: 0px; font-size: 1.1rem; font-weight: normal;">
                                            <?php echo $psqList[0]['vote_title'] ?></h3>
                                        <h3>1.<?php echo $psqList[0]['quest_title'] ?>？</h3>
                                        <ul class="clf">
                                            <li><?php echo $psqList[0]['tab_a'] ?></li>
                                            <li><?php echo $psqList[0]['tab_b'] ?></li>
                                            <li><?php echo $psqList[0]['tab_c'] ?></li>
                                            <li><?php echo $psqList[0]['tab_d'] ?></li>
                                            <input type="hidden" value="<?php echo $psqList[0]['id']; ?>">
                                        </ul>

                                        <?php if ($psqNum == 0) { ?>
                                            <p class="submit_btn"><a href="javascript:void(0);"
                                                                     class="xinjia Link"><span
                                                            class="fl"
                                                            style="color:#ffffff; font-size:0.9rem;">提交</span></a>
                                            </p>
                                        <?php } ?>
                                    </div>
                                </div>
                                <?php if ($psqNum != 0) { ?>
                                    <div class="swiper-slide swiper-slide-next" style="width: 586px;">
                                        <img src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/test.png">
                                        <div class="first" id="second">
                                            <div class="tips clf"><span
                                                        class="fl prev">上一题</span><?php $psqNum = $psqNum - 1;
                                                echo $psqNum == 0 ? '' : '剩' . $psqNum . '题'; ?></div>
                                            <h3 style="margin-top: 0px;">2.<?php echo $psqList[1]['quest_title'] ?>
                                                ?</h3>
                                            <ul class="clf" id="second">
                                                <li><?php echo $psqList[1]['tab_a'] ?></li>
                                                <li><?php echo $psqList[1]['tab_b'] ?></li>
                                                <li><?php echo $psqList[1]['tab_c'] ?></li>
                                                <li><?php echo $psqList[1]['tab_d'] ?></li>
                                                <input type="hidden" value="<?php echo $psqList[1]['id']; ?>">

                                            </ul>
                                            <?php if ($psqNum == 0) { ?>
                                                <p class="submit_btn"><a href="javascript:void(0);" class="xinjia Link">
                                                        <span class="fl"
                                                              style="color:#ffffff; font-size:0.9rem;">提交</span></a>
                                                </p>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php if ($psqNum != 0) { ?>
                                        <div class="swiper-slide swiper-slide-next" style="width: 586px;">
                                            <img
                                                    src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/test.png">
                                            <div class="first" id="third">
                                                <div class="tips clf"><span
                                                            class="fl prev">上一题</span><?php $psqNum = $psqNum - 1;
                                                    echo $psqNum == 0 ? '' : '剩' . $psqNum . '题'; ?></div>
                                                <h3 style="margin-top: 0px;">3.<?php echo $psqList[2]['quest_title'] ?>
                                                    ?</h3>
                                                <ul class="clf" id="third">
                                                    <li><?php echo $psqList[2]['tab_a'] ?></li>
                                                    <li><?php echo $psqList[2]['tab_b'] ?></li>
                                                    <li><?php echo $psqList[2]['tab_c'] ?></li>
                                                    <li><?php echo $psqList[2]['tab_d'] ?></li>
                                                    <input type="hidden" value="<?php echo $psqList[2]['id']; ?>">
                                                </ul>
                                                <?php if ($psqNum == 0) { ?>
                                                    <p class="submit_btn"><a href="javascript:void(0);"
                                                                             class="xinjia Link"><span
                                                                    class="fl"
                                                                    style="color:#ffffff; font-size:0.9rem;">提交</span></a>
                                                    </p>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <?php if ($psqNum != 0) { ?>
                                            <div class="swiper-slide swiper-slide-next" style="width: 586px;">
                                                <img
                                                        src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/test.png">
                                                <div class="first" id="four">
                                                    <div class="tips clf"><span
                                                                class="fl prev">上一题</span><?php $psqNum = $psqNum - 1;
                                                        echo $psqNum == 0 ? '' : '剩' . $psqNum . '题'; ?></div>
                                                    <h3 style="margin-top: 0px;">
                                                        4.<?php echo $psqList[3]['quest_title'] ?>
                                                        ？</h3>
                                                    <ul class="clf" id="four">
                                                        <li><?php echo $psqList[3]['tab_a'] ?></li>
                                                        <li><?php echo $psqList[3]['tab_b'] ?></li>
                                                        <li><?php echo $psqList[3]['tab_c'] ?></li>
                                                        <li><?php echo $psqList[3]['tab_d'] ?></li>
                                                        <input type="hidden" value="<?php echo $psqList[3]['id']; ?>">
                                                    </ul>
                                                    <?php if ($psqNum == 0) { ?>
                                                        <p class="submit_btn"><a href="javascript:void(0);"
                                                                                 class="xinjia Link"><span
                                                                        class="fl"
                                                                        style="color:#ffffff; font-size:0.9rem;">提交</span></a>
                                                        </p>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <?php if ($psqNum != 0) { ?>
                                                <div class="swiper-slide" style="width: 586px;">
                                                    <img
                                                            src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/test.png">
                                                    <div class="first" id="five">
                                                        <div class="tips clf"><span class="fl prev">上一题</span></div>
                                                        <h3 style="margin-top: 0px;">
                                                            5.<?php echo $psqList[4]['quest_title'] ?>？</h3>
                                                        <ul class="clf">
                                                            <li><?php echo $psqList[4]['tab_a'] ?></li>
                                                            <li><?php echo $psqList[4]['tab_b'] ?></li>
                                                            <li><?php echo $psqList[4]['tab_c'] ?></li>
                                                            <li><?php echo $psqList[4]['tab_d'] ?></li>
                                                            <input type="hidden"
                                                                   value="<?php echo $psqList[4]['id']; ?>">
                                                        </ul>
                                                        <p class="submit_btn"><a href="javascript:void(0);"
                                                                                 class="xinjia Link"><span
                                                                        class="fl"
                                                                        style="color:#ffffff; font-size:0.9rem;">提交</span></a>
                                                        </p>
                                                    </div>
                                                </div>
                                            <?php }
                                        }
                                    }
                                } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($s_flag == 1) { ?>
                    <div class="M_voice pr">
                        <audio src="<?php echo $second_audio['url'] ?>" id="middle_audio"></audio>
                        <img src="<?php echo $page['info']['cdn_url'] . $page['info']['avater_img'] ?>"
                             class="star_middle"
                             id="star_middle">
                        <div class="m-a">
                            <div class="m-b">
                                <img src="<?php echo $page['info']['cdn_url']; ?>/static/img/green.png"
                                     style="min-width: 120px;max-width:200px ;width:30vw;padding-top: 10%;">
                                <img src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/voice.png"
                                     class="Middle_voice">
                                <span><?php echo $second_audio['time'] ?>"</span>
                                <em></em>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div id="comment"></div>
            </div>
        </div>
    </div>


</div>
<?php if ($page['info']['bottom_type'] != 2) { ?>

    <div class="top_tip1">
        <img src="<?php echo $page['info']['cdn_url'] . $page['info']['avater_img'] ?>" class="bottom_img"
             style="margin-left: 4%;margin-top: 7px">
        <a href="javascript:void(0);" class="bottom_btn jump"><img
                    src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/add_btn.png"></a>
        <span class="suspension_text" <?php if ($v_flag == 0) echo "style='bottom:30px'"; ?>></span>
        <?php if ($v_flag == 1) { ?>
            <div class="B_voice">
                <audio src="<?php echo $third_audio['url'] ?>" id="bottom_audio"></audio>
                <img src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/voice.png" class="bottom_voice">
                <img src="<?php echo $page['info']['cdn_url']; ?>/static/img/green.png"
                     style="width:30vw;max-width: 150px;height: 40px">
                <div class="bottom_time"><a href="#"><?php echo $third_audio['time'] ?>"</a></div>
                <em></em>

            </div>
        <?php } ?>
    </div>

    <div class="top_tip">
    </div>
<?php } ?>
<div class="rich_media_content " id="js_content">
</div>
<?php
if ($r_flag == 1) {
    $info = MaterialReview::model()->findByPk($page['info']['review_id']);
    $r_type = $info->review_type;
    $reviewDetailList = Dtable::toArr(MaterialReviewDetail::model()->findAll('review_id=' . $page['info']['review_id'] . " order by id asc"));
    $reviewNum = count($reviewDetailList);
    if ($r_type == 2) {
        $numArr = array_count_values(array_column($reviewDetailList, 'floor'));
        $num = 0;
        $ret = array();
        foreach ($numArr as $key => $val) {
            $ret[] = array_slice($reviewDetailList, $num, $val);
            $num += $val;
        }

        require(dirname(__FILE__) . "/selectReview.php");
    } else {
        ?>
        <div id="selectReview"></div>
        <div class="rich_media_content" id="review" style="padding-top: 20px">
            <div class="btn_">用户评论</div>
            <div class="content_box ">
                <?php
                $r_type_name = Linkage::model()->get_name($r_type);
                if ($r_type_name == '女科') {
                    foreach ($reviewDetailList as $k => $v) { ?>
                        <div class="user-img" style="float: left"><img src="<?php echo $v['avatar_url'] ?>"></div>
                        <div class="bigg_">
                            <div class="ctn_box">
                                <h3 class="name_"><?php echo $v['review_name'] ?></h3>
                                <span class="fb_time"><?php echo $v['review_date']; ?></span>
                                <p><?php echo $v['review_content'] ?></p>
                            </div>
                        </div>
                    <?php }
                } elseif ($r_type_name == '男科') {
                    foreach ($reviewDetailList as $k => $v) { ?>
                        <div class="bigg_">
                            <div class="user-img" style="float: left"><img src="<?php echo $v['avatar_url'] ?>"></div>
                            <div style="margin-left: 60px;">

                                <div class="man_box">
                                    <h3><?php echo $v['review_name'] ?></h3>
                                    <span><?php echo $k + 1; ?>楼</span>
                                    <p><?php echo $v['review_content'] ?></p>
                                </div>
                                <div class="font_">
                                    <span class="time_"><?php echo $v['review_date']; ?></span>
                                    <a href="#"><span class="code_">回复</span></a>
                                </div>
                            </div>
                        </div>
                    <?php }
                }
                ?>
            </div>
        </div>
        <?php
    }
} else {
    ?>
    <div id="selectReview"></div>
<?php } ?>
<?php if ($page['info']['cover_url']) {?>
    <div id="qq_share" style="display:none;padding:8px;border:solid 1px #a6c790;background-color: #a6c790;color: white; border-radius:5px;z-index: 111111;width: 24%;position: fixed;left: 75%;top: 40%;font-size: 0.9rem;" onclick="share_to_weixin()">
        分享到微信
    </div>
<?php } ?>
<?php if ($page['info']['pop_time'] >= 0) { ?>
    <!--服务-->
    <input hidden id="pop_time" value="<?php echo $page['info']['pop_time'];?>"/>
    <div class="finish-task-layer finish-task-success" style="display:block;">
        <div class="finish-task-toast" style="top: 12rem;">
            <div class="close"></div>
            <img class="user-imgs" src="<?php echo $page['info']['cdn_url']. $page['info']['avater_img']; ?>">
            <p class="fx-tip"><?php echo helper::cut_str($page['info']['char_intro'],16); ?></p>
            <p class="fx-greeting"><?php echo helper::cut_str($page['info']['chat_content'],30); ?></p>
            <div class="fx-content">请输入内容</div>
            <div class="finish-task-status">
                <img src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/圆角矩形2.png">
            </div>
        </div>
        <div class="finish-task-layer-bg"></div>
    </div>
    <!--客服-->
    <div class="open-kefu-layer open-kefu-success" style="display:none;">
        <div class="open-kefu-toast" style="top: 12rem">

        </div>
    </div>
<?php } ?>
<div class="open-wechat-layer open-wechat-success" style="display: none;">
    <div class="open-wechat-toast" style="top: 13rem;">
        <div class="close-wechat" style="top: -4%;"></div>
        <p class="open-wechat-header">1.长按微信号复制&gt;2.打开微信添加好友</p>
        <div class="wechat-container">
            <p class="open-wechat-number wx_name"></p>
        </div>
        <div class="open-wechat-status">
            <img src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/openWechat.png"
                 onclick="window.location='weixin://'">
        </div>
    </div>
    <div class="finish-task-layer-bg"></div>
</div>
<div class="qq_Mask">
    <div class="pr">
        <img class="qqimg" src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/qq.png">
        <div class="Mask_txt">
            <h3 style="text-align:center;font-size:1.5rem; padding: 2rem 1rem 0px; tex"><?php echo $page['info']['addfans_text'] ?></h3>
            <div class="to_qr">
                &nbsp;
            </div>
        </div>
        <div class="close_Mask"></div>
    </div>
</div>
<div style="height: 60px"></div>

<div style="display:none">
    <?php echo $page['info']['independent_cnzz']; ?>
    <?php echo $page['info']['total_cnzz']; ?>
</div>
<div id="ip" style="display: none"><?php $ip = $_SERVER["REMOTE_ADDR"];
    echo $ip; ?></div>
<div id="vote_id" style="display: none"><?php echo $page['info']['psq_id']; ?></div>
<div id="promotion_id" style="display: none"><?php echo $page['info']['promotion_id']; ?></div>

<script>


    var pageShowDetail = {
        info:<?php echo json_encode(array('title' => $page['info']['article_title'], 'content' => $page['info']['content'], 'tag' => $page['info']['tag'], 'suspension_text' => $page['info']['suspension_text']))?>,
        weixinList:<?php echo json_encode($page['weixinList']); ?>,
        showWeixin: function () {
            var t = this;
            var e = $("#selectReview");
            var mycontent = '';
            var weixin = t.getCacheWeixin();
            var weixin_name = weixin.weixin_name;
            var weixin_id = weixin.weixin_id;
            var weixin_img = weixin.weixin_img;
            var img_width = weixin.img_width;
            var img_height = weixin.img_height;
            var land_url = weixin.land_url;
            var xingxiang = '<?php echo $page['info']['xingxiang']?>';
            
            mycontent = t.info.content;
            mycontent1 = e.html();
            mycontent = mycontent.replace(/\{\{weixin\}\}/g, '<span class="wx_name">' + weixin_name + '</span>');
            mycontent1 = mycontent1.replace(/\{\{weixin\}\}/g, '<span class="wx_name">' + weixin_name + '</span>');
            mycontent = mycontent.replace(/\{\{xingxiang\}\}/g, xingxiang);
            mycontent1 = mycontent1.replace(/\{\{xingxiang\}\}/g, xingxiang);
            mycontent = mycontent.replace(/\{\{weixin_img\}\}/g, '<img class="qrcode_img"  src="' + weixin_img + '" />');
            mycontent1 = mycontent1.replace(/\{\{weixin_img\}\}/g, '<img class="qrcode_img"  src="' + weixin_img + '" />');
            $('#js_content').html(mycontent);
            e.html(mycontent1);
            $('#activity-name').html(t.info.title);
            $('.open-wechat-number').html('<span class="wx_name">' + weixin_name + '</span>');
            $('.btn').attr('data-clipboard-text', weixin_name);
            $('#wxid').val(weixin_id);
            $('.jump').attr("data-url", land_url);
            $('.suspension_text').append(t.info.suspension_text);
            if (Math.abs(img_width - img_height) < 10) {
                $('.to_qr').html('<img class="qrcode_img"  src="' + weixin_img + '" style="width: 60%;    margin: -10px 17%;padding-top: 5%;max-width:450px;max-height: :500px;"/>');
            } else {
                $('.to_qr').html('<img class="qrcode_img"  src="' + weixin_img + '" style="width: 90%;    margin: -10px 3%;padding-top: 5%;max-width:450px;max-height: :500px;"/>');
            }
            document.title = t.info.tag;
            $(".rich_media").fadeIn(1000);
        },
        selectfrom: function (lowValue, highValue) {
            var choice = highValue - lowValue + 1;
            return Math.floor(Math.random() * choice + lowValue);
        },

        getCacheWeixin: function () {
            if ($.cookie('coodata')) {
                try {
                    var coodata = eval('(' + $.cookie('coodata') + ')');
                    if (JSON.stringify(coodata.weixin_list) === JSON.stringify(this.weixinList)) {
                        //console.log(coodata);
                        if (coodata.weixin_name) {
                            return coodata;
                        }
                    }
                } catch (e) {
                    console.log(e.message);
                }
            }
            var t = this;
            var which = 0;
            var a = t.weixinList.length;
            if (a > 1) {
                which = t.selectfrom(0, a - 1);
            }
            var weixin = t.weixinList[which];
            var weixin_name = weixin.weixin_name;
            var weixin_id = weixin.id;
            var weixin_img = weixin.weixin_img;
            var img_width = weixin.img_width;
            var img_height = weixin.img_height;
            var land_url = weixin.land_url;
            var weixin_list = t.weixinList;
            var coodata = {
                weixin_name: weixin_name,
                weixin_img: weixin_img,
                weixin_id: weixin_id,
                img_width: img_width,
                img_height: img_height,
                land_url: land_url,
                weixin_list: weixin_list
            };
            $.cookie('coodata', JSON.stringify(coodata), {expires: 200});
            return coodata;

        }

    };
    $(function () {
        pageShowDetail.showWeixin();
        var aImgs = document.getElementsByTagName('img');
        for (var i = 0; l = aImgs.length, i < l; i++) {
            if (aImgs[i].getAttribute("class") == 'lazy') {
                <?php if($page['info']['is_lazy'] == 1){ ?>
                aImgs[i].setAttribute('data-original', <?php echo '"' . $page['info']['cdn_url'] . '"';?>+aImgs[i].getAttribute('data-original'));
                <?php }else{?>
                aImgs[i].setAttribute('src', <?php echo '"' . $page['info']['cdn_url'] . '"';?>+aImgs[i].getAttribute('data-original'));
                <?php }?>                } else if (aImgs[i].getAttribute('src').indexOf('http') == -1) {
                aImgs[i].src = <?php echo '"' . $page['info']['cdn_url'] . '"';?>+aImgs[i].getAttribute('src');
            }

        }
        var aAudios = document.getElementsByTagName('audio');
        for (var i = 0; l = aAudios.length, i < l; i++) {
            if (aAudios[i].getAttribute('src').indexOf('http') == -1) {
                aAudios[i].src = <?php echo '"' . $page['info']['cdn_url'] . '"';?>+aAudios[i].getAttribute('src');
            }
        }
        var aVideos = document.getElementsByTagName('video');
        for (var i = 0; l = aVideos.length, i < l; i++) {
            if (aVideos[i].getAttribute("class") == 'lazy') {
                <?php if($page['info']['is_lazy'] == 1){ ?>
                aVideos[i].setAttribute('data-original', <?php echo '"' . $page['info']['cdn_url'] . '"';?>+aVideos[i].getAttribute('data-original'));
                <?php }else{?>
                aVideos[i].setAttribute('src', <?php echo '"' . $page['info']['cdn_url'] . '"';?>+aVideos[i].getAttribute('data-original'));
                <?php }?>
            } else if (aVideos[i].getAttribute('src').indexOf('http') == -1) {
                aVideos[i].src = <?php echo '"' . $page['info']['cdn_url'] . '"';?>+aVideos[i].getAttribute('src');
            }
        }
        <?php if($page['info']['is_lazy'] == 1){ ?>
        $("img.lazy").lazyload({
            placeholder: <?php echo '"' . $page['info']['cdn_url'] . '"';?>+"/uploadfile/materialImgs/default_img.gif",
            effect: "fadeIn",
            threshold: 500
        })
        <?php }?>
    })

</script>

<?php require(dirname(__FILE__) . "/common.php");?>
</body>
</html>