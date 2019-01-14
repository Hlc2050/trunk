<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<script src="https://cdn.bootcss.com/bootstrap-colorpicker/2.5.1/js/bootstrap-colorpicker.min.js"></script>
<link href="https://cdn.bootcss.com/bootstrap-colorpicker/2.5.1/css/bootstrap-colorpicker.min.css" rel="stylesheet">
<style>
    .colorpicker {
        background-color: #222222;
        border-radius: 5px 5px 5px 5px;
        box-shadow: 2px 2px 2px #444444;
        color: #FFFFFF;
        font-size: 12px;
        position: absolute;
        width: 135px;
    }
</style>
<input type="hidden" value="<?php echo $this->get('num'); ?>" id="num">
<div class="main mbody">
    <div class="mt10">
        <table class="tb3">
            <tr>
                <td colspan="6"></td>
            </tr>
            <tr>
                <td colspan="6">
                    <div id="backtext">
                        <div class="area-bglis"
                             style=" width: 100%;height: 6rem;background: red;display: -ms-flexbox;display: flex; -ms-flex-direction: row;flex-direction: row;flex-wrap: nowrap;-ms-flex-pack: justify;justify-content: space-between;-ms-flex-align: center;align-items: center">
                            <input id="low_num" hidden value="1000"/>
                            <input id="high_num" hidden value="5000"/>
                            <div class="text first" style="width: 18%;text-align: right;font-size: 1.8rem;">已有</div>
                            <ul class="consont"
                                style="margin: 0;padding: 0;width: 57%;text-align: center;display: -ms-flexbox;display: -webkit-box;display: flex;-ms-flex-direction: row;-webkit-box-orient: horizontal;-webkit-box-direction: normal;flex-direction: row;-ms-flex-wrap: nowrap;flex-wrap: nowrap;-ms-flex-pack: justify;-webkit-box-pack: justify;justify-content: space-between;-ms-flex-align: center;-webkit-box-align: center;align-items: center;">
                                <li class="num"
                                    style="background: #fff;-ms-flex: 1;flex: 1;margin: 0 10px;text-align: center;font-size: 1.8rem;min-width:28px;list-style-type:none;">
                                    1
                                </li>
                                <li class="num"
                                    style="background: #fff;-ms-flex: 1;flex: 1;margin: 0 10px;text-align: center;font-size: 1.8rem;min-width:28px;list-style-type:none;">
                                    0
                                </li>
                                <li class="num"
                                    style="background: #fff;-ms-flex: 1;flex: 1;margin: 0 10px;text-align: center;font-size: 1.8rem;min-width:28px;list-style-type:none;">
                                    0
                                </li>
                                <li class="num"
                                    style="background: #fff;-ms-flex: 1;flex: 1;margin: 0 10px;text-align: center;font-size: 1.8rem;min-width:28px;list-style-type:none;">
                                    0
                                </li>
                            </ul>
                            <div class="text lestt" style=" width: 25%;text-align: left;font-size: 1.8rem;">人领取</div>
                        </div>
                        <div class="area-bglis" style="width: 100%; background: red;text-align: center">
                            <a href="#goumai_title">
                                <img src="/static/order/images/receive.png"
                                     style="width: 40%;height: auto;margin: 10px auto;">

                            </a>

                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="alignright">
                    填充色：
                </td>
                <td class="alignright">
                    <div id="fill_color" class="input-group colorpicker-component">

                        <input name="fill_color" type="text"
                               value="red"
                               class="form-control" hidden/>
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </td>
                <td class="alignright">
                    文字色：
                </td>
                <td class="alignright">
                    <div id="text_color" class="input-group colorpicker-component">
                        <input name="text_color" type="text"
                               value="black"
                               class="form-control" hidden/>
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </td>
                <td class="alignright">
                    人数色：
                </td>
                <td class="alignright">
                    <div id="people_color" class="input-group colorpicker-component">
                        <input name="people_color" type="text"
                               value="black"
                               class="form-control" hidden/>
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>数字范围</td>
                <td colspan="4">
                    <input id="low" class="ipt" type="text" value="1000"/>-
                    <input id="high" class="ipt" type="text" value="5000"/>
                </td>
                <td>
                    <input type="button" style="font-size: medium" class="but" onclick="backtext()" value="插入"/>
                </td>
            </tr>

        </table>
    </div>
</div>
<script>
    $(function () {
        $('#fill_color').colorpicker().on('changeColor', function (ev) {
            $('.area-bglis').css('background', ev.color.toHex());
        });
        $('#text_color').colorpicker().on('changeColor', function (ev) {
            $('.text').css('color', ev.color.toHex());
        });
        $('#people_color').colorpicker().on('changeColor', function (ev) {
            $('.num').css('color', ev.color.toHex());
        });
    });

    function backtext() {
        try {
            low_n = $('#low').val();
            high_n = $('#high').val();
            console.log(low_n,high_n)
            if (!low_n || !high_n) {
                alert('必须填写最低最高领取人数');
                return false;
            }
            else if(isNaN(low_n) || isNaN(high_n))
            {
                alert('领取人数必须是数字');
                return false;
            }
            else if(low_n > high_n)
            {
                alert('最高人数要大于最低人数');
                return false;
            }

            $("#low_num").attr('data-v',low_n)
            $("#high_num").attr('data-v',high_n)
            console.log($("#low_num").val())

            var $text = $('#backtext').html()
            var $num = $('#num').val()
            artDialog.opener.addText($text,$num);
            alert('添加成功');
            artDialog.close();
        } catch (e) {
            alert(e.message);
        }
    }

</script>
