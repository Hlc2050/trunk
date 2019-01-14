<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<script>
$(function(){
	bind_hover();
})
function get_child_tr(id,linkage_type_id){
    try{//alert($("#linkage_tr_"+id+" > div").attr("display"));
	   if($("#linkage_tr_"+id+" > div").html()!=null){
		   if($("#linkage_tr_"+id+" > div").css("display")=='none'){
		       $("#linkage_tr_"+id+" > div").css({"display":""});
		   }else{
			   $("#linkage_tr_"+id+" > div").css({"display":"none"});   
		   }
		   return false;
	   }
	   $.get("/index.php/admin/linkage/getChildLinkageForList/?linkage_type_id="+linkage_type_id+"&linkage_id="+id,function(data){
		     $("#linkage_tr_"+id).append(data);
		     bind_hover(); 	   	  
	   })
			
	}catch(e){alert(e.message);}	
}
function bind_hover(){
	$(".linkage-main-box p").hover(
		function(){
			var top=$(this).offset().top;
			$(".float-bg").css({display:"block",top:top+'px'});
		},
		function(){
			$(".float-bg").css({display:"none"});
		}
	);
	
}
</script>
<style>
.bb002_box{ margin-left:40px; line-height:24px; position:relative;}
.bot_line{ position:absolute; width:100%; height:1px; top:20px; border-bottom:1px solid #ccc;}
.aorder001{ margin:2px 100px 2px 0; display:inline-block;}
.aorder002{ margin:2px 80px 2px 0; display:inline-block;}
.aorder003{ margin:2px 25px 2px 0; display:inline-block;}
.aorder004{ margin:2px 25px 2px 0; display:inline-block;}
.aorder005{ margin:2px 25px 2px 0; display:inline-block;}
.main-form{z-index:2;position:relative;}
.float-bg{height:18px;position:absolute;border:2px solid #f6bd24;width:99%;display:none;z-index:0;}
</style>
<div class="main mhead">
    <div class="snav">基础类别 » 类别配置 » <?php echo $page['type_info']['linkage_type_name']; ?> »  菜单管理	</div>
    <div class="mt10 clearfix">
        <div class="l">
           <input type="button" class="but2" value="修改排序、层级、标识等" onclick="document.form_order.submit();" />
           <input type="button" class="but2" value="删除选中" onclick="set_some('<?php echo $this->createUrl('linkage/delete');?>?linkage_type_id=<?php echo $page['type_info']['linkage_type_id']; ?>&ids=[@]','请注意，如果有子菜单的话会删除失败，请先删除子菜单？');" />
           <input type="button" class="but2" value="添加菜单" onclick="location='<?php echo $this->createUrl('linkage/update');?>?linkage_type_id=<?php echo $page['type_info']['linkage_type_id']; ?>'" />
           <input type="button" class="but2" value="批量添加菜单" onclick="location='<?php echo $this->createUrl('linkage/moreUpdate');?>?linkage_type_id=<?php echo $page['type_info']['linkage_type_id']; ?>'" />
        </div> 
        <div class="r">
            
        </div>
    </div>
</div>
<div class="main mbody">
<form class="main-form" action="<?php echo $this->createUrl('linkage/saveOrder');?>?linkage_type_id=<?php echo $page['type_info']['linkage_type_id']; ?>" name="form_order" method="post">
<div>
   <div class="title02">
       <div class="l"><a href="javascript:void(0);" onclick="check_all('.cklist');">全选/反选</a></div>
       <div class="r"><span class="aorder001" style="margin-right:50px; display:inline-block;">自定义属性</span><span class="aorder001" style="margin-right:50px; display:inline-block;">拼音别名</span><span class="aorder001" style="margin-right:40px; display:inline-block;">自定义标识</span><span class="aorder001" style="margin-right:80px; display:inline-block;">层级</span> <span class="aorder001" style=" display:inline-block;">排序</span> 操作</div>
       <div class="clear"></div>
   </div> 
   <div class="linkage-main-box">
   <?php 
   foreach($page['listdata']['list'] as $r){
   ?>
    <div id="linkage_tr_<?php echo $r['linkage_id']; ?>" style="position: relative;" class="clearfix">
        <p class="l">   
        <input type="checkbox" class="cklist" value="<?php echo $r['linkage_id']; ?>" />
        <?php echo $r['linkage_id']; ?>
        <?php if($r['icon']){?>
        <img src="<?php echo $r['icon'];?>" width=20 height=20/>
        <?php }?>
        <a href="javascript:void(0);" onclick="get_child_tr(<?php echo $r['linkage_id']; ?>,'<?php echo $page['type_info']['linkage_type_id']; ?>');"><?php echo $r['linkage_name']; ?></a>
     	</p>
        <p class="r">
        <span class="aorder005"><input type="text" size="2" name="linkage_attr[<?php echo $r['linkage_id']; ?>]" value="<?php echo $r['linkage_attr']; ?>" /></span>
        <span class="aorder004"><input type="text" size="10" name="linkage_name_py[<?php echo $r['linkage_id']; ?>]" value="<?php echo $r['linkage_name_py']; ?>" /></span>
        <span class="aorder003"><input type="text" size="10" name="listremarks[<?php echo $r['linkage_id']; ?>]" value="<?php echo $r['linkage_remark']; ?>" /></span>
        <span class="aorder002"><input type="text" size="2" name="listdeeps[<?php echo $r['linkage_id']; ?>]" value="<?php echo $r['linkage_deep']; ?>" /></span>
        <span class="aorder001"><input type="text" size="2" name="listorders[<?php echo $r['linkage_id']; ?>]" value="<?php echo $r['linkage_order']; ?>" /></span>
        <a class="am001" href="<?php echo $this->createUrl('linkage/update');?>?id=<?php echo $r['linkage_id'];  ?>&p=<?php echo $_GET['p'];  ?>&linkage_type_id=<?php echo $page['type_info']['linkage_type_id']; ?>">修改</a>

        </p>
        <p class="clear"></p>
        <span class="bot_line"></span> 
     </div>   
   
   <?php 
   } ?> 
   </div>
</div>
  <div class="clear"></div>
</form>
</div>
<div class="float-bg"></div>

<?php require(dirname(__FILE__)."/../common/foot.php"); ?>