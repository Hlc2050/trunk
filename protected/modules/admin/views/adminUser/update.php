<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<?php 
if(!isset($page['info'])){
	$page['info']['csno']='';
	$page['info']['groupid']='';
	$page['info']['csname']='';
	$page['info']['cspwd']='';
	$page['info']['csname_true']='';
	$page['info']['csemail']='';
	$page['info']['csmobile']='';
	$page['info']['csname_true']='';
}
?>
<div class="main mhead">
    <div class="snav">系统功能 »  
    管理员管理 </div>
</div>
<div class="main mbody">
<form method="post" action="<?php echo $this->createUrl('adminUser/update'); ?>?p=<?php echo $_GET['p'];?>">
<input type="hidden" id="id" name="id" value="<?php echo $page['info']['csno']; ?>" />
<table class="tb3">
    <tr>
        <th colspan="2" class="alignleft"><?php echo $page['info']['csno']?'修改管理员':'添加管理员' ?></th>
    </tr>
    <tr>
        <td  width="100">所属部门：</td>
        <td  class="alignleft">
            <div style="border:1px solid #ccc;padding:10px;">
            <?php echo $page['groups_tree']; ?>
            </div>
        </td>      
    </tr>      
    <tr>
    	<td>所属岗位：</td>
    	<td>
    	<?php foreach($page['roles'] as $r){?>
    	<label><input type="checkbox" name="roles[]" value="<?php echo $r['role_id'];?>" <?php echo $r['checked']?'checked':'';?> > <?php echo $r['role_name']; ?></label>
    	<?php }?>
    	</td>
    </tr>
    <tr>
        <td  width="100">帐号：</td>
        <td  class="alignleft">
        <input type="text"  class="ipt"  id="csname" autocomplete="off"   name="csname" value="<?php echo $page['info']['csname']; ?>"/> 
        </td>      
    </tr>
    <tr>
        <td  width="100">真实姓名：</td>
        <td  class="alignleft">
        <input type="text"  class="ipt"  id="csname_true" autocomplete="off"   name="csname_true" value="<?php echo $page['info']['csname_true']; ?>"/> 
        </td>      
    </tr>
    <tr>
        <td  width="100">密码：</td>
        <td  class="alignleft">
        <input type="password"  class="ipt"  id="cspwd" autocomplete="off"    name="cspwd" value=""/> 
        </td>      
    </tr>
    <tr>
        <td  width="100">管理员电话：</td>
        <td  class="alignleft">
        <input type="text" class="ipt"  id="csmobile"   name="csmobile" value="<?php echo $page['info']['csmobile']; ?>"/> 
        </td>      
    </tr>
    <tr>
        <td  width="100">管理员邮箱：</td>
        <td  class="alignleft">
        <input type="text" class="ipt"  id="csemail"   name="csemail" value="<?php echo $page['info']['csemail']; ?>"/> 
        </td>      
    </tr>

    <tr>
        <td colspan="2">
            <div style="text-indent:10px;font-size:18px;" class="mt10"><strong>私有权限(在原岗位权限的基础上增加)：</strong></div>
            <div class="checkbox22">
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
                                        <label><input class="ceng_2"   type="checkbox"  <?php  checked($page,$menu2['id'],1); ?> name="role_levels1[]" value="<?php echo $menu2['id']; ?>"/> <strong><?php echo $menu2['name'] ;?></strong></label>
                                        <?php
                                        $functionArr=AdminFunction::model()->getFunctions($menu2['id']);
                                        foreach($functionArr as $menu3){ ?>
                                            <label><input class="ceng_3 chs" type="checkbox" <?php  checked($page,$menu3['id'],2); ?> name="role_levels2[]" value="<?php echo $menu3['id']; ?>"/> <?php echo $menu3['function_name'] ;?></label>


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
            </div>
        </td>

    </tr>



    <tr>
        <td></td>
        <td  class="alignleft"><input type="submit" class="but" id="subtn" value="确定" /> <input type="button" class="but" value="返回" onclick="window.location='<?php echo $this->createUrl('adminUser/index'); ?>?p=<?php echo $_GET['p'];?>'" /></td>
    </tr>
</table>
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
    $arr=is_array($page['userAuths'])?$page['userAuths']:array();
    foreach($arr as $r){
        if($r['authority_id']==$myvalue && $type==$r['param_type']){
            echo ' checked disabled ';
            break;
        }

    }
    $arr=is_array($page['userPrivateAuths'])?$page['userPrivateAuths']:array();
    foreach($arr as $r){
        if($r['authority_id']==$myvalue && $type==$r['param_type']){
            echo ' checked  ';
            break;
        }

    }





}

?>
