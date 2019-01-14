<?php
/**
 * 客服部预计表
 * User: Administrator
 * Date: 2018/11/23
 * Time: 11:17
 */

class FinanceServiceData extends CActiveRecord{
    public function tableName() {
        return '{{finance_service_data}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }
    public function updateServiceData($pay_id)
    {
        $pay_info = InfancePay::model()->findByPk($pay_id);
        if (!$pay_info) {
            return false;
        }
        $wechat_group = $pay_info->weixin_group_id;
        //预计每日进粉量
        $day_fans_input = $pay_info->day_fans_input;
        $online_day = $pay_info->online_day;
        //先删除已有的打款数据
        $this->deleteAll('fance_pay_id='.$pay_id);
        //查询客服部微信号信息
        $service_wechat = WeChatGroup::model()->getServiceWechat($wechat_group,1);
        if (!$service_wechat) {
            return false;
        }
        $service_info = array();
        //总的微信个数
        $wechat_count = 0;
        foreach ($service_wechat as $key=>$value) {
            $service_info[$key] = array(
                'wechats'=>implode(',',$value),
                'wechat_count'=>count($value),
            );
            $wechat_count += count($value);
        }
        //计算每个客服部的进粉量=预计每日进粉量/总的微信个数*客服部的微信号个数
        $single_count = $day_fans_input/$wechat_count;
        $insert_data = array();
        foreach ($service_info as $sid=>$value) {
            for ($i=0;$i<$online_day;$i++) {
                $date = date('Ymd',$pay_info->online_date+($i*24*60*60));
                $insert_data[] = array(
                    'date'=>$date,
                    'sno'=>$pay_info->sno,
                    'channel_id'=>$pay_info->channel_id,
                    'wechat_list'=>$value['wechats'],
                    'service_id'=>$sid,
                    'fance_pay_id'=>$pay_id,
                    'service_fans_input'=>round($single_count*$value['wechat_count']),
                    'service_wechat'=>$value['wechat_count'],
                    'add_time'=>time(),
                );
            }
        }
        $keys = array('date','sno','channel_id','wechat_list','service_id','fance_pay_id','service_fans_input','service_wechat','add_time');
        helper::batch_insert_data('finance_service_data',$keys,$insert_data);
    }

    public function deleteServiceData($pay_id)
    {
        $this->deleteAll('fance_pay_id='.$pay_id);
    }
}