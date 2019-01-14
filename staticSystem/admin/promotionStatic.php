<?php
/**
 * 静态类型推广信息显示页
 * author lxj 2018-08-02
 */
require (dirname(__FILE__).'/../include/init.php');
session_start();
if (!isset($_SESSION['account'])) {
    header('Location:user.php');
    exit();
}
$view_path = ROOT_PATH.'/admin/views/promotionStatic/';
$action = $_GET['action'] ? $_GET['action']:'index';
$account = $_SESSION['account'];
if ($action == 'index') {
    include ($view_path.'index.php');
}

if ($action == 'top') {
    include ($view_path.'top.php');
}

if ($action == 'left') {
    $menus = $config['menus'];
    include ($view_path.'left.php');
}
//静态推广信息页
if ($action == 'promotion') {
    $token = getAccountToken($account);
    $where = '';
    $promotion_id = '';
    $channel_name = '';
    $page = 1;
    //页数
    if($_GET['p']) {
        $page = intval($_GET['p']);
    }
    if($_GET['promotion_id']) {
        $promotion_id = $_GET['promotion_id'];
        $where.='&promotion_id='.$promotion_id;
    }
    if($_GET['channel_name']) {
        $channel_name = $_GET['channel_name'];
        $where.='&channel_name='.$_GET['channel_name'];
    }
    $url = $config['promotion_info_api'].'?account='.$account.'&token='.$token.'&p='.$page.$where;
    $ret = curl_get($url);
    $promotions = json_decode($ret);
    $promotions = $promotions->data;
    include ($view_path.'promotion.php');
}

//上传文件
if ($action == 'upload_file') {
    $pid = $_GET['id'];
    if (!$pid) {
        die('推广id有误！');
    }
    //推广文件夹
    $folder = getPromotionFolder($pid);
    $path = ROOT_PATH.$config['promotion_path'].$folder;
    if (!$_POST) {
        $files = getFiles($path);
        include ($view_path.'upload_file.php');
        exit();
    }
    $file = $_FILES['file'];
    if (isset($_POST['file_name'])) {
        if (file_exists($path.'/'.$file['name'])){
            die(json_encode(array('status'=>'0','msg'=>'文件已存在')));
        }else {
            die(json_encode(array('status'=>'1','msg'=>'文件不存在')));
        }
    }
    include (ROOT_PATH.'/include/uploadClass.php');
    $upload = new uploadClass($file);
    $res = $upload->uploadFile($path);
    if(!$res) {
        die('文件上传失败！');
    } else {
        if ($res['status'] != 1) {
            die($res['mes']);
        }
    }
}

//删除文件
if ($action == 'delete_file') {
    $pid = $_GET['id'];
    if (!$pid) {
        msg(array('state' => 0, 'msgwords' => '请先选择推广!'));
    }
    //推广文件夹
    $folder = getPromotionFolder($pid);
    $path = ROOT_PATH.$config['promotion_path'].$folder;
    $res = removeDir($path);
    if ($res) {
        msg(array('state' => 1, 'msgwords' => '删除成功!'));
    } else {
        msg(array('state' => 0, 'msgwords' => '删除失败!'));
    }

}
