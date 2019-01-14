<?php require(dirname(__FILE__) . "/../common/head.php"); ?>

    <div class="main mhead">
        <div class="snav">推广管理 » 图文管理 » <?php echo $page['info']['id'] ? '修改图文' : '添加图文' ?></div>
    </div>
    <div class="mt10">
        <div class="tab_box">
            <?php
            $article_type = $page['info']['article_type'] ? $page['info']['article_type'] : 0;
            if (!$page['info']['id']) :?>
                <a href="<?php $this->createUrl('material/addArticle'); ?>?article_type=0" <?php if ($article_type == 0) echo 'class="current"'; ?>>标准图文</a>
                <a href="<?php $this->createUrl('material/addArticle'); ?>?article_type=1" <?php if ($article_type == 1) echo 'class="current"'; ?>>语音问卷</a>
                <a href="<?php $this->createUrl('material/addArticle'); ?>?article_type=2" <?php if ($article_type == 2) echo 'class="current"'; ?>>论坛问答</a>
                <a href="<?php $this->createUrl('material/addArticle'); ?>?article_type=3" <?php if ($article_type == 3) echo 'class="current"'; ?>>微信图文</a>
            <?php endif; ?>
        </div>
    </div>

<?php if ($article_type == 0) { ?>
    <?php require(dirname(__FILE__) . "/updateNormArticle.php"); ?>
<?php } elseif ($article_type == 1) { ?>
    <?php require(dirname(__FILE__) . "/updateAudioArticle.php"); ?>
<?php } elseif ($article_type == 2) { ?>
    <?php require(dirname(__FILE__) . "/updateForumArticle.php"); ?>
<?php } elseif ($article_type == 3) { ?>
    <?php require(dirname(__FILE__) . "/updateWeChatArticle.php"); ?>
<?php } ?>
<script>
    function save_as(element,width,height,isrefresh){
        var d = {};
        var t = $('form').serializeArray();
        $.each(t, function() {
            d[this.name] = this.value;
        });
        console.log(JSON.stringify(d));
        $.ajax({
            type: "POST",
            url: '/admin/material/saveTempData',
            data:d,
            success: function (data) {
                if (data != '') {
                    data = JSON.parse(data);
//                    console.log(data)
                    if(data['state']==0){
                        alert(data['msgwords'])
                        return false
                    }else {
                        id = data['id'];
                        try{
                            art.dialog.open($(element).attr("href")+"?id="+id,{
                                title:$(element).html(),
                                width:width,
                                height:height,
                                lock:true,
                                close:function(){
                                    if(isrefresh==1) {
                                        window.location.reload(true);
                                    }
                                }
                            });
                        }catch(e){alert(e.message);}
                        return false;
                    }
                }
            }
        });
    }
</script>
<!--使用新版本的文件，解决日期选择框随页面拖动一起移动问题-->
<script src="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/My97DatePicker/WdatePickerNew.js" ></script>
