<?php
//本页为在CLI模式下执行 该文件的
//error_reporting(E_ALL & ~E_STRICT);  //php 5.4 以上 对方法重写参数需要一致，不一致需要加上 默认值,问题出现在 Dtable里面
$config=array(
    'domain'=>array(
        'domainlist_api_url'=>'http://admin.two.com/domain/beianDomains',    //供检测的域名列表接口
        'replace_api_url'=>'http://admin.two.com/domain/checkReplace',  //替换接口
        'show_domain_url'=>'http://admin.two.com/domain/domainList',
        'domain_sno_url' => 'http://www.taopeiku.com/domain/getDomainSno' //可接收域名提醒人员
    ),
    'weixin_access_token'=>'http://sysadmin.huashengkan.com/portal/Weixin/getToken',
    'weixin_template_url'=>'http://sysadmin.huashengkan.com/portal/Weixin/getTemplateId?type_id=1',
    'openids'=>array(
        'openids_api_url'=>'http://admin.two.com/domain/getOpenids',  //获取Openids
    )
);
$smartCollect = new domainMonitor($config);
$smartCollect->run();

echo '---------------------------completed!';

//域名备案检测
class domainMonitor
{
    public $config;
    public $statusArr = array(
        0 => ' 正常',
        1 => ' 备案有问题',
        2 => ' 检测失败'
    );

    function __construct($config)
    {
        $this->config = $config;
    }

    public function run()
    {
        global $config;
        $check_start_time = time();//开始检测时间
        //$sourceKey = array('key' => 'asdfkk123344');
        echo 'start time '.$check_start_time. chr(10);
        echo 'start Detection  domain list ..' . chr(10);
//        $this->domain_intercept_clear();
        $domains = $this->domain_list();//print_r($domains);
        $template_desc = array();
        $desc = '';
        $openidArr=$this->openids();
        $iswarming = 0;
        echo count($domains) . chr(10);
        foreach ($domains as $r) {
            $status = $this->checkDomainBeian($r['domain']);
            $statusStr = $this->statusArr[$status];
            echo $r['domain'] . $statusStr . chr(10);

            if ($status == 2) {
                sleep(1);
                $status = $this->checkDomainBeian($r['domain']);
            }
//            $sql = "update domain_intercept set status=$status where domain='" . $r['domain'] . "'";
//            $this->dbm->doSql($sql);
            //域名不能用处理
            if ($status == 1) {
                $sno_url = $this->config['domain']['domain_sno_url'] . '?domain=' . $r['domain'];
                $ret = file_get_contents($sno_url);
                $user = json_decode($ret,1);
                $send_user = $user['send_user'];
                $url = $this->config['domain']['replace_api_url'] . '?type=1&domain=' . $r['domain'] . '&time='.time();
                $html = file_get_contents($url);
                $a = json_decode($html, 1);
                if ($a['ret'] == 1000) {
                    $desc .= $r['domain'] . $statusStr . " 处理成功" . " (" . $a['msg'] . ")" . chr(10);
                    echo $r['domain'] . $statusStr . " (" . $a['msg'] . ")" . chr(10);
                } elseif ($a['ret'] == 2000) {
                    $desc .= $r['domain'] . $statusStr . " 处理成功" . "(" . $a['msg'] . ")" . chr(10);
                    echo $r['domain'] . $statusStr . "(" . $a['ret'] . ',' . $a['msg'] . ")" . chr(10);
                } else {
                    $desc .= $r['domain'] . $statusStr . " 处理失败(" . $a['ret'] . ',' . $a['msg'] . ")" . chr(10);
                    echo $r['domain'] . $statusStr . " 处理失败(" . $a['ret'] . ',' . $a['msg'] . ")" . chr(10);
                }
                echo '可收到的用户id:'.implode(',',$send_user).'\r\n';
                foreach ($send_user as $user) {
                    if (!isset($template_desc[$user])) {
                        $template_desc[$user] = $desc;
                    } else {
                        $template_desc[$user] .= $desc;
                    }
                }
                $iswarming = 1;
            }
            sleep(1);
        }
        $check_end_time = time();//检测结束时间
        echo 'end time '.$check_end_time. chr(10);
        echo 'total time '.($check_end_time-$check_start_time). chr(10);
        if ($iswarming == 1) {
            $wdata = $this->get_weixin_data();
            $access_token = $wdata['access_token'];
            $template_id = $wdata['template_id'];


            foreach ($openidArr as $open) {
                $openid = $open['openid'];
                $user_id = $open['system_user'];
                if (!isset($template_desc[$user_id])) {
                    continue;
                }
                $template_msg = $template_desc[$user_id];
                $this->sendEvent($openid, "二部域名备案检测", $template_msg, date('Y-m-d H:i:s', time()), $this->config['domain']['show_domain_url'] . "/?start=" . $check_start_time . "&end=" . $check_end_time. "&type=1&user_id=".$user_id, $template_id, $access_token); //发送报警通知
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
    public function checkDomainBeian($domain)
    {
        $url = 'http://' . $domain;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $content = curl_exec($ch);
        // 检查是否有错误发生
        if (curl_errno($ch)) {
            return 2;
        }
        curl_close($ch);//echo '--end<br>';
        $delta = "batit.aliyun.com/alww.html";
        if(strstr($content,$delta))
            $status=1;//域名不能用
        else{
            $status=0;//正常域名
        }
        return $status;
    }
    /**
     * 获取通知的人员
     * @return [type] [description]
     */
    private function openids()
    { 
        $url = $this->config['openids']['openids_api_url'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $content = curl_exec($ch);
        curl_close($ch);
        $ret = json_decode($content, true);
        return $ret['data'];
    }
    private function domain_list()
    {
        $url = $this->config['domain']['domainlist_api_url'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $content = curl_exec($ch);
        curl_close($ch);
        $ret = json_decode($content, true);
        return $ret['data'];
    }
    public function get_weixin_data()
    {
        $url = $this->config['weixin_access_token'];
        $token = file_get_contents($url);
        $token = iconv("gb2312", "utf-8//IGNORE", $token);
        $tem_url = $this->config['weixin_template_url'];
        $tem = file_get_contents($tem_url);
        $tem = iconv("gb2312", "utf-8//IGNORE", $tem);

        return array('access_token' => $token, 'template_id' => $tem);

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