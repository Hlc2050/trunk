<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('.package_name').on('keyup focus', function () {
            $('.searchsBox').show();
            var myInput = $(this);
            var key = myInput.val();
            packageName_id = myInput.attr('id');

            var postdata = {search_type: 'keys', search_txt: key};
            var html = '';
            $.getJSON('<?php echo $this->createUrl('packageManage/GetPackage') ?>?jsoncallback=?', postdata, function (reponse) {
                for (var i = 0; i < reponse.data.list.length; i++) {
                    html += '<a href="javascript:void(0);" style="display:block;font-size:12px;padding:2px 5px;" onmouseDown="getTValues(this);" packageName_id="' + packageName_id + '" data-id="' + reponse.data.list[i].id + '" data-name="' + reponse.data.list[i].name + '">' + reponse.data.list[i].name + '</a>'
                }
                var s_height = myInput.height();
                var top = myInput.offset().top + s_height;
                var left = myInput.offset().left;
                var width = myInput.width();
                $('.searchsBox').remove();
                $('body').append('<div class="searchsBox" style="position:absolute;top:' + top + 'px;left:' + left + 'px;background:#fff;z-index:2;border:1px solid #ccc;width:' + width + 'px;">' + html + '</div>');
            });
            myInput.blur(function () {
                $('.searchsBox').hide();
            })
        });
    })

    function chekFrom() {
        var names = [];
        var prices = [];
        var num = 0;

        $("input[name='package_name[]']").each(function () {
            num++;
            names.push($(this).val());
        })
        $("input[name='price[]']").each(function () {
            prices.push($(this).val());
        })

        for (var i = 0; i < num; i++) {
            if (names[i] == '' && prices[i] == '') continue;
            else if (isNaN(prices[i])) {
                alert('第' + (i + 1) + '行价格格式错误');
                event.preventDefault();
                return false;
            } else if (names[i] != '' && prices[i] == '') {
                alert('第' + (i + 1) + '行价格未填写');
                event.preventDefault();
                return false;
            } else if (names[i] == '' && prices[i] != '') {
                alert('第' + (i + 1) + '行下单商品未填写');
                event.preventDefault();
                return false;
            }
        }
    }

    function getTValues(ele) {
        var myobj = $(ele);
        packageName_id = myobj.attr('packageName_id')
        packageId_id = 'package_id_' + packageName_id.substr(packageName_id.length - 1);
        console.log(packageId_id)
        var id = myobj.attr('data-id');
        var name = myobj.attr('data-name')
        $('#' + packageName_id).val(name);
        $('#' + packageId_id).val(id);
    }
</script>

<div class="main mhead">
    <div class="snav">直接下单管理 » 套餐管理 » 商品组设置套餐</div>
</div>

<div class="main mbody">
    <table class="tb">
        <tr>
            <th align='center'>下单商品</th>
            <th align='center'>价格</th>
            <th align='center'></th>
        </tr>
        <form action="<?php echo $this->createUrl('packageManage/packageGroupManage') ?>" method="post"
              onsubmit="return chekFrom();">
            <input hidden value="<?php echo $package_group_id ?>" name="group_id">
            <?php for ($i = 0; $i < 6; $i++) { ?>
                <tr>
                    <td width="25%">
                        <input type="text" value="<?php echo $page[$i]['name'] ?>"
                               id="<?php echo "package_name_" . $i; ?>" class="ipt package_name" name="package_name[]">
                        <input hidden id="<?php echo "package_id_" . $i; ?>"
                               value="<?php echo $page[$i]['package_id'] ?>" class="package_id" name="package_id[]">
                    </td>
                    <td width="25%">
                        <input type="text" value="<?php echo $page[$i]['price'] ?>" name="price[]" class="ipt">
                    </td>
                    <td width="50%"></td>
                </tr>
            <?php } ?>
    </table>
    <div class="mt10"></div>
    <input style="margin-left: 21rem" class="but2" type="submit" value="提交">
    </form>
    <input type="button" class="but2" value="返回"
           onclick="window.location='<?php echo $this->createUrl('packageManage/index'); ?>'"/>
</div>

