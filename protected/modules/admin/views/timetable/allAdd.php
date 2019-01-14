<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<style>
    .add_div {
        border-width: 0px;
        /*position: absolute;*/
        width: 98%;
        height: 427px;
    }
    .ax_default {
        font-family: 'Arial Normal', 'Arial';
        font-weight: 400;
        font-style: normal;
        font-size: 13px;
        color: #333333;
        text-align: center;
        line-height: normal;
        background-color: #F2F2F2;
    }
    .form_group{
        padding: 10px 10px;
        width: 95%;
    }
    .form_group label{
        display:inline-block;
        width: 70px;
        text-align: right;
    }
    .wx_div {
        width: 90%;
        height: 150px;
        overflow: auto;
        background: inherit;
        background-color: rgba(255, 255, 255, 0);
        box-sizing: border-box;
        border-width: 1px;
        border-style: solid;
        border-color: rgba(121, 121, 121, 1);
        border-radius: 0px;
        -moz-box-shadow: none;
        -webkit-box-shadow: none;
        box-shadow: none;
    }
    .wx_div span {
        background-color:#FFFFFF;
        margin: 5px 10px;
        padding:5px;
        display: inline-block;
        border: solid grey 1px;
    }
</style>
<div class="main mhead">
    <div class="snav">创建全部排期</div>
</div>
<script>
    window.onload = function(){
        $('#type') .val('');
        $('#pgid') .val('');
    }
</script>
<div class="main mbody">
    <div class="add_div ax_default" style="overflow: auto;width:100%">
        <form method="post" action="<?php echo $this->createUrl('timetable/allAdd')?>" onsubmit="return chekFrom()">
            <div style="width: 30%; float: left;padding: 20px 0 20px 20px;text-align:left;font-size:13px" >
                <h2 style="font-size:14px">创建排期日期</h2>
                <div class="form_group">
                    <label>排期时间<br/>(按周计算)</label>
                    <input type="text" size="20" class="ipt" style="width:80px;font-weight: bold;font-size: 12px"
                           id="start_date" name="start_date" value="" onclick="WdatePicker({disabledDays:[1,2,3,4,5,6],qsEnabled:false,isShowWeek:true,isShowToday:false,firstDayOfWeek:1,dateFmt:'yyyy-MM-dd',
                           onpicked:function() {
                           var time_obj =$dp.$DV($dp.cal.getDateStr(),{d:+6});console.log(time_obj);$dp.$('end_date').value = time_obj.y+'-'+time_obj.M+'-'+time_obj.d;
                      },onclearing:function() {
                        $dp.$('end_date').value = '';
                      }})" readonly/> -
                    <input type="text" size="20" class="ipt" style="width:80px;font-weight: bold;;font-size: 12px"
                           id="end_date" name="end_date" value="" readonly/>&nbsp;
                </div>
                <div class="form_group">
                    <label> 微信号&nbsp;&nbsp;&nbsp; <br/>(一行一个)</label>
                    <textarea rows="6" cols="20" id="wx_input" style="font-size:13px" name="wx_list"></textarea>
                    <input type="hidden" value="" name="wechat_select">
                </div>
            </div>
            <div style="width: 65%;float: left;padding: 10px 0 20px 20px;text-align:left;margin: 0" >
                <div style="width:100%">
                    <div style="float: left;margin-top: 10px">
                        客服部：
                        <?php
                        helper::getServiceSelect('csid');
                        ?>
                        &nbsp;&nbsp;
                        商品：
                        <?php
                        echo $this->get('csid') ? CHtml::dropDownList('goods_id', $this->get('goods_id'), CHtml::listData(CustomerServiceRelation::model()->getGoodsList($this->get('csid')), 'goods_id', 'goods_name'), array('empty' => '全部')) :
                            CHtml::dropDownList('goods_id', $this->get('goods_id'),CHtml::listData(Goods::model()->findAll(), 'id', 'goods_name'), array('empty' =>'全部'))
                        ?>&nbsp;&nbsp;
                    </div>
                    <div style="float: left;margin-top: 10px">
                        推广小组：
                        <?php
                        $promotionGrouplist = Linkage::model()->getPromotionGroupList();
                        echo CHtml::dropDownList('pgid', $this->get('pgid'), CHtml::listData($promotionGrouplist, 'linkage_id', 'linkage_name'),
                            array(
                                'empty' => '全部',
                                'ajax' => array(
                                    'type' => 'POST',
                                    'url' => $this->createUrl('bssOperationTable/getPromotionStaffByPg'),
                                    'update' => '#tg_id',
                                    'data' => array('pgid' => 'js:$("#pgid").val()'),
                                )
                            )
                        );
                        ?>&nbsp;&nbsp;
                        推广人员：
                        <?php
                        echo $this->get('pgid') ? CHtml::dropDownList('tg_id', $this->get('tg_id'), CHtml::listData(PromotionStaff::model()->getPromotionStaffByPg($this->get('pgid')), 'user_id', 'user_name'), array('empty' => '全部')) :
                            CHtml::dropDownList('tg_id', $this->get('tg_id'), CHtml::listData(PromotionStaff::model()->getPromotionStaffByPg(), 'user_id', 'user_name'), array('empty' => '全部'))
                        ?>&nbsp;&nbsp;
                        <input class="but2" value="列出微信号" onclick="show_wechat()" type="button">
                    </div>
                    <div style="clear: both;"></div>
                </div>
                <div style="width:100%;margin-top: 10px">
                    排期类型：
                    <?php
                    $timetable_types = TimetableType::model()->findAll();
                    echo CHtml::dropDownList('type', 'empty', CHtml::listData($timetable_types, 'type_id', 'name'),
                        array(
                            'empty' => '-请选择-',
                            'ajax' => array(
                                'type' => 'POST',
                                'url' => $this->createUrl('timetableType/getTypeValue'),
                                'update' => '#count',
                                'data' => array('type_id' => 'js:$("#type").val()'),
                            )
                        )
                    );
                    ?>&nbsp;
                    数值：
                    <?php
                    echo $this->get('type') ? CHtml::dropDownList('count', $this->get('count'), CHtml::listData(TimetableType::model()->getTypeCounts($this->get('type')), 'count', 'value'), array('empty' => '-请选择-')) :
                        CHtml::dropDownList('count', $this->get('count'), array('-请选择-'))
                    ?>&nbsp;&nbsp;
                </div>
                <div style="width:100%;margin-top: 10px;margin-bottom: 10px">
                    <p>微信号选择器</p>
                    <div class="wx_div" id="wx_show">
                    </div>
                </div>
                <input class="but2" value="保存" type="submit">
            </div>
            <div style="clear: both"></div>
        </form>
    </div>
</div>
<script>
    function show_wechat() {
        var server_sel = $('#csid option:selected') .val();
        var good_sel = $('#goods_id option:selected') .val();
        var pg_sel = $('#pgid option:selected') .val();
        var tg_sel = $('#tg_id option:selected') .val();
        $.ajax({
            url: '/admin/timetable/getWachat/',
            type: 'POST',
            data:{'csid':server_sel,'goods_id':good_sel,'pgid':pg_sel,'tg_id':tg_sel},
            dataType: 'json',
            success: function (data, status) {
                if(!data){
                    alert('无符合条件的微信号！')
                }else{
                    var str = '';
                    for(var i in data){
                        str+="<span id="+data[i].wechat_id+'_'+data[i].id+" onclick=select_wechat('"+data[i].wechat_id+"',"+data[i].id+")>"+data[i].wechat_id+"</span>"
                    }
                    $("#wx_show").html(str);
                }
            },
            fail: function (err, status) {
            }
        });
    }
    function select_wechat(wechat_id,id) {
        var wx_string = $("#wx_input").val()+'\n';
        var old_val =  $("#wx_input").val();
        $("#wx_input").val(old_val+wechat_id+'\n');
        $("#"+wechat_id+'_'+id).remove();
    }
    function chekFrom(){
        var star_date_sel = $("input[name='start_date']").val();
        var end_date_sel = $("input[name='end_date']").val();
        if(star_date_sel == '' || end_date_sel == ''){
            alert('请先选择排期时间!');
            return false;
        }
        var type_sel = $('#type option:selected') .val();
        if(type_sel == ''){
            alert('请先选择排期类型!');
            return false;
        }
        var count_sel = $('#count option:selected').val();
        if(count_sel == '' || count_sel == undefined){
            alert('请先选择排期数值!');
            return false;
        }
        var wx_string = $("#wx_input").val()+'\n';
        var wx_list = wx_string.split('\n');
        var wechat_count = 0;
        var useful_wechat = [];
        for(var i = 0; i < wx_list.length; i++){
            if(!isNull(wx_list[i])){
                wechat_count++;
                useful_wechat.push($.trim(wx_list[i]));
            }
        }
        if(wechat_count <=0){
            alert('请先输入微信号!');
            return false;
        }
        var wx_str = useful_wechat.join(',');
        $("input[name='wechat_select']").val(wx_str);
    }
    function isNull( str ){
        if ( str == "" ) return true;
        var regu = "^[ ]+$";
        var re = new RegExp(regu);
        return re.test(str);
    }
</script>

