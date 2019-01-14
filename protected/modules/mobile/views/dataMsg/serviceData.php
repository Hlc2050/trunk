<?php require(dirname(__FILE__) . "/../common/header.php"); ?>
<?php $max_date = strtotime(date('Y-m-d',strtotime('-1 day')));?>
<style>
    .am-table {
        margin-bottom: 0;
    }

    .am-table > tbody > tr > td {
        border: 0;
    }

</style>
<div class="admin-content">
    <a id="top" name="top"></a>
    <div></div>

    <div class="admin-content-body">
        <div class="am-panel-group" id="accordion">
            <div class="am-panel am-panel-secd">
                <div class="am-panel am-panel-secondary" style="margin-bottom: 10px">
                    <div style="margin-top:50px;text-align: center">
                        <strong class="am-text-default am-text-lg"> &nbsp;客服部-数据报表
                        </strong>
                    </div>
                    <div class="am-panel-hd">
                        <a href="<?php echo $this->createUrl('/mobile/dataMsg/serviceData');?>?tab_id=<?php echo $page['tab_id'];?>&date=<?php echo ($page['info']['date']-24*60*60);?>">
                            <span style="width: 35%;display: inline-block"><前一天</span>
                        </a>
                        <span><?php echo date('Y-m-d',$page['info']['date']);?></span>
                        <?php if ($max_date > $page['info']['date']) {?>
                            <a href="<?php echo $this->createUrl('/mobile/dataMsg/serviceData');?>?tab_id=<?php echo $page['tab_id'];?>&date=<?php echo ($page['info']['date']+24*60*60);?>" style="float: right;display: inline-block">
                                <span style="display: inline-block">后一天></span>
                            </a>
                        <?php }?>
                    </div>
                </div>

                <div class="tab_box">
                    <a href="?tab_id=0&date=<?php echo ($page['info']['date']);?>" class="<?php if ($page['tab_id'] == 0) echo 'current' ;?>">进粉表</a>
                    <a href="?tab_id=1&date=<?php echo ($page['info']['date']);?>" class="<?php if ($page['tab_id'] == 1) echo 'current' ;?>">产值表</a>
                    <a href="?tab_id=2&date=<?php echo ($page['info']['date']);?>" class="<?php if ($page['tab_id'] == 2) echo 'current' ;?>">预估表</a>
                </div>



                <div class="am-g" style="margin: 10px;">

                    <form style="width: 97%">
                        <?php if ($page['tab_id'] == 0) : ?>
                            <?php require(dirname(__FILE__) . "/DS_fansTap.php"); ?>
                        <?php elseif ($page['tab_id'] == 1) : ?>
                            <?php require(dirname(__FILE__) . "/DS_outputTap.php"); ?>
                        <?php elseif ($page['tab_id'] == 2) : ?>
                            <?php require(dirname(__FILE__) . "/DS_planTap.php"); ?>
                        <?php endif ?>
                    </form>
                </div >
            </div>
        </div>
        <div class="am-g">
            <?php if ($page['tab_id'] == 0) : ?>
                <?php require(dirname(__FILE__) . "/DS_fansTable.php"); ?>
            <?php elseif ($page['tab_id'] == 1) : ?>
                <?php require(dirname(__FILE__) . "/DS_outputTable.php"); ?>
            <?php elseif ($page['tab_id'] == 2) : ?>
                <?php require(dirname(__FILE__) . "/DS_planTable.php"); ?>
            <?php endif ?>
        </div>

    </div>
</div>
<script>
    var date = <?php echo  $page['info']['date'];?>;
    var url = "<?php echo $this->createUrl('/mobile/dataMsg/serviceData');?>";
    function change_paixu(obj,tap_id) {
        var paixu = $(obj).val();
        window.location.href=url+'?tab_id='+tap_id+'&order_type='+paixu+'&date='+date;
    }
    function change_group(obj) {
        var group_id = $(obj).val();
        window.location.href=url+'?tab_id=2&group_id='+group_id+'&date='+date;
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