<?php
/**
 * 财务-打款表
 * User: fang
 * Date: 2016/11/16
 * Time: 9:21
 */
class InfancePay extends CActiveRecord{
    public function tableName() {
        return '{{finance_pay}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }
    public function getWechatId($pk){
        $m=$this->findByPk($pk);
        $wechat_id=$m->weixin_group_id;
        return $wechat_id;
    }

    /**
     * 判断该上线日期是否续过费
     * @param $stat_date
     * @param $channel_id
     * author: yjh
     */
    public function isRenew($stat_date,$channel_id)
    {
        $m = $this->find("online_date = $stat_date and channel_id = $channel_id");
        return $m ? true : false;
    }

    /**
     * 查询打款所属渠道上线日期至前一天的实际数据
     * @param $pay_id int 打款id
     * @return array
     * @author lxj
     */
    public function getRealDataByPay($pay_id)
    {
        $pay_info = $this->findByPk($pay_id);
        $pay_array = array();
        if ($pay_info) {
            //投入金额
            //预估发货金额
            $deliver_money = 0;
            //进线量
            $fans_input = 0;
            $cid = $pay_info->channel_id;
            $online_date = $pay_info->online_date;
            $dx_bid = Yii::app()->params['basic']['dx_bid'];
            $end_date = strtotime(date('Y-m-d',strtotime('-1 day')));
            //查询渠道投入金额
            $cost_where = 'business_type !='.$dx_bid.' and  channel_id = '.$cid.' and stat_date between '.$online_date.' and '.$end_date;
            $sql = 'select sum(money) as money from stat_cost_detail where '.$cost_where;
            $cost = Yii::app()->db->createCommand($sql)->queryAll();
            $money = $cost ? $cost[0]['money']:0;

            //查询渠道修正成本
            $fixed_where =  ' channel_id = '.$cid.' and stat_date between '.$online_date.' and '.$end_date;
            $sql = 'select sum(fixed_cost) as fixed_cost from fixed_cost_new where '.$fixed_where;
            $fixed_cost = Yii::app()->db->createCommand($sql)->queryAll();
            if ($fixed_cost) {
                $money += $fixed_cost[0]['fixed_cost'];
            }
            //上线当天的投入金额
            $where = ' channel_id = '.$cid.' and stat_date = '.$online_date;
            $sql = 'select sum(money) as money from stat_cost_detail where '.$where;
            $cost = Yii::app()->db->createCommand($sql)->queryAll();
            $online_money = $cost ? $cost[0]['money']:0;
            //查询渠道修正成本
            $sql = 'select sum(fixed_cost) as fixed_cost from fixed_cost_new where '.$where;
            $fixed_cost = Yii::app()->db->createCommand($sql)->queryAll();
            if ($fixed_cost) {
                $online_money += $fixed_cost[0]['fixed_cost'];
            }
            //非电销业务类型根据订单下单表、进粉表获取发货金额、进粉量
            //获取订单下单信息
            $condition = " addfan_date between " . $online_date . " and " . $end_date . " and c.status=0  group by addfan_date,weixin_id,customer_service_id";
            $normOrderInfo = PlaceNormOrderManage::model()->getEstimateInfoTypeThree($condition);
            $date_wechat = $this->getDataWechat($online_date,$end_date);
            if ($normOrderInfo) {
                foreach ($normOrderInfo as $value) {
                    $key = $value['addfan_date'] . "_" . $value['weixin_id'];
                    if (array_key_exists($key,$date_wechat)) {
                        if ($date_wechat[$key] == $cid) {
                            $deliver_money += $value['estimate_money'] * 0.01;
                        }
                    } else{
                        $temp_date = $value['addfan_date'] - 86400;
                        for ($d = $temp_date; $d >= ($temp_date - 86400 * 10); $d = $d - 86400) {
                            $key2 = $d . "_" . $value['weixin_id'];
                            if (array_key_exists($key2, $date_wechat)) {
                                if ($date_wechat[$key2] == $cid) {
                                    $deliver_money += $value['estimate_money'] * 0.01;
                                }
                                break;
                            }
                        }
                    }
                }
            }
            //进粉量
            $end_date_ten = $online_date+10*86400;
            $condition = " addfan_date between " . $online_date . " and " . $end_date_ten . "  group by addfan_date,weixin_id";
            $fansCountInfo = FansInputManage::model()->getFansTypeThree($condition);
            if ($fansCountInfo) {
                foreach ($fansCountInfo as $value) {
                    $key = $value['addfan_date'] . "_" . $value['weixin_id'];
                    if (array_key_exists($key,$date_wechat)) {
                        if ($date_wechat[$key] == $cid && $value['addfan_date']==$online_date) {
                            $fans_input += $value['fans_count'];
                        }
                    } else{
                        $temp_date = $value['addfan_date'] - 86400;
                        for ($d = $temp_date; $d >= ($temp_date - 86400 * 10); $d = $d - 86400) {
                            $key2 = $d . "_" . $value['weixin_id'];
                            if (array_key_exists($key2, $date_wechat)) {
                                if ($date_wechat[$key2] == $cid && $d==$online_date) {
                                    $fans_input += $value['fans_count'];
                                }
                                break;
                            }
                        }
                    }
                }
            }
            $pay_array = array(
                'roi' => $money>0 ? round($deliver_money*100/$money,2):0,
                'fans_cost' => $fans_input>0 ? round($online_money/$fans_input,2):0,
                'fans_input' => $fans_input,
            );
        }
        return $pay_array;

    }

    public function createPrintData($pay_id)
    {
        ini_set('memory_limit', -1);
        $cache = Yii::app()->cache;
        $pay_info = $this->findByPk($pay_id);
        $dx_bid = Yii::app()->params['basic']['dx_bid'];
        //本月开始时间
        $start_date = strtotime(date('Y-m-01', time()));
        //最近两个月开始时间
        $start_date_two = strtotime(date('Y-m-01', strtotime('-1 month')));
        $end_date = strtotime(date('Y-m-d',strtotime('-1 day')));
        //渠道汇总数据
        $channel_data = array();
        $channel_data['current']['order_money'] = $channel_data['current']['order_count'] = $channel_data['current']['fans_input'] =0;
        $channel_data['last']['order_money'] = $channel_data['last']['order_count'] = $channel_data['last']['fans_input'] =0;
        $partner_data = array();
        $partner_data['current']['order_money'] = $partner_data['current']['order_count'] = $partner_data['current']['fans_input'] =0;
        $partner_data['last']['order_money'] = $partner_data['last']['order_count'] = $partner_data['last']['fans_input'] =0;
        if ($pay_info) {
            $cid = $pay_info->channel_id;
            /************渠道汇总数据  start**************/
            $where = ' business_type!='.$dx_bid.' and  channel_id='.$cid.' and stat_date between '.$start_date.' and '.$end_date;
            $money = StatCostDetail::model()->getTotalMoney($where);
            $channel_data['current']['money'] = $money[0]['total_money']? $money[0]['total_money']:0;
            $where = ' business_type!='.$dx_bid.' and  channel_id='.$cid.' and stat_date between '.$start_date_two.' and '.$end_date;
            $money = StatCostDetail::model()->getTotalMoney($where);
            $channel_data['last']['money'] = $money[0]['total_money'] ? $money[0]['total_money']:0;
            /************渠道汇总数据  end**************/

            /************推广人员合作商效果  start**************/
            $tg_uid = $pay_info->sno;
            $partner_id = $pay_info->partner_id;
            //推广人员关联测试号
            $test_uid = PromotionStaffRelation::model()->getRelationByStaff($tg_uid);
            $test_uid[] = $tg_uid;
            $uids = array_unique($test_uid);
            $uid_str = implode(',',$uids);
            //非电销数据
            $where = ' business_type!='.$dx_bid.' and stat_date between '.$start_date.' and '.$end_date.' and partner_id='.$partner_id.' and tg_uid in ('.$uid_str.')';
            $money = StatCostDetail::model()->getTotalMoney($where);
            $partner_data['current']['money'] = $money[0]['total_money'] ? round($money[0]['total_money'],2):0;
            $where = ' business_type!='.$dx_bid.' and stat_date between '.$start_date_two.' and '.$end_date.' and partner_id='.$partner_id.' and tg_uid in ('.$uid_str.')';
            $money = StatCostDetail::model()->getTotalMoney($where);
            $partner_data['last']['money'] = $money[0]['total_money'] ? round($money[0]['total_money'],2):0;
            $cost = Dtable::toArr(StatCostDetail::model()->findAll(array('select'=>'id','condition'=>$where)));
            $cost_ids = array_column($cost,'id');
            //修正成本
            $where = ' stat_date between '.$start_date_two.' and '.$end_date . "  group by stat_date,channel_id,weixin_id,business_type,charging_type,goods_id,customer_service_id";
            $fixed_cost = FixedCost::model()->getFixedCost($where);
            $wechatFixed = array();
            if ($fixed_cost) {
                foreach ($fixed_cost as $value) {
                    $key = $value['stat_date'] . "_" . $value['weixin_id'].'_'. $value['channel_id'];
                    $wechatFixed[$key] = $value;
                }
            }
            //查询成本明细表
            $where = "  stat_date between " . $start_date_two . " and " . $end_date;
            $stat_cost = StatCostDetail::model()->getPrintData($where);
            //非电销业务类型根据订单下单表、进粉表获取发货金额、进粉量
            //查询渠道预估发货金额
            //获取订单下单信息
            $condition = "  addfan_date between " . $start_date_two . " and " . $end_date . "   group by addfan_date,weixin_id,customer_service_id";
            $normOrderInfo = PlaceNormOrderManage::model()->getEstimateInfoTypeThree($condition);
            $wechatOrder = array();
            foreach ($normOrderInfo as $value){
                $key = $value['addfan_date'] . "_" . $value['weixin_id'];
                $wechatOrder[$key] = $value;
            }
            //进粉量
            $condition = " addfan_date between " . $start_date_two . " and " . $end_date . "  group by addfan_date,weixin_id";
            $fansCountInfo = FansInputManage::model()->getFansTypeThree($condition);
            $wechatFans = array();
            foreach ($fansCountInfo as $value){
                $key = $value['addfan_date'] . "_" . $value['weixin_id'];
                $wechatFans[$key] = $value;
            }
            $temp_Info = array();
            foreach ($stat_cost as $k=>$c) {
                $key = $c['stat_date'] . "_" . $c['weixin_id'];
                if (!array_key_exists($key, $temp_Info)) {
                    $temp_Info[$key] = $k;
                }
                if ($c['cstatus'] == 0) {
                    if (array_key_exists($key,$wechatOrder)) {
                        $money = $wechatOrder[$key];
                        //渠道
                        if ($c['channel_id']==$cid) {
                            $channel_data['last']['order_money'] += round($money['estimate_money'] * 0.01, 2);
                            $channel_data['last']['order_count'] += round($money['estimate_count'] * 0.01,2);
                            if ($money['addfan_date'] >= $start_date) {
                                $channel_data['current']['order_money'] += round($money['estimate_money'] * 0.01, 2);
                                $channel_data['current']['order_count'] += round($money['estimate_count'] * 0.01,2);
                            }
                        }
                        //合作商
                        if (in_array($c['id'],$cost_ids)) {
                            $partner_data['last']['order_money'] += round($money['estimate_money'] * 0.01, 2);
                            $partner_data['last']['order_count'] += round($money['estimate_count'] * 0.01,2);
                            if ($money['addfan_date'] >= $start_date) {
                                $partner_data['current']['order_money'] += round($money['estimate_money'] * 0.01, 2);
                                $partner_data['current']['order_count'] += round($money['estimate_count'] * 0.01,2);
                            }
                        }
                        unset($wechatOrder[$key]);
                    }
                }
                if (array_key_exists($key,$wechatFans)){
                    $fan = $wechatFans[$key];
                    //渠道
                    if ($c['channel_id']==$cid) {
                        $channel_data['last']['fans_input'] += $fan['fans_count'];
                        if ($fan['addfan_date'] >= $start_date) {
                            $channel_data['current']['fans_input'] += $fan['fans_count'];
                        }
                    }
                    if (in_array($c['id'],$cost_ids)) {
                        $partner_data['last']['fans_input'] += $fan['fans_count'];
                        if ($fan['addfan_date'] >= $start_date) {
                            $partner_data['current']['fans_input'] += $fan['fans_count'];
                        }
                    }
                    unset($wechatFans[$key]);
                }
                $key_2 = $c['stat_date'] . "_" . $c['weixin_id'] . "_" . $c['channel_id'];
                if (array_key_exists($key_2,$wechatFixed)){
                    //渠道
                    if ($c['channel_id']==$cid) {
                        $channel_data['last']['money'] += round($wechatFixed[$key_2]['fixed_cost'],2);
                        if ($c['stat_date'] >= $start_date) {
                            $channel_data['current']['money'] += round($wechatFixed[$key_2]['fixed_cost'],2);
                        }
                    }
                    if (in_array($c['id'],$cost_ids)) {
                        $partner_data['last']['money'] += round($wechatFixed[$key_2]['fixed_cost'],2);
                        if ($c['stat_date'] >= $start_date) {
                            $partner_data['current']['money'] += round($wechatFixed[$key_2]['fixed_cost'],2);
                        }
                    }
                }
            }
            //发货金额
            foreach ($wechatOrder as $key=>$value) {
                if (array_key_exists($key, $wechatFans)) {
                    $fans_count = $wechatFans[$key]['fans_count'];
                    unset($wechatFans[$key]);
                } else {
                    $fans_count = 0;
                }
                $temp_date = $value['addfan_date'] - 86400;
                for ($d = $temp_date; $d >= ($temp_date - 86400 * 10); $d = $d - 86400) {
                    $key2 = $d . "_" . $value['weixin_id'];
                    if (array_key_exists($key2, $temp_Info)) {
                        $cost_info = $stat_cost[$temp_Info[$key2]];
                        //渠道
                        if ($cost_info['channel_id']==$cid) {
                            $channel_data['last']['order_money'] += round($value['estimate_money'] * 0.01, 2);
                            $channel_data['last']['order_count'] += round($value['estimate_count'] * 0.01, 2);
                            $channel_data['last']['fans_input'] += $fans_count;
                            if ($d >= $start_date) {
                                $channel_data['current']['order_money'] += round($value['estimate_money'] * 0.01, 2);
                                $channel_data['current']['order_count'] += round($value['estimate_count'] * 0.01, 2);
                                $channel_data['current']['fans_input'] += $fans_count;
                            }
                        }
                        if (in_array($cost_info['id'],$cost_ids)) {
                            $partner_data['last']['order_money'] += round($value['estimate_money'] * 0.01, 2);
                            $partner_data['last']['order_count'] += round($value['estimate_count'] * 0.01, 2);
                            $partner_data['last']['fans_input'] += $fans_count;
                            if ($d >= $start_date) {
                                $partner_data['current']['order_money'] += round($value['estimate_money'] * 0.01, 2);
                                $partner_data['current']['order_count'] += round($value['estimate_count'] * 0.01, 2);
                                $partner_data['current']['fans_input'] += $fans_count;
                            }
                        }
                        break;
                    }
                }
            }
            //进粉
            if ($wechatFans) {
                foreach ($wechatFans as $key => $value) {
                    $keyInfo = explode('_', $key);
                    $temp_date = intval($keyInfo[0]) - 86400;
                    for ($d = $temp_date; $d >= ($temp_date - 86400 * 10); $d = $d - 86400) {
                        $key2 = $d . "_" . $value['weixin_id'];
                        if (array_key_exists($key2, $temp_Info)) {
                            $cost_info = $stat_cost[$temp_Info[$key2]];
                            //渠道
                            if ($cost_info['channel_id']==$cid) {
                                $channel_data['last']['fans_input'] += $value['fans_count'];
                                if ($d >= $start_date) {
                                    $channel_data['current']['fans_input'] += $value['fans_count'];
                                }
                            }
                            if (in_array($cost_info['id'],$cost_ids)) {
                                $partner_data['last']['fans_input'] += $value['fans_count'];
                                if ($d >= $start_date) {
                                    $partner_data['current']['fans_input'] += $value['fans_count'];
                                }
                            }
                            break;
                        }
                    }
                }
            }

            $channel_data['current']['ROI'] = $channel_data['current']['money'] ? round($channel_data['current']['order_money']*100/$channel_data['current']['money'],2):0;
            $channel_data['current']['fans_cost'] = $channel_data['current']['fans_input'] ? round($channel_data['current']['money']/$channel_data['current']['fans_input'],2):0;
            $channel_data['last']['ROI'] = $channel_data['last']['money'] ? round($channel_data['last']['order_money']*100/$channel_data['last']['money'],2):0;
            $channel_data['last']['fans_cost'] = $channel_data['last']['fans_input'] ? round($channel_data['last']['money']/$channel_data['last']['fans_input'],2):0;
            $channel_data['current']['channel_rate'] = $channel_data['current']['fans_input'] ? round($channel_data['current']['order_count']*100/$channel_data['current']['fans_input'],2):0;
            $channel_data['last']['channel_rate'] = $channel_data['last']['fans_input'] ? round($channel_data['last']['order_count']*100/$channel_data['last']['fans_input'],2):0;
            //渠道效果表上线日期
            $current_online = StatCostDetail::model()->find('channel_id='.$cid.' and stat_date between '.$start_date.' and '.$end_date.' order by stat_date asc');
            $channel_data['current']['online_days'] = $current_online?($end_date-$current_online->stat_date)/86400:0;
            $last_online = StatCostDetail::model()->find('channel_id='.$cid.' and stat_date between '.$start_date_two.' and '.$end_date.' order by stat_date asc');
            $channel_data['last']['online_days'] = $last_online?($end_date-$last_online->stat_date)/86400:0;

            $partner_data['current']['ROI'] = $partner_data['current']['money'] ? round($partner_data['current']['order_money']*100/$partner_data['current']['money'],2):0;
            $partner_data['current']['fans_cost'] = $partner_data['current']['fans_input'] ? round($partner_data['current']['money']/$partner_data['current']['fans_input'],2):0;
            $partner_data['last']['ROI'] = $partner_data['last']['money'] ? round($partner_data['last']['order_money']*100/$partner_data['last']['money'],2):0;
            $partner_data['last']['fans_cost'] = $partner_data['last']['fans_input'] ? round($partner_data['last']['money']/$partner_data['last']['fans_input'],2):0;
            $partner_data['current']['channel_rate'] = $partner_data['current']['fans_input'] ? round($partner_data['current']['order_count']*100/$partner_data['current']['fans_input'],2):0;
            $partner_data['last']['channel_rate'] = $partner_data['last']['fans_input'] ? round($partner_data['last']['order_count']*100/$partner_data['last']['fans_input'],2):0;
        }
        $page = array(
            'create_time'=>time(),
            'channel_data' =>$channel_data,
            'partner_data' =>$partner_data,
        );
        $printData = $cache->get('printData');
        $records = unserialize($printData);
        $records['printData_'.$pay_id] = $page;
        $cache->set('printData', serialize($records));
    }


    /**
     * @param $start_date int 开始日期
     * @param $end_date int 结束日期
     * @param int $type int 0：数组值为渠道id 1:数组值为成本明细id
     * @return array
     */
    public function getDataWechat($start_date,$end_date,$type=0)
    {
        //查询上线时间到前一天日期的渠道打款
        $all_pay = StatCostDetail::model()->findAll(' stat_date between '.$start_date.' and '.$end_date.' and business_type!='.Yii::app()->params['basic']['dx_bid']);
        $all_pay = Dtable::toArr($all_pay);
        $service = Dtable::toArr(CustomerServiceManage::model()->findAll());
        $service = array_combine(array_column($service,'id'),array_column($service,'status'));
        //上线日期,微信号数组
        $date_wechat = array();
        if ($all_pay) {
            foreach ($all_pay as $value) {
                if ($type == 0) {
                    $date_wechat[$value['stat_date'].'_'.$value['weixin_id']] = array($value['channel_id'],$service[$value['customer_service_id']]);
                }
                if ($type == 1) {
                    $date_wechat[$value['stat_date'].'_'.$value['weixin_id']] = array($value['id'],$service[$value['customer_service_id']]);
                }
            }
        }
        return $date_wechat;
    }


    
}