<?php
/**
 * Created by PhpStorm.
 * User: fang
 * Date: 2016/12/13
 * Time: 18:10
 */
class PartnerCost extends CActiveRecord{
    public function tableName() {
        return '{{partner_cost_log}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }
    

    /*
     * 创建渠道费用日志
     * @param $promotion_id 推广id
     * @param $datetime  日期
     *
     */
    public function create($promotion_id,$datetime){
        $promotion=Promotion::model()->findByPk($promotion_id);
        if(!$promotion){
            $ret=array('state'=>-1,'msgwords'=>'推广不存在');
            return $ret;
        }
        $sdomain = PromotionDomain::model()->getPromotionsDomains($promotion_id);
        $sdomain = $sdomain[$promotion_id];
        if(!$sdomain){
            $ret=array('state'=>-2,'msgwords'=>'推广域名不存在');
            return $ret;
        }
        $sql="select * from domain_promotion_change where promotion_id=$promotion_id ";
        $domains=Yii::app()->db->createCommand($sql)->queryAll();
        $mydomains=array();
        foreach($domains as $r){
            $mydomains[$r['domain']]=$r['domain'];
        }
        foreach ($sdomain as $d) {
            $mydomains[$d['domain']]=$d['domain'];
        }

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
        foreach($mydomains as $domain){
            if(!in_array($domain,$domain_cnzz)) continue;
            $ip = $statCnzzFlow['ip'];
            $uv = $statCnzzFlow['uv'];
            $pv = $statCnzzFlow['pv'];
            if (!array_key_exists($domain,$domain_piwik)) continue;
            $ip_piwik += $domain_piwik[$domain]['ip'];
            $uv_piwik += $domain_piwik[$domain]['uv'];
            $pv_piwik += $domain_piwik[$domain]['pv'];

        }
        $financePay=InfancePay::model()->findByPk($promotion->finance_pay_id);
        if(!$financePay){
            $ret=array('state'=>-3,'msgwords'=>'该推广打款不存在');
            return $ret;
        }
        $cost = 0;  //总的成本
        $cost_piwik = 0;
        if($financePay->charging_type==0){
            $cost = $pv*$financePay->unit_price;
            $cost_piwik = $pv_piwik*$financePay->unit_price;
        }else if($financePay->charging_type==1){
            $cost=$uv*$financePay->unit_price;
            $cost_piwik = $uv_piwik*$financePay->unit_price;
        }else if($financePay->charging_type==2){
            $cost=$ip*$financePay->unit_price;
            $cost_piwik = $ip_piwik*$financePay->unit_price;
        }
        $partnerCost=PartnerCost::model()->findByAttributes(array('infance_id'=>$financePay->id,'date'=>$datetime));
        if ($partnerCost){
            $ret=array('state'=>-3,'msgwords'=>'该合作商费用已存在');
            return $ret;
        }
        $partnerCost = new PartnerCost();
        $partnerCost -> system_cost = $cost;
        $partnerCost -> piwik_cost = $cost_piwik;
        $partnerCost -> date = $datetime;
        $partnerCost -> partner_id = $financePay->partner_id;
        $partnerCost -> channel_id = $financePay->channel_id;
        $partnerCost -> infance_id = $financePay->id;
        $partnerCost -> sno=$financePay->sno;
        $partnerCost -> promotion_id=$promotion->id;
        $partnerCost -> promotion_type=$promotion->promotion_type;
        $partnerCost -> save();
        $ret=array('state' => 1,'msgwords' => '创建了1条合作商费用日志');
        return $ret;
    }

    /**
     * 生成免域域名的合作商成本明细
     * @param $promotion_id
     * @param $datetime
     * author: yjh
     */
    public function nonDomainCreate($promotion_id, $datetime){
        $promotion=Promotion::model()->findByPk($promotion_id);
        if(!$promotion){
            $ret=array('state'=>-1,'msgwords'=>'推广不存在');
            return $ret;
        }
        $financePay=InfancePay::model()->findByPk($promotion->finance_pay_id);
        if(!$financePay){
            $ret=array('state'=>-3,'msgwords'=>'该推广打款不存在');
            return $ret;
        }
        $partnerCost = new PartnerCost();
        $partnerCost -> system_cost = 0;
        $partnerCost -> piwik_cost = 0;
        $partnerCost -> date = $datetime;
        $partnerCost -> partner_id = $financePay->partner_id;
        $partnerCost -> channel_id = $financePay->channel_id;
        $partnerCost -> infance_id = $financePay->id;
        $partnerCost -> sno=$financePay->sno;
        $partnerCost -> promotion_type=$promotion->promotion_type;
        $partnerCost -> save();
        $ret=array('state' => 1,'msgwords' => '创建了1条合作商费用日志');
        return $ret;
    }
    /*
     * 处理时间
     * */
    public function getDateTh($start_date,$end_date){
        $start_m = date('m',strtotime($start_date));
        $end_m = date('m',strtotime($end_date));
        $date_l = array();
        if ( $start_m != $end_m){
            $total_show = date('d',strtotime($end_date));
        }else{
            $total_show = $end_date==''?'10':(strtotime($end_date)-strtotime($start_date))/(24*60*60)+1;
        }
        for ($i=0; $i<$total_show; $i++){
            if ($end_date==''){
                $date_l[]=date('m-d',strtotime('-'.$i.' day'));
            }else{
                $date_l[]=date('m-d',strtotime($end_date)-$i*24*60*60);
            }
        }
        return $date_l;
    }
    /*
 * 处理时间
 * */
    public function getDateThTwo($start_date,$end_date){
        $date_l = array();
        $total_show = $end_date==''?'10':(strtotime($end_date)-strtotime($start_date))/(24*60*60)+1;
        for ($i=0; $i<$total_show; $i++){
            if ($end_date==''){
                $date_l[strtotime(strtotime('-'.$i.' day'))]=date('m-d',strtotime('-'.$i.' day'));
            }else{
                $date_l[strtotime($end_date)-$i*24*60*60]=date('m-d',strtotime($end_date)-$i*24*60*60);
            }
        }
        return $date_l;
    }

    /*
     * 处理时间
     * */
    public function getDateInfo($start_date,$end_date){
        $start_m = date('m',strtotime($start_date));
        $end_m = date('m',strtotime($end_date));
        if ( $start_m != $end_m ){
            $total_show = date('d',strtotime($end_date));
        }else{
            $total_show = $end_date == '' ? '10' : (strtotime($end_date)-strtotime($start_date))/(24*60*60)+1;
        }
        $date_s = array();
        for ( $i=0; $i<$total_show; $i++ ){
            if ( $end_date == '' ){
                $date_s[] = strtotime(date('Ymd',strtotime('-'.$i.' day')));
            }else{
                $date_s[] = strtotime(date('Ymd',strtotime($end_date)-$i*24*60*60));
            }
        }
        return $date_s;
    }
    /*
    * 处理时间 不按月
    * */
    public function getDateInfoTwo($start_date,$end_date){
        $start_m = date('m',strtotime($start_date));
        $end_m = date('m',strtotime($end_date));
        $total_show = $end_date == '' ? '10' : (strtotime($end_date)-strtotime($start_date))/(24*60*60)+1;
        $date_s = array();
        for ( $i=0; $i<$total_show; $i++ ){
            if ( $end_date == '' ){
                $date_s[] = strtotime(date('Ymd',strtotime('-'.$i.' day')));
            }else{
                $date_s[] = strtotime(date('Ymd',strtotime($end_date)-$i*24*60*60));
            }
        }
        return $date_s;
    }

    /**
     * 如果修改中间日期的合作商费用日志需要刷新之后日期的合作商费用日志
     * @param $channel_id
     * @param $stat_date
     * @param $partner_cost
     * @return string
     * author: yjh
     */
    public function refreshPartnerCost($channel_id,$stat_date){
        $info = PartnerCost::model()->findAll("channel_id = " . $channel_id . "  and date >= $stat_date order by date asc ");
        $n = count(Dtable::toArr($info));

        if($n==0) return true;
        foreach ($info as $val){
            $lastData = PartnerCost::model()->find("channel_id = " . $val->channel_id . "   and date < $val->date order by date desc ");
            if ($lastData) {
                $balance_prior=$lastData->channel_balance;
            }else{
                $balance_prior = 0;

            }

            $infance_money = InfancePay::model()->findByAttributes(array('partner_id' => $val->partner_id, 'channel_id' => $val->channel_id, 'online_date' => $val->date))->pay_money;
            if (!$infance_money) $infance_money = 0;
            $val->channel_balance = $balance_prior + $infance_money - $val->partner_cost;
//            $val->update_time = time();
            $val->update();
        }
        return true;
    }
}
