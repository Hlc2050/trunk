<?php require(dirname(__FILE__)."/../common/head.php"); ?>
    <div class="main mhead">
        <div class="snav">域名管理 »  微信通知管理	</div>

        <div  class="mt10">
            <select id="search_type">
                <option value="keys" <?php echo $this->get('search_type')=='keys'?'selected':''; ?>>姓名</option>
                <option value="id" <?php echo $this->get('search_type')=='id'?'selected':''; ?>>ID</option>
            </select>&nbsp;
                <input type="text" id="search_txt" class="ipt"  value="<?php echo isset($_GET['search_txt'])?$_GET['search_txt']:''; ?>" >&nbsp;
            <input type="button" class="but" value="查询" onclick="window.location='<?php echo $this->createUrl('domainNoticeManage/index');?>?search_txt='+$('#search_txt').val()+'&search_type='+$('#search_type').val();" >
        </div>
        <div class="mt10 clearfix">
            <div class="l">
                <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="添加人员" onclick="location=\''.$this->createUrl('domainNoticeManage/add').'\'" />','auth_tag'=>'domainNoticeManage_add')); ?>
            </div>
            <div class="r">

            </div>
        </div>
    </div>
    <div class="main mbody">
        <form>
            <table class="tb fixTh" style="width:800px;">
                <thead>
                <tr>
                    <th align='center'><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('domainNoticeManage/index').'?p='.$_GET['p'].'','field_cn'=>'ID','field'=>'id')); ?></th>
                    <th><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('domainNoticeManage/index').'?p='.$_GET['p'].'','field_cn'=>'姓名','field'=>'name')); ?></th>
                    <th><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('domainNoticeManage/index').'?p='.$_GET['p'].'','field_cn'=>'openid','field'=>'openid')); ?></th>
                    <th><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('domainNoticeManage/index').'?p='.$_GET['p'].'','field_cn'=>'修改时间','field'=>'update_time')); ?></th>
                    <th  class="alignleft">操作</th>
                </tr>
                </thead>
                <?php $edit = $this->check_u_menu(array('auth_tag'=>'domainNoticeManage_edit'));
                       $del = $this->check_u_menu(array('auth_tag'=>'domainNoticeManage_delete'));
                ?>
                <?php foreach($page['listdata']['list'] as $r){?>
                    <tr>
                        <td><?php echo $r['id']; ?></td>
                        <td><?php echo $r['name']; ?></td>
                        <td><?php echo $r['openid']; ?></td>
                        <td><?php echo date('Y-m-d',$r['update_time']); ?></td>
                        <td class="alignleft">
                            <?php if ($edit) { ?>
                                <a href="<?php echo $this->createUrl('domainNoticeManage/edit',array('id'=>$r['id'])); ?>">修改</a>&nbsp;&nbsp;
                            <?php }; ?>
                            <?php if ($del) { ?>
                                <a href="<?php echo $this->createUrl('domainNoticeManage/delete',array('id'=>$r['id'])); ?>" onclick="return confirm('确定删除吗')">修改</a>&nbsp;&nbsp;
                            <?php }; ?>
                        </td>
                    </tr>
                    <?php } ?>
            </table>
            <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
            <div class="clear"></div>
        </form>
    </div>
