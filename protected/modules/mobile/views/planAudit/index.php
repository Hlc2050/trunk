<?php require(dirname(__FILE__) . "/../common/header.php"); ?>
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
        <div class="am-g" style="margin-top: 70px">
            <?php foreach ($page['list'] as $value) {
                $name = getName($value['type']);
                $time = getTime($value['type'],$value['plan_time']);
                $change = '';
                if ($value['update_time'] > $value['through_time'] &&  $value['through_time']!=0) {
                    $change = '(计划变更)';
                }
                $title = $page[$name][$value['relation_id']].'-'.$time.$change.'-排期计划';
                $base_url = $this->createUrl('/mobile/planAudit/audit').'?type='.$value['type'].'&id='.$value['id'].'&title='.$title;
            ?>
                <div class="am-panel am-panel-secondary" style="width: 80%;margin-left: 10%;background-color: #ffffff;font-size: 12px">
                    <div class="am-panel-hd">
                        <a href="<?php echo $base_url;?>" style="display: inline-block;width: 100%">
                            <?php echo $title;?>
                            <span class="am-icon-chevron-right" style="float: right"></span>
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>

    </div>
</div>
<?php
    function getName($type){
        if ($type == 1 || $type == 3) {
            $name = 'users';
        }else {
            $name = 'groups';
        }
        return $name;
    }
    function getTime($type,$time) {
        if ($type == 1 || $type == 2) {
            $year = date('Y',$time);
            $month = date('m',$time);
            $d = date('j',$time);
            $week = ceil($d/7);
            return $date=$year.'年'.$month.'月第'.$week.'周';
        }else {
            $year = date('Y',$time);
            $month = date('m',$time);
            return $date=$year.'年'.$month.'月';
        }

    }
?>
</body>
</html>