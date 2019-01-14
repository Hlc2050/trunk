<!DOCTYPE html>
<html>

<head>
    <?php ob_flush();
    flush(); ?>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=0">
    <title><?php echo $page['info']['tag'] ?></title>
    
    <link href="<?php echo $page['info']['css_cdn_url']; ?>/static/forum/css/style.min.css?175" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="/static/front/css/reset.css">
    <link rel="stylesheet" href="<?php echo $page['info']['css_cdn_url']; ?>/static/front/css/style.min.css?176">
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/jquery.min-1.11.3.js"></script>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/lib/ZeroClipboard/clipboard.min.js"></script>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/forum-preview.min.js?201807311540"></script>
    <?php if ($page['info']['is_lazy'] == 1) { ?>
        <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/jquery.lazyload.min.js"></script>
    <?php } ?>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/winpop.min.js?175"></script>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/nativeShare.js?v=123"></script>

    <script type="text/javascript">
        function to_page(page_num) {
            $("div[class*='page_']").hide();
            $(".page_" + page_num).show();
            $("li[class='on']").removeClass("on").addClass("off");
            $("#one_" + page_num).removeClass("off").addClass("on");
            location.href = "#topTitle";
        }
    </script>
</head>

<body>

<?php
$htmlCode = "";
if ($page['info']['level_tag'] == 1) {
    $htmlCode .= " <div class=\"top\"> <img style=\"width:100%; max-height: 120px\" src=\"" . $page['info']['top_img'] . "\"></div>";
}
$htmlCode .= "<div class=\"clear\"></div> <div id=\"topTitle\" class=\"topTitle\">" . $page['info']['top_text'] . "</div>
              <div class=\"h01\"></div><div class=\"main\" id=\"main\">
              <p class=\"title\" id=\"fk\">" . $page['info']['article_title'] . "</p>
              <div class=\"tab1\" id=\"tab1\"><div class=\"menudiv\">
              <div class=\"containers\" id=\"containers\" style=\"display: block;\">
              <div class=\"main1 page_1\"><div class=\"left\">
              <img class=\"lazy\" data-original=\"" . $page['reviewInfo']['avatar_url'] . "\">
              <img class=\"sticky\" src=\"/static/forum/images/louzhu.png\"/><span class=\"name_span\">
              " . $page['reviewInfo']['landlord'] . "</span>
              </div> <div class=\"right\"><div class=\"right_m\">
              " . $page['info']['content'] . "
              </div><div class=\"right_reply\"><div class=\"right_reply_l\">
              <div class=\"right_reply_l_r\">收起回复</div><div class=\"right_reply_l_l\">
             <span>来自<a>
              " . $page['appSign'][array_rand($page['appSign'])] . "
              </a></span><span>1楼</span><span>" . $page['info']['idintity'] . "</span>
              </div></div> </div> </div> </div>";
$review_count = 0;
$page_num = 1;

foreach ($page['reviewDetailInfo'] as $key => $value) {
    $count = count($value);
    if ($page['reviewInfo']['is_page'] == 0) {
        $page_size = $page['reviewInfo']['page_size'];
        if ($page_size == $review_count) {
            $page_num += 1;
            $review_count = 1;
        } else {
            $review_count += 1;
        }
    }
    $htmlCode .= "<div class=\"main1 page_" . $page_num . "\" ";
    if ($page_num != 1) $htmlCode .= " hidden";

    $htmlCode .= ">
                  <div class=\"left\"><img class=\"lazy\" data-original=\"" . $value[0]['avatar_url'] . "\"><span class=\"name_span\">" . $value[0]['review_name'] . "</span>
                  </div><div class=\"right\"> <div class=\"right_m\">
                  " . $value[0]['review_content'] . "
                  </div><div class=\"right_reply\"><div class=\"right_reply_l\">
                  <div class=\"right_reply_l_r\">收起回复</div>
                  <div class=\"right_reply_l_l\"><span>来自<a>
                  " . $page['appSign'][array_rand($page['appSign'])] . "</a></span>
                  <span>" . ($value[0]['floor'] + 1) . "楼</span> <span>" . $value[0]['review_date'] . "</span>
                  </div></div>";
    if ($count > 1) {
        $htmlCode .= " <div class=\"right_reply_m\"><div class=\"right_reply_m_m\"><ul>";
        for ($i = 1; $i < $count; $i++) {
            $floor = $i;
            $htmlCode .= " <li><div class=\"img\"><img src=\"" . $value[$i]['avatar_url'] . "\"></div><div class=\"cont\"><a>";
            if ($value[$i]['reply_to'] == 0)
                $htmlCode .= $value[$i]['review_name'];
            else {
                $reply_to = $value[$value[$i]['reply_to']]['review_name'];
                $htmlCode .= $value[$i]['review_name'] . "回复" . $reply_to;
            }
            $htmlCode .= "：</a><div class=\"cont_m\">" . $value[$i]['review_content'] . "</div></div></li>";
        }
        $htmlCode .= " </ul></div></div>";
    }
    $htmlCode .= "   </div></div></div>";
}
$htmlCode .= "</div></div></div> <div class=\"clear\"></div>";
if ($page['reviewInfo']['is_page'] == 0) {
    $htmlCode .= "<div class=\"menu\" id=\"menu\"><ul>";
    for ($i = 1; $i <= $page_num; $i++) {
        $htmlCode .= "<li id=\"one_" . $i . "\" ";
        $htmlCode .= "onclick=\"to_page(" . $i . ")\"  class=\"";
        if ($i == 1) $htmlCode .= "on";
        else  $htmlCode .= "off";
        $htmlCode .= "\"><a>" . $i . "</a></li>";
    }
    $htmlCode .= " </ul></div>";
}
$htmlCode .= "</div>";
?>
<div style="-webkit-overflow-scrolling:touch; width: 100%; height: 100%; overflow-x: hidden; position: relative;">
    <div class="container">
        <!--end-->
    </div>

    <div class="clear"></div>

    <div class="footerss" style="text-align:center; max-width:640px;line-height:25px;"><br>
        <input id="addfans_type" value="<?php echo $page['info']['addfans_type']; ?>" hidden>
        <input id="wxid" name="wxid" type="text" hidden/>
        <p>
            手机版 电脑版 首页<br>
            @2017 健康论坛 论坛协议 意见反馈</p>
    </div>
    <?php if ($page['info']['cover_url']) {?>
        <div id="qq_share" style="display:none;text-align:center;padding:8px;border:solid 1px #a6c790;background-color: #a6c790;color: white; border-radius:5px;z-index: 111111;width: 25%;position: fixed;left: 75%;top: 40%;font-size: 0.9rem;" onclick="share_to_weixin()">
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
                <p class="fx-tip"><?php echo helper::cut_str($page['info']['char_intro'],16); ?></p>
                <p class="fx-greeting"><?php echo helper::cut_str($page['info']['chat_content'],30); ?></p>
                <div class="fx-content">请输入内容</div>
                <div class="finish-task-status">
                    <img style="width: 100%" src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/圆角矩形2.png">
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
        <div class="open-wechat-toast" style="top: 13.5rem;">
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
                <h3><?php echo $page['info']['addfans_text'] ?></h3>
                <div class="to_qr">
                    &nbsp;
                </div>
            </div>
            <div class="close_Mask"></div>
        </div>
    </div>
</div>
    
<?php if ($page['info']['bottom_type'] != 2) { ?>
    <div class="top_tip1">
        <img src="<?php echo $page['info']['cdn_url'] . $page['info']['avater_img'] ?>" class="bottom_img"
             style="margin-left: 4%;margin-top: 7px">
        <a href="javascript:void(0);" class="bottom_btn jump"><img
                    src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/add_btn.png"></a>
        <span class="suspension_text1" style='bottom:30px'><?php echo $page['info']['suspension_text'] ?></span>
    </div>

    <div class="top_tip2">
    </div>
    <div style="height: 60px"></div>
<?php } ?>
<div style="display:none">
    <?php echo $page['info']['independent_cnzz']; ?>
    <?php echo $page['info']['total_cnzz']; ?>
</div>
<div id="ip" style="display: none"><?php $ip = $_SERVER["REMOTE_ADDR"]; echo $ip; ?></div>
    
<script>
    var pageShowDetail = {
        info:<?php echo json_encode(array('title' => $page['info']['article_title'], 'content' => $htmlCode, 'tag' => $page['info']['tag'], 'suspension_text' => $page['info']['suspension_text']))?>,
        weixinList:<?php echo json_encode($page['weixinList']); ?>,
        showWeixin: function () {
            var t = this;
            var weixin = this.getCacheWeixin();
            var weixin_name = weixin.weixin_name;
            var weixin_id = weixin.weixin_id;
            var weixin_img = weixin.weixin_img;
            var img_width = weixin.img_width;
            var img_height = weixin.img_height;
            var land_url = weixin.land_url;
            var xingxiang = '<?php echo $page['info']['xingxiang']?>';

            var mycontent = t.info.content;
            mycontent = mycontent.replace(/\{\{xingxiang\}\}/g, xingxiang);
            $('#wxid').val(weixin_id);
            $('.open-wechat-number').html('<span class="wx_name">' + weixin_name + '</span>');
            $('.btn').attr('data-clipboard-text', weixin_name);

            $('.jump').attr("data-url", land_url);
            if (Math.abs(img_width - img_height) < 10) {
                $('.to_qr').html('<img class="qrcode_img"  src="' + weixin_img + '" style="width: 60%;    margin: -10px 17%;padding-top: 5%;max-width:450px;max-height: :500px;"/>');

            } else {
                $('.to_qr').html('<img class="qrcode_img"  src="' + weixin_img + '" style="width: 90%;    margin: -10px 3%;padding-top: 5%;max-height: :500px;"/>');
            }
            mycontent = mycontent.replace(/\{\{weixin\}\}/g, '<span class="wx_name">' + weixin_name + '</span>');
            mycontent = mycontent.replace(/\{\{weixin_img\}\}/g, '<img class="qrcode_img"  style="width:260px" src="' + weixin_img + '" />');

            $('.container').html(mycontent);
            $(".container").fadeIn(1000);
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
            container: $("body"),
            threshold: 500
        })
        <?php }?>
    })


</script>
<?php require(dirname(__FILE__) . "/common.php");?>
</body>

</html>