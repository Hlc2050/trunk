<?php require(dirname(__FILE__) . "/../common/header.php"); ?>
<script  src="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/artDialog4.1.7/artDialog.js?skin=<?php echo $page['dialog_skin'];?>" ></script>
<script  src="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/artDialog4.1.7/plugins/iframeTools.source.js" ></script>
<link rel="stylesheet" href="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/artDialog-master/css/ui-dialog.css">
<script src="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/artDialog-master/dist/dialog-plus.js"></script>
<style>

    .am-table > tbody > tr > td {
        border-right: solid 1px #cccccc;
        white-space: nowrap;
    }

    .scroll_div::-webkit-scrollbar-track-piece {
        background-color: #CCCCCC;
        border-left: 1px solid rgba(0, 0, 0, 0);
    }
    .scroll_div::-webkit-scrollbar {
        width: 6px;
        height: 15px;

    }
    .scroll_div::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.5);
        background-clip: padding-box;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        min-height: 28px;
    }
    .scroll_div::-webkit-scrollbar-thumb:hover {
        background-color: #CCCCCC;
    }

    .border_table > tbody > tr:last-child >td{
        border-bottom: solid 1px #cccccc;
    }

</style>
<div class="admin-content">
    <a id="top" name="top"></a>
    <div></div>

    <div class="admin-content-body">
        <div class="am-g">
            <div class="am-panel am-panel-secondary" style="margin-top: 60px;">
                <div class="am-panel-hd"><?php echo $page['title']; ?></div>
                <div style="font-size: 14px;margin-top: 5px">&nbsp;&nbsp;提交日期&nbsp;&nbsp;&nbsp;&nbsp;<?php echo date('Y-m-d H:i:s',$page['week_plan']['update_time']);?></div>
                <div class="">
                    <div style="width: 100%;margin-top: 10px;">
                        <?php if ($page['type'] == 1 || $page['type'] == 2) {?>
                            <div style="width: 52%;float: left">
                                <table class="am-table am-table-centered" style="width: 100% !important;border-bottom: solid 1px #cccccc;">
                                    <tbody>
                                    <tr>
                                        <td colspan="2">客服部/日期</td>
                                    </tr>
                                    <?php foreach ($page['detail'] as $key=>$value) {
                                        ?>
                                        <tr>
                                            <td  rowspan="3" style="white-space: normal"><?php echo $page['service_group'][$key]; ?></td>
                                            <td >微信号个数</td>
                                        </tr>
                                        <tr>
                                            <td  >进粉</td>
                                        </tr>
                                        <tr>
                                            <td >产值</td>
                                        </tr>

                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <div style="width:48%;overflow-x: scroll;float: right" class="scroll_div">
                                <table class="am-table border_table" style="width:auto !important;text-align:center;">
                                    <tbody>
                                    <tr>
                                        <?php for($i=0;$i<7;$i++) {
                                            $date = $page['start_date']+$i*24*60*60;
                                            ?>
                                            <td><?php echo date('m-d',$date);?></td>
                                        <?php } ?>
                                    </tr>
                                    <?php foreach ($page['detail'] as $key=>$value) { ?>
                                        <tr>
                                            <?php foreach ($value as $d=>$data) { ?>
                                                <td><?php echo $data['weChat_num']; ?></td>
                                            <?php } ?>
                                        </tr>
                                            <tr>
                                                <?php foreach ($value as $d=>$data) { ?>
                                                    <td><?php echo $data['fans_count']; ?></td>
                                                <?php } ?>
                                            </tr>
                                            <tr>
                                                <?php foreach ($value as $d=>$data) { ?>
                                                    <td><?php echo $data['output']; ?></td>
                                                <?php } ?>
                                            </tr>
                                     <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                        <?php }?>
                    </div>

                    <?php if ($page['type'] == 3 || $page['type'] == 4) {?>
                        <table class="am-table am-table-centered" style="margin-bottom: 0">
                            <tbody>
                            <tr>
                                <td></td>
                                <td>微信号个数</td>
                                <td>计划进粉</td>
                                <td>计划产值</td>
                            </tr>
                            <?php foreach ($page['detail'] as $key=>$value) {
                                ?>
                                <tr>
                                    <td><?php echo $page['service_group'][$value['cs_id']]; ?></td>
                                    <td><?php echo $value['weChat_num']; ?></td>
                                    <td><?php echo $value['fans_plan']; ?></td>
                                    <td><?php echo $value['output_plan']; ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    <?php }?>
                </div>
            </div>
        </div>
    <div class="am-g" style="border: solid 1px #CCCCCC;width: 98%;padding: 15px;margin-top: 20px">
        <?php if ($page['type'] == 1 || $page['type'] == 2) echo $page['week_plan']['mask'];?>
        <?php if ($page['type'] == 3 || $page['type'] == 4) echo $page['week_plan']['remark'];?>
    </div>
        <?php
        $url = $this->createUrl('/mobile/planAudit/auditEdit').'?type='.$page['type'].'&id='.$page['week_plan']['id'];
        ?>
        <div style="margin-top: 20px;width: 80%;margin-left: 10%;margin-bottom: 20px">
            <a class="am-btn am-btn-primary  am-btn-block am-radius" role="button" onclick="through()"> 同意 </a>
            <a  class="am-btn am-btn-primary  am-btn-block am-radius" role="button" style="background: #cccccc" onclick="unThrough()"> 拒绝 </a>
        </div>
    </div>
    <div class="mt10" style="display: none" id="unthrough_div">
        <textarea name="unthrough_msg" id="unthrough_msg"></textarea>
    </div>
</div>
<script>
    var back_url = "<?php echo $this->createUrl('/mobile/planAudit/index');?>";
    function through() {
        var url = "<?php echo $url?>"+'&status=1';
        jQuery.ajax({
            'type': 'GET',
            'url': url,
            'cache': false,
            'async':false,
            'success': function (result) {
                var res = JSON.parse(result);
                var status = res.state;
                if (status == 0) {
                    return_false = 1;
                    alert(res.msgwords);
                    return false;
                }else {
                    alert(res.msgwords);
                    window.location.href = back_url;
                    return true;
                }
            }
        });
    }
    function unThrough() {
        $("#unthrough_msg").val('');
        var url = "<?php echo $url?>"+'&status=2';
        dialog({
            title: '请输入拒绝理由',
            content:$("#unthrough_div"),
            okVal:'确定',
            cancelVal:'取消',
            ok: function () {
                var unthrough_msg = $.trim($("#unthrough_msg").val());
                if (unthrough_msg=='') {
                    alert('请输入拒绝理由！');
                    return false;
                }
                jQuery.ajax({
                    'type': 'GET',
                    'url': url,
                    'cache': false,
                    'async':false,
                    'data': {'unthrough_msg':unthrough_msg},
                    'success': function (result) {
                        var res = JSON.parse(result);
                        var status = res.state;
                        if (status == 0) {
                            return_false = 1;
                            alert(res.msgwords);
                            return false;
                        }else {
                            alert(res.msgwords);
                            window.location.href = back_url;
                            return true;
                        }
                    }
                });
                if (return_false == 1) {
                    return false;
                }else {
                    return true;
                }
            },
            cancel: function () {
            },
            init: function () {
                this.content('对话框内容被扩展方法改变了');
            }
        }).showModal();
    }
</script>
</body>
</html>