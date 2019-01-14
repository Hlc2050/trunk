<?php
return array(
    'system_name'=>'事业二部静态页面管理系统',
    'token_str'=>'4fBn8e1akM',
    'promotion_info_api'=>'http://erbu.net/staticApi/getPromotionInfo',
    'promotion_api'=>'http://erbu.net/staticApi/getPromotion',
    'css_url' => '/static/admin/',
    //推广文件存放目录
    'promotion_path'=>'/promotions/',
    'menus'=>array(
        1=>array('name'=>'页面管理','url'=>'promotionStatic.php?action=promotion','icon'=>'balance-scale')
    ),
    'user' => array(
        'admin'=>'a5ebd2bdc40c30395aa04e81d3931f14',//VRYGDXKFB014
    ),
);