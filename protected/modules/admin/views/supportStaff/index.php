<?php require(dirname(__FILE__)."/../common/head.php"); ?>
    <div class="main mhead">
        <div class="snav">基础类别 » 支持人员管理	</div>
        <div  class="mt10">
            <select id="search_type">
                <option value="keys" <?php echo $this->get('search_type')=='keys'?'selected':''; ?>>支持人员名称</option>
                <option value="id" <?php echo $this->get('search_type')=='id'?'selected':''; ?>>ID</option>
            </select>&nbsp;
            <input type="text" id="search_txt" class="ipt"  value="<?php echo isset($_GET['search_txt'])?$_GET['search_txt']:''; ?>" >&nbsp;<input type="button" class="but" value="查询" onclick="window.location='<?php echo $this->createUrl('supportStaff/index');?>?search_txt='+$('#search_txt').val()+'&search_type='+$('#search_type').val();" >
        </div>
        <div class="mt10 clearfix">
            <div class="l">
                <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="添加支持人员" onclick="location=\''.$this->createUrl('supportStaff/add').'\'" />','auth_tag'=>'supportStaff_add')); ?>&nbsp;
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
                    <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('supportStaff/index') . '?p=' . $_GET['p'] . '', 'field_cn' => 'ID', 'field' => 'id')); ?></th>
                    <th align='center'><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('supportStaff/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '用户ID', 'field' => 'user_id')); ?></th>
                    <th>支持用户名称</th>
                    <th>支持小组</th>
                    <th><?php echo helper::field_paixu(array('url' => '' . $this->createUrl('supportStaff/index') . '?p=' . $_GET['p'] . '', 'field_cn' => '最近修改时间', 'field' => 'update_time')); ?></th>
                    <th>操作</th>
                </tr>
                </thead>
                <?php
                foreach($page['listdata']['list'] as $val){
                    ?>
                    <tr>
                        <td><?php echo $val['id']; ?></td>
                        <td><?php echo $val['user_id']; ?></td>
                        <td><?php echo $val['name']; ?></td>
                        <td><?php echo $val['support_group_name']; ?></td>
                        <td><?php echo $val['update_time']; ?></td>
                        <td>
                            <?php $this->check_u_menu(array('code'=>'<a href="'.$this->createUrl('supportStaff/edit',array('id'=>$val['id'])).'">修改</a>','auth_tag'=>'supportStaff_edit')); ?>&nbsp;&nbsp;
                            <?php $this->check_u_menu(array('code'=>'<a href="'.$this->createUrl('supportStaff/delete',array('id'=>$val['id'],'user_id'=> $val['user_id'])).'"  onclick="return confirm(\'确定删除吗\')">删除</a>','auth_tag'=>'supportStaff_delete')); ?>
                        </td>
                    </tr>
                    <?php
                } ?>
            </table>
            <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
            <div class="clear"></div>
        </form>
    </div>


