<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
    <div class="main mhead">
        <div class="snav">渠道管理 » 微信号小组管理</div>
        <div class="mt10">
            <form action="<?php echo $this->createUrl('weChatGroup/index'); ?>">

                <select name="search_type">
                    <option value="keys" <?php echo $this->get('search_type') == 'keys' ? 'selected' : ''; ?>>微信小组名称
                    </option>
                    <option value="wechat_id" <?php echo $this->get('search_type') == 'wechat_id' ? 'selected' : ''; ?>>微信号ID
                    </option>
                    <option value="id" <?php echo $this->get('search_type') == 'id' ? 'selected' : ''; ?>>ID</option>
                </select>&nbsp;
                <input type="text" name="search_txt" class="ipt"
                       value="<?php echo isset($_GET['search_txt']) ? $_GET['search_txt'] : ''; ?>">&nbsp;
                操作用户：
                <?php
                $userArr = $this->toArr(AdminUser::model()->findAll());
                echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($userArr, 'csno', 'csname_true'),
                    array('empty' => '全部')
                );
                ?>&nbsp;
                业务类型：
                <?php
                $businessTypes = Dtable::toArr(BusinessTypes::model()->findAll());
                echo CHtml::dropDownList('bs_id', $this->get('bs_id'), CHtml::listData($businessTypes, 'bid', 'bname'),
                    array(
                        'empty' => '全部',
                    )
                );
                ?>&nbsp;
                <input type="submit" class="but" value="查询">

        </div>
        <div class="mt10 clearfix">
            <div class="l">
                <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="添加微信号小组" onclick="location=\'' . $this->createUrl('weChatGroup/add') . '\'" />', 'auth_tag' => 'weChatGroup_add')); ?>
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
                    <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('weChatGroup/index') . '?p=' . $_GET['p'] . '', 'field_cn' => 'ID', 'field' => 'id')); ?></th>
                    <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('weChatGroup/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '微信号小组名称', 'field' => 'wechat_group_name')); ?></th>
                    <th>微信号ID</th>
                    <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('weChatGroup/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '业务', 'field' => 'business_type')); ?></th>
                    <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('weChatGroup/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '计费方式', 'field' => 'charging_type')); ?></th>

                    <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('weChatGroup/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '最近修改时间', 'field' => 'update_time')); ?></th>
                    <th>操作用户</th>
                    <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('weChatGroup/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '状态', 'field' => 'status')); ?></th>
                    <th>操作</th>
                </tr>
                </thead>
                <?php $edit =  $this->check_u_menu(array('auth_tag' => 'weChatGroup_edit'));
                      $del =  $this->check_u_menu(array('auth_tag' => 'weChatGroup_delete'));
                ?>
                <?php foreach ($page['listdata']['list'] as $val) { ?>
                    <tr>
                        <td><?php echo $val['id']; ?></td>
                        <td><?php echo $val['wechat_group_name']; ?></td>
                        <td style="max-width:200px;"><span title="<?php echo $val['wechatList'];?>"><?php echo helper::cut_str($val['wechatList'],45); ?></span></td>
                        <td><?php echo $val['bname']; ?></td>
                        <td><?php echo $val['cname']; ?></td>
                        <td><?php echo $val['update_time']; ?></td>
                        <td><?php echo $val['operator']; ?></td>
                        <td><?php echo $val['status']; ?></td>
                        <td>
                            <?php if ($edit) { ?>
                                <a href="<?php echo $this->createUrl('weChatGroup/edit?id='.$val['id'].'&bid='.$val['business_type'].'&url='.$page['listdata']['url']);  ?>">修改</a>&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php }; ?>
                            <?php if ($edit) { ?>
                                <a href="<?php echo $this->createUrl('weChatGroup/delete?id='.$val['id'].'&url='.$page['listdata']['url']); ?>"  onclick="return confirm(\'确定删除吗\')">删除</a>
                            <?php }; ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
            <div class="clear"></div>
        </form>
        <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>

    </div>