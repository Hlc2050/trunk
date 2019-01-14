$(function () {
    var clipboard = new Clipboard('.btn');
    clipboard.on('success', function (e) {
        console.info('Action:', e.action);
        console.info('Text:', e.text);
        console.info('Trigger:', e.trigger);
//            alert("复制成功");
        var $copysuc = $("<div class='copy-tips' style='left: 33%'><div class='copy-tips-wrap'>☺ 复制成功</div></div>");
        $("body").find(".copy-tips").remove().end().append($copysuc);
        $(".copy-tips").fadeOut(2000);
        e.clearSelection();
    });

    clipboard.on('error', function (e) {
        var $copysuc = $("<div class='copy-tips' style='margin: 0 8%'><div class='copy-tips-wrap'>˃̣̣̥᷄⌓˂̣̣̥᷅!复制按钮出问题了,长按微信号复制</div></div>");
        $("body").find(".copy-tips").remove().end().append($copysuc);
        $(".copy-tips").fadeOut(5000);
        e.clearSelection();
        console.error('Action:', e.action);
        console.error('Trigger:', e.trigger);
    });

    var mySwiper = new Swiper('#case .swiper-container', {
        noSwiping: true,
        noSwipingClass: 'notmove',
    });

    var mySecSwiper = new Swiper('#thumb .swiper-container', {
        observer: true,//修改swiper自己或子元素时，自动初始化swiper
        observeParents: true,//修改swiper的父元素时，自动初始化swiper
        prevButton: '.swiper-button-prev',
        nextButton: '.swiper-button-next',
        initialSlide: 0
    });


    var psqNum = $("#psq_num").val();
    // 第一题
    var question_1 = '', question_2 = '', question_3 = '', question_4 = '', question_5 = '', Last_question = '';
    var lis = $(".swiper-wrapper");
    var odiv = $(".swiper-container");
    var wd = odiv.width();
    $("#first li").on("click", function () {
        question_1 = "";
        $(this).addClass("on").siblings().removeClass("on");
        question_1 = $(this).index() + 1;
        if (psqNum != 1) {
            $(this).parents(".swiper-slide").addClass("active").siblings().removeClass("active");
            lis.animate({left: "-" + wd + "px"}, 400);
        }
    });
    // 第二题
    $("#second li").on("click", function () {
        question_2 = "";
        $(this).addClass("on").siblings().removeClass("on");
        question_2 = $(this).index() + 1;
        if (psqNum != 2) {
            $(this).parents(".swiper-slide").addClass("active").siblings().removeClass("active");
            lis.animate({left: "-" + wd * 2 + "px"}, 400);
        }
    });
    // 第三题
    $("#third li").on("click", function () {
        question_3 = "";
        $(this).addClass("on").siblings().removeClass("on");
        question_3 = $(this).index() + 1;
        if (psqNum != 3) {
            $(this).parents(".swiper-slide").addClass("active").siblings().removeClass("active");
            lis.animate({left: "-" + wd * 3 + "px"}, 400);
        }
    });
    // 第四题
    $("#four li").on("click", function () {
        question_4 = "";
        $(this).addClass("on").siblings().removeClass("on");
        question_4 = $(this).index() + 1;
        if (psqNum != 4) {
            $(this).parents(".swiper-slide").addClass("active").siblings().removeClass("active");
            lis.animate({left: "-" + wd * 4 + "px"}, 400);
        }
    });
    // 第五题
    $("#five li").on("click", function () {
        question_5 = "";
        $(this).addClass("on").siblings().removeClass("on");
        question_5 = $(this).index() + 1;
    });

    // 点击提交弹出对应编号
    $(".submit_btn").on("click", function () {
        switch (psqNum) {
            case '1':
                c_obj = $("#first li");
                break;
            case '2':
                c_obj = $("#second li");
                break;
            case '3':
                c_obj = $("#third li");
                break;
            case '4':
                c_obj = $("#four li");
                break;
            case '5':
                c_obj = $("#five li");
                break;
            default:
                c_obj = '';
        }
        if (!c_obj.hasClass("on")) {
            $.diy_alert({"cont": "第" + psqNum + "题，未选答案"});
            setTimeout(function () {
                $(".diy_alert").remove();
            }, 1000);
            return;
        }
        else {
            Last_question = question_1.toString() + question_2.toString() + question_3.toString() + question_4.toString() + question_5.toString();
            $("#Mask .Mask_txt h3 span,#Mask .Mask_txt .num").html(Last_question);
            var land_url = $(this).attr("data-url");
            if (land_url == undefined)land_url = ''
            var ua = window.navigator.userAgent.toLowerCase();
            if (land_url == "" || ua.match(/MicroMessenger/i) == 'micromessenger') {
                var add_sign = $("#addfans_type").val();
                if (add_sign == 1) {
                    $('.open-wechat-success').fadeIn(400);
                } else {
                    $('.qq_Mask').fadeIn(400);
                }
            }else {
                //接口方式去微信
                jQuery.ajax({
                    'type': 'POST',
                    'dataType': 'json',
                    'url': '/site/getTicket',
                    'data': {'url': land_url, 'type': 99},
                    'cache': false,
                    'success': function (ret) {
                        if(ret.type == 2){
                            if(/baiduboxapp/i.test(navigator.userAgent)){
                                window.location.replace("bdbox://utils?action=sendIntent&minver=7.4&params=%7B%22intent%22%3A%22"+ret.href_url+"%23Intent%3Bend%22%7D");
                            }else{
                                window.location.replace(ret.href_url);
                            }
                        }else{
                            window.location.href = ret.href_url;
                        }
                    }
                });
            }
            $.ajax({
                'type': 'POST',
                'url': '/site/insertVote',
                'data': {
                    'vote_id': $("#vote_id").html(), 'promotion_id': $("#promotion_id").html(), 'ip': $("#ip").html
                    (), 'answer': Last_question
                },
                'cache': false,
                'success': function (data) {

                    console.log(data);
                }
            })
        }
    });


    // 点击上一题
    $(".prev").on("click", function () {
        //mySwiper.slidePrev();
        var b = parseInt(lis.css("left")) + parseInt(wd);
        lis.animate({left: b + "px"}, 400);
    });


    var isPlay = false;

    // 点击播放语音
    $(".voice").click(function () {
        if (!isPlay) {
            AudioPlay($(this), ".voice_pic");
            isPlay = true;
        } else {
            AudioPause($(this), ".voice_pic");
            isPlay = false;
        }
    });

    $(".M_voice").on("click", function () {
        if (!isPlay) {
            AudioPlay($(this), ".Middle_voice");
            isPlay = true;
        } else {
            AudioPause($(this), ".Middle_voice");
            isPlay = false;
        }
    });


    $(".B_voice").on("click", function () {
        if (!isPlay) {
            AudioPlay($(this), ".bottom_voice");
            isPlay = true;
        } else {
            AudioPause($(this), ".bottom_voice");
            isPlay = false;
        }
    });

// 音频播放函数
    function AudioPlay(obj, obj_voice) {
        obj.children("audio").get(0).play();
        obj.find("em,i").fadeOut();
        obj.find(obj_voice).addClass("on");
    }

// 音频暂停且当前时间设为0函数
    function AudioPause(obj, obj_voice) {
        obj.children("audio").get(0).pause();
        obj.find("audio").get(0).currentTime = 0;
        obj.find(obj_voice).removeClass("on");
    }

//关闭微信弹窗
    $(".qq_Mask .close_Mask").click(function () {
        $(".qq_Mask").fadeOut(400);
    });
//微信弹窗
    $(".bottom_btn,.top_r,.top_l").click(function () {
        var land_url = $(this).attr("data-url");
        if (land_url == undefined)land_url = ''
        var ua = window.navigator.userAgent.toLowerCase();
        if (land_url == "" || ua.match(/MicroMessenger/i) == 'micromessenger') {
            var add_sign = $("#addfans_type").val();
            if (add_sign == 1) {
                $('.open-wechat-success').fadeIn(400);
            } else {
                $('.qq_Mask').fadeIn(400);
            }
        } else {
            //接口方式去微信
            jQuery.ajax({
                'type': 'POST',
                'dataType': 'json',
                'url': '/site/getTicket',
                'data': {'url': land_url, 'type': 99},
                'cache': false,
                'success': function (ret) {
                    if(ret.type == 2){
                            if(/baiduboxapp/i.test(navigator.userAgent)){
                                window.location.replace("bdbox://utils?action=sendIntent&minver=7.4&params=%7B%22intent%22%3A%22"+ret.href_url+"%23Intent%3Bend%22%7D");
                            }else{
                                window.location.replace(ret.href_url);
                            }
                        }else{
                            window.location.href = ret.href_url;
                        }
                }
            });
        }
    });
    $(document).ready(function () {
        //图片去微信
        $("img.img2wx").click(function () {
            var land_url = $(this).attr("data-url");
            if (land_url == undefined)land_url = ''
            var ua = window.navigator.userAgent.toLowerCase();
            if (land_url == "" || ua.match(/MicroMessenger/i) == 'micromessenger') {
                return false
            } else {
                //接口方式去微信
                jQuery.ajax({
                    'type': 'POST',
                    'dataType': 'json',
                    'url': '/site/getTicket',
                    'data': {'url': land_url, 'type': 99},
                    'cache': false,
                    'success': function (ret) {
                        if(ret.type == 2){
                            if(/baiduboxapp/i.test(navigator.userAgent)){
                                window.location.replace("bdbox://utils?action=sendIntent&minver=7.4&params=%7B%22intent%22%3A%22"+ret.href_url+"%23Intent%3Bend%22%7D");
                            }else{
                                window.location.replace(ret.href_url);
                            }
                        }else{
                            window.location.href = ret.href_url;
                        }
                    }
                });
            }
        });
    })

    $('.close-wechat').on('click', function () {
        $('.open-wechat-success').fadeOut(400);
    });


});