<?php
/**
 * 自动执行脚本公共方法
 * Created by PhpStorm.
 * Date: 2018/7/12
 * Time: 15:10
 * author lxj
 */

/**
 * @param $url
 * @param int $is_https 是否为https请求
 * @return mixed|string
 */
function curl_get($url,$is_https=0)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    if ($is_https == 1) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    }
    $res = curl_exec($ch);
    if (curl_errno($ch)) {
        return 'curl_error';
    }
    curl_close($ch);
    return $res;
}

/**
 * @param $url
 * @param array $data 参数
 * @param int $is_https 是否为https
 * @param array $header 头部信息
 * @return mixed
 */
function curl_post($url,$data=array(),$is_https=0,$header=array())
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    if ($is_https == 1) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }
    if (!empty($header)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}