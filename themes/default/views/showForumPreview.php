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
    <link href="<?php echo $page['info']['css_cdn_url']; ?>/static/forum/css/style.min.css?176" rel="stylesheet" type="text/css">

    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/jquery.min-1.11.3.js"></script>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/lib/ZeroClipboard/clipboard.min.js"></script>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/swiper-3.4.1.min.js"></script>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/forum-preview.min.js?201807311540"></script>
    <?php if ($page['info']['is_lazy'] == 1) { ?>
        <script src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/jquery.lazyload.min.js"></script>
    <?php } ?>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/winpop.min.js"></script>

    <script type="text/javascript">
        function to_page(page_num) {
            $("div[class*='page_']").hide();
            $(".page_" + page_num).show();
            $("li[class='on']").removeClass("on").addClass("off");
            $("#one_" + page_num).removeClass("off").addClass("on");
        }
    </script>
</head>

<body style="-webkit-overflow-scrolling:touch;">

<?php
$htmlCode = "";
if ($page['info']['level_tag'] == 1) {
    $htmlCode .= " <div class=\"top\"> <img style=\"width:100%; max-height: 120px\" src=\"" . $page['info']['top_img'] . "\"></div>";
}
$htmlCode .= "<div class=\"clear\"></div> <div class=\"topTitle\">" . $page['info']['top_text'] . "</div>
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
        $htmlCode .= "\"><a href=\"javascript:void(0); \">" . $i . "</a></li>";
    }
    $htmlCode .= " </ul></div>";
}
$htmlCode .= "</div>";
?>
<div class="container" id="container">
    <!--end-->
</div>
<div class="clear"></div>
<div class="footerss" style="max-width:640px;text-align:center; line-height:25px;"><br>
    <input id="addfans_type" value="<?php echo $page['info']['addfans_type']; ?>" hidden>
    <p>
        手机版 电脑版 首页<br>
        @2017 健康论坛 论坛协议 意见反馈
    </p>
</div>

<?php if ($page['info']['bottom_type'] != 2) { ?>

    <div class="top_tip1">
        <img src="<?php echo $page['info']['cdn_url'] . $page['info']['avater_img'] ?>" class="bottom_img"
             style="margin-left: 4%;margin-top: 7px">
        <a href="javascript:void(0);" class="bottom_btn"><img
                src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/add_btn.png"></a>
        <span class="suspension_text1" style='bottom:30px'><?php echo $page['info']['suspension_text'] ?></span>
    </div>

    <div class="top_tip2">
    </div>
    <div style="height: 60px"></div>
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
            <img src="/static/front/images/openWechat.png" onclick="window.location.href='weixin://'">
        </div>
    </div>
    <div class="finish-task-layer-bg"></div>
</div>
<div class="qq_Mask">
    <div class="pr">
        <img class="qqimg" src="/static/front/images/qq.png">
        <div class="Mask_txt">
            <h3><?php echo $page['info']['addfans_text'] ?></h3>

            <div>
                <img src="/static/img/defaultQR.gif"
                     style="width: 90%;    margin: -10px 3%;padding-top: 5%;max-height: :500px;">
            </div>
        </div>
        <div class="close_Mask"></div>
    </div>
</div>


<script>

    var pageShowDetail = {
        info:<?php echo json_encode(array('title' => $page['info']['article_title'], 'content' => $htmlCode, 'tag' => $page['info']['tag'], 'suspension_text' => $page['info']['suspension_text']))?>,
        weixinList:<?php echo json_encode($page['weixinList']); ?>,
        showWeixin: function () {
            var t = this;
            var weixin_name = '预览微信号';
            var xingxiang = '<?php echo $page['info']['xingxiang']?>';
            var weixin_img = '/assets/img/defaultQR.gif';

            var mycontent = t.info.content;
            mycontent = mycontent.replace(/\{\{weixin\}\}/g, weixin_name);
            mycontent = mycontent.replace(/\{\{xingxiang\}\}/g, xingxiang);
            mycontent = mycontent.replace(/\{\{weixin_img\}\}/g, '<img style="width:260px"  src="' + weixin_img + '" />');

            $(".container").append(mycontent);
            $('.open-wechat-number').html(weixin_name);
            $('.btn').attr('data-clipboard-text', weixin_name);

            $("#container").fadeIn(1000);
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
</body>

</html>