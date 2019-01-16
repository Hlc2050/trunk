<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<script>
    $(document).ready(function () {
        var html ='';
        for (var i=0;i<4;i++){
            html += '<tr>';
            html += ' <td width="100"><input class="ipt" name="phoneblacklist[]" value=""></td>';
            html += '<td class="alignleft"><input class="ipt" name="phoneremark[]" value=""></td>';
            if(i ==3) html+='<td class="alignlist"><a id="add" href="#" style="font-size: xx-large;color: red">+</a></td>';
            html += '</tr>';
        }
        $("table[class='tb3']").append(html);
    })

    function chkForm() {
        var list = [];
        var remark = [];
        $("input[name='phoneblacklist[]']").each(function () {
            if($(this).val() != '') list.push($(this).val());
        });
        $("input[name='phoneremark[]']").each(function () {
            if($(this).val() != '') remark.push($(this).val());
        });

        if(list.length == 0 && remark.length == 0){
            alert('请填写至少一条数据');
            event.preventDefault();
            return false;
        }else{
            var len = list.length>remark.length ?list.length:remark.length;
            for (var i=0;i<len;i++){
                if(list[i] && !remark[i]){
                    alert('请填写手机号');
                    $("input[name='phoneblacklist[]']")[i].focus();
                    event.preventDefault();
                    return false;
                }else if(!list[i] && !remark[i]){
                    alert('请填写备注');
                    $("input[name='phoneremark[]']")[i].focus();
                    event.preventDefault();
                    return false;
                }
            }
        }
    }

    $(function () {
        $("#add").click(function () {
            var html = '<tr>' + '<td width="100"><input class="ipt" name="phoneblacklist[]" value=""></td>' +
                ' <td  class="alignleft"><input class="ipt" name="phoneremark[]" value=""></td>' + '</tr>'
            $(".tb3").append(html);
        })
    })
</script>

<div class="main mhead">
    <div class="snav">下单黑名单 » 黑名单列表 » 新增手机号黑名单</div>
</div>
<div class="main mbody">
    <form name="form" action="<?php echo $this->createUrl('blackList/addPhone'); ?>" method="post" onsubmit="return chkForm()" style="width: 300px;">
        <table class="tb3">
            <tr>
                <th colspan="2" class="alignleft">新增手机号黑名单</th>
            </tr>
            <tr>
                <td colspan="1" class="alignleft"> 手机号黑名单:</td>
                <td colspan="1" class="alignleft">备注:</td>
            </tr>
        </table>
        <input type="submit" class="but" value="提交">
        <input type="button" class="but" value="返回" onclick="window.location='<?php echo $this->createUrl('blackList/index'); ?>'"/>
    </form>
</div>



