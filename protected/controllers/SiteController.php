<?php


date_default_timezone_set('Asia/Shanghai');

class SiteController extends HomeController
{
    public $page;


    public function filters()
    {
        return array(
            array(
                'COutputCache',
                'duration' => 3600 * 24 * 0,
                'varyByParam' => array('from'),
                'dependency' => array(
                    'class' => 'CDbCacheDependency',
                    'sql' => 'SELECT MAX(log_id) FROM cservice_aclog',  //根据管理员操作日志去检测缓存是否失效
                )
            )
        );
    }

    public function actionGoto()
    {
        $plan_id = $this->get('pid');
        $TTL = Yii::app()->params['basic']['redis_time'];//缓存时长
        if (!$plan_id) {
            die('error 4001 ');
        }
        if (!preg_match('~^\d+_\w+_\d+$~', $plan_id)) {
            die('error 4002');
        }
        $strArr = explode('_', $plan_id);
        if (count($strArr) != 3) {
            die('error 4003');
        }
        $pid = $strArr[0]; //推广id
        $channel_code = $strArr[1];
        
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $key = 'jump_ground_visit_queue';  //value 推广ID_类型（1白名单 2goto 3落地）_时间_IP_UA_当前URL
        $now_request_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        Yii::app()->redis->lpush($key, $pid.'_:_2_:_'.time().'_:_'.helper::getip().'_:_'.$user_agent.'_:_'.$now_request_url);
        
        $promotion = array();
        $promotion_id = $pid;
        $redis_flag = Yii::app()->params['basic']['is_redis'];
        if ($redis_flag == 1) $promotion = Yii::app()->redis->getValue('promotion:' . $promotion_id);
        //需要重新缓存推广的多个域名
        if (!$promotion || !is_array($promotion->domain_id)) {
            $promotion = Promotion::model()->findByPk($promotion_id);
            //缓存推广的落地域名信息 lxj 2019-01-04
            $promotion_arr = $this->toArr($promotion);
            $pro_domains = PromotionDomain::model()->getPromotionsDomains($promotion_id);
            if ($pro_domains) {
                $promotion_arr['domain_list'] = $pro_domains[$promotion_id];
            }
            //缓存推广域名及状态
            $promotion->domain_id = $promotion_arr['domain_list'];
            if ($redis_flag == 1) Yii::app()->redis->setValue('promotion:' . $promotion_id, $promotion, $TTL);
        }

        if (!$promotion) {
            die('error 4004');
        }
        if ($promotion->status != 0) {
            $url = Yii::app()->params['basic']['errorurl'];
            header('location:' . $url);
            exit;
        }
        if ($promotion->is_pc_show == 1) {
            if (stristr($user_agent, 'windows')) {
                $url = $promotion->pc_url;
                if (!$url) $url = Yii::app()->params['basic']['pc_url'];
                header('Location:' . $url);
                exit;
            }
        }
        //随机选取一个推广域名跳转
        //优先选取正常的域名跳转
        $use_domain = array();
        foreach ($promotion->domain_id as $value) {
            if ($value['domain_status'] == 0 || $value['domain_status'] == 1) {
                $use_domain[] = $value;
            }
        }
        if ($use_domain) {
            $key = rand(0,count($use_domain)-1);
            $tg_domain = $use_domain[$key];
        }else {
            $key = rand(0,count($promotion->domain_id)-1);
            $tg_domain = $promotion->domain_id[$key];
        }
        $pro_arr = Dtable::toArr($promotion);
        $pro_arr['channel_code'] = $channel_code;
        $pro_arr['domain_list'][] = $tg_domain;
        $tg_url = helper::build_tg_link($pro_arr);
        $tg_link = $tg_url[0]['domain'];
        if (count($strArr) != 3) {
            echo "<script type='text/javascript'>location.href = '" . $tg_link . "'</script>";
        } else {
            header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
            header('location:' . $tg_link);
        }
        exit;

    }


    /**
     * 判断访问规则
     * author: yjh
     */
    private function check_url_rule()
    {
        if ($this->get('mid')) {
            $plan_id = helper::url_encrypt($this->get('mid'), 'D');
        } elseif ($this->get('did')) {
            $plan_id = helper::digital_encrypt($this->get('did'), 16, 'D') . "_unknow_111";
        } else {
            $plan_id = $this->get('pid');
        }
        $url = $_SERVER['HTTP_HOST'];
        $domainInfo = DomainList::model()->find("domain='$url'");

        if (!$plan_id) {
            $domain_id = $domainInfo->id;
            if ($domain_id) {
                $promotionInfo = PromotionDomain::model()->getPromotionByDomain($domain_id,' (promotion_type=2 || promotion_type=3) ','p.*');
                if ($promotionInfo) {
                    $promotionInfo = $promotionInfo[0];
                    $channel_code = Channel::model()->getChannelCode($promotionInfo['channel_id']);
                    $plan_id = $promotionInfo['id'] . "_" . $channel_code . "_" . $promotionInfo['finance_pay_id'];
                } else {
                    die('error 4001');
                }
            } else {
                die('error 4001');
            }
        }
        if (!preg_match('~^\d+_\w+_\d+$~', $plan_id)) {
            die('error 4002');
        }
        $strArr = explode('_', $plan_id);
        if (count($strArr) != 3) {
            die('error 4003');
        }
        return $strArr;
    }

    public function actionIndex()
    {
        $page = array();
        
        $url = $_SERVER['HTTP_HOST'];
        $strArr = $this->check_url_rule();
        $pid = $strArr[0]; //推广id
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $visit_key = 'jump_ground_visit_queue';  //value 推广ID_类型（1白名单 2goto 3落地）_时间_IP_UA_当前URL
        $now_request_url = 'http://'.$url.$_SERVER['REQUEST_URI'];
        Yii::app()->redis->lpush($visit_key, $pid.'_:_3_:_'.time().'_:_'.helper::getip().'_:_'.$user_agent.'_:_'.$now_request_url);
        
        $domainInfo = DomainList::model()->find("domain='$url'");
        $nid = $this->get('nid');
        $id = $this->get('id');
        $TTL = Yii::app()->params['basic']['redis_time'];//缓存时长
        $promotion_id = $pid;
        if ($id > 10000) {
            $t = time();
            $start = mktime(0, 0, 0, date("m", $t), date("d", $t), date("Y", $t));
            $end = mktime(23, 59, 59, date("m", $t), date("d", $t), date("Y", $t));
            $expire_time = Yii::app()->params['basic']['expire_time'];
            //推广链接请求判断时间是否属于当天（防止修改链接可访问），判断时间是否超过失效时间
            if ($id < $start || $id > $end || $t > ($id + $expire_time)) {
                $url = Yii::app()->params['basic']['errorurl'];
                header('location:' . $url);
                exit;
            }
        }
        $redis_flag = Yii::app()->params['basic']['is_redis'];
        if ($redis_flag == 1) {
            $page = Yii::app()->redis->getValue('article:' . $promotion_id);
        }
        if (!$page || !isset($page['info']['qq_share_link'])) {
            $promotion = Promotion::model()->findByPk($promotion_id);
            if (!$promotion) {
                die('error 4004');
            }
            if ($promotion->status != 0) {
                $url = Yii::app()->params['basic']['errorurl'];
                header('location:' . $url);
                exit;
            }
            if ($promotion->is_pc_show == 1) {
                if (stristr($user_agent, 'window')) {
                    $url = $promotion->pc_url;
                    if (!$url) $url = Yii::app()->params['basic']['pc_url'];
                    header('Location:' . $url);
                    exit;
                }
            }
//        $template_id = $promotion->origin_template_id;
            $template = OnlineMaterialManage::model()->findByAttributes(array('promotion_id' => $promotion_id,'is_main_page'=>1));
            if (!$template) {
                die('error 4005');
            }

            $fiancePay = InfancePay::model()->findByPk($promotion->finance_pay_id);
            if (!$fiancePay) {
                die('error 4008');
            }
            $weixins = WeChat::model()->getWeixins($fiancePay->weixin_group_id);
            if (!$weixins) {
                die('error 4012');
            }
            $pro_data = Dtable::toArr($promotion);
            $pro_data['channel_code'] = Channel::model()->getChannelCode($promotion->channel_id);
            $goto_link = helper::build_goto_link($pro_data,$promotion->is_white_domain);
            if (!$goto_link || $goto_link == 2) {
                $qq_share_link = $now_request_url;
            }else {
                $qq_share_link = $goto_link;
            }

//            print_r($weixins);
            $weixinList = array();
            foreach ($weixins as $r) {
                $data = array();
                $data['id'] = $r['id'];
                $data['weixin_name'] = $r['wechat_id'];
                $data['customer_service_id'] = $r['customer_service_id'];
                $data['land_url'] = $r['land_url'];
                $data['tg_uid'] = $r['promotion_staff_id'];
                $rst = Resource::model()->findByPk($r['qrcode_id']);
                if ($rst) {
                    $data['weixin_img'] = $rst->resource_url;
                    $data['img_width'] = $rst->r_width;
                    $data['img_height'] = $rst->r_height;
                } else {
                    $data['weixin_img'] = '';
                    $data['img_width'] = '';
                    $data['img_height'] = '';
                }
                $weixinList[] = $data;
            }
            $page['weixinList'] = $weixinList;
            $page['info'] = Dtable::toArr($template);
            //qq浏览器分享到微信链接
            $page['info']['qq_share_link'] = $qq_share_link;
            $page['url'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];

            $page['info']['independent_cnzz'] = $promotion->independent_cnzz;
            $page['info']['minus_proportion'] = $promotion->minus_proportion;
            //修改为按域名总统计组别获取总统计代码 lxj 2018-12-29
            if ($domainInfo->cnzz_code_id != 0) {
                $page['info']['total_cnzz'] = CnzzCodeManage::model()->findByPk($domainInfo->cnzz_code_id)->total_cnzz;
            } else {
                $page['info']['total_cnzz'] = $promotion->total_cnzz;
            }
            $testPids = Yii::app()->params['basic']['bpids'];
            $testPidArr = explode(',', $testPids);
            if (in_array($promotion_id, $testPidArr)) {
                $page['info']['article_block'] = $this->toArr(ArticleBlock::model()->find("1 order by rand()"));
            }
            if ($page['info']['article_type'] == 0) {
                $psq_flag = $page['info']['psq_id'] == 0 ? 0 : 1;
                if ($psq_flag == 1) {
                    $page['psqList'] = Questionnaire::model()->getPSQListByPk($page['info']['psq_id']);
                    $page['psqNum'] = count($page['psqList']);
                }
                if ($page['info']['is_order'] == 1 && $page['info']['order_id'] != 0) {
                    $payment = vars::$fields['payment'];
                    $page['info']['payments'] = $payment;
                    $page['info']['payment'] = helper::decbin_digit($page['info']['payment'], count($payment));
                    $page['info']['order'] = $this->toArr(OrderTemplete::model()->findByPk($page['info']['order_id']));
                    $page['info']['order']['packages'] = OrderTemplete::model()->getPackageInfo($page['info']['order_id']);
                    $page['info']['order']['packageNames'] = array_column($page['info']['order']['packages'], 'package_name');
                } else {
                    $page['info']['order']['is_suspend'] = 0;
                }
            } else if ($page['info']['article_type'] == 1) {
                $f_flag = $page['info']['first_audio'] == 0 ? 0 : 1;
                $s_flag = $page['info']['second_audio'] == 0 ? 0 : 1;
                $v_flag = $page['info']['third_audio'] == 0 ? 0 : 1;
                $psq_flag = $page['info']['psq_id'] == 0 ? 0 : 1;
                $r_flag = $page['info']['review_id'] == 0 ? 0 : 1;
                $page['r_type'] = MaterialReview::model()->findByPk($page['info']['review_id'])->review_type;

                if ($f_flag == 1) $page['first_audio'] = MaterialAudio::model()->getUrlByPk($page['info']['first_audio']);
                if ($s_flag == 1) $page['second_audio'] = MaterialAudio::model()->getUrlByPk($page['info']['second_audio']);
                if ($v_flag == 1) $page['third_audio'] = MaterialAudio::model()->getUrlByPk($page['info']['third_audio']);

                if ($psq_flag == 1) {
                    $page['psqList'] = Questionnaire::model()->getPSQListByPk($page['info']['psq_id']);
                    $page['psqNum'] = count($page['psqList']);
                }
                if ($r_flag == 1) {
                    $page['reviewDetailList'] = MaterialReviewDetail::model()->findAllByAttributes(array('review_id' => $page['info']['review_id']));
                    $page['reviewNum'] = count($page['reviewDetailList']);
                }
            } else if ($page['info']['article_type'] == 2) {
                $reviewInfo = Dtable::toArr(MaterialReview::model()->findByPk($page['info']['review_id']));
                $reviewDetailInfo = Dtable::toArr(MaterialReviewDetail::model()->findAll('review_id=' . $page['info']['review_id'] . " order by id asc"));
                $numArr = array_count_values(array_column($reviewDetailInfo, 'floor'));
                $num = 0;
                $ret = array();
                foreach ($numArr as $key => $val) {
                    $ret[] = array_slice($reviewDetailInfo, $num, $val);
                    $num += $val;
                }
                $page['reviewInfo'] = $reviewInfo;
                $page['reviewDetailInfo'] = $ret;
                $page['appSign'] = array("安卓客户端", "IOS客户端");
            } elseif ($page['info']['article_type'] == 3) {
                $page['info']['site_page'] = 1;
            }
            if ($redis_flag == 1) {
                Yii::app()->redis->setValue('article:' . $promotion_id, $page, $TTL);
            }
        }
        if ($domainInfo['is_public_domain'] == 1) {
            $appid = Yii::app()->params['weChat_config']['appID'];
            $secret = Yii::app()->params['weChat_config']['appsecret'];
            $access_token = Yii::app()->redis->getValue('access_token:' . $appid);
            if(!$access_token) {
                /* 不在有效期，重新发送请求，获取access_token */
                $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret;
                $ret = $page_data['result'] = helper::get_public_conetnts($url);
                if ($ret) {
                    $access_token_ret = $ret->access_token;
                    $access_token = $ret->access_token;
                    Yii::app()->redis->setValue('access_token:' . $appid, $access_token_ret, 7100);
                }
            }
            $ticket = Yii::app()->redis->getValue('jsapi_ticket:' . $appid);
            if(!$ticket) {
                $jsapi_ticket = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' . $access_token . '&type=jsapi';
                $get_ret = helper::get_public_conetnts($jsapi_ticket);
                if ($get_ret) {
                    $ticket = $get_ret->ticket;
                    Yii::app()->redis->setValue('jsapi_ticket:' . $appid, $ticket, 7100);
                }
            }

            $timestamp = time();
            $noncestr = $this->create_password(16);
            $tmp = array('noncestr' => $noncestr, 'jsapi_ticket' => $ticket, 'timestamp' => $timestamp, 'url' => $now_request_url);
            ksort($tmp);
            $string = '';
            foreach ($tmp as $key => $val) {
                $string .= $key . '=' . $val.'&';
            }
            $string2 = rtrim($string, '&');
            $signature = sha1($string2);
        }
        
        //cnzz扣量处理-----------start
        $minus_proportion = $page['info']['minus_proportion'];  //扣量比例
        if($minus_proportion > 0){  //有设置扣量比例
            $ip = helper::getip();  //获取IP
            $city_ids_redis_key = 'city_ids:'.$promotion_id;    //不扣量城市key
            $kl_ips_pool_key = 'kl_ip_pool:'.$promotion_id;       //扣掉IP池key
            $kl_ips_pool_string = Yii::app()->redis->getValue($kl_ips_pool_key);
            //判断IP是否已被扣量,已被扣量的IP直接不再加入
            $ip_str = $ip.';';
            if(strpos($kl_ips_pool_string, $ip_str) !== false){
                $page['info']['independent_cnzz'] = '';
            }else{
                //判断IP是否在不扣量地区
                $ip_city = $this->get_city();
                $linkage_id = $ip_city['linkage_id'];
                //不扣量地区
                $city_ids_string = Yii::app()->redis->getValue($city_ids_redis_key);
                if(!$city_ids_string){
                    $city_ids_string = PromotionDeductionAddressRel::model()->find('promotion_id='.$promotion_id)->province_city_ids;
                    Yii::app()->redis->setValue($city_ids_redis_key, $city_ids_string);
                }
                $city_ids = array();
                if($city_ids_string){
                    $city_ids =explode(',', $city_ids_string);
                }
                //该ip不在扣量比例里面
                if(!in_array($linkage_id,  $city_ids)){
                    $visitor_key = 'visitor:'.$promotion_id;    //数量key
                    $ips_pool_key = 'ip_pool:'.$promotion_id;       //IP池key
                    $visitor_num_redis_time = strtotime(date("Y-m-d 23:59:59") - time());   //次数缓存时间当天有效
                    $ips_pool_string = Yii::app()->redis->getValue($ips_pool_key);
                    //判断IP是否在IP池中
                    if(strpos($ips_pool_string, $ip_str) === false){
                        //不在池中，存入IP池
                        $ips_pool_string .= $ip.';';
                        Yii::app()->redis->setValue($ips_pool_key, $ips_pool_string, $visitor_num_redis_time);
                        //记录次数
                        $last_visitor_num = Yii::app()->redis->getValue($visitor_key);
                        $now_visitor_num = $last_visitor_num ? $last_visitor_num + 1 : 1;   //访问数量
                        Yii::app()->redis->setValue($visitor_key, $now_visitor_num, $visitor_num_redis_time);
                        //假设比例为10 则第11个扣量
                        $is_cnzz = $now_visitor_num - $minus_proportion;   
                        if($is_cnzz == 1){
                            $kl_ips_pool_string .= $ip.';';
                            Yii::app()->redis->setValue($visitor_key, 0, $visitor_num_redis_time);
                            Yii::app()->redis->setValue($kl_ips_pool_key, $kl_ips_pool_string, $visitor_num_redis_time);
                            $page['info']['independent_cnzz'] = '';
                        }
                    }
                }
            }
        }
        //cnzz扣量处理-----------end


        
        if ($page['info']['is_vote'] == 1 && $page['info']['vote_id'] != 0 && $nid == '') {
            $data = array();
            if ($redis_flag == 1) $data = Yii::app()->redis->getValue('psq:' . $promotion_id);
            if (!$data) {
                $data = $this->toArr(Questionnaire::model()->findByPk($page['info']['vote_id']));
                $data['psq'] = $this->toArr(Quest::model()->findAll('qus_id =' . $page['info']['vote_id']));
                $data['psq_count'] = count($data['psq']);
                if ($redis_flag == 1) Yii::app()->redis->setValue('psq:' . $promotion_id, $data, $TTL);
            }
            $this->render('/votepage', array('page' => $data));
            exit;
        }
        
        $page['info']['css_cdn_url'] = '';  //css、js使用
        $page['info']['cdn_url'] = '';  //image使用
        $noCdnArr = array();
        $nocdnids = trim(Yii::app()->params['basic']['nocdnids']);
        if(!empty($nocdnids)){
            $noCdnArr = explode(',', $nocdnids);
        }
        if (!in_array($promotion_id, $noCdnArr)) {
            if ($domainInfo->is_https == 1) {
                //cdn的https域名统一为img.1188sl.com
                $cdn_https = Yii::app()->params['basic']['cdn_url'];
                if($cdn_https){
                    $page['info']['css_cdn_url'] = "https://" . $cdn_https;//CDN域名
                    $page['info']['cdn_url'] = "https://" . $cdn_https;//CDN域名
                }
            }else{
                if(Yii::app()->params['basic']['cdn_url']){
                    $page['info']['css_cdn_url'] = "http://" . Yii::app()->params['basic']['cdn_url'];//CDN域名
                }
                if((Yii::app()->params['upload_server']['imgUrl'])){
                    $page['info']['cdn_url'] = "http://" . Yii::app()->params['upload_server']['imgUrl'];//CDN域名
                }
            }
        }
        $domain_http = 'http://';
        if ($domainInfo->is_https == 1) $domain_http = 'https://';
        $page['info']['qq_share']['img_url'] = $domain_http.$_SERVER['HTTP_HOST'];
        if ($page['info']['cdn_url']){
            $page['info']['qq_share']['img_url'] = $page['info']['cdn_url'];
        }elseif (Yii::app()->params['basic']['css_cdn_url']) {
            $page['info']['qq_share']['img_url'] = $page['info']['css_cdn_url'];
        }
        
        $lazyids = Yii::app()->params['basic']['lazyids'];
        $noLazyArr = explode(',', $lazyids);
        if ($noLazyArr && in_array($promotion_id, $noLazyArr)) {
            $page['info']['is_lazy'] = 1;
        } else {
            $page['info']['is_lazy'] = 0;
        }

        $page['info']['link'] = $now_request_url;

        $page['info']['nonceStr'] = $noncestr;
        $page['info']['timestamp'] = $timestamp;
        $page['info']['signature'] = $signature;

        if ($page['info']['article_type'] == 0) {
            $this->render('/index', array('page' => $page));
        } else if ($page['info']['article_type'] == 1) {
            $this->render('/voice', array('page' => $page));
        } else if ($page['info']['article_type'] == 2) {
            $this->render('/forum', array('page' => $page));
        } else if ($page['info']['article_type'] == 3) {
            //查询相同的编码和图文类型
            $sql = "select * FROM online_material_manage WHERE `promotion_id`=" . $promotion_id . " AND article_code= '" . $page['info']['article_code'] . "' order by id";
            $data = Yii::app()->db->createCommand($sql)->queryAll();
            $num = $this->get('data_num') ? $this->get('data_num') : 0;
            $page_data = $data[$num];

            $tmparr = parse_url($page['url']);
            $rstr = empty($tmparr['scheme']) ? 'http://' : $tmparr['scheme'] . '://';
            $rstr .= $tmparr['host'] . $tmparr['path'];
            $page_data['url'] = $rstr;

            $page_data['link'] = $now_request_url;
            $page_data['nonceStr'] = $noncestr;
            $page_data['timestamp'] = $timestamp;
            $page_data['signature'] = $signature;

              $this->render('/weChat', array('idata' => $page_data, 'page' => $page, 'data' => $data));
            }
    }

    /**
     * 创造数字英文字符串
     * author: hlc
     */
    private function create_password($length)
    {
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        $key = '';
        for($i=0;$i<$length;$i++)
        {
            $key .= $pattern{mt_rand(0,36)};    //生成php随机数
        }
        return $key;
    }

    /**
     * 二维码，微信号长按统计
     * author: yjh
     */
    public function actionPressStatistics()
    {
        $promotion_id = $this->get('promotion_id');
        $partner_id = $this->get('partner_id');
        $channel_id = $this->get('channel_id');
        $tg_uid = $this->get('tg_uid');
        $weixin_id = $this->get('weixin_id');
        if (!$promotion_id || !$partner_id || !$channel_id || !$tg_uid || !$weixin_id)
            echo 'failed';
        $date = strtotime(date('Ymd'));
        $d_hour = date('YmdH');
        $condtion = "d_hour='" . $d_hour . "' and date=$date and weixin_id=$weixin_id and tg_uid=$tg_uid and channel_id=$channel_id and promotion_id=$promotion_id";
        $info = FansInputPredict::model()->find($condtion);
        if (!$info) {
            $info = new FansInputPredict();
            $info->num = 1;
            $info->promotion_id = $promotion_id;
            $info->partner_id = $partner_id;
            $info->channel_id = $channel_id;
            $info->tg_uid = $tg_uid;
            $info->weixin_id = $weixin_id;
            $info->date = $date;
            $info->d_hour = $d_hour;
        } else
            $info->num = $info->num + 1;
        $ret = $info->save();
        if ($ret) echo 'successed';
        else echo 'failed';
        exit;
    }

    /**
     * 存第三方统计微信号长按数据
     * author:fang
     */
    public function actionInsertPress()
    {
        $domain = $_POST['domain'];
        $ip = $_POST['ip'];
        $type = $_POST['type'];
        //$time = strtotime(date('Ymd H00'));
        $date = strtotime(date('Ymd'));
        $wehcat = $_POST['wechat'];
        $wehcat = str_replace('微信号：', '', $wehcat);
        $data = LongPress::model()->findByAttributes(array('ip' => $ip, 'dates' => $date, 'type' => $type));
        if (count($data) > 0) return;
        $info = New LongPress();
        $info->ip = $ip;
        $info->type = $type;
        $info->times = time();
        $info->dates = $date;
        $info->domain_id = DomainList::model()->findByAttributes(array('domain' => $domain))->id;
        $info->wechat_id = WeChat::model()->findByAttributes(array('wechat_id' => $wehcat))->id;
        $info->save();

    }

    /**
     * 存问卷统计数据
     * author: fang
     */
    function actionInsertVote()
    {
        $vote_id = $_POST['vote_id'];
        $ip = $_POST['ip'];
        $answer = $_POST['answer'];
        $promotion_id = $_POST['promotion_id'];
        if (!$vote_id || !$ip || !$promotion_id) return 0;
        if ($vote_id == 0 || $ip == 0 || $promotion_id == 0) return 0;
        $data = StatVoteDetail::model()->findByAttributes(array('vote_id' => $vote_id, 'ip' => $ip));
        if (!$data) {
            $info = StatVoteTotal::model()->findByAttributes(array('vote_id' => $vote_id));
            if (!$info) {
                $info = new StatVoteTotal();
                $info->vote_id = $vote_id;
                $info->promotion_id = $promotion_id;
                $info->vote_total = 0;
                $info->create_date = time();
            }
            $info->vote_total = $info->vote_total + 1;
            $info->save();
            $data = new StatVoteDetail();
            $data->vote_id = $vote_id;
            $data->ip = $ip;
            $data->answer = $answer;
            $data->promotion_id = $promotion_id;
            $data->create_date = time();
            $data->save();
        }
    }

    //预览图文
    public function actionShowPreview()
    {
        $page = array();
        $nid = $this->get('nid');

        //显示表单
        if (!$this->get('id')) $this->msg(array('state' => 1, 'msgwords' => '没有传入数据！'));
        $info = MaterialArticleTemplate::model()->findByPk($this->get('id'));
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }

        $page['info'] = Dtable::toArr($info);
        
        $page['info']['css_cdn_url'] = '';  //css、js使用
        $page['info']['cdn_url'] = '';  //image使用
        if(Yii::app()->params['basic']['cdn_url']){
            $page['info']['css_cdn_url'] = "http://" . Yii::app()->params['basic']['cdn_url'];//CDN域名
        }
        if((Yii::app()->params['upload_server']['imgUrl'])){
            $page['info']['cdn_url'] = "http://" . Yii::app()->params['upload_server']['imgUrl'];//CDN域名
        }
            
        $page['info']['promotion_id'] = 0;
        $page['url'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];


        if ($page['info']['is_vote'] == 1 && $page['info']['vote_id'] != 0 && $nid == '') {
            $data = $this->toArr(Questionnaire::model()->findByPk($page['info']['vote_id']));
            $data['psq'] = $this->toArr(Quest::model()->findAll('qus_id =' . $page['info']['vote_id']));
            $data['psq_count'] = count($data['psq']);
            $this->render('/votepage', array('page' => $data));
            exit;
        }
        $page['info']['is_lazy'] = Yii::app()->params['basic']['is_lazyload'];


            if ($page['info']['article_type'] == 0) {
                if ($page['info']['is_order'] == 1 && $page['info']['order_id'] != 0) {
                    $page['info']['order'] = $this->toArr(OrderTemplete::model()->findByPk($page['info']['order_id']));
                    $page['info']['order']['packages'] = $this->toArr(OrderPackageRelation::model()->findAll('order_templete_id =' . $page['info']['order_id']));
                    $page['info']['order']['packageNames'] = array_column($page['info']['order']['packages'], 'package_name');
                    //my_print($page['info']['order']);
                } else {
                    $page['info']['order']['is_suspend'] = 0;
                }
                $this->render('/showNormPreview', array('page' => $page));
            } else if ($page['info']['article_type'] == 1) {
                $this->render('/showAudioPreview', array('page' => $page));

            } else if ($page['info']['article_type'] == 2) {
                $reviewInfo = Dtable::toArr(MaterialReview::model()->findByPk($page['info']['review_id']));
                $reviewDetailInfo = Dtable::toArr(MaterialReviewDetail::model()->findAll('review_id=' . $page['info']['review_id'] . " order by id asc"));
                $numArr = array_count_values(array_column($reviewDetailInfo, 'floor'));
                $num = 0;
                $ret = array();
                foreach ($numArr as $key => $val) {
                    $ret[] = array_slice($reviewDetailInfo, $num, $val);
                    $num += $val;
                }
                $page['reviewInfo'] = $reviewInfo;
                $page['reviewDetailInfo'] = $ret;
                $page['appSign'] = array("安卓客户端", "IOS客户端");
                $this->render('/showForumPreview', array('page' => $page));
            } else if ($page['info']['article_type'] == 3) {
                //查询相同的编码和图文类型
                $sql = "select * FROM material_article_template WHERE  article_code= '" . $page['info']['article_code'] . "'order by id";
                $data = Yii::app()->db->createCommand($sql)->queryAll();

                $this->render('/showWeChatPreview', array('page' => $page, 'data' => $data));

            }
    }

    /**
     * 获取微信ticket
     * author: yjh
     */
    public function actionGetTicket()
    {
        $type = $this->get('type');
        $type = intval($type) ? intval($type) : 1;
        switch ($type){
            case 1: //第三方接口，返回跳转链接
                $url = $this->get('url');
                $objson = json_decode(file_get_contents('http://wx.api-export.com/api/waptoweixin?key=0a632600723fa987635898eb240aa6de&f=json&url=' . $url));
                $status = $objson->status;
                if ($status != 'ok') {
                    $href_url = "weixin://";
                } else {
                    $href_url = $objson->ticket_url;
                }
                break;
            case 2: //京东接口，返回跳转链接
                $get_url = $this->get('url');
                $redis_key = md5($get_url);
                $cache_href_url = Yii::app()->redis->getValue($redis_key);
                if($cache_href_url){
                    $href_url = $cache_href_url;
                }else{
                    $get_url = ltrim($get_url, 'http://');
                    $api = 'https://wq.jd.com/mjgj/link/GetOpenLink?callback=getOpenLink&rurl=';
                    $urlstr = urlencode('http://dc2.jd.com/auto.php?service=transfer&type=pms&to=//'.urlencode($get_url).'&openlink=1');
                    //$urlstr = urlencode('http://wqs.jd.com/ad/jump.shtml?curl=https://'.$url);
                    $urlok = $api.$urlstr;
                    $jsstr = helper::httpsGet($urlok);
                    preg_match('#openlink":"(.*?)"#',$jsstr,$url);
                    $href_url = $url[1];
                    Yii::app()->redis->setValue($redis_key, $href_url, 300);
                }
                break;
            case 3: //直链写入跳转链接
                $href_url = $this->get('url');
                break;
            default :
                $href_url = "weixin://";
                break;
        }
        echo json_encode(array('type'=>$type, 'href_url'=>$href_url));
        exit;
    }

    public function actionNowIndex()
    {
        $page = array();
        
        $url = $_SERVER['HTTP_HOST'];
        $strArr = $this->check_url_rule();
        $pid = $strArr[0]; //推广id
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $visit_key = 'jump_ground_visit_queue';  //value 推广ID_类型（1白名单 2goto 3落地）_时间_IP_UA_当前URL
        $now_request_url = 'http://'.$url.$_SERVER['REQUEST_URI'];
        Yii::app()->redis->lpush($visit_key, $pid.'_:_3_:_'.time().'_:_'.helper::getip().'_:_'.$user_agent.'_:_'.$now_request_url);
        
        $domainInfo = DomainList::model()->find("domain='$url'");
        $nid = $this->get('nid');
        $id = $this->get('id');
        $TTL = Yii::app()->params['basic']['redis_time'];//缓存时长
        $promotion_id = $pid;
        if ($id > 10000) {
            $t = time();
            $start = mktime(0, 0, 0, date("m", $t), date("d", $t), date("Y", $t));
            $end = mktime(23, 59, 59, date("m", $t), date("d", $t), date("Y", $t));
            $expire_time = Yii::app()->params['basic']['expire_time'];
            //推广链接请求判断时间是否属于当天（防止修改链接可访问），判断时间是否超过失效时间
            if ($id < $start || $id > $end || $t > ($id + $expire_time)) {
                $url = Yii::app()->params['basic']['errorurl'];
                header('location:' . $url);
                exit;
            }
        }
        $redis_flag = Yii::app()->params['basic']['is_redis'];
        if ($redis_flag == 1) {
            $page = Yii::app()->redis->getValue('article:' . $promotion_id);
        }

        
        if (!$page) {
            $promotion = Promotion::model()->findByPk($promotion_id);
            if (!$promotion) {
                die('error 4004');
            }
            if ($promotion->status != 0) {
                $url = Yii::app()->params['basic']['errorurl'];
                header('location:' . $url);
                exit;
            }
            if ($promotion->is_pc_show == 1) {
                if (stristr($user_agent, 'window')) {
                    $url = $promotion->pc_url;
                    if (!$url) $url = Yii::app()->params['basic']['pc_url'];
                    header('Location:' . $url);
                    exit;
                }
            }
//        $template_id = $promotion->origin_template_id;
            $template = OnlineMaterialManage::model()->findByAttributes(array('promotion_id' => $promotion_id,'is_main_page'=>1));
            if (!$template) {
                die('error 4005');
            }

            $fiancePay = InfancePay::model()->findByPk($promotion->finance_pay_id);
            if (!$fiancePay) {
                die('error 4008');
            }
            $weixins = WeChat::model()->getWeixins($fiancePay->weixin_group_id);
            if (!$weixins) {
                die('error 4012');
            }
//            print_r($weixins);
            $weixinList = array();
            foreach ($weixins as $r) {
                $data = array();
                $data['id'] = $r['id'];
                $data['weixin_name'] = $r['wechat_id'];
                $data['customer_service_id'] = $r['customer_service_id'];
                $data['land_url'] = $r['land_url'];
                $data['tg_uid'] = $r['promotion_staff_id'];
                $rst = Resource::model()->findByPk($r['qrcode_id']);
                if ($rst) {
                    $data['weixin_img'] = $rst->resource_url;
                    $data['img_width'] = $rst->r_width;
                    $data['img_height'] = $rst->r_height;
                } else {
                    $data['weixin_img'] = '';
                    $data['img_width'] = '';
                    $data['img_height'] = '';
                }
                $weixinList[] = $data;
            }
            $page['weixinList'] = $weixinList;
            $page['info'] = Dtable::toArr($template);
            $page['url'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];

            $page['info']['independent_cnzz'] = $promotion->independent_cnzz;
            $page['info']['minus_proportion'] = $promotion->minus_proportion;
            if ($promotion->cnzz_code_id != 0) {
                $page['info']['total_cnzz'] = CnzzCodeManage::model()->findByPk($promotion->cnzz_code_id)->total_cnzz;
            } else {
                $page['info']['total_cnzz'] = $promotion->total_cnzz;
            }
            $testPids = Yii::app()->params['basic']['bpids'];
            $testPidArr = explode(',', $testPids);
            if (in_array($promotion_id, $testPidArr)) {
                $page['info']['article_block'] = $this->toArr(ArticleBlock::model()->find("1 order by rand()"));
            }
            if ($page['info']['article_type'] == 0) {
                $psq_flag = $page['info']['psq_id'] == 0 ? 0 : 1;
                if ($psq_flag == 1) {
                    $page['psqList'] = Questionnaire::model()->getPSQListByPk($page['info']['psq_id']);
                    $page['psqNum'] = count($page['psqList']);
                }
                if ($page['info']['is_order'] == 1 && $page['info']['order_id'] != 0) {
                    $payment = vars::$fields['payment'];
                    $page['info']['payments'] = $payment;
                    $page['info']['payment'] = helper::decbin_digit($page['info']['payment'], count($payment));
                    $page['info']['order'] = $this->toArr(OrderTemplete::model()->findByPk($page['info']['order_id']));
                    $page['info']['order']['packages'] = OrderTemplete::model()->getPackageInfo($page['info']['order_id']);
                    $page['info']['order']['packageNames'] = array_column($page['info']['order']['packages'], 'package_name');
                } else {
                    $page['info']['order']['is_suspend'] = 0;
                }
            } else if ($page['info']['article_type'] == 1) {
                $f_flag = $page['info']['first_audio'] == 0 ? 0 : 1;
                $s_flag = $page['info']['second_audio'] == 0 ? 0 : 1;
                $v_flag = $page['info']['third_audio'] == 0 ? 0 : 1;
                $psq_flag = $page['info']['psq_id'] == 0 ? 0 : 1;
                $r_flag = $page['info']['review_id'] == 0 ? 0 : 1;
                $page['r_type'] = MaterialReview::model()->findByPk($page['info']['review_id'])->review_type;

                if ($f_flag == 1) $page['first_audio'] = MaterialAudio::model()->getUrlByPk($page['info']['first_audio']);
                if ($s_flag == 1) $page['second_audio'] = MaterialAudio::model()->getUrlByPk($page['info']['second_audio']);
                if ($v_flag == 1) $page['third_audio'] = MaterialAudio::model()->getUrlByPk($page['info']['third_audio']);

                if ($psq_flag == 1) {
                    $page['psqList'] = Questionnaire::model()->getPSQListByPk($page['info']['psq_id']);
                    $page['psqNum'] = count($page['psqList']);
                }
                if ($r_flag == 1) {
                    $page['reviewDetailList'] = MaterialReviewDetail::model()->findAllByAttributes(array('review_id' => $page['info']['review_id']));
                    $page['reviewNum'] = count($page['reviewDetailList']);
                }
            } else if ($page['info']['article_type'] == 2) {
                $reviewInfo = Dtable::toArr(MaterialReview::model()->findByPk($page['info']['review_id']));
                $reviewDetailInfo = Dtable::toArr(MaterialReviewDetail::model()->findAll('review_id=' . $page['info']['review_id'] . " order by id asc"));
                $numArr = array_count_values(array_column($reviewDetailInfo, 'floor'));
                $num = 0;
                $ret = array();
                foreach ($numArr as $key => $val) {
                    $ret[] = array_slice($reviewDetailInfo, $num, $val);
                    $num += $val;
                }
                $page['reviewInfo'] = $reviewInfo;
                $page['reviewDetailInfo'] = $ret;
                $page['appSign'] = array("安卓客户端", "IOS客户端");
            } elseif ($page['info']['article_type'] == 3) {
                $page['info']['site_page'] = 1;
            }
            if ($redis_flag == 1) {
                Yii::app()->redis->setValue('article:' . $promotion_id, $page, $TTL);
            }
        }
        if ($domainInfo['is_public_domain'] == 1) {
            $appid = Yii::app()->params['weChat_config']['appID'];
            $secret = Yii::app()->params['weChat_config']['appsecret'];
            $access_token = Yii::app()->redis->getValue('access_token:' . $appid);
            if(!$access_token) {
                /* 不在有效期，重新发送请求，获取access_token */
                $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret;
                $ret = $page_data['result'] = helper::get_public_conetnts($url);
                if ($ret) {
                    $access_token_ret = $ret->access_token;
                    $access_token = $ret->access_token;
                    Yii::app()->redis->setValue('access_token:' . $appid, $access_token_ret, 7100);
                }
            }
            $ticket = Yii::app()->redis->getValue('jsapi_ticket:' . $appid);
            if(!$ticket) {
                $jsapi_ticket = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' . $access_token . '&type=jsapi';
                $get_ret = helper::get_public_conetnts($jsapi_ticket);
                if ($get_ret) {
                    $ticket = $get_ret->ticket;
                    Yii::app()->redis->setValue('jsapi_ticket:' . $appid, $ticket, 7100);
                }
            }

            $timestamp = time();
            $noncestr = $this->create_password(16);
            $tmp = array('noncestr' => $noncestr, 'jsapi_ticket' => $ticket, 'timestamp' => $timestamp, 'url' => $now_request_url);
            ksort($tmp);
            $string = '';
            foreach ($tmp as $key => $val) {
                $string .= $key . '=' . $val.'&';
            }
            $string2 = rtrim($string, '&');
            $signature = sha1($string2);
        }
        
        //cnzz扣量处理-----------start
        $minus_proportion = $page['info']['minus_proportion'];  //扣量比例
        if($minus_proportion > 0){  //有设置扣量比例
            $ip = helper::getip();  //获取IP
            $city_ids_redis_key = 'city_ids:'.$promotion_id;    //不扣量城市key
            $kl_ips_pool_key = 'kl_ip_pool:'.$promotion_id;       //扣掉IP池key
            $kl_ips_pool_string = Yii::app()->redis->getValue($kl_ips_pool_key);
            //判断IP是否已被扣量,已被扣量的IP直接不再加入
            $ip_str = $ip.';';
            if(strpos($kl_ips_pool_string, $ip_str) !== false){
                $page['info']['independent_cnzz'] = '';
            }else{
                //判断IP是否在不扣量地区
                $ip_city = $this->get_city();
                $linkage_id = $ip_city['linkage_id'];
                //不扣量地区
                $city_ids_string = Yii::app()->redis->getValue($city_ids_redis_key);
                if(!$city_ids_string){
                    $city_ids_string = PromotionDeductionAddressRel::model()->find('promotion_id='.$promotion_id)->province_city_ids;
                    Yii::app()->redis->setValue($city_ids_redis_key, $city_ids_string);
                }
                $city_ids = array();
                if($city_ids_string){
                    $city_ids =explode(',', $city_ids_string);
                }
                //该ip不在扣量比例里面
                if(!in_array($linkage_id,  $city_ids)){
                    $visitor_key = 'visitor:'.$promotion_id;    //数量key
                    $ips_pool_key = 'ip_pool:'.$promotion_id;       //IP池key
                    $visitor_num_redis_time = strtotime(date("Y-m-d 23:59:59") - time());   //次数缓存时间当天有效
                    $ips_pool_string = Yii::app()->redis->getValue($ips_pool_key);
                    //判断IP是否在IP池中
                    if(strpos($ips_pool_string, $ip_str) === false){
                        //不在池中，存入IP池
                        $ips_pool_string .= $ip.';';
                        Yii::app()->redis->setValue($ips_pool_key, $ips_pool_string, $visitor_num_redis_time);
                        //记录次数
                        $last_visitor_num = Yii::app()->redis->getValue($visitor_key);
                        $now_visitor_num = $last_visitor_num ? $last_visitor_num + 1 : 1;   //访问数量
                        Yii::app()->redis->setValue($visitor_key, $now_visitor_num, $visitor_num_redis_time);
                        //假设比例为10 则第11个扣量
                        $is_cnzz = $now_visitor_num - $minus_proportion;   
                        if($is_cnzz == 1){
                            $kl_ips_pool_string .= $ip.';';
                            Yii::app()->redis->setValue($visitor_key, 0, $visitor_num_redis_time);
                            Yii::app()->redis->setValue($kl_ips_pool_key, $kl_ips_pool_string, $visitor_num_redis_time);
                            $page['info']['independent_cnzz'] = '';
                        }
                    }
                }
            }
        }
        //cnzz扣量处理-----------end


        
        if ($page['info']['is_vote'] == 1 && $page['info']['vote_id'] != 0 && $nid == '') {
            $data = array();
            if ($redis_flag == 1) $data = Yii::app()->redis->getValue('psq:' . $promotion_id);
            if (!$data) {
                $data = $this->toArr(Questionnaire::model()->findByPk($page['info']['vote_id']));
                $data['psq'] = $this->toArr(Quest::model()->findAll('qus_id =' . $page['info']['vote_id']));
                $data['psq_count'] = count($data['psq']);
                if ($redis_flag == 1) Yii::app()->redis->setValue('psq:' . $promotion_id, $data, $TTL);
            }
            $this->render('/votepage', array('page' => $data));
            exit;
        }
        
        $page['info']['css_cdn_url'] = '';  //css、js使用
        $page['info']['cdn_url'] = '';  //image使用
        $noCdnArr = array();
        $nocdnids = trim(Yii::app()->params['basic']['nocdnids']);
        if(!empty($nocdnids)){
            $noCdnArr = explode(',', $nocdnids);
        }
        if (!in_array($promotion_id, $noCdnArr)) {
            if ($domainInfo->is_https == 1) {
                if(Yii::app()->params['basic']['cdn_url']){
                    $page['info']['css_cdn_url'] = "https://" . Yii::app()->params['basic']['cdn_url'];//CDN域名
                }
                if((Yii::app()->params['upload_server']['imgUrl'])){
                    $page['info']['cdn_url'] = "https://" . Yii::app()->params['upload_server']['imgUrl'];//CDN域名
                }
            }else{
                if(Yii::app()->params['basic']['cdn_url']){
                    $page['info']['css_cdn_url'] = "http://" . Yii::app()->params['basic']['cdn_url'];//CDN域名
                }
                if((Yii::app()->params['upload_server']['imgUrl'])){
                    $page['info']['cdn_url'] = "http://" . Yii::app()->params['upload_server']['imgUrl'];//CDN域名
                }
            }
        }
        
        $lazyids = Yii::app()->params['basic']['lazyids'];
        $noLazyArr = explode(',', $lazyids);
        if ($noLazyArr && in_array($promotion_id, $noLazyArr)) {
            $page['info']['is_lazy'] = 1;
        } else {
            $page['info']['is_lazy'] = 0;
        }

        $page['info']['link'] = $now_request_url;
        $page['info']['nonceStr'] = $noncestr;
        $page['info']['timestamp'] = $timestamp;
        $page['info']['signature'] = $signature;
        
        $weixin_info= $this->setCookieData($page['weixinList']);
        $page['info']['content'] = $this->replaceContent($page, $weixin_info);
        
        if ($page['info']['article_type'] == 0) {
            $this->render('/newindex', array('page' => $page, 'weixin_info'=> $weixin_info));
        } else if ($page['info']['article_type'] == 1) {
            $this->render('/newvoice', array('page' => $page, 'weixin_info'=> $weixin_info));
        } else if ($page['info']['article_type'] == 2) {
            $this->render('/forum', array('page' => $page));
        } else if ($page['info']['article_type'] == 3) {
            //查询相同的编码和图文类型
            $sql = "select * FROM online_material_manage WHERE `promotion_id`=" . $promotion_id . " AND article_code= '" . $page['info']['article_code'] . "' order by id";
            $data = Yii::app()->db->createCommand($sql)->queryAll();
            $num = $this->get('data_num') ? $this->get('data_num') : 0;
            $page_data = $data[$num];

            $tmparr = parse_url($page['url']);
            $rstr = empty($tmparr['scheme']) ? 'http://' : $tmparr['scheme'] . '://';
            $rstr .= $tmparr['host'] . $tmparr['path'];
            $page_data['url'] = $rstr;

            $page_data['link'] = $now_request_url;
            $page_data['nonceStr'] = $noncestr;
            $page_data['timestamp'] = $timestamp;
            $page_data['signature'] = $signature;

              $this->render('/weChat', array('idata' => $page_data, 'page' => $page, 'data' => $data));
            }
    }
    
    /**
     * 设置cookie微信号信息，同个用户5分钟内打开保持同个客服微信信息
     */
    public function setCookieData($params)
    {
        $cookie_key = "coodata";
        $expires_time = 200;
        $cookie_data = $_COOKIE[$cookie_key];
        if(!$cookie_data){
            $weixinList = $params;
            if(count($weixinList) < 1){
                return false;
            }
            $cookie_data_array = $weixinList[array_rand($weixinList, 1)];
            $cookie_data = json_encode($cookie_data_array);
            setcookie($cookie_key, $json_cookie_data, $expires_time);
        }
        $return_cookie_data = json_decode($cookie_data, true);
        $return_cookie_data['weixin_img'] = $page['info']['cdn_url'].$return_cookie_data['weixin_img'];
        return $return_cookie_data;
    }
    
    /**
     * 替换内容微信号、图片链接等
     */
    public function replaceContent($page, $weixin_info)
    {
        $mycontent = $page['info']['content'];
        $xingxiang = $page['info']['xingxiang'];
        $mycontent = str_replace("{{weixin}}", '<span class="wx_name">'.$weixin_info['weixin_name'].'</span>', $mycontent);
        $mycontent = str_replace("{{xingxiang}}", $xingxiang, $mycontent);
        $mycontent = str_replace("{{weixin_img}}", '<img class="qrcode_img" src="'.$weixin_info['weixin_img'].'" />', $mycontent);
        
        //获取图片
        preg_match_all("/<img(.*?)>/", $mycontent, $result);
        $img_array = $result[0];
        if(count($img_array)){
            foreach($img_array as $key=>$val){
                $link_result = array();
                if(strpos($val, "lazy") !== false){
                    preg_match_all("/data-original=\"(.*?)\"/", $val, $link_result);
                    if($page['info']['is_lazy'] == 1){
                        if((strpos($link_result[1][0], "http://") === false)){
                            $find_str = 'data-original="'.$link_result[1][0].'"';
                            $new_link = 'data-original="'.$page['info']['cdn_url'].$link_result[1][0].'"';
                            $mycontent = str_replace($find_str, $new_link, $mycontent);
                        }
                    }else{
                        if((strpos($link_result[1][0], "http://") !== false)){
                            $new_link = $link_result[1][0];
                            $find_str = 'data-original="'.$link_result[1][0].'"';
                            $replace_str = $find_str.' src="'.$new_link.'"';
                            $mycontent = str_replace($find_str, $replace_str, $mycontent);
                        }else{
                            $new_link = $page['info']['cdn_url'].$link_result[1][0];
                            $find_str = 'data-original="'.$link_result[1][0].'"';
                            $replace_str = $find_str.' src="'.$new_link.'"';
                            $mycontent = str_replace($find_str, $replace_str, $mycontent);
                        }
                    }
                }elseif(strpos($val, "http://") === false){
                    preg_match_all("/src=\"(.*?)\"/", $val, $link_result);
                    $find_str = 'src="'.$link_result[1][0].'"';
                    $new_link = 'src="'.$page['info']['cdn_url'].$link_result[1][0].'"';
                    $mycontent = str_replace($find_str, $new_link, $mycontent);
                }
            }
        }
        
        if($page['third_audio']['url']){
            if(strpos($page['third_audio']['url'], "http://") === false){
                $page['third_audio']['url'] = $page['info']['cdn_url'].$page['third_audio']['url'];
            }
        }
        
        return $mycontent;
    }
}