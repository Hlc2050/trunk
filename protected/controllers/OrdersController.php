<?php

class OrdersController extends HomeController
{
    private $auth = 'zk2bu!@#';
    public function actionIndex(){
        echo 232;
    }
    /*
     * 直接下单
     * author:yjh
     */
    public function actionPlaceOrder()
    {

        if ($this->auth_request()) {
            //订单日志记录
            $ip = $this->getIPaddress();
            $data = $_POST;
            if(!$this->filterParams($data['param'])){
                $retStr=serialize($data['param']);
                $this->insertFailInfo($data['param'][3],long2ip($ip),$retStr,'参数不全');
                $ret = array('status' => 5, 'msg' => '参数不全！');
                echo json_encode($ret);
                exit;
            }
            $wechatInfo=WeChat::model()->findByPk($data['param'][16]);
            $data['param'][19]=$wechatInfo->goods_id;
            $data['param'][20]=$wechatInfo->business_type;
            $data['param'][21]=$wechatInfo->charging_type;

            $data['param'][22] = $ip;
            $phoneBlackList= BlackListPhone::model()->getPhoneBlackList();
            $ipBlackList= BlackListIp::model()->getIpBlackList();
            if(in_array($data['param'][3],$phoneBlackList) ||in_array(long2ip($ip),$ipBlackList)){
                $ret = array('status' => 6, 'msg' => '黑名单！');
                $ret = json_encode($ret);
                $data['type']='黑名单';
                $retStr=serialize($data['param']);
                //黑名单，订单缓存
                $this->insertFailInfo($data['param'][3],long2ip($ip),$retStr,'黑名单');
                echo json_encode($ret);
                exit;
            }

            $url = Yii::app()->params['basic']['order_url'];
            $ret = helper::curl_request($url, $data);

            if (is_numeric($ret)) {
                $ret = array('status' => 3, 'msg' => '接口请求失败！');
                $ret = json_encode($ret);
                $retStr=serialize($data['param']);
                $this->insertFailInfo($data['param'][3],long2ip($ip),$retStr,'接口请求失败');

            }
            echo $ret;

            exit;
        } else {
            $ret = array('status' => 2, 'msg' => '未知请求！');
            echo json_encode($ret);
            exit;
        }
    }

    private function insertFailInfo($mobile,$ip,$info,$fail){
        //接口请求失败，订单缓存
        $failInfo= new FailOrderLogs();
        $failInfo->mobile=$mobile;
        $failInfo->ip=$ip;
        $failInfo->add_time=time();
        $failInfo->other_info=$info;
        $failInfo->fail_info=$fail;
        $failInfo->save();
        return 0;
    }

    /**
     * 过滤参数
     * author: yjh
     */
    private function filterParams($data){

        if(empty($data[0])) return false;
        if(empty($data[2])) return false;
        if(empty($data[5])) return false;
        if(empty($data[6])) return false;
        if(empty($data[9])) return false;
        if(empty($data[10])) return false;
        if(empty($data[11])) return false;
        if(empty($data[12])) return false;
        if(empty($data[13])) return false;
        if(empty($data[15])) return false;
        if(empty($data[16])) return false;
        if(empty($data[17])) return false;
        if(empty($data[18])) return false;

        return true;
    }

    /**
     * 请求确认是否是正常请求
     * @return bool
     * author: yjh
     */
    private function auth_request()
    {
        //是否为POST请求
        $is_post = isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'POST';
        if ($is_post) {
            if ($_POST && $_POST['auth'] == $this->auth) {
                return true;
            }
        }
        return false;
    }

    /**
     * 获取用户真实ip
     * @return string
     * author: yjh
     */
    public function getIPaddress()
    {
        $realip = '';
        $unknown = 'unknown';
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach ($arr as $ip) {
                    $ip = trim($ip);
                    if ($ip != 'unknown') {
                        $realip = $ip;
                        break;
                    }
                }
            } else if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']) && strcasecmp($_SERVER['HTTP_CLIENT_IP'], $unknown)) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else if (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)) {
                $realip = $_SERVER['REMOTE_ADDR'];
            } else {
                $realip = $unknown;
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), $unknown)) {
                $realip = getenv("HTTP_X_FORWARDED_FOR");
            } else if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), $unknown)) {
                $realip = getenv("HTTP_CLIENT_IP");
            } else if (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), $unknown)) {
                $realip = getenv("REMOTE_ADDR");
            } else {
                $realip = $unknown;
            }
        }
        $realip = preg_match("/[\d\.]{7,15}/", $realip, $matches) ? $matches[0] : $unknown;
        return ip2long($realip);
    }



}