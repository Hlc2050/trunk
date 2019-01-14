<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<script>
    function exportList() {
        var data = $("#serchRet").serialize();
        var url = "<?php echo $this->createUrl('promotion/export');?>";
        window.location.href=url+'?'+data;
    }
</script>
<div class="main mhead">
    <div class="snav">推广管理 » 推广列表</div>
    <div class="mt10">
        <form action="<?php echo $this->createUrl('promotion/index'); ?>" id="serchRet">
            <div class="mt10">
                推广域名：
                <input style="width: 120px" type="text" id="domain" name="domain" class="ipt"
                       value="<?php echo $this->get('domain'); ?>">
                跳转域名：
                <input style="width: 120px" type="text" id="goto_domain" name="goto_domain" class="ipt"
                       value="<?php echo $this->get('goto_domain'); ?>">
                白域名：
                <input style="width: 120px" type="text" id="white_domain_name" name="white_domain_name" class="ipt"
                       value="<?php echo $this->get('white_domain_name'); ?>">
                渠道名称：
                <input style="width: 120px" type="text" id="channel_name" name="channel_name" class="ipt"
                       value="<?php echo $this->get('channel_name'); ?>">
                渠道编码：
                <input style="width: 120px" type="text" id="channel_code" name="channel_code" class="ipt"
                       value="<?php echo $this->get('channel_code'); ?>">
                合作商：
                <input style="width: 120px" type="text" id="partner" name="partner" class="ipt"
                       value="<?php echo $this->get('partner'); ?>">
            </div>           
            <div class="mt10">
                图文编码：
                <input style="width:120px;" type="text" name="article_code" class="ipt"
                       value="<?php echo $this->get('article_code'); ?>">
                ID:
                <input style="width: 120px" type="text" id="id" name="id" class="ipt"
                       value="<?php echo $this->get('id'); ?>">

                业务：<select id="business" name="business">
                        <option value="0">全部</option>
                        <option value="1" <?php if ($this->get('business') == "1") { echo 'selected';}?> >微销</option>
                        <option value="2" <?php if ($this->get('business') == "2") { echo 'selected';} ?> >电销</option>
                      </select>
                微信号小组：
                <input type="text" id="wechat_group_name" name="wechat_group_name" class="ipt"
                       value="<?php echo $this->get('wechat_group_name'); ?>">
                文章名称：
                <input style="width: 120px" type="text" id="artName" name="artName" class="ipt"
                       value="<?php echo $this->get('artName'); ?>">
                推广类型：<?php
                $promotion_types = vars::$fields['promotion_types'];
                echo CHtml::dropDownList('promotion_type', $this->get('promotion_type'), CHtml::listData($promotion_types, 'value', 'txt'),
                    array('empty' => '全部')
                );
                ?>
            </div>
            <div class="mt10">
                计费方式：
                <?php
                $chargeList = vars::$fields['charging_type'];
                echo CHtml::dropDownList('chgId', $this->get('chgId'), CHtml::listData($chargeList, 'value', 'txt'),
                    array('empty' => '全部')
                ); ?>
                推广人员： <?php
                $promotionStafflist = AdminUser::model()->get_all_user(0,1);
                echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($promotionStafflist, 'csno', 'csname_true'),
                    array('empty' => '全部')
                );
                ?>
                状态：
                <?php
                $status = vars::$fields['promotion_status'];
                echo CHtml::dropDownList('status', $this->get('status'), CHtml::listData($status, 'value', 'txt'),
                    array('empty' => '全部')
                ); ?>&nbsp;
               白域名:
                <select id="white_domain" name="white_domain">
                    <option value=" ">全部</option>
                    <option value="1" <?php if($this->get('white_domain')==1){echo "selected";}?>>开启</option>
                    <option value="2" <?php if($this->get('white_domain')==2){echo "selected";}?>>关闭</option>
                </select>
                链接类型:
                <select id="line_type" name="line_type">
                    <option value=" ">全部</option>
                    <option value="1" <?php if($this->get('line_type')==1){echo "selected";}?>>普通</option>
                    <option value="2" <?php if($this->get('line_type')==2){echo "selected";}?>>静态</option>
                </select>
                <input type="submit" class="but"  value="查询">
            </div>
    </div>
    <div class="mt10 clearfix">
        <div class="l" id="container">
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="添加推广" onclick="location=\'' . $this->createUrl('promotion/add?url=' . $page['listdata']['url']) . '\'" />', 'auth_tag' => 'promotion_add')); ?>
            &nbsp;
        </div>
        <div class="l" id="container">
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="导出" onclick="exportList()" />', 'auth_tag' => 'promotion_export')); ?>
            &nbsp;
        </div>
        <div class="r">
        </div>
    </div>
</div>
<div class="main mbody">
    <form>
        <table class="tb fixTh">
            <thead>
            <tr>
                <th style="width: 30px"
                    align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('promotion/index') . '?p=' . $_GET['p'] . '', 'field_cn' => 'ID', 'field' => 'id')); ?></th>
                <th style="width: 95px">上线日期</th>
                <th style="width: 80px">域名</th>
                <th style="width: 70px">业务</th>
                <th style="width: 70px">合作商</th>
                <th style="width: 90px">渠道名称</th>
                <th style="width: 70px">渠道编码</th>
                <th style="width: 100px">素材标题</th>
                <th style="width: 60px">图文编码</th>
                <th style="width: 75px">下单标题</th>
                <th style="width: 70px">推广人员</th>
                <th style="width: 75px">跳转链接</th>
                <th style="width: 75px">文章链接</th>
                <th style="width: 75px">链接类型</th>
                <th style="width: 60px">计费方式</th>
                <th style="width: 50px">单价</th>
                <th style="width: 80px">微信号小组</th>
                <th style="width: 40px" align='center'>状态</th>
                <th style="width: 40px" align='center'>类型</th>
                <th style="width: 90px">操作</th>
            </tr>
            </thead>
            <?php
            $stop_auth = $this->check_u_menu(array('auth_tag'=>'promotion_stop'));
            $log_auth = $this->check_u_menu(array('auth_tag'=>'domainPromotionChange_index'));
            $edit_auth = $this->check_u_menu(array('auth_tag'=>'promotion_edit'));
            $delete_auth = $this->check_u_menu(array('auth_tag'=>'promotion_delete'));
            foreach ($page['listdata']['list'] as $key => $val) {
                $online_date = date('Y-m-d', $val['online_date']);//上线日期
                $charging_type = vars::get_field_str('charging_type', $val['charging_type']);//计费方式
                $status = vars::get_field_str('promotion_status', $val['status']);//状态
                $is_public_domain = $val['is_public_domain'] == 1?"(公众号)":"";
                $promotion_type = $val['promotion_type'] == '' ? '-' : vars::get_field_str('promotion_types', $val['promotion_type']);//状态

                ?>
                <tr>
                    <td><?php echo $val['id']; ?></td>
                    <td><?php echo $online_date; ?></td>
                    <td>
                        <?php if($val['domain_list']){
                            foreach ($val['domain_list'] as $k=>$value) {
                                if ($k==1){
                                    echo '<div id="more_domain_'.$val['id'].'" style="display:none">';
                                }
                                if ($value['domain_status']!=0 && $value['domain_status']!=1) {
                                    echo '<font color="red">'.$value['domain'].'</font>';
                                }else {
                                    echo $value['domain'];
                                }
                                if ($value['is_public_domain'] == 1) {
                                    echo '(公众号)';
                                }
                                if ($val['domain_list'][$k+1]) {
                                    echo '<br/>';
                                }
                                if ($k>=1 && !$val['domain_list'][$k+1]){
                                    echo '</div>';
                                }
                            }
                            if (count($val['domain_list'])>1){
                                echo '<a onclick="show_domains(this,'.$val['id'].',1)">查看更多域名</a>';
                            }
                        ?>
                        <?php } else{echo "无";}?>
                    </td>
                    <td>
                        <?php echo $val['type_name'] ; ?></td>
                    <td><a title="<?php echo $val['partner_name']; ?>" href="#"
                           style="color: black"><?php echo helper::cut_str($val['partner_name'], 9); ?></a></td>
                    <td><?php echo $val['channel_name']; ?></td>
                    <td><?php echo $val['channel_code']; ?></td>
                    <td style="max-width: 200px">
                        <?php if(!empty($val['article_title'])){?>
                            <?php if($val['article_type'] == 3){ ?>
                                <?php  $sql = "select * FROM online_material_manage WHERE is_main_page=1 and article_code= '" . $val['article_code'] . "' order by id" ;
                                $data = Yii::app()->db->createCommand($sql)->queryAll();?>
                                <a href="#"
                                   onclick="dialog({title:'素材标题',content:$(this).attr('data-clipboard-text')}).showModal();"
                                   data-clipboard-text="<?php echo $data[0]['article_title']; ?>">点击查看</a>
                            <?php }else{ ?>
                            <a href="#"
                               onclick="dialog({title:'素材标题',content:$(this).attr('data-clipboard-text')}).showModal();"
                               data-clipboard-text="<?php echo $val['article_title']; ?>">点击查看</a>
                                <?php } ?>
                        <?php }else echo "未选素材" ?>
                    </td>
                    <td><?php echo $val['article_code']; ?></td>
                    <td>
                        <?php if ($val['order_id'] == 0) { ?>
                            无
                        <?php } else {
                            $order_title = OrderTemplete::model()->findByPk($val['order_id'])->order_title;
                            ?>
                            <a href="#"
                               onclick="dialog({title:'下单标题',content:$(this).attr('data-clipboard-text')}).showModal();"
                               data-clipboard-text="<?php echo $order_title; ?>">点击查看</a>
                        <?php } ?>
                    </td>
                    <td><?php echo $val['csname_true']; ?></td>
                    <td>
                        <?php
                       $type = $val['is_white_domain'];
                       $goto_url=helper::build_goto_link($val,$type);
                        if ($val['status'] == 2) { ?>
                            <span style="font-weight: bold;color:red;">暂停中</span>
                        <?php } elseif ($goto_url == 2) { ?>
                            <span style="font-weight: bold;color:red;">被拦截</span>
                        <?php } else {
                            if (!$goto_url) {
                                echo "<b>无</b>";
                            } else {
                                ?>
                                <a href="#"
                                   onclick="dialog({title:'跳转链接，直接复制一下',content:$(this).attr('data-clipboard-text')}).showModal();"
                                   data-clipboard-text="<?php echo $goto_url; ?>">点击查看</a>
                            <?php }
                        } ?>
                    </td>
                    <td>
                        <?php
                        $link_url=helper::build_tg_link($val);
                        if ($val['status'] == 2) { ?>
                            <span style="font-weight: bold;color:red;">暂停中</span>
                        <?php }  else {
                            if (!$link_url) {
                                echo "<b>无</b>";
                            } else {
                                ?>
                                <a href="#"
                                   onclick="dialog({title:'推广链接，直接复制一下',content:$(this).attr('data-clipboard-text')}).showModal();"
                                   data-clipboard-text="
                                   <?php foreach ($link_url as $k=>$v){
                                       if ($v['domain_status']!=0 && $v['domain_status']!=1) {
                                           $status_str = '('.vars::get_field_str('domain_status', $v['domain_status']).')';
                                           echo $status_str.$v['domain'];
                                       }else {
                                           echo $v['domain'];
                                       }
                                       if ($link_url[$k+1]){
                                           echo '<br/>';
                                       }
                                   } ; ?>
                            ">点击查看</a>
                            <?php }
                        } ?>
                    </td>
                    <td><?php echo $val['line_type'] == 1?'静态':'普通'; ?></td>
                    <td><?php echo $charging_type; ?></td>
                    <td><?php echo $val['unit_price']; ?></td>
                    <td><a href="javascript:" onclick="window.open('<?php echo $this->createUrl('weChatGroup/index/?search_type=keys&search_txt='.$val['wechat_group_name'].'&from=promote');?>','_blank')"><?php echo $val['wechat_group_name']; ?></a></td>
                    <td><?php echo $status; ?></td>
                    <td><?php echo $promotion_type; ?></td>
                    <td>
                        <?php
                        $confirm_log = $val['status'] == 0 ? "'此推广正在上线，是否仍然删除！'" : "'请谨慎删除！'";
                        $stop_name = $val['status'] == 2 ? "启动" : "暂停";
                        if ($val['status'] != 1) {
                            if ($stop_auth) echo '<a href="' . $this->createUrl('promotion/stop', array('id' => $val['id'], 'status' => $val['status'])) . '"  onclick="return confirm(\'是否改变推广状态！\')">' . $stop_name . '&nbsp;&nbsp;</a>';
                        }
                        if ($log_auth) echo '<a onclick="return dialog_frame(this,700,500)" href="' . $this->createUrl('domainPromotionChange/index', array('promotion_id' => $val['id'])) . '">记录</a>';
                        echo '<br/>';
                        if ($edit_auth) echo '<a href="' . $this->createUrl('promotion/edit?id=' . $val['id'] . '&url=' . $page['listdata']['url']) . '">修改&nbsp;&nbsp;</a>';
                        if ($delete_auth) echo '<a href="' . $this->createUrl('promotion/delete?id=' . $val['id'] . '&url=' . $page['listdata']['url']) . '"  onclick="return confirm(' . $confirm_log . ')">删除</a>';
                        ?>

                    </td>
                </tr>
            <?php } ?>
        </table>
        <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
        <div class="clear"></div>
    </form>
</div>
<script>
    function show_domains(dom,id,show) {
       var domain_dom = $('#more_domain_'+id);
       var new_show = 0;
       if(show == 1) {
           domain_dom.css('display','block');
           $(dom).html('隐藏域名');
       }else{
           new_show =1;
           domain_dom.css('display','none');
           $(dom).html('显示更多域名');
       }
        $(dom).attr('onclick','show_domains(this,'+id+','+new_show+')');
    }
</script>
<?php require(dirname(__FILE__) . "/../common/foot.php"); ?>
