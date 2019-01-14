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
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/jquery.min-1.11.3.js"></script>
    <script src="<?php echo $page['info']['css_cdn_url']; ?>/static/lib/ZeroClipboard/clipboard.min.js"></script>
    <script src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/norm-preview.min.js?201807311540"></script>
    <?php if ($page['info']['is_lazy'] == 1) { ?>
        <script src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/jquery.lazyload.min.js?175"></script>
    <?php } ?>
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/winpop.min.js?175"></script>

    <link media="all" href="<?php echo $page['info']['css_cdn_url']; ?>/static/front/css/norm-index.min.css?201807101115" type="text/css" rel="stylesheet">
    <title>投诉</title>
    <!-- head 中 -->
    <link rel="stylesheet" href="https://cdn.bootcss.com/weui/1.1.2/style/weui.min.css">
    <link rel="stylesheet" href="https://cdn.bootcss.com/jquery-weui/1.2.0/css/jquery-weui.min.css">

    <!-- body 最后 -->
    <script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>

    <!-- 如果使用了某些拓展插件还需要额外的JS -->
    <script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/swiper.min.js"></script>
    <script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/city-picker.min.js"></script>
</head>
<body>

<?php if($idata == null){ ?>
<div class="weui-msg">
    <div class="weui-msg__icon-area"><i class="weui-icon-success weui-icon_msg"></i></div>
    <div class="weui-msg__text-area">
        <h2 class="weui-msg__title">已提交</h2>
        <p class="weui-msg__desc">您的投诉已提交审核，投诉单号2018888555。我们会在七个工作日内处理，感谢您对平台的支持。</p>
    </div>
    <div class="weui-msg__opr-area">
        <p class="weui-btn-area">
            <a href="<?php echo $this->createUrl('site/showPreview') ?>?id=<?php echo $data[0]['id']; ?>" class="weui-btn weui-btn_primary">确定</a>
        </p>
    </div>
    <div class="weui-msg__extra-area">
        <div class="weui-footer">

        </div>
    </div>
</div>
<?php }else{ ?>
    <div class="weui-msg">
        <div class="weui-msg__icon-area"><i class="weui-icon-success weui-icon_msg"></i></div>
        <div class="weui-msg__text-area">
            <h2 class="weui-msg__title">已提交</h2>
            <p class="weui-msg__desc">您的投诉已提交审核，投诉单号2018888555。我们会在七个工作日内处理，感谢您对平台的支持。</p>
        </div>
        <div class="weui-msg__opr-area">
            <p class="weui-btn-area">
                <a href="<?php echo $idata['url']; ?>" class="weui-btn weui-btn_primary">确定</a>
            </p>
        </div>
        <div class="weui-msg__extra-area">
            <div class="weui-footer">

            </div>
        </div>
    </div>
<?php } ?>
</body>
</html>
