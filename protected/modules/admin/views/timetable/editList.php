<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<style>
    .width_100{
        width:100px !important;
    }
</style>
<div class="main mhead">
    <div class="snav">排期管理 » 编辑排期表 </div>
</div>
<div class="main mbody">
    <form action="<?php echo $action; ?>" name="form_order" method="post">
        <table class="tb fixTh">
            <tr>
                <th>微信号</th>
                <th>
                    排期类型
                    <select id="type_all" name="type" onchange="change_type()">
                        <option value="">-请选择-</option>
                        <?php
                        foreach ($types as $item){
                            ?>
                            <option value="<?php echo $item['type_id'];?>"><?php echo $item['name'];?></option>
                            <?php
                        }
                        ?>
                    </select>
                </th>
                <th>
                    排期数值
                    <select id="count" name="count" onchange="change_all_count()">
                        <option value="">-请选择-</option>
                    </select>
                </th>
                <th>推广人员</th>
                <th>客服部</th>
                <th>商品</th>
                <?php
                for($i=0;$i<=6;$i++){
                    ?>
                    <th style="border:1px solid #d1d5de"><?php echo date('m/d',$start_time+($i*86400));?><br>
                        <input style="width: 50px" id="numerical_change_<?php echo $i+1; ?>" type="text">
                    </th>

                    <?php
                }
                ?>
                <th>合计</th>
                <th width=220 style="border:1px solid #d1d5de">微信号状态及日期 <br>
                    <?php
                    $weChat_status = vars::$fields['timetable_status'];

                    ?>
                    <select id="status_sel">
                        <?php
                        foreach($weChat_status as $k=>$v){
                            ?>
                            <option value="<?php echo $v['value'] ;?>"  <?php if(($v) == 0) echo 'selected';?>><?php echo $v['txt'];?></option>
                            <?php
                        }
                        ?>
                    </select>

                    <?php $arr = '';
                    foreach ($timetable_list as $k=>$v){
                        $arr .= $k."_";
                    }
                    $tmp = rtrim($arr,'_');

                    ?>
                    <select id="date_sel">
                        <?php
                        foreach($datelist as $k=>$v){
                            ?>
                            <option value="<?php echo $k+1;?>"  <?php if(($v) == $wechat['default_time']) echo 'selected';?>><?php echo date('m/d',$v);?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <input type="button" value="设置" onclick="change_all_status('<?php echo $tmp ?>')">
                </th>
            </tr>

            <?php
            foreach($timetable_list as $key=>$wechat){
                ?>
                <!--微信号-->
                    <td><?php echo $wechat['wechat_id']?></td>
                <!--排期类型-->
                    <td>
                        <?php
                        $timeTypeList = Dtable::toArr(TimetableType::model()->findAll());
                        echo CHtml::dropDownList('type_id_'.$wechat['wid'], $wechat['type_id'], CHtml::listData($timeTypeList, 'type_id', 'name'),
                            array(
                                'ajax' => array(
                                    'type' => 'POST',
                                    'url' => $this->createUrl('/admin/timetableType/getTypeValueBYtid'),
                                    'update' => "#count_".$wechat['wid'],
                                    'data' => array('type_id' => 'js:$("#type_id_'.$wechat['wid'].'").val()'),
                                )
                            )
                        );
                        ?>
                    </td>
                <!--数值-->
                    <td>
                        <?php
                        $count_array = array();
                        if(!$this->get('type_id')){
                                $data = TimetableType::model()->find('type_id = '.$wechat['type_id']);
                                $count = explode(',',$data['count']);
                                foreach ($count as $key=>$value){
                                    $count_array[$key]['count'] = $value;
                                    $count_array[$key]['value'] = $value;
                                }
                        }else{
                            $count_array = TimetableType::model()->getTypeCounts($this->get('type_id'));
                        }
                        echo CHtml::dropDownList('count_'.$wechat['wid'], $wechat['type_count'], CHtml::listData($count_array, 'count', 'value'),array('id'=>'count_'.$wechat['wid']));
                        ?>
                    </td>
                <td><?php echo $wechat['ps_name']?></td>
                <td><?php echo $wechat['cname']?></td>
                    <td><?php echo $wechat['goods_name']?></td>
                    <?php
                    for($i=1;$i<=7;$i++){
                        ?>
                        <td style="border:1px solid #d1d5de">
                            <input style="width: 50px" class="<?php if($wechat['table_list'][$i]['editable'] !=1)  echo 'disable_edit' ;?>" type="text" name="wechat_<?php echo $wechat['wid'];?>_<?php echo $i;?>"
                                   value="<?php echo  $count = (!empty($wechat['table_list'][$i]) &&  $wechat['table_list'][$i]['count_show'] >= 0 )?$wechat['table_list'][$i]['count_show']:'--';?>"
                                   onblur="check_input_count(<?php echo $wechat['wid']; ?>,<?php echo $i; ?>)"
                                   <?php if($wechat['table_list'][$i]['status']!=0 || $wechat['table_list'][$i]['editable'] !=1 ) echo 'readonly disabled'; ?>
                            >
                            <input type="hidden" name="status_<?php echo $wechat['wid'];?>_<?php echo $i;?>" value="<?php echo $status=!isset($wechat['table_list'][$i]['status'])?'0':$wechat['table_list'][$i]['status']; ?>">
                            <input type="hidden" name="count_<?php echo $wechat['wid'];?>_<?php echo $i;?>" value="<?php echo $count=!isset($wechat['table_list'][$i]['count'])?'-1':$wechat['table_list'][$i]['count']; ?>">
                        </td>
                        <?php
                    }
                    ?>
                    <td id="count_total_<?php echo $wechat['wid']; ?>"><?php echo $wechat['count_total']?></td>
                    <td style="border:1px solid #d1d5de">
                        <?php
                        $weChat_status = vars::$fields['timetable_status'];
                        echo CHtml::dropDownList('status_sel_'.$wechat['wid'], $wechat['default_status'], CHtml::listData($weChat_status, 'value', 'txt'),
                            array(
                                'empty' => '状态',
                            )
                        );
                        ?>
                        <select id="date_sel_<?php echo $wechat['wid'];?>">
                                <?php
                                foreach($datelist as $k=>$v){
                                ?>
                                    <option value="<?php echo $k+1;?>"  <?php if(($v) == $wechat['default_time']) echo 'selected';?>><?php echo date('m/d',$v);?></option>
                                <?php
                                }
                                ?>
                        </select>
                        <input type="button" value="设置" onclick="change_status(<?php echo $wechat['wid']; ?>)">
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
        <input type="submit" class="but2" value="保存" style="margin-top: 20px">
        <input type="hidden" name="start_time" value="<?php echo $start_time;?>">
        <input type="hidden" name="backurl" value="<?php echo $this->get('backurl');?>">

        <div class="clear">
        </div>
    </form>
</div>
<script>
    var start_time = <?php echo $start_time;?>;
    window.onload = function(){
        $("select[id^='count_']").change(function(dom){
            //adding your code here
            var count_id = $(this).attr('id');
            var wid = count_id.replace('count_','');

            var count = $(this).val();
            var date_count = 0;
            var type_id = '#type_id_'+wid;
            var type = $(''+type_id+' option:selected').val();
            if(count == ''){
                alert('请选择排期数值！');
                return false;
            }
            for(var i=1;i<=7;i++){
                var is_change = $("input[name='wechat_"+wid+"_"+i+"']").attr("readonly");
                var date = start_time+(i-1)*86400;
                var unixTimestamp = new Date(date * 1000);
                var   D = unixTimestamp.getDate();
                switch (type){
                    case '1':
                        date_count = count;
                        break;
                    case '2':
                        if(D%2 == 1){
                            date_count = count;
                        }else{
                            date_count = 0;
                        }
                        break;
                    case '3':
                        if(D%2 == 0){
                            date_count = count;
                        }else{
                            date_count = 0;
                        }
                        break;
                }
                var classname = $("input[name='wechat_"+wid+"_"+i+"']").attr("class");
                if(classname != 'disable_edit'){ // 普通编辑时判断该日期是否可编辑
                    $("input[name='count_"+wid+"_"+i+"']").val(date_count);
                }
                if(is_change == undefined){
                    $("input[name='wechat_"+wid+"_"+i+"']").val(date_count);
                }
            }
            change_count_total(wid);
        });
    }
    function change_type(){
        var select_type = $('#type_all option:selected') .val();
        $.ajax({
            url: '/admin/timetableType/getTypeValueAjax/type_id/'+select_type,
            type: 'get',
            dataType: 'json',
            timeout: 1000,
            success: function (data, status) {
                var sta = data.status;
                var str = '';
                if(sta =='0'){
                    str = str+'<option value="">-没有数值-</option>'
                }else{
                    str = str+'<option value="">-请选择-</option>';
                    var counts = data.counts.split(',');
                    $('#count').html('');
                    for(var i=0;i<counts.length;i++){
                        str = str+'<option value="'+counts[i]+'">'+counts[i]+'</option>'
                    }
                }
                $('#count').html(str);
            },
            fail: function (err, status) {
            }
        });
    }
    function change_status(wid){
        var status = $('#status_sel_'+wid+' option:selected').val();//微信号状态
        var status_text = $('#status_sel_'+wid+' option:selected').text();//微信号状态值
        var begin_date = $('#date_sel_'+wid+' option:selected').val();//开始时间

        if(status != ""){
            if(status != 0){
                for(var i=begin_date;i<=7;i++){
                    $("input[name='wechat_"+wid+"_"+i+"']").val(status_text);
                    $("input[name='wechat_"+wid+"_"+i+"']").attr("readonly","readonly");
                    $("input[name='wechat_"+wid+"_"+i+"']").attr("disabled","disabled");
                    $("input[name='status_"+wid+"_"+i+"']").val(status);
                }
            }else{
                for(var i=begin_date;i<=7;i++){
                    var old_count = $("input[name='count_"+wid+"_"+i+"']").val();
                    $("input[name='wechat_"+wid+"_"+i+"']").val(old_count);
                    $("input[name='wechat_"+wid+"_"+i+"']").attr("readonly",false);
                    $("input[name='wechat_"+wid+"_"+i+"']").attr("disabled",false);
                    $("input[name='status_"+wid+"_"+i+"']").val(status);
                }
            }
            change_count_total(wid);
        }else{
            alert('请先选择状态！')
        }

    }

    //批量改变微信号状态及日期
    function change_all_status(array) {

        var status_all = $('#status_sel').val();
        var data_all = $('#date_sel').val();

        $("select[id^='status_sel_']").each(function () {
            $(this).val(status_all);
        });
        $("select[id^='date_sel_']").each(function () {
            $(this).val(data_all);
        });
        var num = array.split('_');
        var count = num.length

        for(i=0;i<count;i++){
            change_status(num[i]);
            for(j=1;j<=7;j++){
                //当文本框没有数值的时候设为9
                var data = $('#numerical_change_'+j).val() == ''?0:$('#numerical_change_'+j).val();
                var int = isInt(data);

                //当文本框数值是整数的时候执行
                if(int == true ){
                    //文本框没有数值的时候不记录
                    if(data != 0){
                        //状态是推广的时候
                        if(status_all == 0){
                            if(isInt($("input[name='wechat_"+num[i]+"_"+j+"']").val())){
                                $("input[name='wechat_"+num[i]+"_"+j+"']").attr('value',$('#numerical_change_'+j).val());
                                $("input[name='count_"+num[i]+"_"+j+"']").attr('value',$('#numerical_change_'+j).val());
                            }
                        }else{
                            //状态是推广的时候
                            if( j < data_all){
                                    $("input[name='wechat_"+num[i]+"_"+j+"']").attr('value',$('#numerical_change_'+j).val());
                                    $("input[name='count_"+num[i]+"_"+j+"']").attr('value',$('#numerical_change_'+j).val());
                            }
                        }
                    }
                }else{
                        alert('请输入正确数值');
                        return ;
                }
            }
        }

    }

    //判断是否为正整数
    function isInt(s){//是否为正整数
        var re=/^([1-9]\d*|[0]{1,1})$/;
        return re.test(s)
    }

    //批量设置微信类型及数字
    function change_all_count(){
        var type_all = $('#type_all option:selected').val();
        var count_all = $('#count option:selected').val();
        $("select[id^='type_id_']").each(function () {
            $(this).val(type_all);
        });
        $("select[id^='count_']").each(function () {
            var str = '<option value="'+count_all+'" selected>'+count_all+'</option>';
            $(this).append(str);
        });
        var date_count = [];
        for(var i=1;i<=7;i++){
            var date = start_time+(i-1)*86400;
            var unixTimestamp = new Date(date * 1000);
            var   D = unixTimestamp.getDate();
            switch (type_all){
                case '1':
                    date_count[i] = count_all;
                    break;
                case '2':
                    if(D%2 == 1){
                        date_count[i] = count_all;
                    }else{
                        date_count[i] = 0;
                    }
                    break;
                case '3':
                    if(D%2 == 0){
                        date_count[i] = count_all;
                    }else{
                        date_count[i] = 0;
                    }
                    break;
            }
        }
        $("input[name^='wechat_']").each(function () {
            var id = $(this).attr('name');
            var day_id = id.split('_');
            var is_change = $(this).attr("readonly");
            var classname = $(this).attr('class');
            if(is_change == undefined){
                $(this).val(date_count[day_id[2]]);
            }
        });
        $("input[name^='count_']").each(function () {
            var id = $(this).attr('name');
            var day_id = id.split('_');
            var classname = $("input[name='wechat_"+day_id[1]+"_"+day_id[2]+"']").attr('class');
            console.log(classname);
            if( classname != 'disable_edit') {
                $(this).val(date_count[day_id[2]]);
            }
        });
        change_count_total();
    }
    //改变合计值
    function change_count_total(wid=0) {
        if(wid == 0){
            $("td[id^='count_total_']").each(function () {
                var id = $(this).attr('id');
                var id_array = id.split('_');
                var wechat_id = 'wechat_'+id_array[2]+'_';
                var total = 0;
                $("input[name^='"+wechat_id+"']").each(function () {
                    if (/^[0-9]*$/.test($(this).val()))
                    {
                        total += parseInt($(this).val());
                    }
                });
                $(this).html(total);
            })
        }else{
            var wechat_id = 'wechat_'+wid+'_';
            var total = 0;
            $("input[name^='"+wechat_id+"']").each(function () {
                if (/^[0-9]*$/.test($(this).val()))
                {
                    total += parseInt($(this).val());
                }
            });
            $('#count_total_'+wid).html(total);
        }
    }
    function check_input_count(wid,day_id) {
        var input_val = $("input[name='wechat_"+wid+"_"+day_id+"']").val();
        if (!/^[0-9]*$/.test(input_val))
        {
            alert('请输入一个数字！');
            $("input[name='wechat_"+wid+"_"+day_id+"']").val(0);
            $("input[name='wechat_"+wid+"_"+day_id+"']").focus();
        }else{
            $("input[name='count_"+wid+"_"+day_id+"']").val(input_val);
            change_count_total(wid);
        }
    }
</script>

