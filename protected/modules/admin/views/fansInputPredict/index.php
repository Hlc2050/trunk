<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<style>
</style>
<script>
    function exportList() {
        var data = $("#serchForm").serialize();
        var url = "<?php echo $this->createUrl('fansInput/export');?>";
        window.location.href = url + '?' + data;
    }
</script>
<div class="main mhead">
    <div class="snav">推广管理 » 微信进粉预估</div>
    <div class="mt10 clearfix" >
        <form action="<?php echo $this->createUrl('fansInputPredict/index'); ?>" id="serchForm">
            <div class="mt10">
                日期：
                <input type="text" size="20" class="ipt" style="width:120px;" name="start_date"
                       id="start_date"
                       value="<?php echo $page['date']['start_date']; ?>"
                       onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>-
                <input type="text" size="20" class="ipt" style="width:120px;" name="end_date" id="end_date"
                       value="<?php echo $page['date']['end_date']; ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>&nbsp;
                合作商：
                <input type="text" id="partner_name" name="partner_name" class="ipt" style="width:130px"
                       value="<?php echo $this->get('partner_name'); ?>">
                渠道名称：
                <input type="text" id="channel_name" name="channel_name" class="ipt" style="width:130px;"
                       value="<?php echo $this->get('channel_name'); ?>">&nbsp;
                渠道编码：
                <input type="text" id="channel_code" name="channel_code" class="ipt" style="width:130px;"
                       value="<?php echo $this->get('channel_code'); ?>">&nbsp;
            </div>
            <div class="mt10">
                客服部：
                <?php
                helper::getServiceSelect('csid',array(array('htmlOptions'=>'全部')));
                ?>
                &nbsp;&nbsp
                推广人员：
                <?php
                $promotionStafflist = PromotionStaff::model()->getPromotionStaffList();
                echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($promotionStafflist, 'user_id', 'name'),
                    array('empty' => '请选择')
                );
                ?>
                微信号：
                <input type="text" id="wechat_id" name="wechat_id" class="ipt" style="width:130px;"
                       value="<?php echo $this->get('wechat_id'); ?>">&nbsp;
                <input type="submit" class="but" value="查询">
                <?php $this->check_u_menu(array('code' => '<input type="button" class="but" value="导出列表"
                     onclick="location=\''.$this->createUrl('fansInputPredict/export').'?start_date='.$this->get('start_date').'&end_date='.$this->get('end_date').
                    '&partner_name='.$this->get('partner_name').'&channel_name='.$this->get('channel_name').'&channel_code='.$this->get('channel_code')
                    .'&csid='.$this->get('csid').'&user_id='.$this->get('user_id').'&wechat_id='.$this->get('wechat_id').'\'" />',
                    'auth_tag' => 'fansInputPredict_export')); ?>
            </div>
        </form>
    </div>
</div>
<div class="main mbody" >
    <form>
        <table class="tb fixTh" style="float: left;width: 55%">
            <thead>
            <tr>
                <th>日期</th>
                <th>合作商</th>
                <th >渠道名称</th>
                <th>渠道编码</th>
                <th>客服部</th>
                <th>推广人员</th>
                <th>微信号</th>
                <th>合计</th>
            </tr>
            <tr>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td><?php echo $page['list']['total_num'] ?></td>
            </tr >
            </thead >
            <tbody>
            <?php
            foreach ($page['list']['listdata'] as $value) {
                ?>
                <tr>
                    <td><?php echo date('Y-m-d',$value['date']);?></td >
                    <td><?php echo $value['p_name'];?></td >
                    <td><?php echo $value['channel_name'];?></td >
                    <td><?php echo $value['channel_code'];?></td >
                    <td><?php echo $value['cname'];?></td >
                    <td><?php echo $value['csname_true'];?></td >
                    <td><?php echo $value['wechat_id'];?></td >
                    <td><?php echo $value['date_num'];?></td >
                </tr>
            <?php } ?>

            </tbody>

        </table>
        <div  class="tb" style="width: 40%;border:none;border-right:1px solid #d1d5de;overflow-x: scroll;">
            <table class="fixTh" style="width: 1200px">
                <thead>
                <tr>
                    <?php
                    for ($i = 0; $i <= 23; $i++) {
                        echo "<th>" . $i . "点</th>";
                    }
                    ?>
                </tr>
                <tr>
                    <?php
                    for ($i = 0; $i <= 23; $i++) {
                        $key = $i;
                        if ($i<10) {
                            $key = '0'.$i;
                        }
                        $hours_total_num = isset($page['list']['hours_total_num'][$key]) ? $page['list']['hours_total_num'][$key]:0;
                        echo "<td>" . $hours_total_num . "</td>";
                    }
                    ?>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($page['list']['listdata'] as $key=>$value) {
                    ?>
                    <tr>
                        <?php
                        for ($i = 0; $i <= 23; $i++) {
                            $date = date('Ymd',$value['date']);
                            $key = $i<10 ? ($date.'0'.$i):($date.$i);
                            $num = isset($value['hours_num'][$key]) ? $value['hours_num'][$key]:0;
                            echo "<td>".$num."</td>";
                        }
                        ?>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="pagebar"><?php echo $page['list']['pagearr']['pagecode']; ?></div>
        <div class="clear"></div>
    </form>
</div>



