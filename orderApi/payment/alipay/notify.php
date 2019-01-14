<?php
header('Content-type:text/html; Charset=utf-8');
require_once __DIR__ . '/../../framework/db/dbMysql.php';
$config = require_once __DIR__ . '/../../config/config.php';
$dbm = new dbMysql($config['db_mysql']['default']);
$aliConfig = require_once __DIR__ . '/aliconfig.php';

//支付宝公钥，账户中心->密钥管理->开放平台密钥，找到添加了支付功能的应用，根据你的加密类型，查看支付宝公钥
$alipayPublicKey = $aliConfig['alipayPublicKey'];
$aliPay = new AlipayService($alipayPublicKey,$dbm);
//验证签名
$result = $aliPay->rsaCheck($_POST, $_POST['sign_type']);
$aliPay->writeLog(var_export($_POST, true));
if ($result === true) {
    $order_code = $_POST['out_trade_no'];
    //out_trade_no 查找订单 插入支付状态以及trade_no
    $orderInfo = $aliPay->getOrderInfo($order_code);
    if ($orderInfo) {
        $pay_status=1;
        if($_POST['trade_status']=='TRADE_SUCCESS')$pay_status=1;
        elseif ($_POST['trade_status']=='WAIT_BUYER_PAY ')$pay_status=0;
        elseif ($_POST['trade_status']=='TRADE_CLOSED ')$pay_status=0;
        $update_data=array(
            'pay_status'=>$pay_status,
            'payment'=>0,
            'trade_no'=>$_POST['trade_no'],
        );
        $aliPay->updateOrderInfo($order_code,$update_data);
        $data=array(
            'out_trade_no'=>$order_code,
            'trade_no'=>$_POST['trade_no'],
            'trade_status'=>$_POST['trade_status'],
            'order_id'=>$orderInfo[0]['id'],
            'operate_time'=>time(),
            'total_amount'=>$_POST['total_amount'],
            'payment'=>1,
        );
        $aliPay->insertOrderBill($data);

    }
    //处理你的逻辑，例如获取订单号$_POST['out_trade_no']，订单金额$_POST['total_amount']等
    //程序执行完后必须打印输出“success”（不包含引号）。如果商户反馈给支付宝的字符不是success这7个字符，支付宝服务器会不断重发通知，直到超过24小时22分钟。一般情况下，25小时以内完成8次通知（通知的间隔频率一般是：4m,10m,10m,1h,2h,6h,15h）；
    echo 'success';
    $aliPay->writeLog('success');

    exit();
}
echo 'error';
exit();

class AlipayService
{
    //支付宝公钥
    protected $alipayPublicKey;
    protected $charset;

    public function __construct($alipayPublicKey,$dbm)
    {
        $this->charset = 'utf8';
        $this->alipayPublicKey = $alipayPublicKey;
        $this->dbm = $dbm;

    }

    /**
     *  验证签名
     **/
    public function rsaCheck($params)
    {
        $sign = $params['sign'];
        $signType = $params['sign_type'];
        unset($params['sign_type']);
        unset($params['sign']);
        return $this->verify($this->getSignContent($params), $sign, $signType);
    }

    function verify($data, $sign, $signType = 'RSA')
    {
        $pubKey = $this->alipayPublicKey;
        $res = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($pubKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";
        ($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');

        //调用openssl内置方法验签，返回bool值
        if ("RSA2" == $signType) {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res, version_compare(PHP_VERSION, '5.4.0', '<') ? SHA256 : OPENSSL_ALGO_SHA256);
        } else {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        }
//        if(!$this->checkEmpty($this->alipayPublicKey)) {
//            //释放资源
//            openssl_free_key($res);
//        }
        return $result;
    }

    /**
     * 校验$value是否非空
     *  if not set ,return true;
     *    if is null , return true;
     **/
    protected function checkEmpty($value)
    {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;
        return false;
    }

    public function getSignContent($params)
    {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
                // 转换成目标字符集
                $v = $this->characet($v, $this->charset);
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }
        unset ($k, $v);
        return $stringToBeSigned;
    }

    /**
     * 转换字符集编码
     * @param $data
     * @param $targetCharset
     * @return string
     */
    function characet($data, $targetCharset)
    {
        if (!empty($data)) {
            $fileType = $this->charset;
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
                //$data = iconv($fileType, $targetCharset.'//IGNORE', $data);
            }
        }
        return $data;
    }

    function getOrderInfo($order_code){
        $condtion = "order_code='" . $order_code . "'";
        //out_trade_no 查找订单 插入支付状态以及trade_no
        $orderInfo = $this->dbm
            ->where($condtion)
            ->limit(1)
            ->select('order_manage');
        return $orderInfo;
    }
    function updateOrderInfo($order_code,$update_data){
        $condtion = "order_code='" . $order_code . "'";
        //out_trade_no 查找订单 插入支付状态以及trade_no
        $ret=$this->dbm->where($condtion)->update('order_manage',$update_data);
        return $ret;
    }
    function insertOrderBill($data){
        $ret=$this->dbm->insert('order_bill_detail',$data);
        return $ret;
    }

    //请确保项目文件有可写权限，不然打印不了日志。
    function writeLog($text)
    {
        // $text=iconv("GBK", "UTF-8//IGNORE", $text);
        //$text = characet ( $text );
        file_put_contents('/data/2bu/alipaylogs/notifylog.txt', date("Y-m-d H:i:s") . "  " . $text . "\r\n", FILE_APPEND);
    }
}