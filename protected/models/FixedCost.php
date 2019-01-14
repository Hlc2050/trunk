<?php
/**
 * Created by PhpStorm.
 * User: fang
 * Date: 2016/12/15
 * Time: 14:04
 */
class FixedCost extends CActiveRecord
{
    public function tableName()
    {
        return '{{fixed_cost_new}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getFixedCost($condition){
        $sql = '   SELECT stat_date,weixin_id,channel_id,IFNULL(fixed_cost, 0) as fixed_cost,business_type,charging_type,goods_id,customer_service_id
                   FROM fixed_cost_new
                   WHERE ' . $condition;
        $info = Yii::app()->db->createCommand($sql)->queryAll();
        return $info;
    }

    /*
     * 非订阅创建修正成本
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
            $fixCost = FixedCost::model()->findAllByAttributes(array('stat_date' => $datetime, 'weixin_id' => $r['id'], 'promotion_id' => $promotion_id));
            if ($fixCost) {
                continue;
            }
            $i++;
            $fixCost = new FixedCost();
            $fixCost->weixin_id = $r['id'];
            $fixCost->stat_date = $datetime;
            $fixCost->fixed_date = strtotime(date('Ymd'));
            $fixCost->partner_id = $financePay->partner_id;
            $fixCost->channel_id = $financePay->channel_id;
            $fixCost->tg_uid = $r['promotion_staff_id'];
            $fixCost->goods_id = $r['goods_id'];
            $fixCost->customer_service_id = $r['customer_service_id'];
            $fixCost->promotion_id = $promotion_id;
            $fixCost->business_type = $financePay->business_type;
            $fixCost->charging_type = $financePay->charging_type;
            $fixCost->create_time = time();
            $fixCost->update_time = time();
            $fixCost->save();

        }
        $ret = array('state' => 1, 'msgwords' => '创建了' . $i . '条修正成本');
        return $ret;

    }


    /**
     * 总修正成本
     * @param $select string
     * @param $condition string
     * @return int
     * @author lxj
     */
    public function getTotalFixedCost($condition,$select='')
    {
        $select_def = "SUM(fixed_cost) as total_fixed_cost";
        if ($select) {
            $select_def .=','.$select;
        }
        $sql = "SELECT ".$select_def." FROM fixed_cost_new where ".$condition;
        $fixed_cost = Yii::app()->db->createCommand($sql)->queryAll();
        return $fixed_cost;
    }

}