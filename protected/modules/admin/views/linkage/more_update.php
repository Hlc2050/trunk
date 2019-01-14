<?php require(dirname(__FILE__)."/../common/head.php"); ?>
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
<form method="post" action="<?php echo $this->createUrl('linkage/moreUpdate'); ?>?p=<?php echo $_GET['p'];?>">
<input type="hidden" id="linkage_type_id" name="linkage_type_id" value="<?php echo $page['type_info']['linkage_type_id']; ?>" />
<table class="tb3">
    <tr>
        <th colspan="2" class="alignleft">批量添加联动菜单</th>
    </tr>      
    <tr>
        <td>选择父菜单</td>
        <td>
        
         <span id="t_s">
        </span>
        <span id="t_s_load"></span> 
           <script>cg_edit_sele_cc('<?php echo $this->get('parent_id'); ?>','parent_id[]','t_s','<?php echo $page['type_info']['linkage_type_id']; ?>');</script>    
        </td>
    </tr>   
    
        
    <tr>
        <td  width="100">层级：</td>
        <td  class="alignleft">
        <input type="text" size="5" class="ipt"  id="linkage_deep" onmouseover="get_deep();"   name="linkage_deep" value=""/> <span> 此处可不填，特殊的时候用于检索</span>

        </td>      
    </tr>
    
    <tr>
        <td  width="100">属性（数字）：</td>
        <td  class="alignleft">
        <input type="text" size="5" class="ipt"  id="linkage_attr"   name="linkage_attr" value=""/> <span> 特殊的时候用于检索，数字类型，如 1 为热门等</span>

        </td>      
    </tr>
    
    <tr>
        <td  width="100">名称：</td>
        <td  class="alignleft">
        <textarea name="linkage_name_str" onfocus="get_deep();" style="width:600px;height:200px;"></textarea>
         <span> 用空格隔开</span>

        </td>      
    </tr>
       
    
    <tr>
        <td></td>
        <td  class="alignleft"><input type="submit" class="but" id="subtn" value="确定" onmouseover="get_deep();" /> <input type="button" class="but" value="返回" onclick="window.location='<?php echo $this->createUrl('linkage/index'); ?>?p=<?php echo $_GET['p'];?>&linkage_type_id=<?php echo $page['type_info']['linkage_type_id']; ?>'" /></td>
    </tr>
</table>
</form>
</div>
<script>
setTimeout("get_deep();",2000);
</script>
<?php require(dirname(__FILE__)."/../common/foot.php"); ?>


