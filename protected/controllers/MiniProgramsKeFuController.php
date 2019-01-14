<?php
/**
 * Created by PhpStorm.
 * User: yjh
 * Date: 2017/10/24
 * Time: 15:32
 */
header('Content-type:text');
define("TOKEN", "xmzk20171025");

class MiniProgramsKeFuController extends HomeController
{

    //校验服务器地址URL
    public function actionCheckServer()
    {
        if (isset($_GET['echostr'])) {
            $this->valid();
        } else {
            $this->responseMsg();
        }
    }

    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if ($this->checkSignature()) {
            header('content-type:text');
            echo $echoStr;
            exit;
        } else {
            echo $echoStr . '+++' . TOKEN;
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    protected function responseMsg()
    {
        $postStr  = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        if (!empty($postStr) && is_string($postStr)) {
            //禁止引用外部xml实体
            //libxml_disable_entity_loader(true);
            //$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $postArr = json_decode($postStr, true);
            if (!empty($postArr['MsgType']) && $postArr['MsgType'] == 'text') {   //文本消息
                $fromUsername = $postArr['FromUserName'];   //发送者openid
                $toUserName = $postArr['ToUserName'];       //小程序id
                $textTpl = array(
                    "ToUserName" => $fromUsername,
                    "FromUserName" => $toUserName,
                    "CreateTime" => time(),
                    "MsgType" => "transfer_customer_service",
                );
                exit(json_encode($textTpl));
            } elseif (!empty($postArr['MsgType']) && $postArr['MsgType'] == 'image') { //图文消息
                $fromUsername = $postArr['FromUserName'];   //发送者openid
                $toUserName = $postArr['ToUserName'];       //小程序id
                $textTpl = array(
                    "ToUserName" => $fromUsername,
                    "FromUserName" => $toUserName,
                    "CreateTime" => time(),
                    "MsgType" => "transfer_customer_service",
                );
                exit(json_encode($textTpl));
            } elseif ($postArr['MsgType'] == 'event' && $postArr['Event'] == 'user_enter_tempsession') { //进入客服动作
                //发送信息
                $output = $this->sendMsg($postStr);
                if ($output == 0) {
                    echo 'success';
                    exit;
                }

            } else {
                exit('aaa');
            }
        } else {
            echo "";
            exit;
        }
    }

    /**
     * 发送图片
     * @param $postStr string 请求数据
     * author: yjh
     */
    protected function sendMsg($postStr)
    {
        $postArr = json_decode($postStr, true);
        $openId = $postArr['FromUserName'];   //发送者openid
        $origin_id = $postArr['ToUserName'];   //小程序原始id
        $redis_flag = Yii::app()->params['basic']['is_redis'];
        if ($redis_flag == 1) {
            $t_info = Yii::app()->redis->getValue('miniAppName:' . $origin_id . ':openId:' . $openId);
        } else {
            $cache = Yii::app()->cache;
            $t_info = $cache->get($origin_id . ":" . $openId);
        }
        //判断是否一分钟内重复触发，只执行一次
        if ($t_info) {
            echo 'error';
            exit;
        }

        if ($redis_flag == 1) {
            $minAppInfo = Yii::app()->redis->getValue('miniAppName:' . $origin_id . ':minAppInfo');
            if (!$minAppInfo) {
                $sql = "SELECT id,kefu_type,kefu_content FROM mini_apps_manage where origin_id='" . $origin_id . "'";
                $minAppInfo = Yii::app()->db->createCommand($sql)->queryAll();
                Yii::app()->redis->setValue('miniAppName:' . $origin_id . ':minAppInfo', $minAppInfo, 86400);
            }
        }else {
            $sql = "SELECT id,kefu_type,kefu_content FROM mini_apps_manage where origin_id='" . $origin_id . "'";
            $minAppInfo = Yii::app()->db->createCommand($sql)->queryAll();
        }

        //获取图片media_id 先获取小程序名称获取微信号小组，随机取一张二维码
        if ($redis_flag == 1) {
            $wechatsInfo = Yii::app()->redis->getValue('miniAppName:' . $origin_id . ':wechatInfo');
            if (!$wechatsInfo) {
                $sql = "SELECT b.id,b.wechat_id,c.resource_url FROM mini_apps_manage AS a 
                         LEFT JOIN wechat_relation AS w ON a.wechat_group_id=w.wechat_group_id 
                         LEFT JOIN wechat AS b ON b.id=w.wid 
                         LEFT JOIN resource_list AS c ON c.resource_id=b.qrcode_id 
                         where a.origin_id='" . $origin_id . "'";
                $wechatsInfo = Yii::app()->db->createCommand($sql)->queryAll();
                Yii::app()->redis->setValue('miniAppName:' . $origin_id . ':wechatInfo', $wechatsInfo, 86400);
            }
        } else {
            $sql = "SELECT b.id,b.wechat_id,c.resource_url FROM mini_apps_manage AS a 
                         LEFT JOIN wechat_relation AS w ON a.wechat_group_id=w.wechat_group_id 
                         LEFT JOIN wechat AS b ON b.id=w.wid 
                         LEFT JOIN resource_list AS c ON c.resource_id=b.qrcode_id 
                         where a.origin_id='" . $origin_id . "'";
            $wechatsInfo = Yii::app()->db->createCommand($sql)->queryAll();
        }

        //获取media_id 从redis获取 缓存三天
        $wechatNum=count($wechatsInfo)-1;
        $randKey = mt_rand(0,$wechatNum);
//        $randKey = array_rand($wechatsInfo, 1);
        $wechat_id = $wechatsInfo[$randKey]['id'];
        //0发送图片，1发送文字
        if($minAppInfo[0]['kefu_type']==0){
            $media_id = '';
            if ($redis_flag == 1) {
                $media_id = Yii::app()->redis->getValue('miniAppName:' . $origin_id . ':media_id:' . $wechat_id);
            }
            if (!$media_id) {
                $imgPath = $wechatsInfo[$randKey]['resource_url'];
                $media_id = $this->get_media_id($imgPath, $origin_id);
                if ($redis_flag == 1) Yii::app()->redis->setValue('miniAppName:' . $origin_id . ':media_id:' . $wechat_id, $media_id, 86300 * 3);
            }
            $output=$this->send_img($media_id,$openId,$origin_id);
        }else{
            $content='';
            if ($redis_flag == 1) {
                $content = Yii::app()->redis->getValue('miniAppName:' . $origin_id . ':content:' . $wechat_id);
            }
            if (!$content) {
                $content = $minAppInfo[0]['kefu_content'];
                $content=str_replace('{{wechat}}',$wechatsInfo[$randKey]['wechat_id'],$content);
                if ($redis_flag == 1) Yii::app()->redis->setValue('miniAppName:' . $origin_id . ':content:' . $wechat_id, $content, 86300 * 3);
            }
            $output=$this->send_text($content,$openId,$origin_id);
        }
        if ($redis_flag == 1) Yii::app()->redis->setValue('miniAppName:' . $origin_id . ':openId:' . $openId, 1, 60);
        else $cache->set($origin_id . ":" . $openId, 1, 60);
        $appid=$minAppInfo[0]['id'];
        $this->clicks_stat($appid);
        return $output;

    }

    /**
     * 发送文字
     * @param  $content
     * @param $openId
     * @param $origin_id
     * author: yjh
     */
    protected function send_text($content,$openId,$origin_id){
        $data = array(
            "touser" => $openId,
            "msgtype" => "text",
            "text" => array("content" => $content)
        );
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);  //php5.4+

        $access_token = $this->get_accessToken($origin_id);
        /*
        * POST发送https请求客服接口api
        */
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" . $access_token;
        //以'json'格式发送post的https请求
        $output=$this->curl_post($url,$json);
        return $output;
    }
    /**
     * 发送图片
     * @param $media_id
     * @param $openId
     * @param $origin_id
     * @return mixed
     * author: yjh
     */
    protected function send_img($media_id,$openId,$origin_id){
        if (!$media_id) {
            echo "can't get media_id";
            exit;
        }

        $data = array(
            "touser" => $openId,
            "msgtype" => "image",
            "image" => array("media_id" => $media_id)
        );
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);  //php5.4+

        $access_token = $this->get_accessToken($origin_id);

        /*
         * POST发送https请求客服接口api
         */
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" . $access_token;
        //以'json'格式发送post的https请求
        $output=$this->curl_post($url,$json);
        return $output;
    }


    /* 调用微信api，获取access_token，有效期7200s -xzz0704 */
    protected function get_accessToken($origin_id)
    {
        $cache = Yii::app()->cache;
        $access_token = $cache->get($origin_id . ':access_token');
        /* 在有效期，直接返回access_token */
        if ($access_token) {
            return $access_token;
        } /* 不在有效期，重新发送请求，获取access_token */
        else {
            $sql="select appid,secret from mini_apps_manage where origin_id='".$origin_id."'";
            $info=Yii::app()->db->createCommand($sql)->queryAll();
            $appid = $info[0]['appid'];
            $secret = $info[0]['secret'];
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。

            $result = curl_exec($ch);
            // 检查是否有错误发生
            if (curl_errno($ch)) {
                return 'Errno' . curl_error($ch);//捕抓异常
            }
            curl_close($ch);
            $res = json_decode($result, true);   //json字符串转数组
            if ($res) {
                $cache->set('access_token', $res['access_token'], 7100);
                return $cache->get('access_token');
            } else {
                return 'api return error';
            }
        }
    }

    /**
     * 获取media_id
     * @param $imgPath string 图片路径
     * @return string
     * author: yjh
     */
    protected function get_media_id($imgPath, $origin_id)
    {
        $imgPath = dirname(__FILE__) . '/../..' . $imgPath;
        $access_token = $this->get_accessToken($origin_id);

        $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token=' . $access_token . '&type=image';
        $ch = curl_init();
        $data = array('name' => 'Foo', 'media' => '@' . $imgPath);
        //兼容5.0-5.6版本的curl
        if (class_exists('\CURLFile')) {
            $data['media'] = new \CURLFile(realpath($imgPath));
        } else {
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($ch, CURLOPT_SAFE_UPLOAD, FALSE);
            }
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。

        $result = curl_exec($ch);

        // 检查是否有错误发生
        if (curl_errno($ch)) {
            return 'Errno' . curl_error($ch);//捕抓异常
        }
        curl_close($ch);//echo '--end<br>';
        $res = json_decode($result, true);   //json字符串转数组 = json_decode($result,true);   //json字符串转数组
        return $res['media_id'];
    }

    public function actionGetMediaId()
    {
        $origin_id = 'gh_69f43d920408';
        $imgPath = dirname(__FILE__) . '/../../static/img/default_avatar.jpg';//\static\img\default_avatar.jpg
        $access_token = $this->get_accessToken($origin_id);
        $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token=' . $access_token . '&type=image';
        $ch = curl_init();
        $data = array('name' => 'Foo', 'media' => '@' . $imgPath);
        //兼容5.0-5.6版本的curl
        if (class_exists('\CURLFile')) {
            $data['media'] = new \CURLFile(realpath($imgPath));
        } else {
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($ch, CURLOPT_SAFE_UPLOAD, FALSE);
            }
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。

        $result = curl_exec($ch);
        // 检查是否有错误发生
        if (curl_errno($ch)) {
            return 'Errno' . curl_error($ch);//捕抓异常
        }
        curl_close($ch);//echo '--end<br>';
        $res = json_decode($result, true);   //json字符串转数组 = json_decode($result,true);   //json字符串转数组
        var_dump($res);
    }

    public function actionGetAccessToken()
    {

        $appid = Yii::app()->params['miniApps']['appid'];
        $secret = Yii::app()->params['miniApps']['secret'];
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。

        $result = curl_exec($ch);
        echo $result;
    }

    private function curl_post($url,$json){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($json)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($curl, CURLOPT_HTTPHEADER, $headers );
        $output = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Errno' . curl_error($curl);//捕抓异常
        }
        curl_close($curl);
        return $output;
    }

    /**
     * 发送二维码储存点击次数
     * @param $app_id
     * @return bool
     * author: yjh
     */
    public function clicks_stat($app_id){
        $date=strtotime(date('Ymd',time()));
        $info=MiniAppsClicks::model()->find('miniapp_id='.$app_id.' and date='.$date);
        $mInfo=MiniAppsManage::model()->findByPk($app_id);
        $mInfo->click_num =$mInfo->click_num+1;
        $mInfo->save();
        if($info){
            $info->click_num=$info->click_num+1;
        }else{
            $info=new MiniAppsClicks();
            $info->click_num=1;
            $info->date=$date;
            $info->miniapp_id=$app_id;
        }
        $info->save();
        return true;

    }
}