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
        0 => ' 正常',
        1 => ' 被转码',
        2 => ' 被拦截',
        3 => ' 查询失败',
        -1 => ' 接口到期',
        -2 => ' 频率过快',
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
        //$sourceKey = array('key' => 'asdfkk123344');
        echo 'start time '.$check_start_time. chr(10);
        echo 'start Detection Odd domain list ..' . chr(10);
//        $this->domain_intercept_clear();
        $domains = $this->domain_list();//print_r($domains);

        $template_desc = '';
        $openids = array();
        $openidArr = $this->dbm->select('openid_manage');
        foreach ($openidArr as $value) {
            $openids[] = $value['openid'];
        }
        $iswarming = 0;
        echo count($domains) . chr(10);
        foreach ($domains as $r) {
            $is_public = '';
            if ($r['is_public_domain'] == 1) {
                $is_public = '(公众号)';
            }
            $status = $this->checkDomain($r['domain']);
            if ($status == -2 || $status == 3) {
                $statusStr = $this->statusArr[$status];
                echo $r['domain'] .$is_public. $statusStr . chr(10);
                sleep(1);//频率过快、查询失败
                $status = $this->checkDomain($r['domain']);
            }
            $statusStr = $this->statusArr[$status];
//            $sql = "update domain_intercept set status=$status where domain='" . $r['domain'] . "'";
//            $this->dbm->doSql($sql);
            echo $r['domain'] .$is_public. $statusStr . chr(10);
            if ($status == 2) {
                $url = $this->config['domain']['replace_api_url'] . '?domain=' . $r['domain'] . '';
                $html = file_get_contents($url);
                $a = json_decode($html, 1);
                if ($a['ret'] == 1000) {
                    $template_desc .= $r['domain'] .$is_public. $statusStr . " 处理成功" . " (" . $a['msg'] . ")" . chr(10);
                    echo $r['domain'] .$is_public. $statusStr . " (" . $a['msg'] . ")" . chr(10);
                } elseif ($a['ret'] == 2000) {
                    $template_desc .= $r['domain']  .$is_public. $statusStr . " 处理成功" . "(" . $a['msg'] . ")" . chr(10);
                    echo $r['domain']  .$is_public. $statusStr . "(" . $a['ret'] . ',' . $a['msg'] . ")" . chr(10);
                } else {
                    $template_desc .= $r['domain']  .$is_public. $statusStr . " 处理失败(" . $a['ret'] . ',' . $a['msg'] . ")" . chr(10);
                    echo $r['domain']  .$is_public. $statusStr . " 处理失败(" . $a['ret'] . ',' . $a['msg'] . ")" . chr(10);
                }
                $iswarming = 1;
            }
            // sleep(1);//500ms

        }
        $check_end_time = time();//检测结束时间
        echo 'end time '.$check_end_time. chr(10);
        echo 'total time '.($check_end_time-$check_start_time). chr(10);

        if ($iswarming == 1) {
            $wdata = $this->get_weixin_data();
            $access_token = $wdata['access_token'];
            $template_id = $wdata['template_id'];

            foreach ($openids as $openid) {
                //仅当被拦截或被拦截域名恢复才通知
                $this->sendEvent($openid, "二部域名监控(Line-one)", $template_desc, date('Y-m-d H:i:s', time()), $this->config['domain']['show_domain_url'] . "/?start=" . $check_start_time . "&end=" . $check_end_time. "&line=1", $template_id, $access_token); //发送报警通知
            }
        }
//        $this->domain_intercept_clear();
    }

    /**
     * 接口检测
     * @param $domain
     * @return int|mixed
     * author: yjh
     */
    //0为正常，1为被转码，2为被封，3查询失败，-1接口到期。注意：频率过高默认返回0状态
    public function checkDomain($domain)
    {
        $url = 'http://vip.weixin139.com/weixin/wx_domain.php?user=yaoyy1&key=9d9ef5e8a830853def00984a141d74f4&domain=' . $domain;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $content = curl_exec($ch);
        // 检查是否有错误发生
        if (curl_errno($ch)) {
            return 3;
        }
        curl_close($ch);//echo '--end<br>';
        //echo $content;
        if(strpos($content,'<html>') != false){
            return 3;
        }
        $content = json_decode($content, true);

        $status = $content['status'];
        if(array_key_exists('code',$content)) $code = $content['code'];
        else $code='';
        switch ($status) {
            case '0':
                $now_status = 0;//正常或者频率过快
                if ($code != "" && $code == "API is too busy") $now_status = -2;//重新检测
                break;
            case '1':
                $now_status = 1;//1为被转码
                break;
            case '2':
                $now_status = 2;//域名被拦截
                break;
            case '3':
                $now_status = 3;//3查询失败
                break;
            case '-1':
                $now_status = -1;//接口到期
                break;
            default:
                $now_status = 0;
        }
        //file_put_contents('/data/2bu/domain_log/domain_log_' . date('md') . '.txt' , date('Y-m-d H:i:s') . "| $domain | $now_status | (Line-one)  \n" , FILE_APPEND  );
        return $now_status;
    }

    private function domain_list()
    {
        $sql = "SELECT domain_id FROM `promotion_manage` WHERE status=2";
        $pauseArrs = $this->dbm->doSql($sql);
        $pauseIds=implode(',',array_column($pauseArrs, 'domain_id'));
        $sql = "SELECT * FROM `domain_list` WHERE  status=1 and promotion_type!=1 and mod(id,2) = 1 and id NOT IN (".$pauseIds.") ORDER BY id DESC";
        $a = $this->dbm->doSql($sql);
        return $a;

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