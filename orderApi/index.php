<?php
ini_set('date.timezone', 'Asia/Shanghai');
include(dirname(__FILE__) . '/framework/db/dbMysql.php');
include(dirname(__FILE__) . '/protected/modules/order.php');
$config = include(dirname(__FILE__) . '/config/config.php');
$dbm = new dbMysql($config['db_mysql']['default']);
$order = new Order($dbm, $config);
$order->index();