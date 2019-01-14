<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">计划管理 » 每月进粉计划</div>
</div>
<form method="post" action="<?php echo $this->createUrl('planMonth/editGroup') ?>" onsubmit="return chckForm()">
    <?php
    $plan_time = date("Y", strtotime("now"));
    ?>
    <input hidden value="<?php echo $updateGroup['listdata']['id']; ?>" name="id">
    <input hidden value="<?php echo $updateGroup['listdata']['month']; ?>" name="month">

        <?php foreach($updateGroup as $k=>$value){ ?>
            <table class="tb" style="width: 750px;border: solid 1px #CCCCCC;margin-left: 20px;margin-top: 10px;">
            <tr>
                <td style="width: 150px;"><a style="float: left" class="but2" id="add" href="#" >新增客服部</a></td>
                <input name="title" value="<?php echo $updateGroup['listdata']['name'].'-'.date('Y-m',$updateGroup['listdata']['month']). '-'.'进粉计划' ?>" hidden>
                <input name="status" value="<?php echo $value['status']; ?>" hidden>
                <input name="groupid" value="<?php echo $value['groupid']; ?>" hidden>
                <td colspan="4">
                    <div  style="margin-right: 100px;">
                        <span><?php echo $updateGroup['listdata']['name'] . '-';?></span>
                        <span id="month"><?php echo date('Y-m',$updateGroup['listdata']['month']); ?></span>
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
            <?php for ($i=0;$i<$value['num'];$i++){ ?>
                <input hidden name="ids[]" value="<?php echo $value['list'][$i]['id'];?>">
                <tr>
                    <td style="text-align: center">
                        <?php
                        echo helper::getServiceSelect2($value['list'][$i]['cs_id']);
                        ?>
                    </td>
                    <td style="text-align: center">
                        <input type="text" style="width: 100px;" class="ipt" name="weChat_num[]" value="<?php echo $value['list'][$i]['weChat_num'];  ?>">
                    </td>
                    <td style="text-align: center">
                        <input type="text" style="width: 100px;" class="ipt" name="fans_plan[]" value="<?php echo $value['list'][$i]['fans_plan'];  ?>">
                    </td>
                    <td style="text-align: center">
                        <input type="text" style="width: 100px;" class="ipt" name="output_plan[]" value="<?php echo $value['list'][$i]['output_plan'];  ?>">
                    </td>
                    <td></td>
                </tr>
            <?php } ?>
        </table>
        <?php } ?>

    <div style="margin-left: 20px;margin-top: 10px;">备注:</div>
    <div style="margin-left: 20px;margin-top: 10px;"><textarea style="height: 100px; width: 730px;" name="remark"><?php echo $value['remark']; ?></textarea></div>
    <div style="margin-left: 620px;margin-top: 10px;">
        <input type="button" class="but" value="返回" onclick="window.location='<?php echo $this->createUrl('planMonth/index?group_id=2'); ?>'"/>
        <button type="submit" class="but2" >提交</button>
    </div>
    </div>

</form>
<script>
    function del(obj) {
        $(obj).parent().parent().remove();
    }

    $("#add").click(function () {
        var html = '<tr>' +
            '<td style="text-align: center"><select name="csid[]" onchange="change_group(this)" style="width: 100px;">'+
             <?php $list = CustomerServiceManage::model()->findAll('state_cooperation=0'); ?>
            '<option value="">请选择</option>'+
             <?php foreach ($list as $value){ ?>
                '<option value="<?php echo $value['id'] ?>"><?php echo $value['cname'] ?></option>'+
             <?php } ?>
            '</select></td>' +
            '<td style="text-align: center"><input style="width: 100px;" class="ipt" name="weChat_num[]"></td> ' +
            '<td style="text-align: center"><input style="width: 100px;" class="ipt" name="fans_plan[]"></td> ' +
            '<td style="text-align: center"style="text-align: center"><input style="width: 100px;" class="ipt" name="output_plan[]" ></td> ' +
            '<td style="text-align: center"><a href="#" class="but2" onclick="del(this)" >删除</a></td> ' +
            '</tr>';
        $(".tb").append(html);
    });

    function change_group(object) {
        var tr = $(object).parent().parent();
        var ser_group = "<?php echo $updateGroup['listdata']['groupid']; ?>";
        var service_id = $(object).val();
        var month = "<?php echo date('Y-m', $updateGroup['listdata']['month']); ?>";
        jQuery.ajax({
            'type': 'POST',
            'l': '/admin/planMonth/getGroupPlanTotal',
            'data': {'group_id': ser_group, 'service_id': service_id, 'month': month},
            'cache': false,
            'async': false,
            'success': function (result) {
                var res = JSON.parse(result);
                console.log(res)
                tr.find("input[name^='weChat_num']").each(function (obj, val) {
                    var plan = res;
                    $(this).val(plan.weChat_num)
                });
                tr.find("input[name^='fans_plan']").each(function (obj, val) {
                    var plan = res;
                    $(this).val(plan.fans_count)
                });
                tr.find("input[name^='output_plan']").each(function (obj, val) {
                    var plan = res;
                    $(this).val(plan.output)
                });
            }
        });
    }

    function chckForm() {
        var select_groups = [];
        var return_false = 0;
        $('select[name="csid[]"]').each(function (index,val) {
            var group_id = $(val).val();
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
        $("input[name='fans_plan[]']").each(function (index,obj) {
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
        $("input[name='output_plan[]']").each(function (index,obj) {
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
        }
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
