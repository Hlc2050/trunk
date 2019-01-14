<?php
/**
 * Created by PhpStorm.
 * User: yjh
 * Date: 2018/3/27
 * Time: 14:02
 */

/**
 * 微信H5支付Demo
 */
date_default_timezone_set('Asia/Shanghai');

// 公共配置
$wxConfig = require_once __DIR__ . '/wxconfig.php';
// SDK实例化，传入公共配置
$wxPay = new WxPayService($wxConfig);


$out_trade_no = 'test' . mt_rand(10000000, 99999999); // 订单号
$order_data = array(
    'body' => 'test', // 商品描述
    'out_trade_no' => $out_trade_no, // 订单号
    'nonce_str' => MD5($out_trade_no), //随机字符串
    'total_fee' => 1, //订单总金额，单位为：分
    'spbill_create_ip' => $_SERVER["REMOTE_ADDR"], // 客户端ip，必须传正确的用户ip，否则会报错
    'scene_info' => '{"h5_info":{"type":"Wap","wap_url":"http://www.baidu.com","wap_name":"支付"}}',//场景信息 必要参数
);
$sHtml = $wxPay->doPay();
echo $sHtml;
exit;
class WxPayService
{
    protected $appid;//公众账号ID
    protected $mch_id;//商户号
    protected $key;//自己设置的微信商家key
    protected $pay_notify_url;//通知地址
    protected $pay_return_url;//跳转地址
    protected $trade_type;//交易类型
    protected $sign_type;//签名类型
    protected $pay_api;//接口地址

    protected $nonce_str;//随机字符串
    protected $sign;//签名

    protected $total_fee;//标价金额
    protected $out_trade_no;//商户订单号
    protected $body;//商品描述
    protected $spbill_create_ip;//终端IP
    protected $scene_info;//场景信息
    protected $limit_pay;//指定支付方式



    public function __construct($wxConfig)
    {
        $this->appid = $wxConfig['appid'];
        $this->mch_id = $wxConfig['mch_id'];
        $this->key = $wxConfig['key'];
        $this->limit_pay = $wxConfig['limit_pay'];
        $this->pay_notify_url = $wxConfig['pay_notify_url'];
        $this->trade_type = $wxConfig['trade_type'];
        $this->sign_type = $wxConfig['sign_type'];
        $this->fee_type = $wxConfig['fee_type'];
        $this->pay_return_url =$_SERVER['HTTP_REFERER'];


    }

    public function setOrderInfo($order_data)
    {
        $this->body = $order_data['body'];
        $this->total_fee = $order_data['total_fee'];
        $this->out_trade_no = $order_data['out_trade_no'];
        $this->nonce_str = $order_data['nonce_str'];
        $this->spbill_create_ip = $order_data['spbill_create_ip'];
        $this->scene_info = $order_data['scene_info'];
        $this->sign = $this->buildSign();
    }

    protected function buildSign()
    {
        $signA = "appid=$this->appid&body=$this->body&mch_id=$this->mch_id&nonce_str=$this->nonce_str&notify_url=$this->pay_notify_url&out_trade_no=$this->out_trade_no&scene_info=$this->scene_info&spbill_create_ip=$this->spbill_create_ip&total_fee=$this->total_fee&trade_type=$this->trade_type";
        $strSignTmp = $signA . "&key=$this->key"; //拼接字符串 注意顺序微信有个测试网址 顺序按照他的来 直接点下面的校正测试 包括下面XML 是否正确
        $sign = strtoupper(MD5($strSignTmp)); // MD5 后转换成大写
        return $sign;
    }

    //拼接成XML格式 *XML格式文件要求非常严谨不能有空格这点一定要注意
    protected function toXML()
    {
        $post_data = "<xml><appid>$this->appid</appid><body>$this->body</body><mch_id>$this->mch_id</mch_id><nonce_str>$this->nonce_str</nonce_str><notify_url>$this->pay_notify_url</notify_url><out_trade_no>$this->out_trade_no</out_trade_no><scene_info>$this->scene_info</scene_info><spbill_create_ip>$this->spbill_create_ip</spbill_create_ip><total_fee>$this->total_fee</total_fee><trade_type>$this->trade_type</trade_type><sign>$this->sign</sign></xml>";
        return $post_data;
    }


    public function doPay(){
        $headers = array();
        $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $headers[] = 'Connection: Keep-Alive';
        $headers[] = 'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3';
        $headers[] = 'Accept-Encoding: gzip, deflate';
        $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:22.0) Gecko/20100101 Firefox/22.0';

        $post_data=$this->toXML();
        $url=$this->pay_api;
        $dataxml = $this->http_post($url, $post_data, $headers);//传参调用curl请求
        libxml_disable_entity_loader(true);//禁止引用外部xml实体
        $objectxml = (array)simplexml_load_string($dataxml, 'SimpleXMLElement', LIBXML_NOCDATA); //将微信返回的XML 转换成数组
        $arrxml=json_decode(json_encode($objectxml),true);
        //记录日志
        if($arrxml['return_code']=='SUCCESS'){
            if($arrxml['result_code']=='SUCCESS'){
                $this->writeLog('订单成功返回内容'.var_export($arrxml,true));
                //业务成功，返回mweb_url
                $mweb_url = $arrxml['mweb_url'];
                $mweb_url .= "&redirect_url=".$this->pay_return_url;
                $ret= "<script language='JavaScript' type='text/javascript'>";
                $ret.= "window.location.href='".$mweb_url."'";
                $ret .= "</script>";
            }else{
                $ret=$arrxml['err_code_des'];//错误信息
                $this->writeLog('订单失败返回内容'.$ret);
            }
        }else{
            $ret=$arrxml['return_msg'];//错误信息
            $this->writeLog('订单失败返回内容'.$ret);
        }
        return $ret;

    }



    public function http_post($url = '', $post_data = array(), $header = array(), $timeout = 30)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

    //请确保项目文件有可写权限，不然打印不了日志。
    function writeLog($text) {
        // $text=iconv("GBK", "UTF-8//IGNORE", $text);
        //$text = characet ( $text );
        file_put_contents ( '/data/2bu/wxpaylogs/paylog.txt', date ( "Y-m-d H:i:s" ) . "  " . $text . "\r\n", FILE_APPEND );
    }
}
