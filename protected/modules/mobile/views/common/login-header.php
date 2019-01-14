<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport"
          content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no"/>
    <title><?php echo Yii::app()->params['management']['name']; ?></title>
    <?php
    $cdnUrl = '';
    if (Yii::app()->params['basic']['cdn_url']) {
        $cdnUrl = "http://" . Yii::app()->params['basic']['cdn_url'];//CDN域名
    } elseif (Yii::app()->params['upload_server']['imgUrl']) {
        $cdnUrl = "http://" . Yii::app()->params['upload_server']['imgUrl'];//CDN域名
    }
    ?>
    <link rel="stylesheet" href="http://cdn.amazeui.org/amazeui/2.7.2/css/amazeui.min.css"/>
    <link rel="stylesheet" href="<?php echo $cdnUrl; ?>/static/amazeui/2.7.2/css/admin.css"/>
    <link rel="stylesheet" href="<?php echo $cdnUrl; ?>/static/amazeui/2.7.2/css/app.css"/>
    <script type="text/javascript" src="<?php echo $cdnUrl; ?>/static/front/js/jquery.min-1.11.3.js"></script>
    <script type="text/javascript" src="http://cdn.amazeui.org/amazeui/2.7.2/js/amazeui.min.js"></script>
    <script type="text/javascript" src="<?php echo $cdnUrl; ?>/static/amazeui/2.7.2/js/amazeui.dialog.min.js"></script>
    <script type="text/javascript" src="http://cdn.amazeui.org/amazeui/2.7.2/js/amazeui.ie8polyfill.js"></script>
    <script type="text/javascript" src="http://cdn.amazeui.org/amazeui/2.7.2/js/amazeui.widgets.helper.min.js"></script>
    <script type="text/javascript">
        window.addEventListener('resize', function () {
            if (document.activeElement.tagName === 'INPUT') {
                document.activeElement.scrollIntoView({behavior: "smooth"})
            }
        })

    </script>