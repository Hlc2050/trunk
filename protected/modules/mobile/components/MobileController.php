<?php
/**
 * Created by PhpStorm.
 * User: fang
 * Date: 2017/10/24
 * Time: 11:25
 */

class MobileController extends CController
{
    public  $appid;
    public  $appsecret;
    public  $openid=-1;

    public function init(){
        parent::init();
        //先获取用户的openid 写一个获取openid的接口
        //判断是否是从手机微信端登录
        //是否授权微信
        $this->appid = Yii::app()->params['wechat']['appid'];
        $this->appsecret = Yii::app()->params['wechat']['appsecret'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($user_agent, 'MicroMessenger') !== false) {
            $this->openid=$this->get_openid();
        }

    }
    //获取openid
    public function get_openid(){
        $openid=$_SESSION['mobile_openid'];
        $access_token=$_SESSION['mobile_token'];
        if(empty($openid) || empty($access_token)) {
            $state = $_GET['state'];
            if (empty($state)) {
                $now_url = urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
                $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->appid}&redirect_uri=$now_url&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirects";
                header('location:' . $url);
                exit;
            }
            $code = $_GET['code'];
            //获取openid
            $token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->appid}&secret={$this->appsecret}&code={$code}&grant_type=authorization_code";
            $res =  helper::httpsGet($token_url);
            $token = json_decode($res);
            $openid = $token->openid;
            $access_token=$token->access_token;
            session_start();
            $_SESSION['mobile_openid'] = $openid;
            $_SESSION['mobile_token'] = $access_token;
        }
        $snsapi_userinfo=$this->get_snsapi_userinfo($access_token,$openid);
        if($snsapi_userinfo)$_SESSION['snsapi_userinfo']=$snsapi_userinfo;

        return $openid;
    }

    //拉取用户信息
    public function get_snsapi_userinfo($access_token,$openid){
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';
        $snsapi_userinfo = helper::httpsGet($url);

        return json_decode($snsapi_userinfo,true);
    }




}
