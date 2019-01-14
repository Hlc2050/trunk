<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<style>
    #selected_list {
        max-width: 800px;
        margin: 10px 0;
    }
    #selected_list span{
        display: inline-block;
        border:solid 1px #ccc;
        border-radius: 5px;
        padding: 5px;
        font-size: 12px;
        margin-right: 8px;
        margin-bottom: 5px;
    }
    #selected_list span input{
        margin-right: 5px;
    }
    </style>
<?php
//页面默认值
if (!isset($page['info'])) {
    $page['info']['id'] = '';
    $page['info']['domain_id'] = '';
    $page['info']['articleTitle'] = '';
    $page['info']['finance_pay_id'] = '';
    $page['info']['independent_cnzz'] = '';
    $page['info']['total_cnzz'] = '';
    $page['info']['origin_template_id'] = '';
    $page['info']['promotion_staff_id'] = '';
    $page['info']['promotion_type'] = $page['info']['promotion_type'] ? $page['info']['promotion_type'] : 0;
}

?>
<div class="main mhead">
    <div class="snav">推广管理 » 推广列表 » <?php echo $page['info']['id'] ? '修改推广' : '添加推广' ?></div>
</div>

<div class="main mbody" onload="Init()">
    <form name=frm method="post"
          action="<?php echo $this->createUrl($page['info']['id'] ? 'promotion/edit' : 'promotion/add'); ?>?p=<?php echo $_GET['p']; ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <input type="hidden" id="fpay_id" name="fpay_id" value="<?php echo $page['info']['finance_pay_id']; ?>"/>
        <input type="hidden" id="tg_id" name="tg_id" value="<?php echo $page['info']['sno']; ?>"/>
        <input type="hidden" id="backurl" name="backurl" value="<?php echo $this->get('url'); ?>"/>
        <table class="tb3">

            <?php if (!$page['info']['id']) { ?>
                <tr>
                    <th colspan="2" class="alignleft">添加推广</th>
                </tr>

                <tr>
                    <td width="100">合作商：</td>
                    <td>
                        <?php
                        $partnerList = $this->toArr(Partner::model()->findAll());
                        echo CHtml::dropDownList('partnerId_temp', '', CHtml::listData($partnerList, 'id', 'name'),
                            array(
                                'empty' => '请选择',
                                'id' => 'partnerId_temp',
                                'style' => 'display:none'
                            )
                        );
                        ?>
                        <span id="demo">
                        <?php
                        echo CHtml::dropDownList('partnerId', '', '',
                            array(
                                'empty' => '请选择',
                                'id' => 'partnerId',
                            )
                        );
                        ?>
                    </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input class="ipt" style="font-size:small;width: 100px" name="txt" onkeyup="SelectTip(0)"/>
                    </td>
                </tr>
                <tr>
                    <td width="120">渠道名称：</td>
                    <td class="alignleft">
                        <?php echo CHtml::dropDownList('channel_id', '', array('请选择')); ?>
                    </td>
                </tr>
                <tr>
                    <td width="120">上线日期：</td>
                    <td class="alignleft">
                        <?php echo CHtml::dropDownList('online_date', '', array('请选择')); ?>
                    </td>
                </tr>
                <tr>
                    <td width="120">渠道编码：</td>
                    <td class="alignleft">
                        <input type="text" class="ipt" id="channelCode"
                               disabled/>
                    </td>
                </tr>
                <tr>
                    <td width="120">计费方式：</td>
                    <td class="alignleft">
                        <input type="text" class="ipt" id="chgId" disabled/>
                    </td>
                </tr>
                <tr>
                    <td width="120">计费单价：</td>
                    <td class="alignleft">
                        <input type="text" class="ipt" id="unitPrice"
                               disabled/>
                    </td>
                </tr>
                <tr>
                    <td width="120">单位：</td>
                    <td class="alignleft">
                        <input type="text" class="ipt" id="unit" value="元/次" disabled/>
                    </td>
                </tr>
                <tr>
                    <td width="120">计费公式：</td>
                    <td class="alignleft">
                        <input type="text" class="ipt" id="formula" disabled/>
                    </td>
                </tr>
                <tr>
                    <td width="120">微信号小组：</td>
                    <td class="alignleft">
                        <input type="text" class="ipt" id="wechat_group" disabled/>
                    </td>
                </tr>
                <tr>
                    <td width="120">推广人员：</td>
                    <td class="alignleft">
                        <input type="text" class="ipt" id="tg_name" disabled/>
                    </td>
                </tr>
            <?php } else { ?>
                <tr>
                    <th colspan="2" class="alignleft">修改推广</th>
                </tr>
                <tr>
                    <td width="120">合作商</td>
                    <td>
                        <?php echo $page['info']['partner']; ?>
                    </td>
                </tr>
                <tr>
                    <td width="120">渠道名称：</td>
                    <td class="alignleft">
                        <?php echo $page['info']['channel_name']; ?>
                    </td>
                </tr>
                <tr>
                    <td width="120">上线日期：</td>
                    <td class="alignleft">
                        <?php echo $page['info']['online_date']; ?>
                    </td>
                </tr>
                <tr>
                    <td width="120">渠道编码：</td>
                    <td class="alignleft">
                        <?php echo $page['info']['channel_code']; ?>
                    </td>
                </tr>
                <tr>
                    <td width="120">计费方式：</td>
                    <td class="alignleft">
                        <?php echo $page['info']['charging_type']; ?>
                    </td>
                </tr>
                <tr>
                    <td width="120">计费单价：</td>
                    <td class="alignleft">
                        <?php echo $page['info']['unit_price']; ?>
                    </td>
                </tr>
                <tr>
                    <td width="120">单位：</td>
                    <td class="alignleft">元/次</td>
                </tr>
                <tr>
                    <td width="120">计费公式：</td>
                    <td class="alignleft">
                        <?php echo $page['info']['formula']; ?>
                    </td>
                </tr>
                <tr>
                    <td width="120">微信号小组：</td>
                    <td class="alignleft">
                        <?php echo $page['info']['wechat_group']; ?>
                    </td>
                </tr>
                <tr>
                    <td width="120">推广人员：</td>
                    <td class="alignleft">
                        <?php echo $page['info']['tg_name']; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <span style="color: red">提示；修改推广无法改动以上项目</span>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <td>推广类型：</td>
                <td>
                    <?php
                    $promotion_types = vars::$fields['promotion_types'];
                    foreach ($promotion_types as $val) {
                        ?>
                        <input name="promotion_type" <?php echo $page['info']['promotion_type'] == $val['value'] ? "checked" : ""; ?>
                               type="radio" value="<?php echo $val['value']; ?>"/>
                        <?php
                        echo $val['txt'];
                    } ?>
                </td>
            </tr>

            <tr>
                <td>PC端与手机端一致：</td>
                <td>
                    <input name="is_pc_show" <?php echo $page['info']['is_pc_show'] == 0 ? "checked" : ""; ?>
                           type="radio" value="0" onclick="showtext(this.value)"/> 是
                    <input name="is_pc_show" <?php echo $page['info']['is_pc_show'] == 1 ? "checked" : ""; ?>
                           type="radio" value="1" onclick="showtext(this.value)"/> 否
                </td>
            </tr>
            <tr class="PCUrl" <?php echo $page['info']['is_pc_show'] == 1 ? '' : 'hidden' ?>>
                <td>
                    PC端显示网址：
                </td>
                <td>
                    <input type="text" class="ipt" id="pc_url" name="pc_url"
                           value="<?php echo $page['info']['pc_url'] ? $page['info']['pc_url'] : ""; ?>"/>
                </td>
            </tr>
            <tr>
                <td>链接类型</td>
                <td><input name="line_type" <?php echo $page['info']['line_type'] == 0 ? "checked" : ""; ?> value="0" type="radio" onclick="hideWhiteDomain(1)">普通&nbsp;&nbsp;
                    <input name="line_type" <?php echo $page['info']['line_type'] == 1 ? "checked" : ""; ?> value="1" type="radio" onclick="hideWhiteDomain(2)">静态</td>
            </tr>
            <tr id="static_file" style=" <?php if($page['info']['line_type'] == 0){echo "display:none";}?>">
                <td>静态文件夹名</td>
                <td><?php echo helper::getPromotionFolder($page['info']['id']); ?></td>
            </tr>
            <tr id="article_title" style=" <?php if($page['info']['line_type'] == 1){echo "display:none";}?>">
                <td width="120">素材编码：</td>
                <td class="alignleft">
                    <input type="text" class="ipt article_title" style="width: 199px;" name="article_title" id="article"
                           value="<?php $data = MaterialArticleTemplate::model()->findByPk($page['info']['origin_template_id']);
                           echo $data['article_code']; ?>" size="30"/>
                    <input type="hidden" class="ipt article_id" id="article_id" name="article_id"
                           value="<?php echo $page['info']['origin_template_id']; ?>"/>
                </td>
            </tr>
            <tr>
                <?php
                //推广类型
                if ($page['info']['promotion_type'] != 3) {
                    $domain_type = 0;
                    $is_short = '';
                }else {
                    $domain_type = 3;
                    $is_short = '短';
                }
                ?>
                <td width="120" id="label_fro_domain">推广<?php echo $is_short;?>域名：</td>
                <td class="alignleft">
                    <?php if ($page['info']['id']) { ?>
                        <input type="hidden" id="last_domain" name="last_domain"
                               value="<?php echo implode(',',array_column($page['info']['domain_list'],'id'))?>"/>
                    <div id="selected_list">
                        <?php
                        $i = 1;
                        foreach ($page['info']['domain_list'] as $d) {
                            $add_str = '';
                            if ($d['is_https'] == 1) {
                                $add_str.='https';
                            }
                            if ($d['is_public_domain'] == 1) {
                                if ($add_str){
                                    $add_str.='、公众号';
                                }else {
                                    $add_str.='公众号';
                                }
                            }
                            if ($add_str) {
                                $add_str='('.$add_str.')';
                            }
                        ?>
                            <span>
                                <input value="<?php echo $d['id'];?>" name="domain_id[]" checked="" onchange="delDomain(this)" type="checkbox">
                                <?php echo $d['domain'].$add_str;?>
                            </span>
                        <?php }?>
                    </div>
                        <input type="button" class="but2" id="domain_select" value="重新选择域名" onclick="return domain_dialog(this,400,800)"
                               href="" />
                    <?php } else { ?>
                        <input type="button" class="but2" id="domain_select" value="选择域名" style="background-color: #CCCCCC" onclick="return domain_dialog(this,400,800)"
                               href="" disabled/>
                    <?php } ?>
                    <input type="hidden" name="domain_ids" value="<?php echo implode(',',array_column($page['info']['domain_list'],'id'))?>">
                </td>
            </tr>

            <tr>
                <td width="120">跳转域名：</td>
                <td class="alignleft">
                    <?php if ($page['info']['id']) {
                        $domainList = DomainList::model()->getGotoDomains($page['info']['sno'],$page['info']['line_type']);
                        echo CHtml::dropDownList('goto_domain_id', $page['info']['goto_domain_id'], CHtml::listData($domainList, 'id', 'domain'),
                            array('empty' => '请选择跳转域名',)
                        );
                        ?>
                        <span>原本跳转域名：<?php echo DomainList::model()->findByPk($page['info']['goto_domain_id'])->domain; ?></span>

                        <input type="hidden" id="last_goto_domain" name="last_goto_domain"
                                    value="<?php echo $page['info']['goto_domain_id']; ?>"/>
                    <?php } else { ?>
                        <?php echo CHtml::dropDownList('goto_domain_id', '', array('请选择跳转域名')); ?>
                    <?php } ?>
                </td>
            </tr>

            <tr>
                <td>选择路由规则：</td>
                <td><?php
                    $url_rule = vars::$fields['url_rule'];
                    echo CHtml::dropDownList('rule', $page['info']['url_rule'], CHtml::listData($url_rule, 'value', 'txt'),
                        array(
                            'empty' => '未选择路由规则为默认',
                        )
                    );
                    ?></td>
            </tr>

            <tr>
                <td width="120">独立统计代码：</td>
                <td>
                    <textarea id="indCnzz" name="indCnzz"><?php echo $page['info']['independent_cnzz']; ?> </textarea>
                </td>
            </tr>

            <tr>
                <td width="120">扣量比例：</td>
                <td class="alignleft">
                    <input type="text" name="minus_proportion" value="<?php echo $page['info']['minus_proportion']; ?>" style="height: 23px;">*多少次访问有1次不加载cnzz代码，为空不扣量
                </td>
            </tr>

            <tr>
                <td width="120">不扣量地区设置</td>
                <td id="add_box">
                    <?php if ($page['info']['id'] && $address_info['provinces']) { ?>
                        <?php foreach($address_info['provinces'] as $k=>$value){?>
                            <div>
                                <select  class="province" id="province_<?php echo $k+1?>" data-id="<?php echo $k+1?>" data-city-id="<?php echo $address_info['citys'][$k]?>" name="provinces[]" >
                                      <option value="0">请选择</option>
                                      <?php foreach($province_data as $key=>$val){?>
                                      <option <?php if($val['linkage_id'] == $value){?>selected="selected"<?php }?> value="<?php echo $val['linkage_id']?>"><?php echo $val['linkage_name']?></option>
                                      <?php }?>
                                </select>
                                <select class="city" id="city_<?php echo $k+1?>" data-id="<?php echo $k+1?>" name="citys[]">
                                    <option value="0">请选择</option>
                                </select>
                                <?php if($k < 1){ ?>
                                    <a id="add" data-id="<?php echo $address_info['province_num']?>" href="javascript:;" style="font-size:20px;color: red">+</a>
                                <?php }else{ ?>
                                    <a class="delete" id="delete_<?php echo $k+1?>" data-id="<?php echo $k+1?>" href="javascript:;" style="font-size:15px;color: red"> X</a></div>
                                <?php }?>
                            </div>
                        <?php }?>
                    <?php }else{?>
                        <div>
                            <select class="province" id="province_1" data-id="1" data-city-id="0" name="provinces[]" >
                                  <option value="0">请选择</option>
                                  <?php foreach($province_data as $key=>$val){?>
                                  <option value="<?php echo $val['linkage_id']?>"><?php echo $val['linkage_name']?></option>
                                  <?php }?>
                            </select>
                            <select class="city" id="city_1" data-id="1" name="citys[]">
                                <option value="0">请选择</option>
                            </select>
                            <a id="add" data-id="1" href="javascript:;" style="font-size:20px;color: red">+</a>
                        </div>
                    <?php }?>
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="alignleft"><input type="submit" class="but" id="subtn" value="确定"/>
                    <input type="button" class="but" value="返回"
                           onclick="window.location='<?php echo $this->get('url'); ?>'"/>
                </td>
            </tr>
            <tr>
                <td style="height: 200px"></td>
            </tr>

        </table>
        <script language="javascript">
            //路由规则显示
            var select_line_type = $("input[name=line_type]:checked").val();
            if (select_line_type == 1) {
                $("#rule").val('');
                $("#rule").attr('disabled','disabled');
            }
            //点击隐藏白域名选项和素材编码
           var line_type_check = 0;
            function hideWhiteDomain(n)
            {
                var old_goto_id = $("input[name='last_goto_domain']").val();
                if(n==1)
                {
                    line_type_check = 0;
                    document.getElementById('article_title').style.display = 'none';
                    document.getElementById('article_title').style.display = '';
                    document.getElementById('static_file').style.display = '';
                    document.getElementById('static_file').style.display = 'none';
                    $("#article").val('');
                    $("#article_id").val('');
                    $("#rule").removeAttr('disabled');
                    delAllSelectDomain();
                }
                if(n==2)
                {
                    line_type_check = 1;
                    document.getElementById('article_title').style.display = '';
                    document.getElementById('article_title').style.display = 'none';
                    document.getElementById('static_file').style.display = 'none';
                    document.getElementById('static_file').style.display = '';
                    $("#article").val('');
                    $("#article_id").val('');
                    $("#rule").val('');
                    $("#rule").attr('disabled','disabled');
                    delAllSelectDomain();
                }
                jQuery.ajax({
                    type: "POST",
                    url: "/admin/promotion/getGotoDomain",
                    data: {application_type:line_type_check,tg_id:$("#tg_id").val(),old_goto_id:old_goto_id},
                    dataType: "json",
                    success: function (result) {
                        if(result){
                            console.log(result.length);
                            var html = '';
                            var data ='';
                            html += '<option value="">'+ '请选择跳转域名' + '</option>';
                            for ( var i = 0,l = result.length; i < l; i++ ) {
                                var is_https = result[i].is_https;
                                var is_public_domain=result[i].is_public_domain;
                                var id =result[i].id;
                                if(is_https == 1 && is_public_domain == 1){
                                    data ="(https、公众号)";
                                }else if(is_https == 1){
                                    data = "(https)";
                                }else if(is_public_domain == 1){
                                    data = "(公众号)";
                                }

                                html += '<option value='+id+'>'+ result[i].domain+ data
                                    + '</option>'
                            }
                            $("#goto_domain_id").html(html);
                        }
                    }
                });

            }


            //添加地址选择
            $("#add").click(function () {
                var num = $(this).attr("data-id");
                var last_num = Number(num) + 1;
                $(this).attr("data-id",last_num);
                var html = '<div>'
                    +'<select class="province" id="province_'+last_num+'" data-id="'+last_num+'" data-city-id="0" name="provinces[]"><option value="0">请选择</option>'
                    +'<?php foreach($province_data as $key=>$val){?><option value="<?php echo $val['linkage_id']?>"><?php echo $val['linkage_name']?></option><?php }?>'
                    +'</select> '
                    +'<select class="city" id="city_'+last_num+'" data-id="'+last_num+'" name="citys[]"><option value="0">请选择</option></select>'
                    +'<a class="delete" id="delete_'+last_num+'" data-id="'+last_num+'" href="javascript:;" style="font-size:15px;color: red"> X</a></div>';
                $("#add_box").append(html);
            });
            //删除地址选择
            $(".delete").live("click", function(){
                var num = $(this).attr("data-id");
                $("#province_"+num).remove();
                $("#city_"+num).remove();
                $("#delete_"+num).remove();
            });
            //省份选择  
            $(".province").live("change",function(){
                var myInput = $(this);
                var num = $(this).attr("data-id");
                var city_id = $(this).attr("data-city-id");
                var key = myInput.val();
                jQuery.ajax({
                    type: 'POST',
                    url: '/admin/promotion/getProvince',
                    dataType: "json",
                    data: {id: key},
                    success: function (result) {
                        if(result.IsSuccess){
                            var html = '<option>全省</option>'
                            for ( var i = 0,l = result.data.length; i < l; i++ ){
                                if(result.data[i].linkage_id == city_id){
                                    html += '<option selected="selected" value="'+result.data[i].linkage_id+'">'+result.data[i].linkage_name+'</option>';
                                }else{
                                    html += '<option value="'+result.data[i].linkage_id+'">'+result.data[i].linkage_name+'</option>';
                                }
                            }
                            $("#city_"+num).html(html);
                        }else{
                            var html = '<option value="">请选择</option>';
                            $("#city_"+num).html(html);
                        }
                    }
                });
            });
            <?php if ($page['info']['id']) { ?>
            $('.province').trigger('change');
            <?php }?>
            /**
             * 模糊搜索合作商
             * @type {Array}
             */
            var TempArr = [];//存贮option
            function Init() {
                var SelectObj = document.frm.elements["partnerId_temp"];
                /*先将数据存入数组*/
                with (SelectObj)
                    for (i = 0; i < length; i++) TempArr[i] = [options[i].text, options[i].value];
            }

            function SelectTip(flag) {
                domain_disble();
                delAllSelectDomain();
                var TxtObj = document.frm.elements["txt"];
                console.log(TxtObj.value);
                var SelectObj = document.getElementById("demo");
                var Arr = [];
                console.log(0, SelectObj);
                with (SelectObj) {
                    var SelectHTML = innerHTML.match(/<[^>]*>/)[0];
                    if (TxtObj.value == '') Arr[Arr.length] = "<option value=''>请选择</option>"
                    else {
                        for (i = 0; i < TempArr.length; i++)
                            if (TempArr[i][0].indexOf(TxtObj.value) != -1 || flag)//若找到以txt的内容，添option。若flag为true,对下拉框初始化
                                Arr[Arr.length] = "<option value='" + TempArr[i][1] + "'>" + TempArr[i][0] + "</option>"
                    }
                    innerHTML = SelectHTML + Arr.join();
                    secondChange();
                }
            }

            window.onload = function () {
                Init();
            };

            //合作商 渠道联动搜索
            jQuery(function ($) {
                jQuery('body').on('change', '#partnerId', secondChange);
            });

            function secondChange() {
                jQuery.ajax({
                    'type': 'POST',
                    'url': '/admin/promotion/getChannel',
                    'data': {'partnerId': $("#partnerId").val()},
                    'cache': false,
                    'success': function (html) {
                        jQuery("#channel_id").html(html);
                        thirdChange();
                    }
                });
                return false;
            }

            //渠道 上线日期联动查询
            jQuery(function ($) {
                jQuery('body').on('change', '#channel_id', thirdChange);
            });

            function thirdChange() {
                jQuery.ajax({
                    'type': 'POST',
                    'url': '/admin/promotion/getOnlineDate',
                    'data': {'channel_id': $("#channel_id").val()},
                    'cache': false,
                    'success': function (html) {
                        jQuery("#online_date").html(html)
                        fourthChange();
                    }
                });
                return false;
            }

            //上线日期变化 其他数据也出现
            jQuery(function ($) {
                jQuery('body').on('change', '#online_date', fourthChange);
            });


            function fourthChange() {
                domain_disble();
                delAllSelectDomain();
                jQuery.ajax({
                    'type': 'POST',
                    'url': '/admin/promotion/getOtherData',
                    'data': {'onlineDate': $("#online_date").val(), 'channel_id': $("#channel_id").val(),'line_type':line_type_check},
                    'cache': false,
                    'success': function (result) {
                        json = eval('(' + result + ')');

                        jQuery("#fpay_id").val(json.fpay_id);//打款id
                        jQuery("#channelCode").val(json.channelCode);//渠道编码
                        jQuery("#chgId").val(json.chgId);//计费方式
                        jQuery("#unitPrice").val(json.unitPrice);//单价
                        jQuery("#formula").val(json.formula);//计费公式
                        jQuery("#wechat_group").val(json.wechat_group);//微信号小组
                        jQuery("#tg_name").val(json.tg_name);//推广人员
                        jQuery("#tg_id").val(json.tg_id);//推广人员
                        jQuery("#goto_domain_id").html(json.goto_domains);
                        if (json.fpay_id && json.channelCode && json.tg_id) {
                            domain_enable();
                        }
                    }
                });
                return false;
            }

            $(document).ready(function () {
                $('.article_title').on('keyup focus', function () {
                    $('.searchsBox').show();
                    var myInput = $(this);
                    var key = myInput.val();
                    var postdata = {search_type: 'keys', search_txt: key};
                    $.getJSON('<?php echo $this->createUrl('promotion/getArticleList') ?>?jsoncallback=?', postdata, function (reponse) {
                        try {
                            if (reponse.state < 1) {
                                alert(reponse.msg);
                                return false;
                            }
                            var html = '';
                            for (var i = 0; i < reponse.data.list.length; i++) {
                                html += '<a href="javascript:void(0);" data-id="' + reponse.data.list[i].id + '" data-article_title="' + reponse.data.list[i].article_title + '" ' +
                                    'data-article_code="' + reponse.data.list[i].article_code + '"  ' +
                                    'onmouseDown="getTipsValue(this);"   style="display:block;font-size:12px;padding:2px 5px;">' + reponse.data.list[i].article_title + '(' + reponse.data.list[i].article_code + ')</a>';
                            }
                            var s_height = myInput.height();
                            var top = myInput.offset().top + s_height;
                            var left = myInput.offset().left;
                            var width = myInput.width();
                            $('.searchsBox').remove();
                            $('body').append('<div class="searchsBox" style="position:absolute;top:' + top + 'px;left:' + left + 'px;background:#fff;z-index:2;border:1px solid #ccc;width:' + width + 'px;">' + html + '</div>');
                        } catch (e) {
                            alert(e.message)
                        }
                    });
                    myInput.blur(function () {
                        $('.searchsBox').hide();
                    })
                });
            });

            function getTipsValue(ele) {
                var myobj = $(ele);
                var id = myobj.attr('data-id');
                var article_title = myobj.attr('data-article_title');
                var article_code = myobj.attr('data-article_code');
                $('.article_title').val(article_code);
                $('#article_id').val(id);

            }

            //切换底部类型
            function showtext(value) {
                if (value == 0) {
                    $(".PCUrl").hide();
                } else if (value == 1) {
                    $(".PCUrl").show();
                }
            }
        </script>
    </form>
</div>
<style>
    .searchsBox a:hover {
        background: #eee;
    }

</style>
<script>
    var old_promotion_type = $("input[name='promotion_type']:checked").val();
    var old_domain_id  = <?php echo $page['info']['domain_id'] ? $page['info']['domain_id']:0;?>;
    $("input[name='promotion_type']").change(function () {
        var val = $(this).val();
        var line_type = $("input[name='line_type']:checked").val();
        if (val == 3) {
            $("#label_fro_domain").html('推广短域名:');
        }else {
            $("#label_fro_domain").html('推广域名:');
        }
        if ((old_promotion_type != 3 && val== 3 )|| (old_promotion_type == 3 && val!=3)) {
            delAllSelectDomain();
        }
        old_promotion_type = val;
    });

    function domain_enable() {
        $('#domain_select').css('background','#2fa4e7');
        $('#domain_select').removeAttr('disabled');
    }
    function domain_disble() {
        $('#domain_select').css('background','#cccccc');
        $('#domain_select').attr('disabled','disabled');
    }
    function delAllSelectDomain() {
        $("input[name='domain_ids']").val('');
        $("#selected_list").remove();
    }

    function domain_dialog(element,height,width) {
        try{
            if(!width||!height){
                width='90%';
                height='90%';
            }
            var application_type = $("input[name='line_type']:checked").val();
            var promotion_type = $("input[name='promotion_type']:checked").val();
            var tg_uid = $("#tg_id").val();
            var url = "<?php echo $this->createUrl('promotion/selectDomains')?>?application_type="+application_type+"&promotion_type="+promotion_type+"&tg_uid="+tg_uid;
            var selected_id = $("input[name='domain_ids']").val();
            url += "&selected_id="+selected_id;
            url += "&promotion_id=<?php echo $page['info']['id'];?>";
            delAllSelectDomain();
            art.dialog.open(url,{
                title:$(element).html(),
                width:width,
                height:height,
                lock:true,
                close:function(){
                    var select_domainIds = art.dialog.data('select_domainIds'); // 读取子窗口返回的数据
                    if (select_domainIds != undefined){
                        $("input[name='domain_ids']").val(select_domainIds);
                    }
                    var select_domains = art.dialog.data('select_domains'); // 读取子窗口返回的数据
                    if (select_domains != undefined) {
                        var html = '<div id="selected_list">';
                        for (var i=0;i<select_domains.length;i++) {
                            var info = select_domains[i];
                            html += '<span><input value="'+info['id']+'" name="domain_id[]" checked="" onchange="delDomain(this)" type="checkbox">'+info['domain']+'</span>';
                        }
                        html += '</div>';
                        $("#domain_select").before(html);
                    }

                }
            });
        }catch(e){alert(e.message);}
        return false;
    }
    
    function delDomain(dom) {
        var span = $(dom);
        span.parent().remove();
        var ids = getDomainIds();
        console.log(ids);
        $("input[name='domain_ids']").val(ids.join(','));

    }
    //已选择的域名
    function getDomainIds() {
        var select_ids = [];
        $("input[name='domain_id[]']").each(function () {
            select_ids.push($(this).val());
        });
        return select_ids;
    }
</script>
