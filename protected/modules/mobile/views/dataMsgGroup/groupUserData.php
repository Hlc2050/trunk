<?php require(dirname(__FILE__) . "/../common/header.php"); ?>
<style>
    .am-table {
        margin-bottom: 0;
    }

    .am-table > tbody > tr > td {
        border: 0;
    }

</style>
<?php $max_date = strtotime(date('Y-m-d',strtotime('-1 day')));?>
<div class="admin-content">
    <a id="top" name="top"></a>
    <div></div>
    <?php $base_url = $this->createUrl('/mobile/dataMsgGroup/groupUserData').'?service_id='.$page['service_info']['id'].'&date='.$page['info']['date'];?>

    <div class="admin-content-body">
        <div class="am-panel-group" id="accordion">
            <div class="am-panel am-panel-secd">
                <div class="am-panel-hd">
                    <strong class="am-text-default am-text-lg"> &nbsp;客服部-数据报表
                    </strong>
                </div>
                <div class="am-panel am-panel-secondary" style="margin-bottom: 10px">
                    <div class="am-panel-hd">
                        <a href="<?php echo $this->createUrl('/mobile/dataMsgGroup/groupUserData');?>?service_id=<?php echo $page['service_info']['id'];?>&date=<?php echo ($page['info']['date']-24*60*60);?>&data_type=<?php echo $page['data_type'];?>">
                            <span style="width: 35%;display: inline-block"><前一天</span>
                        </a>
                        <span><?php echo date('Y-m-d',$page['info']['date']);?></span>
                        <?php if ($max_date > $page['info']['date']) {?>
                            <a href="<?php echo $this->createUrl('/mobile/dataMsgGroup/groupUserData');?>?service_id=<?php echo $page['service_info']['id'];?>&date=<?php echo ($page['info']['date']+24*60*60);?>" style="float: right;display: inline-block">
                                <span style="display: inline-block">后一天></span>
                            </a>
                        <?php }?>
                    </div>
                </div>
                <div class="am-g" style="margin: 10px;">

                    <form style="width: 98%">
                        <label><?php echo $page['service_info']['cname'];?></label>
                        <select name="order" class="am-input-sm" style="width: 60%;padding: 5px;float: right" onchange="change_paixu(this)">
                            <option value="1" <?php if ($page['info']['order_type'] ==1) echo 'selected' ;?>>涨幅-从低到高</option>
                            <option value="2" <?php if ($page['info']['order_type'] == 2) echo 'selected' ;?>>涨幅-从高到低</option>
                        </select>
                    </form>
                </div >
            </div>
        </div>
        <div class="am-g">
            <?php foreach ($page['info']['list'] as $value) {?>
                <div class="am-panel am-panel-secondary">
                    <div class="am-panel-hd"><?php echo $value['user_name'];?>(<?php echo $page['group_info'][$page['user_group'][$value['tg_uid']]];?>)</div>
                    <div class="am-panel-bd">
                        <table class="am-table am-table-centered">
                            <tbody>
                            <?php if ($page['data_type'] == 1) {?>
                                <tr>
                                    <td>实际进粉</td>
                                    <td>计划进粉</td>
                                    <td class="dif_class_1"><span><?php echo $value['fans_radio'];?></span>%</td>
                                </tr>
                                <tr>
                                    <td><?php echo $value['data_fans'];?></td>
                                    <td><?php echo $value['plan_fans'];?></td>
                                    <td class="dif_class_11">(<?php echo $value['data_fans']-$value['plan_fans'];?>)</td>
                                </tr>
                            <?php } else { ?>
                                <tr>
                                    <td>实际产值</td>
                                    <td>计划产值</td>
                                    <td class="dif_class_2"><span><?php echo $value['output_radio'];?></span>%</td>
                                </tr>
                                <tr>
                                    <td><?php echo $value['data_output'];?></td>
                                    <td><?php echo $value['plan_output'];?></td>
                                    <td class="dif_class_21">(<?php echo $value['data_output']-$value['plan_output'];?>)</td>
                                </tr>
                            <?php } ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } ?>
        </div>

    </div>
</div>
<script type="text/javascript">
    var base_url = "<?php echo $base_url;?>";
    function change_paixu(obj) {
        var paixu = $(obj).val();

        window.location.href=''+base_url+'&order_type='+paixu;

    }
</script>
<?php
$remind = Yii::app()->params['remind'];
$high_fans = $remind['fans_up'];
$low_fans = $remind['Fans_down'];
//产值数据浮动提醒配置
$high_output = $remind['output_up'];
$low_output = $remind['output_down'];
?>
<script type="text/javascript">
    var high_fans = <?php echo $high_fans;?>;
    var low_fans = <?php echo $low_fans;?>;
    var low_output = <?php echo $low_output;?>;
    var high_output = <?php echo $high_output;?>;
    $(".dif_class_1").each(function (key,obj) {
        var val = $(this).find('span').text();
        console.log(val);
        if (val > high_fans) {
            $(this).css({color:"red"});
            $(this).parent().parent().find('.dif_class_11').css({color:"red"});
        }
        if (val <low_fans) {
            $(this).css({color:"blue"});
            $(this).parent().parent().find('.dif_class_11').css({color:"blue"});
        }
    });
    $(".dif_class_2").each(function (key,obj) {
        var val = $(this).find('span').text();
        if (val > high_output) {
            $(this).css({color:"red"})
            $(this).parent().parent().find('.dif_class_21').css({color:"red"});
        }
        if (val <low_output) {
            $(this).css({color:"blue"})
            $(this).parent().parent().find('.dif_class_21').css({color:"blue"});
        }
    })

</script>
</body>
</html>