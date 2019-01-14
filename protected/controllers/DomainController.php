<?php

class DomainController extends CController
{

    public $config=array(
        'domain' => array(
            'replace_api_url' => 'http://erbu178.net/domain/checkReplace',  //替换接口
            'show_domain_url' => 'http://erbu178.net/domain/domainList',
        ),
        'weixin_access_token' => 'http://sysadmin.huashengkan.com/portal/Weixin/getToken',
        'weixin_template_url' => 'http://sysadmin.huashengkan.com/portal/Weixin/getTemplateId?type_id=1',
        'allow_ip'=>array('139.199.198.207','119.29.14.119','124.72.64.66','127.0.0.1'),
    );

    public function actionIndex()
    {
        echo 'hi';
        die('hehe');
    }

    /**
     * 详情页
     * author: yjh
     */
    public function actionDomainList()
    {
        $start_time = $_GET['start'];
        $end_time = $_GET['end'];
        $line = isset($_GET['line']) ? "=" . $_GET['line'] : "in (0,1,2)";
        $type = isset($_GET['type']) ? $_GET['type'] : 0;
        $user_id = $_GET['user_id'] ? $_GET['user_id'] : 0;
        if ($start_time && $end_time) {
            $sno_where = ' 1 ';
            //判断权限
            $intercept_data = $this->getInterceptSnoData();
            $user_str = $intercept_data['user_str'];
            if ($user_str != 0) {
                $sno_where .= " AND h.sno in (" . $user_str . ") ";
            }
            $m2 = $intercept_data['m2'];
            $finance_sno = $intercept_data['finance_sno'];
            $sno_where .= " AND a.promotion_id!=0 ";
            //取出这次被拦截的域名
            $sql = "SELECT a.*,b.domain,b.is_https,b.is_public_domain,b.application_type,c.finance_pay_id,c.white_domain_id,c.goto_domain_id,d.channel_name,d.channel_code,f.name as partner_name,g.csname_true as tg_name
                    FROM domain_intercept_detail AS a
                    LEFT JOIN domain_list AS b ON b.id=a.domain_id 
                    LEFT JOIN promotion_manage AS c ON c.id=a.promotion_id
                    LEFT JOIN channel AS d ON d.id=c.channel_id
                    LEFT JOIN partner AS f ON f.id=d.partner_id
                    LEFT JOIN cservice AS g ON g.csno=a.uid
                    LEFT JOIN finance_pay AS h ON h.id=c.finance_pay_id
                    WHERE " . $sno_where . "  AND a.time BETWEEN  " . $start_time . " AND " . $end_time . " AND a.line " . $line . " AND a.detection_type=" . $type;
            $m = Yii::app()->db->createCommand($sql)->queryAll();
            $keyArr = array_unique(array_column($m, 'domain'));

            $page['domains'] = array_merge($m, $m2);
            $page['intercepts'] = count($keyArr) + count($m2);
            $page['start'] = date('Y-m-d H:i:s', $start_time);
            $page['end'] = date('H:i:s', $end_time);
            $page['finance_sno'] = $finance_sno;
            $page['user_id'] = $_GET['user_id'] ? $_GET['user_id'] : 0;

            if ($type == 1)
                $this->render('beianErrorNotice', array('page' => $page));
            else {
                $temp = array();
                $now = time();
                $today_begin = strtotime(date('Y-m-d 00:00:00', $now));
                $tomorrow = strtotime(date('Y-m-d 00:00:00', strtotime('+1 day')));
                $sql = 'select * from domain_intercept_detail 
                where promotion_id !=0 order by promotion_id';
                $table_list = Yii::app()->db->createCommand($sql)->queryAll();

                foreach ($table_list as $value) {
                    $key = $value['promotion_id'];

                    if (strstr($value['mark'], '成功')) {
                        //总替换域名个数
                        if ($value['detection_type'] == 0) {
                            $temp[$key]['all_num'] += 1;
                        } else {
                            $temp[$key]['all_num'] += 0;
                        }
                        //今日替换域名个数
                        if ($today_begin < $value['time'] && $value['time'] < $tomorrow && $value['detection_type'] == 0) {
                            $temp[$key]['today_num'] += 1;
                        } else {
                            $temp[$key]['today_num'] += 0;
                        }
                    }
                    //掉备案域名
                    if ($value['detection_type'] == 1) {
                        $temp[$key]['detection'] += 1;
                    } else {
                        $temp[$key]['detection'] += 0;
                    }
                }

                foreach ($page['domains'] as $key => $value) {
                    if (isset($temp[$value['promotion_id']])) {
                        $page['domains'][$key] = array_merge($page['domains'][$key], $temp[$value['promotion_id']]);
                    }
                }

                $this->render('interceptNotice', array('page' => $page));
            }
        } else {
//            $sql = "select * from domain_intercept ";
//            $m = Yii::app()->db->createCommand($sql)->queryAll();
//            $page['domains'] = $m;
//            $intercepts = 0;
//            foreach ($page['domains'] as $r) {
//                if ($r['status'] == 2) $intercepts++;
//            }
//            $page['intercepts'] = $intercepts;
            $page = array();
            $this->render('domainList', array('page' => $page));

        }
    }

    /*
     * 域名拦截替换
     * author: yjh
     */
    public function actionCheckReplace()
    {
        $domain = isset($_GET['domain']) ? $_GET['domain'] : '';
        $detection_type = isset($_GET['type']) ? $_GET['type'] : 0;
        //变更状态(改为被拦截状态)
        $domainModel = DomainList::model()->findByAttributes(array('domain' => $domain));
        if (!$domainModel) {
            $ret = array('ret' => 4001, 'msg' => '域名不存在');
            die(json_encode($ret));
        }
        $domainModel->status = 2;//改成拦截
        if ($detection_type == 1) $domainModel->status = 4;//改成备案失效

        $domainModel->save();
        //域名id 推广人员
        $domain_id = $domainModel->id;
        $line = ($domain_id % 2) == 1 ? 1 : 2;
        $uid = $domainModel->uid;
        $type = $domainModel->domain_type;
        $is_https = $domainModel->is_https;
        $pro_type = $domainModel->promotion_type; // 域名的推广类型
        $app_type = $domainModel->application_type;
        if ($pro_type == 2) {
            $this->interceptOpenDomain($domain_id, $domain, $uid, $type, $detection_type);
        } else {
            //推广域名
            if ($type == 0 || $type == 3) {
                //$promotion = Promotion::model()->findByAttributes(array('domain_id' => $domain_id, 'status' => 0));
//                $promotion = Promotion::model()->find('domain_id=' . $domain_id . ' and status!=1');//暂停或正常
                //修改为从推广-域名关联表取推广信息
                $promotion = PromotionDomain::model()->getPromotionByDomain($domain_id,' status!=1 ');
                if (!$promotion) {
                    $data = array('domain_id' => $domain_id, 'detection_type' => $detection_type, 'domain_type' => $type, 'line' => $line, 'uid' => $uid, 'mark' => '域名的推广不存在,不进行替换');
                    DomainInterceptDetail::model()->insertInterceptDomain($data);
                    $ret = array('ret' => 4004, 'msg' => '域名的推广不存在,不进行替换');
                    die(json_encode($ret));
                }
                $promotion = $promotion[0];
                $cnzz_code_id = $domainModel->cnzz_code_id;
                $promotion_id = $promotion['id'];
                //若推广的落地域名被拦截，删除推广缓存
                $redis_flag = Yii::app()->params['basic']['is_redis'];
                if ($redis_flag == 1) {
                    $ret = Yii::app()->redis->getValue('promotion:' . $promotion_id);
                    if ($ret) Yii::app()->redis->deleteValue('promotion:' . $promotion_id);
                }
                //查找备用域名 总统计组别+推广人员
                $readyDomain = DomainList::model()->find('status=0 and domain_type='.$type.' and cnzz_code_id=' . $cnzz_code_id . ' and uid='.$uid.' and is_https=' . $is_https . ' and application_type='.$app_type .' order by rand()');
                if (!$readyDomain) {
                    $readyDomain = DomainList::model()->find('status=0 and domain_type='.$type.' and cnzz_code_id!=0 and cnzz_code_id!=' . $cnzz_code_id . ' and uid='.$uid.' and is_https=' . $is_https .' and application_type='.$app_type .' order by rand()' );
                    if (!$readyDomain) {
                        $data = array('domain_id' => $domain_id, 'detection_type'=>$detection_type, 'domain_type' => $type, 'line' =>$line,'promotion_id' => $promotion_id, 'log_promotion_id'=>$promotion_id,'uid' => $uid, 'mark' => '备用推广域名不足,不进行替换');
                        DomainInterceptDetail::model()->insertInterceptDomain($data);
                        $ret = array('ret' => 4007, 'msg' => '备用推广域名不足,不进行替换');
                        die(json_encode($ret));
                    }
                }

                //获取上一条推广记录创建时间
                $last_creat_time = DomainPromotionChange::model()->find("promotion_id=$promotion_id order by id desc")->create_time;

                //获取统计piwik 域名id
//            $idSite = $this->getIdSite($readyDomain->domain);
//            $promotion->idsite = $idSite;
//            $promotion->three_cnzz = $this->getPiwikJS($idSite);

                //域名替换修改为更新推广-域名关联表 lxj 2018-12-20
                PromotionDomain::model()->replacePromotionDomain($promotion_id,$domain_id,$readyDomain->id);
                //替换域名修改状态
                $readyDomain->status = 1;
                $readyDomain->save();
                $is_public = '';
                if ($readyDomain->is_public_domain == 1) {
                    $is_public = '(公众号)';
                }

                //推广替换记录 edit lxj 2019-01-02
                $change_log[] = array(
                    'domain'=>$readyDomain->domain,
                    'from_domain'=>$domain
                );
                DomainPromotionChange::model()->addChangeLogs($promotion_id, $change_log,1);

                $data = array('domain_id' => $domain_id, 'detection_type'=>$detection_type,  'new_domain_id' => $readyDomain->id, 'domain_type' => $type, 'promotion_id' => $promotion_id,'log_promotion_id'=>$promotion_id, 'is_white_domain'=>$promotion->is_white_domain,'uid' => $uid, 'line' => $line);
                DomainInterceptDetail::model()->insertInterceptDomain($data);

                //写入日志
                $log = new DomainInterCeptLog();
                $log->domain = $domain;
                $log->domain2 = $readyDomain->domain;
                $log->create_time = $last_creat_time;
                $log->update_time = time();
                $log->detection_type = $detection_type;
                $log->domain_type = $type;
                $log->save();
                $ret = array('ret' => 1000, 'msg' => '推广ID:' . $promotion_id . ' 文章域名替换成' . $readyDomain->domain.$is_public);

                die(json_encode($ret));
            } else//跳转域名 白域名
            {
                $this->replaceDomain($domain_id, $domain, $uid, $type, $is_https,$detection_type,$app_type);
            }

        }



    }

    /**
     * 跳转域名和白域名自动替换
     * @param $domain
     * @param $uid
     * @param $type
     * author: yjh
     */
    private function replaceDomain($domain_id, $domain, $uid, $type, $is_https,$detection_type,$app_type)
    {
        $line = ($domain_id % 2) == 1 ? 1 : 2;
        $str = '被拦截';
        if ($detection_type == 1) {
            $str = '掉备案';
        }
        if ($type == 1) {
            $promotions = Promotion::model()->findAll('goto_domain_id=' . $domain_id . ' and status!=1');//下线的推广不替换跳转域名
            $un_online_promotion = Promotion::model()->findAll('goto_domain_id=' . $domain_id . ' and status=1');
        } else {
            $un_online_promotion = Promotion::model()->findAll('white_domain_id=' . $domain_id . ' and status=1');
            $promotions = Promotion::model()->findAll('white_domain_id=' . $domain_id . ' and status!=1');//下线的推广不替换跳转域名
        }
        $un_promotion_str = '';
        $un_promotion = array();
        if (!empty($un_online_promotion)) {
            $un_promotion = array_column(Dtable::toArr($un_online_promotion),'id');
            $un_promotion_str = implode(',',$un_promotion);
        }
        $on_promotion = array();
        if (!empty($promotions)) {
            $on_promotion = array_column(Dtable::toArr($promotions),'id');
        }
        $all_promotion = array_merge($un_promotion,$on_promotion);
        $all_promotion_str = implode(',',$all_promotion);
        if (!$promotions) {
            $data = array('domain_id' => $domain_id, 'detection_type'=>$detection_type, 'line' =>$line, 'domain_type' => $type, 'uid' => $uid,'log_promotion_id'=>$un_promotion_str, 'mark' => '域名的推广不存在');
            DomainInterceptDetail::model()->insertInterceptDomain($data);
            $ret = array('ret' => 4004, 'msg' => '域名的推广不存在,不进行替换');
            die(json_encode($ret));
        }
        //若推广的落地域名被拦截，删除推广缓存
        $redis_flag = Yii::app()->params['basic']['is_redis'];
        $msg = '(';
        foreach ($promotions as $r) {
            //删除推广的缓存信息
            if ($redis_flag == 1) {
                $ret = Yii::app()->redis->getValue('promotion:' . $r->id);
                if ($ret) Yii::app()->redis->deleteValue('promotion:' . $r->id);
            }
            $msg .= " " . $r->id;

        }
        $msg .= ')';

        $readyDomain = DomainList::model()->find('uid = ' . $uid . ' and status in(0,1) and domain_type=' . $type . ' and is_https=' . $is_https . ' and application_type='.$app_type .' order by rand()');
        $types = $type == 1 ? '跳转' : '白';
        if (!$readyDomain) {
            $data = array('domain_id' => $domain_id, 'detection_type'=>$detection_type, 'domain_type' => $type, 'uid' => $uid, 'line' =>$line, 'log_promotion_id'=>$all_promotion_str,'mark' => '备用' . $types . '域名不足,不进行替换');
            DomainInterceptDetail::model()->insertInterceptDomain($data);
            $ret = array('ret' => 4007, 'msg' => '推广ID' . $msg . '的' . $types . '域名'.$str.'，备用' . $types . '域名不足,不进行替换');
            die(json_encode($ret));
        }
        if ($readyDomain->status == 0) {
            $readyDomain->status = 1;
            $readyDomain->save();
        }

        //替换域名
        foreach ($promotions as $r) {
            //域名替换
            if ($type == 1) {
                $promotion_domain = $r->white_domain_id;
                $r->goto_domain_id = $readyDomain->id;
            } else {
                $promotion_domain = $r->goto_domain_id;
                $r->white_domain_id = $readyDomain->id;
            }
            $r->save();
            $data = array('domain_id' => $domain_id, 'detection_type'=>$detection_type, 'new_domain_id' => $readyDomain->id, 'domain_type' => $type, 'promotion_id' => $r->id,'log_promotion_id'=>$r->id,'promotion_domain'=>$promotion_domain, 'is_white_domain'=>$r->is_white_domain,'uid' => $uid, 'line' => $line);
            DomainInterceptDetail::model()->insertInterceptDomain($data);
        }

        //写入日志
        $log = new DomainInterCeptLog();
        $log->domain = $domain;
        $log->domain2 = $readyDomain->domain;
        $log->domain_type = $type;
        $log->create_time = time();
        $log->update_time = time();
        $log->detection_type = $detection_type;
        $log->domain_type = $type;
        $log->save();

        $sql = 'select csname_true from cservice where csno=' . $uid;
        $info = Yii::app()->db->createCommand($sql)->queryAll();
        $uname = $info[0]['csname_true'];

        $ret = array('ret' => 2000, 'msg' => $uname . ',推广ID' . $msg . '的' . $types . '域名'.$str.'，已替换成功！');
        die(json_encode($ret));
    }

    /**
     * 开户类型域名被拦截只修改域名状态为拦截，并提示信息，不做域名替换
     * @param int $domain_id
     * @param int $uid
     */
    private function interceptOpenDomain($domain_id, $domain, $uid, $type, $detection_type)
    {
        $line = ($domain_id % 2) == 1 ? 1 : 2;
//        $promotion = Promotion::model()->find('domain_id=' . $domain_id.' and status!=1');
        $promotion = PromotionDomain::model()->getPromotionByDomain($domain_id,'status!=1');
        if (!$promotion) {
            $data = array('domain_id' => $domain_id, 'detection_type'=>$detection_type, 'domain_type' => $type, 'uid' => $uid,'line' => $line, 'mark' => '域名的推广不存在');
            DomainInterceptDetail::model()->insertInterceptDomain($data);
            $ret = array('ret' => 4004, 'msg' => '域名的推广不存在');
            die(json_encode($ret));
        }
        $promotion = $promotion[0];
        $data = array('domain_id' => $domain_id, 'detection_type'=>$detection_type, 'new_domain_id' => 0, 'domain_type' => $type, 'line' => $line,'promotion_id' => $promotion->id, 'log_promotion_id'=>$promotion->id,'uid' => $uid,'mark'=>'开户推广');
        DomainInterceptDetail::model()->insertInterceptDomain($data);
        //若推广的落地域名被拦截，删除推广缓存
        $redis_flag = Yii::app()->params['basic']['is_redis'];
        if ($redis_flag == 1) {
            $ret = Yii::app()->redis->getValue('promotion:' . $promotion['id']);
            if ($ret) Yii::app()->redis->deleteValue('promotion:' . $promotion['id']);
        }

        //写入日志
        $log = new DomainInterCeptLog();
        $log->domain = $domain;
        $log->domain2 = '';
        $log->domain_type = 3;
        $log->create_time = time();
        $log->update_time = time();
        $log->detection_type = $detection_type;
        $log->domain_type = $type;
        $log->save();
        $sql = 'select csname_true from cservice where csno=' . $uid;
        $info = Yii::app()->db->createCommand($sql)->queryAll();
        $uname = $info[0]['csname_true'];
        $str = '被拦截！';
        if ($detection_type == 1) {
            $str = '掉备案！';
        }
        $ret = array('ret' => 2000, 'msg' => $uname . ',推广ID' . $promotion['id'] . '的域名' . $domain . $str);
        die(json_encode($ret));
    }


    /**
     * 获取idsite,如果域名已添加则不需要新增，如果没有则新增
     * @param $p_domain
     * @return mixed|null
     * author: yjh
     */
    private function getIdSite($p_domain)
    {
        if (!$p_domain) {
            $this->msg(array('state' => 0, 'msgwords' => '未选择推广域名'));
        }
        CActiveRecord::$db = Yii::app()->getPiwikDb();
        $PiwikSiteInfo = PiwikSite::model()->find('main_url=:main_url', array(':main_url' => $p_domain));
        if ($PiwikSiteInfo) $idSite = $PiwikSiteInfo->idsite;
        else {
            $PiwikSite = new PiwikSite();
            $PiwikSite->name = $p_domain;
            $PiwikSite->main_url = $p_domain;
            $PiwikSite->ts_created = date('Y-m-d H:i:s', time());
            $PiwikSite->timezone = 'Asia/Shanghai';
            $PiwikSite->currency = 'USD';
            $PiwikSite->type = 'website';
            $PiwikSite->save();
            $idSite = $PiwikSite->primaryKey;
        }
        CActiveRecord::$db = $db = Yii::app()->getDb();
        return $idSite;
    }

    /**
     * 获取统计js代码
     * @param $p_idsite
     * @return mixed
     * author: yjh
     */
    private function getPiwikJS($p_idsite)
    {
        $config = Yii::app()->params['basic'];
        $url = $config['piwik_url'] . '?module=API&method=SitesManager.getJavascriptTag&idSite=' . $p_idsite . '&format=JSON&token_auth=' . $config['piwik_token'];
        $contents = json_decode(file_get_contents($url))->value;
        return $contents;
    }

    /*
  * 新接口域名拦截替换
  * author: yjh
  */
    public function actionDomainReplace()
    {
        $check_start_time = time();//检测结束时间
        #限制IP
        $ALLOWED_IP = $this->config['allow_ip'];
        $IP = $this->getIP();
        if (!in_array($IP, $ALLOWED_IP)) {
            header('HTTP/1.1 403 Forbidden');
            echo "Access forbidden";
            die;
        }//end foreach
        $domain = isset($_GET['url']) ? $_GET['url'] : '';
        if (!$domain) {
            header('HTTP/1.1 403 Forbidden');
            echo "Access forbidden";
            die;
        }
        $template_desc = '';
        $openidArr=array();
        $sql = "select * from openid_manage";
        $openidArr =  Yii::app()->db->createCommand($sql)->queryAll();
        foreach ($openidArr as $value) {
            $openids[] = $value['openid'];
        }
        $url = $this->config['domain']['replace_api_url'] . '?domain=' . $domain . '';
        $html = file_get_contents($url);

        $a = json_decode($html, 1);
        if ($a['ret'] == 1000) {
            $template_desc .= $domain  . "被拦截 处理成功" . " (" . $a['msg'] . ")" . chr(10);
        } elseif ($a['ret'] == 2000) {
            $template_desc .= $domain  . "被拦截 处理成功" . "(" . $a['msg'] . ")" . chr(10);
        } else {
            $template_desc .= $domain . "被拦截 处理失败(" . $a['ret'] . ',' . $a['msg'] . ")" . chr(10);
        }
        $wdata = $this->get_weixin_data();
        $access_token = $wdata['access_token'];
        $template_id = $wdata['template_id'];
        $check_end_time = time();//检测结束时间
        foreach ($openids as $openid) {
            //仅当被拦截或被拦截域名恢复才通知
            $this->sendEvent($openid, "二部域名监控(新接口)", $template_desc, date('Y-m-d H:i:s', time()), $this->config['domain']['show_domain_url'] . "/?start=" . $check_start_time . "&end=" . $check_end_time, $template_id, $access_token); //发送报警通知
        }
        echo 'success';
    }

    public function actionSendMsg()
    {
        $from = isset($_GET['from']) ? $_GET['from'] : 0;
        $wdata = $this->get_weixin_data();
        $access_token = $wdata['access_token'];
        $template_id = $wdata['template_id'];
        $domain = isset($_GET['url']) ? $_GET['url'] : '';
        $template_desc = $domain . "被拦截 处理成功" . chr(10);
        $sql = "select * from openid_manage";
        $openidArr = Yii::app()->db->createCommand($sql)->queryAll();
        foreach ($openidArr as $value) {
            $openids[] = $value['openid'];
        }
        foreach ($openids as $openid) {
            //仅当被拦截或被拦截域名恢复才通知
            $this->sendEvent($openid, "二部域名监控(新接口)".$from, $template_desc, date('Y-m-d H:i:s', time()), $this->config['domain']['show_domain_url'], $template_id, $access_token); //发送报警通知
        }
        echo 'success';
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

    public function actionGetDomains()
    {
        $sql = "SELECT domain_id FROM `promotion_manage` WHERE status=2";
        $pauseArrs = Yii::app()->db->createCommand($sql)->queryAll();
        $pauseIds=implode(',',array_column($pauseArrs, 'domain_id'));
        $sql = "SELECT * FROM `domain_list` WHERE status=1 and promotion_type=0 and id NOT IN (".$pauseIds.")  ORDER BY `id` DESC";
        $m = Yii::app()->db->createCommand($sql)->queryAll();
        $ret = implode(',5|', array_column($m, 'domain'));
        echo $ret.',1';
        die;
    }

    /**
     * 获取奇数的检测域名
     * author: yjh
     */
    public function actionGetOddDomains()
    {
        #限制IP
        $ALLOWED_IP = $this->config['allow_ip'];
        $IP = $this->getIP();
        if (in_array($IP, $ALLOWED_IP)) {
            $sql = "SELECT * FROM `domain_list` WHERE status=1 and promotion_type=0 and mod(id,2) = 1 ORDER BY `id` DESC";
            $m = Yii::app()->db->createCommand($sql)->queryAll();
            $ret = implode('|',array_column($m, 'domain'));
            echo $ret;
            die;

        }//end foreach
        header('HTTP/1.1 403 Forbidden');
        echo "Access forbidden";
        die;
    }

    /**
     * 获取偶数的检测域名
     * author: yjh
     */
    public function actionGetEvenDomains()
    {
        #限制IP
        $ALLOWED_IP = $this->config['allow_ip'];
        $IP = $this->getIP();
        if (in_array($IP, $ALLOWED_IP)) {
            $sql = "SELECT * FROM `domain_list` WHERE status=1 and promotion_type=0 and mod(id,2) = 0 ORDER BY `id` DESC";
            $m = Yii::app()->db->createCommand($sql)->queryAll();
            $ret = implode('|',array_column($m, 'domain'));
            echo $ret;
            die;

        }//end foreach
        header('HTTP/1.1 403 Forbidden');
        echo "Access forbidden";
        die;
    }


    /**
     * 获得访问的IP
     * Enter description here ...
     */
    function getIP()
    {
        return isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"]
            : (isset($_SERVER["HTTP_CLIENT_IP"]) ? $_SERVER["HTTP_CLIENT_IP"]
                : $_SERVER["REMOTE_ADDR"]);
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

    public  function actionGetOpenids()
    {
        $openids=Dtable::toArr(OpenidManage::model()->findAll());
        helper::json(0, $openids);
        exit;
    }

    public  function actionBeianDomains()
    {
        $sql = "SELECT id FROM `promotion_manage` WHERE status=2";
        $pauseArrs = Yii::app()->db->createCommand($sql)->queryAll();
        $pauseIds=implode(',',array_column($pauseArrs, 'id'));
        $pauseDomains = '';
        if ($pauseIds) {
            //修改为从推广-域名关联表取暂停域名 2018-12-19
            $sql = "SELECT domain_id FROM `promotion_domain_rel`  WHERE promotion_id in (".$pauseIds.")";
            $pause = Yii::app()->db->createCommand($sql)->queryAll();
            $pauseDomains=implode(',',array_column($pause, 'domain_id'));
        }
        $sql = "SELECT domain FROM `domain_list` WHERE  status=1 and  (promotion_type=0 or  promotion_type=3)";
        if ($pauseDomains) {
            $sql .= " and id NOT IN (".$pauseDomains.")";
        }
        $sql .= " ORDER BY `id` DESC";
        $a =  Yii::app()->db->createCommand($sql)->queryAll();
        helper::json(0, $a);
        exit;
    }

    /**
     * 编辑渠道处理结果反馈
     */
    public function actionEditResult()
    {
        $intercept_id = $_POST['intercept_id'];
        if (!$intercept_id) {
            $ret = array('ret' => 1001, 'msg' => '请选择操作域名');
            die(json_encode($ret));
        }
        $user_id = $_POST['user_id'];
        if (!$user_id) {
            $ret = array('ret' => 1001, 'msg' => '您没有操作权限');
            die(json_encode($ret));
        }
        $is_replace = $_POST['is_replace'] ? $_POST['is_replace']:0;
        $intercept = DomainInterceptDetail::model()->findByPk($intercept_id);
        if (!$intercept) {
            $ret = array('ret' => 1001, 'msg' => '拦截记录不存在');
            die(json_encode($ret));
        }
        if ($intercept['is_replace'] == 1) {
            $ret = array('ret' => 1001, 'msg' => '已全部替换的记录无法修改');
            die(json_encode($ret));
        }
        if ($intercept['is_replace'] == $is_replace) {
            $ret = array('ret' => 1001, 'msg' => '渠道处理结果未改变');
            die(json_encode($ret));
        }
        $intercept->is_replace = $is_replace;
        $intercept->user_id = $user_id;
        $intercept->upd_time = time();
        $res = $intercept->save();
        $detection_str = '被拦截';
        if ($intercept->detection_type == 1) {
            $detection_str = '掉备案';
        }
        if ($res) {
            //查询微信信息
            $wdata = $this->get_weixin_data();
            $access_token = $wdata['access_token'];
            $template_id = $wdata['template_id'];
            //查询域名
            $domain = DomainList::model()->findByPk($intercept['domain_id']);
            //查询推广人员
            $sql = " SELECT f.sno FROM promotion_manage p LEFT JOIN finance_pay f  ON f.id=p.finance_pay_id WHERE p.id = ".$intercept['promotion_id'];
            $promotion_staff = Yii::app()->db->createCommand($sql)->queryAll();
            if ($promotion_staff) {
                $sno = $promotion_staff[0]['sno'];
                //推广组长（含上级部门）
                $manage_user = $this->get_promotion_staff_manager($sno);
                //超级管理员
                $send_user = $admin_user = $this->get_super_admin();
                if ($manage_user) {
                    $send_user = array_merge($admin_user,$manage_user);
                }
                $send_user = array_unique($send_user);
                $template_desc = $domain['domain'].$detection_str.','.vars::$fields['intercept_result'][$is_replace];
                if (!empty($send_user)) {
                    $str = implode(',',$send_user);
                    $openidArr = OpenidManage::model()->findAll('system_user in ('.rtrim($str,',').')');
                    foreach ($openidArr as $value) {
                        $openid = $value['openid'];
                        $this->sendEvent($openid, "域名拦截渠道处理反馈详情", $template_desc, date('Y-m-d H:i:s', time()), Yii::app()->params['basic']['show_result_url'].'?intercept_id='.$intercept_id, $template_id, $access_token); //发送报警通知
                    }
                }
            }
            $ret = array('ret' => 200, 'msg' => '修改成功');
            die(json_encode($ret));
        } else {
            $ret = array('ret' => 1001, 'msg' => '修改失败');
            die(json_encode($ret));
        }
    }

    public function actionInterceptResult()
    {
        $intercept_id = $_GET['intercept_id'] ? $_GET['intercept_id'] : 0;
        if (!$intercept_id) {
            die('error 4001');
        }
        $detail = DomainInterceptDetail::model()->findByPk($intercept_id);
        if (!$detail) {
            die('error 4001');
        }
        $sno = $detail['uid'];
        $sql = "SELECT csno,csname_true FROM `cservice` WHERE  csno=".$sno;
        $user = Yii::app()->db->createCommand($sql)->queryAll($sql);
        $domain = DomainList::model()->findAll('( id='.$detail['domain_id'].' or id='.$detail['new_domain_id'].' )');
        $domian_info = array();
        foreach ($domain as $value) {
            $domian_info[$value['id']] = $value;
        }
        //推广
        $promotion = Promotion::model()->findByPk($detail['promotion_id']);
        //渠道
        $channel = Channel::model()->findByPk($promotion['channel_id']);
        //合作商
        $partner = Partner::model()->findByPk($channel['partner_id']);
        //域名类型

        if ($domian_info[$detail['domain_id']]['domain_type'] == 1 || $domian_info[$detail['domain_id']]['domain_type'] == 2) {
            if ($domian_info[$detail['domain_id']]['domain_type'] == 1) {
                $goto_domainInfo = DomainList::model()->findByPk($detail['new_domain_id']);
                $white_domainInfo = DomainList::model()->findByPk($promotion['white_domain_id']);
            }else {
                $goto_domainInfo = DomainList::model()->findByPk($promotion['goto_domain_id']);
                $white_domainInfo = DomainList::model()->findByPk($detail['new_domain_id']);
            }
            if ($white_domainInfo) {
                if ($white_domainInfo->is_https == 1) {
                    $white_domain = "https://" . $white_domainInfo->domain . "/qq/?go=";//白域名
                } else {
                    $white_domain = "http://" . $white_domainInfo->domain . "/qq/?go=";//白域名
                }
            } else {
                $white_domain = Yii::app()->params['basic']['white_domain'];//白域名
            }
            if ($goto_domainInfo->is_https == 1) {
                $new_url = "https://" . $goto_domainInfo->domain . "/" . date("md", time()) . "_goto/" . $detail["promotion_id"] . "_" . $channel['channel_code'] . "_" . $promotion["finance_pay_id"] . ".html";
            } else {
                $new_url = "http://" . $goto_domainInfo->domain . "/" . date("md", time()) . "_goto/" . $detail["promotion_id"] . "_" . $channel['channel_code'] . "_" . $promotion["finance_pay_id"] . ".html";
            }
            if ($white_domain != '') {
                $new_url = $white_domain . urlencode($new_url);//跳转链接
            }
        }else {
            if ($domian_info[$detail['new_domain_id']]['is_https'] == 1) $new_url = "https://" . $domian_info[$detail['new_domain_id']]['domain'];
            else $new_url = "http://" . $domian_info[$detail['new_domain_id']]['domain'];
        }

        $href = $new_url;
        $is_https = $domian_info[$detail['domain_id']]['is_https'] == 1 ? 'https://':'http://';
        $is_public = $domian_info[$detail['domain_id']]['is_public_domain'] == 1?"(公众号)":" ";
        $page['info'] = array(
            'domain' => $is_https.$domian_info[$detail['domain_id']]['domain'],
            'domain_info' => $is_https.$domian_info[$detail['domain_id']]['domain'].$is_public,
            'domain_type' => vars::get_field_str('domain_types', $domian_info[$detail['domain_id']]['domain_type']),
            'promotion_id' => $detail['promotion_id'],
            'user_name' => $user[0] ? $user[0]['csname_true']:'-',
            'partner_name' => $partner['name'] ? $partner['name']:"-",
            'channel_name' => $channel['channel_name'] ? $channel['channel_name'] : '-',
            'channel_code' => $channel['channel_code'] ? $channel['channel_code'] : '-',
            'mark' => $detail['mark'],
            'new_domain' => $href,
            'upd_time' => date('Y-m-d H:i:s',$detail['upd_time']),
            'is_replace' => $detail['is_replace'],
            'replace_txt' => vars::$fields['intercept_result'][$detail['is_replace']],
        );
        $this->render('interceptResult', array('page' => $page));


    }

    /**
     * 超级管理员用户
     * @return array
     */
    private function get_super_admin()
    {
        $role_sql = "SELECT param_value  FROM params WHERE param_name = 'domain_msg_role'";
        $roles = Yii::app()->db->createCommand($role_sql)->queryAll();
        $other_roles = $roles[0]['param_value'];
        $role_where = ' role_id = 1 ';
        if ($other_roles) {
            $role_where = ' role_id in (1,'.trim($other_roles,',').')';
        }
        $data = array();
        $sql = "SELECT sno  FROM cservice_roles WHERE ".$role_where;
        $users = Yii::app()->db->createCommand($sql)->queryAll();
        foreach ($users as $user) {
            $data[] = $user['sno'];
        }
        $data[] = Yii::app()->params['management']['super_admin_id'];
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
        $dbm = Yii::app()->db;
        $sql = "SELECT * FROM cservice_groups WHERE  sno=".$user_id;
        $groups = $dbm->createCommand($sql)->queryAll();
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
            $managers = $dbm->createCommand($sql)->queryAll();
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
        $m =  Yii::app()->db->createCommand($sql)->queryAll();
        if (empty($m)) return $parent_groups;
        $group_id = $m[0]['parent_id'];
        $this->get_parent_groups($group_id);
        return $parent_groups;
    }

    private function data_authority($uid){
        Yii::import('application.modules.admin.models.*', 1);
        //判断是否为超级管理员
        if($uid==Yii::app()->params['management']['super_admin_id'] ) return 0;
        //判断权限是否为超级管理员权限
        $urole = AdminUser::model()->get_user_role($uid);
        foreach ($urole as $val){
            if($val['role_id'] == 1) return 0;
        }

        //获取登录人员所在部门及以下部门
        $ugroups=AdminUser::model()->get_user_group($uid);
        $ugroupArr=$groupArr=array();
        foreach($ugroups as $r){
            $ugroupArr[]=$r['groupid'];
        }
        $mgroupArr = AdminUser::model()->get_manager_group($uid);
        $ugroupArr = empty($mgroupArr)?$ugroupArr:array_unique(array_merge($ugroupArr,$mgroupArr));
        foreach ($ugroupArr as $value){
            $groupArr = array_merge($groupArr,AdminGroup::model()->get_children_groups($value));
        }
        $unique_arr = array_unique ( $groupArr );
        $repeat_arr = array_diff_assoc ( $groupArr, $unique_arr );
        $groupArr = array_diff($groupArr,$ugroupArr);
        $groupArr = array_merge($groupArr,$repeat_arr);
        $groupArr = array_merge($groupArr,$mgroupArr);
        $groupStr = implode(',',array_unique($groupArr));
        $userStr = $uid;
        if(!empty($groupStr)){
            $userStr .= ",".implode(',',AdminUserGroup::model()->getUsersByGroups($groupStr));


        }
        //获取可以查看数据的人员

        return $userStr;
    }

    private function getInterceptSnoData()
    {
        $start_time = $_GET['start'];
        $end_time = $_GET['end'];
        $line = isset($_GET['line'])?"=".$_GET['line']:"in (0,1,2)";
        $type = isset($_GET['type'])?$_GET['type']:0;
        $user_id = $_GET['user_id'] ? $_GET['user_id'] : 0;
        //可查看人员权限
        $user_str = $this->data_authority($user_id);
        //若推广id为0，查询推广表查询推广人员
        $finance_sno = array();
        $log_promotion = '';
        $un_promotion_domain = array();
        $un_promotion = Dtable::toArr(DomainInterceptDetail::model()->findAll(" promotion_id=0 AND time BETWEEN  " . $start_time . " AND " . $end_time ." AND line ".$line ." AND detection_type=".$type));
        foreach ($un_promotion as $value) {
            if ($value['log_promotion_id']!= '' || $value['log_promotion_id']!= '0') {
                $log_promotion .= $value['log_promotion_id'].',';
            }
        }
        if ($log_promotion) {
            $un_promotion_domain = array_unique(explode(',',rtrim($log_promotion,',')));
        }
        foreach ($un_promotion_domain as $key=>$pd) {
            if (!$pd) unset($un_promotion_domain[$key]);
        }
        //推广id不为0的推广人员
        $on_promotion = Dtable::toArr(DomainInterceptDetail::model()->findAll(" promotion_id!=0 AND time BETWEEN  " . $start_time . " AND " . $end_time ." AND line ".$line ." AND detection_type=".$type));
        $on_pro_ids = array_column($on_promotion,'promotion_id');
        $all_pro_ids = array_unique(array_merge($un_promotion_domain,$on_pro_ids));
        $pro_finance = array();
        $pro = array();
        $pro_id = array();
        if (!empty($all_pro_ids)) {
            $sql = "SELECT id,finance_pay_id FROM promotion_manage WHERE id in (".implode(',',$all_pro_ids).")";
            $pro = Yii::app()->db->createCommand($sql)->queryAll();
        }
        foreach ($pro as $f) {
            $pro_finance[] = $f['finance_pay_id'];
            $pro_id[$f['id']] =$f['finance_pay_id'];
        }
        $pro_finance = array_unique($pro_finance);
        foreach ($pro_finance as $key=>$value) {
            if (empty($value)) {
                unset($pro_finance[$key]);
            }
        }
        if (!empty($pro_finance)) {
            $sql = "SELECT id,sno FROM finance_pay WHERE id in (".implode(',',$pro_finance).")";
            $finance = Yii::app()->db->createCommand($sql)->queryAll();
            $finance_sno = array_combine(array_column($finance,'id'),array_column($finance,'sno'));
        }
        //查询推广id为0的记录
        $sql = "SELECT a.*,b.domain
                    FROM domain_intercept_detail AS a
                    LEFT JOIN domain_list AS b ON b.id=a.domain_id
                    WHERE a.promotion_id=0 AND a.time BETWEEN  " . $start_time . " AND " . $end_time ." AND a.line ".$line ." AND a.detection_type=".$type ;
        $m2 = Yii::app()->db->createCommand($sql)->queryAll();
        //查询推广id为0的记录的权限判断
        if ($user_str != 0) {
            foreach ($m2 as $key=>$value) {
                $pros = explode(',',$value['log_promotion_id']);
                foreach ($pros as $p) {
                    $fi = $pro_id[$p];
                    $p_sno = $finance_sno[$fi];
                    if (strpos(','.$user_str.',',','.$p_sno.',') === false) {
                        unset($m2[$key]);
                    }
                }
            }
        }
        $data['user_str'] = $user_str;
        $data['finance_sno'] = $finance_sno;
        $data['m2'] = $m2;
        return $data;

    }

    public function actionGetDomainSno()
    {
        //查询推广人员
        $domain = $_GET['domain'];
        $domainModel = DomainList::model()->findByAttributes(array('domain' => $domain));
        if (!$domainModel) {
            $ret = array('ret' => 200, 'send_user' => array());
            die(json_encode($ret));
        }
        $domain_id = $domainModel->id;
        $super_admin = $this->get_super_admin();
        $where = ' 1 ';
        if ($domainModel->domain_type == 0 || $domainModel->domain_type == 3) {
            $promotion = PromotionDomain::model()->getProIdsByDomain($domain_id);
            if ($promotion) {
                $where .=  ' and p.id in ('.implode(',',$promotion).')  ';
            }else {
                $where .=  ' and p.id=0 ';
            }
        }
        if ($domainModel->domain_type == 1) {
            $where .= ' and goto_domain_id = '.$domain_id;
        }
        if ($domainModel->domain_type == 2) {
            $where .= ' and white_domain_id = '.$domain_id;
        }
        $where .= ' and p.status!=1';
        $sql = " SELECT f.sno FROM promotion_manage p LEFT JOIN finance_pay f  ON f.id=p.finance_pay_id WHERE ".$where;
        $promotion_staff = Yii::app()->db->createCommand($sql)->queryAll();
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
        $ret = array('ret' => 200, 'send_user' => $send_user);
        die(json_encode($ret));
    }

}