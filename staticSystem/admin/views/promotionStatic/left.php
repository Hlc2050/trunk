<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<style>
html{ overflow:auto;}
body{ background:#f0f6f6;overflow:hidden;}
</style>
<div class="leftmenu">
<ul>
    <?php foreach ($menus as $value) {?>
        <li class="menubig"><a href="<?php echo $value['url']?>" target="main"><span><i class="fa fa-<?php echo $value['icon']?>"></i> </span><?php echo $value['name']?></a></li>
    <?php } ?>
</ul>
</div>
