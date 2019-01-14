<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<script>
$(document).ready(function(){
	$("#linkage_box").treeview({
		control: "#treecontrol",
		persist: "cookie",
		cookieId: "treeview-black_c"
	});
});
</script>
<div class="main mhead">
    <div class="snav">基础类别 »  类别配置 </div> 
    <div class="mt10 clearfix">
        <div class="l">
           <input type="button" class="but2" value="修改排序" onclick="document.form_order.submit();" />
           <input type="button" class="but2" value="删除选中" onclick="set_some('<?php echo $this->createUrl('linkageType/delete');?>?ids=[@]','确定删除吗？');" />
           <input type="button" class="but2" value="添加类别" onclick="location='<?php echo $this->createUrl('linkageType/update');?>'" />
        </div> 
        <div class="r">                    
        </div>
    </div>
</div>
<div class="main mbody">
<form action="?m=save_order" name="form_order" method="post">
<table class="tb fixTh" style="width: 60%">
    <thead>
    <tr>
        <th width="100"><a href="javascript:void(0);" onclick="check_all('.cklist');">全选/反选</a></th>
        <th  width="80"><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('linkageType/index').'?p='.$_GET['p'],'field_cn'=>'ID','field'=>'a.linkage_type_id')); ?></th>
        <th width="200"  class="alignleft"><?php echo helper::field_paixu(array('url'=>''.$this->createUrl('linkageType/index').'?p='.$_GET['p'],'field_cn'=>'类别名称','field'=>'a.linkage_type_name')); ?></th>
        <th width=200>操作</th>
    </tr>
    </thead>
   <?php 
   foreach($page['listdata']['list'] as $r){
   ?>
    <tr>   
        <td><input type="checkbox" class="cklist" value="<?php echo $r['linkage_type_id']; ?>" /></td>
        <td><?php echo $r['linkage_type_id']; ?></td>
        <td class="alignleft"><a href="<?php echo $this->createUrl('linkage/index');?>?linkage_type_id=<?php echo $r['linkage_type_id'];  ?>&p=<?php echo $_GET['p'];  ?>"><?php echo $r['linkage_type_name']; ?></a></td>     	
        <td><a href="<?php echo $this->createUrl('linkage/index');?>?linkage_type_id=<?php echo $r['linkage_type_id'];  ?>&p=<?php echo $_GET['p'];  ?>">管理菜单</a>
         <a href="<?php echo $this->createUrl('linkageType/update');?>?id=<?php echo $r['linkage_type_id'];  ?>&p=<?php echo $_GET['p'];  ?>">修改</a></td>
    </tr>
   <?php 
   } ?>
                  
</table>
  <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
  <div class="clear"></div>
</form>
</div>
<?php require(dirname(__FILE__)."/../common/foot.php"); ?>