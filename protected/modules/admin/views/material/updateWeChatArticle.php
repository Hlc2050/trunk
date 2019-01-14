<div class="mt10">
    <div class="tab_box">
        <a href="javascript:;" onclick="touchNav(1)" class="current" id="Page_1">主页面图文标题</a>
        <a href="javascript:;" onclick="touchNav(2)" id="Page_2">返回页面图文标题</a>
        <a href="javascript:;" onclick="touchNav(3)" id="Page_3">作者页面图文标题</a>
        <a href="javascript:;" onclick="touchNav(4)" id="Page_4">阅读原文页面图文标题</a>
    </div>
</div>

<?php
if($page['info']['article_code'] != null){
    $sql = "select * FROM material_article_template WHERE article_code= '".$page['info']['article_code']."'order by id";
    $data = Yii::app()->db->createCommand($sql)->queryAll();
    $str =$data[0]['id'].','.$data[1]['id'].','.$data[2]['id'].','.$data[3]['id'];
}
?>
<form method="post" action="<?php echo $this->createUrl($page['info']['id'] ? 'material/editArticle' : 'material/addArticle'); ?>?article_type=3&p=<?php echo $_GET['p']; ?>">
    <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
    <input type="hidden" id="weChat_id" name="weChat_id" value="<?php echo $str?$str:""; ?>"/>
    <input type="hidden" id="backurl" name="backurl" value="<?php echo $this->get('url') ?>"/>
    <div id="turnPage_1" style="display:block;">
        <table class="tb3">
            <tbody>
            <!--            空行-->
            <tr>
                <td style="height:3px;"></td>
            </tr>
            <!--            主页面图文标题-->
            <tr>
                <td colspan="2" style="vertical-align:middle;">
                    <span
                            style="vertical-align:middle;font-size: large;color: dimgrey;font-weight:bold"> 主页面图文标题：</span><br/>
                    <input style="width: 500px;height:30px;font-size: large;" type="text" class="ipt" id="mainTitle"
                           name="main_title"
                           value="<?php echo $data[0]['article_title'] ? $data[0]['article_title'] : ""; ?>"/>
                    <span style="color: red">*必填</span>
                </td>
            </tr>
            <!--            所在组别-->
            <tr>
                <td><span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 所在组别：</span></td>
                <td class="alignleft">
                    <?php
                    $articleGroups = MaterialArticleGroup::model()->getArticleGroups();
                    echo CHtml::dropDownList('group_id', $data[0]['group_id'], CHtml::listData($articleGroups, 'id', 'group_name'),
                        array(
                            'empty' => '选择组别',
                        )
                    );
                    ?>
                    <span style="color: red">*必填</span>
                </td>
            </tr>
            <!--            支持人员-->
            <tr>
                <td><span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 支持人员：</span></td>
                <td class="alignleft">
                    <?php
                    $supportStafflist = SupportStaff::model()->getSupportStaffList();
                    echo CHtml::dropDownList('support_staff_id',  $data[0]['support_staff_id'], CHtml::listData($supportStafflist, 'user_id', 'name'),
                        array(
                            'empty' => '选择支持人员',
                        )
                    );
                    ?>
                    <span style="color: red">*必填</span>
                </td>
            </tr>
            <!--            正文-->
            <tr>
                <td colspan="2" style="position:relative;">
                    <span style="vertical-align:middle;font-size: large;color: dimgrey;font-weight:bold">
                        正文：
                    </span>

                    <br/>
                    <div style="position:relative;">
                        <textarea style="width:100%; height: 400px" id="info_body1"
                                  name="info_body1"><?php echo $data[0]['content'] ? $data[0]['content'] : ''; ?></textarea>
                        <script>
                            var info_body1 = $("#info_body1").xheditor({
                                plugins: allplugin,
                                internalScript: true,
                                tools: "mfull",
                                skin: "nostyle",

                            });
                        </script>
                        <span class="downhttpimgbtn" id="downbtn_info_body">
                            <a href="#" onclick="addWeChatSign(1)">添加微信号标识</a>&nbsp;&nbsp;
                            <a href="#" onclick="addWeChatImg(1)">添加微信号图片标识</a>&nbsp;&nbsp;
                            <a href="#" onclick="addXingXiangSign(1)">添加形象标识</a><br/>
                            <a onclick="return dialog_frame(this,650,400,false)"
                               href="<?php echo $this->createUrl('material/addReceiveStyle?num=1'); ?>">插入领取人数样式</a>&nbsp;
                            <a onclick="return dialog_frame(this,500,580,false)"
                               href="<?php echo $this->createUrl('material/addMaterialPics?num=1'); ?>">插入素材库图片</a>&nbsp;&nbsp;
                            <a onclick="return dialog_frame(this,500,580,false)"
                               href="<?php echo $this->createUrl('material/addMaterialVideos?num=1'); ?>">插入素材库视频</a><br/>
                        </span>
                        <button class="upbtn_box">本地上传<input type="file" ></button>
                        <script>load_editor_upload("info_body1");</script>
                        </span>
                    </div>
                </td>
            </tr>
            <!--            分享链接小图-->
            <tr>
                <td style="vertical-align:middle;">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 分享链接小图：</span>
                </td>
                <td class="alignleft">
                    <input type="hidden" id="cover_url1" name="cover_url1"
                           value="<?php echo $data[0]['cover_url']?$data[0]['cover_url']:'' ?>"/>
                    <img
                            id="cover_show1" <?php if ( $data[0]['cover_url']) echo "style='width: 100px;height: 100px;'"; ?>
                            src="<?php echo  $data[0]['cover_url']?$data[0]['cover_url']:"" ?>">
                    <a class="but1" onclick="return dialog_frame(this,450,580,false)"
                       href="<?php echo $this->createUrl('material/addMaterialPics'); ?>?add_type=3&&num=1">选择素材库图片</a>
                </td>
            </tr>
            <!--            浏览器Title-->
            <tr>
                <td style="vertical-align:middle;width: 80px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 浏览器Title：</span>
                </td>
                <td>
                    <input style="font-size: small;width: 300px" type="text" class="ipt" id="tag_1" name="tag_1"
                           value="<?php echo $data[0]['tag'] ? $data[0]['tag'] : ""; ?>"/>
                </td>
            </tr>
            <!--            商品类型-->
            <tr>
                <td style="vertical-align:middle;width: 80px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 商品类型：</span>
                </td>
                <td>
                    <select name="cat_id">
                        <option value="" selected>
                            请选择类型
                        </option>
                        <?php
                        //商品类别列表
                        $categoryList = Linkage::model()->getGoodsCategoryList();
                        foreach ($categoryList as $key => $val) { ?>
                            <option
                                    value="<?php echo $key; ?>" <?php echo $key == $data[0]['cat_id'] ? 'selected' : ''; ?>>
                                <?php echo $val; ?>
                            </option>
                        <?php } ?>
                    </select>
                    <span style="color: red">*必选</span>
                </td>
            </tr>
            <!--            形象-->
            <tr>
                <td style="vertical-align:middle;width: 80px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 形象：</span>
                </td>
                <td>
                    <input class="ipt" id="xingxiang"
                           name="xingxiang" value="<?php echo $data[0]['xingxiang']?$data[0]['xingxiang']:""; ?>">
                </td>
            </tr>

            <!--            文案备注-->
            <tr>
                <td style="vertical-align:middle;width: 80px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 文案备注：</span>
                </td>
                <td>
                    <textarea style="font-size: small;width: 290px;height: 50px;" class="ipt" id="article_info"
                              name="article_info"><?php echo $data[0]['article_info']?$data[0]['article_info']:""; ?></textarea>
                </td>
            </tr>
            <!--            描述语句-->
            <tr>
                <td style="vertical-align:middle;width: 80px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 描述语句：</span>
                </td>
                <td>
                    <textarea style="font-size: small;width: 290px;height: 50px;" class="ipt" id="descriptive_statement"
                              name="descriptive_statement"><?php echo $data[0]['descriptive_statement']?$data[0]['descriptive_statement']:""; ?></textarea>
                </td>
            </tr>
            <!--            发布时间-->
            <tr>
                <td>发布时间</td>
                <td>&nbsp;<input name="release_date" type="radio" value="0" <?php if($page['info']['release_date'] == 0){echo  "checked";} ?>>&nbsp;昨天
                    &nbsp;<input name="release_date" type="radio" value="1" <?php if($page['info']['release_date'] == 1){echo  "checked";} ?>>&nbsp;今天
                    &nbsp;<input name="release_date" type="radio" value="2" <?php if($page['info']['release_date'] == 2){echo  "checked";} ?>>&nbsp;&nbsp;三天前
                    &nbsp;<input name="release_date" type="radio" value="3" <?php if(helper::checkReleaseDate($page['info']['release_date'])){echo  "checked";} ?>>&nbsp;
                    <input type="text" id="release_date" class="ipt Wdate" style="font-size: small;width: 150px;background-color: #DDDDDD;" placeholder="请选择日期" name="release_date1" value="<?php echo helper::checkReleaseDate($page['info']['release_date']) ? date("Y-m-d",$page['info']['release_date']) : ""; ?>"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
                </td>
            </tr>

            <!--            作者名-->
            <tr>
                <td style="vertical-align:middle;width: 80px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 作者名：</span>
                </td>
                <td>
                    <input style="font-size: small;width: 300px" type="text" class="ipt" id="author" name="author"
                           value="<?php echo $data[0]['suspension_text'] ? $data[0]['suspension_text'] : ""; ?>"/>
                </td>
            </tr>
            <!--            点赞数-->
            <tr>
                <td style="vertical-align:middle;width: 80px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 点赞数：</span>
                </td>
                <td>
                    <input style="font-size: small;width: 300px" type="text" class="ipt" id="thumb_up" name="thumb_up"
                           value="<?php echo $data[0]['first_audio'] ? $data[0]['first_audio'] : ""; ?>"/>
                </td>
            </tr>
            <!--            阅读数-->
            <tr>
                <td style="vertical-align:middle;width: 80px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 阅读数：</span>
                </td>
                <td>
                    <input style="font-size: small;width: 300px" type="text" class="ipt" id="read_num" name="read_num"
                           value="<?php echo $data[0]['second_audio'] ? $data[0]['second_audio'] : ""; ?>"/>
                </td>
            </tr>
            <!--            开启返回页面功能-->
            <tr>
                <td style="vertical-align:middle;width: 110px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 开启返回页面功能：</span>
                </td>
                <td>
                    <input name="is_rtn" <?php  if($data == null){echo  "checked";}else{echo $data[0]['is_hide_title'] == 1 ? "checked" : "";} ?>
                           type="radio" value='1'/>开启&nbsp;&nbsp;
                    <input name="is_rtn" <?php  if($data){echo $data[0]['is_hide_title'] == 0 ? "checked" : "";} ?>
                           type="radio" value='0'/>关闭&nbsp;&nbsp;
                </td>
            </tr>
            <!--            开启作者页面功能-->
            <tr>
                <td style="vertical-align:middle;width: 110px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 开启作者页面功能：</span>
                </td>
                <td>
                    <input name="is_au_article" <?php if(!$data){echo  "checked";}else{echo $data[0]['is_fill'] == 1 ? "checked" : ""; }?>
                           type="radio" value='1'/>开启&nbsp;&nbsp;
                    <input name="is_au_article" <?php if($data){ echo $data[0]['is_fill'] == 0 ? "checked" : ""; }?>
                           type="radio" value='0'/>关闭&nbsp;&nbsp;
                </td>
            </tr>
            <!--            保存-->
            <tr>
                <td></td>
                <td class="alignleft">
                    <input style="font-size: medium" type="submit" class="but"  value="保存"/>&nbsp;&nbsp;&nbsp;
                    <?php $this->check_u_menu(array('code' => '<input value="另存为" style="font-size: medium" type="button" class="but" onclick="return save_as(this,300,150,false)" href="' . $this->createUrl('material/saveAs') . '"/>', 'auth_tag' => 'material_saveAs')); ?>
                    &nbsp;&nbsp;&nbsp;
                    <input style="font-size: medium" type="button" class="but" value="返回"
                           onclick="window.location='<?php echo $this->get('url') ? $this->get('url') : $this->createUrl('material/index'); ?>'"/>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div id="turnPage_2" style="display:none;">
        <table class="tb3">
            <tbody>
            <!--            空行-->
            <tr>
                <td style="height:3px;"></td>
            </tr>
            <!--  返回页面图文标题-->
            <tr>
                <td colspan="2" style="vertical-align:middle;">
                    <span
                            style="vertical-align:middle;font-size: large;color: dimgrey;font-weight:bold"> 返回页面图文标题：</span><br/>
                    <input style="width: 500px;height:30px;font-size: large;" type="text" class="ipt" id="rtn_Title"
                           name="rtn_Title"
                           value="<?php echo $data[1]['article_title'] ? $data[1]['article_title'] : ""; ?>"/>
                    <span style="color: red">*必填</span>
                </td>
            </tr>
            <!--  正文-->
            <tr>
                <td colspan="2" style="position:relative;">
                    <span style="vertical-align:middle;font-size: large;color: dimgrey;font-weight:bold">
                        正文：
                    </span>

                    <br/>
                    <div style="position:relative;">
                        <textarea style="width:100%; height: 400px" id="info_body2"
                                  name="info_body2"><?php echo $data[1]['content'] ? $data[1]['content'] : ''; ?></textarea>
                        <script>
                            var info_body = $("#info_body2").xheditor({
                                plugins: allplugin,
                                internalScript: true,
                                tools: "mfull",
                                skin: "nostyle",

                            });
                        </script>
                        <span class="downhttpimgbtn" id="downbtn_info_body">
                            <a href="#" onclick="addWeChatSign(2)">添加微信号标识</a>&nbsp;&nbsp;
                            <a href="#" onclick="addWeChatImg(2)">添加微信号图片标识</a>&nbsp;&nbsp;
                            <a href="#" onclick="addXingXiangSign(2)">添加形象标识</a><br/>
                            <a onclick="return dialog_frame(this,650,400,false)"
                               href="<?php echo $this->createUrl('material/addReceiveStyle?num=2'); ?>">插入领取人数样式</a>&nbsp;
                            <a onclick="return dialog_frame(this,500,580,false)"
                               href="<?php echo $this->createUrl('material/addMaterialPics?num=2'); ?>">插入素材库图片</a>&nbsp;&nbsp;
                            <a onclick="return dialog_frame(this,500,580,false)"
                               href="<?php echo $this->createUrl('material/addMaterialVideos?num=2'); ?>">插入素材库视频</a><br/>
                        </span>
                        <button class="upbtn_box">本地上传<input type="file" ></button>
                        <script>load_editor_upload("info_body2");</script>
                        </span>
                    </div>
                </td>
            </tr>
            <!--分享链接小图-->
            <tr>
                <td style="vertical-align:middle;">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 分享链接小图：</span>
                </td>
                <td class="alignleft">
                    <input type="hidden" id="cover_url2" name="cover_url2"
                           value="<?php echo $data[1]['cover_url']?$data[1]['cover_url']:"" ?>"/>
                    <img
                            id="cover_show2" <?php if ($data[1]['cover_url']) echo "style='width: 100px;height: 100px;'"; ?>
                            src="<?php echo $data[1]['cover_url']?$data[1]['cover_url']:"" ?>">
                    <a class="but1" onclick="return dialog_frame(this,450,580,false)"
                       href="<?php echo $this->createUrl('material/addMaterialPics'); ?>?add_type=3&&num=2">选择素材库图片</a>
                </td>
            </tr>
            <!--浏览器Title-->
            <tr>
                <td style="vertical-align:middle;width: 110px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 浏览器Title：</span>
                </td>
                <td>
                    <input style="font-size: small;width: 300px" type="text" class="ipt" id="tag_2" name="tag_2"
                           value="<?php echo $data[1]['tag'] ? $data[1]['tag'] : ""; ?>"/>
                </td>
            </tr>
<!--            跳转链接-->
            <tr>
                <td style="vertical-align:middle;width: 110px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 跳转链接：</span>
                </td>
                <td>
                    <input style="font-size: small;width: 300px" type="text" class="ipt" id="link1" name="link1"
                           value="<?php echo $data[1]['link'] ? $data[1]['link'] : ""; ?>"/>
                </td>
            </tr>
            <!--保存-->
            <tr>
                <td></td>
                <td class="alignleft">
                    <input style="font-size: medium" type="submit" class="but"  value="保存"/>&nbsp;&nbsp;&nbsp;
                    <?php $this->check_u_menu(array('code' => '<input value="另存为" style="font-size: medium" type="button" class="but" onclick="return save_as(this,300,150,false)" href="' . $this->createUrl('material/saveAs') . '"/>', 'auth_tag' => 'material_saveAs')); ?>
                    &nbsp;&nbsp;&nbsp;
                    <input style="font-size: medium" type="button" class="but" value="返回"
                           onclick="window.location='<?php echo $this->get('url') ? $this->get('url') : $this->createUrl('material/index'); ?>'"/>
                </td>
            </tr>
            </tbody>
        </table>

    </div>
    <div id="turnPage_3" style="display:none;">
        <table class="tb3">
            <tbody>
            <!--            空行-->
            <tr>
                <td style="height:3px;"></td>
            </tr>
            <!--        作者页面图文标题-->
            <tr>
                <td colspan="2" style="vertical-align:middle;">
                    <span
                            style="vertical-align:middle;font-size: large;color: dimgrey;font-weight:bold"> 作者页面图文标题：</span><br/>
                    <input style="width: 500px;height:30px;font-size: large;" type="text" class="ipt" id="au_title"
                           name="au_title"
                           value="<?php echo $data[2]['article_title'] ? $data[2]['article_title'] : ""; ?>"/>
                    <span style="color: red">*必填</span>
                </td>
            </tr>
            <!--正文-->
            <tr>
                <td colspan="2" style="position:relative;">
                    <span style="vertical-align:middle;font-size: large;color: dimgrey;font-weight:bold">
                        正文：
                    </span>

                    <br/>
                    <div style="position:relative;">
                        <textarea style="width:100%; height: 400px" id="info_body3"
                                  name="info_body3"><?php echo $data[2]['content'] ? $data[2]['content'] : ''; ?></textarea>
                        <script>
                            var info_body = $("#info_body3").xheditor({
                                plugins: allplugin,
                                internalScript: true,
                                tools: "mfull",
                                skin: "nostyle",

                            });
                        </script>
                        <span class="downhttpimgbtn" id="downbtn_info_body">
                            <a href="#" onclick="addWeChatSign(3)">添加微信号标识</a>&nbsp;&nbsp;
                            <a href="#" onclick="addWeChatImg(3)">添加微信号图片标识</a>&nbsp;&nbsp;
                            <a href="#" onclick="addXingXiangSign(3)">添加形象标识</a><br/>
                            <a onclick="return dialog_frame(this,650,400,false)"
                               href="<?php echo $this->createUrl('material/addReceiveStyle?num=3'); ?>">插入领取人数样式</a>&nbsp;
                            <a onclick="return dialog_frame(this,500,580,false)"
                               href="<?php echo $this->createUrl('material/addMaterialPics?num=3'); ?>">插入素材库图片</a>&nbsp;&nbsp;
                            <a onclick="return dialog_frame(this,500,580,false)"
                               href="<?php echo $this->createUrl('material/addMaterialVideos?num=3'); ?>">插入素材库视频</a><br/>
                        </span>
                        <button class="upbtn_box">本地上传<input type="file" ></button>
                        <script>load_editor_upload("info_body3");</script>
                        </span>
                    </div>
                </td>
            </tr>
            <!--分享链接小图-->
            <tr>
                <td style="vertical-align:middle;">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 分享链接小图：</span>
                </td>
                <td class="alignleft">
                    <input type="hidden" id="cover_url3" name="cover_url3"
                           value="<?php echo $data[2]['cover_url']?$data[2]['cover_url']:"" ?>"/>
                    <img
                            id="cover_show3" <?php if ($data[2]['cover_url']) echo "style='width: 100px;height: 100px;'"; ?>
                            src="<?php echo $data[2]['cover_url']?$data[2]['cover_url']:"" ?>">
                    <a class="but1" onclick="return dialog_frame(this,450,580,false)"
                       href="<?php echo $this->createUrl('material/addMaterialPics'); ?>?add_type=3&&num=3">选择素材库图片</a>
                </td>
            </tr>
            <!--浏览器Title-->
            <tr>
                <td style="vertical-align:middle;width: 110px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 浏览器Title：</span>
                </td>
                <td>
                    <input style="font-size: small;width: 300px" type="text" class="ipt" id="tag_3" name="tag_3"
                           value="<?php echo $data[2]['tag'] ? $data[2]['tag'] : ""; ?>"/>
                </td>
            </tr>
            <!--   跳转链接-->
            <tr>
                <td style="vertical-align:middle;width: 110px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 跳转链接：</span>
                </td>
                <td>
                    <input style="font-size: small;width: 300px" type="text" class="ipt" id="link2" name="link2"
                           value="<?php echo $data[2]['link'] ? $data[2]['link'] : ""; ?>"/>
                </td>
            </tr>
            <!--保存-->
            <tr>
                <td></td>
                <td class="alignleft">
                    <input style="font-size: medium" type="submit" class="but" value="保存"/>&nbsp;&nbsp;&nbsp;
                    <?php $this->check_u_menu(array('code' => '<input value="另存为" style="font-size: medium" type="button" class="but" onclick="return save_as(this,300,150,false)" href="' . $this->createUrl('material/saveAs') . '"/>', 'auth_tag' => 'material_saveAs')); ?>
                    &nbsp;&nbsp;&nbsp;
                    <input style="font-size: medium" type="button" class="but" value="返回"
                           onclick="window.location='<?php echo $this->get('url') ? $this->get('url') : $this->createUrl('material/index'); ?>'"/>
                </td>

            </tr>
            </tbody>
        </table>
    </div>
    <div id="turnPage_4" style="display:none;">
        <table class="tb3">
            <tbody>
            <!--            空行-->
            <tr>
                <td style="height:3px;"></td>
            </tr>
            <!--        阅读页面图文标题        -->
            <tr>
                <td colspan="2" style="vertical-align:middle;">
                    <span
                            style="vertical-align:middle;font-size: large;color: dimgrey;font-weight:bold"> 阅读页面图文标题：</span><br/>
                    <input style="width: 500px;height:30px;font-size: large;" type="text" class="ipt" id="read_title"
                           name="read_title"
                           value="<?php echo $data[3]['article_title'] ? $data[3]['article_title'] : ""; ?>"/>
                    <span style="color: red">*必填</span>
                </td>
            </tr>
            <!--正文-->
            <tr>
                <td colspan="2" style="position:relative;">
                    <span style="vertical-align:middle;font-size: large;color: dimgrey;font-weight:bold">
                        正文：
                    </span>

                    <br/>
                    <div style="position:relative;">
                        <textarea style="width:100%; height: 400px" id="info_body4"
                                  name="info_body4"><?php echo $data[3]['content'] ? $data[3]['content'] : ''; ?></textarea>
                        <script>
                            var info_body = $("#info_body4").xheditor({
                                plugins: allplugin,
                                internalScript: true,
                                tools: "mfull",
                                skin: "nostyle",

                            });
                        </script>
                        <span class="downhttpimgbtn" id="downbtn_info_body">
                            <a href="#" onclick="addWeChatSign(4)">添加微信号标识</a>&nbsp;&nbsp;
                            <a href="#" onclick="addWeChatImg(4)">添加微信号图片标识</a>&nbsp;&nbsp;
                            <a href="#" onclick="addXingXiangSign(4)">添加形象标识</a><br/>
                            <a onclick="return dialog_frame(this,650,400,false)"
                               href="<?php echo $this->createUrl('material/addReceiveStyle?num=4'); ?>">插入领取人数样式</a>&nbsp;
                            <a onclick="return dialog_frame(this,500,580,false)"
                               href="<?php echo $this->createUrl('material/addMaterialPics?num=4&add_type=3'); ?>">插入素材库图片</a>&nbsp;&nbsp;
                            <a onclick="return dialog_frame(this,500,580,false)"
                               href="<?php echo $this->createUrl('material/addMaterialVideos?num=4'); ?>">插入素材库视频</a><br/>
                        </span>
                        <button class="upbtn_box">本地上传<input type="file"></button>
                        <script>load_editor_upload("info_body4");</script>
                        </span>
                    </div>
                </td>
            </tr>
            <!--分享链接小图-->
            <tr>
                <td style="vertical-align:middle;">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 分享链接小图：</span>
                </td>
                <td class="alignleft">
                    <input type="hidden" id="cover_url4" name="cover_url4"
                           value="<?php echo $data[3]['cover_url']?$data[3]['cover_url']:"" ?>"/>
                    <img
                            id="cover_show4" <?php if ($data[3]['cover_url']) echo "style='width: 100px;height: 100px;'"; ?>
                            src="<?php echo $data[3]['cover_url']?$data[3]['cover_url']:"" ?>">
                    <a class="but1" onclick="return dialog_frame(this,450,580,false)"
                       href="<?php echo $this->createUrl('material/addMaterialPics'); ?>?add_type=3&&num=4">选择素材库图片</a>
                </td>
            </tr>
            <!--浏览器Title-->
            <tr>
                <td style="vertical-align:middle;width: 110px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 浏览器Title：</span>
                </td>
                <td>
                    <input style="font-size: small;width: 300px" type="text" class="ipt" id="tag_4" name="tag_4"
                           value="<?php echo $data[3]['tag'] ? $data[3]['tag'] : ""; ?>"/>
                </td>
            </tr>
            <!--   跳转链接-->
            <tr>
                <td style="vertical-align:middle;width: 110px">
                    <span style="vertical-align:middle;color: dimgrey;font-weight:bold"> 跳转链接：</span>
                </td>
                <td>
                    <input style="font-size: small;width: 300px" type="text" class="ipt" id="link3" name="link3"
                           value="<?php echo $data[3]['link'] ? $data[3]['link'] : ""; ?>"/>
                </td>
            </tr>
            <!--保存-->
            <tr>
                <td></td>
                <td class="alignleft">
                    <input style="font-size: medium" type="submit" class="but"  value="保存"/>&nbsp;&nbsp;&nbsp;
                    <?php $this->check_u_menu(array('code' => '<input value="另存为" style="font-size: medium" type="button" class="but" onclick="return save_as(this,300,150,false)" href="' . $this->createUrl('material/saveAs') . '"/>', 'auth_tag' => 'material_saveAs')); ?>
                    &nbsp;&nbsp;&nbsp;
                    <input style="font-size: medium" type="button" class="but" value="返回"
                           onclick="window.location='<?php echo $this->get('url') ? $this->get('url') : $this->createUrl('material/index'); ?>'"/>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</form>

<script type="text/javascript">
    //分板块
    function touchNav(num) {
        for (var i = 1; i < 5; i++) {
            document.getElementById('turnPage_' + i).style.display = 'none';
            document.getElementById('Page_' + i).className ='';
        }
        document.getElementById('turnPage_' + num).style.display = 'block';
        document.getElementById('Page_' + num).className ='current';
    }

    //插入领取人数字样
    function addText(text,num) {
        $("#info_body"+ num).xheditor().pasteHTML(text);
    }
    //添加素材库图片交互
    function addImg(url, is_jump ,num) {
        if (is_jump == 1) {
            var html = "<p><img class='lazy img2wx jump' src='" + url + "'  _xhe_src='" + url + "' /></p>";
        } else {
            var html = "<p><img class='lazy' src='" + url + "'  _xhe_src='" + url + "' /></p>";
        }
        $("#info_body" + num).xheditor().pasteHTML(html);
    }

    //插入素材库视频
    function addOneVideo(url,num) {
        var html = "<p  style='margin:5%;text-align: center;'><video style='max-width: 90%' src='" + url + "' _xhe_src='" + url + "' controls></p><br/>";
        $("#info_body"+ num).xheditor().pasteHTML(html);
    }


    //添加一张图片 顶部背景图和头像
    function addOneImg(url, type,num) {
        if (type == 1) {
            var obj = $("#top_img");
            $("#top_show").attr('src', url);
            $("#top_show").css({width: 160, height: 50})
        } else if (type == 2) {
            var obj = $("#avater_img");
            $("#avater_show").attr('src', url);
            $("#avater_show").css({width: 80, height: 80})
        } else if (type == 3) {
            var obj = $("#cover_url"+num);
            $("#cover_show"+num).attr('src', url);
            $("#cover_show"+num).css({width: 80, height: 80})
        }
        $(obj).val(url);
    }

    //      添加微信号标识
    function addWeChatSign(num) {
        var html = "<span>{{weixin}}</span>";
        $("#info_body" + num).xheditor().pasteHTML(html);
    }

    //添加微信号图片标识
    function addWeChatImg(num) {
        var html = "<span>{{weixin_img}}</span>";
        $("#info_body" + num).xheditor().pasteHTML(html);
    }

    //添加形象标识
    function addXingXiangSign(num) {
        var html = "<span>{{xingxiang}}</span>";
        $("#info_body" + num).xheditor().pasteHTML(html);
    }

    $("#release_date").click(function () {
        $(" input[type:radio][name=release_date][value=3]").attr('checked',true);
    })
    $(" input[type:radio][name=release_date]").click(function () {
        $("#release_date").attr('value','');
    })

</script>

<style type="text/css">
    .upbtn_box {
        border: 1px solid #ccc;
        background: #eee;
        width: 80px;
        height: 26px;
        display: inline-block;
    }

    .upbtn_box input {
        opacity: 0;
        position: absolute;
        right: 0;
        width: 80px;
        height: 26px;
        float: right;
        cursor: pointer;
        top: 0;
    }
</style>