<?php
define('MODE','devoloper');  //test 测试, production,上线,devoloper  开发

ini_set('date.timezone','Asia/Shanghai');
$config=include(dirname(__FILE__).'/config_'.MODE.'.php');
include(dirname(__FILE__) . '/publicFunction.php');

