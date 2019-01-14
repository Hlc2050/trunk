<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<?php 
if(!isset($page['info'])){
	$page['info']['id']='';
	$page['info']['name']='';
	$page['info']['mname']='';
	$page['info']['url']='';
	$page['info']['display']=1;
	$page['info']['parent_id']='';
    $page['info']['fonticon']='';
	$page['info']['displayorder']=50;
}
?>
<div class="main mhead">
    <div class="snav">内容中心 »  
    页面菜单管理 </div>
</div>
<div class="main mbody">
<form method="post" action="<?php echo $this->createUrl($page['info']['id']?'adminModules/edit':'adminModules/add'); ?>?p=<?php echo $_GET['p'];?>">
<input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>" />
<table class="tb3">
    <tr>
        <th colspan="2" class="alignleft"><?php echo $page['info']['id']?'修改页面菜单':'添加页面菜单' ?></th>
    </tr>   
    
    <tr>
        <td  width="100">上级栏目：</td>
        <td  class="alignleft"><select id="parent_id" name="parent_id">
        <option value="0">≡ 作为一级模块 ≡</option>
		<?php echo $page['categorys']; ?></select></td>      
    </tr>
    
    <tr>
        <td  width="100">页面名称：</td>
        <td  class="alignleft">
        <input type="text" class="ipt"  id="name"   name="name" value="<?php echo $page['info']['name']; ?>"/>
        </td>      
    </tr>
    
    <tr>
        <td  width="100">模块名：</td>
        <td  class="alignleft">
        <input type="text" class="ipt"  id="mname"   name="mname" value="<?php echo $page['info']['mname']; ?>"/>  *程序里的模块名
        </td>      
    </tr>
    <tr>
        <td  width="100">显示：</td>
        <td  class="alignleft">
        <label>显示 <input type="radio" class="ipt" id="display" name="display"  value="1" <?php echo $page['info']['display']?'checked':''; ?>/></label>
        <label>隐藏 <input type="radio" class="ipt" id="display" name="display" value="0" <?php echo $page['info']['display']?'':'checked'; ?>/></label>
        </td>      
    </tr>
    
    <tr>
        <td  width="100">URL：</td>
        <td  class="alignleft">
        <input type="text"  class="ipt"  id="url"   name="url" value="<?php echo $page['info']['url']; ?>"/> 

        </td>      
    </tr>
    <tr>
        <td  width="100">图标名称：</td>
        <td  class="alignleft">
            <input type="text"  class="ipt"  id="fonticon"   name="fonticon" value="<?php echo $page['info']['fonticon']; ?>"/>
            <a href="<?php echo $this->createUrl('adminModules/font'); ?>" onclick="return dialog_frame(this);">点击这里选择</a>

        </td>
    </tr>
    
    <tr>
        <td  width="100">显示顺序：</td>
        <td  class="alignleft">
        <input type="text"  class="ipt"  id="displayorder"   name="displayorder" value="<?php echo $page['info']['displayorder']; ?>"/> 

        </td>      
    </tr>
    
    <tr>
        <td></td>
        <td  class="alignleft"><input type="submit" class="but" id="subtn" value="确定" /> <input type="button" class="but" value="返回" onclick="window.location='<?php echo $this->createUrl('adminModules/index'); ?>?p=<?php echo $_GET['p'];?>'" /></td>
    </tr>
</table>
</form>
</div>
<?php require(dirname(__FILE__)."/../common/foot.php"); ?>