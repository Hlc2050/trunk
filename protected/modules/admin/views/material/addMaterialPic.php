<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<input type="hidden" value="<?php echo $this->get('num'); ?>" id="num">
<div class="main mhead">
    <div class="snav">
        <table class="tb3">
            <tr>
                <th style="font-size: small;" align="left">选择插入的图片(<span style="color: grey">点击缩略图可查看大图</span>)</th>
            </tr>
        </table>
    </div>
    <div class="mt10">
        图片标题：
        <input type="text" id="pic_name" name="pic_name" class="ipt"
               value="<?php echo $this->get('pic_name'); ?>">
        <div class="mt10">
            图片组别：
            <input type="text" class="ipt pic_group" style="width: 199px;" name="pic_group"
                   value="<?php $data = MaterialPicGroup::model()->findByPk($this->get('pic_group_id'));
                   echo $data->group_name; ?>" size="30"/>
            <input type="hidden" class="ipt pic_group_id" id="pic_group_id" name="pic_group_id"
                   value="<?php echo $this->get('pic_group_id'); ?>" size="30"/>&nbsp;&nbsp;
            <input hidden id="add_type" name="add_type" value="<?php echo $this->get('add_type') ?>">
            <input hidden id="sign" name="sign"
                   value="<?php echo $page['listdata']['sign'] ? $page['listdata']['sign'] : '' ?>"/>
            <input type="button" class="but" value="查询"
                   onclick="window.location='<?php echo $this->createUrl('material/addMaterialPics'); ?>?id='+$('#sign').val()+'&add_type='+$('#add_type').val()+'&pic_name='+$('#pic_name').val()+'&pic_group_id='+$('#pic_group_id').val()+'&num='  +$('#num').val();"/>
        </div>

        <?php if (!$this->get('add_type')) { ?>

            <div class="mt10">
                <input type="button" class="but2" value="添加选择图片" onclick="backImgs();"/> &nbsp;
                 是否为跳转图片&nbsp;
                <input name="is_jump" checked type="radio" value='0'/>否&nbsp;&nbsp;
                <input name="is_jump" type="radio" value='1'/>是&nbsp;&nbsp;
            </div>
        <?php } ?>

    </div>
</div>
<div class="main mbody">
    <table style="width: 100%">
        <thead>
        <tr>
            <th align="left" style="line-height:25px">
                <?php echo helper::field_paixu(array('url' => '' . $this->createUrl('material/addMaterialPics') . '?add_type=' . $this->get('add_type') . '&id=' . $page['listdata']['sign'].'&num=' . $this->get('num') . '&p=' . $_GET['p'] . '', 'field_cn' => '按时间排序', 'field' => 'create_time')); ?>
            </th>
        </tr>
        <tr>
            <?php if (!$this->get('add_type')) { ?>
                <th style="width:80px;"><a href="javascript:void(0);" onclick="check_all('.cklist');">全选/反选</a></th>
            <?php } ?>
            <th>图片名称</th>
            <th>缩略图</th>
            <th>组别</th>
            <?php if ($this->get('add_type')) { ?>
                <th>操作</th>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($page['listdata']['list'] as $key => $val) {
            $imgUrl = Resource::model()->findByPk($val['img_id'])->resource_url;
            $picGroup = MaterialPicGroup::model()->findByPk($val['group_id'])->group_name;
            ?>
            <tr style="height: 45px">
                <?php if (!$this->get('add_type')) { ?>
                    <td style="text-align:center;vertical-align:middle;"><input type="checkbox" id="<?php echo $val['id']; ?>" class="cklist" value="<?php echo $imgUrl; ?>"/></td>
                <?php } ?>
                <td style="text-align:center;vertical-align:middle;">
                    <label for="<?php echo $val['id']; ?>"><?php echo $val['name']; ?></label>
                </td>
                <td style="text-align:center;vertical-align:middle;">
                    <img style="width: 40px;height: 40px" src="<?php echo $imgUrl; ?>"
                         onclick="return dialog_frame(this,350,400,0)"
                         href="<?php echo $this->createUrl('material/showPic', array('img_id' => $val['img_id'])); ?>"/>
                </td>
                <td style="text-align:center;vertical-align:middle;">
                    <?php echo $picGroup; ?>
                </td>
                <?php if ($this->get('add_type')) { ?>
                    <td style="text-align:center;vertical-align:middle;">
                        <a type="button" href="#" class="but1" attr-data="<?php echo $imgUrl; ?>"
                           onclick="backfile(this)">添加图片</a>
                    </td>
                    <input id="addType" type="hidden" value="<?php echo $this->get('add_type'); ?>"/>
                <?php } ?>
            </tr>
            <?php ;
        } ?>
        </tbody>
    </table>
    <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>

</div>
<script type="text/javascript">
    function backfile(obj) {
        try {
            var $url = $(obj).attr('attr-data');
            var $num = $('#num').val();
            console.log($url);
            var $addType = $("#addType").val();
            artDialog.opener.addOneImg($url, $addType,$num);
            alert('添加成功');
            artDialog.close();
        } catch (e) {
            alert(e.message);
        }
    }
    function backImgs() {
        var idarr = get_group_checked('.cklist');
        var num = $('#num').val();
        console.log(num)
        var sign = $('#sign').val();
        var is_jump =$("input[name='is_jump']:checked").val();
        console.log(sign);
        var words = '确定上传吗';
        if (idarr.length == 0) {
            alert('请选中至少一个');
            return false;
        }
        if (words.match(/none/) == null) {
            if (!confirm(words)) {
                alert('错误')
                return false;
            }
        }

        $.each(idarr, function (n, value) {
            if (sign != '') {
                artDialog.opener.addImg(value, sign ,num);
            } else {
                artDialog.opener.addImg(value,is_jump ,num);

            }
        });
        alert('添加成功！');
        artDialog.close();
    }

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

