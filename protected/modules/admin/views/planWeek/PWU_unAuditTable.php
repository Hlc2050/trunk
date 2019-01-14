<!-- 待审核计划(个人)  -->
<div style="height: 550px;width:100%;overflow-y: scroll">
    <?php  foreach ($page['info']['listdata'] as $value) { ?>
        <table class="tb" style="width: 900px;margin-top: 10px;margin-bottom: 10px;">
            <thead>
            <tr>
                <td colspan="3" style="text-align: left">
                    <input type="button" name="plan_through" class="but" value="同意" style="border-radius: 5px"> &nbsp;
                    <input type="button" name="plan_un_through" class="but" value="拒绝" style="background-color: red;border-color: red;border-radius: 5px"> &nbsp;
                    <input type="hidden" name="plan_id" value="<?php echo $value['id'];?>">

                </td>
                <td colspan="4" style="text-align: center">
                    <?php echo $page['user_name'][$value['tg_uid']];?>-<?php echo date('Y-m-d',$value['start_date']);?>至<?php echo date('Y-m-d',$value['start_date']+6*24*60*60);?>-排期计划
                    <?php if ($value['through_time'] < $value['update_time'] && $value['through_time']>0) { ?>
                        <span style="background-color: yellow;padding: 2px;color: red">计划变更</span>
                    <?php } ?>
                </td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <th colspan="2">客服部/日期</th>
                <?php for ($i=0;$i<7;$i++){ ?>
                    <th style="width: 90px;border: solid 1px #cccccc"><?php echo date('m-d',$value['start_date']+$i*24*60*60);?></th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($page['info']['detail_plan'][$value['id']] as $key=>$service) {?>
                <tr>
                    <td rowspan="3">
                        <?php echo $page['service_group'][$key];?>
                    </td>
                    <td style="border: solid 1px #cccccc">微信号个数</td>
                    <?php foreach ($service as $k=>$plan) { ?>
                        <td style="border: solid 1px #cccccc"><?php echo $plan['weChat_num'];?></td>
                    <?php }?>
                </tr>
                <tr>

                    <td style="border: solid 1px #cccccc">进粉</td>
                    <?php foreach ($service as $k=>$plan) { ?>
                        <td style="border: solid 1px #cccccc">
                            <?php echo $plan['fans_count'];?></td>
                    <?php }?>
                </tr>
                <tr>
                    <td style="border: solid 1px #cccccc">产值</td>
                    <?php foreach ($service as $k=>$plan) { ?>
                        <td style="border: solid 1px #cccccc"><?php echo $plan['output'];?></td>
                    <?php }?>
                </tr>

            <?php }?>
            <tr><td colspan="9" style="text-align: left"><?php echo $value['mask'];?></td></tr>
            </tbody>
        </table>
    <?php } ?>
</div>

<div class="mt10" style="display: none" id="unthrough_div">
    <textarea name="unthrough_msg" id="unthrough_msg"></textarea>
</div>


<script type="text/javascript">
    $('input[name="plan_through"]').click(function () {
        if (confirm('确定通过该计划审核？')) {
            var btn = $(this);
            var week_id = $(this).parent().find('input[name="plan_id"]').val();
            jQuery.ajax({
                'type': 'POST',
                'url': '/admin/planWeek/auditUserPlan',
                'data': {'week_id': week_id,'status':1},
                'cache': false,
                'async':false,
                'success': function (result) {
                    var res = JSON.parse(result);
                    var status = res.state;
                    if (status == 0) {
                        alert(res.msgwords);
                        return false;
                    }else {
                        $(btn).parent().parent().parent().parent().remove();
                        alert('审核成功！');
                        return true;
                    }
                }
            });
        }

    });
    $('input[name="plan_un_through"]').click(function () {
        var btn = $(this);
        var week_id = $(this).parent().find('input[name="plan_id"]').val();
        var return_false = 0;
        $("#unthrough_msg").val('');
        dialog({
            title: '请输入拒绝理由',
            content:$("#unthrough_div"),
            okVal:'确定',
            cancelVal:'取消',
            ok: function () {
                var unthrough_msg = $.trim($("#unthrough_msg").val());
                if (unthrough_msg=='') {
                    alert('请输入拒绝理由！');
                    return false;
                }
                jQuery.ajax({
                    'type': 'POST',
                    'url': '/admin/planWeek/auditUserPlan',
                    'data': {'week_id': week_id, 'unthrough_msg':unthrough_msg,'status':2},
                    'cache': false,
                    'async':false,
                    'success': function (result) {
                        var res = JSON.parse(result);
                        var status = res.state;
                        if (status == 0) {
                            return_false = 1;
                            alert(res.msgwords);
                            return false;
                        }else {
                            $(btn).parent().parent().parent().parent().remove();
                            return true;
                        }
                    }
                });
                if (return_false == 1) {
                    return false;
                }else {
                    return true;
                }
            },
            cancel: function () {
            },
            init: function () {
                this.content('对话框内容被扩展方法改变了');
            }
        }).showModal();
    });
</script>