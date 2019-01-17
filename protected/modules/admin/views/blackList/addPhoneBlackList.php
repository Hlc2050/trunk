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
        var list_num = 0;
        var remark_num = 0;
        $("input[name='phoneblacklist[]']").each(function () {
            list.push($(this).val());
            if($(this).val() == '') list_num +=1
        });
        $("input[name='phoneremark[]']").each(function () {
            remark.push($(this).val());
            if($(this).val() == '') remark_num +=1
        });

      if (list_num == 4 && remark_num == 4) {
            alert('请填写至少一条数据');
            event.preventDefault();
            return false;
        }else{
            var len = list.length > remark.length ? list.length : remark.length;

           for (var i = 0; i < len; i++) {
                var row = i+1;
                if (!list[i] && remark[i]) {
                    alert('请填写第'+row+'行手机号');
                    $("input[name='phoneblacklist[]']")[i].focus();
                    event.preventDefault();
                    return false;
                } else if (list[i] && !remark[i]) {
                    alert('请填写第'+row+'行备注');
                    $("input[name='phoneremark[]']")[i].focus();
                    event.preventDefault();
                    return false;
                } else if (IsPhone(list[i]) == false && list[i]) {
                    alert('第'+row+'行手机号不符合规则');
                    $("input[name='phoneblacklist[]']")[i].focus();
                    event.preventDefault();
                    return false;
                }
            }
        }

    }

    function IsPhone(phone) {
        var reg =/^0?1[3|4|5|6|7|8][0-9]\d{8}$/;
        return reg.test(phone);
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



