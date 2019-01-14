<?php require(dirname(__FILE__) . "/../common/head.php"); ?>

<div class="main mhead">
    <div class="snav">计划管理 » 每周进粉计划 » 添加个人计划</div>
</div>
<div class="main mbody">
    <form method="post"
          action="<?php echo $this->createUrl('planWeek/addUserPlan'); ?>?id=<?php echo $page['info']['id']; ?>" onsubmit="return chckForm()">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <input type="hidden" id="backurl" name="backurl" value="<?php echo $this->createUrl('planWeek/index'); ?>?tab_id=1"/>

        <table class="tb3" style="border: solid 1px #c2c2c2;width: 1000px" id="plan_table">
            <thead>
            <tr>
                <td><input type="button" class="but" value="添加客服部" id="add_tr"> &nbsp;&nbsp;</td>
                <td colspan="2"></td>
                <?php if ($page['is_manage'] == 1) {?>
                    <td colspan="5">
                        <select name="tg_uid">
                            <option value="">请选择</option>
                            <?php foreach ($page['plan_user'] as $value) {?>
                                <option value="<?php echo $value['csno'];?>"><?php echo $value['csname_true'];?></option>
                            <?php }?>
                        </select>
                        -<?php echo date('Y-m-d', $page['week_dates'][0]) . '至' . date('Y-m-d', end($page['week_dates'])) ?>
                        -排期计划
                    </td>
                <?php }?>
                <?php if ($page['is_manage'] == 0) {?>
                    <td colspan="5"><?php echo $page['user_name'];?>-<?php echo date('Y-m-d', $page['week_dates'][0]) . '至' . date('Y-m-d', end($page['week_dates'])) ?>
                        -排期计划
                    </td>
                <?php }?>

                <td>
                    <?php if ($page['hide_add_curent'] == 0 && $page['type']!=2) {?>
                        <input type="button" class="but" value="补写本周" onclick="location='<?php echo $this->createUrl('planWeek/addUserPlan'); ?>?type=2'">
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <th>客服部/日期</th>
                <?php foreach ($page['week_dates'] as $value) { ?>
                    <th style="border: solid 1px #CCCCCC"><?php echo date('m-d', $value); ?></th>
                <?php } ?>
                <th>操作</th>
            </tr>
            </thead>
            <?php $data = CustomerServiceManage::model()->findAll('state_cooperation=0'); ?>
            <?php foreach ($data as $value) { ?>
            <tr class="plan_tr">
                <td style="text-align: center;border: solid 1px #CCCCCC" >
                    <select name="cservice_group[]" style="width:100px;margin-top: 8%" onchange="change_group(this)">
                            <option value="<?php echo $value['id'];?>"><?php echo $value['cname'];?></option>
                    </select>
                    <div style="float: right;">
                        微信号个数<br/>
                        进粉<br/>
                        产值
                    </div>
                </td>
                <?php for ($i=0;$i<7;$i++) { ?>
                    <td style="text-align: center;border: solid 1px #CCCCCC" >
                        <input type="text" name="<?php echo 'wechat_'.$value['id'].'[]'; ?>" value="" style="width: 60px;margin-bottom: 5px" required><br/>
                        <input type="text" style="width: 60px;margin-bottom: 5px" name="<?php echo 'fans_'.$value['id'].'[]'; ?>" value="" required><br/>
                        <input type="text" style="width: 60px" name="<?php echo 'output_'.$value['id'].'[]'; ?>" value="" required>
                    </td>
                <?php } ?>
                <td style="text-align: center;" >
                    <button type="button" class="but" onclick="del_plan(this)">删除</button>&nbsp;
                </td>
            </tr>
            <tr id="add_tr_info" style="display: none">
                <td style="text-align: center;border: solid 1px #CCCCCC">
                    <select name="" onchange="change_group(this)" style="width: 100px;margin-top: 8%">
                        <option value="">请选择</option>
                        <?php foreach ($data as $value) {?>
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
                    <td style="text-align: center;border: solid 1px #CCCCCC">
                        <input type="text" name="wechat"  style="width: 60px;margin-bottom: 5px" value="" ><br/>
                        <input type="text" name="fans" style="width: 60px;margin-bottom: 5px" value="" ><br/>
                        <input type="text" name="output" style="width: 60px" value="" >
                    </td>
                <?php } ?>
                <td style="text-align: center;">
                    <button type="button" class="but" onclick="del_plan(this)">删除</button>
                </td>
            </tr>
            <?php }?>
        </table>
        <div class="mt10">
            <h5>备注信息</h5>
            <textarea cols="100" rows="3" name="mask" required></textarea>
            <input type="hidden" name="type" value="<?php echo $page['type'];?>">
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

        }
        function del_plan(object) {
            $(object).parent().parent().remove();
        }

        function chckForm() {
            var select_groups = [];
            var return_false = 0;
            var tg_select = $('select[name="tg_uid"]').length;
            if (tg_select > 0) {
                var tg_uid = $('select[name="tg_uid"]').val();
                if (tg_uid == '') {
                    alert('请先选择推广人员！');
                    return false;
                }
            }
            $('select[name="cservice_group[]"]').each(function (index,val) {
                var select_tr = $(this).parent().parent().attr('id');
                console.log(select_tr);
                if (select_tr != 'add_tr_info') {
                    var group_id = $(val).val();
                    if (group_id=='') {
                        return_false =1;
                        alert('请选择客服部!');
                        return false;
                    }
                    if (return_false == 1) {
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
                    $("input[name='fans_"+group_id+"[]']").each(function (index,obj) {
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
                    $("input[name='output_"+group_id+"[]']").each(function (index,obj) {
                        var output = $(this).val();
                        if (!isIntNum(output)) {
                            alert('预估产值请输入一个整数!');
                            return_false =1;
                            $(this).focus();
                            return false;
                        }
                    });
                    if (return_false == 1) {
                        return false;
                    }
                    $("input[name='wechat_"+group_id+"[]']").each(function (index,obj) {
                        var wechat = $(this).val();
                        if (!isIntNum(wechat)) {
                            alert('预估微信号个数请输入一个整数!');
                            return_false =1;
                            $(this).focus();
                            return false;
                        }
                    });
                    if (return_false == 1) {
                        return false;
                    }
                }
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
