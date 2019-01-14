<?php require(dirname(__FILE__)."/../common/head.php"); ?>
    <div class="main mhead">
        <div class="snav">基础类别 » 计划表	</div>

        <div class="mt10 clearfix">
            <div class="l">
                <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="新增" onclick="location=\''.$this->createUrl('schedule/add').'\'" />','auth_tag'=>'schedule_add')); ?>
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
                    <th align='center'><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('schedule/index').'?p='.$_GET['p'].'','field_cn'=>'ID','field'=>'id')); ?></th>
                    <th align='center'><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('schedule/index').'?p='.$_GET['p'].'','field_cn'=>'日期','field'=>'target_time')); ?></th>
                    <th>对象</th>
                    <th><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('schedule/index').'?p='.$_GET['p'].'','field_cn'=>'计划A','field'=>'target_a')); ?></th>
                    <th><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('schedule/index').'?p='.$_GET['p'].'','field_cn'=>'计划B','field'=>'target_b')); ?></th>
                    <th><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('schedule/index').'?p='.$_GET['p'].'','field_cn'=>'计划C','field'=>'target_c')); ?></th>
                    <th  class="alignleft">操作</th>
                </tr>
                </thead>
                <?php
                foreach($page['listdata']['list'] as $r){
                    ?>
                    <tr>
                        <td><?php echo $r['id']; ?></td>
                        <td><?php echo date('Y-m',$r['target_time']); ?></td>
                        <td><?php echo $r['sname']?></td>
                        <td><?php echo $r['target_type']==1?$r['target_a'].'%':$r['target_a'];?></td>
                        <td><?php echo $r['target_type']==1?$r['target_b'].'%':$r['target_b'];;?></td>
                        <td><?php echo $r['target_type']==1?$r['target_c'].'%':$r['target_c'];;?></td>
                        <td class="alignleft">
                            <?php $this->check_u_menu(array('code'=>'<a href="'.$this->createUrl('schedule/edit',array('id'=>$r['id'])).'">修改</a>','auth_tag'=>'schedule_edit')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php $this->check_u_menu(array('code'=>'<a href="'.$this->createUrl('schedule/delete',array('id'=>$r['id'])).'"  onclick="return confirm(\'确定删除吗\')">删除</a>','auth_tag'=>'schedule_delete')); ?>
                        </td>
                    </tr>
                    <?php
                } ?>
            </table>
            <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
            <div class="clear"></div>
        </form>
    </div>


