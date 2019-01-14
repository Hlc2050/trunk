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
<div class="weui-cells__title">投诉内容</div>
<div class="weui-cells">
    <div class="weui-cell">
        <div class="weui-cell__hd"></div>
        <div class="weui-cell__bd">
            <p><?php echo $page['info']['article_title'] ?></p>
        </div>
        <div class="weui-cell__ft">
            <img id="cover_show1" style="width: 50px;height: 50px;" src="<?php echo $page['info']['cover_url'] ?>">
        </div>
    </div>
</div>
<div class="weui-cells__title">投诉描述</div>
<div class="weui-cells weui-cells_form">
    <div class="weui-cell">
        <div class="weui-cell__bd">
            <textarea class="weui-textarea" id="connent" placeholder="请输入投诉内容" rows="3"></textarea>
            <div class="weui-textarea-counter"><span>0</span>/200</div>
        </div>
    </div>
</div>
<div class="weui-cells weui-cells_form">
    <div class="weui-cell">
        <div class="weui-cell__bd">
            <div class="weui-uploader">
                <div class="weui-uploader__hd">
                    <p class="weui-uploader__title">图片上传</p>
                    <div class="weui-uploader__info">0/4</div>
                </div>

                <div class="weui-uploader__bd">
                    <ul class="weui_uploader_files"><!-- 预览图插入到这 --> </ul>
                </div>
                <div class="weui-uploader__input-box" style="margin-left: 10px;margin-top: 10px" >
                    <input id="uploaderInput" class="weui-uploader__input js_file" type="file" accept="image/*"
                          multiple="">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="weui-cells__title"></div>
<a onclick="TurnShow()" class="weui-btn weui-btn_primary">确定</a>
<?php }else{?>
    <div class="weui-cells__title">投诉内容</div>
    <div class="weui-cells">
        <div class="weui-cell">
            <div class="weui-cell__hd"></div>
            <div class="weui-cell__bd">
                <p><?php echo $idata['article_title'] ?></p>
            </div>
            <div class="weui-cell__ft">
                <img id="cover_show1" style="width: 50px;height: 50px;" src="<?php echo $idata['cover_url'] ?>">
            </div>
        </div>
    </div>
    <div class="weui-cells__title">投诉描述</div>
    <div class="weui-cells weui-cells_form">
        <div class="weui-cell">
            <div class="weui-cell__bd">
                <textarea class="weui-textarea" id="connent_index" placeholder="请输入投诉内容" rows="3"></textarea>
                <div class="weui-textarea-counter"><span>0</span>/200</div>
            </div>
        </div>
    </div>
    <div class="weui-cells weui-cells_form">
        <div class="weui-cell">
            <div class="weui-cell__bd">
                <div class="weui-uploader">
                    <div class="weui-uploader__hd">
                        <p class="weui-uploader__title">图片上传</p>
                        <div class="weui-uploader__info">0/4</div>
                    </div>

                    <div class="weui-uploader__bd">
                        <ul class="weui_uploader_files"><!-- 预览图插入到这 --> </ul>
                    </div>
                    <div class="weui-uploader__input-box" style="margin-left: 10px;margin-top: 10px" >
                        <input id="uploaderInput" class="weui-uploader__input js_file" type="file" accept="image/*"
                               multiple="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="weui-cells__title"></div>
    <a onclick="TurnIndex()" class="weui-btn weui-btn_primary">确定</a>

<?php } ?>
</body>
</html>
<script type="text/javascript">
    function TurnShow() {
        var connent = $("#connent").val().length ;
//        var li = $("ul").find("li").length;
        var url = "<?php echo $this->createUrl('site/showPreview') ?>?complaint=3&&id=<?php echo $page['info']['id'] ?>";
        if (connent == 0) $.alert("请输入投诉内容")
        else  window.location.href = url ;
//        if(li == 0) $.alert("请上传图片");
    }

    function TurnIndex() {
        var connent_index = $("#connent_index").val().length ;
        var url = "<?php echo $idata['url']."?complaint=3" ?>";
        if (connent_index == 0) $.alert("请输入投诉内容")
        else  window.location.href = url ;
    }

    $(function () {
        // 允许上传的图片类型
        var allowTypes = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];
        // 1024KB，也就是 1MB
        var maxSize = 1024 * 1024;
        // 图片最大宽度
        var maxWidth = 300;
        // 最大上传图片数量
        var maxCount = 4;
        $('.js_file').on('change', function (event) {
            var files = event.target.files;

            // 如果没有选中文件，直接返回
            if (files.length === 0) {
                return;
            }

            for (var i = 0, len = files.length; i < len; i++) {
                var file = files[i];
                var reader = new FileReader();

                // 如果类型不在允许的类型范围内
                if (allowTypes.indexOf(file.type) === -1) {
                    $.weui.alert({text: '该类型不允许上传'});
                    continue;
                }

                if (file.size > maxSize) {
                    $.weui.alert({text: '图片太大，不允许上传'});
                    continue;
                }

                if ($('.weui_uploader_file').length >= maxCount) {
                    $.weui.alert({text: '最多只能上传' + maxCount + '张图片'});
                    return;
                }

                reader.onload = function (e) {
                    var img = new Image();
                    img.onload = function () {
                        // 不要超出最大宽度
                        var w = 77;
                        // 高度按比例计算
                        var h = 77;
                        var canvas = document.createElement('canvas');
                        var ctx = canvas.getContext("2d");
                        // 设置 canvas 的宽度和高度
                        canvas.width = w;
                        canvas.height = h;
                        ctx.drawImage(img, 0, 0, w, h);
                        var base64 = canvas.toDataURL('image/png');
                        // 插入到预览区
                        var $preview = $('<li class="weui_uploader_file weui_uploader_status" style="margin-left:10px;float:left;width: 77px;height: 77px;background-image:url(' + base64 + ')"><div class="weui_uploader_status_content">0%</div></li>');
                        $('.weui_uploader_files').append($preview);
                        var num = $('.weui_uploader_file').length;
                        $('.js_counter').text(num + '/' + maxCount);

                        // 然后假装在上传，可以post base64格式，也可以构造blob对象上传，也可以用微信JSSDK上传

                        var progress = 0;

                        function uploading() {
                            $preview.find('.weui_uploader_status_content').text(++progress + '%');
                            if (progress < 100) {
                                setTimeout(uploading, 30);
                            }
                            else {
                                // 如果是失败，塞一个失败图标
                                //$preview.find('.weui_uploader_status_content').html('<i class="weui_icon_warn"></i>');
                                $preview.removeClass('weui_uploader_status').find('.weui_uploader_status_content').remove();
                            }
                        }

                        setTimeout(uploading, 30);
                    };

                    img.src = e.target.result;

                    $.post("/wap/uploader.php", {img: e.target.result}, function (res) {
                        if (res.img != '') {
                            alert('upload success');
                            $('#showimg').html('<img src="' + res.img + '">');
                        } else {
                            alert('upload fail');
                        }
                    }, 'json');
                };
                reader.readAsDataURL(file);

            }
        });
    });


</script>

