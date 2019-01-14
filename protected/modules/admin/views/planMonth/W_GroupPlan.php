<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">计划管理 » 每月进粉计划</div>
</div>

<form method="post" action="<?php echo $this->createUrl('planMonth/addGroup') ?>" onsubmit="return chckForm()">
        <?php
        $id = Yii::app()->admin_user->uid;
        $group_ids = AdminUser::model()->get_manager_group($id);
        $data = CustomerServiceManage::model()->findAll('state_cooperation=0');
        ?>
        <?php foreach ($group_ids as $k=>$value){ ?>
            <?php $group_names = AdminGroup::model()->getGroupName($value); ?>
            <table class="tb" id="tb<?php echo $k; ?>" style="width: 1000px;border: solid 1px #CCCCCC;margin-left: 20px;margin-top: 10px;">
                <thead>
                        <input hidden id="month<?php echo $k; ?>"  name="month[<?php echo $k; ?>]"  value="<?php echo $month; ?>">
                        <input hidden id="group_names<?php echo $k; ?>" name="group_names[<?php echo $k; ?>]" value="<?php echo $group_names; ?>">
                        <input hidden id="group_ids<?php echo $k; ?>" name="group_ids[<?php echo $k; ?>]" value="<?php echo $group_ids[$k]; ?>">
                        <input hidden id="title<?php echo $k; ?>" name="title[<?php echo $k; ?>]" value="<?php echo $group_names.'-'.$month. '-'.'进粉计划' ?>" >
                    <tr>
                        <td><a style="float: left" class="but2"  href="#" onclick="add(<?php echo $k; ?>)">新增客服部</a></td>
                        <td colspan="3">
                            <div style="margin-right: 150px;">
                                <span><?php echo $group_names . '-' ;?></span>
                                <span><?php echo $month; ?></span>
                                <span><?php echo '-'.'进粉计划'; ?></span>
                            </div>
                        </td>
                        <td><a style="float: right" class="but2"  href="#" onclick="dele(this,<?php echo $k; ?>)">删除该推广组</a></td>
                    </tr>
                    <tr>
                        <th style="width: 200px;">客服部</th>
                        <th style="width: 200px;">微信号个数</th>
                        <th style="width: 200px;">计划进粉</th>
                        <th style="width: 200px;">计划产值</th>
                        <th style="width: 200px;">操作</th>
                    </tr>
                </thead>
                    <tbody>
                    <?php foreach ($arr[$value] as $key=>$val){  ?>
                        <tr>
                            <td style="text-align: center">
                                <select name="<?php echo 'cs_id['.$k.'][]'; ?>" style="width: 100px;">
                                    <option value="<?php echo $key; ?>"><?php echo CustomerServiceManage::model()->getCSName($key); ?></option>
                                </select>
                            </td>
                            <td style="text-align: center;">

                                <input style="width: 80px;background-color: #CCCCCC;" value="<?php echo $val['weChat_num'] ?>" readonly="readonly" class="ipt" name="old_weChat_num[<?php echo $k; ?>][]">
                                <input style="width: 80px;" class="ipt" name="weChat_num[<?php echo $k; ?>][]" value="<?php echo $val['weChat_num'] ?>">
                            </td>
                            <td style="text-align: center">
                                <input style="width: 80px;background-color: #CCCCCC;" value="<?php echo $val['fans_plans'] ?>" readonly="readonly" class="ipt" name="old_fans_count[<?php echo $k; ?>][]">
                                <input style="width: 80px;" class="ipt" name="fans_count[<?php echo $k; ?>][]" value="<?php echo $val['fans_plans'] ?>">
                            </td>
                            <td style="text-align: center">
                                <input style="width: 80px;background-color: #CCCCCC;" value="<?php echo $val['output_plans'] ?>" readonly="readonly" class="ipt" name="old_output[<?php echo $k; ?>][]">
                                <input style="width: 80px;" class="ipt" value="<?php echo $val['output_plans'] ?>" name="output[<?php echo $k; ?>][]">
                            </td>
                            <td style="text-align: center">
                                <a href="#" class="but2" onclick="del(this)" >删除</a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                <input type="hidden" name="table_group" value="<?php echo $group_ids[$k]; ?>">
            </table>


        <?php } ?>
    <div style="margin-left: 20px;margin-top: 10px;">备注:</div>
    <div style="margin-left: 20px;margin-top: 10px;"><textarea style="height: 100px; width: 990px;" name="remark"></textarea></div>
    <div style="margin-left: 880px;margin-top: 10px;">
        <input type="button" class="but" value="返回" onclick="window.location='<?php echo $this->createUrl('planMonth/index?group_id=2'); ?>'"/>
        <button type="submit" class="but2" >提交</button>
    </div>
</form>

<script type="text/javascript">
    function del(obj) {
        $(obj).parent().parent().remove();

    }
    function dele(obj,k) {
        $(obj).parent().parent().parent().parent().remove();
        $("#month"+k).remove();
        $("#group_names"+k).remove();
        $("#group_ids"+k).remove();
        $("#title"+k).remove();

    }
    function add(k) {
        var html = '<tr>' +
            '<td style="text-align: center"><select onchange="getData(this)" name="cs_id['+k+'][]" style="width: 100px;"><option value="">请选择</option><?php foreach ($data as $val){ ?><option value="<?php echo $val['id']; ?>"><?php echo $val['cname']; ?></option><?php } ?></select></td>' +
            '<td style="text-align: center"><input style="width: 80px;background-color: #CCCCCC;" value="0" readonly="readonly" class="ipt" name="old_weChat_num['+k+'][]; ?>">'+ '&nbsp'+
            '<input style="width: 80px;" class="ipt" name="weChat_num['+k+'][]" value="0"></td> ' +
             '<td style="text-align: center"><input style="width: 80px;background-color: #CCCCCC;" value="0" readonly="readonly" class="ipt" name="old_fans_count['+k+'][]; ?>">'+ '&nbsp'+
            '<input style="width: 80px;" class="ipt" name="fans_count['+k+'][]" value="0"></td> ' +
            '<td style="text-align: center"><input style="width: 80px;background-color: #CCCCCC;" value="0" readonly="readonly" class="ipt" name="old_output['+k+'][]; ?>">'+ '&nbsp'+
            '<input style="width: 80px;" class="ipt" name="output['+k+'][]" value="0"></td> ' +
            '<td style="text-align: center"> <a href="#" class="but2" onclick="del(this)" >删除</a></td> ' +
            '</tr>';
        $("#tb"+k).append(html);

    }

    function getData(obj) {
        var tr = $(obj).parent().parent();
        var table = $(obj).parent().parent().parent().parent();
        var ser_group = table.find('input[name="table_group"]').val();
        var service_id = $(obj).val();
        var month = "<?php echo $month; ?>";
        console.log(service_id)
        jQuery.ajax({
            'type': 'POST',
            'url': '/admin/planMonth/getGroupPlanTotal',
            'data': {'group_id': ser_group, 'service_id':service_id,'month':month},
            'cache': false,
            'async':false,
            'success': function (result) {
                var res = JSON.parse(result);
                console.log(res);
                tr.find("input[name^='old_weChat_num']").each(function (obj,val) {
                    var plan = res;
                    $(this).val(plan.weChat_num)
                });
                tr.find("input[name^='weChat_num']").each(function (obj,val) {
                    var plan = res;
                    $(this).val(plan.weChat_num)
                });
                tr.find("input[name^='old_fans_count']").each(function (obj,val) {
                    var plan = res;
                    $(this).val(plan.fans_count)
                });
                tr.find("input[name^='fans_count']").each(function (obj,val) {
                    var plan = res;
                    $(this).val(plan.fans_count)
                });
                tr.find("input[name^='old_output']").each(function (obj,val) {
                    var plan = res;
                    $(this).val(plan.output)
                });
                tr.find("input[name^='output']").each(function (obj,val) {
                    var plan = res;
                    $(this).val(plan.output)
                });
            }
        });
    }


    function chckForm() {
        var select_groups = [];
        var return_false = 0;

        $('input[name="group_ids[]"]').each(function (index,val) {
            $("select[name='cs_id["+index+"][]']").each(function (index,val) {
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
                if (return_false == 1) {
                    return false;
                }
            });
            if (return_false == 1) {
                return false;
            }
            $("input[name='fans_count["+index+"][]']").each(function (index,obj) {
                var fans = $(this).val();
                if (!isIntNum(fans)) {
                    alert('预估进粉数请输入一个整数!');
                    return_false =1;
                    $(this).focus();
                    return false;
                }
            });
            if (return_false == 1) {
                return false;
            }
            $("input[name='output["+index+"][]']").each(function (index,obj) {
                var output = $(this).val();
                if (!isNumber(output)) {
                    alert('预估产值请输入一个数字!');
                    return_false =1;
                    $(this).focus();
                    return false;
                }
            });
            if (return_false == 1) {
                return false;
            }

        });
        if (return_false == 1) {
            return false;
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