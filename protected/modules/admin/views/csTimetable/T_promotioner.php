<form action="<?php echo $this->createUrl('csTimetable/promotioner'); ?>?group_id=1" >
    日期：
    <input type="text" size="20" class="ipt" style="width:120px;" name="start_date"
           id="start_date" value="<?php if($page['start_date']){echo date('Y-m-d',$page['start_date']);}  ; ?>"
           onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>-
    <input type="text" size="20" class="ipt" style="width:120px;" name="end_date"
           id="end_date" value="<?php if($page['end_date']){echo date('Y-m-d',$page['end_date']);} ; ?>"
           onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>&nbsp;&nbsp;

    <?php $promotion_group =AdminGroup::model()->getGroup();
    echo CHtml::dropDownList('promotion_group',$this->get('promotion_group'), CHtml::listData($promotion_group, 'groupid', 'groupname'),
        array('empty' => '推广组',)
    );
    ?>
    <input type="text" class="ipt" id="tg" name="tg" value="<?php echo $page['tg']?$page['tg']:$this->get('tg'); ?>" >&nbsp;
    <input hidden id="tg_id" name="tg_id">
    <input hidden name="group_id" value="1">
    <input type="submit" class="but" value="搜索">&nbsp;
</form>

<script>
    $("#promotion_group").change(function () {
            $("#tg").attr('value','');
    })
    
    $(document).ready(function () {
        $('#tg').on('keyup focus', function () {
            $('.searchsBox').show();
            var myInput = $(this);
            var key = myInput.val();
            var group_id = $("#promotion_group").val();
            var postdata = {search_type: 'keys', search_txt: key , group_id:group_id };
            var html = '';
            $.getJSON('<?php echo $this->createUrl('csTimetable/getTgPeople') ?>?jsoncallback=?', postdata, function (reponse) {
                for (var i = 0; i < reponse.data.length; i++) {
                    html += '<a href="javascript:void(0);" style="display:block;font-size:12px;padding:2px 5px;" onmouseDown="getTValues(this);" + data-id="' + reponse.data[i].sno+ '" + data-name="' + reponse.data[i].csname_true+ '">' + reponse.data[i].csname_true + '</a>'
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
        var id = myobj.attr('data-id');
        var name = myobj.attr('data-name');
        $("#tg_id").val(id);
        $("#tg").val(name);
    }
</script>