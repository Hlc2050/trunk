<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<script type="text/javascript">
    $(document).ready(function () {
        var type_rule = $("#type_rule").val();
        var arr = [];
        if (type_rule != '') {
            var temp = type_rule.split(",");
            for (var i = 0; i < 32; i++) {
                arr[i] = temp[i] ? temp[i] : '';
            }
        } else {
            for (var i = 0; i < 32; i++) {
                arr[i] = '';
            }
        }

        var html = '';
        html += '<tr>';
        for (var i = 1; i < 17; i++) {
            html += '<th>' + i + '</th>';
        }
        html += '</tr><tr>';
        for (var i = 0; i < 16; i++) {
            html += '<td><input name="standard[]" value="' + arr[i] + '" class="standard"></td>';
        }
        html += '</tr><tr>';
        for (var i = 17; i < 31; i++) {
            html += '<th>' + i + '</th>';
        }
        html += '<th>40</th>';
        html += '<th>60</th>';

        html += '</tr><tr>';
        html += '</tr><tr>';
        for (var i = 16; i < 30; i++) {
            html += '<td><input name="standard[]" value="' + arr[i] + '" class="standard"></td>';
        }
        html += '<th><input name="standard[]" value="' + arr[30] + '" class="standard"></th>';
        html += '<th><input name="standard[]" value="' + arr[31] + '" class="standard"></th>';
        html += '</tr>';

        $("#inform").html(html);
    })

    $("input[type='submit']").click(function () {
        var str = '';
        var old_type = $("input[id='type_name']").val();
        var new_type = $("input[name='type']").val();

        if ($("input[name='type']").val() == '') {
            alert('请输入渠道类型名称');
            $("input[name='type']").focus();
            event.preventDefault();
            return false;
        }

        if (old_type != new_type || old_type == '') {
            jQuery.ajax({
                'type': 'POST',
                'async': false,
                'url': '<?php echo $this->createUrl('channelTypeManage/isSameName')?>',
                'data': {'type': $("input[name='type']").val(), 'action': $("input[id='type_rule']").val()},
                'success': function (data) {
                    if (data == 'exist') {
                        alert('渠道类型名称已存在');
                        event.preventDefault();
                        return false;
                    }
                }
            });
        }

        if ($("input[name='fans_input']").val() == '') {
            alert('请输入进粉成本');
            $("input[name='fans_input']").focus();
            event.preventDefault();
            return false;
        }

        $("input[name='standard[]']").each(function () {
            var e = $(this).val();
            str += e;
            if (isNaN(e) == true) {
                alert('请输入数字');
                event.preventDefault();
                return false;
            }
            if (e == '') {
                alert('请填写完整参数');
                $(this).focus();
                event.preventDefault();
                return false;
            }
        })
    })

</script>
<style>
    .standard {
        height: 25px;
        width: 80px;
        margin-left: 5px;
    }
</style>
<div class="main mhead">
    <div class="snav">市场管理 » 渠道类型设置</div>
</div>
<div class="main mbody">
    <div class="mt10">
        <input id="type_name" value="<?php echo $edit ? $edit['type_name'] : ''; ?>" hidden>
        <input id="type_rule" value="<?php echo $edit ? $edit['type_rule'] : ''; ?>" hidden>
        <form action="<?php echo $edit ? $this->createUrl('channelTypeManage/edit') : $this->createUrl('channelTypeManage/add') ?>"
              method="post">
            <input type="text" name="id" hidden value="<?php echo $edit ? $edit['id'] : ''; ?>">
            <table class="tb3">
                <th><p style="float: left"><?php echo $edit ? '修改渠道类型' : '添加渠道类型'; ?></p></th>
                <tr>
                    <td>渠道类型名称:<input type="text" name="type" class="ipt" style="margin-left: 30px;"
                                      value="<?php echo $edit ? $edit['type_name'] : ''; ?>"></td>
                </tr>
                <tr>
                    <td>进粉成本:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="fans_input" class="ipt"
                                                                        style="margin-left: 30px;"
                                                                        value="<?php echo $edit ? $edit['fans_input'] : ''; ?>">
                    </td>
                </tr>
            </table>
            <table class="tb3" id="inform"></table>
            <table class="tb3">
                <td>
                    <input type="button" class="but" value="返回" style="float: right;"
                           onclick="window.location='<?php echo $this->get('url') ? $this->get('url') : $this->createUrl('channelTypeManage/index'); ?>'"/>
                    <input type="submit" class="but2" value="保存" style="float: right;margin-right: 10px;">
                </td>
            </table>
        </form>
    </div>
</div>

