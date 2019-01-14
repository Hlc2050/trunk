<?php
define('MODE','devoloper');  //test 测试, production,上线,devoloper  开发
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('date.timezone','Asia/Shanghai');
//站点根目录路径
$new_path = str_replace('\\','/',dirname(__FILE__));
define('ROOT_PATH',str_replace('/include','',$new_path));
$config=include(ROOT_PATH.'/include/config.php');
include(ROOT_PATH . '/include/function.php');