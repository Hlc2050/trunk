<?php require(dirname(__FILE__) . "/../common/header.php"); ?>

<!-- sidebar start -->
<div class="admin-sidebar am-offcanvas" id="admin-offcanvas">
    <div class="am-offcanvas-bar admin-offcanvas-bar">
        <ul class="am-list admin-sidebar-list">
            <li><a href="<?php echo $this->createUrl('frame/index'); ?>"><span class="am-icon-home"></span> 首页</a></li>
            <?php $mobile_menus=vars::$fields['mobile_menus'];
            foreach ($mobile_menus as $val){
           $this->check_u_menu(array('code'=>'<li><a href="'.$this->createUrl($val['url']).'"><span class="'.$val['icon'].'"></span> '.$val['name'].'</a></li>','auth_tag'=>$val['auth'],'param_type'=>1,'echo'=>1));
            }
            ?>

        </ul>

        <div class="am-panel am-panel-default admin-sidebar-panel">
            <div class="am-panel-bd">
                <p><span class="am-icon-bookmark"></span> 公告</p>
                <p>developing...</p>
            </div>
        </div>

    </div>
</div>
<!-- sidebar end -->
<a href="#" class="am-icon-th-list am-show-sm-only admin-menu" data-am-offcanvas="{target: '#admin-offcanvas'}"></a>