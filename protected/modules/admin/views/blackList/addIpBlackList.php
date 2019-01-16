<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<script>
    $(document).ready(function () {
        var html ='';
        for (var i=0;i<4;i++){
            html += '<tr>';
            html += ' <td width="100"><input class="ipt" name="ipblacklist[]"></td>';
            html += '<td class="alignleft"><input class="ipt" name="ipremark[]"></td>';
            if(i ==3) html+='<td class="alignlist"><a id="add" href="#" style="font-size: xx-large;color: red">+</a></td>';
            html += '</tr>';
        }
        $("table[class='tb3']").append(html);
    })

    function chkForm() {
        var list = [];
        var remark = [];
        $("input[name='ipblacklist[]']").each(function () {
            if($(this).val() != '') list.push($(this).val());
        });
        $("input[name='ipremark[]']").each(function () {
            if($(this).val() != '') remark.push($(this).val());
        });

        if(list.length == 0 && remark.length == 0){
            alert('请填写至少一条数据');
            event.preventDefault();
            return false;
        }else{
            var len = list.length>remark.length ?list.length:remark.length;
            for (var i=0;i<len;i++){console.log(remark[i]);
                if(list[i] && !remark[i] == undefined){
                    alert('请填写ip');
                    $("input[name='ipblacklist[]']")[i].focus();
                    event.preventDefault();
                    return false;
                }else if(list[i] == undefined && remark[i]){
                    alert('请填写备注');
                    $("input[name='ipremark[]']")[i].focus();
                    event.preventDefault();
                    return false;
                }else if(IsIP(list[i])){
                    alert('ip不符合规则');
                    $("input[name='ipblacklist[]']")[i].focus();
                    event.preventDefault();
                    return false;
                }
         }
        }
    }

    function IsIP(ip) {
        var pat = "/^(?:(?:2[0-4][0-9]\.)|(?:25[0-5]\.)|(?:1[0-9][0-9]\.)|(?:[1-9][0-9]\.)|(?:[0-9]\.)){3}(?:(?:2[0-5][0-5])|(?:25[0-5])|(?:1[0-9][0-9])|(?:[1-9][0-9])|(?:[0-9]))$/";
        return ip.text(pat);
    }

    $(function () {
        $("#add").click(function () {
            var html = '<tr>' + '<td width="100"><input class="ipt" name="ipblacklist[]"></td>' +
                ' <td  class="alignleft"><input class="ipt" name="ipremark[]"></td>' + '</tr>'
            $(".tb3").append(html);
        })
    })
</script>

<div class="main mhead">
    <div class="snav">下单黑名单 » 黑名单列表 » 新增ip黑名单</div>
</div>
<div class="main mbody">
    <form name="form" action="<?php echo $this->createUrl('blackList/addIp'); ?>" method="post" onsubmit="return chkForm()" style="width: 300px;">
        <table class="tb3">
            <tr>
                <th colspan="2" class="alignleft">新增ip黑名单</th>
            </tr>
            <tr>
                <td class="alignleft"> ip黑名单:</td>
                <td class="alignleft">备注:</td>
            </tr>
        </table>
        <input type="submit" class="but" value="提交">
        <input type="button" class="but" value="返回" onclick="window.location='<?php echo $this->createUrl('blackList/index'); ?>'"/>
    </form>
</div>
