<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav"><span style="font-size: x-large;font-weight: bold; color: dimgrey">小程序列表</span></div>
    <div class="mt10 clearfix">
        <div class="l" id="container">
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="新增小程序" onclick="location=\'' . $this->createUrl('miniAppsManage/add') . '\'" />', 'auth_tag' => 'miniAppsManage_add')); ?>
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
                <th align='center'>ID</th>
                <th align='center'>小程序名称</th>
                <th align='center'>创建时间</th>
                <th align='center'>关联公众号</th>
                <th align='center'>微信号小组</th>
                <th align='center'>客服点击次数</th>
                <th align='center'>客服访问统计</th>
                <th align='center'>小程序接口</th>
                <th align='center'>点击接口</th>
                <th align='center'>状态</th>
                <th align='center'>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($page['listdata']['list'] as $k => $r) { ?>
                <tr>
                    <td><?php echo $r['id']; ?></td>
                    <td><?php echo $r['app_name']; ?></td>
                    <td><?php echo date('Y-m-d H:i:s', $r['create_time']); ?></td>
                    <td><?php echo $r['official_accounts']; ?></td>
                    <td><?php echo $r['wechat_group_name']; ?></td>
                    <td><?php echo $r['click_num']; ?></td>
                    <td>
                        <a href="<?php echo $this->createUrl('miniAppsManage/clicksStatTable?url='.$page['listdata']['url'].'&id='.$r['id']) ?>">点击进入</a>
                    </td>
                    <td>
                        <?php
                        $api_url=Yii::app()->params['miniApps']['api_url'].'/miniProgramsApi/getInfo?id='.$r['id'];
                        $clicks_url=Yii::app()->params['miniApps']['api_url'].'/miniProgramsApi/clicks?id='.$r['id'];
                        ?>
                        <a href="#"
                           onclick="dialog({title:'小程序接口',content:$(this).attr('data-clipboard-text')}).showModal();"
                           data-clipboard-text="<?php echo $api_url; ?>">点击查看
                        </a>
                    </td>
                    <td>
                        <?php
                        $api_url=Yii::app()->params['miniApps']['api_url'].'/miniProgramsApi/getInfo?id='.$r['id'];
                        ?>
                        <a href="#"
                           onclick="dialog({title:'点击统计接口',content:$(this).attr('data-clipboard-text')}).showModal();"
                           data-clipboard-text="<?php echo $clicks_url; ?>">点击查看
                        </a>
                    </td>
                    <td> <?php echo vars::get_field_str('miniApps_status', $r['status']); ?></td>
                    <td>
                        <?php $this->check_u_menu(array('code' => '<a href="' . $this->createUrl('miniAppsManage/edit?id=' . $r['id'] . '&url=' . $page['listdata']['url']) . '">编辑</a>', 'auth_tag' => 'miniAppsManage_edit')); ?>
                        &nbsp;
                        <?php $this->check_u_menu(array('code' => '<a href="' . $this->createUrl('miniAppsManage/delete?id=' . $r['id'] . '&url=' . $page['listdata']['url']) . '"  onclick="return confirm(\'确认删除吗\')">删除</a>', 'auth_tag' => 'miniAppsManage_delete')); ?>
                        &nbsp;
                        <?php $this->check_u_menu(array('code' => '<a href="' . $this->createUrl('miniAppsManage/content?id=' . $r['id'] . '&url=' . $page['listdata']['url']) . '">编辑导航内容</a>', 'auth_tag' => 'miniAppsManage_content')); ?>
                        &nbsp;
                        <?php
                        if ($r['status'] != 0) {
                            if($r['status'] == 2)
                                $this->check_u_menu(array('code' => '<a href="' . $this->createUrl('miniAppsManage/status?id=' . $r['id'] . '&status=2&url=' . $page['listdata']['url']) . '">下线</a>', 'auth_tag' => 'miniAppsManage_status'));
                            else
                                $this->check_u_menu(array('code' => '<a href="' . $this->createUrl('miniAppsManage/status?id=' . $r['id'] . '&status='.$r['status'].'&url=' . $page['listdata']['url']) . '">上线</a>', 'auth_tag' => 'miniAppsManage_status'));

                        }
                        ?>

                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
    </form>
</div>


