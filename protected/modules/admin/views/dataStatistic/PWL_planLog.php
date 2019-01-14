<form action="<?php echo $this->createUrl('domainList/saveOrder'); ?>" name="form_order" method="post">
        <table class="tb fixTh">
            <thead>
            <tr>
                <th width="40">计划类型</th>
                <th width="100">标题</th>
                <th width="40">操作说明</th>
                <th width="100">提交日期</th>
                <th width="80">操作人</th>
            </tr>
            </thead>

            <?php
            foreach ($page['info']['listdata']['list'] as $r) {
                ?>
                <tr>
                    <td><?php echo $r['plan_type'] == 1 ? '个人计划' : '组计划'; ?></td>
                    <td><?php echo $r['title'] ?>-排期计划</td>
                    <td><?php echo $r['mask']; ?></td>
                    <td><?php echo date('Y-m-d H:i:s', $r['add_time']); ?></td>
                    <td><?php echo $page['user_name'][$r['user_id']]; ?></td>
                </tr>
                <?php
            } ?>

        </table>
        <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
        <div class="clear"></div>
    </form>


