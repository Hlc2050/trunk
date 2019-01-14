<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<?php 
if(!isset($page['info'])){
	$page['info']['role_id']='';
	$page['info']['role_name']='';
}
?>
<div class="main mhead">
    <div class="snav">系统功能 »  
    岗位管理 </div>
</div>
<div class="main mbody">
<form method="post" action="<?php echo $this->createUrl('adminRole/update'); ?>?p=<?php echo $_GET['p'];?>">
<input type="hidden" id="id" name="id" value="<?php echo $page['info']['role_id']; ?>" />
<table class="tb3">
    <tr>
        <th colspan="2" class="alignleft"><?php echo $page['info']['role_id']?'修改岗位':'添加岗位' ?></th>
    </tr>   

    
    <tr>
        <td  width="100">岗位名称：</td>
        <td  class="alignleft">
        <input type="text"  class="ipt"  id="role_name"   name="role_name" value="<?php echo $page['info']['role_name']; ?>"/> 

        </td>      
    </tr>
 </table>   
  <div style="text-indent:10px;" class="mt10"><strong>岗位权限：</strong></div>
   <?php
   $c=0;
   $menuCates=AdminModules::model()->cate_son(0);
foreach($menuCates as $menu){if($menu['display']==0)continue; 
?>
<table class="tb mt10 tb4">
<tr>
<th><label><input class="ceng_1" type="checkbox" <?php  checked($page,$menu['id'],1); ?>  name="role_levels1[]" value="<?php echo $menu['id']; ?>"/> <?php echo $menu['name']; ?></label></th>
</tr>
<tr>
<td><div class="less2">
<?php
 foreach($menu['son'] as $menu2){$c++;if(isset($menu2['hide'])&&$menu2['hide']==1)continue;  ?>
<div class="wwwd_<?php echo $c; ?>">
 <label><input class="ceng_2"  type="checkbox" onclick="$('.wwwd_<?php echo $c; ?>').find('.chs').attr('checked',true)" <?php  checked($page,$menu2['id'],1); ?> name="role_levels1[]" value="<?php echo $menu2['id']; ?>"/> <strong><?php echo $menu2['name'] ;?></strong></label>
<?php
	$functionArr=AdminFunction::model()->getFunctions($menu2['id']);
    foreach($functionArr as $menu3){ ?>
 <label><input class="ceng_3" class="chs" type="checkbox" <?php  checked($page,$menu3['id'],2); ?> name="role_levels2[]" value="<?php echo $menu3['id']; ?>"/> <?php echo $menu3['function_name'] ;?></label>


<?php } ?>
</div>
<?php } ?>
</div>

</td>
</tr>
</table>
<?php 
}
?><br />  
    

  <input type="submit" class="but" id="subtn" value="确定" /> <input type="button" class="but" value="返回" onclick="window.location='<?php echo $this->createUrl('adminRole/index'); ?>?p=<?php echo $_GET['p'];?>'" />
</form>
</div>

<script>
    $(document).ready(function(){
        $('.ceng_1').change(function(){
            if($(this).attr('checked')){
                $(this).parent().parent().parent().parent().find('.ceng_2,.ceng_3').attr('checked',true);
            }else{
                $(this).parent().parent().parent().parent().find('.ceng_2,.ceng_3').attr('checked',false);
            }
        });
        $('.ceng_2').change(function(){
            if($(this).attr('checked')){
                $(this).parent().parent().find('.ceng_3').attr('checked',true);
            }else{
                $(this).parent().parent().find('.ceng_3').attr('checked',false);
            }
        })
    })
</script>

<?php require(dirname(__FILE__)."/../common/foot.php"); ?>
<?php 
function checked($page,$myvalue,$type){
	$arr=is_array($page['role_auth'])?$page['role_auth']:array();
	foreach($arr as $r){
	    if($r['authority_id']==$myvalue && $type==$r['type']){
		    echo ' checked ';	
		    break;
		}
	}
}
?>
