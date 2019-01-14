<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav"><span style="font-size: x-large;font-weight: bold; color: dimgrey">执行SQL语句</span></div>
    <div class="mt10">
    </div>
</div>
<div class="main mbody">
    <form>
        <table class="tb3">
            <tbody>
            <tr>
                <td>选择数据库：
                <select name="db" id="db">
                    <option value="0">后台数据库</option>
                    <option value="1">订单数据库</option>
                </select>
                </td>
            </tr>
            <tr>
                <td style=" font-size:large; font-weight:bold; color:dimgrey;width: 100%;height: 50px;">输入SQL语句：</td>
            </tr>
            <tr>
                <td>
                    <textarea id="sql" name="sql" style="width: 600px;height: 160px"></textarea>
                    <br/>
                </td>
            </tr>

            <tr>
                <td>
                    <input id="execute" name="execute" type="button" class="but2" value="提交执行"/>&nbsp;&nbsp;&nbsp;
                    <input type="button" class="but2" value="清空" onclick="$('#sql').val('');"/>
                </td>
            </tr>
            <tr>
                <td style="color:dimgrey;">执行结果：&nbsp;&nbsp;
                    <input type="button" value="清空结果" onclick="$('#result').html('');"/>
                </td>
            </tr>
            <tr>
                <td>
                    <span id="result"></span>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="clear"></div>
    </form>
</div>
<script type="text/javascript">

    jQuery(function ($) {
        jQuery('body').on('click', '#execute', executeSql);
    });

    function executeSql() {
        jQuery.ajax({
            'type': 'POST',
            'url': '/admin/executeSQL/ajaxExecute',
            'data': {'sql': $("#sql").val(),'db':$("#db").val()},
            'cache': false,
            'success': function (html) {
                jQuery("#result").append(html);
            }
        });
        return false;
    }
</script>

