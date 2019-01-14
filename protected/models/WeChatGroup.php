<?php
/**
 * 微信小组表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/2
 * Time: 14:18
 */
class WeChatGroup extends CActiveRecord{
    public function tableName() {
        return '{{wechat_group}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }
    
    public function status($id,$status){
        //状态修改为备用时判断是否有上线推广在使用该微信号小组
        if ($status == 0) {
            $fancePay = Dtable::toArr(InfancePay::model()->findAll(
                array(
                    'select'=>'id',
                    'condition'=>'weixin_group_id='.$id,
                )));
            $pay_ids = array_column($fancePay,'id');
            if ($pay_ids) {
                $promotion = Promotion::model()->find('status!=1  and finance_pay_id in ('.implode(',',$pay_ids).')');
                if ($promotion) {
                    return false;
                }
            }
        }
        $m=$this->findByPk($id);
        $old_status = $m->status;
        if(!$m) {
            return false;
        }
        $m->id=$id;
        $m->status=$status;
        $m->save();
        //写入状态改变日志
        if ($old_status != $status) {
            $log_details = '状态由'.vars::get_field_str('weChatGroup_status', $old_status).'变更为'.vars::get_field_str('weChatGroup_status', $status);
            $status_log = new WeChatGroupStatusLog();
            $status_log->user_id = Yii::app()->admin_user->uid;
            $status_log->add_time = time();
            $status_log->group_id = $id;
            $status_log->log_details = $log_details;
            $status_log->save();
        }
    }


    /**
     * 统计微信号客服部
     * @param $group_id int 微信小组id
     * @param $type int
     * @return array
     * @author lxj
     */
    public function getServiceWechat($group_id,$type=0)
    {
        $wechats = WeChatRelation::model()->findAll('wechat_group_id='.$group_id);
        $wechats = Dtable::toArr($wechats);
        $wechat_ids = array_column($wechats,'wid');
        $service_wechat = array();
        if ($wechat_ids) {
            $wechat_info = WeChat::model()->findAll(
                array('select'=>'id,wechat_id,customer_service_id','condition'=>'id in ('.implode(',',$wechat_ids).')')
            );
            if ($type == 0) {
                foreach ($wechat_info as $value) {
                    $service_wechat[$value['customer_service_id']][] = $value['wechat_id'];
                }
            }else{
                foreach ($wechat_info as $value) {
                    $service_wechat[$value['customer_service_id']][] = $value['id'];
                }
            }

        }
        return $service_wechat;


    }


}