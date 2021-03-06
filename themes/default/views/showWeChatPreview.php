
<!DOCTYPE html>
<html>
<?php ob_flush();
flush(); ?>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0">
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

    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
    <script type="text/javascript" src="<?php echo $page['info']['css_cdn_url']; ?>/static/front/js/jq.js"></script>
    <style>
        * {
            padding: 0;
            margin: 0;
        }

        a {
            text-decoration: none;
        }

        .scroll{
            max-width: 100%;
            width: 100%;
            margin-top: 0px;
            left: 0px;
            padding: 0px;
            top: 0px;
            background: white;
            height: 100%;
        }
        .myBox {
            display: block;
            background: white;
            color: #000;
            padding-top: 20px;
            padding-left: 15px;
            padding-right: 15px;
            box-sizing: border-box;
            width: 100%;
            position: relative;
            max-width: 500px;
            margin:0 auto;
        }

        .topTitle {
            max-width: 500px;
            margin: 0 auto 10px;
            font-size: 24px;
            font-weight: 400;
            line-height: 1.4;
            color: #444;
            font-family: "微软雅黑";
            outline: 0;
            border-bottom: 1px solid #e7e7eb;
            padding-bottom: 10px;
        }

        .timer {
            font-family: "微软雅黑";
            max-width: 500px;
            margin: 0 auto;
            font-size: 17px;
            padding-bottom: 25px;
            color: #8c8c8c;
            box-sizing: border-box;
            line-height: 20px;
        }

        .floot {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            height: 88px;
        }

        img {
            width: 100%;
        }


        #loading-warp{
            height: 100px;
            margin-top: -150px;
            background: rgba(48, 51, 54, 1);
            text-align: center;
            padding-top: 50px;
            color: rgba(168, 168, 168, 0.9);
            font-size: 16px;
        }
    </style>
</head>

<body >
<div>
    <div id="loading-warp" class="loading-warp">
        此网页由 mp.weixin.qq.com 提供
    </div>
    <div class="scroll" style="width: 1920px; height: 950px;">
        <div class="myBox">
            <p class="topTitle" id="activity-name"></p>

            <p class="timer">
                <?php if ($page['info']['is_main_page'] == 1 && $page['info']['is_fill'] == 1) { ?>
                    <a onclick="TurnAu()"  href="javascript:void 0" style="display: inline-block;vertical-align: middle;margin-right: 8px;margin-bottom: 2px;font-size: 15px;color:rgb(0, 102, 255);text-decoration: none;height:19px;"><?php echo $page['info']['suspension_text'] ? $page['info']['suspension_text'] : "点击查看更多作者信息"; ?></a>
                <?php } else { ?>
                    <a  style="display: inline-block;vertical-align: middle;margin-right: 8px;margin-bottom: 2px;font-size: 15px;color:rgb(0, 102, 255);text-decoration: none;height:19px;"><?php echo $page['info']['suspension_text'] ? $page['info']['suspension_text'] : "点击查看更多作者信息"; ?></a>
                <?php } ?>
                <span style="font-size: 15px;">
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
                        echo date('Y-m-d',$page['info']['release_date']);
                }
                ?>
                </span>&nbsp;
            </p>
            <div id="main">
                <div id="js_content" class="rich_media_content">

                </div>
                <div id="selectReview"></div>
            </div>
            <br />
            <div>
                <p class="floot" style="margin-top: 15px; font-family: -apple-system-font,&quot;Helvetica Neue&quot;,&quot;PingFang SC&quot;,&quot;Hiragino Sans GB&quot;,&quot;Microsoft YaHei&quot;,sans-serif;line-height: 20px;font-size: 16px;color: #8c8c8c;box-sizing: border-box;">
                    <?php if ($page['info']['is_main_page'] == 1) { ?>
                        <a  onclick="TurnRead()" href="javascript:void 0" style="color: rgb(0, 102, 255);">阅读原文</a>&nbsp;&nbsp;&nbsp;
                    <?php } ?>
                    阅读<span>&nbsp;<?php echo $page['info']['second_audio']; ?></span> &nbsp;&nbsp;&nbsp;
                    <span class="zanV" style="float:right;">&nbsp;<?php echo $page['info']['first_audio']; ?></span>
                    <img class="zan" src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/zan2.png" style="float:right; padding-top:3px; width: 14px;height: 14px;display:inline-block;transform: translateY(1px);"><img class="zan1" src="<?php echo $page['info']['cdn_url']; ?>/static/front/images/zan3.png" style="float:right; padding-top:3px; width: 14px;height: 14px;display:none;transform: translateY(1px);">
                    <!--<a style="line-height: 20px;font-size: 16px;color: #8c8c8c;display: inline-block;box-sizing: border-box;float: right;" href="https://my2.yynovel.cc/Public/Home/imitation/file/toushu.html?id=1954">投诉</a>-->
                </p>
            </div>
        </div>
    </div>
    <script>
        //监听返回事件
        <?php if ($page['info']['is_main_page'] == 1 && $page['info']['is_hide_title'] == 1) {?>
        //返回页面链接不为空
        <?php  if($data[1]['link'] != null){ ?>
        var go_href = <?php echo "'" . $data[1]['link'] . "'" ?>;
        //返回页面链接为空
        <?php }else{ ?>
        var go_href = "<?php echo $this->createUrl('site/showPreview') ?>?id=<?php echo $data[1]['id'] ?>";
        <?php } ?>
        <?php }else{ ?>
        var go_href = "<?php echo $this->createUrl('site/showPreview') ?>?id=<?php echo $data[0]['id'] ?>";
        <?php } ?>
        var $window = (navigator.userAgent.match('iPhone') && self != top) ? window.parent : window;
        var u = navigator.userAgent;
        var version = isWx(u);
        if(version == 'ios') {
            getHistory();
            var flag = true;
            window.addEventListener('popstate', function(e) {
                //监听到返回事件
                if(flag) {
                    window.location.href = go_href;
                }
                getHistory();
            }, false);
            function getHistory() {
                window.history.pushState({
                    title: null,
                    url: "#"
                }, null, '#');
            }
        } else {
            if(window.history && window.history.pushState) {
                $(window).on('popstate', function() {
                    window.history.pushState('forward', null, '');
                    window.history.forward(1);
                    window.location.href = go_href;
                });
            }
            window.history.pushState('forward', null, '');
            window.history.forward(1);
        }

        function isWx(u) { //判断微信系统
            if(!!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/)) {
                return 'ios';
            } else if(u.indexOf('Android') > -1 || u.indexOf('Linux') > -1) {
                return 'andriod';
            }
            var ua = window.navigator.userAgent.toLowerCase();
            if(ua.match(/MicroMessenger/i) == 'micromessenger') {
                return 'weixin';
            }
            return 'undefined';
        }
    </script>
    <script>
        //点击作者链接
        function TurnAu() {
            //返回页面链接不为空
            <?php  if($data[2]['link'] != null){ ?>
            window.location.href = <?php echo "'" . $data[2]['link'] . "'" ?>;
            //返回页面链接为空
            <?php }else{ ?>
            var url = "<?php echo $this->createUrl('site/showPreview') ?>?id=<?php echo $data[2]['id'] ?>";
            window.location.href = url;
            <?php } ?>
        }

        //点击阅读原文
        function TurnRead() {
            //返回页面链接不为空
            <?php  if($data[3]['link'] != null){ ?>
            window.location.href = <?php echo "'" . $data[3]['link'] . "'" ?>;
            //返回页面链接为空
            <?php }else{ ?>
            var url = "<?php echo $this->createUrl('site/showPreview') ?>?id=<?php echo $data[3]['id'] ?>";
            window.location.href = url;
            <?php } ?>
        }

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

                var mycontent = t.info.content;
                mycontent = mycontent.replace(/\{\{weixin\}\}/g, weixin_name);
                mycontent = mycontent.replace(/\{\{xingxiang\}\}/g, xingxiang);
                mycontent = mycontent.replace(/\{\{weixin_img\}\}/g, '<img style="width:260px"  src="' + weixin_img + '" />');

                mycontent = t.info.content;
                mycontent1 = e.html();
                mycontent = mycontent.replace(/\{\{weixin\}\}/g, weixin_name);
                mycontent1 = mycontent1.replace(/\{\{weixin\}\}/g, weixin_name);
                mycontent = mycontent.replace(/\{\{xingxiang\}\}/g, xingxiang);
                mycontent1 = mycontent1.replace(/\{\{xingxiang\}\}/g, xingxiang);
                mycontent = mycontent.replace(/\{\{weixin_img\}\}/g, '<img  src="' + weixin_img + '" />');
                mycontent1 = mycontent1.replace(/\{\{weixin_img\}\}/g, '<img  src="' + weixin_img + '" />');
                $('#activity-name').html(t.info.title);
                $('#js_content').html(mycontent);
                e.html(mycontent1);
                document.title = t.info.tag;
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
                if (aImgs[i].getAttribute('src').indexOf('http') == -1) {
                    aImgs[i].src = <?php echo '"' . $page['info']['cdn_url'] . '"';?>+aImgs[i].getAttribute('src');
                }
            }
        })
    </script>
</body>
</html>