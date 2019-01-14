<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<?php $today = strtotime(date('Y-m-d',time()));?>
<div class="main mhead">
    <div class="snav">计划管理 » 每周进粉计划 » 组计划修改</div>
</div>
<div class="main mbody">
    <form method="post"
          action="<?php echo $this->createUrl('planWeek/editGroupPlan'); ?>" onsubmit="return chckForm()">
        <input type="hidden" id="week_id" name="week_id" value="<?php echo $page['week_plan']['id']; ?>"/>
        <input type="hidden" id="group_id" name="group_id" value="<?php echo $page['week_plan']['group_id']; ?>"/>
        <input type="hidden" id="backurl" name="backurl" value="<?php echo $this->createUrl('planWeek/index'); ?>?tab_id=3"/>

        <table class="tb3" style="border: solid 1px #c2c2c2;width: 1000px" id="plan_table">
            <thead>
            <tr>
                <td><input type="button" class="but" value="添加客服部" id="add_tr"> &nbsp;&nbsp;</td>
                <td colspan="2"></td>
                <td colspan="6"><?php echo date('Y-m-d', $page['week_dates'][0]) . '至' . date('Y-m-d', end($page['week_dates'])) ?>
                    -排期计划
                </td>
            </tr>
            <tr>
                <th>客服部/日期</th>
                <?php foreach ($page['week_dates'] as $value) { ?>
                    <th style="border: solid 1px #CCCCCC"><?php echo date('m-d', $value); ?></th>
                <?php } ?>
                <th style="border: solid 1px #CCCCCC">操作</th>
            </tr>
            </thead>
            <?php $today = strtotime(date('Y-m-d'),time());?>
            <?php foreach ($page['detail_plan'] as $key=>$value) {?>
                <tr class="plan_tr">
                    <td style="text-align: center;border: solid 1px #CCCCCC" >
                        <?php if ($page['week_plan']['start_date'] <$today) {?>
                            <select name="cservice_group[]" onchange="change_group(this)" style="width:100px;margin-top: 8%" readonly>
                                <option value="<?php echo $key;?>"><?php echo $page['service_group'][$key];?></option>
                            </select>
                        <?php } else { ?>
                            <select name="cservice_group[]" onchange="change_group(this)" style="width:100px;margin-top: 8%">
                                <option value="">请选择</option>
                                <?php foreach ($page['service_group'] as $sk=>$sv) {?>
                                    <option value="<?php echo $sk;?>"  <?php if ($sk==$key) echo 'selected';?>><?php echo $sv;?></option>
                                <?php }?>
                            </select>
                        <?php }?>
                        <div style="float: right;">
                            微信号个数<br/>
                            进粉<br/>
                            产值
                        </div>
                    </td>
                    <?php foreach ($value as $k=>$plan) { ?>
                        <td style="text-align: center;border: solid 1px #CCCCCC" >
                            <p style="margin-bottom: 5px">
                                <input type="text" style="width: 60px" name="wechat_<?php echo $key;?>[]" value="<?php echo $plan['weChat_num'];?>" <?php if($k<$today){ echo "readonly" ;}?> required><br/>
                            </p>
                            <p style="margin-bottom: 5px">
                                <input type="text" style="width: 60px" name="fans_<?php echo $key;?>[]" value="<?php echo $plan['fans_count'];?>" <?php if($k<$today){ echo "readonly" ;}?> required><br/>
                            </p>
                            <input type="text" name="output_<?php echo $key;?>[]" value="<?php echo $plan['output'];?>" style="width: 60px" <?php if($k<$today){ echo "readonly" ;}?> required>
                        </td>
                    <?php } ?>
                    <td style="text-align: center;" >

                    </td>
                </tr>
            <?php } ?>

            <tr id="add_tr_info" style="display: none">
                <td style="text-align: center;border: solid 1px #CCCCCC">
                    <select name="" onchange="change_group(this)" style="width: 100px;margin-top: 8%">
                        <option value="">请选择</option>
                        <?php foreach ($page['service_group'] as $key=>$value) {?>
                            <option value="<?php echo $key;?>"><?php echo $value;?></option>
                        <?php }?>
                    </select>
                    <div style="float: right;">
                        微信号个数<br/>
                        进粉<br/>
                        产值
                    </div>
                </td>
                <?php for ($i=0;$i<7;$i++) { ?>
                    <td style="text-align: center;border: solid 1px #CCCCCC">
                        <p style="margin-bottom: 5px"><input type="text" name="wechat" style="width: 60px" value="<?php if($page['week_dates'][$i] < $today){ echo '0' ;}?>"   <?php if($page['week_dates'][$i] < $today){ echo "readonly" ;}?> >
                            <br/></p>
                        <p style="margin-bottom: 5px"><input type="text" name="fans" style="width: 60px" value="<?php if($page['week_dates'][$i] < $today){ echo '0' ;}?>"   <?php if($page['week_dates'][$i] < $today){ echo "readonly" ;}?> >
                            <br/></p>
                        <input type="text" name="output"  style="width: 60px" value="<?php if($page['week_dates'][$i] < $today){ echo '0' ;}?>" <?php if($page['week_dates'][$i] < $today){ echo "readonly" ;}?> >
                    </td>
                <?php } ?>
                <td style="text-align: center;">
                    <button type="button" class="but" onclick="del_plan(this)">删除</button>
                </td>
            </tr>

        </table>
        <div class="mt10">
            <h5>备注信息</h5>
            <textarea cols="100" rows="3" name="mask" required><?php echo $page['week_plan']['mask'];?></textarea>
            <input type="hidden" name="type" value="1">
            <input type="hidden" name="group_id" value="<?php echo $page['week_plan']['group_id'];?>">
            <p style="margin-top: 10px"><input type="submit" class="but" id="subtn" value="提交"></p>
        </div>
    </form>
    <script>
        $("#add_tr").click(function () {
            var html = $("#add_tr_info").html();
            $("#plan_table").append('<tr class="plan_tr">'+html+'</tr>');
            $("#plan_table").find('.plan_tr').find('select').attr('name','cservice_group[]');
            $("#plan_table").find('.plan_tr').find('input').attr('required','required');
        });
        function change_group(object) {
            var tr = $(object).parent().parent();
            var service_id = $(object).val();
            tr.find('input').each(function (obj,val) {
                var name = $(this).attr('name');
                var array = name.split('_');
                name = array[0];
                $(this).attr('name',name+'_'+service_id+'[]');
            });
            var group_id = $('input[name=group_id]').val();
            var start_date = "<?php echo $page['week_dates'][0];?>";
            jQuery.ajax({
                'type': 'POST',
                'url': '/admin/planWeek/getGroupPlanTotal',
                'data': {'group_id': group_id, 'service_id':service_id,'start_date':start_date},
                'cache': false,
                'async':false,
                'success': function (result) {
                    var res = JSON.parse(result);
                    tr.find("input[name='wechat_"+service_id+"[]']").each(function (obj,val) {
                        var plan = res[obj];
                        $(this).val(plan.weChat_num)
                    });
                    tr.find("input[name='fans_"+service_id+"[]']").each(function (obj,val) {
                        var plan = res[obj];
                        $(this).val(plan.fans_count)
                    });
                    tr.find("input[name='output_"+service_id+"[]']").each(function (obj,val) {
                        var plan = res[obj];
                        $(this).val(plan.output)
                    });
                }
            });

        }
        function del_plan(object) {
            $(object).parent().parent().remove();
        }

        function chckForm() {
            var select_groups = [];
            var is_false = 0;
            $('select[name="cservice_group[]"]').each(function (index,val) {
                var select_tr = $(this).parent().parent().attr('id');
                if (select_tr != 'add_tr_info') {
                    var group_id = $(val).val();
                    if (group_id=='') {
                        alert('请选择客服部!');
                        is_false = 1;
                        return false;
                    }
                    var str = select_groups.join(',');
                    str = ','+str+',';
                    if (str.indexOf(','+group_id+',') == -1) {
                        select_groups.push(group_id);
                    } else {
                        var txt = $(val).find("option:selected").text();
                        alert('客服部：'+txt+' 重复添加了！');
                        is_false = 1;
                        return false;
                    }
                    $("input[name='fans_"+group_id+"[]']").each(function (index,obj) {
                        var fans = $(this).val();
                        if (!isIntNum(fans)) {
                            alert('预估进粉数请输入一个整数!');
                            $(this).focus();
                            is_false = 1;
                            return false;
                        }
                    });
                    $("input[name='output_"+group_id+"[]']").each(function (index,obj) {
                        var output = $(this).val();
                        if (!isNumber(output)) {
                            alert('预估产值请输入一个数字!');
                            $(this).focus();
                            is_false = 1;
                            return false;
                        }
                    });
                }

            });
            if (is_false == 1) {
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
