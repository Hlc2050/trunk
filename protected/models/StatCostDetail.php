<?php

class StatCostDetail extends CActiveRecord
{
    public function tableName()
    {
        return '{{stat_cost_detail}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    /*
     * 非订阅创建成本明细
     * @param $promotion_id 推广id
     * @param $datetime  日期
     *
     */
    public function create($promotion_id, $datetime)
    {
        $promotion = Promotion::model()->findByPk($promotion_id);
        if (!$promotion) {
            $ret = array('state' => -1, 'msgwords' => '推广不存在');
            return $ret;
        }

        //查询推广在用域名
        $sdomain = PromotionDomain::model()->getPromotionsDomains($promotion_id);
        $sdomain = $sdomain[$promotion_id];
        if (!$sdomain) {
            $ret = array('state' => -2, 'msgwords' => '推广域名不存在');
            return $ret;
        }
        $sql = "select domain from domain_promotion_change where promotion_id=$promotion_id ";
        $domains = Yii::app()->db->createCommand($sql)->queryAll();
        $mydomains = array();
        foreach ($domains as $r) {
            $mydomains[] = $r['domain'];
        }
        foreach ($sdomain as $sd) {
            $mydomains[] = $sd['domain'];
        }

        $mydomains = array_unique($mydomains);

        $ip = 0;
        $uv = 0;
        $pv = 0;
        $ip_piwik = 0;
        $uv_piwik = 0;
        $pv_piwik = 0;
        $statCnzzFlow = Dtable::toArr(StatCnzzFlow::model()->findByAttributes(array( 'stat_date' => $datetime, 'promotion_id' => $promotion_id)));
        $domain_cnzz = explode(',',$statCnzzFlow['domain']);
        $statPiwikFlow = Dtable::toArr(StatPiwikFlow::model()->findAll('stat_date='.$datetime));
        $domain_piwik = array();
        foreach ($statPiwikFlow as $value){
            $domain_piwik[$value['domain']] = $value;
        }
        foreach ($mydomains as $domain) {
            if (!in_array($domain,$domain_cnzz)) {
                continue;
            }else {
                $ip = $statCnzzFlow['ip'];
                $uv = $statCnzzFlow['uv'];
                $pv = $statCnzzFlow['pv'];
            }
            if (!array_key_exists($domain,$domain_piwik)) continue;
            $ip_piwik += $domain_piwik[$domain]['ip'];
            $uv_piwik += $domain_piwik[$domain]['uv'];
            $pv_piwik += $domain_piwik[$domain]['pv'];

        }
        $financePay = InfancePay::model()->findByPk($promotion->finance_pay_id);
        if (!$financePay) {
            $ret = array('state' => -3, 'msgwords' => '该推广打款不存在');
            return $ret;
        }
        if ($financePay->business_type == 1) {
            $ret = array('state' => -3, 'msgwords' => '该推广打款是订阅号的');
            return $ret;
        }
        $cost = 0;  //总的成本
        if ($financePay->charging_type == 0) {
            $cost = $pv * $financePay->unit_price;
        } else if ($financePay->charging_type == 1) {
            $cost = $uv * $financePay->unit_price;
        } else if ($financePay->charging_type == 2) {
            $cost = $ip * $financePay->unit_price;
        }
        $cost_piwik = 0;  //总的成本
        if ($financePay->charging_type == 0) {
            $cost_piwik = $pv * $financePay->unit_price;
        } else if ($financePay->charging_type == 1) {
            $cost_piwik = $uv * $financePay->unit_price;
        } else if ($financePay->charging_type == 2) {
            $cost_piwik = $ip * $financePay->unit_price;
        }

        $weixin_group_id = $financePay->weixin_group_id;
        $sql = "select b.*,a.wid as weixin_id from wechat_relation as a
						left join  wechat as b on b.id=a.wid  where wechat_group_id= $weixin_group_id";
        $a = Yii::app()->db->createCommand($sql)->queryAll();
        $i = 0;
        foreach ($a as $r) {
            $costDetail = StatCostDetail::model()->findByAttributes(array('stat_date' => $datetime, 'weixin_id' => $r['id'], 'promotion_id' => $promotion_id));
            if ($costDetail) {
                $costDetail->money = $cost / count($a);
                $costDetail->uv = $uv / count($a);
                $costDetail->save();
                continue;
            }
            $i++;
            $costDetail = new StatCostDetail();
            $costDetail->weixin_id = $r['id'];
            $costDetail->money = $cost / count($a);
            $costDetail->uv = $uv / count($a);
            $costDetail->ip = $ip / count($a);
            $costDetail->third_money = $cost_piwik / count($a);
            $costDetail->stat_date = $datetime;
            $costDetail->pay_date = $financePay->pay_date;
            $costDetail->partner_id = $financePay->partner_id;
            $costDetail->channel_id = $financePay->channel_id;
            $costDetail->tg_uid = $r['promotion_staff_id'];
            $costDetail->goods_id = $r['goods_id'];
            $costDetail->customer_service_id = $r['customer_service_id'];
            $costDetail->promotion_id = $promotion_id;
            $costDetail->business_type = $financePay->business_type;
            $costDetail->charging_type = $financePay->charging_type;
            $costDetail->create_time = time();
            $costDetail->update_time = time();
            $costDetail->save();
        }
        $ret = array('state' => 1, 'msgwords' => '创建了' . $i . '条成本明细');
        return $ret;

    }

    /**
     * 生成免域域名的成本明细
     * @param $promotion_id
     * @param $datetime
     * author: yjh
     */
    public function nonDomainCreate($promotion_id, $datetime)
    {
        $promotion = Promotion::model()->findByPk($promotion_id);
        if (!$promotion) {
            $ret = array('state' => -1, 'msgwords' => '推广不存在');
            return $ret;
        }
        $financePay = InfancePay::model()->findByPk($promotion->finance_pay_id);
        if (!$financePay) {
            $ret = array('state' => -3, 'msgwords' => '该推广打款不存在');
            return $ret;
        }
        if ($financePay->business_type == 1) {
            $ret = array('state' => -3, 'msgwords' => '该推广打款是订阅号的');
            return $ret;
        }


        $weixin_group_id = $financePay->weixin_group_id;
        $sql = "select b.*,a.wid as weixin_id from wechat_relation as a
						left join  wechat as b on b.id=a.wid  where wechat_group_id= $weixin_group_id";
        $a = Yii::app()->db->createCommand($sql)->queryAll();
        $i = 0;
        foreach ($a as $r) {
            $i++;
            $costDetail = new StatCostDetail();
            $costDetail->weixin_id = $r['id'];
            $costDetail->money = 0;
            $costDetail->uv = 0;
            $costDetail->third_money = 0;
            $costDetail->stat_date = $datetime;
            $costDetail->pay_date = $financePay->pay_date;
            $costDetail->partner_id = $financePay->partner_id;
            $costDetail->channel_id = $financePay->channel_id;
            $costDetail->tg_uid = $r['promotion_staff_id'];
            $costDetail->goods_id = $r['goods_id'];
            $costDetail->customer_service_id = $r['customer_service_id'];
            $costDetail->promotion_id = $promotion_id;
            $costDetail->business_type = $r['business_type'];
            $costDetail->charging_type = $r['charging_type'];
            $costDetail->create_time = time();
            $costDetail->update_time = time();
            $costDetail->save();
        }
        $ret = array('state' => 1, 'msgwords' => '创建了' . $i . '条成本明细');
        return $ret;
    }


    /*
    * 特殊非订阅号创建成本明细
    * @param $fpay_id  打款ID
    */

    public function createSpcl($fpay_id, $ip, $uv, $pv, $datetime)
    {
        $financePay = InfancePay::model()->findByPk($fpay_id);
        if (!$financePay) {
            $ret = array('state' => -1, 'msgwords' => '打款不存在');
            return $ret;
        }
        $weixin_group_id = $financePay->weixin_group_id;
        $sql = "select b.*,a.wid as weixin_id from wechat_relation as a
						left join  wechat as b on b.id=a.wid  where wechat_group_id= $weixin_group_id";
        $a = Yii::app()->db->createCommand($sql)->queryAll();

        $cost = 0;  //成本
        if ($financePay->charging_type == 0) {
            $cost = $pv * $financePay->unit_price;
        } else if ($financePay->charging_type == 1) {
            $cost = $uv * $financePay->unit_price;
        } else if ($financePay->charging_type == 2) {
            $cost = $ip * $financePay->unit_price;
        }
        $i = 0;
        foreach ($a as $r) {
            $wechat = WeChat::model()->findByPk($r['id']);
            $costDetail = new StatCostDetail();
            $costDetail->weixin_id = $r['id'];
            $costDetail->money = $cost / count($a);
            //$costDetail->third_money = $financePay->pay_money / count($a);
            $costDetail->stat_date = $datetime;
            $costDetail->pay_date = $financePay->pay_date;
            $costDetail->partner_id = $financePay->partner_id;
            $costDetail->channel_id = $financePay->channel_id;
            $costDetail->tg_uid = $wechat->promotion_staff_id;
            $costDetail->goods_id = $wechat->goods_id;
            $costDetail->customer_service_id = $wechat->customer_service_id;
            $costDetail->business_type = $financePay->business_type;
            $costDetail->charging_type = $financePay->charging_type;
            $costDetail->create_time = time();
            $costDetail->update_time = time();
            $costDetail->save();
            $i++;
        }
        $ret = array('state' => 1, 'msgwords' => '创建了' . $i . '条成本明细');
        return $ret;

    }


    /*
    * 订阅号创建成本明细
    * @param $weixin_group_id 微信号小组id
    * @param $fpay_id  打款ID
    */
    public function createBusiness($weixin_group_id, $fpay_id)
    {
        $financePay = InfancePay::model()->findByPk($fpay_id);
        $sql = "select b.*,a.wid as weixin_id from wechat_relation as a
						left join  wechat as b on b.id=a.wid  where wechat_group_id= $weixin_group_id";
        $a = Yii::app()->db->createCommand($sql)->queryAll();
        foreach ($a as $r) {

            $wechat = WeChat::model()->findByPk($r['id']);
            $costDetail = new StatCostDetail();
            $costDetail->weixin_id = $r['id'];
            $costDetail->money = $financePay->pay_money / count($a);
            $costDetail->third_money = $financePay->pay_money / count($a);
            $costDetail->stat_date = $financePay->online_date;
            $costDetail->pay_date = $financePay->pay_date;
            $costDetail->partner_id = $financePay->partner_id;
            $costDetail->channel_id = $financePay->channel_id;
            $costDetail->tg_uid = $wechat->promotion_staff_id;
            $costDetail->goods_id = $wechat->goods_id;
            $costDetail->customer_service_id = $wechat->customer_service_id;
            $costDetail->business_type = $financePay->business_type;
            $costDetail->charging_type = $financePay->charging_type;
            $costDetail->create_time = time();
            $costDetail->update_time = time();
            $costDetail->save();
        }
    }

    /**
     * 获取运营表数据
     * @param string $start_date
     * @param string $end_date
     * @param string $csid
     * @param string $goodsid
     * @param string $pgid
     * @param string $tgid
     * @return array
     * author: yjh
     */
    public function getOperateTableData($params = array())
    {
        //数据集合
        $data = array();
        $date = helper::get_right_date($params['start_date'], $params['end_date']);
        $data['first_day'] = date("Y-m-d", $date['first_day']);
        $data['last_day'] = date("Y-m-d", $date['last_day']);
        //搜索条件
        $orderParams = '';
        //客服部
        if ($params['csid']) {
            $orderParams .= " and customer_service_id=" . $params['csid'];
        }
        //商品
        if ($params['goodsid'] != 0) {
            $orderParams .= " and goods_id=" . $params['goodsid'];
        }
        //推广人员
        if ($params['tgid'] != 0) {
            $orderParams .= " and tg_uid=" . $params['tgid'];
        } elseif ($params['pgid'] != 0) {
            $promotionStaffArr = PromotionStaff::model()->getPromotionStaffByPg($params['pgid']);
            if (!$promotionStaffArr) $orderParams .= " and tg_uid=0";
            else {
                $str = implode(',', array_column($promotionStaffArr, 'user_id', 'user_id'));
                $orderParams .= " and tg_uid in (" . $str . ")";
            }
        }
        $orderParams .= " and business_type!=" .Yii::app()->params['basic']['dx_bid'];

        //业务类型
        if ($params['bsid'] != 0) {
            $orderParams .= " and business_type=" . $params['bsid'];
        }

        //计费方式
        if ($params['chg_id'] !== null && $params['chg_id'] !== '') {
            $orderParams .= " and charging_type=" . $params['chg_id'];
        }
        $key = 0;
        //按日期循环
        for ($temp_date = $date['first_day']; $temp_date <= $date['last_day']; $temp_date = $temp_date + 86400) {
            //进粉量
            $condition = "addfan_date = " . $temp_date . $orderParams;
            $sql = 'SELECT SUM(addfan_count) as fans_count FROM fans_input_manage WHERE ' . $condition;
            $fansInfo = Yii::app()->db->createCommand($sql)->queryAll();
            $fans_count = $fansInfo[0]['fans_count'] ? intval($fansInfo[0]['fans_count']) : 0;
            //预估发货量 预估发货金额（独立客服部和普通客服部）
            $order_count = $order_money = $estimate_count = $estimate_money = 0;
            $condition = "order_date = " . $temp_date . $orderParams . " group by customer_service_id";
            $sql = 'SELECT customer_service_id,COUNT(*) as order_count,SUM(order_money) AS order_money FROM place_norm_order_manage WHERE ' . $condition;
            $orderInfo = Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($orderInfo as $value) {
                $customerServiceInfo = CustomerServiceManage::model()->findByPk($value['customer_service_id']);
                $order_count += $value['order_count'];
                $order_money += $value['order_money'];
                $estimate_count += $value['order_count'] * 0.01 * $customerServiceInfo->estimate_rate;
                $estimate_money += $value['order_money'] * 0.01 * $customerServiceInfo->estimate_rate;
            }
            $condition = "order_date = " . $temp_date . $orderParams . " group by customer_service_id";
            $sql = 'SELECT customer_service_id,SUM(order_count) as order_count,SUM(order_money) AS order_money FROM place_indep_order_manage WHERE ' . $condition;
            $orderInfo = Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($orderInfo as $value) {
                $customerServiceInfo = CustomerServiceManage::model()->findByPk($value['customer_service_id']);
                $order_count += $value['order_count'];
                $order_money += $value['order_money'];
                $estimate_count += $value['order_count'] * 0.01 * $customerServiceInfo->estimate_rate;
                $estimate_money += $value['order_money'] * 0.01 * $customerServiceInfo->estimate_rate;
            }
            //投入金额(成本明细+修正成本)
            $condition = "stat_date=" . $temp_date . $orderParams;
            $sql = 'SELECT SUM(money) AS money FROM stat_cost_detail WHERE ' . $condition;
            $moneyInfo = Yii::app()->db->createCommand($sql)->queryAll();
            $sql = 'SELECT SUM(fixed_cost) AS fixed_cost FROM fixed_cost_new WHERE ' . $condition;
            $fixedCostInfo = Yii::app()->db->createCommand($sql)->queryAll();
            $money = $moneyInfo[0]['money'] + $fixedCostInfo[0]['fixed_cost'];
            //ROI
            $ROI = $money == 0 ? 0 : round($estimate_money * 100 / $money); //ROI
            //订单转化率
            $order_cor = $fans_count == 0 ? 0 : round($estimate_count * 100 / $fans_count, 1);
            //客单价
            $unit = $estimate_count == 0 ? 0 : round($estimate_money / $estimate_count);
            //进粉成本

            $fans_cost = $fans_count == 0 ? 0 : round($money / $fans_count);
            //均粉产出
            $fans_avg = $fans_count == 0 ? 0 : round($estimate_money / $fans_count);

            $data['info'][$key]['stat_date'] = date('Y-m-d', $temp_date);
            $data['info'][$key]['fans_count'] = $fans_count;
            $data['info'][$key]['estimate_count'] = $estimate_count;
            $data['info'][$key]['estimate_money'] = $estimate_money;
            $data['info'][$key]['money'] = $money;
            $data['info'][$key]['ROI'] = $ROI;
            $data['info'][$key]['order_cor'] = $order_cor;
            $data['info'][$key]['unit'] = $unit;
            $data['info'][$key]['fans_cost'] = $fans_cost;
            $data['info'][$key]['fans_avg'] = $fans_avg;
            $data['info'][$key]['order_count'] = $order_count;
            $data['info'][$key]['order_money'] = $order_money;
            $key++;
        }

        return $data;
    }

    /**
     * 业绩表数据
     * @param array $params
     * @return array
     * author: yjh
     */
    public function getPerfTableData($params = array())
    {
        //数据集合
        $data = array();
        //可跨月查询
        $date = helper::get_right_date($params['start_date'], $params['end_date']);
        $data['first_day'] = date("Y-m-d", $date['first_day']);
        $data['last_day'] = date("Y-m-d", $date['last_day']);
        //搜索条件
        $orderParams = '';
        //客服部
        if ($params['csid']) {
            $orderParams .= " and customer_service_id=" . $params['csid'];
        }
        //商品
        if ($params['goodsid'] != 0) {
            $orderParams .= " and goods_id=" . $params['goodsid'];
        }
        //推广人员
        if ($params['tgid'] != 0) {
            $orderParams .= " and tg_uid=" . $params['tgid'];
        } elseif ($params['pgid'] != 0) {
            $promotionStaffArr = PromotionStaff::model()->getPromotionStaffByPg($params['pgid']);
            if (!$promotionStaffArr) $orderParams .= " and tg_uid=0";
            else {
                $str = implode(',', array_column($promotionStaffArr, 'user_id', 'user_id'));
                $orderParams .= " and tg_uid in (" . $str . ")";
            }
        }
        $orderParams .= " and business_type!=" .Yii::app()->params['basic']['dx_bid'];

        //业务类型
        if ($params['bsid'] != 0) {
            $orderParams .= " and business_type=" . $params['bsid'];
        }
        //计费方式
        if ($params['chg_id'] !== null && $params['chg_id'] !== '') {
            $orderParams .= " and charging_type=" . $params['chg_id'];
        }
        $key = 0;
        //按日期循环
        for ($temp_date = $date['first_day']; $temp_date <= $date['last_day']; $temp_date = $temp_date + 86400) {
            //进粉量
            $condition = "addfan_date = " . $temp_date . $orderParams;
            $sql = 'SELECT SUM(addfan_count) as fans_count FROM fans_input_manage WHERE ' . $condition;
            $fansInfo = Yii::app()->db->createCommand($sql)->queryAll();
            $fans_count = $fansInfo[0]['fans_count'] ? intval($fansInfo[0]['fans_count']) : 0;
            //预估发货量 预估发货金额（独立客服部和普通客服部）
            $order_count = $order_money = $estimate_count = $estimate_money = 0;
            $condition = "delivery_status = 1 and delivery_date = " . $temp_date . $orderParams . " group by customer_service_id";
            $sql = 'SELECT customer_service_id,COUNT(*) as order_count,SUM(delivery_money) AS order_money FROM delivery_norm_order_manage WHERE ' . $condition;
            $orderInfo = Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($orderInfo as $value) {
                $order_count += $value['order_count'];
                $order_money += $value['order_money'];
            }
            $condition = "delivery_date = " . $temp_date . $orderParams . " group by customer_service_id";
            $sql = 'SELECT customer_service_id,SUM(delivery_count) as order_count,SUM(delivery_money) AS order_money FROM delivery_indep_order_manage WHERE ' . $condition;
            $orderInfo = Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($orderInfo as $value) {
                $order_count += $value['order_count'];
                $order_money += $value['order_money'];
            }
            //投入金额(成本明细+修正成本)
            $condition = "stat_date=" . $temp_date . $orderParams;
            $sql = 'SELECT SUM(money) AS money FROM stat_cost_detail WHERE ' . $condition;
            $moneyInfo = Yii::app()->db->createCommand($sql)->queryAll();
            $sql = 'SELECT SUM(fixed_cost) AS fixed_cost FROM fixed_cost_new WHERE ' . $condition;
            $fixedCostInfo = Yii::app()->db->createCommand($sql)->queryAll();
            $money = $moneyInfo[0]['money'] + $fixedCostInfo[0]['fixed_cost'];
            //ROI
            $ROI = $money == 0 ? 0 : round($order_money * 100 / $money); //ROI
            //订单转化率
            $order_cor = $fans_count == 0 ? 0 : round($order_count * 100 / $fans_count, 1);
            //客单价
            $unit = $order_count == 0 ? 0 : round($order_money / $order_count);
            //进粉成本
            $fans_cost = $fans_count == 0 ? 0 : round($money / $fans_count);
            //均粉产出
            $fans_avg = $fans_count == 0 ? 0 : round($order_money / $fans_count);

            $data['info'][$key]['stat_date'] = date('Y-m-d', $temp_date);
            $data['info'][$key]['fans_count'] = $fans_count;
            $data['info'][$key]['money'] = $money;
            $data['info'][$key]['ROI'] = $ROI;
            $data['info'][$key]['order_cor'] = $order_cor;
            $data['info'][$key]['unit'] = $unit;
            $data['info'][$key]['fans_cost'] = $fans_cost;
            $data['info'][$key]['fans_avg'] = $fans_avg;
            $data['info'][$key]['order_count'] = $order_count;
            $data['info'][$key]['order_money'] = $order_money;
            $key++;
        }

        return $data;
    }

    public function getEffectTableData($param)
    {
        $params['where'] = $param;
        $params['join'] = "
                left join channel as c on c.id=a.channel_id
                left join partner as d on d.id=a.partner_id
                left join wechat as e on e.id=a.weixin_id
                left join cservice as f on f.csno=a.tg_uid
                left join promotion_staff_manage as g on g.user_id=a.tg_uid
                left join goods as h on h.id=a.goods_id
                left join business_types as i on i.bid=a.business_type
                left join customer_service_manage as j on j.id=a.customer_service_id
                left join promotion_manage as k on k.id=a.promotion_id
                left join online_material_manage as l on l.id=k.article_id
                left join material_article_template as m on m.id=l.origin_template_id
                ";
        $params['order'] = "  order by a.stat_date desc,a.weixin_id desc,a.channel_id desc     ";
        $params['select'] = "a.*,k.finance_pay_id,IFNULL(a.money, 0) as money,m.article_code,l.article_type,j.status as cstatus,cname,j.estimate_rate,i.bname,c.channel_name,c.channel_code,e.wechat_id,f.csname_true,h.goods_name,d.name as partner_name";
        $sql = "select " . $params['select'] . " from stat_cost_detail as a " . $params['join'] . $params['where'] . $params['order'];

        $info = Yii::app()->db->createCommand($sql)->queryAll();

        return $info;
    }


    /**
     * 总成本
     * @param $select string
     * @param $condition string
     * @return int
     * @author lxj
     */
    public function getTotalMoney($condition,$select='')
    {
        $select_def = "SUM(money) as total_money";
        if ($select) {
            $select_def .=','.$select;
        }
        $sql = "SELECT ".$select_def." FROM stat_cost_detail where ".$condition;
        $money = Yii::app()->db->createCommand($sql)->queryAll();
        return $money;
    }

    public function getPrintData($condition)
    {
        $params['where'] = $condition;
        $params['join'] = "
                left join customer_service_manage as j on j.id=a.customer_service_id
                ";
        $params['order'] = "  order by a.stat_date desc,a.weixin_id desc,a.channel_id desc     ";
        $params['select'] = "a.*,j.status as cstatus";
        $sql = "select " . $params['select'] . " from stat_cost_detail as a " . $params['join'] . ' where '.$params['where'] . $params['order'];
        $info = Yii::app()->db->createCommand($sql)->queryAll();
        return $info;
    }


}
