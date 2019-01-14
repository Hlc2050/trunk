<?php require(dirname(__FILE__) . "/../common/menu.php"); ?>
<div class="admin-content">
    <a id="top" name="top"></a>
    <div style="margin-top: 50px;"></div>
    <div class="admin-content-body">
        <div class="am-cf am-padding">
            <div class="am-fl am-cf"><strong class="am-text-default am-text-lg">首页</strong>
                <small></small>
            </div>
        </div>
        <div class="am-g">
            <div class="am-u-sm-9 am-u-sm-centered">
                <?php $mobile_menus=vars::$fields['mobile_menus'];
                foreach ($mobile_menus as $val){
                    $this->check_u_menu(array('code'=>'<a href="'.$this->createUrl($val['url']).'" class="am-btn am-btn-primary  am-btn-block am-radius" role="button"><i class="'.$val['icon'].'"></i>&nbsp; '.$val['name'].'</a>','auth_tag'=>$val['auth'],'param_type'=>1,'echo'=>1));
                }
                ?>

            </div>
        </div>
    </div>


</div>
</body>
</html>