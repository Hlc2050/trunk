<!--///////////////////////////////////////////////////////////////////////////////////////////////-->
<link rel="stylesheet" href="<?php echo $page['info']['css_cdn_url']; ?>/static/order/css/2018.4.8.min.css">
<style>


    .dxbox{
        line-height: 1.5em;
        float: left;
        display: inline;
        overflow: hidden;
    }


    .red {
        color: #F00;
    }

    .chanpin label {
        border: 1px #ccc solid;
        padding: 0px 13px;
        float: left;
        margin-right: 7px;
        display: inline;
        margin-bottom: 6px;
        overflow: hidden;
        position: relative;
        cursor: pointer;
        border-radius: 2px;
        line-height: 33px;
        vertical-align: middle;
        color: #000000;
        font-size: 14px;
        background: #ffffff;
        transition: all .9s;
        -webkit-transition: all .9s;
    }

    .dxbox label {
        margin-right: 4px;
        cursor: pointer;
    }

    .chanpin .now, .chanpin .now:hover {
        border: 1px solid #ccc;
        color: #ffffff;
        text-shadow: 0 0 0 #fff;
        background: <?php  echo $page['info']['order']['price_color'];?>;
        border-radius: 10px;
    }

    .now img {
        border-top-right-radius: 15px;
    }

    .tj_img {
        border-width: 0px;
        position: absolute;
        right: -2px;
        top: -2px;
        width: 29px;
        height: 29px;
    }

    .chanpin label:hover {
        border: 1px solid #f66;
        text-shadow: 0 0 0 #fff;
    }

    .chanpin label {
        border: 1px #ccc solid;
        padding: 0px 13px;
        float: left;
        margin-right: 7px;
        display: inline;
        margin-bottom: 6px;
        overflow: hidden;
        position: relative;
        cursor: pointer;
        border-radius: 2px;
        line-height: 33px;
        vertical-align: middle;
        color: #000000;
        font-size: 14px;
        background: #ffffff;
        transition: all .9s;
        -webkit-transition: all .9s;
    }
</style>

<?php if ($page['info']['order']['is_carousel'] == 1) { ?>
    <div class="content_title overall_color " id="carousel"><span class="font_color" style="color:white;">最新成功订单</span>
    </div>
    <div class="contenptp" id="contenptp">

    </div>
    <div class="contenpt" id="callboard">
        <ul id="customer">
            <li>
                <div class="licont">
                    <div class="title">
                        刘**【178****9393】
                        <span>平顶山市</span>

                    </div>
                    <div
                            class="context"><?php echo $page['info']['order']['packageNames'][array_rand($page['info']['order']['packageNames'])] ?>
                        <span>30分钟前</span>
                    </div>
                </div>
            </li>
            <li>
                <div class="licont">
                    <div class="title">
                        黄**【178****8046】
                        <span>鞍山</span>

                    </div>
                    <div
                            class="context"><?php echo $page['info']['order']['packageNames'][array_rand($page['info']['order']['packageNames'])] ?>
                        <span>10分钟前</span>
                    </div>
                </div>
            </li>
            <li>
                <div class="licont">
                    <div class="title">
                        刘**【178****9937】
                        <span>厦门</span>

                    </div>
                    <div
                            class="context"><?php echo $page['info']['order']['packageNames'][array_rand($page['info']['order']['packageNames'])] ?>
                        <span>1分钟前</span>
                    </div>
                </div>
            </li>
            <li>
                <div class="licont">
                    <div class="title">
                        刘**【178****9937】
                        <span>湖州</span>

                    </div>
                    <div
                            class="context"><?php echo $page['info']['order']['packageNames'][array_rand($page['info']['order']['packageNames'])] ?>
                        <span>1分钟前</span>
                    </div>
                </div>
            </li>
            <li>
                <div class="licont">
                    <div class="title">
                        李**【178****9937】
                        <span>哈尔滨</span>
                    </div>
                    <div
                            class="context"><?php echo $page['info']['order']['packageNames'][array_rand($page['info']['order']['packageNames'])] ?>
                        <span>8分钟前</span>
                    </div>
                </div>
            </li>
        </ul>
    </div>
<?php } ?>
<!--///////////////////////////////////////////////////////////////////////////////////////////////-->
<div class="content_title overall_color "><span class="font_color"
                                                style="color:white;"><?php echo $page['info']['order']['order_title'] ?></span>
</div>

<!--弹出购买窗口-->

<div class="popus_box" id="popus_goumai">
    <div class="conter_box">
        <div class="middle_box">
            <div class="title overall_color font_color"> 立即订购 <img
                        src="<?php echo $page['info']['cdn_url']; ?>/static/order/images/close.png"
                        class="icon" id="closeicon"></div>
            <form>
                <input id="oweixinid" name="oweixinid" type="text" hidden/>
                <input id="csid" name="csid" type="text" hidden/>
                <input id="wechat_id" name="wechat_id" type="text" hidden/>
                <!--商品选择框 start-->
                <dl>
                    <dt class="width5rem" style="vertical-align: top">选择套餐<span>*</span></dt>
                    <dd>
                        <div class="dxbox red chanpin not3chanpin" id="popus_goumai">
                            <?php foreach ($page['info']['order']['packages'] as $k => $v) { ?>
                                <label class="<?php echo $k == 0 ? "now" : " "; ?>" style="width: auto">
                                    <input type="radio" name="goods_sku_id" id="a0" style="display: none" value="41"
                                           datan="<?php echo $v['package_name']; ?>"
                                           dataId="<?php echo $v['package_id'] ?>"
                                           dataPrice="<?php echo $v['package_price'] ?>" <?php echo $k == 0 ? "checked" : " "; ?>>
                                    <?php echo $v['package_name']; ?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $v['package_price'] ?>&nbsp;
                                    <?php if ($v['recommend'] == 1) {
                                        ?>
                                        <img class="tj_img"
                                             src="<?php echo $page['info']['cdn_url']; ?>/static/order/images/tj.png">
                                    <?php } ?>
                                </label>
                            <?php } ?>
                        </div>

                    </dd>
                </dl>
                <!--商品选择框 end-->
                <dl>
                    <dt>姓名<span>*</span></dt>
                    <dd><input type="text" placeholder="请输入您的姓名" id="namepopus"></dd>
                </dl>
                <dl>
                    <dt>手机<span>*</span></dt>
                    <dd><input type="text" placeholder="请输入您的手机号码" id="telpopus"
                               onKeyUp="if(this.value.length>11){this.value=this.value.substr(0,11)};this.value=this.value.replace(/[^\d]/g,'');">
                    </dd>
                </dl>
                <dl>
                    <dt class="width5rem">选择地区：<span>*</span></dt>
                    <dd style="height: 1.5rem;">
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
                    <dt>留言</dt>
                    <dd><input type="text" placeholder="留言" id="liuyanpopus"></dd>
                </dl>
                <dl>
                    <dt class="width5rem">支付方式</dt>
                    <?php
                    $payments = str_split($page['info']['payment'], 1);
                    $k = 0;
                    foreach ($payments as $key => $value) {
                        if (1 == $value) {
                            $k++;
                            ?>
                            <dd>
                                <label>
                                    <input type="radio"
                                           value="<?php echo $page['info']['payments'][$key]['value']; ?>" <?php echo 1 == $k ? 'checked' : ''; ?>
                                           name="payment"><?php echo $page['info']['payments'][$key]['txt']; ?>
                                </label>
                            </dd>
                        <?php }
                    } ?>

                </dl>
                <dl>
                    <button type="button" id="submitpopu"
                            class="overall_color font_color"><?php echo $page['info']['order']['obtn_text'] ? $page['info']['order']['obtn_text'] : "确认提交"; ?></button>
                </dl>
            </form>
        </div>
    </div>
</div>
<!--///////////////////////////////////////////////////////////////////////////////////////////////-->
<!--<div class="shuomin">温馨提示：订单提交后会有工作人员与您联系！保持电话畅通！祝您生活愉快！</div>-->
<!--<img src="--><?php //echo $page['info']['cdn_url']; ?><!--/static/order/images/icons.jpg" class="icons_img">-->
<!--///////////////////////////////////////////////////////////////////////////////////////////////-->
<?php echo $page['info']['order']['order_tips'] ?>


<!--底部图片-->
<?php if ($page['info']['order']['is_suspend'] == 1) { ?>
    <div class="foot_fieximg" id="foot_fieximg">
        <img src="<?php echo $page['info']['cdn_url'] . $page['info']['order']['suspend_img']; ?>"/>
    </div>
<?php } ?>
<?php if ($page['info']['order']['is_dobber'] == 1) { ?>
    <div class="button_fieximg" id="button_fieximg">
        <img src="<?php echo $page['info']['cdn_url'] . $page['info']['order']['dobber_img']; ?>">
    </div>
<?php } ?>

<!--///////////////////////////////////////////////////////////////////////////////////////////////-->

<div class="popus_box popus_boxco retrnon" id="success_info" style="display: none">
    <div class="conter_quren">
        <div class="middle_quren">
            <div class="icon_box"><img
                        src="<?php echo $page['info']['cdn_url']; ?>/static/order/images/popus.2017.7.4.png"></div>
            <div class="title">订单提交成功</div>
            <div class="content_txt"><?php echo $page['info']['order']['success_info']; ?></div>
            <button type="button" class="retrnon" id="retrnon">返回</button>
        </div>
    </div>
</div>
<div class="popus_box popus_boxco retrnon" style="z-index: 90001 !important;display: none" id="loading">
    <div class="conter_quren">
        <div style="position: absolute;left: 42%;top: 25%;">
            <img style="width: 64px" src="<?php echo $page['info']['cdn_url']; ?>/static/order/images/Spinner.gif">
        </div>
    </div>
</div>
<!--///////////////////////////////////////////////////////////////////////////////////////////////-->
<div class="popus_box popus_boxco" style="display: none" id="returnpopus">
    <div class="conter_quren">
        <div class="middle_quren">
            <div class="icon_box"><img
                        src="<?php echo $page['info']['cdn_url']; ?>/static/order/images/popus.2017.7.4f.png"></div>
            <div class="content_txt" style="font-size: 1.2rem; text-align: center">您填写的信息有误<br>
                请检查后再次提交
            </div>
            <button type="button" id="returnpopus_btn">返回上一步</button>
        </div>
    </div>
</div>
<!--///////////////////////////////////////////////////////////////////////////////////////////////-->
<div class="popus_box popus_boxco" style="display: none" id="returnfrequently">
    <div class="conter_quren">
        <div class="middle_quren">
            <div class="icon_box"><img
                        src="<?php echo $page['info']['cdn_url']; ?>/static/order/images/popus.2017.7.4f.png"></div>
            <div class="content_txt" style="font-size: 1.2rem; text-align: center">您的订单已提交成功<br>
                请耐心等待客服联系
            </div>
            <button type="button" id="returnpopus_btn">返回上一步</button>
        </div>
    </div>
</div>
<!--///////////////////////////////////////////////////////////////////////////////////////////////-->
<div class="popus_box popus_boxco" id="msgrepopus" style="display: none">
    <div class="conter_quren">
        <div class="middle_quren">
            <div class="top_title" style="color: <?php echo $page['info']['order']['overall_color'] ?>">请确认您的订单信息</div>
            <table>
                <tr>
                    <td>商品</td>
                    <td id="sb_tradename"></td>
                </tr>
                <tr>
                    <td>姓名</td>
                    <td id="sb_usernamee"></td>
                </tr>
                <tr>
                    <td>手机</td>
                    <td id="sb_tel"></td>
                </tr>
                <tr>
                    <td>收货地址</td>
                    <td id="sb_address"></td>
                </tr>
                <tr>
                    <td>支付金额</td>
                    <td id="sb_money"></td>
                </tr>
                <tr>
                    <td>支付方式</td>
                    <td id="sb_payment"></td>
                </tr>
            </table>
            <div class="button_box">
                <button type="button" id="returnpo_sb">返回</button>
                <button type="button" id="sb_returnpo" class="overall_color font_color">确认</button>
            </div>
        </div>
    </div>

</div>


<script src="<?php echo $page['info']['cdn_url']; ?>/static/order/js/jquery.area.js"></script>

<script>
    (function () {
        $(document).ready(function () {
            /*整体颜色*/
            $(".overall_color").css('background', '<?php echo $page['info']['order']['overall_color'] ?>');
            $(".font_color").css('color', '<?php echo $page['info']['order']['font_color'] ?>');
            $(".now").css('background', '<?php echo $page['info']['order']['price_color'] ?>');
            $(".now").css('color', '<?php echo $page['info']['order']['goods_color'] ?>');

            /*滚动消息*/
            function scroll() {

                $(".contenpt ul").animate({"margin-top": "-5rem"}, function () {

                    $(".contenpt ul li:eq(0)").appendTo($(".contenpt ul"));

                    $(".contenpt ul").css({"margin-top": 0});

                })

            }

            setInterval(scroll, 2000);
            var submarry = [];
            /*点击底部图片弹窗*/
            $('#foot_fieximg').click(function () {
                $('#popus_goumai').addClass('popus_boxco');
                $('.conter_box').addClass('conter_boxco');
            });
            /*弹窗关闭按钮*/
            $('#closeicon').click(function () {
                $('#popus_goumai').removeClass('popus_boxco');
                $('.conter_box').removeClass('conter_boxco');
            });
            /*左侧点击弹窗*/
            $('#button_fieximg').click(function () {
                takes = 1;
                $('#popus_goumai').addClass('popus_boxco');
                $('.conter_box').addClass('conter_boxco');
            });
            /*购买信息提交*/
            $(document).on('click', '#submitpopu', function () {
                var telphone = /^(((13[0-9]{1})|(15[0-9]{1})|(14[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
                var strs = new Array();
                var radio = $('#popus_goumai :radio:checked').attr('datan');//商品选择套餐
                var package_id = $('#popus_goumai :radio:checked').attr('dataId');//商品选择套餐
                var price = $('#popus_goumai :radio:checked').attr('dataPrice');//商品单价
                var namepopus = $('#namepopus').val();//用户姓名
                var telpopus = $('#telpopus').val();//用户电话
                var expressArea = $('#expressArea').html().replace(/&gt;/g, '');//用户选择的地区
                var jiedpopus = $('#jiedpopus').val();//用户街道
                var selectlpopus = $("#selectlpopus").find("option:selected").val();//用户方便接电话时间
                var liuyanpopus = $('#liuyanpopus').val();//用户留言
                var region = "";//用户地区
                var promotionid = "<?php echo $page['info']['promotion_id'] ? $page['info']['promotion_id'] : 0;?>";//推广id
                var channelid = "<?php echo $page['info']['channel_id'];?>";//渠道id
                var partnerid = "<?php echo $page['info']['partner_id'];?>";//合作商id
                var articleid = "<?php echo $page['info']['origin_template_id'];?>";//图文id
                var article_code = "<?php echo $page['info']['article_code'];?>";//图文id
                var orderid = "<?php echo $page['info']['order']['id'];?>";//下单模板id
                var tg_uid = "<?php echo $page['info']['promotion_staff_id'];?>";//推广人员
                var wechat_id = $('#wechat_id').val();//
                var weixinid = $('#oweixinid').val();//
                var csid = $('#csid').val();//
                console.log(tg_uid);
//                console.log(expressArea);
                strs = expressArea.split(" ");
                for (i = 0; i < strs.length; i++) {
                    if (strs.length == 3) {
                        region = strs[0];
                    } else {
                        region = strs[2];
                    }
                }
                if ($('#expressArea')[0].innerText == '省 > 市 > 区/县') {
                    $('#expressArea').css('border-color', 'red')
                } else {
                    $('#expressArea').css('border-color', '#666')
                }
                if (telpopus == '') {
                    $('#telpopus').css('border-color', 'red')
                }
                else {
                    $('#telpopus').css('border-color', '#cbcbcb')
                }
                if (namepopus == '') {
                    $('#namepopus').css('border-color', 'red')
                }
                else {
                    $('#namepopus').css('border-color', '#cbcbcb')
                }
                if (jiedpopus == '') {
                    $('#jiedpopus').css('border-color', 'red')
                }
                else {
                    $('#jiedpopus').css('border-color', '#cbcbcb')
                }
                if (radio == '' ||
                    price == '' ||
                    namepopus == '' ||
                    telpopus == '' ||
                    jiedpopus == '' ||
                    $('#expressArea')[0].innerText == '省 > 市 > 区/县') {
                    return false
                } else {
                    $('#popus_goumai').hide();
                    if (!telphone.exec(telpopus)) {
                        $("#returnpopus").show();
                    } else {
                        $("#sb_tradename").html(radio);
                        $("#sb_usernamee").html(namepopus);
                        $("#sb_tel").html(telpopus);
                        $("#sb_address").html(expressArea + '' + jiedpopus);
                        $("#sb_money").html(price);
                        $("#sb_payment").html('货到付款');

                        submarry.push(radio, price, namepopus, telpopus, expressArea, package_id, jiedpopus, selectlpopus, liuyanpopus,
                            promotionid, channelid, partnerid, articleid, orderid, wechat_id, article_code, weixinid, csid, tg_uid)
                        $("#returnpopus").hide();
                        $("#msgrepopus").show();
                    }
                }


            });

            $(document).on('click', '.retrnon', function () {
                $('#returnpopus').hide();
                $('#returnfrequently').hide();
                $('#success_info').hide();
                setTimeout(function () {
                    $("#contenptp").hide()
                }, 5000)
            });
            /*错误窗口返回上一步*/
            $(document).on('click', '#returnpopus_btn', function () {
                $('#returnpopus').hide();
                $('#popus_goumai').show();
                $('#returnfrequently').hide();

            });
            /*提交窗口返回按钮*/
            $(document).on('click', '#returnpo_sb', function () {

                $('#msgrepopus').hide();
                $('#popus_goumai').show();

            });
            /*提交窗口提交按钮*/
            $(document).on('click', '#sb_returnpo', function () {
                //

                var time = getOrderCookie();
                var auth = 'zk2bu!@#';//
                if (time >= 5) {
                    $('#msgrepopus').hide();
                    $('#returnfrequently').show();
                    return false;
                } else {
                    $("#loading").show();
                    $.ajax({
                        url: '/orders/placeOrder',
                        type: 'POST',
                        dataType: 'json',
                        data: {param: submarry, auth: auth}//submarry数组八个字段
                    })
                        .done(function (ret) {
                            var str2 = submarry[3].substr(0, 3) + "****" + submarry[3].substr(7);
                            var temp = "";
                            if (submarry[2].length > 0) {

                                for (var i = 0; i < submarry[2].length; i++) {
                                    if (i == 0) {
                                        temp = temp + submarry[2].substring(i, i + 1);
                                    }
                                    else {
                                        temp = temp + "*";
                                    }
                                }
                            }
                            $("#loading").hide();
                            $("#contenptp").show();
                            $("#contenptp").html(
                                "<ul ><li> <div class='licont'><div class='title'>" +

                                temp.substr(0, 3) + '【' + str2 +

                                "】<img style='display: inline-block;height: 16px;width: 16px;padding: 0px;margin: 0px;margin-left: -6px;' src='/static/order/images/itant.gif' /><span>" + submarry[5] + "</span>" +

                                "<div class='context'>" + submarry[0] + "<span>1分钟前</span>" +

                                "</div></li></ul>"
                            );

                            location.href = "#carousel";
                            $('#msgrepopus').hide();
                            $("#loading").hide();
                            $('#popus_goumai').removeClass('popus_boxco');
                            $('.conter_box').removeClass('conter_boxco');
                            $('#popus_goumai').show();
                            $('#success_info').show();

//                                $('#expressArea').html('省  市  区/县');
//                                $('#namepopus').val('');//用户姓名
//                                $('#telpopus').val('');//用户电话
//                                $('#jiedpopus').val('');//用户街道
//                                $('#liuyanpopus').val('');//用户留言
                            submarry = [];
                            checkOrderCookie();


                        })
                        .fail(function () {
                            console.log("error");
                        })
                        .always(function () {
                            console.log("complete");
                        });

                }
            });
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

    // 控制商品选择框方法
    function not3chanpin() {
        $(".not3chanpin label").bind("click", function () {
            var o = $(this);
            if (!o.hasClass("now")) {
                $(".now").css('background', '#ffffff');
                $(".now").css('color', '#000000');
                $(".not3chanpin label").removeClass("now");
                o.addClass("now");
                o.css('background', '<?php echo $page['info']['order']['price_color'] ?>');
                o.css('color', '<?php echo $page['info']['order']['goods_color'] ?>');
            }
        });
    }

    not3chanpin();
</script>