<!--统计图搜索栏-->
<form action="<?php echo $this->createUrl('orderGoodsEffect/index'); ?>"    >

    <input type="hidden" name="group_id" value="<?php echo $this->get('group_id');?>"/>
    日期：
    <input type="text" size="20" class="ipt" style="width:120px;" name="start_date"
           id="start_online_date" value="<?php echo $allData['start_date']; ?>"
           onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>-
    <input type="text" size="20" class="ipt" style="width:120px;" name="end_date"
           id="end_online_date" value="<?php echo $allData['end_date'] ?>"
           onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
    <a href="#"
       onclick="$('#start_online_date').val('<?php echo date("Y-m-d", mktime(0, 0, 0, date('m'), 1, date('Y'))); ?>');$('#end_online_date').val('<?php echo date("Y-m-d", mktime(0, 0, 0, date('m'), date('t'), date('Y'))); ?>')">本月</a>
    下单商品：
    <input type="text" id="package_name" style="width:120px;" name="package_name" class="ipt"
           value="<?php echo $this->get('package_name'); ?>">
    客服部：
    <?php
    helper::getServiceSelect('csid');
    ?>
    <input type="submit" class="but" value="查询">
    &nbsp

</form>


<script type="text/javascript">
    $(document).ready(function () {
        $('#package_name').on('keyup focus', function () {
            $('.searchsBox').show();
            var myInput = $(this);
            var key = myInput.val();
            var postdata = {search_type: 'keys', search_txt: key};
            console.log(key);
            var html = '';
            $.getJSON('<?php echo $this->createUrl('orderGoodsEffect/SearchPackage') ?>?jsoncallback=?', postdata, function (reponse) {
                console.log(reponse);
                for (var i = 0; i < reponse.data.list.length; i++) {
                    html += '<a href="javascript:void(0);" style="display:block;font-size:12px;padding:2px 5px;" onmouseDown="getTValues(this);" name="' + reponse.data.list[i].name + '">' + reponse.data.list[i].name + '</a>'
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

    function getTValues(ele) {
        var myobj = $(ele);
        var name = myobj.attr('name')
        $('#package_name').val(name);
    }
</script>
