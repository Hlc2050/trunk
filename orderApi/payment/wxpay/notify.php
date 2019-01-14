<?php
/**
 * Created by PhpStorm.
 * User: yjh
 * Date: 2018/3/27
 * Time: 14:02
 */
header('Content-type:text/html; Charset=utf-8');
require_once __DIR__ . '/../../framework/db/dbMysql.php';
$config = require_once __DIR__ . '/../../config/config.php';
$dbm = new dbMysql($config['db_mysql']['default']);
// 公共配置
$wxConfig = require_once __DIR__ . '/wxconfig.php';
$wxPay = new WxPayService($wxConfig, $dbm);
$wxPay->writeLog(var_export($wxPay->xml2arr($xml), true));
$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
$data=$wxPay->xml2arr($postStr);
$result = $wxPay->notify();
if($result){
    //完成你的逻辑
    //例如连接数据库，获取付款金额$result['cash_fee']，获取订单号$result['out_trade_no']，修改数据库中的订单状态等;
    $order_code = $data['out_trade_no'];
    //out_trade_no 查找订单 插入支付状态以及trade_no
    $orderInfo = $aliPay->getOrderInfo($order_code);
    if ($orderInfo) {
        $update_data=array(
            'pay_status'=>1,
            'payment'=>1,
            'trade_no'=>$data['transaction_id'],//微信支付订单号

        );
        $aliPay->updateOrderInfo($order_code,$update_data);
        $data=array(
            'out_trade_no'=>$order_code,
            'trade_no'=>$data['transaction_id'],
            'trade_status'=>$_POST['result_code'],
            'order_id'=>$orderInfo[0]['id'],
            'operate_time'=>time(),
            'payment'=>1,
        );
        $aliPay->insertOrderBill($data);

    }
}else{
    echo 'pay error';
}


class WxPayService
{
    protected $appid;//公众账号ID
    protected $mch_id;//商户号
    protected $key;//自己设置的微信商家key
    protected $sign_type;//签名类型

    public function __construct($wxConfig, $dbm)
    {
        $this->dbm = $dbm;
        $this->appid = $wxConfig['appid'];
        $this->mch_id = $wxConfig['mch_id'];
        $this->key = $wxConfig['key'];
        $this->sign_type = $wxConfig['sign_type'];
    }

    //将xml转为数组
    public function xml2arr($xml)
    {
        libxml_disable_entity_loader(true);//禁止引用外部xml实体
        $objectxml = (array)simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA); //将微信返回的XML 转换成数组
        if ($objectxml === false) {
            die('parse xml error');
        }
        $arrxml = json_decode(json_encode($objectxml), true);
        return $arrxml;
    }

    public function notify()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($postObj === false) {
            die('parse xml error');
        }
        if ($postObj->return_code != 'SUCCESS') {
            die($postObj->return_msg);
        }
        if ($postObj->result_code != 'SUCCESS') {
            die($postObj->err_code);
        }
        $arr = (array)$postObj;
        unset($arr['sign']);
        if (self::getSign($arr, $this->key) == $postObj->sign) {
            echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
            return $arr;
        }
    }
    /**
     * 获取签名
     */
    public static function getSign($params, $key)
    {
        ksort($params, SORT_STRING);
        $unSignParaString = self::parseSignData($params, false);
        $signStr = strtoupper(md5($unSignParaString . "&key=" . $key));
        return $signStr;
    }
    protected static function parseSignData($paraMap, $urlEncode = false)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if (null != $v && "null" != $v) {
                if ($urlEncode) {
                    $v = urlencode($v);
                }
                $buff .= $k . "=" . $v . "&";
            }
        }
        $reqPar = '';
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }



    function getOrderInfo($order_code)
    {
        $condtion = "order_code='" . $order_code . "'";
        //out_trade_no 查找订单 插入支付状态以及trade_no
        $orderInfo = $this->dbm
            ->where($condtion)
            ->limit(1)
            ->select('order_manage');
        return $orderInfo;
    }

    function updateOrderInfo($order_code, $update_data)
    {
        $condtion = "order_code='" . $order_code . "'";
        //out_trade_no 查找订单 插入支付状态以及trade_no
        $ret = $this->dbm->where($condtion)->update('order_manage', $update_data);
        return $ret;
    }

    function insertOrderBill($data)
    {
        $ret = $this->dbm->insert('order_bill_detail', $data);
        return $ret;
    }

    //请确保项目文件有可写权限，不然打印不了日志。
    function writeLog($text)
    {
        // $text=iconv("GBK", "UTF-8//IGNORE", $text);
        //$text = characet ( $text );
        file_put_contents('/data/2bu/wxpaylogs/notifylog.txt', date("Y-m-d H:i:s") . "  " . $text . "\r\n", FILE_APPEND);
    }
}
