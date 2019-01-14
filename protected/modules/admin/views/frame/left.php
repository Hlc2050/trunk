<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<script>

$(document).ready(function(){
	
    $(".leftmenu ul li.menubig").click(function(){
	    var id=$(this).attr("id");
		$(".leftmenu ul li").each(function(){
			if(!$(this).attr("id")){
				<?php if(Yii::app()->params['management']['menu_open']==0){ ?>
				$(this).stop().hide('fast');
				<?php }?>
			}	
		})
		$("."+id).stop().slideToggle('fast');	
		if(!$(this).attr('href')){
			return false;
		}
	})
	<?php if(Yii::app()->params['management']['menu_open']==0){ ?>
	$(".leftmenu ul li.menubig").eq(0).click();
	<?php }?>

	$(".leftmenu ul li").click(function(){
		if($(this).hasClass('menubig')) return;
		$(".leftmenu ul li.act").removeClass('act');
		$(this).addClass('act');
		
	})
	
})
</script>
<style>
html{ overflow:auto;}
body{ background:#f0f6f6;overflow:hidden;}
</style>
<div class="leftmenu">
<ul>
<?php if($this->get('layout')==2){ ?>
	<?php 
	$menuCates=AdminModules::model()->cate_son(0);
    foreach($menuCates as $menu){
    ?>
    <?php if($menu['display']==0)continue; ?>
    <?php $this->check_u_menu(array('code'=>'<li class="menubig" id="menuss'.$menu['mname'].'"><a href="'.(count($menu['url'])==0?$this->createUrl($menu['url']):'javascript:void(0);').'"  target="main"><span><i class="fa fa-'.$menu['fonticon'].'"></i> </span>'.$menu['name'].'</a></li>','auth_tag'=>$menu['mname'],'param_type'=>1,'echo'=>1)); ?>
        <?php foreach($menu['son'] as $menu2){ ?>
        <?php if($menu2['display']==0)continue; ?>
        <?php $this->check_u_menu(array('code'=>'<li '.(Yii::app()->params['management']['menu_open']==0?'style="display:none;"':'').' class="menuss'.$menu['mname'].'"><a href="'.$this->createUrl($menu2['url']).'"  target="main"><span class="structspace"></span> <i class="fa fa-'.$menu2['fonticon'].'"></i> '.$menu2['name'].'</a></li>','auth_tag'=>$menu2['mname'],'param_type'=>1,'echo'=>1)); ?>
    <?php }?>
    <?php 
    }
    ?>
<?php }else{?>
	<?php $menuSons=AdminModules::model()->cate_son($this->get('mid'));//print_r($menuSons);?>
	<?php foreach($menuSons as $menu2){ ?>
	<?php if($menu2['display']==0)continue; ?>
	<?php $this->check_u_menu(array('code'=>'<li ><a href="'.$this->createUrl($menu2['url']).'"  target="main"><i class="fa fa-'.$menu2['fonticon'].'"></i> '.$menu2['name'].'</a></li>','auth_tag'=>$menu2['mname'],'param_type'=>1,'echo'=>1)); ?>
	<?php }?>

<?php }?>

</ul>
</div>
