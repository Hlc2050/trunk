<?php
/**
 * Created by PhpStorm.
 * User: yjh
 * Date: 2018/3/27
 * Time: 14:02
 */
return [
    'appid'			=>	'',
    'mch_id'		=>	'',
    'key'			=>	'',
    'trade_type'		=>	'MWEB',
    'sign_type'	    	=>	'MD5',
    'fee_type'	    	=>	'CNY',
    'pay_notify_url'	=>	'http://test2.pinsetang.net/orderApi/payment/wxpay/notify.php',
//    'certPath'	=>	__DIR__ . '/cert/apiclient_cert.pem',
//    'keyPath'	=>	__DIR__ . '/cert/apiclient_key.pem',
    'pay_api' => 'https://api.mch.weixin.qq.com/pay/unifiedorder',
    'limit_pay'=>array(

    ),

];