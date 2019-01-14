<?php $_GET['layout']=isset($_GET['layout'])?$_GET['layout']:''; ?>
<?php
$page['doctype']=1;
$page['body_extern']='style="overflow:hidden;" class="body_top"';
?>
<?php require(dirname(__FILE__)."/../common/head.php"); ?>


<script>
function set_admin_style(style_folder){
	$.cookie("admin_style",style_folder,{expires:1000,path:'/'});
	parent.location.reload();	
}
</script>
<style>
.stylebox a{ display:inline-block; width:15px; height:15px; margin-right:5px; overflow:hidden; text-indent:100px; line-height:100px; vertical-align:middle;}
</style>

<div class="l">
	<a href="<?php echo $this->createUrl('frame/welcome'); ?>" target="main" class="logoa" style=" display:block; <?php if($this->layout==''){echo 'height:48px;line-height:48px;overflow:hidden;';}else if($this->layout==2){echo 'height:48px;line-height:48px;overflow:hidden;color:#fff;';} ?>width:200px;">
        <?php echo Yii::app()->params['management']['name']?Yii::app()->params['management']['name']:Yii::app()->params['basic']['sitename']; ?></a>

</div>



<div style="height:48px;text-align:right;float:right;">
<div class="r" style=" padding:13px 10px 0 0;">
<a href="<?php echo $this->createUrl("frame/index");  ?>?layout=<?php echo $this->get('layout')=='1'?2:1; ?>"  target="_parent">[修改布局]</a>
<span class="stylebox">
<?php 
foreach($this->AdminStyleArray as $r){
    echo '<a href="javascript:void(0);" onclick="set_admin_style(\''.$r['style_folder'].'\');" style="background:'.$r['style_color'].';" title="'.$r['style_name'].'">'.$r['style_name'].'</a>';	
}
?>
</span>
欢迎登录，<?php $id = Yii::app()->admin_user->uid; $name = AdminUser::model()->find('csno='.$id); echo $name['csname']; ?>
    <?php  if( $_SERVER['HTTP_HOST'] != yii::app()->params['customer_config']['domain']){ ?>
        <?php  echo Yii::app()->admin_user->uname_true; ?>
<a href="<?php echo $this->createUrl("site/editPassword");?>" target="main">修改密码</a>
    <?php }; ?>
<a href="<?php echo $this->createUrl('site/logout'); ?>" target=_parent><img  src="/static/admin/img/out.png"></a>
</div>
</div>

<?php  if($this->get('layout')==1){ ?>

<div class="topmenu">
<ul class="clearfix">
	<?php
	$menuCates=AdminModules::model()->cate_son(0);
	$i=0;foreach($menuCates as $menu){$i++;
		?>
		<?php if($menu['display']==0)continue; ?>
		<?php $this->check_u_menu(array('code'=>'<li '.($i==1?'class="current"':'').'  class="menubig" id="menuss'.$menu['mname'].'"><a class="bigmodulea" href="'.$this->createUrl('frame/left').'?mid='.$menu['id'].'&layout=1"  target="left_frame"><span><i ckass="fa fa-podcast"></i></span>'.$menu['name'].'</a></li>','auth_tag'=>$menu['mname'],'param_type'=>1,'echo'=>1)); ?>

		<?php
	}
	?>
</ul>
</div>

	<script>

		$(document).ready(function(){
			$('.topmenu li').eq(0).find('span').click();
			$('.topmenu li').click(function(){
				$('.topmenu li').removeClass('current');
				$(this).addClass('current');
			});
		})
	</script>

<?php }?>



<div id="sound_box" style=" height:1px; overflow:hidden; position:relative; width:1px; position:absolute;"></div>

</body>
</html>