<?php require(dirname(__FILE__) . "/../common/head.php"); ?>

<?php
$plan_time = date("Y", strtotime("now"));
$timestrap = date("m", strtotime("now"));
$time = date('m',strtotime("+1 months", strtotime("now")));
$next_month = str_replace ("0", "", $time);
$current_month = str_replace ("0", "", $timestrap);
?>
<input hidden value="<?php echo $next_month; ?>" id="next_month">


<?php foreach($check['list'] as $key => $value){ ?>
    <table class="tb" style="width: 800px;border: solid 1px #CCCCCC" >
        <tr>
            <td style="width: 200px;"> <input type="button"  class="but" value="同意" style="float: left;" onclick="agree(this,'<?php echo $key; ?>')">
                <input type="button"  class="but" value="拒绝" style="float: left;margin-left: 10px;background-color: red;border-color: red;" onclick="refuse(this,'<?php echo $key; ?>')"></td>
            <td colspan="3">
                <div style="margin-right: 120px;">
                    <span><?php echo $value['name'] . '-';?></span>
                    <span id="month"><?php echo date('Y-m',$value['month']); ?></span>
                    <span><?php echo '月'.'-'.'进粉计划'; ?></span>
                    <span  <?php if($value['through_time']<$value['update_time'] && $value['through_time'] != 0) echo 'style="background-color: yellow;padding: 2px;color: red"'; ?> ><?php if($value['through_time']<$value['update_time'] && $value['through_time'] != 0) echo '计划变更'; ?></span>
                </div>
            </td>

        </tr>
        <tr>
            <th style="width: 200px;">客服部</th>
            <th style="width: 200px;">计划进粉</th>
            <th style="width: 200px;">计划产值</th>
            <th style="width: 200px;">微信号个数</th>
        </tr>
        <?php for ($i=0;$i<$value['num'];$i++){ ?>
            <tr>
                <td style="text-align: center">
                    <?php
                    echo helper::getServiceSelect2($value['data'][$i]['cs_id'],1);
                    ?>
                </td>
                <td style="text-align: center">
                    <span><?php echo $value['data'][$i]['fans_plan'];  ?></span>
                </td>
                <td style="text-align: center">
                    <span><?php echo $value['data'][$i]['output_plan'];  ?></span>
                </td>
                <td style="text-align: center">
                    <span><?php echo $value['data'][$i]['weChat_num'];  ?></span>
                </td>
            </tr>

        <?php } ?>
        <tr>
            <td colspan="4" style="text-align: left;"><span><?php echo $value['remark']; ?></span></td>
        </tr>
        <tr style="height: 10px;"></tr>
</table>
<?php } ?>

<div class="mt10" style="display: none" id="unthrough_div">
    <textarea name="unthrough_msg" id="unthrough_msg"></textarea>
</div>

<script type="text/javascript">
   function agree(obj,key) {
       if (confirm('确定通过该计划审核？')) {
           $(obj).parent().parent().parent().remove();
           var arr = key.split("_");
           var status = arr[0];
           var id = arr[1];
           var month = arr[2];
           var type = arr[3];
           jQuery.ajax({
               type: "POST",
               url: "/admin/planMonth/agree",
               data: {status: status, id: id, month: month, type: type},
               dataType: "json",
               success: function (result) {

               }
           });
       }
   }
       function refuse(obj,key) {
           var arr = key.split("_");
           var status = arr[0];
           var id = arr[1];
           var month = arr[2];
           var type = arr[3];
           dialog({
               title: '请输入拒绝理由',
               content:$("#unthrough_div"),
               okVal:'确定',
               cancelVal:'取消',
               ok: function () {
                   var unthrough_msg = $.trim($("#unthrough_msg").val());
                   console.log(unthrough_msg);
                   if (unthrough_msg=='') {
                       alert('请输入拒绝理由！');
                       return false;
                   }
                   jQuery.ajax({
                       'type': 'POST',
                       'url': '/admin/planMonth/refuse',
                       'data': {status: status, id: id, month: month, type: type,'unthrough_msg':unthrough_msg},
                       'cache': false,
                       'async':false,
                       'success': function (result) {
                           console.log(result);
                           $(obj).parent().parent().parent().remove();
                       }
                   });
               },
           cancel: function () {
           }
           }).showModal();
       }


</script>