<!DOCTYPE html>
<html lang="en" class="ui-mobile">
<head>
    <?php ob_flush();
    flush(); ?>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, minimal-ui">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta name="format-detection" content="telphone=no">
    <meta name="renderer" content="webkit">
    <meta name="screen-orientation" content="portrait">
    <meta name="x5-orientation" content="portrait">
    <meta name="full-screen" content="yes">
    <meta name="x5-fullscreen" content="true">
    <meta name="browsermode" content="application">
    <meta name="”renderer”" content="”webkit|ie-comp|ie-stand”">
    <link rel="stylesheet" href="<?php echo $page['info']['css_cdn_url']; ?>/static/front/css/reset.min.css?175">
    <link media="all" href="<?php echo $page['info']['css_cdn_url']; ?>/static/front/css/index.min.css?201807101115" type="text/css" rel="stylesheet">

    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/jquery.min-1.11.3.js"></script>
    <script src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/audio-preview.min.js?201807311540"></script>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/swiper-3.4.1.min.js"></script>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/winpop.min.js"></script>
    <script src="<?php echo $page['info']['css_cdn_url']; ?>/static/lib/ZeroClipboard/clipboard.min.js"></script>
    <?php if ($page['info']['is_lazy'] == 1) { ?>
        <script src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/jquery.lazyload.min.js"></script>
    <?php } ?>
    <script type="text/javascript">
        $(document).bind('mobileinit', function () {
            $.mobile.autoInitializePage = false;
        });
    </script>


<body style="-webkit-overflow-scrolling:touch;">

<?php
$f_flag = $page['info']['first_audio'] == 0 ? 0 : 1;
$s_flag = $page['info']['second_audio'] == 0 ? 0 : 1;
$v_flag = $page['info']['third_audio'] == 0 ? 0 : 1;
$psq_flag = $page['info']['psq_id'] == 0 ? 0 : 1;
$r_flag = $page['info']['review_id'] == 0 ? 0 : 1;
$r_type = MaterialReview::model()->findByPk($page['info']['review_id'])->review_type;

if ($f_flag == 1) $first_audio = MaterialAudio::model()->getUrlByPk($page['info']['first_audio']);
if ($s_flag == 1) $second_audio = MaterialAudio::model()->getUrlByPk($page['info']['second_audio']);
if ($v_flag == 1) $third_audio = MaterialAudio::model()->getUrlByPk($page['info']['third_audio']);
if ($psq_flag == 1) {
    $psqList = Questionnaire::model()->getPSQListByPk($page['info']['psq_id']);
    $psqNum = count($psqList);
}
?>

<div class="main pr">
    <input id="addfans_type" value="<?php echo $page['info']['addfans_type']; ?>" hidden>

    <img src="<?php echo $page['info']['cdn_url'] . $page['info']['top_img'] ?>" align="absmiddle" class="top_img">
    <div class="top_box1 clf">
        <div class="top_l">
            <img src="<?php echo $page['info']['cdn_url'] . $page['info']['avater_img'] ?>" class="star_head">
            <?php if ($page['info']['avater_tag'] == 1) { ?>
                <img src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/vicon.png"
                     style="position: absolute;left: 70%;width: 26px;top: 65%;">
            <?php } ?>
        </div>
        <div class="top_c">
            <p class="clf"><span class="fl" style="color:<?php echo $page['info']['top_color'] ?>;"><span
                        class="wx-name"><?php echo $page['info']['article_title'] ?></span></span></p>
            <p class="clf"><span class="fl"
                                 style="color:<?php echo $page['info']['top_color'] ?>;"><?php echo $page['info']['idintity'] ?></span>
                <?php if ($page['info']['level_tag'] == 1) { ?>
                    <img src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/vip.png"
                         style="width: 45px;margin: 2px 4%;">
                <?php } ?>
            </p>

            <?php if ($f_flag == 1) { ?>
                <div class="voice">
                    <audio src="<?php echo $page['info']['cdn_url'] . $first_audio['url'] ?>" id="top_voice">
                        你的浏览器不支持mp3播放！
                    </audio>
                    <img src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/voice.png" class="voice_pic">
                    <span><?php echo $first_audio['time'] ?>"</span>
                    <i></i>
                    <img src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/border.png" class="border">
                </div>
            <?php } ?>
        </div>
        <div class="top_r">
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
                                        </ul>
                                        <?php if ($psqNum == 0) { ?>
                                            <p class="submit_btn"><a href="javascript:void(0);" class="xinjia Link"><span class="fl"
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

                                            </ul>
                                            <?php if ($psqNum == 0) { ?>
                                                <p class="submit_btn"><a href="javascript:void(0);" class="xinjia Link"><span class="fl"
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
                                                </ul>
                                                <?php if ($psqNum == 0) { ?>
                                                    <p class="submit_btn"><a href="javascript:void(0);" class="xinjia Link"><span
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

                                                    </ul>
                                                    <?php if ($psqNum == 0) { ?>
                                                        <p class="submit_btn"><a href="javascript:void(0);" class="xinjia Link"><span
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
                                                        </ul>
                                                        <p><a href="javascript:void(0);" class="xinjia Link"><span
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
                        <audio src="<?php echo $page['info']['cdn_url'] . $second_audio['url'] ?>"
                               id="middle_audio"></audio>
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
        <a href="javascript:void(0);" class="bottom_btn"><img
                src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/add_btn.png"></a>
        <span class="suspension_text" <?php if ($v_flag == 0) echo "style='bottom:30px'"; ?>></span>
        <?php if ($v_flag == 1) { ?>
            <div class="B_voice">
                <audio src="<?php echo $page['info']['cdn_url'] . $third_audio['url'] ?>" id="bottom_audio"></audio>
                <img src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/voice.png" class="bottom_voice">
                <img src="<?php echo $page['info']['cdn_url']; ?>/static/img/green.png"
                     style="width:30vw;max-width: 150px;height: 40px">
                <span style="position: absolute;top: 6px;left: 45px;"><?php echo $third_audio['time'] ?>"</span>
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

<?php if ($page['info']['pop_time'] >= 0) { ?>
    <!--服务-->
    <input hidden id="pop_time" value="<?php echo $page['info']['pop_time'];?>"/>
    <div class="finish-task-layer finish-task-success" style="display:none;">
        <div class="finish-task-toast" style="top: 12rem;">
            <div class="close"></div>
            <img class="user-imgs" src="<?php echo $page['info']['cdn_url'] . $page['info']['avater_img'] ?>">
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
                 onclick="window.location.href='weixin://'">
        </div>
    </div>
    <div class="finish-task-layer-bg"></div>
</div>


<div class="qq_Mask">
    <div class="pr">
        <img class="qqimg" src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/qq.png">
        <div class="Mask_txt">
            <h3 style="text-align:center;font-size:1.5rem; padding: 2rem 1rem 0px; "><?php echo $page['info']['addfans_text'] ?></h3>
            <div>
                <img src="<?php echo $page['info']['cdn_url']; ?>/static/img/defaultQR.gif"
                     style="width: 90%;    margin: -10px 3%;padding-top: 5%;max-width:450px;max-height: :500px;">
            </div>
        </div>
        <div class="close_Mask"></div>
    </div>
</div>
<div style="height: 60px"></div>
<script>

    var pageShowDetail = {
        info:<?php echo json_encode(array('title' => $page['info']['article_title'], 'content' => $page['info']['content'], 'tag' => $page['info']['tag'], 'suspension_text' => $page['info']['suspension_text']))?>,
        weixinList:<?php echo json_encode($page['weixinList']); ?>,
        showWeixin: function () {
            var t = this;
            var e = $("#selectReview");
            var mycontent = '';
            var weixin_name = '预览微信号';
            var weixin_img = '/assets/img/defaultQR.gif';
            var xingxiang = '<?php echo $page['info']['xingxiang']?>';
            mycontent = t.info.content;
            mycontent1 = e.html();
            mycontent = mycontent.replace(/\{\{weixin\}\}/g, weixin_name);
            mycontent1 = mycontent1.replace(/\{\{weixin\}\}/g, weixin_name);
            mycontent = mycontent.replace(/\{\{xingxiang\}\}/g, xingxiang);
            mycontent1 = mycontent1.replace(/\{\{xingxiang\}\}/g, xingxiang);
            mycontent = mycontent.replace(/\{\{weixin_img\}\}/g, '<img  src="' + weixin_img + '" />');
            mycontent1 = mycontent1.replace(/\{\{weixin_img\}\}/g, '<img  src="' + weixin_img + '" />');
            $('#js_content').html(mycontent);
            e.html(mycontent1);
            $('#activity-name').html(t.info.title);
            $('.open-wechat-number').html(weixin_name);
            $('.btn').attr('data-clipboard-text', weixin_name);

            $('.suspension_text').append(t.info.suspension_text);
            document.title = t.info.tag;

            $(".rich_media").fadeIn(1000);
        },

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
                <?php }?>       
            } else if (aImgs[i].getAttribute('src').indexOf('http') == -1) {
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


</body>


</html>