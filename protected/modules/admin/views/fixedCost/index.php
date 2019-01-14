<?php require(dirname(__FILE__)."/../common/head.php");?>
<style>
    .file {
        position: relative;
        display: inline-block;
        background: #2fa4e7;
        border: 1px solid #2C91CB;
        padding: 4px 12px;
        overflow: hidden;
        color: white;
        text-decoration: none;
        text-indent: 0;
        line-height: 20px;
        margin-left: 4px;
    }
    .file input {
        position: absolute;
        font-size: 100px;
        right: 0;
        top: 0;
        opacity: 0;
    }
    .file:hover{
        color: white;
    }
</style>
<script>
    function show_frame_infos(){

        var d=dialog({
            title:'Excel导入结果',
            content:$(window.frames["frame02"].document).find(".msgbox0009").html(),
            okValue: '确定',
            ok:function(){
                window.location.reload(1);
            }
        });
        d.showModal();
    }

</script>

<div class="main mhead" xmlns="http://www.w3.org/1999/html">
    <div class="snav">财务管理 »  修正成本	</div>
    <div class="mt10">

    <form action="<?php echo $this->createUrl('fixedCost/index'); ?>">
        上线日期：&nbsp;
        <input type="text" id="stat_date_s" class="ipt" style="width: 120px;font-size: 15px;" name="stat_date_s" value="<?php echo $this->get('stat_date_s'); ?>"  placeholder="上线起始日期" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>~
        <input type="text" id="stat_date_e" class="ipt" style="width: 120px;font-size: 15px;" name="stat_date_e" value="<?php echo $this->get('stat_date_e'); ?>"  placeholder="上线结束日期" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>&nbsp;&nbsp;
        修正日期：&nbsp;
        <input type="text" id="fixed_date_s" class="ipt" style="width: 120px;font-size: 15px;" name="fixed_date_s" value="<?php echo $this->get('fixed_date_s'); ?>"  placeholder="修正起始日期" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>~
        <input type="text" id="fixed_date_e" class="ipt" style="width: 120px;font-size: 15px;" name="fixed_date_e" value="<?php echo $this->get('fixed_date_e'); ?>"  placeholder="修正结束日期" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>&nbsp;&nbsp;
        <select name="search_type">
            <option value="partner_name" <?php  if($this->get('search_type')=='partner_name')echo 'selected'; ?>>合作商</option>
            <option value="channel_name" <?php if($this->get('search_type')=='channel_name')echo 'selected'; ?>>渠道名称</option>
            <option value="channel_code" <?php if($this->get('search_type')=='channel_code')echo 'selected'; ?>>渠道编码</option>
        </select>&nbsp;
        <input type="text" id="search_txt" name="search_txt" class="ipt" value="<?php echo $this->get('search_txt'); ?>" >&nbsp;&nbsp;
        业务：&nbsp;
        <?php
        $businessTypes = Dtable::toArr(BusinessTypes::model()->findAll());
        echo CHtml::dropDownList('bs_id', $this->get('bs_id'), CHtml::listData($businessTypes, 'bid', 'bname'),
            array(
                'empty' => '请选择',
            )
        );
        ?>&nbsp;&nbsp;
        <div style="margin-top: 10px">
            微信号：
            <input type="text" name="wechat_id"  class="ipt" value="<?php echo $this->get('wechat_id'); ?>">&nbsp;&nbsp;
            客服部：
            <?php helper::getServiceSelect('csid'); ?>&nbsp;&nbsp;
            商品：
            <?php
            echo $this->get('csid') ? CHtml::dropDownList('goods_id', $this->get('goods_id'), CHtml::listData(CustomerServiceRelation::model()->getGoodsList($this->get('csid')), 'goods_id', 'goods_name'), array('empty' => '全部')) : CHtml::dropDownList('goods_id', $this->get('goods_id'),CHtml::listData(Goods::model()->findAll(), 'id', 'goods_name'), array('empty' =>'全部'))
            ?>&nbsp;&nbsp;
            推广人员：
            <?php
            $promotionStafflist = PromotionStaff::model()->getPromotionStaffList();
            echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($promotionStafflist, 'user_id', 'name'),
                array('empty' => '请选择')
            );
            ?>&nbsp;&nbsp;
            <input type="submit" class="but" value="查询">
        </div>

    </form>
    <div class="mt10 clearfix">
        <div class="l">
            <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="添加修正" onclick="location=\''.$this->createUrl('fixedCost/add').'\'" />','auth_tag'=>'fixedCost_add')); ?>
            <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="删除" onclick="set_some(\''.$this->createUrl('fixedCost/delete').'?ids=[@]\',\'确定删除吗？\');" />','auth_tag'=>'fixedCost_delete')); ?>
            <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="模板下载" onclick="location=\''.$this->createUrl('fixedCost/template').'\'" />','auth_tag'=>'fixedCost_template')); ?>
            <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="列表导出" onclick="location=\''.$this->createUrl('fixedCost/export').'?wechat_id='.$this->get('wechat_id').'&csid='.$this->get('csid').'&goods_id='.$this->get('goods_id').'&user_id='.$this->get('user_id').'&search_type='.$this->get('search_type').'&search_txt='.$this->get('search_txt').'&stat_date_s='.$this->get('stat_date_s').'&stat_date_e='.$this->get('stat_date_e').'&fixed_date_s='.$this->get('fixed_date_s').'&fixed_date_e='.$this->get('fixed_date_e').'\'" />','auth_tag'=>'fixedCost_export')); ?>
        </div>
        <form action="<?php echo $this->createUrl('fixedCost/load'); ?>" target="frame02" method="post"  enctype="multipart/form-data">
            <a href="javascript:;" class="file">选择文件
                <?php $this->check_u_menu(array('code' => '<input type="file"  name="filename"  />', 'auth_tag' => 'fixedCost_load')); ?>
            </a>
            <?php $this->check_u_menu(array('code' => '<input type="submit" name="submit" class="but2" value="导入" />', 'auth_tag' => 'fixedCost_load')); ?>
        </form>
        <div class="r"></div>
    </div>
</div>
<div class="main mbody">
<form>
    <table class="tb fixTh">
        <thead>
        <tr>
            <th width="40"><?php $this->check_u_menu(array('code' => '<a href="javascript:void(0);"  onclick="check_all(\'.cklist\');">反选</a>', 'auth_tag' => 'fixedCost_delete')); ?>&nbsp;</th>
            <th width="100">上线日期</th>
            <th width="80">推广人员</th>
            <th width="80">推广小组</th>
            <th width="80">归属客服部</th>
            <th width="80">商品</th>
            <th width="80">合作商</th>
            <th width="80">渠道名称</th>
            <th width="80">渠道编码</th>
            <th width="70">微信号</th>
            <th width="80">业务</th>
            <th width="80">计费方式</th>
            <th width="100">修正友盟金额</th>
            <th width="100">修正日期</th>
            <th width="80">操作</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>合计</td>
            <td><?php echo round($page['listdata']['fixed_cost'],2);?></td><td>-</td><td>-</td>
        </tr>

        <?php $edit = $this->check_u_menu(array('auth_tag'=>'fixedCost_edit'));
              $del = $this->check_u_menu(array('auth_tag'=>'fixedCost_delete'));
        ?>
        <?php foreach($page['listdata']['list'] as $r){ ?>
            <tr>
                <td>
                    <?php if ($del) { ?>
                        <input type="checkbox" class="cklist" value="<?php echo $r['cid'];  ?>"/>
                    <?php }; ?>
                </td>
                <td><?php echo date('Y-m-d', $r['stat_date']); ?></td>
                <td><?php echo $r['csname_true']; ?></td>
                <td><?php echo $r['linkage_name'] ?></td>
                <td><?php echo $r['cname']; ?></td>
                <td style="max-width: 200px"><?php echo $r['goods_name'] ?></td>
                <td><?php echo $r['partner_name']; ?></td>
                <td><?php echo $r['channel_name'] ?></td>
                <td><?php echo $r['channel_code'] ?></td>
                <td><?php echo $r['wechat_id'] ?></td>
                <td><?php echo $r['bname'] ?></td>
                <td><?php echo vars::get_field_str('charging_type', $r['charging_type']) ?></td>
                <td><?php echo round($r['fixed_cost'],2) ?></td>
                <td><?php echo date('Y-m-d', $r['fixed_date']) ?></td>
                <td>
                    <?php if ($edit) { ?>
                        <a href="<?php echo $this->createUrl('fixedCost/edit').'?id='.$r['cid'].'&url='.$page['listdata']['url']; ?>">编辑</a>
                    <?php }; ?>
                    <?php if ($del) { ?>
                        <a href="<?php echo $this->createUrl('fixedCost/delete').'?ids='.$r['cid']; ?>" onclick="return confirm('确定删除吗')">删除</a>
                    <?php }; ?>
                </td>
            </tr>
            <?php
        } ?>
        </tbody>
    </table>
  <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
  <div class="clear"></div>
</form>
</div>
    <div class="float-simage-box" style="position: absolute;">
    </div>

    <script src="/static/lib/jquery.jcrop/jquery.jcrop.min.js"></script>
    <link rel="stylesheet" href="/static/lib/jquery.jcrop/jquery.Jcrop.css">
    <div id="framebox" style="display: none;">
        <iframe name="frame02" src="" id="frame02"></iframe>
    </div>



