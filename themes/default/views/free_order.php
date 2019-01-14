<link rel="stylesheet" href="<?php echo $page['info']['css_cdn_url']; ?>/static/order/css/2018.4.8.min.css?1751">


<!--弹出购买窗口-->
<div class="nav_title overall_color font_color" id="goumai_title"><?php echo $page['info']['order']['order_title'] ?></div>
<div class="popus_box" id="popus_goumai">
    <div class="conter_box">
        <div class="middle_box">
            <form>
                <input id="oweixinid" name="oweixinid" type="text" hidden/>
                <input id="csid" name="csid" type="text" hidden/>
                <input id="tguid" name="tguid" type="text" hidden/>
                <input id="wechat_id" name="wechat_id" type="text" hidden/>
                <dl style="margin: 10px auto">
                    <dt class="width5rem" style="  font-size: 1.2rem;">请填写收件人信息：</dt>

                </dl>
                <dl>
                    <dt class="width5rem">姓&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;名<span>*</span></dt>
                    <dd><input type="text" placeholder="请输入您的姓名" id="namepopus"></dd>
                </dl>
                <dl>
                    <dt class="width5rem">手机号码<span>*</span></dt>
                    <dd><input type="text" placeholder="请输入您的手机号码" id="telpopus"
                               onKeyUp="if(this.value.length>11){this.value=this.value.substr(0,11)};this.value=this.value.replace(/[^\d]/g,'');">
                    </dd>
                </dl>
                <dl>
                    <dt class="width5rem">选择地区<span>*</span></dt>
                    <dd style="height: 1.2rem;">
                        <div class="browser">
                            <!--选择地区-->
                            <a id="expressArea" href="javascript:void(0)"
                               style="border-radius: 4px;display: block;color: #666; border: 1px #cbcbcb solid; height: 2.5rem;text-decoration: none; line-height: 2.5rem; padding-left: 1rem;">省
                                > 市 > 区/县</a>
                            <section id="areaLayer" class="express-area-box" style="display: none">
                                <header>
                                    <h3>选择地区</h3>
                                    <a id="backUp" class="back" href="javascript:void(0)" title="返回"></a>
                                    <a id="closeArea" class="close" href="javascript:void(0)" title="关闭"></a>
                                </header>
                                <article id="areaBox">
                                    <ul id="areaList" class="area-list"></ul>
                                </article>
                            </section>
                            <div id="areaMask" class="mask"></div>

                        </div>
                    </dd>
                </dl>
                <dl>
                    <dd><input type="text" placeholder="详细地址" id="jiedpopus"></dd>
                </dl>
                <dl style="color:red;font-size: 0.8rem;text-align: center; margin-top: -10px">
                    <span>*温馨提示：同一地址限领一份</span>
                </dl>
                <dl>
                    <dt style="width: 45%;">方便接电话时间</dt>
                    <dd>
                        <select name="best_time" id="selectlpopus">
                            <option value="0">随时可以联系我</option>
                            <option value="1">上午（9点~12点）</option>
                            <option value="2">下午（12点~18点）</option>
                            <option value="3">09：00~10：00</option>
                            <option value="4">10：00~11：00</option>
                            <option value="5">11：00~12：00</option>
                            <option value="6">12：00~13：00</option>
                            <option value="7">13：00~14：00</option>
                            <option value="8">14：00~15：00</option>
                            <option value="9">15：00~16：00</option>
                            <option value="10">16：00~17：00</option>
                            <option value="11">17：00~18：00</option>
                        </select>
                    </dd>
                </dl>
                <dl>
                    <button type="button" id="sb_returnpo" style="background: red;"
                            class="overall_color font_color">确认提交
                    </button>
                </dl>
            </form>

        </div>
        <dl class="demo">
            <div id="demo" class="order_smg">
                [最新领取]:139****1548在4分钟前成功领取了黄精牡蛎覆盆子<br>[最新领取]:159****1078在3分钟前成功领取了黄精牡蛎覆盆子<br></div>
        </dl>
    </div>
    <?php if ($page['info']['order']['is_suspend'] == 1) { ?>
        <div class="buttom_d">
            <a href="#goumai_title"><img
                        src="<?php echo $page['info']['cdn_url'] . $page['info']['order']['suspend_img']; ?>"
                        width="100%"></a>
        </div>
    <?php } ?>

    <script src="<?php echo $page['info']['cdn_url']; ?>/static/order/js/jquery.area.js"></script>
</div>
<div id="f_success"
     style="display:none;width: 100%;height: 100%;position: fixed;z-index: 1000;background: white;top: 0;left: 0px;">
    <div class="w-data">

        <div align="center" style="margin: 1em"><img
                    src="<?php echo $page['info']['cdn_url']; ?>/static/order/images/cg.png" height="100" width="100"
                    style="margin:10px 0"/>

            <br>
            <strong style="color:#900;line-height: 2em;font-size: 1.5em">您的信息已成功提交!</strong>

        </div>

        <div style="line-height: 2em;width:90%; margin:0 5%; text-align:center; color:#930">
            <?php echo $page['info']['order']['success_info']; ?>
        </div>


        <div class="dinggou2" style="line-height: 2em">


            试用产品：<span id="sb_tradename"></span><br>
            收货人：<span id="sb_usernamee"></span><br>
            您的联系电话：<span id="sb_tel"></span><br>
            收货地址：<span id="sb_address"></span><br>
        </div>

        <div class="dinggou" style=" font-weight: bold"><span style="color:#f00">您方便接听电话时间：</span><span
                    id="sb_best"></span></div>

        <a id="return_o"
           style="background-color:#999;color:#FFF; margin:50px auto; display:block; width:120px; text-align:center; padding:5px">返回首页</a>
    </div>
</div>
<div id="f_error"
     style="display:none;width: 100%;height: 100%;position: fixed;z-index: 1000;background: white;top: 0;left: 0px;">
    <div class="w-data">
        <input type="text" id="inputTexts"
               style=" background-color:#FFFFFF; color:#FFFFFF; border: none; height:0.5px; width:0.5px"
               value="xFXbY4361X" readonly="readonly">
        <div align="center"><img src="<?php echo $page['info']['cdn_url']; ?>/static/order/images/sb.png" width="110"
                                 height="110" style="margin:10px 0"><br>
            <strong style="color:#900;font-size: 1.3em;line-height: 2em;"><span
                        id="error_msg">您的收货地址过于简单，快递小哥无从送起！</span></strong>
        </div>
        <div style="line-height:2em;width:90%; margin:0 5%; text-align:center; color:#930;padding: 1em 0em;"><?php echo $page['info']['order']['fail_info']; ?></div>
        <div class="dinggou" id="return_e" style="text-align: center;">
            <a>立即返回</a>
        </div>
    </div>
</div>


<script>
    var marquee = new Array(
        "[最新领取]:157****1349在1分钟前成功领取了黄精牡蛎覆盆子<br/>", "[最新领取]:151****1162在5分钟前成功领取了黄精牡蛎覆盆子<br/>", "[最新领取]:156****1228在5分钟前成功领取了黄精牡蛎覆盆子<br/>", "[最新领取]:150****1760在1分钟前成功领取了黄精牡蛎覆盆子<br/>", "[最新领取]:139****1548在4分钟前成功领取了黄精牡蛎覆盆子<br/>", "[最新领取]:159****1078在3分钟前成功领取了黄精牡蛎覆盆子<br/>", "[最新领取]:155****1560在2分钟前成功领取了黄精牡蛎覆盆子<br/>", "[最新领取]:159****1302在5分钟前成功领取了黄精牡蛎覆盆子<br/>", "[最新领取]:130****1565在1分钟前成功领取了黄精牡蛎覆盆子<br/>", "[最新领取]:158****1009在6分钟前成功领取了黄精牡蛎覆盆子<br/>");
    var marqeeI = 0;
    var marqueeII = 1;

    function marqueeL() {
        if (marqeeI > (marquee.length - 1)) marqeeI = 0;
        if (marqueeII > (marquee.length - 1)) marqeeI = 0;
        marqueeII = marqeeI + 1;
        var marHTML = marquee[marqeeI] + marquee[marqueeII];
        document.getElementById("demo").innerHTML = marHTML;
        marqeeI += 1;
        marqueeII += 1;
    }

    window.setInterval("marqueeL()", 2000);
</script>

<script>

    (function () {
        $(document).ready(function () {
            /*整体颜色*/
            $(".overall_color").css('background', '<?php echo $page['info']['order']['overall_color'] ?>');
            $(".font_color").css('color', '<?php echo $page['info']['order']['font_color'] ?>');
            /*提交窗口提交按钮*/
            $(document).on('click', '#sb_returnpo', function () {
                var namepopus = $('#namepopus').val();//用户姓名
                var telpopus = $('#telpopus').val();//用户电话
                var expressArea = $('#expressArea').html().replace(/&gt;/g, '');//用户选择的地区
                var jiedpopus = $('#jiedpopus').val();//用户街道
                var selectlpopus = $("#selectlpopus").find("option:selected").val();//用户方便接电话时间
                if ($('#expressArea')[0].innerText == '省 > 市 > 区/县') {
                    text = "您的收货地址过于简单，快递小哥无从送起";
                    $("#error_msg").html(text);
                    $("#f_error").show();
                    $('#expressArea').css('border-color', 'red')
                    return false
                } else {
                    $('#expressArea').css('border-color', '#666')
                }
                var r = /^((0\d{2,3}-\d{7,8})|(1([358][0-9]|4[579]|66|7[0135678]|9[89])[0-9]{8}))$/;
                if (!r.test(telpopus)) {
                    text = "您的电话号码有误，快递小哥无从送起";
                    $("#error_msg").html(text);
                    $("#f_error").show();
                    $('#telpopus').css('border-color', 'red')
                    return false
                }
                else {
                    $('#telpopus').css('border-color', '#cbcbcb')
                }
                if (namepopus == '') {
                    text = "您的名字未填写";
                    $("#error_msg").html(text);
                    $("#f_error").show();
                    $('#namepopus').css('border-color', 'red')
                    return false
                }
                else {
                    $('#namepopus').css('border-color', '#cbcbcb')
                }
                if (jiedpopus == '') {
                    text = "您的收货地址过于简单，快递小哥无从送起";
                    $("#error_msg").html(text);
                    $("#f_error").show();
                    $('#jiedpopus').css('border-color', 'red')
                    return false
                }
                else {
                    $('#jiedpopus').css('border-color', '#cbcbcb')
                }

                if (namepopus == '' || telpopus == '' || jiedpopus == '' || $('#expressArea')[0].innerText == '省 > 市 > 区/县') {
                    text = "您的信息填写过于简单，快递小哥无从送起";
                    $("#error_msg").html(text);
                    $("#f_error").show();

                    return false
                }
                var radio = "<?php echo $page['info']['order']['packages'][0]['package_name'] ?>";//商品选择套餐
                var package_id = "<?php echo $page['info']['order']['packages'][0]['package_id'] ?>";//商品选择套餐
                var price = "<?php echo $page['info']['order']['packages'][0]['package_price'] ?>";//商品单价
                var liuyanpopus = '';//用户留言
                var payment = '货到付款';
                var region = "";//用户地区
                var promotionid = "<?php echo $page['info']['promotion_id'] ? $page['info']['promotion_id'] : 0;?>";//推广id
                var channelid = "<?php echo $page['info']['channel_id'];?>";//渠道id
                var partnerid = "<?php echo $page['info']['partner_id'];?>";//合作商id
                var articleid = "<?php echo $page['info']['origin_template_id'];?>";//图文id
                var article_code = "<?php echo $page['info']['article_code'];?>";//图文id
                var orderid = "<?php echo $page['info']['order']['id'];?>";//下单模板id
                var wechat_id = $('#wechat_id').val();//
                var weixinid = $('#oweixinid').val();//
                var csid = $('#csid').val();//
                var tg_uid = $('#tguid').val();//
                strs = expressArea.split(" ");
                for (i = 0; i < strs.length; i++) {
                    if (strs.length == 3) {
                        region = strs[0];
                    } else {
                        region = strs[2];
                    }
                }
                var auth = 'zk2bu!@#';//
                var submarry = [];
                submarry.push(radio, price, namepopus, telpopus, expressArea, package_id, jiedpopus, selectlpopus, liuyanpopus,
                    promotionid, channelid, partnerid, articleid, orderid, wechat_id, article_code, weixinid, csid, tg_uid)
                console.log(submarry)
                var time = getOrderCookie();
                var auth = 'zk2bu!@#';//
                if (time >= 10000) {
                    $('#msgrepopus').hide();
                    $('#returnfrequently').show();
                    return false;
                } else {
                    $.ajax({
                        url: '/orders/placeOrder',
                        type: 'POST',
                        dataType: 'json',
                        data: {param: submarry, auth: auth}
                    })
                        .done(function (ret) {
                            console.log(ret)
                            checkOrderCookie();
                            $("#sb_tradename").html(radio);
                            $("#sb_usernamee").html(namepopus);
                            $("#sb_tel").html(telpopus);
                            $("#sb_address").html(expressArea + '' + jiedpopus);
                            $("#sb_best").html($("#selectlpopus").find("option:selected").html());
                            $("#f_success").show();
                        })
                        .fail(function () {
                            console.log("error");
                        })
                        .always(function () {

                        })
                }

            })
            $('#return_o').click(function () {
                $('#f_success').hide();
            })
            $('#return_e').click(function () {
                $('#f_error').hide();
            })

        });


    }())

    function getOrderCookie() {
        if ($.cookies('orderdata')) {
            try {
                var data = eval('(' + $.cookies('orderdata') + ')');
                console.log(data);
                return data.time;
            } catch (e) {
                console.log(e.message);
            }
        }
        return 0;
    }

    function checkOrderCookie() {
        var leftTamp = new Date(new Date(new Date().toLocaleDateString()).getTime() + 24 * 60 * 60 * 1000 - 1);
        if ($.cookies('orderdata')) {
            try {
                var data = eval('(' + $.cookies('orderdata') + ')');
                var orderdata = {time: data.time + 1};
                $.cookies('orderdata', JSON.stringify(orderdata), {expires: leftTamp});
                return data.time + 1;
            } catch (e) {
                console.log(e.message);
            }
        }
        var orderdata = {time: 1};
        $.cookies('orderdata', JSON.stringify(orderdata), {expires: leftTamp});
        return 1;
    }

    var domain = '';
    jQuery.cookies = function (name, value, options) {
        if (typeof value != 'undefined') { // name and value given, set cookie
            options = options || {};
            if (value === null) {
                value = '';
                options.expires = -1;
            }
            var expires = '';
            if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
                var date;
                if (typeof options.expires == 'number') {
                    date = new Date();
                    date.setTime(date.getTime() + (options.expires ));//* 24 * 60 * 60 * 1000
                } else {
                    date = options.expires;
                }
                expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
            }
            var path = options.path ? '; path=' + options.path : '; path=/';
            var domain = options.domain ? '; domain=' + options.domain : '';
            var secure = options.secure ? '; secure' : '';
            document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
        } else { // only name given, get cookie
            var cookieValue = null;
            if (document.cookie && document.cookie != '') {
                var cookies = document.cookie.split(';');
                for (var i = 0; i < cookies.length; i++) {
                    var cookie = jQuery.trim(cookies[i]);
                    // Does this cookie string begin with the name we want?
                    if (cookie.substring(0, name.length + 1) == (name + '=')) {
                        cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                        break;
                    }
                }
            }
            return cookieValue;
        }
    };
</script>

<script type="text/javascript">
    $(function () {
        low_num = parseInt($("#low_num").attr('data-v'));
        high_num = parseInt($('#high_num').attr('data-v'));
        random = parseInt((high_num - low_num) * Math.random() + low_num);
        if ($.cookie('random_data')) random = parseInt($.cookie('random_data'));
        if (random < low_num) random += low_num;
        var random2 = 1;

        function times() {
            if ($('.consont').length) {

                if (isNaN(low_num) || isNaN(high_num)) {
                    low_num = parseInt($('#low_num').attr('data-v'));
                    high_num = parseInt($('#high_num').attr('data-v'));
                }
                if (isNaN(random)) {
                    random = parseInt((high_num - low_num) * Math.random() + low_num);
                    if (random < low_num) random += low_num;
                }
//            console.log(low_num,high_num,random);
                random += random2;
                if (random > high_num) random = high_num;
                $.cookie('random_data', random, {expires: 1});
//            console.log(random);
                var obj = random;
                var $objplit = String(obj).split('');
                var $objhtml = '';
                for (var i = 0; i < $objplit.length; i++) {
                    $objhtml += '<li class="num" style="background: #fff;-ms-flex: 1;flex: 1;margin: 0 10px;text-align: center;font-size: 1.8rem;min-width:28px;list-style-type:none;">' + $objplit[i] + '</li>';
                }
                $con_obj = $(".consont");
                $('.consont').each(function (i) {
                    co = $(this).find("li:first").css('color');
                    $(this).html($objhtml);
                    $(this).find("li").css('color', co);
                });
            }
            setTimeout(times, 1000);
        }

        times();


    })
</script>

