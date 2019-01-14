<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<style>
    .review{
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
    <div class="snav">素材管理 » 评论管理 » 修改评论</div>
</div>
<div class="main mbody">
    <form method="post"
          action="<?php echo $this->createUrl($page['info']['id'] ? 'material/editReview' : 'material/addReview'); ?>?p=<?php echo $_GET['p']; ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <table class="tb3" id="questionnaire">
            <tbody>
            <tr>
                <td style="vertical-align:middle;">
                    评论标题：
                    <input style="width: 350px;height:30px;font-size: large;" type="text" class="ipt"
                           name="review_title"
                           value="<?php echo $page['info']['review_title'] ? $page['info']['review_title'] : ""; ?>"/>
                    <span style="color: red">*必填</span>
                </td>
            </tr>
            <tr>
                <td>评论类型：
                    <?php $reviewList = Linkage::model()->getReviewTypeList();
                    foreach ( $reviewList as $k => $v ){
                        ?>
                        <input type="radio" name="review_type" value="<?php echo $k;?>" <?php if ( $page['info']['review_type'] == $k ) echo 'checked';?>><?php echo $v; ?>&nbsp&nbsp&nbsp&nbsp
                    <?php } ?>
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
            <?php  foreach ($data as $k => $v){
              
                ?>
                <tr>
                    <td>
                        <input type="hidden" id="r_id" name="r_id[]" value="<?php echo $data[$k]['id']; ?>"/>
                        <div class="review">评论<?php echo $k+1;?>：<?php if ( $k != 0 ) { ?><span onclick="deleteTr(this);" class="deleteTr" >删除</span><?php } ?><span class="fewer" onclick="showHide(this)">收起</span></div>
                        <div id="review" style="border: 1px solid black;height: 210px;width: 600px;margin: 0;padding: 0;">
                            <table style="margin: 0 20px;border: none;">
                                <tr>
                                    <td width="70px">名称</td>
                                    <td>
                                        <input style="width: 300px;height:30px;font-size: large;" type="text" class="ipt"
                                               name="review_name[]"
                                               value="<?php echo $v['review_name']; ?>"/>
                                        <span style="color: red">*必填</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>评论内容</td>
                                    <td>
                                        <textarea  style="width: 290px;height:90px;font-size: large;" name="review_content[]"><?php echo $v['review_content']?></textarea>
                                        <span style="color: red">*必填</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>评论时间</td>
                                    <td>
                                        <input style="width: 300px;height:30px;font-size: large;" type="text" class="ipt"
                                               name="review_date[]"
                                               value="<?php echo $v['review_date'] ; ?>"/>
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
                <td><div id="add_review" onclick="" style="text-align: center;height:40px;line-height: 40px;" class="review">+添加评论</div></td>
            </tr>

            <tr>
                <td class="alignleft">
                    <input style="font-size: medium" type="submit" class="but" id="subtn" value="保存"/>&nbsp;&nbsp;&nbsp;
                    <input style="font-size: medium" type="button" class="but" value="返回"
                           onclick="window.location='<?php echo $this->createUrl('material/index'); ?>?group_id=5&p=<?php echo $_GET['p']; ?>'"/>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>
<script>
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
            url: '/admin/material/deleteReviewDetail',
            data: {
                'id': $(nowTr).parent().parent().children("input").val()
            },
            success: function (data) {
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
        $("#add_review").click(function () {
            var b = $(".review").size();
            if(b>=9){
                alert('你最多只可以增加8个问题');
                return false;
            }
            var addHtml = '<tr><td>'+
                '<div class="review">评论'+b+'：<span onclick="deleteTr(this);" class="deleteTr" >删除</span><span class="fewer" onclick="showHide(this);" >编辑</span></div> '+
                '<div id="review'+b+'" style="border: 1px solid black;height: 210px;width: 600px;margin: 0;padding: 0;display:none">'+
                '<table style="margin: 0 20px;border: none">'+
                '<tr>'+
                '<td width="70px">评论名称</td>'+
                '<td>'+
                '<input style="width: 300px;height:30px;font-size: large;" type="text" class="ipt" '+
                ' name="review_name[]" '+
                '  value="<?php echo $page['info']['review_title[]'] ? $page['info']['review_title[]'] : ""; ?>"/>'+
                '<span style="color: red">*必填</span></td>'+
                '</tr>'+
                '<tr>'+
                '<td>评论内容</td>'+
                '<td>'+
                ' <textarea  style="width: 290px;height:90px;font-size: large;" name="review_content[]">  '+
                ' </textarea> '+
                '<span style="color: red">*必填</span></td>'+
                '</tr>'+
                '<tr>'+
                '<td>评论时间</td>'+
                '<td>'+
                ' <input style="width: 300px;height:30px;font-size: large;" type="text" class="ipt" '+
                ' name="review_date[]" '+
                ' value="<?php echo $page['info']['review_date[]'] ? $page['info']['review_date[]'] : ""; ?>"/>  '+
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
