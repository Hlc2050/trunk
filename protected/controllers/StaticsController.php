<?php
date_default_timezone_set('Asia/Shanghai');

class StaticsController extends HomeController
{
    function actionOpenld(){
        $t = $_POST['t'];
        $t1 = substr($t, 10);
        $pid = intval(rtrim($t1, substr($t1, -5)));
        if($pid < 1){
            exit;
        }
        $url = $_SERVER['HTTP_HOST'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $key = 'jump_ground_visit_queue';  //value 推广ID_类型（1白名单 2goto 3落地 4文章渲染后）_时间_IP_UA_当前URL
        $now_request_url = 'http://'.$url.$_SERVER['REQUEST_URI'];
        Yii::app()->redis->lpush($key, $pid.'_:_4_:_'.time().'_:_'.helper::getip().'_:_'.$user_agent.'_:_'.$now_request_url);
    }
}