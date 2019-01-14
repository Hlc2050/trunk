<?php
include(dirname(__FILE__) . '/init.php');
include(dirname(__FILE__) . '/dbMysql.php');
include(dirname(__FILE__) . '/domainMonitor.php');
//本页为在CLI模式下执行 该文件的
//error_reporting(E_ALL & ~E_STRICT);  //php 5.4 以上 对方法重写参数需要一致，不一致需要加上 默认值,问题出现在 Dtable里面
$dbm = new dbMysql($config['db_mysql']['default']);
$config['domain_check_url'] = $config['domain_check_two'];
$smartCollect = new domainMonitor($dbm, $config);
$smartCollect->msgTitle = 'Line-Two';
$smartCollect->even = 0;
$smartCollect->run();

echo '---------------------------completed!';