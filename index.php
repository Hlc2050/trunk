<?php 
error_reporting(E_ALL || ~E_NOTICE);
// change the following paths if necessary
$yii=dirname(__FILE__).'/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following line when in production mode
 defined('YII_DEBUG') or define('YII_DEBUG',true);

require_once($yii);
Yii::createWebApplication($config)->run();

/**
 * 自己写全局打印函数
 * @param $param
 * author: yjh
 */
function my_print($param)
{
    $type = gettype($param);
    echo '<pre>';
    if (in_array($type, array('resource', 'object', 'unknow type', 'boolean'))) {
        var_dump($param);
    } else if (in_array($type, array('array'))) {
        print_r($param);
    } else {
        echo $param;
    }
    echo '</pre>';
}


//echo  Yii::getVersion();