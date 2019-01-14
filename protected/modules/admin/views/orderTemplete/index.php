<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
    <div class="main mhead">
        <div class="snav">直接下单管理 » 下单设置管理</div>

        <div class="mt10">
            下单标题：
            <input type="text" id="order_title" name="order_title" class="ipt" value="<?php echo $this->get('order_title'); ?>">&nbsp;
            <input type="button" class="but" value="查询" onclick="window.location='<?php echo $this->createUrl('orderTemplete/index'); ?>?order_title='+$('#order_title').val()">
        </div>
        <div class="mt10 clearfix">
            <div class="l">
                <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="添加下单模板" onclick="location=\'' . $this->createUrl('orderTemplete/add?url=' . $page['listdata']['url']) . '\'" />', 'auth_tag' => 'orderTemplete_add')); ?>
            </div>
            <div class="r">
            </div>
        </div>
    </div>
    <div class="main mbody">
        <table class="tb fixTh" style="width: 800px">
            <thead>
            <tr>
                <th style="width: 10%" align='center'>ID</th>
                <th style="width: 30%" align='center'>下单标题</th>
                <th style="width: 15%" align='center'>操作</th>
            </tr>
            </thead>
            <tbody>
            <!-- 判断权限-->
            <?php $edit = $this->check_u_menu(array('auth_tag'=>'orderTemplete_edit')); ?>
            <?php foreach ($page['listdata']['list'] as $k=>$v){?>
            <tr>
                <td><?php echo $v['id'];?></td>
                <td><?php echo $v['order_title'];?></td>
                <td>
                    <?php if ($edit) { ?>
                        <a href="<?php echo $this->createUrl('orderTemplete/edit') .'?id='.$v['id'].'&url='.$page['listdata']['url']; ?>">修改</a>
                    <?php }; ?>
                </td>
            </tr>
            <?php }?>
            </tbody>
        </table>
        <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
        <div class="clear"></div>
    </div>
