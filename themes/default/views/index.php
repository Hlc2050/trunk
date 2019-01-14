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
    <link media="all" href="<?php echo $page['info']['css_cdn_url']; ?>/static/front/css/norm-index.min.css?201807311540" type="text/css" rel="stylesheet">
    <link media="all" href="<?php echo $page['info']['css_cdn_url']; ?>/static/front/css/swiper.css?201811151115" type="text/css" rel="stylesheet">
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/jquery.min-1.11.3.js"></script>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/norm-preview.min.js?201807311540"></script>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/norm.min.js"></script>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/nativeShare.js?v=123"></script>
    <script src="<?php echo $page['info']['css_cdn_url']; ?>/static/lib/ZeroClipboard/clipboard.min.js"></script>
    <?php if ($page['info']['is_lazy'] == 1) { ?>
        <script src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/jquery.lazyload.min.js"></script>
    <?php } ?>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/winpop.min.js?175"></script>

    <title><?php echo $page['info']['tag']; ?></title>
</head>


<body style="-webkit-overflow-scrolling:touch;">
<?php
$psq_flag = $page['info']['psq_id'] == 0 ? 0 : 1;
if ($psq_flag == 1) {
    $psqList = $page['psqList'];
    $psqNum = $page['psqNum'];
}
?>

<?php if ($page['info']['level_tag'] == 1) { ?>
    <div class="top_img" style="margin: 0 auto">
        <img style="width: 100%;height: 100px;" src="<?php echo $page['info']['cdn_url'] . $page['info']['top_img'] ?>">
    </div>
<?php } ?>
<?php if ($page['info']['article_block']) { ?>
    <div class="reveal-div">
        <span class="reveal-span"><?php echo $page['info']['article_block']['blank_content'] ?></span>
    </div>
<?php } ?>

<div class="rich_media <?php echo 0 == $page['info']['is_fill'] ? '' : 'full_screen' ?>" style="display:none; ">
    <input id="addfans_type" value="<?php echo $page['info']['addfans_type']; ?>" hidden>
    <input id="wxid" name="wxid" type="text" hidden/>

    <?php if (0 == $page['info']['is_hide_title']) { ?>
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
    <?php } ?>
    <div class="rich_media_content " id="js_content">
    </div>
    <?php if ($page['info']['article_block']) { ?>
        <div class="settle-div">
            <span class="settle-span"><?php echo $page['info']['article_block']['block_two'] ?></span>
        </div>
    <?php } ?>
    <?php
    $r_flag = $page['info']['review_id'] == 0 ? 0 : 1;
    $r_type = 0;
    if ($r_flag == 1) {
    ?>
    <div class="rich_media_content" id="review" style="padding-top: 20px">
        <?php
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
            <?php
        }

        ?>
        <?php if ($page['info']['cover_url']) {?>
        <div id="qq_share" style="display:none;padding:10px 10px;border:solid 1px #a6c790;background-color: #a6c790;color: white; border-radius:5px;z-index: 111111;width: 20%;position: fixed;left: 75%;top: 40%;font-size: 1rem;" onclick="share_to_weixin()">
            分享到微信
        </div>
        <?php } ?>
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
                    <div class="to_qr">
                        &nbsp;
                    </div>
                </div>
                <div class="close_Mask"></div>
            </div>
        </div>
        <?php if ($r_flag == 1) {
        ?>
    </div>
<?php } ?>
    <?php if ($page['info']['article_block']) { ?>
        <div class="settle-div">
            <span class="settle-span"><?php echo $page['info']['article_block']['block_three'] ?></span>
        </div>
    <?php } ?>
    <?php
    if ($page['info']['order']['is_suspend'] == 0) {
        if ($page['info']['bottom_type'] == 0) { ?>
            <div class="top_tip">
                <div class="to_pc" style="max-width: 60%"></div>
                <div style="padding-left:10px;float: left;width: auto;max-width: 40%">
                    <p style="font-size:15px;margin-top:5px !important;padding-left: 5px;font-weight: bold; ">微信号：<span style="background: red;color: white;font-size: 18px;" class="wx_name" id="floot_weixin1"></span></p>
                    <p class="suspension_text"
                       style="font-weight:bold;padding-right:5px !important;font-size:14px;bottom: auto !important;margin-top: 8px !important;"></p>
                </div>
            </div>
        <?php } elseif ($page['info']['bottom_type'] == 1) { ?>
            <div class="top_tip1">
                <img src="<?php echo $page['info']['cdn_url'] . $page['info']['avater_img'] ?>" class="bottom_img"
                     style="margin-left: 4%;margin-top: 7px">
                <a href="javascript:void(0);" class="jump bottom_btn"><img
                            src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/add_btn.png"></a>
                <span class="suspension_text1" style='bottom:30px;font-size: 15px;'><?php echo $page['info']['suspension_text'] ?></span>
            </div>

            <div class="top_tip2">
            </div>

        <?php }elseif ($page['info']['bottom_type'] == 3){?>
            <div class="top_tip">
                <img class="to_pc" src="<?php echo $page['info']['cdn_url']; ?>/static/img/weixin.png"
                         style="max-height: 75px;float: left;">
                <div style="padding-top:4px;">
                    <b class="wx_account">
                        <div>
                            <b style="font-size:15px;padding-left: 5px;color: black;">微信号：</b>
                            <b style="background: red;color: white;font-size: 18px;" id="floot_weixin1"></b><br>
                            <div style="margin-top: 10px;"></div>
                            <b style="font-size:15px;color: black;white-space:nowrap;font-size: 15px;"><?php echo $page['info']['suspension_text']; ?></b>
                            <a href="javascript:void(0);" class="jump bottom_btn2"><img onclick="window.location.href='<?php echo $page['weixinList'][0]['land_url'] !=''?$page['weixinList'][0]['land_url']:'weixin://' ?>'"
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
                            <b style="background: red;color: white;font-size: 18px;" id="floot_weixin1"></b><br>
                            <b style="font-size:15px;color: black;white-space:nowrap;padding-left: 5px;;"><?php echo $page['info']['suspension_text']; ?></b>
                        </div>
                    </b>
                </div>
            </div>
    <?php    }
    } ?>
    <?php if ($page['info']['article_block']) { ?>
        <div class="settle-div">
            <span class="settle-span"><?php echo $page['info']['article_block']['block_one'] ?></span>
        </div>
    <?php } ?>
    <div style="display:none">
        <?php echo $page['info']['independent_cnzz']; ?>
        <?php echo $page['info']['total_cnzz']; ?>
    </div>
    <div id="ip" style="display: none"><?php $ip = $_SERVER["REMOTE_ADDR"];
        echo $ip; ?></div>
    <?php
    if ($page['info']['is_order'] == 1 && $page['info']['order_id'] != 0) {
        if ($page['info']['order']['goods_templete'] == 0) {
            require(dirname(__FILE__) . "/order.php");
        } elseif ($page['info']['order']['goods_templete'] == 1) {
            require(dirname(__FILE__) . "/order_templete_one.php");
        } elseif ($page['info']['order']['goods_templete'] == 2) {
            require(dirname(__FILE__) . "/free_order.php");
        }
//        require(dirname(__FILE__) . "/free_order.php");
    }
    ?>
    <div style="height: 60px"></div>
    <?php if ($page['info']['article_block']) { ?>
        <div class="settle-div">
            <span class="settle-span"><?php echo $page['info']['article_block']['block_four'] ?></span>
        </div>
        <div class="settle-div">
            <span class="settle-span"><?php echo $page['info']['article_block']['block_five'] ?></span>
        </div>
    <?php } ?>
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
            var weixin = t.getCacheWeixin();
            var weixin_name = weixin.weixin_name;
            var weixin_id = weixin.weixin_id;
            var customer_service_id = weixin.customer_service_id;
            var tg_uid = weixin.tg_uid;
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
            mycontent = mycontent.replace(/\{\{weixin_img\}\}/g, '<img class="qrcode_img" src="' + weixin_img + '" />');
            mycontent1 = mycontent1.replace(/\{\{weixin_img\}\}/g, '<img class="qrcode_img"  src="' + weixin_img + '" />');
            mycontent = mycontent.replace(/\{\{question\}\}/g, '<div id="swiper"></div>');
            mycontent1 = mycontent1.replace(/\{\{question\}\}/g, '<div id="swiper"></div>');
            $('#js_content').html(mycontent);
            e.html(mycontent1);
            $('#activity-name').html(t.info.title);
            $('#floot_weixin1').append('<span class="wx_name">' + weixin_name + '</span>');
            $('.open-wechat-number').html('<span class="wx_name">' + weixin_name + '</span>');
            $('.btn').attr('data-clipboard-text', weixin_name);
            $('.jump').attr("data-url", land_url);
            $('#wechat_id').val(weixin_name);//下单信息微信号
            $('#oweixinid').val(weixin_id);//下单信息微信号id
            $('#wxid').val(weixin_id);
            $('#csid').val(customer_service_id);//下单信息
            $('#tguid').val(tg_uid);//下单信息
            if ($('.consont').length) {
                low_num = parseInt($("#low_num").attr('data-v'));
                high_num = parseInt($('#high_num').attr('data-v'));
                console.log(low_num, high_num);
                random = parseInt((high_num - low_num) * Math.random() + low_num);
                if ($.cookie('random_data')) random = parseInt($.cookie('random_data'));
                if (random < low_num) random += low_num;
                if (random > high_num) random = high_num;

                $.cookie('random_data', random, {expires: 1});
                var obj = random;
                var $objplit = String(obj).split('');
                var $objhtml = '';
                for (var i = 0; i < $objplit.length; i++) {
                    $objhtml += '<li class="num" style="background: #fff;-ms-flex: 1;flex: 1;margin: 0 10px;text-align: center;font-size: 1.8rem;min-width: 28px;list-style-type:none;">' + $objplit[i] + '</li>';
                }
                $con_obj = $(".consont");
                $('.consont').each(function (i) {
                    co = $(this).find("li:first").css('color');
                    $(this).html($objhtml);
                    $(this).find("li").css('color', co);
                });

            }

            $('.suspension_text').append(t.info.suspension_text);
            $('.to_pc').append('<img class="qrcode_img" src="' + weixin_img + '" style="max-height: 75px"/>');
            if (Math.abs(img_width - img_height) < 10) {
                $('.to_qr').html('<img class="qrcode_img"  src="' + weixin_img + '" style="width: 65%;    margin: -10px 14%;padding-top: 5%;max-width:450px;max-height: :500px;"/>');
            } else {
                $('.to_qr').html('<img class="qrcode_img"  src="' + weixin_img + '" style="width: 90%;"/>');
            }
            document.title = t.info.tag;
            $(".rich_media").fadeIn(1000);
            var container = $("#swiper_change").html();
            $("#swiper").html(container);
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
            var weixin_id = weixin.id;
            var weixin_name = weixin.weixin_name;
            var customer_service_id = weixin.customer_service_id;
            var tg_uid = weixin.tg_uid;
            var weixin_img = weixin.weixin_img;
            var img_width = weixin.img_width;
            var img_height = weixin.img_height;
            var land_url = weixin.land_url;
            var weixin_list = t.weixinList;

            var coodata = {
                weixin_id: weixin_id,
                customer_service_id: customer_service_id,
                tg_uid: tg_uid,
                weixin_name: weixin_name,
                weixin_img: weixin_img,
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
<?php require(dirname(__FILE__) . "/common.php");?>
</body>
</html>