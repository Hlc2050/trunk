<?php require(dirname(__FILE__) . "/../common/head.php"); ?>

<div class="main mhead">
    <div class="snav">系统功能 » 支持人员权限</div>
</div>
<div class="main mhead">
    <table class="tb" style="width: 800px;border: solid 1px #CCCCCC;">
        <tr>
            <th>支持人员</th>
            <th>关联推广人员</th>
        </tr>
        <?php foreach ($supportStaff as $key=>$value){  ?>
            <tr>
                <td><?php echo $value ?></td><input name="supportStaff" hidden value="<?php echo $key ?>">
            <td>
                <select class="combox" id="tagId_<?php echo $key ?>" name="tagId" multiple>
                    <?php foreach ($promotionStaff as $key=>$value){  ?>
                    <option value="<?php echo $key ?>"><?php echo $value ?></option>
                    <?php }  ?>
                </select>
            </td>
            </tr>
        <?php }  ?>
    </table>
        <input id="submit" style="margin-left: 650px;margin-top: 20px;" class="but" value="提交">
</div>
<link rel="stylesheet" href="/static/lib/selsect2/select2.css">
<script src="/static/lib/js/select2.full.js"></script>
<script src="/static/lib/js/select2.js"></script>
<script type="text/javascript">
    window.onload = function () {
        $.ajax({
            type: "POST",
            url: "/admin/supporterAu/getData",
            dataType: "json",
            success: function (result) {
                if(result){
                    $('input[name=supportStaff]').each(function () {
                        var supportStaff =$(this).val();
                        $("#tagId_"+supportStaff).each(function () {
                            var pro = result[supportStaff];
                            console.log(pro);
                            if (pro != undefined){
                                for(i=0;i<pro.length;i++){
                                    $("#tagId_"+supportStaff).append(new Option(pro[i].text,  pro[i].id, false, true));
                                }
                            }
                        })
                    })
                }
            }
        })
    }

    $(function(){
        $('select[name=tagId]').select2({
            placeholder: "请至少选择一个推广人员",
            tags:true,
            createTag:function (decorated, params) {
                return null;
            },
            width:'256px'
        });
    });

    $("#submit").click(function () {
        var data =[];
        var data1 =[];
        var data2 =[];
        $('input[name=supportStaff]').each(function () {
            data.push($(this).val());
        });
        $('select[name=tagId]').each(function () {
            var array = $(this).val();
            var str;
            if(array != null){
                str = array.toString();
            }else{
                str = '';
            }
            data1.push(str) ;

        });

        for(i=0;i<data.length;i++){
            var str;
            if(i != (data.length-1)){
                str = data[i] +':'+data1[i]+'/';
            }else{
                str = data[i] +':'+data1[i];
            }
            data2.push(str) ;
        }
        console.log(data2);
            var url = "<?php echo $this->createUrl('supporterAu/add');?>";
            var data3 = 'data='+ data2;
            window.location.href=url+'?'+data3;
    })


</script>


