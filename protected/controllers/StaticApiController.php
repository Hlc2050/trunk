<?php
/**
 * 静态系统调用接口文件
 * author lxj 2018-08-02
 */

class StaticApiController extends HomeController
{
    public $config = array(
        //随机串，用于token验证
        'token_str' => '4fBn8e1akM',
    );

    /**
     * 获取静态类型推广
     */
    public function actionGetPromotionInfo()
    {
        //验证token
        if (!$this->checkToken()) {
            $ret = array('ret' => 4001, 'msg' => 'token验证失败！');
            die(json_encode($ret));
        }
        if (!$_GET['p']) {
            $_GET['p'] = 1;
        }
        //查询数据
        $params['where'] = ' and line_type=1';
        //$params['where'] .= " and(a.status!=2) ";
        //渠道查询
        if ($_GET['promotion_id']) {
            $params['where'] .= " and(a.id=" . $_GET['promotion_id'] . ") ";
        }
        if ($_GET['channel_name']) {
            $params['where'] .= " and(c.channel_name like '%" . $_GET['channel_name'] . "%') ";
        }
        $params['order'] = "  order by a.id desc    ";
        $params['pagesize'] = $_GET['size'] ? $_GET['size'] : 20;
        $params['join'] = "
		left join finance_pay as f on f.id=a.finance_pay_id
		left join channel as c on c.id=f.channel_id
		left join cservice as b on b.csno=f.sno
		";
        $params['pagebar'] = 1;
        $params['select'] = "a.finance_pay_id,a.id,a.domain_id,a.line_type,a.url_rule,a.goto_domain_id,a.status,a.promotion_type,c.channel_name,c.channel_code,f.sno as tg_uid,f.channel_id,b.csname_true";
        $params['smart_order'] = 1;
        $page = Dtable::model(Promotion::model()->tableName())->listdata($params);
        if (!empty($page['list'])) {
            $pro_ids = array_column($page['list'], 'id');
            $pro_domains = PromotionDomain::model()->getPromotionsDomains($pro_ids);
            foreach ($page['list'] as $key=>$value) {
                $page['list'][$key]['domain_list'] =$pro_domains[$value['id']] ? $pro_domains[$value['id']]:array();
                $page['list'][$key]['goto_url'] = helper::build_goto_link($value,$value['is_white_domain']);
            }
        }
        $ret = array('ret' => 200, 'data' => $page);
        die(json_encode($ret));
    }

    /**
     * 验证token
     * @return bool
     */
    private function checkToken()
    {
        $account = $_GET['account'];
        $token = $_GET['token'];
        if (!$account || !$token) {
            return false;
        }
        $time = intval(time() / 60 / 60) * 60 * 60;
        $check_token = md5(str_pad(strrev($account), 30, $this->config['token_str'], STR_PAD_BOTH) . $time);
        if ($token == $check_token) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 获取推广页面信息
     */
    public function actionGetPromotion()
    {
        $info = $_GET['info'];
        if (!$info) {
            $ret = array('ret' => 4001, 'msg' => '缺少参数！');
            die(json_encode($ret));
        }
        $pid = intval($_GET['pid']);
        $domain = trim($_GET['domain']);
        $domain_info = DomainList::model()->find("domain='".$domain."'");
        if (!$pid && !$domain) {
            $ret = array('ret' => 4001, 'msg' => '缺少推广必要参数！');
            die(json_encode($ret));
        }
        if (!$pid && $domain) {
            if (!$domain_info) {
                $ret = array('ret' => 4001, 'msg' => '域名不存在！');
                die(json_encode($ret));
            }
            $promotion = PromotionDomain::model()->getPromotionByDomain($domain_info['id'],' (p.promotion_type=3 || p.promotion_type=2) ');
            $promotion = $promotion[0];
            if (!$promotion) {
                $ret = array('ret' => 4001, 'msg' => '推广不存在！');
                die(json_encode($ret));
            }
        }
        if ($pid) {
            $promotion = Dtable::toArr(Promotion::model()->findByPk($pid));
        }
        if (!$promotion || $promotion['line_type'] != 1) {
            $ret = array('ret' => 4001, 'msg' => '该静态推广不存在！');
            die(json_encode($ret));
        }
        $is_pc_show = 0;
        $pc_url = '';

        if ($promotion['is_pc_show'] == 1) {
            $url = $promotion['pc_url'];
            if (!$url) $url = Yii::app()->params['basic']['pc_url'];
            $is_pc_show = 1;
            $pc_url = $url;
        }
        $page['info']['is_pc_show'] = $is_pc_show;
        $page['info']['pc_url'] = $pc_url;

        if ($info == 'domain') {
            $pro_data = array();
            $pro_data['is_pc_show'] = $is_pc_show;
            $pro_data['pc_url'] = $pc_url;
            $pro_data['promotion_type'] = $promotion['promotion_type'];
            $pro_domains = PromotionDomain::model()->getPromotionsDomains($pid);
            $pro_data['domain_list'] = $pro_domains[$pid];
            $ret = array('ret' => 200, 'data' => $pro_data);
            die(json_encode($ret));
        }
        if ($info == 'promotion') {
            // 获取微信号
            $fiancePay = InfancePay::model()->findByPk($promotion['finance_pay_id']);
            if (!$fiancePay) {
                $ret = array('ret' => 4008, 'msg' => '打款不存在！');
                die(json_encode($ret));
            }
            $weixins = WeChat::model()->getWeixins($fiancePay->weixin_group_id);
            if (!$weixins) {
                $ret = array('ret' => 4008, 'msg' => '微信小组不存在！');
                die(json_encode($ret));
            }
//            print_r($weixins);
            $rand_id= array_rand($weixins);
            //微信号信息
            $wexin = $weixins[$rand_id];
            $rst = Resource::model()->findByPk($wexin['qrcode_id']);
            if ($rst) {
                $wexin['weixin_img'] = $rst->resource_url;
                $wexin['img_width'] = $rst->r_width;
                $wexin['img_height'] = $rst->r_height;
            } else {
                $wexin['weixin_img'] = '';
                $wexin['img_width'] = '';
                $wexin['img_height'] = '';
            }
            $page['info']['wexin'] = $wexin;
            //修改为按域名总统计组别获取总统计代码 lxj 2018-12-29
            if ($domain_info->cnzz_code_id != 0) {
                $page['info']['total_cnzz'] = CnzzCodeManage::model()->findByPk($domain_info->cnzz_code_id)->total_cnzz;
            } else {
                $page['info']['total_cnzz'] = $promotion['total_cnzz'];
            }
            $page['info']['independent_cnzz'] = $promotion['independent_cnzz'];
            //cnzz扣量处理-----------start
            $minus_proportion = $promotion['minus_proportion'];  //扣量比例
            if($minus_proportion > 0){  //有设置扣量比例
                $ip = helper::getip();  //获取IP
                $city_ids_redis_key = 'city_ids:'.$pid;    //不扣量城市key
                $kl_ips_pool_key = 'kl_ip_pool:'.$pid;       //扣掉IP池key
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
                        $city_ids_string = PromotionDeductionAddressRel::model()->find('promotion_id='.$pid)->province_city_ids;
                        Yii::app()->redis->setValue($city_ids_redis_key, $city_ids_string);
                    }
                    $city_ids = array();
                    if($city_ids_string){
                        $city_ids =explode(',', $city_ids_string);
                    }
                    //该ip不在扣量比例里面
                    if(!in_array($linkage_id,  $city_ids)){
                        $visitor_key = 'visitor:'.$pid;    //数量key
                        $ips_pool_key = 'ip_pool:'.$pid;       //IP池key
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
            
            $page['info']['css_cdn_url'] = '';  //css、js使用
            $page['info']['cdn_url'] = '';  //image使用
            if ($domain_info->is_https == 1) {
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
            $page['info']['promotion'] = $promotion;
            $ret = array('ret' => 200, 'data' => $page);
            die(json_encode($ret));
        }

    }



}