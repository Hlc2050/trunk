<?php
/**
 * 域名拦截检测公用类
 * Created by PhpStorm.
 * Date: 2018/7/12
 * Time: 9:17
 * Author: lxj
 */

class domainMonitor
{
    public $dbm;
    public $config;
    public $msgTitle='';
    public $even = 0;
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
        echo 'start Detection Even domain list ..' . chr(10);
//        $this->domain_intercept_clear();
        $domains = $this->domain_list();//print_r($domains);
        $openids = array();
        $openidArr = $this->dbm->select('openid_manage');
        foreach ($openidArr as $value) {
            $openids[] = $value['openid'];
        }
        $super_admin = $this->get_super_admin();
        $iswarming = 0;
        $template_desc = array();
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
            echo $r['domain'] .$is_public. $statusStr . chr(10);
            if ($status == 2) {
                //查询推广人员
                $domain_id = $r['id'];
                $where = ' 1 ';
                if ($r['domain_type'] == 0 || $r['domain_type'] == 3) {
                    //推广域名修改为从推广-域名关联表取推广 lxj 2018-12-19
                    $sql = " SELECT promotion_id FROM promotion_domain_rel WHERE domain_id=".$domain_id;
                    $domain_pro = $this->dbm->doSql($sql);
                    if ($domain_pro) {
                        $pro_id = array_column($domain_pro,'promotion_id');
                        $where .=  ' and p.id in ('.implode(',',$pro_id).')';
                    }else {
                        $where .=  ' and p.id=0 ';
                    }
                }
                if ($r['domain_type'] == 1) {
                    $where .= ' and goto_domain_id = '.$domain_id;
                }
                if ($r['domain_type'] == 2) {
                    $where .= ' and white_domain_id = '.$domain_id;
                }
                $where .= ' and p.status!=1';

                $sql = " SELECT f.sno FROM promotion_manage p LEFT JOIN finance_pay f  ON f.id=p.finance_pay_id WHERE ".$where;

                $promotion_staff = $this->dbm->doSql($sql);
                $url = $this->config['domain']['replace_api_url'] . '?domain=' . $r['domain'] . '';
                $html = file_get_contents($url);
                $a = json_decode($html, 1);
                if ($a['ret'] == 1000) {
                    $desc = $r['domain'] .$is_public. $statusStr . " 处理成功" . " (" . $a['msg'] . ")" . chr(10);
                    echo $r['domain'] . $is_public.$statusStr . " (" . $a['msg'] . ")" . chr(10);
                } elseif ($a['ret'] == 2000) {
                    $desc = $r['domain'] .$is_public. $statusStr . " 处理成功" . "(" . $a['msg'] . ")" . chr(10);
                    echo $r['domain'] . $is_public.$statusStr . "(" . $a['ret'] . ',' . $a['msg'] . ")" . chr(10);
                } else {
                    $desc = $r['domain'] .$is_public. $statusStr . " 处理失败(" . $a['ret'] . ',' . $a['msg'] . ")" . chr(10);
                    echo $r['domain'] .$is_public. $statusStr . " 处理失败(" . $a['ret'] . ',' . $a['msg'] . ")" . chr(10);
                }
                //根据人员权限推送消息
                $promotion_user = array();
                foreach ($promotion_staff as $pro) {
                    $promotion_user[] = $pro['sno'];
                }
                $promotion_user = array_unique($promotion_user);
                //推广人员部门负责人
                $send_user = array();
                foreach ($promotion_user as $value) {
                    $send_user[] = $value;
                    $manage_user = $this->get_promotion_staff_manager($value);
                    if (!empty($manage_user)) {
                        $send_user = array_merge($send_user,$manage_user);
                    }
                }
                $send_user = array_merge($super_admin,$send_user);
                $send_user = array_unique($send_user);
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
            // sleep(1);//500ms

        }
        $check_end_time = time();//检测结束时间
        echo 'end time '.$check_end_time. chr(10);
        echo 'total time '.($check_end_time-$check_start_time). chr(10);
        if ($iswarming == 1) {
            $wdata = $this->get_weixin_data();
            $access_token = $wdata['access_token'];
            $template_id = $wdata['template_id'];

            foreach ($openidArr as $open) {
                //仅当被拦截或被拦截域名恢复才通知
                $line = 2;
                if ($this->even == 1) {
                    $line = 1;
                }
                $openid = $open['openid'];
                $user_id = $open['system_user'];
                if (!isset($template_desc[$user_id])) {
                    continue;
                }
                $template_msg = $template_desc[$user_id];
                $this->sendEvent($openid, "二部域名监控(".$this->msgTitle.")", $template_msg, date('Y-m-d H:i:s', time()), $this->config['domain']['show_domain_url'] . "/?start=" . $check_start_time . "&end=" . $check_end_time. "&line=".$line.'&user_id='.$user_id, $template_id, $access_token); //发送报警通知
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
        $url = $this->config['domain_check_url'].'&domain=' . $domain;
        $content = curl_get($url);
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
//        $content = curl_exec($ch);
        // 检查是否有错误发生
        if ($content == 'curl_error') {
            return 3;
        }
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
        //file_put_contents('/data/2bu/domain_log/domain_log_' . date('md') . '.txt' , date('Y-m-d H:i:s') . "| $domain | $now_status | (Line-two)  \n" , FILE_APPEND  );

        return $now_status;
    }

    private function domain_list()
    {
        $sql = "SELECT id FROM `promotion_manage` WHERE status=2";
        $pauseArrs = $this->dbm->doSql($sql);
        $pauseIds=implode(',',array_column($pauseArrs, 'id'));
        $pauseDomains = '';
        if ($pauseIds) {
            //修改为从推广-域名关联表取暂停域名 2018-12-19
            $sql = "SELECT domain_id FROM `promotion_domain_rel`  WHERE promotion_id in (".$pauseIds.")";
            $pause = $this->dbm->doSql($sql);
            $pauseDomains=implode(',',array_column($pause, 'domain_id'));
        }
        $sql = "SELECT * FROM `domain_list` WHERE status=1 and promotion_type!=1 and mod(id,2) = ".$this->even;
        if ($pauseDomains) {
            $sql .= " and id NOT IN (".$pauseDomains.")";
        }
        $sql .= " ORDER BY `id` DESC";
        $a = $this->dbm->doSql($sql);
        return $a;

    }

    public function get_weixin_data()
    {
        $url = $this->config['weixin_access_token'];
        $token = file_get_contents($url);
        $token = iconv("gb2312", "utf-8//IGNORE", $token);
        $temp_url = $this->config['weixin_template_url'];
        $temp = file_get_contents($temp_url);
        $temp = iconv("gb2312", "utf-8//IGNORE", $temp);
        return array('access_token' => $token, 'template_id' => $temp);
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

        $url = $this->config['send_msg_url'].'?access_token=' . $access_token;
        $header [] = "content-type: application/x-www-form-urlencoded; charset=UTF-8";
        $res = curl_post($url,$msg_arr,1,$header);
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
//        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
//        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $msg_arr);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        $res = curl_exec($ch);
//        curl_close($ch);
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


    /**
     * 超级管理员+支持人员用户
     * @return array
     */
    private function get_super_admin()
    {
        $role_sql = "SELECT param_value  FROM params WHERE param_name = 'domain_msg_role'";
        $roles = $this->dbm->doSql($role_sql);
        $other_roles = $roles[0]['param_value'];
        $role_where = ' role_id = 1 ';
        if ($other_roles) {
            $role_where = ' role_id in (1,'.trim($other_roles,',').')';
        }
        $data = array();
        $sql = "SELECT sno  FROM cservice_roles WHERE ".$role_where;
        $users = $this->dbm->doSql($sql);
        foreach ($users as $user) {
            $data[] = $user['sno'];
        }
        $data[] = $this->config['super_admin_id'];
        $data = array_unique($data);
        return $data;
    }

    /**
     * 查询推广人员组长
     * @param $user_id
     * @return array
     */
    private function get_promotion_staff_manager($user_id)
    {
        $dbm = $this->dbm;
        /* @var $dbm dbMysql */
        $sql = "SELECT * FROM cservice_groups WHERE  sno=".$user_id;
        $groups = $dbm->doSql($sql);
        $parent_groups = array();
        foreach ($groups as $value) {
            $parent = $this->get_parent_groups($value['groupid']);
            foreach ($parent as $p) {
                $parent_groups[] = $p;
            }
        }
        $parent_groups = array_unique($parent_groups);
        if (!empty($parent_groups)) {
            $sql = "SELECT manager_id FROM cservice_group WHERE  groupid IN (".implode(',',$parent_groups).")";
            $managers = $dbm->doSql($sql);
            $data = array_column($managers,'manager_id');
            $data = array_unique($data);
            return $data;
        }
    }

    /**
     * 查询部门的上级部门
     * @param $group_id int|array
     * @return array
     */
    private function get_parent_groups($group_id)
    {
        static $parent_groups;
        $parent_groups[] = $group_id;
        $sql = "SELECT * FROM `cservice_group` WHERE groupid=".$group_id.' AND parent_id!=0 ';
        $m = $this->dbm->doSql($sql);
        if (empty($m)) return $parent_groups;
        $group_id = $m[0]['parent_id'];
        $this->get_parent_groups($group_id);
        return $parent_groups;
    }

}