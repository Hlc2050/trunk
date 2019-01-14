<?php
include(dirname(__FILE__) . '/init.php');
include(dirname(__FILE__) . '/dbMysql.php');
//本页为在CLI模式下执行 该文件的
//error_reporting(E_ALL & ~E_STRICT);  //php 5.4 以上 对方法重写参数需要一致，不一致需要加上 默认值,问题出现在 Dtable里面
$dbm = new dbMysql($config['db_mysql']['default']);
$smartCollect = new domainMonitor($dbm, $config);
$smartCollect->run();

echo '---------------------------completed!';

//域名拦截检测
class domainMonitor
{
    public $dbm;
    public $config;
    public $statusArr = array(
        ' 正常',
        ' 403服务器错误',
        ' 被拦截',
        ' 查询失败',
        ' 404',
    );

    function __construct($dbm, $config)
    {
        $this->dbm = $dbm;
        $this->config = $config;
    }

    public function run()
    {
        global $config;
        $check_start_time = time();//开始检测时间
        $sourceKey = array('key' => 'asdfkk123344');
        echo 'start clear intercept domain list ..' . chr(10);
        $this->domain_intercept_clear();
        $domains = $this->domain_list();//print_r($domains);
        $wdata = $this->get_weixin_data();
        $access_token = $wdata['access_token'];
        $template_id = $wdata['template_id'];
        $template_desc = '';
        $openids = array();
        $openidArr = $this->dbm->select('openid_manage');
        foreach ($openidArr as $value) {
            $openids[] = $value['openid'];
        }
        $iswarming = 0;
        foreach ($domains as $r) {
            $status = $this->checkDomain($r['domain']);
            if ($status == 2) $status = $this->checkDomain($r['domain']);
            $statusStr = $this->statusArr[$status];
            $sql = "update domain_intercept set status=$status where domain='" . $r['domain'] . "'";
            $this->dbm->doSql($sql);
            echo $r['domain'] . $statusStr . chr(10);
            if ($status == 2) {
//                $time = date('Y-m-d H:i:s');
//                $paramArr = array(
//                    'domain' => $r['domain'],
//                    'key' => $sourceKey['key'],
//                    'time' => $time
//                );
//                ksort($paramArr);
//                $sign = md5(http_build_query($paramArr));
                $url = $this->config['domain']['replace_api_url'] . '?domain=' . $r['domain']. '';
                $html = helper::get_curl_contents($url);
                $a = json_decode($html, 1);
                if ($a['ret'] == 1000) {
                    $template_desc .= $r['domain'] . $statusStr . " 处理成功" . " (" . $a['msg'] . ")" . chr(10);
                    echo $r['domain'] . $statusStr . " (" . $a['msg'] . ")" . chr(10);
                } elseif ($a['ret'] == 2000) {
                    $template_desc .= $r['domain'] . $statusStr . " 处理成功" . "(" . $a['msg'] . ")" . chr(10);
                    echo $r['domain'] . $statusStr . "(" . $a['ret'] . ',' . $a['msg'] . ")" . chr(10);
                } else {
                    $template_desc .= $r['domain'] . $statusStr . " 处理失败(" . $a['ret'] . ',' . $a['msg'] . ")" . chr(10);
                    echo $r['domain'] . $statusStr . " 处理失败(" . $a['ret'] . ',' . $a['msg'] . ")" . chr(10);
                }
                $iswarming = 1;
            }
        }
        if ($iswarming == 1) {
            $check_end_time = time();//检测结束时间
            foreach ($openids as $openid) {
                //仅当被拦截或被拦截域名恢复才通知
                $this->sendEvent($openid, "二部域名监控", $template_desc, date('Y-m-d H:i:s', time()), $this->config['domain']['show_domain_url'] . "/?start=" . $check_start_time . "&end=" . $check_end_time, $template_id, $access_token); //发送报警通知
            }
        }
        $this->domain_intercept_clear();
    }

    public function checkDomain($domain)
    {
        //财神到判断域名
        $url = 'http://mm.xjich.net/index.php?s=/Home/Page/wx_lanjie/domain/' . $domain;
        $html = file_get_contents($url);
        $html = iconv("gb2312", "utf-8//IGNORE", $html);

        //获取http状态码
        $httpCode = $this->_get_http_code($domain);

        switch ($html) {
            case '[0]':
                $now_status = 0;
                if ($httpCode == '404' || $httpCode == '0') $now_status = 4;
                break;
            case '[1]':
                $now_status = 1;
                if ($httpCode == '404' || $httpCode == '0') $now_status = 4;
                break;
            case '[2]':
                $now_status = 2;
                break;
            case '[3]':
                $now_status = 3;
                if ($httpCode == '404' || $httpCode == '0') $now_status = 4;
                break;
            default:
                $now_status = 0;
        }
        return $now_status;

    }

    private function domain_list()
    {
        $sql = "SELECT * FROM `domain_list` WHERE status=1 and promotion_type=0 ORDER BY `id` DESC";
        $a = $this->dbm->doSql($sql);

        return $a;

    }

    private function domain_intercept_clear()
    {
        $domains = $this->domain_list();
        $sql = "select * from domain_intercept ";
        $cept_domains = $this->dbm->doSql($sql);
        $domain_arr = array();
        foreach ($domains as $r) {
            $domain = $r['domain'];
            $domain_arr[] = $domain;
            $sql = "select * from domain_intercept where domain='$domain' ";//echo $sql.chr(10);
            $a2 = $this->dbm->doSql($sql);
            if (count($a2) == 0) {
                $data = array();
                $data['domain'] = $r['domain'];
                $data['create_time'] = time();
                $data['remark'] = $r['mark'];
                $this->dbm->insert('domain_intercept', $data);
                echo $domain . ' into  intercept ' . chr(10);
                //echo $this->dbm->getLastSql().chr(10);
            }
        }


        foreach ($cept_domains as $r) {
            $domain = $r['domain'];
            if (!in_array($domain, $domain_arr)) {
                $sql = "delete from domain_intercept where id=" . $r['id'];
                $this->dbm->doSql($sql);

                echo $domain . ' out of intercept ' . chr(10);
            }
        }

        echo 'intercept domain list update ok ' . chr(10);

    }

    private function _get_http_code($domain)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $domain);
        curl_setopt($ch, CURLOPT_TIMEOUT, 200);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_NOBODY, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return $httpCode;
    }

    public function get_weixin_data()
    {
        $url = $this->config['weixin_access_token'];
        $html = file_get_contents($url);
        $html = iconv("gb2312", "utf-8//IGNORE", $html);
        $a = json_decode($html, 1);
        if (!$a) {
            die('access token void ' . chr(10));
        }

        return array('access_token' => $a['access_token'], 'template_id' => $a['template_id']);

    }

    public function cut($begin, $end, $str)
    {
        $b = mb_strpos($str, $begin) + mb_strlen($begin);
        $e = mb_strpos($str, $end) - $b;
        return mb_substr($str, $b, $e);
    }

    /**
     * 发送模板消息
     */
    public function sendTemplateMessage($openid, $template_id, $url, $data, $access_token = null)
    {
        /* 获取token */
        if (is_null($access_token)) {
            $wdata = $this->get_weixin_data();
            $access_token = $wdata['access_token'];
        }
        $msg_arr = array("touser" => $openid, "template_id" => $template_id, "url" => $url, "topcolor" => "#ff0000", "data" => $data);

        $msg_arr = json_encode($msg_arr);

        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $access_token;
        $header [] = "content-type: application/x-www-form-urlencoded; charset=UTF-8";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $msg_arr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($res, true);

        if ($res['errcode'] == 0) {
            //echo "发送模板消息成功！";
        } else {
            $res['openid'] = $openid;
            $res['template_id'] = $template_id;
            $res['access_token'] = $access_token;
            file_put_contents(dirname(__FILE__) . '/wxerr_tpl.txt', serialize($res) . PHP_EOL, FILE_APPEND);
            //echo "发送模板消息失败！";
            //dump($res);
        }
    }

    /**
     * 安全事件通知
     * template_id: TjxvOKJ0prd7kaMR_T3-2n1Z8_CXwuverZScNxRj2Vk
     */
    public function sendEvent($open_id, $type, $remark, $time, $url = '', $template_id = '', $access_token = null)
    {
        $msg = array("first" => array("value" => " ", "color" => "#173177",), "keyword1" => array("value" => $type, "color" => "#173177",), "keyword2" => array("value" => $time, "color" => "#173177",), "remark" => array("value" => $remark, "color" => "#173177",));
        $this->sendTemplateMessage($open_id, $template_id, $url, $msg, $access_token);
    }

}