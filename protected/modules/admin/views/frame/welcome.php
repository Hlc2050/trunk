<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
    <div class="main mhead">
        <div class="snav">系统 »
            欢迎页
        </div>
    </div>
    <div class="main mbody">
        <div class="welcome_box">
            <div><h1>
                    欢迎使用<b><?php echo Yii::app()->params['management']['name'] ? Yii::app()->params['management']['name'] : Yii::app()->params['basic']['sitename']; ?>
                    </b>。</h1></div>
        </div>
        <div class="list08 clearfix">
            <?php if ($this->check_u_menu(array('auth_tag' => 'frame_firstIndex'))) { ?>
                <label>昨日统计</label>
                <div class="mt10 clearfix">
                    <form action="<?php echo $this->createUrl('frame/welcome'); ?>">
                        合作商：
                        <input style="width: 100px" type="text" name="partner" class="ipt"
                               value="<?php echo $this->get('partner'); ?>">&nbsp;
                        渠道编码：
                        <input style="width: 100px" type="text" name="chlId" class="ipt"
                               value="<?php echo $this->get('chlId'); ?>">&nbsp;
                        渠道名称：
                        <input style="width: 100px" type="text" name="chlName" class="ipt"
                               value="<?php echo $this->get('chlName'); ?>">&nbsp;
                        IP<=
                        <input style="width: 100px" type="text" name="ip" class="ipt"
                               value="<?php echo $this->get('ip'); ?>">&nbsp;
                        推广人员： <?php
                        $promotionStafflist = PromotionStaff::model()->getPromotionStaffList();
                        echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($promotionStafflist, 'user_id', 'name'),
                            array('empty' => '请选择')
                        );
                        ?>&nbsp;
                        类型：<?php
                        $promotion_types = vars::$fields['promotion_types'];
                        echo CHtml::dropDownList('promotion_type', $this->get('promotion_type'), CHtml::listData($promotion_types, 'value', 'txt'),
                            array('empty' => '全部')
                        );
                        ?>&nbsp;

                        <input type="submit" class="but" value="查询">
                    </form>
                </div>
                <div class="mt10">
                    <form>
                        <table class="tb fixTh" style="width: 800px;">
                            <thead>
                            <tr>
                                <th width="80">推广ID</th>
                                <th width="80">类型</th>
                                <th width="100">合作商</th>
                                <th width="80">渠道编码</th>
                                <th width="100">渠道名称</th>
                                <th width="100">推广人员</th>
                                <th width="80">IP</th>
                                <th width="80">操作</th>
                            </tr>
                            </thead>

                            <?php
                            foreach ($page['listdata']['list'] as $r) { ?>
                                <tr>
                                    <td><?php echo $r['id'] ?></td>
                                    <td><?php echo vars::get_field_str('promotion_types', $r['promotion_type']); ?></td>
                                    <td><?php echo $r['partner_name']; ?></td>
                                    <td><?php echo $r['channel_code']; ?></td>
                                    <td><?php echo $r['channel_name']; ?></td>
                                    <td><?php echo $r['csname_true'] ?></td>
                                    <td><?php echo $r['ip'] ?></td>
                                    <td>
                                        <?php $this->check_u_menu(array('code' => '<a onclick="return dialog_frame(this,1000,580,false)" href="' . $this->createUrl('statCnzz/index') . '?id=' . $r['id'] . '">详情</a>', 'auth_tag' => 'statCnzz_index')); ?>
                                        <?php $this->check_u_menu(array('code' => '<a href="' . $this->createUrl('frame/down') . '?promotion_id=' . $r['id'] . '" onclick="return confirm(\'确定下线吗\')">下线</a>', 'auth_tag' => 'frame_down')); ?>
                                    </td>
                                </tr>
                                <?php
                            } ?>


                        </table>
                        <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
                        <div class="clear"></div>
                    </form>
                </div>
            <?php } ?>

        </div>


    </div>

