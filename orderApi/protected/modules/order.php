<?php
/**
 * Created by PhpStorm.
 * User: yjh
 * Date: 2017/10/12
 * Time: 13:48
 */


/**
 * 订单下单处理类
 * author: yjh
 */
class Order
{
    public $dbm;
    public $config;
    public $auth = 'zk2bu!@#';

    function __construct($dbm, $config)
    {
        $this->dbm = $dbm;
        $this->config = $config;
    }

    /**
     * 下单处理
     * author: yjh
     */
    public function index()
    {
        if ($this->auth_request()) {
            $params = $_POST['param'];
            $package_name = $params[0];
            $package_price = $params[1];
            $real_name = $params[2];
            $mobile = $params[3];
            $package_id = $params[5];
            $detail_area = $params[6];
            $best_time = $params[7];
            $remark = $params[8];
            $promotion_id = $params[9];
            $channel_id = $params[10];
            $partner_id = $params[11];
            $article_id = $params[12];
            $order_tid = $params[13];
            $wechat_id = $params[14];
            $article_code = $params[15];
            $weixin_id = $params[16];
            $customer_service_id = $params[17];
            $promotion_staff_id = $params[18];
            $goods_id = $params[19];
            $business_type = $params[20];
            $charging_type= $params[21];
            $ip = $params[22] ? $params[22] : $this->getIPaddress();
            $address = explode('  ', $params[4]);
            //获取省市区
            if (count($address) == 2) {
                $province = '';
                $city = $address[0];
                $region = $address[1];
            } else {
                $province = $address[0];
                $city = $address[1];
                $region = $address[2];
            }
            if (!$this->is_mobile($mobile)) {
                $ret = array('status' => -2, 'msg' => '手机号错误！');
                echo json_encode($ret);
                exit;
            }
            if ($promotion_id == 0 || $wechat_id == "预览微信号") {
                //预览下单，模拟下单成功
                $ret = array('status' => -1, 'msg' => '这里是预览图文下单，预览下单成功！');
                echo json_encode($ret);
                exit;
            }
            //订单去重1、同一天同一手机号同一渠道只有一条订单，以最后一条为准,未导出
            $today = date('Ymd');
            $condition = " mobile=$mobile and o_date=$today and promotion_id=$promotion_id and is_export=0  ";
            $orderInfoTemp = $this->dbm
                ->where($condition)
                ->limit(1)
                ->select('order_manage');
            $time = 0;
            if ($orderInfoTemp) {
                $time = $orderInfoTemp[0]['time'];
            }
            $order_code = $this->get_order_code();

            //事务
            $this->dbm->startTrans();
            $orderInfo = array(
                'time' => $time + 1,//同一用户同一天下单次数
                'package_name' => $package_name,//下单套餐
                'package_price' => $package_price,//套餐价格
                'package_id' => $package_id,//套餐id
                'real_name' => $real_name,//用户姓名
                'mobile' => $mobile,//手机
                'ip' => $ip,//ip
                'best_time' => $best_time,//适合打电话时间
                'promotion_id' => $promotion_id,//推广id
                'channel_id' => $channel_id,//渠道id
                'partner_id' => $partner_id,//合作商id
                'customer_service_id' => $customer_service_id,//客服id
                'order_code' => $order_code,//订单编号
                'corder_code' => '',//客服订单号
                'order_status' => 0,//客服状态
                'is_export' => 0,//是否已导出
                'add_time' => time(),//下单时间
                'update_time' => time(),//更新时间
                'o_date' => date('Ymd', time()),//下单日期
                'o_hour' => date('YmdH', time()),//下单日期
                'order_type' => 0,//下单类型（正常下单）
                'promotion_staff_id' => $promotion_staff_id,//推广人员
                'goods_id' => $goods_id,
            );

            $ret = $this->dbm->insert('order_manage', $orderInfo);
            //$order_id = $this->dbm->dosql("select LAST_INSERT_ID()");
            $order_id = $this->dbm->lastInsertId();
            if (!$ret) {
                //插入失败 回滚数据
                $this->dbm->rollback();
                $ret = array('status' => -3, 'msg' => '插入主表出错,下单失败！');
                echo json_encode($ret);
                exit;
            }
            if ($order_id) {
                $orderGoods=array(
                    'order_id' => $order_id,
                    'package_id' => $package_id,//套餐id
                    'package_name' => $package_name,//下单套餐
                    'package_price' => $package_price,//套餐价格
                    'nums'=>1,//数量
                    'update_time'=>time(),
                );
                $orderAssistant = array(
                    'order_id' => $order_id,
                    'province' => $province,//省
                    'city' => $city,//城市
                    'region' => $region,//区
                    'detail_area' => $detail_area,//详细地址
                    'remark' => $remark,//备注
                    'article_id' => $article_id,//图文id
                    'article_code' => $article_code,//图文编码
                    'order_tid' => $order_tid,//下单模板id
                    'weixin_id' => $weixin_id,//微信id
                    'wechat_id' => $wechat_id,//微信号
                    'device' => $this->check_wap(),//下单设备
                    'business_type' => $business_type,//业务类型
                    'charging_type' => $charging_type,//计费方式
                );
            }
            if ($ret) {
                $ret = $this->dbm->insert('order_assistant', $orderAssistant);
            }
            if (!$ret) {
                //插入失败 回滚数据
                $this->dbm->rollback();
                $ret = array('status' => -4, 'msg' => '插入商品表出错,下单失败！');
                echo json_encode($ret);
                exit;
            }
            if ($ret) {
                $ret = $this->dbm->insert('order_goods_manage', $orderGoods);
            }
            if (!$ret) {
                //插入失败 回滚数据
                $this->dbm->rollback();
                $ret = array('status' => -5, 'msg' => '插入辅表出错,下单失败！');
                echo json_encode($ret);
                exit;
            }
            //删除上一条订单数据
            if ($ret && $orderInfoTemp) {
                $this->dbm->where('id=' . $orderInfoTemp[0]['id'])->limit(1)->delete('order_manage');
                $this->dbm->where('order_id=' . $orderInfoTemp[0]['id'])->limit(1)->delete('order_assistant');
                $this->dbm->where('order_id=' . $orderInfoTemp[0]['id'])->limit(1)->delete('order_goods_manage');
            }

            $this->dbm->commit();

            //支付

            if (!$ret) {
                $ret = array('status' => 0, 'msg' => '下单失败！');
                echo json_encode($ret);
            } else {
                $ret = array('status' => 1,'orderCode'=>$order_code, 'msg' => '下单成功！');
                echo json_encode($ret);
            }
            exit;
        } else {
            $ret = array('status' => 2, 'msg' => '未知请求！');
            echo json_encode($ret);
            exit;
        }

    }


    /**
     * 得到新订单号
     * @return  string
     */
    function get_order_code()
    {
        $topnum = date("YmdHis");
        $mtime = substr(microtime(), 2, 4);
        $currenttime = rand(0, 9);
        return 'm'.$topnum . $mtime . $currenttime;
    }

    /**
     * 请求确认是否是正常请求
     * @return bool
     * author: yjh
     */
    function auth_request()
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
     * 判断是否为手机号
     * @param $mobile
     * @return bool
     * author: yjh
     */
    function is_mobile($mobile)
    {
        if (preg_match("/^1[3456789]{1}\d{9}$/", $mobile)) {
            return true;
        } else {
            return false;
        }
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

    /**
     * 获取 IP  地理位置
     * 淘宝IP接口
     * @Return: array
     */
    function getCity($ip = '')
    {
        if ($ip == '') {
            $url = "http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json";
            $ip = json_decode(file_get_contents($url), true);
            $data = $ip['province'] . $ip['city'];
        } else {
            $url = "http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip;
            $ipinfo = json_decode(file_get_contents($url));
            if ($ipinfo->code == '1') {
                return false;
            }
            $data = $ipinfo->data->region . $ipinfo->data->city;

        }

        return $data;
    }

    /**
     * 判断是手机访问pc访问
     */
    protected function check_wap()
    {
        if (isset($_SERVER['HTTP_VIA'])) {
            return true;
        }
        if (isset($_SERVER['HTTP_X_NOKIA_CONNECTION_MODE'])) {
            return true;
        }
        if (isset($_SERVER['HTTP_X_UP_CALLING_LINE_ID'])) {
            return true;
        }
        if (strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML") > 0) {
            // Check whether the browser/gateway says it accepts WML.
            $br = "WML";
        } else {
            $browser = isset($_SERVER['HTTP_USER_AGENT']) ? trim($_SERVER['HTTP_USER_AGENT']) : '';
            if (empty($browser)) {
                return true;
            }
            $mobile_os_list = array('Google Wireless Transcoder', 'Windows CE', 'WindowsCE', 'Symbian', 'Android', 'armv6l', 'armv5', 'Mobile', 'CentOS', 'mowser', 'AvantGo', 'Opera Mobi', 'J2ME/MIDP', 'Smartphone', 'Go.Web', 'Palm', 'iPAQ');

            $mobile_token_list = array('Profile/MIDP', 'Configuration/CLDC-', '160×160', '176×220', '240×240', '240×320', '320×240', 'UP.Browser', 'UP.Link', 'SymbianOS', 'PalmOS', 'PocketPC', 'SonyEricsson', 'Nokia', 'BlackBerry', 'Vodafone', 'BenQ', 'Novarra-Vision', 'Iris', 'NetFront', 'HTC_', 'Xda_', 'SAMSUNG-SGH', 'Wapaka', 'DoCoMo', 'iPhone', 'iPod');

            $found_mobile = $this->checkSubstrs($mobile_os_list, $browser) || $this->checkSubstrs($mobile_token_list, $browser);
            if ($found_mobile) {
                $br = "WML";
            } else {
                $br = "WWW";
            }
        }
        if ($br == "WML") {
            return 'Mobile';
        } else {
            return 'PC';
        }
    }

    /**
     * 判断手机访问， pc访问
     */
    protected function checkSubstrs($list, $str)
    {
        $flag = false;
        for ($i = 0; $i < count($list); $i++) {
            if (strpos($str, $list[$i]) > 0) {
                $flag = true;
                break;
            }
        }
        return $flag;
    }
    public  function curl_request($url, $post = '')
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_REFERER, "http://XXX");
        if ($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            return curl_errno($curl);
        }
        curl_close($curl);

        return $data;

    }
}
