<?php require(dirname(__FILE__) . "/../common/head.php"); ?>

<form method="post" action="<?php echo $this->createUrl('planMonth/index') ?>">
    <input name="group_id" value="3" hidden>
    <table class="tb fixTh" style="width: 750px;">
        <tr style="height: 10px;"></tr>
        <tr>
            <th colspan="5">
                <input type="submit" style="float: right;" class="but2" value="搜索">
                <input name="title" placeholder="按标题搜索" type="text" style="float: right;margin-right: 5px;" class="ipt">
            </th>
        </tr>
        <tr style="height: 10px;"></tr>
        <tr>
            <th>计划类型</th>
            <th>标题</th>
            <th>操作说明</th>
            <th>提交日期</th>
            <th>操作人</th>
        </tr>
         <?php foreach ($log['listdata']['list'] as $val){?>
             <tr>
                 <td><?php echo $val['plan_type']==1?"个人计划":"组计划"; ?></td>
                 <td><?php echo $val['title'] ?></td>
                 <td><?php echo $val['mask'] ?></td>
                 <td><?php echo date('Y-m-d H:i:s',$val['add_time']) ?></td>
                 <td><?php echo AdminUser::model()->getUserNameByPK($val['user_id']) ?></td>
             </tr>
         <?php } ?>
</form>
