<?php require(dirname(__FILE__) . "/../common/head.php"); ?>

<div class="main mhead">
    <div class="snav">推广管理 » 素材管理</div>

    <div class="mt10">
        <div class="tab_box">
            <?php foreach ($page['listdata']['params_groups'] as $k => $r) { ?>
                <a href="<?php $this->createUrl('material/index'); ?>?group_id=<?php echo $r['value']; ?>" <?php if ($this->get('group_id') == $r['value']) echo 'class="current"'; ?>><?php echo $r['txt']; ?></a>
            <?php } ?>

            <?php if ($this->get('group_id') == 0) { ?>
                <?php if ($this->get('type') == 1) {
                    ?>
                    <div class="mt10">
                        <p style="font-size: x-large; font-weight:bold;color:dimgrey">
                            <?php echo $page['listdata']['group_name']?$page['listdata']['group_name']: ''; ?>
                        </p>
                    </div>
                    <div class="mt10">
                        <form action="<?php echo $this->createUrl('material/index'); ?>?group_id=0">
                            <input hidden name="type" value="1">
                            添加时间：
                            <input type="text" class="ipt" id="create_date" name="create_date" style="width:120px;"
                                   value="<?php echo $this->get('create_date') ? $this->get('create_date') : ''; ?>"
                                   onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>&nbsp;
                            标题：
                            <input type="text" id="article_title" name="article_title" class="ipt" style="width:120px;"
                                   value="<?php echo $this->get('article_title') ? $this->get('article_title') : ''; ?>">&nbsp;
                            文案编码：
                            <input type="text" id="article_code" name="article_code" class="ipt" style="width:120px;"
                                   value="<?php echo $this->get('article_code') ? $this->get('article_code') : ''; ?>">&nbsp;
                            文案备注：
                            <input type="text" id="article_info" name="article_info" class="ipt" style="width:120px;"
                                   value="<?php echo $this->get('article_info') ? $this->get('article_info') : ''; ?>">&nbsp;
                            文章类型：
                            <?php
                            $article_type = vars::$fields['article_types'];
                            echo CHtml::dropDownList('article_type', $this->get('article_type'), CHtml::listData($article_type, 'value', 'txt'),
                                array('empty' => '全部')
                            );
                            ?>
                            <input hidden name="gid" value="<?php echo $page['listdata']['gid'] ?>">
                            <input type="submit" class="but" value="查询">
                        </form>
                    </div>
                    <div class="mt10 clearfix">
                        <div class="l">
                            <input type="button" class="but2" value="返回" onclick="window.location='<?php echo $this->get('url')?$this->get('url'): $this->createUrl('material/index'); ?>'"/>
                            &nbsp;&nbsp;
                            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="全选/反选" href="javascript:void(0);" onclick="check_all(\'.cklist\');"/> ', 'auth_tag' => 'material_checkAllArticle')); ?>
                            &nbsp;&nbsp;
                            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="移动分组" onclick="return set_url(this,400,600,1,\''.$this->createUrl('material/changeArticlesGroup') . '?ids=[@]\',\'none\');" />', 'auth_tag' => 'material_changeArticlesGroup')); ?>
                        </div>
                        <div class="r">
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="mt10">
                        <form action="<?php echo $this->createUrl('material/index'); ?>?group_id=0">
                            组别名称：
                            <input id="artGroupName" name="artGroupName" class="ipt" style="width:120px;"
                                   value="<?php echo $this->get('artGroupName') ?>"/>&nbsp;&nbsp;
                            组别编码：
                            <input id="artGroupCode" name="artGroupCode" class="ipt" style="width:120px;"
                                   value="<?php echo $this->get('artGroupCode') ?>"/>&nbsp;&nbsp;
                            商品类型：
                            <?php
                            $categoryList = Linkage::model()->get_linkage_data(20);
                            echo CHtml::dropDownList('cat_id', $this->get('cat_id'), CHtml::listData($categoryList, 'linkage_id', 'linkage_name'),
                                array(
                                    'empty' => '请选择'
                                )
                            ); ?>
                            <input type="submit" class="but" value="查询">
                        </form>
                    </div>
                    <div class="mt10 clearfix">
                        <div class="l">
                            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="添加图文" onclick="location=\'' . $this->createUrl('material/addArticle?url=' . $page['listdata']['url']) . '\'" />', 'auth_tag' => 'material_addArticle')); ?>
                            &nbsp;&nbsp;
                            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="新增图文组别" onclick="return dialog_frame(this,500,300,1)" href="' . $this->createUrl('material/addArticleGroup') . '" />', 'auth_tag' => 'material_addArticleGroup')); ?>

                            &nbsp;&nbsp;
                        </div>
                        <div class="r">
                        </div>
                    </div>
                <?php } ?>

            <?php } elseif ($this->get('group_id') == 1) { ?>
                <div class="mt10">
                    <p style="font-size: x-large; font-weight:bold;color:dimgrey">
                        <?php echo $this->get('pic_group_id') ? MaterialPicGroup::model()->findByPk(intval($this->get('pic_group_id')))->group_name : '全部图片'; ?>
                    </p>
                </div>
                <div class="mt10">

                    图片标题：
                    <input type="text" id="pic_name" name="pic_name" class="ipt" style="width:120px;"
                           value="<?php echo $this->get('pic_name'); ?>">&nbsp;&nbsp;
                    图片组别：
                    <input type="text" class="ipt pic_group" style="width: 199px;" name="pic_group"
                           value="<?php $data = MaterialPicGroup::model()->findByPk($page['info']['pic_group_id']);
                           echo $data->group_name; ?>" size="30"/>
                    <input type="hidden" class="ipt pic_group_id" id="pic_group_id" name="pic_group_id"
                           value="<?php echo $this->get('pic_group_id'); ?>" size="30"/>
                    <input type="button" class="but" value="查询"
                           onclick="window.location='<?php echo $this->createUrl('material/index'); ?>?group_id=1&pic_name='+$('#pic_name').val()+'&pic_group_id='+$('#pic_group_id').val();">

                </div>
                <div class="mt10 clearfix">
                    <div class="l">
                        <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="全选/反选" href="javascript:void(0);" onclick="check_all(\'.cklist\');"/> ', 'auth_tag' => 'material_checkAllPics')); ?>
                        &nbsp;&nbsp;
                        <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="移动分组" onclick="return set_url(this,400,600,1,\''.$this->createUrl('material/changePicsGroup') . '?ids=[@]\',\'none\');" />', 'auth_tag' => 'material_changePicsGroup')); ?>
                        &nbsp;&nbsp;
                        <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="删除选中" onclick="set_some(\'' . $this->createUrl('material/deletePics') . '?ids=[@]\',\'确定删除吗？\');" />', 'auth_tag' => 'material_deletePics')); ?>
                        &nbsp;&nbsp;
                        <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="添加图片" onclick="return dialog_frame(this,500,500,1)" href="' . $this->createUrl('material/uploadImgs') . '" />', 'auth_tag' => 'material_uploadImgs')); ?>
                        &nbsp;&nbsp;
                        <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="组别管理" onclick="return dialog_frame(this,500,500,1)" href="' . $this->createUrl('material/picGroupManage') . '" />', 'auth_tag' => 'material_picGroupManage')); ?>
                        &nbsp;&nbsp;
                    </div>
                    <div class="r">
                    </div>
                </div>
            <?php } elseif ($this->get('group_id') == 2) { ?>
                <div class="mt10 clearfix">
                    <div class="l">
                        <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="添加视频" onclick="return dialog_frame(this,500,500,1)" href="' . $this->createUrl('material/videoAdd') . '" />', 'auth_tag' => 'material_videoAdd')); ?>
                    </div>
                    <div class="r">
                    </div>
                </div>
            <?php } elseif ($this->get('group_id') == 3) { ?>
                <div class="mt10 clearfix">
                    <div class="l">
                        <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="添加语音" onclick="return dialog_frame(this,500,500,1)" href="' . $this->createUrl('material/audioAdd') . '" />', 'auth_tag' => 'material_audioAdd')); ?>
                    </div>
                    <div class="r">
                    </div>
                </div>
            <?php } elseif ($this->get('group_id') == 4) { ?>
                <div class="mt10">
                    <div class="l">
                        <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="添加问卷" onclick="location=\'' . $this->createUrl('material/addQuestionnaire') . '\'" />', 'auth_tag' => 'material_addQuestionnaire')); ?>
                    </div>
                </div>
            <?php } elseif ($this->get('group_id') == 5) { ?>
                <div class="mt10">
                    <div class="l">
                        <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="添加评论" onclick="location=\'' . $this->createUrl('material/addReview') . '\'" />', 'auth_tag' => 'material_addReview')); ?>
                        &nbsp;&nbsp;
                        <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="添加论坛评论" onclick="location=\'' . $this->createUrl('material/addForumReview') . '\'" />', 'auth_tag' => 'material_addForumReview')); ?>
                        &nbsp;&nbsp;
                        <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="添加精选评论" onclick="location=\'' . $this->createUrl('material/addSelectReview') . '\'" />', 'auth_tag' => 'material_addSelectReview')); ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="main mbody">
        <form>
            <?php if ($this->get('group_id') == 0) {
                if ($this->get('type') == 1) {
                    require(dirname(__FILE__) . "/articleList.php");
                } else {
                    require(dirname(__FILE__) . "/articleGroup.php");
                }
            } elseif ($this->get('group_id') == 1) {
                require(dirname(__FILE__) . "/picList.php");
            } elseif ($this->get('group_id') == 2) {
                require(dirname(__FILE__) . "/videoList.php");
            } elseif ($this->get('group_id') == 3) {
                require(dirname(__FILE__) . "/audioList.php");

            } elseif ($this->get('group_id') == 4) {
                require(dirname(__FILE__) . "/questionnaire.php");
            } elseif ($this->get('group_id') == 5) {
                require(dirname(__FILE__) . "/review.php");
            } ?>
        </form>
    </div>
</div>
<style type="text/css">
    #layout tr.level1-tr {
        list-style: none;
    }

    #layout td.level1-td {
        list-style-type: square;
        no-repeat 0px 4px;
        padding-left: 20px;
    }

    #layout img.aimg {
        border: 0;
        width: 230px;
        height: 180px
    }

    #layout img.bimg {
        border: 0;
        width: 230px;
        height: 200px
    }

    #layout td.level1-td tr.level1-tr a img {
        padding: 1px;
        border: 1px solid;
        margin-bottom: 3px;
        display: block;
    }

    #layout tr.level1-tr td.level1-td {
        float: left;
        width: 260px;
        height: 290px;
        margin-top: 20px;
        margin-right: 0;
        margin-bottom: 0;
        margin-left: 20px;
        text-align: center;
    }

    #layout td.level1-td tr.level1-tr a {
        display: block;
    }

    #layout td.level1-td {
        list-style-type: none;
    }

    #layout tr.level1-tr td a：hover img {
        padding: 0;
        border: 2px solid #FF6600;
    }
</style>

<script type="text/javascript">

    $(document).ready(function () {

        $('.pic_group').on('keyup focus', function () {
            $('.searchsBox').show();
            var myInput = $(this);
            var key = myInput.val();
            var postdata = {search_type: 'keys', search_txt: key};
            $.getJSON('<?php echo $this->createUrl('material/getPicGroups') ?>?jsoncallback=?', postdata, function (reponse) {
                try {
                    if (reponse.state < 1) {
                        alert(reponse.msg);
                        return false;
                    }
                    var html = '';
                    console.log(reponse);
                    for (var i = 0; i < reponse.data.list.length; i++) {
                        html += '<a href="javascript:void(0);" data-id="' + reponse.data.list[i].id + '" ' +
                            'data-picGroup="' + reponse.data.list[i].group_name + '"  ' +
                            'onmouseDown="getTipsValues(this);"   style="display:block;font-size:12px;padding:2px 5px;">' + reponse.data.list[i].group_name + '</a>';
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
    function getTipsValues(ele) {
        var myobj = $(ele);
        var id = myobj.attr('data-id');
        var pic_group = myobj.attr('data-picGroup');
        $('.pic_group_id').val(id);
        $('.pic_group').val(pic_group);
    }
</script>