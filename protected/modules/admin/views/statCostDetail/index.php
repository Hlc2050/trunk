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
        <div class="snav">财务管理 » 成本明细</div>

        <div class="mt10">
            <form action="<?php echo $this->createUrl('statCostDetail/index'); ?>">
                <div class="mt10">
                    <select id="search_type" name="search_type">
                        <option
                            value="partner_name" <?php echo $this->get('search_type') == 'partner_name' ? 'selected' : ''; ?>>
                            合作商
                        </option>
                        <option
                            value="channel_name" <?php echo $this->get('search_type') == 'channel_name' ? 'selected' : ''; ?>>
                            渠道名称
                        </option>
                        <option
                            value="channel_code" <?php echo $this->get('search_type') == 'channel_code' ? 'selected' : ''; ?>>
                            渠道编码
                        </option>
                        <option
                            value="weixin_id" <?php echo $this->get('search_type') == 'weixin_id' ? 'selected' : ''; ?>>
                            微信号ID
                        </option>
                    </select>&nbsp;
                    <input type="text" id="search_txt" name="search_txt" class="ipt" style="width: 120px"
                           value="<?php echo $this->get('search_txt'); ?>">
                    推广人员： <?php
                    $promotionStafflist = PromotionStaff::model()->getPromotionStaffList();
                    echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($promotionStafflist, 'user_id', 'name'),
                        array('empty' => '请选择')
                    );
                    ?>
                    客服部：
                    <?php
                    helper::getServiceSelect('csid');
                    ?>
                    &nbsp;&nbsp
                    商品：
                    <?php
                    echo $this->get('csid') ? CHtml::dropDownList('goods_id', $this->get('goods_id'), CHtml::listData(CustomerServiceRelation::model()->getGoodsList($this->get('csid')), 'goods_id', 'goods_name'), array('empty' => '全部')) :
                        CHtml::dropDownList('goods_id', $this->get('goods_id'),CHtml::listData(Goods::model()->findAll(), 'id', 'goods_name'), array('empty' =>'全部'))
                    ?>&nbsp;&nbsp;
                    &nbsp;
                    付款日期：<input type="text" size="12" class="ipt" name="pay_start_time" id="pay_start_time"
                                value="<?php echo $this->get('pay_start_time'); ?>"
                                onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>-
                    <input type="text" size="12"
                           onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" class="ipt" name="pay_end_time"
                           id="pay_end_time"
                           value="<?php echo $this->get('pay_end_time'); ?>"/>
                    <a href="#"
                       onclick="$('#pay_start_time').val('<?php echo date("Y-m-d"); ?>');$('#pay_end_time').val('<?php echo date("Y-m-d"); ?>')">今天</a>
                    <a href="#"
                       onclick="$('#pay_start_time').val('<?php echo date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'))); ?>');$('#pay_end_time').val('<?php echo date("Y-m-d", mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1); ?>')">昨天</a>
                    <a href="#"
                       onclick="$('#pay_start_time').val('<?php echo date("Y-m-d", mktime(0, 0, 0, date('m'), 1, date('Y'))); ?>');$('#pay_end_time').val('<?php echo date("Y-m-d", mktime(0, 0, 0, date('m'), date('t'), date('Y'))); ?>')">本月</a>

                    <?php $a = helper::lastMonth(time()); ?>
                    <a href="#"
                       onclick="$('#pay_start_time').val('<?php echo $a[0]; ?>');$('#pay_end_time').val('<?php echo $a[1]; ?>')">上月</a>
                    <a href="#" onclick="$('#pay_start_time').val('');$('#pay_end_time').val('')">清空</a>

                    <?php
                    $businessTypes = Dtable::toArr(BusinessTypes::model()->findAll());
                    echo CHtml::dropDownList('bs_id', $this->get('bs_id'), CHtml::listData($businessTypes, 'bid', 'bname'),
                        array(
                            'empty' => '业务',
                        )
                    );
                    ?>&nbsp;

                </div>

                <div class="mt10">
                    上线日期：<input type="text" size="12" class="ipt" name="online_start_time" id="online_start_time"
                                value="<?php echo $this->get('online_start_time'); ?>"
                                onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>-
                    <input type="text" size="12"
                           onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" class="ipt" name="online_end_time"
                           id="online_end_time"
                           value="<?php echo $this->get('online_end_time'); ?>"/>
                    <a href="#"
                       onclick="$('#online_start_time').val('<?php echo date("Y-m-d 00:00:00"); ?>');$('#online_end_time').val('<?php echo date("Y-m-d 23:59:59"); ?>')">今天</a>
                    <a href="#"
                       onclick="$('#online_start_time').val('<?php echo date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'))); ?>');$('#online_end_time').val('<?php echo date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1); ?>')">昨天</a>
                    <a href="#"
                       onclick="$('#online_start_time').val('<?php echo date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), 1, date('Y'))); ?>');$('#online_end_time').val('<?php echo date("Y-m-d H:i:s", mktime(23, 59, 59, date('m'), date('t'), date('Y'))); ?>')">本月</a>

                    <?php $a = helper::lastMonth(time()); ?>
                    <a href="#"
                       onclick="$('#online_start_time').val('<?php echo $a[0]; ?>');$('#online_end_time').val('<?php echo $a[1]; ?>')">上月</a>
                    <a href="#" onclick="$('#online_start_time').val('');$('#online_end_time').val('')">清空</a>&nbsp;&nbsp;
                    <input type="submit" class="but" value="查询">
                </div>
                <div class="mt10">
                    <?php $this->check_u_menu(array('code' => '<input type="button" class="but" value="删除选中" onclick="set_some(\'' . $this->createUrl('statCostDetail/del') . '?ids=[@]\',\'确定删除吗？\');" />', 'auth_tag' => 'statCostDetail_del')); ?>&nbsp;
                    <?php $this->check_u_menu(array('code' => '<input type="button" class="but" value="导出成本明细"
                     onclick="location=\'' . $this->createUrl('statCostDetail/export') . '?user_id='.$this->get('user_id').'&csid='.$this->get('csid').'&goods_id='.$this->get('goods_id').'&online_end_time='.$this->get('online_end_time').'&online_start_time='.$this->get('online_start_time').'&pay_start_time='.$this->get('pay_start_time').'&pay_end_time='.$this->get('pay_end_time').'&search_type='.$this->get('search_type').'&search_txt='.$this->get('search_txt').'\'" />', 'auth_tag' => 'statCostDetail_export')); ?>


                </div>
            </form>

            <div class="mt10 clearfix">
                <div class="l">

                </div>

                <div class="r">

                </div>
            </div>
        </div>
        <div class="main mbody">
            <form action="<?php echo $this->createUrl('ad/saveOrder'); ?>" name="form_order" method="post">
                <table class="tb fixTh">
                    <thead>
                    <tr>
                        <th width="60"><?php $this->check_u_menu(array('code' => '<a href="javascript:void(0);"  onclick="check_all(\'.cklist\');">全选</a>', 'auth_tag' => 'statCostDetail_del')); ?>&nbsp;</th>
                        <th width="90"><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('statCostDetail/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '上线日期', 'field' => 'a.stat_date')); ?></th>
                        <th width="80"><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('statCostDetail/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '推广人员', 'field' => 'a.tg_uid')); ?></th>
                        <th width="80">推广小组</th>
                        <th width="80"><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('statCostDetail/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '归属客服部', 'field' => 'a.customer_service_id')); ?></th>
                        <th width="60"><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('statCostDetail/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '商品', 'field' => 'h.goods_name')); ?></th>
                        <th width="80"><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('statCostDetail/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '合作商', 'field' => 'a.partner_id')); ?></th>
                        <th width="80"><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('statCostDetail/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '渠道名称', 'field' => 'c.channel_code')); ?></th>
                        <th width="80"><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('statCostDetail/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '渠道编码', 'field' => 'a.channel_id')); ?></th>
                        <th width="80"><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('statCostDetail/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '微信号', 'field' => 'a.weixin_id')); ?></th>
                        <th width="60"><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('statCostDetail/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '业务', 'field' => 'a.business_type')); ?></th>
                        <th width="60"><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('statCostDetail/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '计费方式', 'field' => 'a.charging_type')); ?></th>
                        <th width="80"><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('statCostDetail/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '友盟金额', 'field' => 'a.money')); ?></th>
                        <th width="90"><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('statCostDetail/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '付款日期', 'field' => 'a.pay_date')); ?></th>
                        <th width="90">操作</th>

                    </tr>
                    <tr>
                        <th>-</th> <th>-</th> <th>-</th> <th>-</th> <th>-</th> <th>-</th> <th>-</th> <th>-</th> <th>-</th> <th>-</th> <th>-</th><th>合计</th>
                        <th><?php echo round($page['listdata']['money'],2) ?></th>
                        <th>-</th> <th>-</th>
                    </tr>
                    </thead>
                    <?php $del = $this->check_u_menu(array('auth_tag' => 'statCostDetail_del'));
                           $edit = $this->check_u_menu(array('auth_tag' => 'statCostDetail_edit'));
                    ?>
                    <?php
                    foreach ($page['listdata']['list'] as $r) {
                        ?>
                        <tr>
                            <td>
                                <?php if ($del) { ?>
                                    <input type="checkbox" class="cklist" value="<?php echo $r['cid'];  ?>"/>
                                <?php }; ?>
                            </td>
                            <td><?php echo date('Y-m-d', $r['stat_date']); ?></td>
                            <td><?php echo $r['csname_true']; ?></td>
                            <td><?php echo $r['linkage_name']; ?></td>
                            <td><?php echo $r['cname']; ?></td>
                            <td style="max-width: 200px"><?php echo $r['goods_name'] ?></td>
                            <td><?php echo $r['partner_name']; ?></td>
                            <td><?php echo $r['channel_name'] ?></td>
                            <td><?php echo $r['channel_code'] ?></td>
                            <td><?php echo $r['wechat_id'] ?></td>
                            <td><?php echo $r['bname'] ?></td>
                            <td><?php echo vars::get_field_str('charging_type', $r['charging_type']) ?></td>
                            <td><?php echo round($r['money'],2) ?></td>
                            <td><?php echo date('Y-m-d', $r['pay_date']) ?></td>
                            <td>
                                <?php if ($edit) { ?>
                                    <a href="<?php echo $this->createUrl('statCostDetail/edit?id='.$r['cid']."&url=".$page['listdata']['url']);  ?>">编辑</a>
                                <?php }; ?>
                            </td>
                        </tr>
                        <?php
                    } ?>

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

