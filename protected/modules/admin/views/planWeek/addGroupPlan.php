<?php require(dirname(__FILE__) . "/../common/head.php"); ?>

<div class="main mhead">
    <div class="snav">计划管理 » 每周进粉计划 » 添加推广组计划</div>
</div>
<div class="main mbody">
    <div class="mt10" style="margin-bottom: 10px">
        <form action="<?php echo $this->createUrl('planWeek/addGroupPlan'); ?>">
        <select id="manage_group" name="manage_group" style="width:150px;">
            <option value="">选择推广组...</option>
            <?php foreach ($page['manage_group'] as $key=>$value) {?>
                <option value="<?php echo $key;?>"><?php echo $value;?></option>
            <?php }?>
        </select>
        <input type="button" class="but" value="添加" id="add_group">
        </form>
    </div>
    <form method="post"
          action="<?php echo $this->createUrl('planWeek/addGroupPlan'); ?>?id=<?php echo $page['info']['id']; ?>" onsubmit="return chckForm()" id="group_form">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>

        <input type="hidden" id="backurl" name="backurl" value="<?php echo $this->createUrl('planWeek/index'); ?>?tab_id=3"/>

        <div class="mt10 before_div">
            <h5>备注信息</h5>
            <textarea cols="100" rows="3" name="mask" required></textarea>
            <input type="hidden" name="type" value="<?php echo $page['type'];?>">
            <p style="margin-top: 10px"><input type="submit" class="but" id="subtn" value="提交"></p>
        </div>
    </form>
    <!--    添加推广组-->
    <table style="display: none" id="plan_table_add">
        <tr>
            <td><button type="button" class="but" onclick="add_service(this)">添加客服部</button> &nbsp;&nbsp;</td>
            <td colspan="2"></td>
            <td colspan="4"><span class="group_name"></span><?php echo date('Y-m-d', $page['week_dates'][0]) . '至' . date('Y-m-d', end($page['week_dates'])) ?>
                -排期计划
            </td>
            <td colspan="2" style="text-align: right">
                <button type="button" class="but" onclick="del_group(this)">删除该推广组</button> &nbsp;&nbsp;
            </td>
        </tr>
        <tr>
            <th>客服部/日期</th>
            <?php foreach ($page['week_dates'] as $value) { ?>
                <th><?php echo date('m-d', $value); ?></th>
            <?php } ?>
            <th>操作</th>
        </tr>
        <tr id="add_tr_info" style="display: none">
            <td style="text-align: center;border: solid 1px #CCCCCC">
                <select name="" onchange="change_group(this)" style="width: 100px;margin-top: 8%">
                    <option value="">请选择</option>
                    <?php foreach ($page['cservice_group'] as $value) {?>
                        <option value="<?php echo $value['id'];?>"><?php echo $value['cname'];?></option>
                    <?php }?>
                </select>
                <div style="float: right;">
                    微信号个数<br/>
                    进粉<br/>
                    产值
                </div>
            </td>
            <?php for ($i=0;$i<7;$i++) { ?>
                <td class="tr_plan" style="text-align: center;border: solid 1px #CCCCCC">
                    <p style="margin-bottom: 5px">
                        <input type="text" style="width: 60px;background-color: #CCCCCC;;" name="oldwechat" readonly="readonly" value="" >
                        <input type="text" style="width: 60px" name="wechat" value="" >
                        <br/></p>
                    <p style="margin-bottom: 5px">
                        <input type="text" style="width: 60px;background-color: #CCCCCC;;" name="oldfans" readonly="readonly" value="" >
                        <input type="text" style="width: 60px" name="fans" value="" >
                        <br/></p>
                    <input type="text" name="oldoutput" value="" readonly="readonly" style="width: 60px;background-color: #CCCCCC;" >
                    <input type="text" name="output" value="" style="width: 60px;" >
                </td>
            <?php } ?>
            <td style="text-align: center;">
                <button type="button" class="but" onclick="del_plan(this)">删除</button>
            </td>
        </tr>
    </table>
    <script>
        var tg_group = <?php echo json_encode( $page['manage_group']);?>;
        $("#add_group").click(function () {
            var return_false = 0;
            var group_id = $('select[name="manage_group"]').val();
            var start_date = "<?php echo $page['week_dates'][0];?>";


            $("#group_id").attr('value',group_id);
            if (group_id == '') {
                alert('请选择推广组!');
                return false
            }
            $('input[name="group_id[]"]').each(function () {
                var select_group = $(this).val();
                if (group_id == select_group) {
                    alert('该推广组已添加!');
                    return_false = 1;
                    return false;
                }
            });
            if (return_false == 1) {
                return false;
            }
            var html = '<table class="tb3 plan_table" style="border: solid 1px #c2c2c2;width: 1600px;margin-top: 15px">' +$('#plan_table_add').html();
            var html1 = '';
            jQuery.ajax({
                'type': 'POST',
                'url': '/admin/planWeek/getGroupPlanData',
                'data': {'group_id': group_id,'start_date':start_date},
                'cache': false,
                'async':false,
                'dataType':'json',
                'success': function (result) {
                    var res = result;
                    <?php foreach ($page['cservice_group'] as $value){ ?>
                    html1+='<tr class="plan_tr">';
                    html1+=' <td style="text-align: center;border: solid 1px #CCCCCC" >';
                    html1+=' <select name="cservice_group_'+group_id+'[]" style="width:100px;margin-top: 8%" >' +
                        '<option value="<?php echo $value['id'];?>"><?php echo $value['cname'];?></option>'+ '</select>';
                    html1+='<div style="float: right;">' +
                        '微信号个数<br/>' +
                        '进粉<br/>' +
                        '产值' +
                        '</div>';
                    html1+='</td>';
                    html1+='<?php for ($i = 0;$i < 7;$i++) { ?>' +
                        '<td style="text-align: center;border: solid 1px #CCCCCC" >' +
                        '<p style="margin-bottom: 5px">' +
                        '<input type="text" style="width: 60px;background-color: #CCCCCC;" name="oldwechat_'+group_id+'_<?php echo $value['id'] ?>[]" readonly="readonly" value="'+res[<?php echo $value['id'] ?>][<?php echo $i ?>]['weChat_num']+'" >' +
                        '<input type="text" style="width: 60px" name="wechat_'+group_id+'_<?php echo $value['id'] ?>[]" value="'+res[<?php echo $value['id'] ?>][<?php echo $i ?>]['weChat_num']+'" >' +
                        '<br/></p>' +
                        '<p style="margin-bottom: 5px">' +
                        '<input type="text" style="width: 60px;background-color: #CCCCCC;" name="oldfans_'+group_id+'_<?php echo $value['id'] ?>[]" readonly="readonly" value="'+res[<?php echo $value['id'] ?>][<?php echo $i ?>]['fans_count']+'" >' +
                        '<input type="text" style="width: 60px" name="fans_'+group_id+'_<?php echo $value['id'] ?>[]" value="'+res[<?php echo $value['id'] ?>][<?php echo $i ?>]['fans_count']+'" >' +
                        '<br/></p>' +
                        '<input type="text" name="oldoutput_'+group_id+'_<?php echo $value['id'] ?>[]" readonly="readonly" value="'+res[<?php echo $value['id'] ?>][<?php echo $i ?>]['output']+'" style="width: 60px;background-color: #CCCCCC;" >' +
                        '<input type="text" name="output_'+group_id+'_<?php echo $value['id'] ?>[]"  value="'+res[<?php echo $value['id'] ?>][<?php echo $i ?>]['output']+'" style="width: 60px" >' +
                        '</td>' +
                        '<?php } ?>';
                    html1+='<td style="text-align: center;">' +
                        '<button type="button" class="but" onclick="del_plan(this)">删除</button>' +
                        '</td>';
                    html1+='</tr>';
                    <?php  } ?>
                }
            });
            var all_html = html + html1  +'<input type="hidden" name="group_id[]" value="'+group_id+'">'+'</table>';
            $("#group_form").find('.before_div').before(all_html);
            $('.plan_table').find('.group_name').each(function () {
                var group = $(this).parent().parent().parent().parent().find('input[name="group_id[]"]').val();
                $(this).text(tg_group[group]+'-');
            });
            $('.plan_table').find('.plan_tr').find('select').each(function () {
                var group = $(this).parent().parent().parent().parent().find('input[name="group_id[]"]').val();
                $(this).attr('name','cservice_group_'+group+'[]');
            });
        });
        function add_service(obj) {
            var html = $("#add_tr_info").html();
            var table = $(obj).parent().parent().parent().parent();
            var ser_group = table.find('input[name="group_id[]"]').val();
            table.append('<tr class="plan_tr">'+html+'</tr>');
            table.find('.plan_tr').find('select').attr('name','cservice_group_'+ser_group+'[]');
            table.find(".plan_tr").find('input').attr('required','required');
        }
        function del_group(obj) {
            var html = $("#add_tr_info").html();
            var table = $(obj).parent().parent().parent().parent().remove();
        }
        function change_group(object) {
            var tr = $(object).parent().parent();
            var table = $(object).parent().parent().parent().parent();
            var service_id = $(object).val();
            var week_type = $('input[name="type"]').val();
            var service_group = $(table).find('input[name="group_id[]"]').val();
            var start_date = "<?php echo $page['week_dates'][0];?>";
            tr.find('input').each(function (obj,val) {
                var name = $(this).attr('name');
                var array = name.split('_');
                name = array[0];
                $(this).attr('name',name+'_'+service_group+'_'+service_id+'[]');
            });
            jQuery.ajax({
                'type': 'POST',
                'url': '/admin/planWeek/getGroupPlanTotal',
                'data': {'group_id': service_group, 'service_id':service_id,'start_date':start_date},
                'cache': false,
                'async':false,
                'success': function (result) {
                    var res = JSON.parse(result);
                    tr.find("input[name='oldfans_"+service_group+"_"+service_id+"[]']").each(function (obj,val) {
                        var plan = res[obj];
                        $(this).val(plan.fans_count)
                    });
                    tr.find("input[name='fans_"+service_group+"_"+service_id+"[]']").each(function (obj,val) {
                        var plan = res[obj];
                        $(this).val(plan.fans_count)
                    });
                    tr.find("input[name='oldoutput_"+service_group+"_"+service_id+"[]']").each(function (obj,val) {
                        var plan = res[obj];
//                        console.log(plan);
                        $(this).val(plan.output)
                    });
                    tr.find("input[name='output_"+service_group+"_"+service_id+"[]']").each(function (obj,val) {
                        var plan = res[obj];
                        $(this).val(plan.output)
                    });
                    tr.find("input[name='oldwechat_"+service_group+"_"+service_id+"[]']").each(function (obj,val) {
                        var plan = res[obj];
//                        console.log(plan);
                        $(this).val(plan.weChat_num)
                    });
                    tr.find("input[name='wechat_"+service_group+"_"+service_id+"[]']").each(function (obj,val) {
                        var plan = res[obj];
                        $(this).val(plan.weChat_num)
                    });

                }
            });

        }
        function del_plan(object) {
            $(object).parent().parent().remove();
        }

        function chckForm() {
            var tables = $('.plan_table');
            var groups = tables.find('input[name="group_id[]"]');
            if (groups.length <= 0) {
                alert('请选择推广组添加!');
                return false;
            }
            var return_false = 0;
            tables.each(function () {
                var service_group  = $(this).find('input[name="group_id[]"]').val();
                var select_groups = [];
                var select = $(this).find("select[name='cservice_group_"+service_group+"[]']").each(function () {
                    var group_id = $(this).find('option:selected').val();
                    if (group_id=='') {
                        alert('请选择客服部!');
                        return_false = 1;
                        return false;
                    }
                    var str = select_groups.join(',');
                    str = ','+str+',';
                    if (str.indexOf(','+group_id+',') == -1) {
                        select_groups.push(group_id);
                    } else {
                        var txt = $(this).find("option:selected").text();
                        alert('客服部：'+txt+' 重复添加了！');
                        return_false = 1;
                        return false;
                    }
                    $("input[name='fans_"+service_group+"_"+group_id+"[]']").each(function (index,obj) {
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
                    $("input[name='output_"+service_group+"_"+group_id+"[]']").each(function (index,obj) {
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
</div>
