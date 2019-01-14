<?php require(dirname(__FILE__) . "/../common/head.php"); ?>

<style>
    .searchsBox a:hover {
        background: #eee;
    }

</style>

<script>
    $(document).ready(function () {
        $('.channel_name').on('keyup focus', function () {
            $('.searchsBox').show();
            var myInput = $(this);
            var key = myInput.val();
            var postdata = {search_type: 'keys', search_txt: key};
            $.getJSON('<?php echo $this->createUrl('partner/channelIndex') ?>?jsoncallback=?', postdata, function (reponse) {
                try {
                    if (reponse.state < 1) {
                        alert(reponse.msg);
                        return false;
                    }
                    var html = '';
                    console.log(reponse);
                    for (var i = 0; i < reponse.data.list.length; i++) {
                        html += '<a href="javascript:void(0);" data-id="' + reponse.data.list[i].id + '" data-channel_code="' + reponse.data.list[i].channel_code + '" data-partnerId="' + reponse.data.list[i].partner_id + '" ' +
                            'data-channelName="' + reponse.data.list[i].channel_name + '"  ' +
                            'data-partnerName="' + reponse.data.list[i].partnerName + '"  ' +
                            'data-business_type="' + reponse.data.list[i].business_type + '"  ' +
                            'data-bname="' + reponse.data.list[i].bname + '"  ' +
                            'onmouseDown="getTipsValue(this);"   style="display:block;font-size:12px;padding:2px 5px;">' + reponse.data.list[i].channel_name + '(' + reponse.data.list[i].partnerName + ')</a>';
                    }
                    var s_height = myInput.height();
                    var top = myInput.offset().top + s_height;
                    var left = myInput.offset().left;
                    var width = myInput.width();
                    $('.searchsBox').remove();
                    $('body').append('<div class="searchsBox" style="position:absolute;top:' + top + 'px;left:' + left + 'px;background:#fff;z-index:2;border:1px solid #ccc;width:' + width + 'px;">' + html + '</div>');
                } catch (e) {
                    alert(e.message)
                }
            });
            myInput.blur(function () {
                $('.searchsBox').hide();
            })
        });
    });
    function getTipsValue(ele) {
        var myobj = $(ele);
        var id = myobj.attr('data-id');
        var channel_name = myobj.attr('data-channelName');
        var channel_code = myobj.attr('data-channel_code');
        var partner_id = myobj.attr('data-partnerId');
        var partner_name = myobj.attr('data-partnerName');
        var business_type = myobj.attr('data-business_type');
        var bname = myobj.attr('data-bname');
        $('.channel_name').val(channel_name);
        $('.channel_code').html(channel_code);
        $('#channel_id').val(id);
        $('.partner_id').val(partner_id);
        $('.partner_name').html(partner_name);
        $('.business_type').val(business_type);
        $('.bname').html(bname);
    }

</script>
<div class="main mhead">
    <div style="    font-size: large; font-weight: bold;color: dimgrey;">图文信息</div>
</div>
<div class="main mbody">
    <form method="post"
          action="<?php echo $page['info']['id'] ? $this->createUrl('articleDelMessage/edit') : $this->createUrl('material/addMessage'); ?>">
        <?php if (!$page['info']['id']){ ?>
        <input type="hidden" id="article_id" name="article_id"
               value="<?php echo $page['info']['article_id']; ?>" size="30"/>
        <table class="tb3">
            <tr>
                <td style="width: 90px;color: dimgrey;">&nbsp;渠道：</td>
                <td>
                    <input type="text" class="ipt channel_name" style="width: 199px;" name="channel_name"
                           value="" size="30"/>
                    <input type="hidden" class="ipt channel_id" id="channel_id" name="channel_id"
                           value="<?php echo $page['info']['id']; ?>" size="30"/>
                </td>
            </tr>
            <tr>
                <td style="width: 90px;color: dimgrey;">&nbsp;合作商：</td>
                <td>
                    <span class="partner_name"><?php $data = Partner::model()->findByPk($page['info']['partner_id']);
                        echo $data['name']; ?></span>
                    <input type="hidden" class="ipt partner_id" name="partner_id"
                           value="<?php echo $page['info']['partner_id']; ?>"/>
                </td>
            </tr>

            <tr>
                <td style="width: 90px;color: dimgrey;">&nbsp;业务类型：</td>
                <td class="alignleft">
                      <span class="bname"><?php echo BusinessTypes::model()->findByPk($page['info']['business_type'])->bname; ?></span>
                    <input type="hidden" class="ipt business_type" name="business_type"
                           value="<?php echo $page['info']['business_type']; ?>"/>
                    <?php
//                    $businessTypes = Dtable::toArr(BusinessTypes::model()->findAll());
//                    echo CHtml::dropDownList('business_type', $page['info']['business_type'], CHtml::listData($businessTypes, 'bid', 'bname'),
//                        array(
//                            'empty' => '选择业务类型',
//                        )
//                    );
                    ?>
                </td>

            </tr>
            <?php }else{ ?>
            <input type="hidden" id="id" name="id"
                   value="<?php echo $page['info']['id']; ?>"/>
            <table class="tb3">
                <tr>
                    <td style="width: 90px;color: dimgrey;">&nbsp;渠道：</td>
                    <td>
                        <?php echo Channel::model()->findByPk($page['info']['channel_id'])->channel_name; ?>
                    </td>
                </tr>
                <tr>
                    <td style="width: 90px;color: dimgrey;">&nbsp;合作商：</td>
                    <td>
                        <?php echo Partner::model()->findByPk($page['info']['partner_id'])->name; ?>
                    </td>
                </tr>

                <tr>
                    <td style="width: 90px;color: dimgrey;">&nbsp;业务类型：</td>
                    <td>
                    <?php echo BusinessTypes::model()->findByPk($page['info']['business_type'])->bname; ?>
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <td style="width: 90px;color: dimgrey;">&nbsp;上线日期：</td>
                    <td>
                        <input type="text" class="ipt" id="online_date" name="online_date"
                               value="<?php echo date('Y-m-d H:i', $page['info']['online_date']?$page['info']['online_date']:time()) ?>"
                               onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'})"/>
                    </td>
                </tr>
                <tr>
                    <td style="width: 90px;color: dimgrey;">&nbsp;删除日期：</td>
                    <td>
                        <input type="text" class="ipt" id="del_date" name="del_date"
                               value="<?php echo date('Y-m-d H:i', $page['info']['del_date']?$page['info']['del_date']:time()) ?>"
                               onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'})"/>
                    </td>
                </tr>
                <tr>
                    <td style="width: 100px;color: dimgrey;">&nbsp;删除信息备注：</td>
                    <td>
                        <textarea style="font-size: small;height: 50px;" class="ipt" id="mark"
                                  name="mark"><?php echo $page['info']['mark']; ?> </textarea>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td class="alignleft"><input type="submit" class="but" id="subtn" value="确定"/>
                    </td>
                </tr>
            </table>
    </form>
</div>
