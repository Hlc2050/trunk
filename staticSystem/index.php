<?php
include (dirname(__FILE__).'/include/init.php');
$uri = $_SERVER['REQUEST_URI'];
$domain = $_SERVER['HTTP_HOST'];
$length = strpos($uri,'?');
if ($length !== false) {
    $uri = substr($uri,0,$length);
}
$goto_rule = "/\/index.php\\/\d*\w*.html/";
$tg_rule = '/\/index.php\/\w*\/\w*\.html/';
//是否为跳转链接
$is_goto = preg_match($goto_rule,$uri) ? true:false;
//是否为落地页连接
$is_tg = (preg_match($tg_rule,$uri) || trim($uri,'/') == '') ? true:false;
if (!$is_goto && !$is_tg) {
    die('static_error 4001');
}

//跳转链接
if ($is_goto) {
    $rand_str = explode('/',$uri);
    $rand_str = str_replace('.html','',end($rand_str));
    $pid = getGotoPid($rand_str);
    $url = $config['promotion_api'].'?info=domain&pid='.$pid;
    $ret = curl_get($url);
    $ret = json_decode($ret);
    if ($ret->ret != 200 ) {
        die('static_error_ret '.$ret->ret);
    }
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if (stristr($user_agent, 'windows')) {
        if (isset($ret->data->is_pc_show) && $ret->data->is_pc_show == 1) {
            $pc_url = $ret->data->pc_url;
            if (stripos($ret->data->pc_url,'http://') === false && stripos($ret->data->pc_url,'https://') === false) {
                $pc_url = 'http://'.$pc_url;
            };
            header('Location:' . $pc_url);
            exit();
        }
    }
    $folder = getPromotionFolder($pid);
    //优先选取正常的域名跳转
    $use_domain = array();
    $pro_domains = $ret->data->domain_list;
    foreach ($pro_domains as $value) {
        if ($value->domain_status == 0 || $value->domain_status == 1) {
            $use_domain[] = $value;
        }
    }
    if ($use_domain) {
        $key = rand(0,count($use_domain)-1);
        $tg_url = $use_domain[$key];
    }else {
        $key = rand(0,count($pro_domains)-1);
        $tg_url = $pro_domains[$key];
    }
    if (!$tg_url) {
        die('static_error 4002');
    }
    $ret->data->domain_list = array(0=>$tg_url);
    $tg_link = buildTgLink($ret->data,$pid,$ret->data->promotion_type);
    $tg_link = $tg_link[0]['domain'];
    header('Location: '.$tg_link);
}

//推广链接
if ($is_tg) {
    //短域名推广
    if (trim($uri,'/') == '') {
        $url = $config['promotion_api'].'?info=promotion&domain='.$domain;
    } else{
        $pid_str = explode('/',$uri);
        $pid_str = str_replace('.html','',end($pid_str));
        if ($pid_str == 'index') { //兼容旧的规则的推广链接
            $folder = str_replace('/index.php/','',$uri);
            $folder = str_replace('/index.html','',$folder);
            $pid = getFolderPid($folder);
        }else {
            $pid = digital_encrypt($pid_str, 16, 'D');
        }
        if (!$pid) {
            die('static_error 4004');
        }
        $url = $config['promotion_api'].'?info=promotion&pid='.$pid;
    }
    $ret = curl_get($url);
    $ret = json_decode($ret);
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if (stristr($user_agent, 'windows')) {
        if (isset($ret->data->info->is_pc_show) && $ret->data->info->is_pc_show == 1) {
            $pc_url = $ret->data->info->pc_url;
            if (stripos($ret->data->info->pc_url,'http://') === false && stripos($ret->data->info->pc_url,'https://') === false) {
                $pc_url = 'http://'.$pc_url;
            };
            header('Location:' .$pc_url);
            exit();
        }
    }
    if ($ret->ret != 200 ) {
        die('static_error_ret '.$ret->msg);
    }
    if (trim($uri,'/') == '') {
        $pid = $ret->data->info->promotion->id;
    }
    $folder = getPromotionFolder($pid);
    $page = $ret->data->info;
    $wexin = $page->wexin;
    $wechat = $wexin->wechat_id;
    $wechat_img = $wexin->weixin_img;
    $file = ROOT_PATH.$config['promotion_path'].$folder.'/index.php';
    if (!file_exists($file)) {
        die('static_error 4003');
    }
    include ($file);
    include ('common.php');
    exit();
}
