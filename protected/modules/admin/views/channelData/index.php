<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<div class="main mhead" xmlns="http://www.w3.org/1999/html">
    <div class="snav">渠道管理 »  渠道数据表	</div>
     
     <div class="mt10">
    <form action="<?php echo $this->createUrl('channelData/index'); ?>">
        <input type="hidden" id="url" value="<?php echo $this->createUrl('infancePay/inputtip'); ?>">
        业务类型：
        <?php
        $businessTypes = Dtable::toArr(BusinessTypes::model()->findAll());
        echo CHtml::dropDownList('business_type', $this->get('business_type'), CHtml::listData($businessTypes, 'bid', 'bname'),
            array(
                'empty' => '请选择',
            )
        );
        ?>&nbsp;
        <select id="search_type" name="search_type">
            <option value="partner_name" <?php echo $this->get('search_type')=='partner_name'?'selected':''; ?>>合作商</option>
            <option value="channel_name" <?php echo $this->get('search_type')=='channel_name'?'selected':''; ?>>渠道名称</option>
            <option value="channel_code" <?php echo $this->get('search_type')=='channel_code'?'selected':''; ?>>渠道编码</option>
        </select>&nbsp;
        <input type="text" id="search_txt" name="search_txt" class="ipt" value="<?php echo $this->get('search_txt'); ?>" >
        <input type="submit" class="but" value="查询"  >
        <div class="mt10">
        </div>
    </form>
    <div class="mt10 clearfix">
        <div class="l">
            <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="添加数据" onclick="location=\''.$this->createUrl('channelData/add?url='.$page['listdata']['url']).'\'" />','auth_tag'=>'channelData_add')); ?>
        </div>
        <div class="r">
            
        </div>
    </div>
</div>
<div class="main mbody">
<form action="<?php echo $this->createUrl('channelData/saveOrder'); ?>" name="form_order" method="post">
<table class="tb fixTh">
    <thead>
        <tr>
            <th width="40">ID</th>
            <th width="100">上线日期</th>
            <th width="100">合作商</th>
            <th width="80">渠道名称</th>
            <th width="80">渠道编码</th>
            <th width="80">业务类型</th>
            <th width="80">市场单价</th>
            <th width="80">推广前平均阅读量</th>
            <th width="80">粉丝数</th>
            <th width="100">预计进粉人数</th>
            <th width="80">预计进粉成本</th>
            <th width="80">阅读量</th>
            <th width="80">图文编码</th>
            <th width="80">修改时间</th>
            <th width="80">操作</th>
        </tr>
    </thead>
    <?php $eidt = $this->check_u_menu(array('auth_tag'=>'channelData_edit'));
          $del = $this->check_u_menu(array('auth_tag'=>'channelData_delete'));
    ?>
   <?php
   foreach($page['listdata']['list'] as $r) {
       $ready = array($r['first'], $r['second'], $r['third'], $r['fourth'], $r['fifth']);
       sort($ready);
       $end = count($ready) - 1;
       unset($ready[0]);
       unset($ready[$end]);
       $avg = array_sum($ready) / count($ready);
       $avg = round($avg, 2);
       $cost = $r['pay_money'];
       $price = $cost/($r['fans'])*10000;
       $price = round($price,2);
       ?>
    <tr>
        <td><?php echo $r['id']?></td>
        <td><?php echo date('Y-m-d',$r['online_date'])?></td>
        <td><?php echo $r['name']?></td>
        <td><?php echo $r['channel_name']?></td>
        <td><?php echo $r['channel_code']?></td>
        <td><?php echo $r['bname']; ?></td>
        <td><?php echo $price?></td>
        <td><?php echo $avg?></td>
        <td><?php echo $r['fans'] ?></td>
        <td><?php echo ceil($avg*$r['add_fans'])?></td>
        <td><?php echo round($cost/ceil($avg*$r['add_fans']),2)?></td>
        <td><?php echo $r['read_num']?></td>
        <td><?php echo $r['article_code']?></td>
        <td><?php echo date('Y-m-d',$r['update_date'])?></td>
        <td>
            <?php if ($eidt) { ?>
                <a href="<?php echo $this->createUrl('channelData/edit').'?id='.$r['id'].'&url='.$page['listdata']['url']; ?>">编辑</a>
            <?php }; ?>
            <?php if ($del) { ?>
                <a href="<?php echo $this->createUrl('channelData/delete').'?ids='.$r['id'].'&url='.$page['listdata']['url']; ?>" onclick="return confirm(\'确定删除吗\')">删除</a>
            <?php }; ?>
        </td>
    </tr>
   <?php
   } ?>

    
</table>
  <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
  <div class="clear"></div>
</form>
</div>

