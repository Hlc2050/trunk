<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<style>
    .quest{
        height:40px;
        line-height: 40px;
        width:602px;
        vertical-align:middle;
        font-size: large;
        color: black;
        background-color: #CCCCCC;
    }
    .fewer{
        float: right;
        margin-right: 15px;
        color: blue;
        font-weight: normal;
        font-size: 13px;
    }
    .deleteTr{
        float: right;
        margin-right: 15px;
        color: blue;
        font-weight: normal;
        font-size: 13px;
    }
</style>
<div class="main mhead">
    <div class="snav">推广管理 » 问卷管理 » 修改问卷</div>
</div>
<div class="main mbody">
    <form method="post"
          action="<?php echo $this->createUrl($page['info']['id'] ? 'material/editQuestion' : 'material/addQuestionnaire'); ?>?p=<?php echo $_GET['p']; ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <table class="tb3" id="questionnaire">
            <tbody>
            <tr>
                <td style="vertical-align:middle;">
                    投票名称：
                    <input style="width: 350px;height:30px;font-size: large;" type="text" class="ipt"
                           name="vote_title"
                           value="<?php echo $page['info']['vote_title'] ? $page['info']['vote_title'] : ""; ?>"/>
                    <span style="color: red">*必填</span>
                </td>
            </tr>
            <tr>
                <td>商品类别：
                    <select name="cat_id">
                        <option value="" selected>
                            请选择类别
                        </option>
                        <?php
                        //商品类别列表
                        $categoryList=Linkage::model()->getGoodsCategoryList();
                        foreach ($categoryList as $key => $val) {
                            ?>
                            <option
                                value="<?php echo $key; ?>" <?php echo $key==$page['info']['cat_id'] ? 'selected' : ''; ?>>
                                <?php echo $val;?>
                            </option>
                        <?php } ?>
                    </select>
                    <span style="color: red">*必填</span>
                </td>
            </tr>
            <tr>
                <td>支持人员：
                    <?php
                    //支持人员列表哦
                    $supportStafflist = SupportStaff::model()->getSupportStaffList();
                    echo CHtml::dropDownList('support_staff_id', $page['info']['support_staff_id'], CHtml::listData($supportStafflist, 'user_id', 'name'),
                        array(
                            'empty' => '选择支持人员',
                        )
                    );
                    ?>
                    <span style="color: red">*必填</span>
                </td>
            </tr>
            <tr>
                <td style="vertical-align:middle;width: 100px">
                    是否当做投票页：
                    <input name="is_vote" <?php echo $page['info']['is_vote'] == 0 ? "checked" : ""; ?>
                           onclick="addSth(this.value)" type="radio" value='0'/>否&nbsp;&nbsp;
                    <input name="is_vote" <?php echo $page['info']['is_vote'] == 1 ? "checked" : ""; ?>
                           onclick="addSth(this.value)" type="radio" value='1'/>是&nbsp;&nbsp;
                </td>
            </tr>
            <tr class="sth" <?php echo $page['info']['is_vote'] == 0?"hidden":'' ?>>
                <td>
                    投票页名称：
                    <input style="width: 350px;height:30px;font-size: large;" type="text" class="ipt"
                           name="vote_page"
                           value="<?php echo $page['info']['vote_page'] ? $page['info']['vote_page'] : ""; ?>"/>
                </td>
            </tr>
            <tr class="sth"  <?php echo $page['info']['is_vote'] == 0?"hidden":'' ?>>
                <td>
                    顶部banner：
                    <input type="hidden" id="top_img" name="top_img" value="<?php echo $page['info']['top_img'] ?>"/>
                    <img
                        id="top_show" <?php if ($page['info']['top_img']) echo "style='width: 100px;height: 100px;'"; ?>
                        src="<?php echo $page['info']['top_img'] ?>">
                    <a class="but1" onclick="return dialog_frame(this,450,580,false)"
                       href="<?php echo $this->createUrl('material/addMaterialPics'); ?>?add_type=1">选择素材库图片</a>
                </td>
            </tr>
            <tr class="sth"  <?php echo $page['info']['is_vote'] == 0?"hidden":'' ?>>
                <td>
                    小提示：&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input style="width: 350px;height:30px;font-size: large;" type="text" class="ipt"
                           name="tip"
                           value="<?php echo $page['info']['tip'] ? $page['info']['tip'] : ""; ?>"/>
                </td>
            </tr>
            <tr class="sth"  <?php echo $page['info']['is_vote'] == 0?"hidden":'' ?>>
                <td>
                    底部提示语：
                    <input style="width: 350px;height:30px;font-size: large;" type="text" class="ipt"
                           name="bottom_tip"
                           value="<?php echo $page['info']['bottom_tip'] ? $page['info']['bottom_tip'] : ""; ?>"/>
                </td>
            </tr>
            <tr>
                <td>
                    <span style="color: red">提示：选项不适合太长！</span>
                </td>
            </tr>
            <?php  foreach ($data as $k => $v){
              
                ?>
                <tr>
                    <td>
                        <input type="hidden" id="q_id" name="q_id[]" value="<?php echo $data[$k]['id']; ?>"/>
                        <div class="quest">问题<?php echo $k+1;?>：<?php if ( $k != 0 ) { ?><span onclick="deleteTr(this);" class="deleteTr" >删除</span><?php } ?><span class="fewer" onclick="showHide(this)">收起</span></div>
                        <div id="quest" style="border: 1px solid black;height: 210px;width: 600px;margin: 0;padding: 0;">
                            <table style="margin: 0 20px;border: none;">
                                <tr>
                                    <td width="70px">标题</td>
                                    <td>
                                        <input style="width: 300px;height:30px;font-size: large;" type="text" class="ipt"
                                               name="quest_title[]"
                                               value="<?php echo $v['quest_title']; ?>"/>
                                        <span style="color: red">*必填</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>A</td>
                                    <td>
                                        <input style="width: 300px;height:30px;font-size: large;" type="text" class="ipt"
                                               name="tab_a[]"
                                               value="<?php echo $v['tab_a']; ?>"/>
                                        <span style="color: red">*必填</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>B</td>
                                    <td>
                                        <input style="width: 300px;height:30px;font-size: large;" type="text" class="ipt"
                                               name="tab_b[]"
                                               value="<?php echo $v['tab_b']; ?>"/>
                                        <span style="color: red">*必填</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>C</td>
                                    <td>
                                        <input style="width: 300px;height:30px;font-size: large;" type="text" class="ipt"
                                               name="tab_c[]"
                                               value="<?php echo $v['tab_c']; ?>"/>
                                        <span style="color: red">*必填</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>D</td>
                                    <td>
                                        <input style="width: 300px;height:30px;font-size: large;" type="text" class="ipt"
                                               name="tab_d[]"
                                               value="<?php echo $v['tab_d']; ?>"/>
                                        <span style="color: red">*必填</span>
                                    </td>
                                </tr>
                            </table>

                        </div>
                    </td>
                </tr>
            <?php } ?>

            <tr class="a"></tr>
            <tr></tr>
            <tr>
                <td><div id="add_quest" onclick="" style="text-align: center;height:40px;line-height: 40px;" class="quest">+添加问题</div></td>
            </tr>

            <tr>
                <td class="alignleft">
                    <input style="font-size: medium" type="submit" class="but" id="subtn" value="保存"/>&nbsp;&nbsp;&nbsp;
                    <input style="font-size: medium" type="button" class="but" value="返回"
                           onclick="window.location='<?php echo $this->createUrl('material/index'); ?>?group_id=4&p=<?php echo $_GET['p']; ?>'"/>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>
<script>
    //添加一张图片 顶部背景图和头像
    function addOneImg(url,type) {

        var obj = $("#top_img");
        $("#top_show").attr('src', url);
        $("#top_show").css({width: 288, height: 40});

        $(obj).val(url);

    }
    //切换底部类型
    function addSth(value) {
        if (value == 0) {
            $(".sth").hide();
        } else if (value == 1) {
            $(".sth").show();
        }
    }
    function showHide(n) {
        var ns = $(n).parent().next('div');
        ns.slideToggle();
        var text =$(n).text();
        var text =text=='收起'?'编辑':'收起';
        $(n).text(text);
    }
    function deleteTr(nowTr){
        //多一个parent就代表向前一个标签,
        // 本删除范围为<td><tr>两个标签,即向前两个parent
        //如果多一个parent就会删除整个table
        console.log($(nowTr).parent().parent().children("input").val());
        $.ajax({
            type: "POST",
            url: '/admin/material/deletequestbank',
            data: {
                'id': $(nowTr).parent().parent().children("input").val()
            },
            success: function (data) {
               alert("删除问题成功");
            }
        });
        $(nowTr).parent().parent().remove();
    }
    function deleteTd(nowTd){
        $(nowTd).parent().parent().children("td").eq(0).children("span").eq(0).css("display","none");
        $(nowTd).parent().parent().children("td").eq(0).children("span").eq(1).css("display","block");
        $(nowTd).parent().parent().children("td").eq(1).css("display","none");
        $(nowTd).parent().parent().children("td").eq(1).children("input").val('');
    }
    function addTab(n) {
        var b = $(".quest").size();
        if ( b == 2 ) { b = 1 }else { b = b-1 }
        $(n).css("display","none");
        $(n).parent().children("span").eq(0).css("display","block");
        $(n).parent().parent().children("td").eq(1).css("display","block");

    }
    $(function () {
        $("#add_quest").click(function () {
            var b = $(".quest").size();
            if(b>=6){
                alert('你最多只可以增加5个问题');
                return false;
            }
            var addHtml = '<tr><td>'+
                '<div class="quest">问题'+b+'：<span onclick="deleteTr(this);" class="deleteTr" >删除</span><span class="fewer" onclick="showHide(this);" >编辑</span></div> '+
                '<div id="quest'+b+'" style="border: 1px solid black;height: 210px;width: 600px;margin: 0;padding: 0;display:none">'+
                '<table style="margin: 0 20px;border: none">'+
                '<tr>'+
                '<td width="70px">标题</td>'+
                '<td>'+
                '<input style="width: 300px;height:30px;font-size: large;" type="text" class="ipt" '+
                ' name="quest_title[]" '+
                '  value="<?php echo $page['info']['quest_title[]'] ? $page['info']['quest_title[]'] : ""; ?>"/>'+
                '<span style="color: red">*必填</span></td>'+
                '</tr>'+
                '<tr>'+
                '<td>A</td>'+
                '<td>'+
                '<input style="width: 300px;height:30px;font-size: large;" type="text" class="ipt"  '+
                ' name="tab_a[]" '+
                ' value="<?php echo $page['info']['tab_a[]'] ? $page['info']['tab_a[]'] : ""; ?>"/>'+
                '<span style="color: red">*必填</span></td>'+
                '</tr>'+
                '<tr>'+
                '<td>B</td>'+
                '<td>'+
                '<input style="width: 300px;height:30px;font-size: large;" type="text" class="ipt"  '+
                ' name="tab_b[]" '+
                ' value="<?php echo $page['info']['tab_b[]'] ? $page['info']['tab_b[]'] : ""; ?>"/>'+
                '<span style="color: red">*必填</span></td>'+
                '</tr>'+
                '<tr>'+
                '<td>C</td>'+
                '<td>'+
                '<input style="width: 300px;height:30px;font-size: large;" type="text" class="ipt" '+
                'name="tab_c[]"'+
                'value="<?php echo $page['info']['tab_c[]'] ? $page['info']['tab_c[]'] : ""; ?>"/>'+
                '<span style="color: red">*必填</span>'+
                '</td>'+
                '</tr>'+
                '<tr>'+
                '<td>D</td>'+
                '<td>'+
                '<input style="width: 300px;height:30px;font-size: large;" type="text" class="ipt" '+
                'name="tab_d[]"'+
                'value="<?php echo $page['info']['tab_d[]'] ? $page['info']['tab_d[]'] : ""; ?>"/>'+
                '<span style="color: red">*必填</span>'+
                '</td>'+
                '</tr>'+
                '</table>'+
                '</div> </td></tr>';
            //$("#quest3").append(tr).append(table);
            $(".a").append(addHtml)
        });
        /*$(".fewer").on('click',function () {
         var ns=$(this).parent().next('div');
         ns.slideToggle();
         var text =$(this).text();
         var text =text=='收起'?'编辑':'收起';
         $(this).text(text);
         });*/
    });

</script>
