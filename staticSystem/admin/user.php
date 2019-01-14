<?php
session_start();
require (dirname(__FILE__).'/../include/init.php');
$action = $_GET['action'] ? $_GET['action']:'login';
$view_path = ROOT_PATH.'/admin/views/user/';
//用户登录
if ($action == 'login') {
    if (isset($_SESSION['account'])) {
        header('Location: promotionStatic.php');
        exit();
    }
    if (!$_POST) {
        require ($view_path.'login.php');
        exit();
    }else {
        $user_name = $_POST['uname']?$_POST['uname']:'';
        $password = $_POST['upass']?$_POST['upass']:'';
        if (!$user_name) {
            msg(array('state' => 0, 'msgwords' => '请输入用户名!'));
        }
        if (!$password) {
            msg(array('state' => 0, 'msgwords' => '请输入密码!'));
        }
        $users = $config['user'];
        if (isset($users[$user_name])) {
            $pass = $users[$user_name];
            if (md5($password) != $pass) {
                msg(array('state' => 0, 'msgwords' => '密码错误!'));
            }
        } else {
            msg(array('state' => 0, 'msgwords' => '用户不存在!'));
        }
        //账号信息验证成功
        $_SESSION['account'] = $user_name;
        header('Location: promotionStatic.php');
        exit();
    }
}
if ($action == 'logout') {
    unset($_SESSION['account']);
    header('Location: user.php');
    exit();
}