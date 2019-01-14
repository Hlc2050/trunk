<?php require(dirname(__FILE__)."/../common/head.php"); ?>
    <div class="main mhead">
        <div class="snav">基础类别 » 业务类型管理	</div>

        <div class="mt10 clearfix">
            <div class="l">
                <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="新增" onclick="location=\''.$this->createUrl('businessTypesManage/add').'\'" />','auth_tag'=>'businessTypes_add')); ?>
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
                    <th align='center'><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('businessTypesManage/index').'?p='.$_GET['p'].'','field_cn'=>'ID','field'=>'id')); ?></th>
                    <th>业务类型</th>
                    <th>计费方式</th>
                    <th>操作人员</th>
                    <th>操作</th>
                </tr>
                </thead>
                <?php
                foreach($page['listdata']['list'] as $r){
                    ?>
                    <tr>
                        <td><?php echo $r['bid']?></td>
                        <td><?php echo $r['bname']?></td>
                        <td><?php echo $r['chargingTypes']?></td>
                        <td><?php echo $r['csname_true']?></td>
                        <td>
                            <?php $this->check_u_menu(array('code'=>'<a href="'.$this->createUrl('businessTypesManage/edit').'?bid='.$r['bid'].'&p='.$_GET['p'].'">修改</a>','auth_tag'=>'businessTypes_edit')); ?>
                            <?php $this->check_u_menu(array('code'=>'<a href="'.$this->createUrl('businessTypesManage/delete').'?bid='.$r['bid'].'" onclick="return confirm(\'确定删除吗\')">删除</a>','auth_tag'=>'businessTypes_delete')); ?>
                        </td>
                    </tr>
                    <?php
                } ?>
            </table>
            <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
            <div class="clear"></div>
        </form>
    </div>


