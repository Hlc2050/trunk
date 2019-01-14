<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
<div class="snav">计划管理 » 每月进粉计划</div>
</div>
<div style="margin-top: 10px;"></div>
<form method="post" action="<?php echo $this->createUrl('planMonth/add') ?>" onsubmit="return chckForm()">
    <?php
    $id = Yii::app()->admin_user->uid;
    $name = AdminUser::model()->find('csno=' . $id);
    $data = CustomerServiceManage::model()->findAll('state_cooperation=0');
    ?>

    <input hidden name="month" value="<?php echo $month; ?>">
    <table class="tb" style="width: 750px;border: solid 1px #CCCCCC;margin-left: 20px;">
        <tr>
            <td><a style="float: left" class="but2" id="add" href="#" >新增客服部</a></td>
            <td colspan="4" style="margin-top: 10px;">
                    <div style="margin-right: 150px;">
                        <select name="tg_uid">
                            <?php foreach ($select['list'] as $value) { ?>
                                <option value="<?php echo $value['user_id'] ?>"><?php echo $value['name'] ?></option>
                            <?php } ?>
                        </select>
                        <span id="month"><?php echo '-'.$month; ?></span>
                        <span><?php echo '-'.'进粉计划'; ?></span>
                    </div>

            </td>
        </tr>
        <tr>
            <th style="width: 150px;">客服部</th>
            <th style="width: 150px;">微信号个数</th>
            <th style="width: 150px;">计划进粉</th>
            <th style="width: 150px;">计划产值</th>
            <th style="width: 150px;">操作</th>
        </tr>
        <?php foreach ($data as $val){ ?>
            <tr>
                <td style="text-align: center;">
                    <select name="csid[]" style="width: 100px;">
                        <option value="<?php echo $val['id'] ?>"><?php echo $val['cname'] ?></option>
                    </select>
                </td>
                <td style="text-align: center"><input  style="width: 100px;" class="ipt" name="weChat_num[]"></td>
                <td style="text-align: center"><input  style="width: 100px;" class="ipt" name="fans_count[]"></td>
                <td style="text-align: center"><input  style="width: 100px;" class="ipt" name="output[]"></td>
                <td style="text-align: center">
                    <a href="#" class="but2" onclick="del(this)" >删除</a>
                </td>
            </tr>
        <?php } ?>

    </table>
    <div style="margin-left: 20px;">备注:</div>
    <div style="margin-left: 20px;margin-top: 10px;"><textarea style="height: 100px; width: 790px;" name="remark"></textarea></div>
    <div style="margin-left: 680px;margin-top: 10px;">
        <input type="button" class="but" value="返回" onclick="window.location='<?php echo $this->createUrl('planMonth/index?group_id=1'); ?>'"/>
        <button type="submit" class="but2" >提交</button>
    </div>

</form>

<script type="text/javascript">
    function del(obj) {
        $(obj).parent().parent().remove();
    }

    $("#add").click(function () {
        var html = '<tr>' +
            '<td style="text-align: center"><?php echo helper::getServiceSelect2();?></td>' +
            '<td style="text-align: center"style="text-align: center"><input style="width: 100px;" class="ipt" name="weChat_num[]"></td> ' +
            '<td style="text-align: center"><input style="width: 100px;" class="ipt" name="fans_count[]"></td> ' +
            '<td style="text-align: center"style="text-align: center"><input style="width: 100px;" class="ipt" name="output[]"></td> ' +
            '<td style="text-align: center"><a href="#" class="but2" onclick="del(this)" >删除</a></td> ' +
            '</tr>';
        $(".tb").append(html);
    });


    function chckForm() {
        var select_groups = [];
        var return_false = 0;
        $('select[name="csid[]"]').each(function (index,val) {
            var group_id = $(val).val();
            console.log(group_id);
            if (group_id=='' || group_id==0) {
                return_false =1;
                alert('请选择客服部!');
                return false;
            }
            var str = select_groups.join(',');
            str = ','+str+',';
            if (str.indexOf(','+group_id+',') == -1) {
                select_groups.push(group_id);
            } else {
                var txt = $(val).find("option:selected").text();
                alert('客服部：'+txt+' 重复添加了！');
                return_false =1;
                return false;
            }
        });
        if (return_false == 1) {
            return false;
        }
        $("input[name='weChat_num[]']").each(function (index,obj) {
            var fans = $(this).val();
            if (!isIntNum(fans)) {
                alert('微信号个数请输入一个整数!');
                return_false =1;
                $(this).focus();
                return false;
            }
        });
        if (return_false == 1) {
            return false;
        }

        $("input[name='fans_count[]']").each(function (index,obj) {
            var fans = $(this).val();
            if (!isIntNum(fans)) {
                alert('计划进粉数请输入一个整数!');
                return_false =1;
                $(this).focus();
                return false;
            }
        });
        if (return_false == 1) {
            return false;
        }
        $("input[name='output[]']").each(function (index,obj) {
            var output = $(this).val();
            if (!isNumber(output)) {
                alert('计划产值请输入一个数字!');
                return_false =1;
                $(this).focus();
                return false;
            }
        });
        if (return_false == 1) {
            return false;
        } else {
            return true;
        };


    }

    function isNumber(val){
        var regPos = /^\d+(\.\d+)?$/; //非负浮点数
        if(regPos.test(val)){
            return true;
        }else{
            return false;
        }
    }
    function isIntNum(val){
        var regPos = /^\d+$/; // 非负整数
        if(regPos.test(val)){
            return true;
        }else{
            return false;
        }
    }


</script>
