<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<style>
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

    #unselected_domain {
        margin: 0 10px;
    }
    #unselected_domain span{
        display: inline-block;
        border:solid 1px #ccc;
        border-radius: 5px;
        padding: 5px;
        font-size: 13px;
        margin-right: 8px;
        margin-bottom: 5px;
    }
    .select {
        background-color: #f6f8fa;
    }
</style>
<div class="main mhead">
    <div class="snav">添加推广》随机推广域名选择</div>
    <div class="mt10">
        <form id="serchRet">
            <div class="mt10">
                域名：
                <input style="width: 120px" type="text" id="domain" name="domain" class="ipt"
                       value="<?php echo $this->get('domain'); ?>">
                总统计组别：
                <?php
                $cnzz = Dtable::toArr(CnzzCodeManage::model()->findAll());
                echo CHtml::dropDownList('cnzz_code_id', $this->get('cnzz_code_id'), CHtml::listData($cnzz, 'id', 'name'),
                    array('empty' => '全部')
                );
                ?>
                <input type="button" class="but"  value="查询" id="search_btn">
                <input type="hidden" name="search_url"
                       value="<?php echo $this->createUrl('promotion/freshDomains')?>?application_type=<?php echo $this->get('application_type');?>&promotion_type=<?php echo $this->get('promotion_type');?>&tg_uid=<?php echo $this->get('tg_uid');?>&promotion_id=<?php echo $this->get('promotion_id');?>">
            </div>
    </div>
    <div class="mt10" id="selected_list">
        <?php if ($page['promotion_domain']){?>
        <div class="mt10">
            <div class="snav" style="margin: 10px 0;">推广原域名</div>
            <?php
            $i=1;
            foreach ($page['promotion_domain'] as  $val) {?>
                    <label style="margin-right:10px"><?php echo $i.':&nbsp;'.$val['domain'];?></label>
            <?php
                $i++;
            } ?>
        </div>
        <?php }?>
        <div class="snav" style="margin: 10px 0;">已选择域名</div>
        <?php foreach ($page['select_domain'] as  $val) {?>
            <span id="check_<?php echo $val['id'];?>"><input name="domain_id[]" value="<?php echo $val['id'];?>" checked="" type="checkbox" onchange="delSelectedDomain(this)"><label><?php echo $val['domain'];?></label></span>
        <?php } ?>
    </div>

</div>
<div style="border: dotted 1px #cccccc;padding: 10px 0;margin: 10px 10px;">
    <div class="main mbody" id="unselected_domain">
        <?php $select_array = explode(',',$this->get('selected_id'));?>
        <?php foreach ($page['list'] as  $val) {
            $cla = '';
            if (in_array($val['id'],$select_array)) $cla = 'select';
         ?>
            <span class="<?php echo $cla;?>" id="span_<?php echo $val['id'];?>" onclick="selected(this,<?php echo $val['id'];?>,'<?php echo $val['domain'];?>')"><?php echo $val['domain'];?></span>
        <?php } ?>
    </div>
    <div class="pagebar" style="margin-left: 10px;"><?php echo $page['pagearr']['pagecode']; ?></div>
    <div style="clear: both"></div>
</div>

<div class="mt10" style="margin-left: 10px;">
    <input type="button" class="but"  value="保存" id="save_btn">
</div>
<script>
    setDialogData();
    pager_click();
    $("#search_btn").click(function () {
        var url = $("input[name='search_url']").val();
        freshDomains(url);

    });
    $("#save_btn").click(function () {
        setDialogData();
        art.dialog.close();
    });
    function setDialogData() {
        var select_ids = getDomainIds();
        var select_str = select_ids.join(',');
        var select_domains = getDomains();
        //将值存起来，供父页面读取
        artDialog.data("select_domainIds", select_str);
        artDialog.data("select_domains", select_domains);
    }
    function selected(dom,domain_id,domian) {
        var span = $(dom);
        console.log(span.hasClass('select'));
        //未选择
        if (!span.hasClass('select')) {
            span.addClass('select');
            span.attr('disabled','disabled');
            addSelectedDomain(domain_id,domian);
        }
    }

    function addSelectedDomain(domain_id,domian) {
        var select_div = $("#selected_list");
        if ($('#check_'+domain_id).length <=0) {
            var html='<span id="check_'+domain_id+'"><input name="domain_id[]" value="'+domain_id+'" checked="" type="checkbox" onchange="delSelectedDomain(this)"><label>'+domian+'</label></span>';
            select_div.append(html);
        }
    }
    
    function delSelectedDomain(dom) {
        var span = $(dom);
        var domain_id = span.val();
        span.parent().remove();
        $("#span_"+domain_id).removeClass('select');
        $("#span_"+domain_id).removeAttr('disabled');
    }

    //重写分页a标签点击事件
    function pager_click() {
        $(".pagebar").find('a').each(function () {
            var url = $(this).attr('href');
            $(this).click(function () {
                $(this).attr('href', '#');
                freshDomains(url);
            });
        });
    }

    //搜索、分页时刷新域名
    function freshDomains(url) {
        var domain = $("input[name='domain']").val();
        var cnzz_code_id = $("#cnzz_code_id").val();
        url = url+'&domain='+domain+'&cnzz_code_id='+cnzz_code_id;
        var new_url = url.replace('selectDomains','freshDomains');
        jQuery.ajax({
            type: "GET",
            url: new_url,
            dataType: "json",
            cache: false,
            async: false,
            success: function (result) {
                var html ='';
                if(result['list']){
                    var domains = result['list'];
                    var select_ids = getDomainIds();
                    for (var i=0;i<domains.length;i++) {
                        var item = domains[i];
                        var sel_cla = '';
                        var item_id = item['id'];
                        var item_domain = item['domain'];
                        if ($.inArray(item['id'], select_ids) != -1) {
                            sel_cla='select';
                        }
                        html +='<span class="'+sel_cla+'" id="span_'+item_id+'" onclick="selected(this,'+item_id+',\''+item_domain+'\')">'+item_domain+'</span>'
                    }
                    var psge = '';
                    $(".pagebar").html(result['pagearr']['pagecode']);
                }
                if (result.length <=0) {
                    html += '<option value="">请选择域名</option>'
                }
                $("#unselected_domain").html(html);
                pager_click();
            }
        });
    }

    //已选择的域名
    function getDomainIds() {
        var select_ids = [];
        $("input[name='domain_id[]']").each(function () {
            if ($(this).attr('checked') == 'checked') {
                select_ids.push($(this).val());
            }

        });
        console.log(select_ids);
        return select_ids;
    }
    //已选择域名数组
    function getDomains() {
        var select_domains = [];
        $("input[name='domain_id[]']").each(function () {
            var arr = [];
            if ($(this).attr('checked') == 'checked') {
                arr['id'] = $(this).val();
                arr['domain'] = $(this).parent().find('label').html();
                select_domains.push(arr);
            }
        });
        console.log(select_domains);
        return select_domains;
    }

</script>
