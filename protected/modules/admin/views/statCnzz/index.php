<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<style>
    .file {
        position: relative;
        display: inline-block;
        background: #2fa4e7;
        border: 1px solid #2C91CB;
        padding: 4px 12px;
        overflow: hidden;
        color: white;
        text-decoration: none;
        text-indent: 0;
        line-height: 20px;
        margin-left: 4px;
    }

    .file input {
        position: absolute;
        font-size: 100px;
        right: 0;
        top: 0;
        opacity: 0;
    }

    .file:hover {
        color: white;
    }
</style>
<script>
    function show_frame_infos() {

        var d = dialog({
            title: 'Excel导入结果',
            content: $(window.frames["frame02"].document).find(".msgbox0009").html(),
            okValue: '恩',
            ok: function () {
                window.location.reload(1);
            }
        });
        d.showModal();
    }

</script>

<div class="main mhead">
    <div class="snav">第三方统计 » 友盟统计</div>

    <div class="mt10">
        <form action="<?php echo $this->createUrl('statCnzz/index'); ?>">
            <select id="search_type" name="search_type">
                <option value="domain" <?php echo $this->get('search_type') == 'domain' ? 'selected' : ''; ?>>域名
                </option>
                <option value="partner" <?php echo $this->get('search_type') == 'partner' ? 'selected' : ''; ?>>合作商
                </option>
                <option
                    value="channel_code" <?php echo $this->get('search_type') == 'channel_code' ? 'selected' : ''; ?>>
                    渠道编码
                </option>
                <option
                    value="channel_name" <?php echo $this->get('search_type') == 'channel_name' ? 'selected' : ''; ?>>
                    渠道名称
                </option>
            </select>&nbsp;
            <input style="width: 150px" type="text" id="search_txt" name="search_txt" class="ipt"
                   value="<?php echo $this->get('search_txt'); ?>">&nbsp;
            推广人员： <?php
            $promotionStafflist = PromotionStaff::model()->getPromotionStaffList();
            echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($promotionStafflist, 'user_id', 'name'),
                array('empty' => '请选择')
            );
            ?>&nbsp;
            推广类型：
            <?php
            $promotion_types = vars::$fields['promotion_types'];
            echo CHtml::dropDownList('promotion_type', $this->get('promotion_type'), CHtml::listData($promotion_types, 'value', 'txt'),
                array('empty' => '全部')
            );
            ?>
            统计日期：<input style="width: 150px" type="text" size="20" class="ipt" name="stat_start_time"
                        id="stat_start_time"
                        value="<?php echo $this->get('stat_start_time'); ?>"
                        onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>-
            <input style="width: 150px" type="text" size="20"
                   onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" class="ipt" name="stat_end_time"
                   id="stat_end_time"
                   value="<?php echo $this->get('stat_end_time'); ?>"/>
            <a href="#"
               onclick="$('#stat_start_time').val('<?php echo date("Y-m-d"); ?>');$('#stat_end_time').val('<?php echo date("Y-m-d"); ?>')">今天</a>
            <a href="#"
               onclick="$('#stat_start_time').val('<?php echo date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'))); ?>');$('#stat_end_time').val('<?php echo date("Y-m-d", mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1); ?>')">昨天</a>
            <a href="#"
               onclick="$('#stat_start_time').val('<?php echo date("Y-m-d", mktime(0, 0, 0, date('m'), 1, date('Y'))); ?>');$('#stat_end_time').val('<?php echo date("Y-m-d", mktime(0, 0, 0, date('m'), date('t'), date('Y'))); ?>')">本月</a>

            <?php $a = helper::lastMonth(time()); ?>
            <a href="#"
               onclick="$('#stat_start_time').val('<?php echo $a[0]; ?>');$('#stat_end_time').val('<?php echo $a[1]; ?>')">上月</a>

            <?php $a = helper::get_period_time('3month'); ?>
            <a href="#"
               onclick="$('#stat_start_time').val('<?php echo $a['beginTime']; ?>');$('#stat_end_time').val('<?php echo $a['endTime']; ?>')">最近三个月</a>
            <?php $a = helper::get_period_time('half_year'); ?>
            <a href="#"
               onclick="$('#stat_start_time').val('<?php echo $a['beginTime']; ?>');$('#stat_end_time').val('<?php echo $a['endTime']; ?>')">最近半年</a>
            <a href="#" onclick="$('#stat_start_time').val('');$('#stat_end_time').val('')">清空</a>&nbsp;&nbsp;
            <input type="submit" class="but" value="查询">
        </form>

        <div class="mt10 clearfix">
            <div class="l">
                <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="添加统计" onclick="location=\'' . $this->createUrl('statCnzz/add') . '?search_type=' . $this->get('search_type') . '&search_txt=' . $this->get('search_txt') . '\'" />', 'auth_tag' => 'statCnzz_add')); ?>
                <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="删除选中" onclick="set_some(\'' . $this->createUrl('statCnzz/delete') . '?ids=[@]\',\'确定删除吗？\');" />', 'auth_tag' => 'statCnzz_delete')); ?>
            </div>
            <form action="<?php echo $this->createUrl('statCnzz/import'); ?>" method="post" target="frame02"
                  enctype="multipart/form-data">
                <?php $this->check_u_menu(array('code' => '<a href="javascript:" class="file">选择文件 <input type="file" name="filename" /> </a>', 'auth_tag' => 'statCnzz_import')); ?>
                <?php $this->check_u_menu(array('code' => '<input type="submit" name="submit" class="but2" value="导入" />', 'auth_tag' => 'statCnzz_import')); ?>
            </form>
            <div class="r">

            </div>
        </div>
    </div>
    <div class="main mbody">
        <form action="<?php echo $this->createUrl('ad/saveOrder'); ?>" name="form_order" method="post">
            <table class="tb fixTh">
                <thead>
                <tr>
                    <th width="100"><a href="javascript:void(0);" onclick="check_all('.cklist');">全选/反选</a></th>
                    <th width="80"><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('statCnzz/index') . '?p=' . $_GET['p'] . '', 'field_cn' => 'ID', 'field' => 'a.id')); ?></th>
                    <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('statCnzz/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '日期', 'field' => 'a.stat_date')); ?></th>
                    <th>推广类型</th>
                    <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('statCnzz/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '受访域名', 'field' => 'a.domain')); ?></th>
                    <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('statCnzz/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '合作商', 'field' => 'd.name')); ?></th>
                    <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('statCnzz/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '渠道名称', 'field' => 'c.channel_name')); ?></th>
                    <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('statCnzz/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '渠道编码', 'field' => 'c.channel_code')); ?></th>
                    <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('statCnzz/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '浏览次数pv', 'field' => 'a.pv')); ?></th>
                    <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('statCnzz/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '独立访客uv', 'field' => 'a.uv')); ?></th>
                    <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('statCnzz/index') . '?p=' . $_GET['p'] . '', 'field_cn' => 'IP', 'field' => 'a.ip')); ?></th>
                    <th>推广人员</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>合计</td>
                    <td><?php echo $page['listdata']['pv']; ?></td>
                    <td><?php echo $page['listdata']['uv']; ?></td>
                    <td><?php echo $page['listdata']['ip']; ?></td>
                    <td>-</td>
                </tr>

                <?php foreach ($page['listdata']['list'] as $r) { ?>
                    <tr>
                        <td><input type="checkbox" class="cklist" value="<?php echo $r['id']; ?>"/></td>
                        <td><?php echo $r['id']; ?></td>
                        <td><?php echo date('Y-m-d', $r['stat_date']); ?></td>
                        <td><?php echo $r['promotion_type'] == '' ? '-' : vars::get_field_str('promotion_types', $r['promotion_type']) ?></td>
                        <td><?php echo $r['domain']; ?></td>
                        <td><?php echo $r['partner_name']; ?></td>
                        <td><?php echo $r['channel_name'] ?></td>
                        <td><?php echo $r['channel_code'] ?></td>
                        <td><?php echo $r['pv'] ?></td>
                        <td><?php echo $r['uv'] ?></td>
                        <td><?php echo $r['ip'] ?></td>
                        <td><?php echo $r['csname_true']; ?></td>
                    </tr>
                    <?php }; ?>
                </tbody>
            </table>
            <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
            <div class="clear"></div>
        </form>
    </div>

    <div class="float-simage-box" style="position: absolute;">
    </div>


    <script src="/static/lib/jquery.jcrop/jquery.jcrop.min.js"></script>
    <link rel="stylesheet" href="/static/lib/jquery.jcrop/jquery.Jcrop.css">
    <script>
        $(".slider-simage").hover(
            function () {
                $(".float-simage-box").show();
                var imgurl = $(this).attr("src");
                $(".float-simage-box").html('<img src="' + imgurl + '" width=150 />');
                var left = $(this).offset().left - 150;
                var top = $(this).offset().top;
                $(".float-simage-box").css({"left": left + 'px', "top": top + 'px'});
            },
            function () {
                $(".float-simage-box").hide();
            }
        );
        //封面快速裁剪
        $(".slider-simage").click(function () {
            var img = $(this).attr("src");
            var id = $(this).attr("data-id");
            var table = "ad_list";
            var idField = 'id';
            var imgField = 'ad_img';
            info_cover_crop(table, id, idField, img, imgField);
        })


    </script>

    <div id="framebox" style="display: none;">
        <iframe name="frame02" src="" id="frame02"></iframe>
    </div>

