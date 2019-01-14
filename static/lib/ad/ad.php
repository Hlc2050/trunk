<?php
//	页面初始化，每个文件都要使用该部分
//	载入配置文件/核心文件
require_once(dirname(__FILE__)."/../../core/config.php");
require_once(dirname(__FILE__)."/../../core/core.class.php");
require_once(dirname(__FILE__)."/../../common/config.php");
require_once(dirname(__FILE__)."/../../common/common.fun.php");
$time_start = helper::getmicrotime();

//******************** 自定义代码开始 *************************************************************************
// 取得GET和POST变量并进行验证，参数分支选择
$page['get']=$_GET;
$page['post']=$_POST;
if(!isset($page['get']['p'])) $page['get']['p']=1;
$dbm=new db_mysql($config['db_mysql']['default']);
// print_r($dbm->query("show tables",'',1));print_r($dbm->query_count);
// 设置页面数据
if(!isset($page['get']['aid'])) $page['get']['aid']='';
$page['get']['aid']=intval($page['get']['aid']);

$sql="select * from ad_list where ad_id=".$page['get']['aid'];//echo $sql;
$a=$dbm->query($sql);
if(count($a['list'])==0){
    die();	
}
$b=$a['list'][0];
if($b['show_type']==0){
    echo $b['ad_code'];
	die();
}else if($b['show_type']==1){
    $code='<a href="'.$b['ad_url'].'" target="_blank">'.$b['ad_words'].'</a>';
	echo 'document.write("'.$code.'")';
	die();
}else if($b['show_type']==2){
     $code='<a href="'.$b['ad_url'].'" target="_blank">'.$b['ad_img'].'</a>';
	echo 'document.write("'.$code.'")';
	die();
}
?>