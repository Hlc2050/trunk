<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">系统管理 »  参数管理	</div>
     
     <div class="mt10">

 
    <div class="mt10 clearfix">
        <div class="l">
           <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="修改排序" onclick="document.form_order.submit();" />','auth_tag'=>'params_saveOrder')); ?>
           <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="删除选中" onclick="set_some(\''.$this->createUrl('params/delete').'?ids=[@]\',\'确定删除吗？\');" />','auth_tag'=>'params_delete')); ?>
           <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="参数分组管理" onclick="return dialog_frame(this,800,700,1)" href="'.$this->createUrl('paramsGroup/index').'" />','auth_tag'=>'paramsGroup_index')); ?>
		   <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="添加参数" onclick="return dialog_frame(this,500,500,1)" href="'.$this->createUrl('params/add').'?group_id='.$this->get('group_id').'&search_type='.$this->get('search_type').'&search_txt='.$this->get('search_txt').'" />','auth_tag'=>'params_add')); ?>
        </div>
        <div class="r">
            
        </div>
    </div>
</div>
<div class="main mbody">
<form action="<?php echo $this->createUrl('params/saveOrder'); ?>" name="form_order" method="post">

<div class="tab_box">
    <?php foreach($page['params_groups'] as $k=>$r){if($r['isshow']==0)continue; ?>
    <a href="<?php $this->createUrl('params/index'); ?>?group_id=<?php echo $r['id']; ?>" <?php if($this->get('group_id')==$r['id'])echo 'class="current"'; ?>><?php echo $r['group_name']; ?></a>
    <?php }?>

</div>



<table class="tb fixTh">
    <thead>
    <tr>
        <th width="100"><a href="javascript:void(0);" onclick="check_all('.cklist');">全选/反选</a></th>
        <th width="80">排序</th>
        <th width="60" >参数属性</th>
        <th width="100" >参数名</th>

        <th width="300"  class="alignleft">参数值</th>
        <th width=200>操作</th>
    </tr>
    </thead>
   <?php 
   foreach($page['listdata']['list'] as $r){
       if($r['param_name'] != 'is_redis'){
   ?>
    <tr>   
        <td><input type="checkbox" class="cklist" value="<?php echo $r['id']; ?>" /></td>
        <td><input type="text" size="2" name="listorders[<?php echo $r['id']; ?>]" value="<?php echo $r['displayorder']; ?>" /></td>

        <td><?php echo $r['param_name']; ?> </td>
        <td><?php echo $r['param_desc']; ?> </td>
        <td class="alignleft" style="font-size:14px;font-weight:bold;"><?php echo $r['param_value']; ?></td>
        <td>
            <?php $this->check_u_menu(array('code'=>'<a onclick="return dialog_frame(this,500,500,1)" href="'.$this->createUrl('params/edit').'?id='.$r['id'].'&p='.$_GET['p'].'&search_type='.$this->get('search_type').'&search_txt='.$this->get('search_txt').'">修改</a>','auth_tag'=>'params_edit')); ?>
        </td>

    </tr>
   <?php 
       }
   } ?> 
     
    
</table>
</form>
</div>

<div class="float-simage-box" style="position: absolute;">
</div>


<script src="/static/lib/jquery.jcrop/jquery.jcrop.min.js"></script>
<link rel="stylesheet" href="/static/lib/jquery.jcrop/jquery.Jcrop.css">
<script>
$(".slider-simage").hover(
	function(){
		$(".float-simage-box").show();
		var imgurl=$(this).attr("src");
		$(".float-simage-box").html('<img src="'+imgurl+'" width=150 />');
		var left=$(this).offset().left-150;
		var top=$(this).offset().top;
		$(".float-simage-box").css({"left":left+'px',"top":top+'px'});
	},
	function(){
		$(".float-simage-box").hide();
	}
)
//封面快速裁剪
$(".slider-simage").click(function(){
	var img=$(this).attr("src");
	var id=$(this).attr("data-id");
	var table="params_list";
	var idField='params_id';
	var imgField='params_img';
	info_cover_crop(table,id,idField,img,imgField);
})



</script>

<?php require(dirname(__FILE__)."/../common/foot.php"); ?>
