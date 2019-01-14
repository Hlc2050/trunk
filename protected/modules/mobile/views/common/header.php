<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport"
          content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no"/>
    <meta http-equiv="Expires" CONTENT="0">
    <meta http-equiv="Cache-Control" CONTENT="no-cache">
    <meta http-equiv="Pragma" CONTENT="no-cache">

    <title><?php echo Yii::app()->params['management']['name']; ?></title>
    <?php
    $cdnUrl = '';
    if (Yii::app()->params['basic']['cdn_url']) {
        $cdnUrl = "http://" . Yii::app()->params['basic']['cdn_url'];//CDN域名
    } elseif (Yii::app()->params['upload_server']['imgUrl']) {
        $cdnUrl = "http://" . Yii::app()->params['upload_server']['imgUrl'];//CDN域名
    }
    ?>
    <link rel="stylesheet" href="<?php echo $cdnUrl; ?>/static/amazeui/2.7.2/css/amazeui.min.css?9"/>
    <link rel="stylesheet"
          href="<?php echo $cdnUrl; ?>/static/amazeui/2.7.2/css/amazeui.chosen.min.css"/>
    <link rel="stylesheet" href="<?php echo $cdnUrl; ?>/static/amazeui/2.7.2/css/admin.min.css"/>
    <link rel="stylesheet" href="<?php echo $cdnUrl; ?>/static/amazeui/2.7.2/css/app.min.css"/>
    <link rel="stylesheet" href="<?php echo $cdnUrl; ?>/static/message/message.css"/>
    <script type="text/javascript"
            src="<?php echo $cdnUrl; ?>/static/front/js/jquery.min-1.11.3.js"></script>
    <script type="text/javascript" src="<?php echo $cdnUrl; ?>/static/amazeui/2.7.2/js/amazeui.min.js"></script>
    <script type="text/javascript" src="<?php echo $cdnUrl; ?>/static/amazeui/2.7.2/js/amazeui.ie8polyfill.js"></script>
    <script type="text/javascript" src="<?php echo $cdnUrl; ?>/static/amazeui/2.7.2/js/amazeui.widgets.helper.min.js"></script>
    <script type="text/javascript" src="<?php echo $cdnUrl; ?>/static/amazeui/2.7.2/js/amazeui.chosen.min.js"></script>
    <script type="text/javascript" src="<?php echo $cdnUrl; ?>/static/amazeui/2.7.2/js/app.min.js"></script>
    <script type="text/javascript">
        window.addEventListener('resize', function () {
            if (document.activeElement.tagName === 'INPUT') {
                document.activeElement.scrollIntoView({behavior: "smooth"})
            }
        });
        var u = navigator.userAgent;
        var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
        window.addEventListener('touchmove', function () {
            if(isiOS){
                $('input').blur()
            }

        });





    </script>

</head>

<header class="am-topbar am-topbar-inverse admin-header am-header-fixed">
    <div class="am-topbar-brand">
        <a href="<?php echo $this->createUrl('site/index'); ?>"><strong>新微商系统</strong>
            <small>移动版</small>
        </a>
    </div>

    <button class="am-topbar-btn am-topbar-toggle am-btn am-btn-sm am-btn-success am-show-sm-only"
            data-am-collapse="{target: '#topbar-collapse'}"><span class="am-icon-bars"></span></button>

    <div class="am-collapse am-topbar-collapse" id="topbar-collapse">

        <ul class="am-nav am-nav-pills am-topbar-nav am-topbar-right admin-header-list">
            <li><a href="#"><span class="am-icon-user"></span> <?php $id = Yii::app()->mobile->uid;
                    $name = AdminUser::model()->find('csno=' . $id);
                    echo $name['csname']; ?></a></li>
            <li><a href="#"><span class="am-icon-user-secret"></span> <?php echo Yii::app()->mobile->uname_true; ?></a>
            </li>
            <?php if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) { ?>
                <li><a href="<?php echo $this->createUrl('site/unbind'); ?>"><span class="am-icon-sign-out"></span> 解除绑定</a>
                </li>
            <?php } ?>
            <li><a href="<?php echo $this->createUrl('site/logout'); ?>"><span class="am-icon-sign-out"></span>
                    注销</a></li>
        </ul>
    </div>
</header>

<body>



