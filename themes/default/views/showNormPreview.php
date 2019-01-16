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
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telphone=no, email=no">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Cache-Control" content="no-siteapp">
    <meta name="HandheldFriendly" content="true">
    <meta name="MobileOptimized" content="320">
    <meta name="screen-orientation" content="portrait">
    <meta name="x5-orientation" content="portrait">
    <meta name="full-screen" content="yes">
    <meta name="x5-fullscreen" content="true">
    <meta name="browsermode" content="application">
    <meta name="x5-page-mode" content="app">
    <meta name="msapplication-tap-highlight" content="no">
    <link media="all" href="<?php echo $page['info']['css_cdn_url']; ?>/static/front/css/norm-index.min.css?201807101115" type="text/css" rel="stylesheet">
    <link media="all" href="<?php echo $page['info']['css_cdn_url']; ?>/static/front/css/swiper.css?201811151115" type="text/css" rel="stylesheet">
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/jquery.min-1.11.3.js"></script>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/lib/ZeroClipboard/clipboard.min.js"></script>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/norm-preview.min.js?201807311540"></script>
    <?php if ($page['info']['is_lazy'] == 1) { ?>
        <script src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/jquery.lazyload.min.js?175"></script>
    <?php } ?>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/winpop.min.js?175"></script>

    <title>素材预览</title>
</head>

<body style="-webkit-overflow-scrolling:touch;">
<?php
$psq_flag = $page['info']['psq_id'] == 0 ? 0 : 1;
if ($psq_flag == 1) {
    $psqList = Questionnaire::model()->getPSQListByPk($page['info']['psq_id']);
    $psqNum = count($psqList);
}
?>

<?php if ($page['info']['level_tag'] == 1) { ?>
    <div class="top_img" style="margin: 0 auto">
        <img style="width: 100%;height: 100px;" src="<?php echo $page['info']['cdn_url'] . $page['info']['top_img'] ?>">
    </div>
<?php } ?>
<!--服务-->

<div class="rich_media <?php echo 0 == $page['info']['is_fill'] ? '' : 'full_screen' ?>" id="main" style="display:none;">
    <input id="addfans_type" value="<?php echo $page['info']['addfans_type']; ?>" hidden>
    <?php if(0==$page['info']['is_hide_title']){ ?>
        <h2 class="rich_media_title" id="activity-name"></h2>
        <div class="detail_attr">
            <?php  switch ($page['info']['release_date']){
                case 0:
                    echo "昨天";
                    break;
                case 1:
                    echo "今天";
                    break;
                case 2:
                    echo "三天前";
                    break;
                default:
                    echo date('Y-m-d',$page['info']['release_date']);;
            }
            ?>
        </div>
    <?php }?>
    <?php
    if ($page['info']['order']['is_suspend'] == 0) {
        if ($page['info']['bottom_type'] == 0) { ?>
            <div class="top_tip">
                <div class="to_pc" style="max-width: 60%">
                    &nbsp;<img src="<?php echo $page['info']['cdn_url']; ?>/static/img/defaultQR.gif"
                               style="max-height: 75px;">
                </div>
                <div style="float: left;width: auto;max-width: 40%;padding-left: 10px">
                    <p style="font-size:15px;margin-top:5px !important;padding-left: 5px;font-weight: bold; ">微信号：<span style="background: red;color: white;font-size: 18px;" class="wx_name" id="weixin1"></span></p>
                    <p class="suspension_text"
                       style="font-weight:bold;padding-right:5px !important;font-size:14px;bottom: auto !important;margin-top: 8px !important;"></p>
                </div>
            </div>

        <?php } elseif ($page['info']['bottom_type'] == 1) { ?>
            <div class="top_tip1">
                <img src="<?php echo $page['info']['cdn_url'] . $page['info']['avater_img'] ?>" class="bottom_img"
                     style="margin-left: 4%;margin-top: 7px">
                <a href="javascript:void(0);" class="bottom_btn"><img
                            src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/add_btn.png"></a>
                <span class="suspension_text1" style='bottom:30px;font-size: 15px;'><?php echo $page['info']['suspension_text'] ?></span>
            </div>

            <div class="top_tip2">
            </div>

        <?php }elseif ($page['info']['bottom_type'] == 3){?>
            <div class="top_tip">
                <div class="to_pc">
                    &nbsp;<img src="<?php echo $page['info']['cdn_url']; ?>/static/img/weixin.png"
                               style="max-height: 75px;">
                </div>
                <div style="padding-top:4px;">
                    <b class="wx_account">
                        <div>
                            <b style="font-size:15px;padding-left: 5px;color: black;">微信号：</b>
                            <b style="background: red;color: white;font-size: 18px;" id="weixin1"></b><br>
                            <div style="margin-top: 10px;"></div>
                            <b style="font-size:15px;color: black;white-space:nowrap;"><?php echo $page['info']['suspension_text']; ?></b>
                            <a href="javascript:void(0);" class="bottom_btn2"><img onclick="window.location.href='weixin://'"
                                        src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/add_friend.png"></a>
                        </div>
                    </b>
                </div>
            </div>
       <?php } elseif ($page['info']['bottom_type'] == 4) { ?>
            <div class="top_tip">
                <img class="to_pc" src="<?php echo $page['info']['cdn_url']; ?>/static/img/weixin.png"
                     style="max-height: 75px;float: left;margin-left: 5px">
                <div style="padding-top:4px;">
                    <b class="wx_account" >
                        <div style="margin-top: 13px;">
                            <b style="font-size:15px;padding-left: 5px;color: black;">微信号：</b>
                            <b style="background: red;color: white;font-size: 18px;" id="weixin1"></b><br>
                            <b style="font-size:15px;color: black;white-space:nowrap;padding-left: 5px;;"><?php echo $page['info']['suspension_text']; ?></b>
                        </div>
                    </b>
                </div>
            </div>
    <?php
        }
    } ?>
    <div class="" id="js_content">
    </div>
    <?php
    $r_flag = $page['info']['review_id'] == 0 ? 0 : 1;
    $r_type = 0;
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

            <div class="" id="review" style="padding-top: 20px">
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
        <?php
    } ?>

    <?php if ($page['info']['pop_time'] >= 0) { ?>
        <!--服务-->
        <input hidden id="pop_time" value="<?php echo $page['info']['pop_time']; ?>"/>
        <div class="finish-task-layer finish-task-success" style="display:none;">
            <div class="finish-task-toast" style="top: 12rem;">
                <div class="close"></div>
                <img class="user-imgs" src="<?php echo $page['info']['cdn_url'] . $page['info']['avater_img'] ?>">
                <p class="fx-tip"><?php echo helper::cut_str($page['info']['char_intro'], 16); ?></p>
                <p class="fx-greeting"><?php echo helper::cut_str($page['info']['chat_content'], 30); ?></p>
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
        <div class="open-wechat-toast" style="top: 5.5rem;">
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
                <h3 style="text-align: center;font-size: 1.8rem;padding: 12% 0 0;margin: 28px 0 0 -17px;"><?php echo $page['info']['addfans_text'] ?></h3>
                <div>
                    <img src="<?php echo $page['info']['cdn_url']; ?>/static/img/defaultQR.gif"
                         style="width: 90%; ">
                </div>
            </div>
            <div class="close_Mask"></div>
        </div>
    </div>
    <?php
    //    require(dirname(__FILE__) . "/free_order.php");
    if ($page['info']['is_order'] == 1 && $page['info']['order_id'] != 0) {
        if ($page['info']['order']['goods_templete'] == 0) {
            require(dirname(__FILE__) . "/order.php");
        } elseif ($page['info']['order']['goods_templete'] == 1) {
            require(dirname(__FILE__) . "/order_templete_one.php");
        }elseif ($page['info']['order']['goods_templete'] == 2) {
            require(dirname(__FILE__) . "/free_order.php");
        }
//        require(dirname(__FILE__) . "/free_order.php");
    }
    ?>
    <div style="height: 60px"></div>
</div>


<!--问卷-->
<?php if ($psq_flag == 1) require(dirname(__FILE__) . "/swiper.php"); ?>

<script>
    var pageShowDetail = {
        info:<?php echo json_encode(array('title' => $page['info']['article_title'], 'content' => $page['info']['content'], 'tag' => $page['info']['tag'], 'suspension_text' => $page['info']['suspension_text']))?>,
        weixinList:<?php echo json_encode($page['weixinList']); ?>,
        showWeixin: function () {
            var t = this;
            var e = $("#selectReview");
            var mycontent = '';
            var weixin_name = '预览微信号';
            var xingxiang = '<?php echo $page['info']['xingxiang']?>';
            var weixin_img = '/assets/img/defaultQR.gif';

            mycontent = t.info.content;
            mycontent1 = e.html();
            mycontent = mycontent.replace(/\{\{weixin\}\}/g, weixin_name);
            mycontent1 = mycontent1.replace(/\{\{weixin\}\}/g, weixin_name);
            mycontent = mycontent.replace(/\{\{xingxiang\}\}/g, xingxiang);
            mycontent1 = mycontent1.replace(/\{\{xingxiang\}\}/g, xingxiang);
            mycontent = mycontent.replace(/\{\{weixin_img\}\}/g, '<img  src="' + weixin_img + '" />');
            mycontent1 = mycontent1.replace(/\{\{weixin_img\}\}/g, '<img  src="' + weixin_img + '" />');
            mycontent = mycontent.replace(/\{\{question\}\}/g, '<div id="swiper"></div>');
            mycontent1 = mycontent1.replace(/\{\{question\}\}/g, '<div class="swiper"></div>');
            $('#js_content').html(mycontent);
            e.html(mycontent1);
            $('#activity-name').html(t.info.title);
            $('#weixin1').append(weixin_name);
            $('#wechat_id').val(weixin_name);//下单信息
            $('.open-wechat-number').html(weixin_name);
            $('.btn').attr('data-clipboard-text', weixin_name);

            //console.log(mycontent);
            $('.suspension_text').append(t.info.suspension_text);
            document.title = t.info.tag;
            $(".rich_media").fadeIn(500);
            var container = $("#swiper_change").html();
            $("#swiper").html(container);
        },
        selectfrom: function (lowValue, highValue) {
            var choice = highValue - lowValue + 1;
            return Math.floor(Math.random() * choice + lowValue);
        }

    };
    $(function () {
        pageShowDetail.showWeixin();
        var aImgs = document.getElementsByTagName('img');
        for (var i = 0; l = aImgs.length, i < l; i++) {
            if (aImgs[i].getAttribute("class") == 'lazy') {
                <?php if($page['info']['is_lazy'] == 1){ ?>
                if (aImgs[i].getAttribute('data-original').indexOf('http') == -1) {
                    aImgs[i].setAttribute('data-original', <?php echo '"' . $page['info']['cdn_url'] . '"';?>+aImgs[i].getAttribute('data-original'));
                } else {
                    aImgs[i].setAttribute('data-original', aImgs[i].getAttribute('data-original'));
                }
                <?php }else{?>
                if (aImgs[i].getAttribute('data-original').indexOf('http') == -1) {
                    aImgs[i].setAttribute('src', <?php echo '"' . $page['info']['cdn_url'] . '"';?>+aImgs[i].getAttribute('data-original'));
                } else {
                    aImgs[i].setAttribute('src', aImgs[i].getAttribute('data-original'));
                }
                <?php }?>
            } else if (aImgs[i].getAttribute('src').indexOf('http') == -1) {
                aImgs[i].src = <?php echo '"' . $page['info']['cdn_url'] . '"';?>+aImgs[i].getAttribute('src');
            }

        }
        var aVideos = document.getElementsByTagName('video');
        for (var i = 0; l = aVideos.length, i < l; i++) {
            if (aVideos[i].getAttribute("class") == 'lazy') {
                <?php if($page['info']['is_lazy'] == 1){ ?>
                if (aVideos[i].getAttribute('data-original').indexOf('http') == -1) {
                    aVideos[i].setAttribute('data-original', <?php echo '"' . $page['info']['cdn_url'] . '"';?>+aVideos[i].getAttribute('data-original'));
                } else {
                    aVideos[i].setAttribute('data-original', aVideos[i].getAttribute('data-original'));
                }
                <?php }else{?>
                if (aVideos[i].getAttribute('data-original').indexOf('http') == -1) {
                    aVideos[i].setAttribute('src', <?php echo '"' . $page['info']['cdn_url'] . '"';?>+aVideos[i].getAttribute('data-original'));
                } else {
                    aVideos[i].setAttribute('src', aVideos[i].getAttribute('data-original'));
                }
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
<script src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/audio-preview.min.js?201807311540"></script>
<script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/swiper-3.4.1.min.js"></script>

</body>
</html>