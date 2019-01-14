<style>
    .or_form {
        margin: 10px 0;
        padding: 10px;
        color: #6d6d6d;
        background-color: #FFF
    }

    .or_form_1 {
        padding: 7px;
        line-height: 150%
    }

    .or_form_1 strong {
        color: #2d2d2d;
    }

    .or_form_2 {
        color: #2d2d2d;
        margin: 15px 10%;
        font-size: 120%;
        font-weight: 500;
    }

    .or_form_3 {
        color: #6b6b6b;
        margin: 10px 5% 8px 10%;
    }

    .or_form_3 input[type="text"] {
        width: 75%;
        height: 25px;
        border: solid #c4c4c4 1px;
    }

    .or_form_3 span {
        color: #f76e5e;
        margin-left: 10px;
    }

    .or_form_captcha {
        color: #6b6b6b;
        margin: 0 5% 8px 10%;
    }

    .or_form_captcha input[type="text"] {
        width: 30%;
        height: 25px;
        border: solid #c4c4c4 1px;
    }

    .or_form_captcha span {
        color: #f76e5e;
        margin-left: 10px;
    }

    .or_form_4 {
        color: #f76e5e;
        margin: 0 0 8px 22%;
    }

    .or_form_5 {
        position: relative;
        margin-left: 24%;
        width: 45%;
        margin-bottom: 15px;
    }

    .or_form_5 span {
        width: 12%;
        background-color: # #6d6d6d;
        height: 26px;
        color: #FFF;
        line-height: 26px;
        display: inline-block;
        position: absolute;
        z-index: 99;
        top: 1px;
        right: 1px;
        text-align: center;
        font-size: 140%;
    }

    .or_form_5 select {
        border: solid #828282 1px;
        width: 100%;
        height: 28px;
    }

    #anniu {
        margin: 15px 25%;
        background-color: #ff0000;
        color: #807e7e;
        height: 30px;
        width: 35%;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        border-radius: 5px;
        border: solid 1px #f1f1f1;
    }

    }
    .order {
        max-width: 480px;
        width: 100%; /*background-color:#d5d5d5;*/
        margin: 0 auto;
        overflow: hidden
    }

    .or_form_5 li {
        float: left
    }

    .nav_title {
        /*background-color: #C20000;*/
        color: #C20000;
        font-size: 18px;
        font-weight: bold;
        height: 40px;
        text-align: center;
        line-height: 40px;
        overflow: hidden;
    }

    .package {
        margin: 15px 10%;
        font-size: 100%;
        font-weight: 500;
    }
    @media only screen and (min-width:320px) {
        footer {height:3.5em; line-height: 1.3rem;}
        .fonzize{font-size: 1rem; margin-top: 25px;}
        button{
            font-size: 0.4rem;
            font-weight: bold;}
    }
    @media only screen and (min-width: 700px) {
        footer {height:5em; line-height: 5rem;}
        .fonzize{font-size: 2rem;}
        button{font-size: 1.5rem;}
    }
    footer { min-width:320px; background: red; max-width:640px; position:fixed;
        display: flex ;
        -moz-flex-direction: row ;
        -webkit-flex-direction: row ;
        -o-flex-direction: row ;
        flex-direction: row ;
        flex-wrap: wrap ;
        -moz-flex-wrap: wrap ;
        -webkit-flex-wrap: wrap ;
        -o-flex-wrap: wrap ;
        justify-content: space-between ;
        -moz-justify-content: space-between ;
        -webkit-justify-content: space-between ;
        -o-justify-content: space-between ;
        align-items: center ;
        -moz-align-items: center ;
        -webkit-align-items: center ;
        -o-align-items: center ;
        align-content: center ;
        -moz-align-content: center ;
        -webkit-align-content: center ;
        -o-align-content: center ;
        color: white;
        bottom:0; margin:0 auto; left:0; right:0; z-index:99999;}
    footer div{ height: 100%; display: table-cell; vertical-align: middle;}
    footer div:nth-of-type(1) img{ position: absolute; left: 15%; bottom:0;width: 82%;  }
    footer div:nth-of-type(1){flex: 0.76; height:100%; width:15%;  position: relative; }
    footer div:nth-of-type(2){flex: 2.5;width: 60%; height: 100%;}
    footer div:nth-of-type(3){flex: 1;}
    footer div:nth-of-type(3) button{ margin-top: 5%;  border-radius: 6px; display: block; width: 90%;height: 80%; background: #f0ff00; border: none; outline: none; color: #ff000c; }


</style>
<script src="<?php echo  $page['info']['css_cdn_url']; ?>/static/lib/layer_mobile/layer.js"></script>

<link media="all" href="<?php echo  $page['info']['css_cdn_url']; ?>/static/lib/layer_mobile/need/layer.css?16" type="text/css" rel="stylesheet">

<div class="order" id="order">
    <div class="nav_title"
         style="color: <?php echo $page['info']['order']['title_color'] ?>"><?php echo $page['info']['order']['order_title'] ?></div>
    <form>
        <div class="or_form">
            <input id="promotion_id" name="promotion_id" value="<?php echo $page['info']['promotion_id']; ?>" hidden>
            <input id="wechat_id" name="wechat_id" type="text" hidden/>
            <input id="package_price" name="package_price" value="<?php echo $page['info']['order']['packages'][0]['package_price'] ?>" type="text" hidden/>


            <div style="margin: 15px 10%;font-size: 100%;font-weight: 500; width:90%; height: 80px">
                <div style=" float: left;width: 20%;">选择套餐:</div>
                <div style=" float: left;width: 80%;">
                    <?php foreach ($page['info']['order']['packages'] as $k => $v) { ?>
                        <input name="package_name" onclick="changebottom(this)" <?php echo $k == 0 ? "checked" : " "; ?>
                               type="radio" value="<?php echo $v['package_name'] ?>"
                               data-price="<?php echo $v['package_price'] ?>"><?php echo $v['package_name'] ?><br/>
                    <?php } ?>
                </div>

                <div style=" float: left;width: 100%;margin-top: 10px;"><span>价格:&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    <span style="color: <?php echo $page['info']['order']['price_color'] ?>"
                          id="package_prices"><?php echo $page['info']['order']['packages'][0]['package_price'] == 0 ? '免费' : $page['info']['order']['packages'][0]['package_price'] . "元" ?></span>
                </div>
            </div>
            <div class="or_form_3"> 姓名：<input name="realName" type="text" value="" id="realName"><span>*</span></div>
            <div class="or_form_3"> 电话：<input name="mobile" type="text" value="" id="mobile"><span>*</span></div>
            <div class="or_form_3"> 地址：<input name="address" type="text" value="" id="address"><span>*</span></div>
            <!--            <div class="or_form_3" style="color:red;"><b>*温馨提示：同一地址限领一份</b></div>-->
            <!--<div class="or_form_4">“老用户只需填写电话”</div>-->
            <div class="or_form_3" style="margin:10px 10%; color:#585858;">方便接听电话时间:</div>

            <div class="or_form_5">
                <span>▼</span>
                <select name="best_time">
                    <option value="-1" selected="selected">请选择...</option>
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
                    <option value="0">随时可以联系我</option>
                </select>
            </div>
            <span id="wait"></span>
            <input style="background-color: <?php echo $page['info']['order']['obtn_color'] ?>" type="button"
                   onclick="checkInput()" id="anniu" value="提交">
        </div>
    </form>

</div>
<?php if ($page['info']['order']['is_suspend'] == 1) { ?>
    <footer style="background-color: <?php echo $page['info']['order']['bottom_color']; ?>">
        <div><img src="<?php echo $page['info']['order']['suspend_img']; ?>"></div>
        <div class="fonzize"
             style="color: <?php echo $page['info']['order']['suspend_color']; ?>"><?php echo $page['info']['order']['suspend_text']; ?></div>
        <div>
            <a href="#order" style="text-decoration:none">
                <button type="button" style="color:<?php echo $page['info']['order']['stext_color']; ?>;background-color: <?php echo $page['info']['order']['sbtn_color']; ?>"><?php echo $page['info']['order']['sbtn_text']; ?></button>
            </a>
        </div>
    </footer>
<?php } ?>
<script>
    function changebottom(e) {
        var price = $(e).attr('data-price')
        var t = price + '元';
        if (price == 0) t = '免费'
        $('#package_prices').html(t)
        $('#package_price').val(price)

    }

    function checkInput() {
        var d = {};
        var t = $('form').serializeArray();
        $.each(t, function () {
            d[this.name] = this.value;
        });
        if (d['realName'] == '' && d['mobile'] == '' && d['address'] == '') {
            layer.open({
                content: '请准确填写订单信息，方便尽快送货',
                btn: '好的',
                shadeClose: false
            });
            return 0;
        }
        if (d['realName'] == '') {
            layer.open({
                content: '请正确填写您的姓名，方便客服联系',
                btn: '好的',
                shadeClose: false
            });
            return 0;
        }
        if (d['mobile'] == '' || !d['mobile'].match(/^(((13[0-9]{1})|(14[0-9]{1})|(17[0]{1})|(15[0-3]{1})|(15[5-9]{1})|(18[0-9]{1}))+\d{8})$/)) {
            layer.open({
                content: '请正确填写您的手机号码，方便客服联系',
                btn: '好的',
                shadeClose: false
            });
            return 0;
        }
        if (d['address'] == '') {
            layer.open({
                content: '请填写您的收货地址，方便快递送货',
                btn: '好的',
                shadeClose: false
            });
            return 0;
        }

        var alen = d['address'].replace(/[^\x00-\xff]/g, "aa").length;
        if (alen < 26) {
            layer.open({
                content: '您的收货地址太过简单请重新填写，方便快递送货',
                btn: '好的',
                shadeClose: false
            });
            return 0;
        }
//        var orderdata = {time: 0};
//        $.cookies('orderdata', JSON.stringify(orderdata), {expires: 10});
        var time = getOrderCookie();
        console.log(time);
        if (time < 5) {
            jQuery.ajax({
                'type': 'POST',
                'url': '/orders/placeOrder',
                'data': d,
                'cache': false,
                'success': function (ret) {
                    data = JSON.parse(ret);
                    console.log(data);
                    if (data['status'] != 1) {//图文预览下单或者失败
                        layer.open({
                            content: "<img style='width: 25%;margin-top: -25px;' src='/static/front/images/cg.png'/>" +
                            "<div><span style='color: red'>" + data['msg'] + "</span></div>"
                            , btn: '好的',
                            shadeClose: false
                        });

                    } else if (data['status'] == 0) {//下单成功
                        layer.open({
                            content: "<img style='width: 25%;margin-top: -25px;' src='/static/front/images/sb.png'/>" +
                            "<div><span style='color: red'>" + data['msg'] + "</span></div>"
                            , btn: '好的',
                            shadeClose: false
                        });
                    } else if (data['status'] == 1) {//下单成功
                        //缓存，跳转到新的页面
                        layer.open({
                            content: "<img style='width: 25%;margin-top: -25px;' src='/static/front/images/cg.png'/>" +
                            "<div><span style='font-weight: bold;'>您的信息提交成功!</span></div>" +
                            "<div><span style='color: red'><?php echo $page['info']['order']['success_info']?></span></div>"
                            , btn: '好的',
                            shadeClose: false
                        });
                        checkOrderCookie();
                    }

                }
            });
        }

    }
    function getOrderCookie() {
        if ($.cookies('orderdata')) {
            try {
                var data = eval('(' + $.cookies('orderdata') + ')');
                if (data.time >= 5) {
                    //loading带文字
                    layer.open({
                        type: 2
                        , content: '您已提交订单。请耐心等待...'
                        , time: 3
                    });
                    $('#wait').html('您已提交订单。请耐心等待...');
                    $('#wait').css('margin-left', '15%');
                    $('#anniu').hide();
                }
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