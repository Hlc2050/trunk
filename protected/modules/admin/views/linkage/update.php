<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<?php 
if(!isset($page['info'])){
	$page['info']['linkage_id']='';
	$page['info']['linkage_name']='';
	$page['info']['linkage_name_py']='';
	$page['info']['linkage_type_id']='';
	$page['info']['linkage_deep']=0;
	$page['info']['linkage_remark']='';
	$page['info']['linkage_attr']=0;
	$page['info']['icon']='';
	$page['info']['linkage_order']=50;
	$page['info']['parent_id']=$this->get('parent_id');
}
?>
<script>
function get_pinyin(){
    var cname_py=$("#linkage_name_py");
	$.get("<?php echo $this->createUrl('infoCategory/getPinyin');?>?str="+encodeURI($("#linkage_name").val()),function(jsondata){
	    try{
		    var  data=$.evalJSON(jsondata);
			cname_py.val(data.str_py);
		}catch(e){
		    alert(e);	
		}
	})	
	
}
function get_deep(){
	var num=$("#t_s").find("select").length+1;
	if($("#t_s").find("select").last().find("option:selected").text()=='--选择--'){
		num-=1;
	}
	$("#linkage_deep").val(num);
	
}
</script>
<div class="main mhead">
    <div class="snav">内容中心 » 联动分类 » <?php echo $page['type_info']['linkage_type_name']; ?> »  菜单管理	</div>
</div>
<div class="main mbody">
<form method="post" action="<?php echo $this->createUrl('linkage/update'); ?>?p=<?php echo $_GET['p'];?>">
<input type="hidden" id="id" name="id" value="<?php echo $page['info']['linkage_id']; ?>" />
<input type="hidden" id="linkage_type_id" name="linkage_type_id" value="<?php echo $page['type_info']['linkage_type_id']; ?>" />
<table class="tb3">
    <tr>
        <th colspan="2" class="alignleft"><?php echo $page['info']['linkage_id']?'修改联动菜单':'添加联动菜单'; ?></th>
    </tr>      
    <tr>
        <td>选择父菜单</td>
        <td>
        
         <span id="t_s">
        </span>
        <span id="t_s_load"></span> 
           <script>cg_edit_sele_cc('<?php echo $page['info']['parent_id']; ?>','parent_id[]','t_s','<?php echo $page['type_info']['linkage_type_id']; ?>');</script>    
        </td>
    </tr>   
    <tr>
        <td  width="100">菜单名称：</td>
        <td  class="alignleft">
        <input type="text" class="ipt" onblur="get_deep();"  id="linkage_name"   name="linkage_name" value="<?php echo $page['info']['linkage_name']; ?>"/> 

        </td>      
    </tr>
    <tr>
        <td  width="100">别名：</td>
        <td  class="alignleft">
        <input type="text" class="ipt"  id="linkage_name_py"   name="linkage_name_py" value="<?php echo $page['info']['linkage_name_py']; ?>"/>  <input type="button" class="but2" value="获取拼音" onclick="get_pinyin()" />

        </td>      
    </tr>    
    <tr>
        <td  width="100">层级：</td>
        <td  class="alignleft">
        <input type="text" size="5" class="ipt"  id="linkage_deep"   name="linkage_deep" value="<?php echo $page['info']['linkage_deep']; ?>"/> <span> 此处可不填，特殊的时候用于检索</span>

        </td>      
    </tr>
    <tr>
        <td  width="100">自定义标记：</td>
        <td  class="alignleft">
        <input type="text"  class="ipt"  id="linkage_remark"   name="linkage_remark" value="<?php echo $page['info']['linkage_remark']; ?>"/> <span> 此处可不填，特殊的时候用于检索</span>

        </td>      
    </tr>
    <tr>
        <td  width="100">属性（数字）：</td>
        <td  class="alignleft">
        <input type="text" size="5" class="ipt"  id="linkage_attr"   name="linkage_attr" value="<?php echo $page['info']['linkage_attr']; ?>"/> <span> 特殊的时候用于检索，数字类型，如 1 为热门等</span>

        </td>      
    </tr>
     <tr>
        <td  width="100">排序：</td>
        <td  class="alignleft">
        <input type="text" size="5" class="ipt"  id="linkage_order"   name="linkage_order" value="<?php echo $page['info']['linkage_order']; ?>"/> <span> </span>

        </td>      
    </tr>    
    <tr>
        <td  width="100">图标：</td>
        <td  class="alignleft">
         <div class="l">
            <input type="text" class="ipt" id="icon" name="icon" value="<?php echo $page['info']['icon']; ?>"/>
        </div>
        <div class="l" style="margin:0px 10px;" id="icon_span">
        <?php echo $page['info']['icon']?'<img src="'.$page['info']['icon'].'" width=24 height=24>':'' ?>
        </div>
        <div class="l" >
           <script>create_upload_iframe('{"func":"callback_upload","Eid":"icon"}');</script>
        </div>

        </td>      
    </tr>
    <tr>
        <td></td>
        <td  class="alignleft"><input type="submit" class="but" id="subtn" value="确定" onmouseover="get_deep();" /> <input type="button" class="but" value="返回" onclick="window.location='<?php echo $this->createUrl('linkage/index'); ?>?p=<?php echo $_GET['p'];?>&linkage_type_id=<?php echo $page['type_info']['linkage_type_id']; ?>'" /></td>
    </tr>
</table>
</form>
</div>
<?php require(dirname(__FILE__)."/../common/foot.php"); ?>


