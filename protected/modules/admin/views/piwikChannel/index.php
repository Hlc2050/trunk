<?php require(dirname(__FILE__)."/../common/head.php");
?>
<script type="text/javascript">
    $(document).ready(function(){
        $(".list0").click(function(){
            var index = null;
            if (true == $(this).prop("checked")) {
                index = $(this).prop('value');
                console.log($(this).prop('value'));
                var strth = ".listTable tr th:nth-child("
                var strtd = ".listTable tr td:nth-child("
                $(strtd+index+")").show();
                $(strth+index+")").show();
            }else {
                index = $(this).prop('value');
                var strth = ".listTable tr th:nth-child("
                var strtd = ".listTable tr td:nth-child("
                $(strtd+index+")").hide();
                $(strth+index+")").hide();
            }

        });

        $(".show_list").click(function () {
            $(".list").toggle();
        });
    });
</script>
<div class="main mhead" xmlns="http://www.w3.org/1999/html">
    <div class="snav">第三方统计 »  渠道统计	</div>

    <div class="mt10">
        <form action="<?php echo $this->createUrl('piwikChannel/index'); ?>">日期：
            <input type="text" id="start_date" class="ipt" style="width: 120px;font-size: 15px;" name="start_date" value="<?php echo $this->get('start_date') ?>"  placeholder="起始日期" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/> 一
            <input type="text" id="end_date" class="ipt" style="width: 120px;font-size: 15px;" name="end_date" value="<?php echo $this->get('end_date') ?>"  placeholder="结束日期" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
            推广人员： <?php
            $promotionStafflist = AdminUser::model()->get_all_user();
            echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($promotionStafflist, 'csno', 'csname_true'),
                array('empty' => '请选择')
            );
            ?>&nbsp;&nbsp;
            计费方式：
            <select id="chgId" name="chgId">
                <option value="">请选择</option>
                <?php $chargeList = vars::$fields['charging_type'];
                foreach ($chargeList as $key => $val) { ?>
                    <option
                        value="<?php echo $val['value']; ?>" <?php if ($_GET['chgId'] != '' && $_GET['chgId'] == $val['value']) echo 'selected'; ?>><?php echo $val['txt']; ?></option>
                <?php } ?>
            </select>
            <select id="search_type" name="search_type">
                <option value="partner_name" <?php echo $this->get('search_type')=='partner_name'?'selected':''; ?>>合作商</option>
                <!--<option value="channel_name" <?php /*echo $this->get('search_type')=='channel_name'?'selected':''; */?>>渠道名称</option>-->
                <option value="channel_code" <?php echo $this->get('search_type')=='channel_code'?'selected':''; ?>>渠道编码</option>
            </select>&nbsp;
            <input type="text" id="search_txt" name="search_txt" class="ipt" value="<?php echo $this->get('search_txt'); ?>" >

            <input type="submit" class="but" value="查询"  >
            <div class="mt10 clearfix">
                <div class="r" style="border: 1px solid black; line-height: 30px;text-align: center;">
                    <span style="padding:10px">显示</span>
             <span class="list" style="display: none;">
                 <input type="checkbox" name="list" value="2" class="list0" checked>上线日期
                 &nbsp;&nbsp;<input type="checkbox" name="list" value="3" class="list0" checked>推广人员
                 &nbsp;&nbsp;<input type="checkbox" name="list" value="4" class="list0" checked>合作商
                 &nbsp;&nbsp;<input type="checkbox" name="list" value="5" class="list0" checked>渠道名称
                 &nbsp;&nbsp;<input type="checkbox" name="list" value="6" class="list0" checked>渠道编码
                 &nbsp;&nbsp;<input type="checkbox" name="list" value="7" class="list0" checked>计费单价
                 &nbsp;&nbsp;<input type="checkbox" name="list" value="8" class="list0" checked>计费方式
                 &nbsp;&nbsp;<input type="checkbox" name="list" value="9" class="list0" checked>pv
                 &nbsp;&nbsp;<input type="checkbox" name="list" value="10" class="list0" checked>uv
                 &nbsp;&nbsp;<input type="checkbox" name="list" value="11" class="list0" checked>独立ip
                 &nbsp;&nbsp;<input type="checkbox" name="list" value="12" class="list0" checked>微信号长按次数
                 &nbsp;&nbsp;<input type="checkbox" name="list" value="13" class="list0" checked>二维码长按次数
                 &nbsp;&nbsp;<input type="checkbox" name="list" value="14" class="list0" checked>第三方投入金额
                 &nbsp;&nbsp;<input type="checkbox" name="list" value="15" class="list0" checked>微信号小组
                 &nbsp;&nbsp;<input type="checkbox" name="list" value="16" class="list0" checked>操作
             </span>
                    <input type="button" class="show_list" style="line-height: 33px;background: #2fa4e7;color: #fff;padding: 0px 10px;;border: 1px solid #2C91CB;" value=">"  >
                </div>
            </div>
        </form>
        <div class="mt10 clearfix">
            <div class="l">
                <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="列表导出" onclick="location=\''.$this->createUrl('piwikChannel/export').'?start_date='.$this->get('start_date').'&end_date='.$this->get('end_date').'&chgId='.$this->get('chgId').'&user_id='.$this->get('user_id').'&search_type='.$this->get('search_type').'&search_txt='.$this->get('search_txt').'\'" />','auth_tag'=>'piwikChannel_export')); ?>
            </div>
            <div class="r">

            </div>
        </div>
    </div>
    <div class="main mbody">
        <form action="<?php echo $this->createUrl('piwikChannel/saveOrder'); ?>" name="form_order" method="post">
            <table class="tb fixTh listTable">
                <thead>
                <tr>
                    <th width="80">ID</th>
                    <th width="100">上线日期</th>
                    <th width="80">推广人员</th>
                    <th width="80">合作商</th>
                    <th width="100">渠道名称</th>
                    <th width="100">渠道编码</th>
                    <th width="80">计费单价</th>
                    <th width="80">计费方式</th>
                    <th width="80">pv</th>
                    <th width="80">uv</th>
                    <th width="80">独立ip</th>
                    <th width="100">微信号长按次数</th>
                    <th width="100">二维码长按次数</th>
                    <th width="100">第三方投入金额</th>
                    <th width="100">微信号小组</th>
                    <th width="100">操作</th>
                </tr>
                </thead>

                <?php
                foreach($list as $r){
                    ?>
                    <tr>
                        <td><?php echo $r['id']?></td>
                        <td><?php echo date('Y-m-d',$r['stat_date']) ?></td>
                        <td><?php echo AdminUser::model()->findByPk($r['sno'])->csname_true?></td>
                        <td><?php echo $r['name']?></td>
                        <td><?php echo $r['channel_name']?></td>
                        <td><?php echo $r['channel_code']?></td>
                        <td><?php echo $r['unit_price']?></td>
                        <td><?php echo vars::get_field_str('charging_type',$r['charging_type']);?></td>
                        <td><?php echo $r['pv'];?></td>
                        <td><?php echo $r['uv']?></td>
                        <td><?php echo $r['ip']?></td>
                        <td><?php echo $r['wechat_touch']?></td>
                        <td><?php echo $r['qr_code_click']?></td>
                        <td><?php echo PiwikHour::model()->piwikHourCost($r['charging_type'],$r['ip'],$r['pv'],$r['uv'],$r['unit_price'])?></td>
                        <td><?php echo WeChatGroup::model()->findByPk($r['weixin_group_id'])->wechat_group_name ?></td>
                        <td>
                            <?php $this->check_u_menu(array('code'=>'<a href="'.$this->createUrl('piwikHour/index').'?&domain='.$r['domain'].'&date='.date('Y-m-d',$r['stat_date']).'">详情</a>','auth_tag'=>'piwikHour_index')); ?>
                        </td>
                    </tr>
                    <?php
                } ?>

            </table>
            <div class="clear"></div>
        </form>
    </div>

