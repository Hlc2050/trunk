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
    <form name="form" action="<?php echo $this->createUrl('blackList/addIp'); ?>" method="post" onsubmit="return checkForm()" style="width: 300px;">
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
