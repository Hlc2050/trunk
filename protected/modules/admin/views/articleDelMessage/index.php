<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<script>
   function exportList(){
         var data = $("#serFrom").serialize();
         var url = "<?php echo $this->createUrl('articleDelMessage/export');?>";
         window.location.href=url+'?'+data;
    }
</script>
<div class="main mhead">
    <div class="snav">推广管理 » 文案删除统计</div>

    <div class="mt10">
        <form action="<?php echo $this->createUrl('articleDelMessage/index'); ?>" id="serFrom">
            <select id="search_type" name="search_type" style="width: 85px">
                <option
                    value="article_title" <?php echo $this->get('search_type') == 'article_title' ? 'selected' : ''; ?>>
                    文案标题
                </option>
                <option
                    value="article_code" <?php echo $this->get('search_type') == 'article_code' ? 'selected' : ''; ?>>
                    文案编码
                </option>
            </select>&nbsp;
            <input style="width:120px;" type="text" id="search_txt" name="search_txt" class="ipt"
                   value="<?php echo $this->get('search_txt'); ?>">&nbsp;
            推广人员： <?php
            $promotionStafflist = AdminUser::model()->get_all_user();
            echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($promotionStafflist, 'csno', 'csname_true'),
                array('empty' => '全部')
            );
            ?>&nbsp;
            添加信息时间：
            <input style="width:180px;" type="text" class="ipt" id="start_time" name="start_time"
                   value="<?php echo $this->get('start_time') ?>"
                   onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00'})"/>-
            <input style="width:180px;" type="text" class="ipt" id="end_time" name="end_time"
                   value="<?php echo $this->get('end_time') ?>"
                   onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00'})"/>&nbsp;
            文章类型：
            <?php
            $article_type = vars::$fields['article_types'];
            echo CHtml::dropDownList('article_type', $this->get('article_type'), CHtml::listData($article_type, 'value', 'txt'),
                array('empty' => '全部')
            );
            ?>&nbsp;
            <input type="submit" class="but" value="查询">
        </form>
        <div class="mt10 clearfix">
            <div class="l">
                <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="删除" onclick="set_some(\'' . $this->createUrl('articleDelMessage/delete') . '?ids=[@]\',\'确定删除吗？\');" />', 'auth_tag' => 'articleDelMessage_delete')); ?>
                <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="导出" onclick="exportList()" />', 'auth_tag' => 'articleDelMessage_export')); ?>
            </div>
            <div class="r"></div>
        </div>
    </div>
    <div class="main mbody">
        <table class="tb fixTh">
            <thead>
            <tr>
                <th width="50" class="cklist"><a href="javascript:void(0);" class="cklist"
                                                 onclick="check_all('.cklist');">反选</a></th>
                <th width="40">ID</th>
                <th width="100">上线日期</th>
                <th width="80">合作商</th>
                <th width="80">渠道</th>
                <th width="80">业务类型</th>
                <th width="80">文案编码</th>
                <th width="80">文案标题</th>
                <th width="80">文案类型</th>
                <th width=80>商品类型</th>
                <th width=80>推广人员</th>
                <th width=100>文案备注</th>
                <th width=100>删除日期</th>
                <th width=80>存在时长</th>
                <th width=100>添加时间</th>
                <th width=100>信息备注</th>
                <th width=60>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php $edit = $this->check_u_menu(array('auth_tag' => 'articleDelMessage_edit')); ?>
            <?php foreach ($page['listdata']['list'] as $r) { ?>
                <tr>
                    <td><input type="checkbox" class="cklist" value="<?php echo $r['id']; ?>"/></td>
                    <td><?php echo $r['id']; ?></td>
                    <td><?php echo date('Y-m-d H:i', $r['online_date']); ?></td>
                    <td><?php echo $r['name']; ?></td>
                    <td><?php echo $r['channel_name']; ?></td>
                    <td><?php echo $r['bname']; ?></td>
                    <td><?php echo $r['article_code']; ?></td>
                    <td><?php echo $r['article_title']; ?></td>
                    <td><?php echo vars::get_field_str('article_types', $r['article_type']) ?></td>
                    <td><?php echo $r['linkage_name']; ?></td>
                    <td><?php echo $r['csname_true']; ?></td>
                    <td><a title="<?php echo $r['article_info']; ?>" href="#"
                           style="color: black;"><?php echo helper::cut_str($r['article_info'], 10); ?></a></td>
                    <td><?php echo date('Y-m-d H:i', $r['del_date']); ?></td>
                    <td><?php echo helper::getTimeDiff($r['online_date'], $r['del_date']) ?></td>
                    <td><?php echo date('Y-m-d H:i', $r['create_time']); ?></td>
                    <td><a title="<?php echo $r['mark']; ?>" href="#"
                           style="color: black;"><?php echo helper::cut_str($r['mark'], 10); ?></a>
                    </td>
                    <td>
                        <?php if ($edit) { ?>
                            <input value="编辑" type="button" class="but1" onclick="return dialog_frame(this,350,400,1)"  href="<?php echo $this->createUrl('articleDelMessage/edit?id=' . $r['id'] . '&url=' . $page['listdata']['url']); ?>"/>
                        <?php }; ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>

