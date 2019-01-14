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
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/jquery.min-1.11.3.js"></script>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/norm-preview.min.js?201807311540"></script>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/norm.min.js"></script>
    <script src="<?php echo $page['info']['css_cdn_url']; ?>/static/lib/ZeroClipboard/clipboard.min.js"></script>
    <?php if ($page['info']['is_lazy'] == 1) { ?>
        <script src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/jquery.lazyload.min.js"></script>
    <?php } ?>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/winpop.min.js?175"></script>

    <title><?php echo $page['info']['tag']; ?></title>
</head>


<body style="-webkit-overflow-scrolling:touch;">


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
        <!--    当选择标准样式2的时候直接去微信不弹框-->
        <?php if ($page['info']['bottom_type'] != 3){ ?>
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
        <?php } ?>
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
                <div class="to_pc">
                    &nbsp;
                </div>
                <div style="padding-top:4px;">
                    <b class="wx_account">
                        <div>
                            <b style="font-size:15px;padding-left: 5px;color: black;">微信号：</b>
                            <b style="background: red;color: white" id="floot_weixin1"></b><br>
                        </div>
                    </b>
                    <b class="suspension_text"
                       style="font-weight:bold;font-size:xx-small;padding-left: 5px ;padding-right:30px;"></b>
                </div>
            </div>
        <?php } elseif ($page['info']['bottom_type'] == 1) { ?>
            <div class="top_tip1">
                <img src="<?php echo $page['info']['cdn_url'] . $page['info']['avater_img'] ?>" class="bottom_img"
                     style="margin-left: 4%;margin-top: 7px">
                <a href="javascript:void(0);" class="jump bottom_btn"><img
                            src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/add_btn.png"></a>
                <span class="suspension_text1" style='bottom:30px'><?php echo $page['info']['suspension_text'] ?></span>
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
                            <b style="background: red;color: white" id="floot_weixin1"></b><br>
                            <b style="font-size:15px;color: black;white-space:nowrap;"><?php echo $page['info']['suspension_text']; ?></b>
                            <a href="javascript:void(0);" class="jump bottom_btn"><img onclick="window.location.href='<?php echo $page['weixinList'][0]['land_url'] !=''?$page['weixinList'][0]['land_url']:'weixin://' ?>'"
                                        src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/add_friend.png"></a>
                        </div>
                    </b>
                </div>
            </div>
        <?php }
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

<script>
    var pageShowDetail = {
        info:<?php echo json_encode(array('title' => $page['info']['article_title'], 'content' => $page['info']['content'], 'tag' => $page['info']['tag'], 'suspension_text' => $page['info']['suspension_text']))?>,
        weixinList:<?php echo json_encode($page['weixinList']); ?>,
        showWeixin: function () {
            var t = this;
            var e = $("#selectReview");
            var mycontent = '';
            var weixin_name = '<?php echo $weixin_info['weixin_name']; ?>';
            var weixin_id = '<?php echo $weixin_info['weixin_id']; ?>';
            var customer_service_id = '<?php echo $weixin_info['customer_service_id']; ?>';
            var tg_uid = '<?php echo $weixin_info['tg_uid']; ?>';
            var weixin_img = '<?php echo $weixin_info['weixin_img']; ?>';
            var img_width = '<?php echo $weixin_info['img_width']; ?>';
            var img_height = '<?php echo $weixin_info['img_height']; ?>';
            var land_url = '<?php echo $weixin_info['land_url']; ?>';
            var xingxiang = '<?php echo $page['info']['xingxiang'];?>';

            mycontent = t.info.content;
            mycontent1 = e.html();
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
        },
        selectfrom: function (lowValue, highValue) {
            var choice = highValue - lowValue + 1;
            return Math.floor(Math.random() * choice + lowValue);
        },

    };

    $(function () {
        pageShowDetail.showWeixin();
        <?php if($page['info']['is_lazy'] == 1){ ?>
        $("img.lazy").lazyload({
            placeholder: <?php echo '"' . $page['info']['cdn_url'] . '"';?>+"/uploadfile/materialImgs/default_img.gif",
            effect: "fadeIn",
            threshold: 500
        })
        <?php }?>

        $(".wx_name").longPress(function () {

            weixin_id = $('#wxid').val()
            if ($.cookie('longPress' + weixin_id)) {
                return true
            }
            //do something...
            tdata = {
                'promotion_id':<?php echo $page['info']['promotion_id'];?>,
                'partner_id':<?php echo $page['info']['partner_id'];?>,
                'channel_id':<?php echo $page['info']['channel_id'];?>,
                'tg_uid':<?php echo $page['info']['promotion_staff_id'];?>,
                'weixin_id': weixin_id
            }
            jQuery.ajax({
                'type': 'POST',
                'url': '/site/pressStatistics',
                'data': tdata,
                'cache': false,
                'success': function (data) {
                    console.log(data)
                }
            })
            var leftTamp = new Date(new Date(new Date().toLocaleDateString()).getTime() + 24 * 60 * 60 * 1000 - 1);
            $.cookie('longPress' + weixin_id, 1, {expires: leftTamp});
        })
        $(".qrcode_img").longPress(function () {
            weixin_id = $('#wxid').val()
            if ($.cookie('longPress' + weixin_id)) {
                return true
            }
            console.log(378);

            //do something...
            tdata = {
                'promotion_id':<?php echo $page['info']['promotion_id'];?>,
                'partner_id':<?php echo $page['info']['partner_id'];?>,
                'channel_id':<?php echo $page['info']['channel_id'];?>,
                'tg_uid':<?php echo $page['info']['promotion_staff_id'];?>,
                'weixin_id': weixin_id
            }
            jQuery.ajax({
                'type': 'POST',
                'url': '/site/pressStatistics',
                'data': tdata,
                'cache': false,
                'success': function (data) {
                    console.log(data)
                }
            })
            var leftTamp = new Date(new Date(new Date().toLocaleDateString()).getTime() + 24 * 60 * 60 * 1000 - 1);
            $.cookie('longPress' + weixin_id, 1, {expires: leftTamp});
        });

    })
</script>
<?php require(dirname(__FILE__) . "/common.php");?>
</body>
</html>