<?php
/**
 * Created by PhpStorm.
 * User: fang
 * Date: 2017/1/12
 * Time: 15:01
 */
class PiwikHour extends CActiveRecord{
    public function tableName() {
        return '{{piwik_hour_data}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }
   public function piwikHourCost($chanrging_type,$ip,$pv,$uv,$unit_price){
       $hourCost=0;
       if ($chanrging_type == 0) {
           $hourCost = $pv * $unit_price;
       } else if ($chanrging_type == 1) {
           $hourCost = $uv * $unit_price;
       } else if ($chanrging_type == 2) {
           $hourCost = $ip * $unit_price;
       }
       return$hourCost;
   }
    public function piwikCost($time,$channel_name,$domain){
        $cost_ip=0;
        $cost_uv=0;
        $cost_pv=0;
        $cost_qr=0;
        $cost_wechat=0;
        $date=date('Y-m-d',$time);
        $date=strtotime($date);
        $date_end=$date+24*3600-1;
        $params['where'] = '';
        if ($channel_name != 0){$params['where'] .= " and(d.channel_name=" . $channel_name . ") ";}

        else{$params['where'] .= " and(a.domain=" . $domain . ") ";}

        $params['where'] .= " and(a.stat_date between $date and $date_end) ";
        $params['join']="  left join domain_list as f on a.domain=f.domain
                           left join promotion_manage as b on f.id=b.domain_id
                           left join finance_pay as c on b.finance_pay_id=c.id
                           left join channel as d on b.channel_id=d.id
                           left join partner as e on c.partner_id=e.id     ";
        $params['select']="  a.*,c.unit_price,c.charging_type,c.sno,c.weixin_group_id,d.channel_name,d.channel_code,e.name  ";
        $params['order'] = "  order by a.id desc    ";
        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(PiwikHour::model()->tableName())->listdata($params);
        foreach ($page['listdata']['list'] as $r){
            $cost_ip+=$r['ip'];
            $cost_uv+=$r['uv'];
            $cost_pv+=$r['pv'];
            $cost_qr+=$r['qr_code_click'];
            $cost_wechat+=$r['wechat_touch'];
        }
        $cost_totle=array('ip'=>$cost_ip,'uv'=>$cost_uv,'pv'=>$cost_pv,'qr'=>$cost_qr,'wechat'=>$cost_wechat);
        return $cost_totle;
    }
}