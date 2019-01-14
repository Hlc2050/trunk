<!--    <%--引入js文件--%>-->
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<!--    <%--通过config接口注入权限验证配置--%>-->
<script>
    wx.config({
        debug: false,
        appId: '<?php echo Yii::app()->params['weChat_config']['appID']; ?>',
        timestamp: '<?php echo $page['info']['timestamp'] ?>',
        nonceStr: '<?php echo $page['info']['nonceStr'] ?>',
        signature: '<?php echo $page['info']['signature'] ?>',
        jsApiList: [
            'checkJsApi',
            'onMenuShareTimeline',
            'onMenuShareAppMessage'
        ]
    });
    wx.ready(function () {
//        <%--公共方法--%>
        var shareData = {
            title: '<?php echo $page['info']['tag'] ?>',
            desc: '<?php echo $page['info']['descriptive_statement'] ?>',
            link: '<?php echo $page['info']['link'] ?>',
            imgUrl: '<?php echo $imgurl = $page['info']['cover_url'] ? 'http://' . $_SERVER['HTTP_HOST'] . $page['info']['cover_url'] : '' ?>',
            success: function (res) {
//                alert('已分享');
            },
            cancel: function (res) {
            }
        };
//        <%--分享给朋友接口--%>
        wx.onMenuShareAppMessage(shareData);
//        <%--分享到朋友圈接口--%>
        wx.onMenuShareTimeline(shareData);
    });

    $(function () {
        //微信号长按方法
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

        //微信号二维码长按方法
        $(".qrcode_img").longPress(function () {
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
        });
    });
//qq浏览器分享到微信
<?php if ($page['info']['cover_url']) {?>
    var config = {
        url:"<?php echo $page['info']['qq_share_link'];?>",
        title:"<?php echo $page['info']['article_title']; ?>",
        desc:"<?php echo $page['info']['descriptive_statement'];?>",
        img:"<?php echo $page['info']['qq_share']['img_url'].$page['info']['cover_url'];?>",
        img_title:'',
        from:''
    };
    var share_obj = new nativeShare('qq_share',config);
    function share_to_weixin() {
        share_obj.share('weixin');
    }
<?php }?>
</script>