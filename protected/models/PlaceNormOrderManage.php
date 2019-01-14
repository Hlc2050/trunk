<?php
/**
 * 订单下单管理表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2017/01/04
 * Time: 14:18
 */
class PlaceNormOrderManage extends CActiveRecord{
    public function tableName() {
        return '{{place_norm_order_manage}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }
    /**
     * 类型一
     * 获取预估发货金额通过（微信号分组）
     * author: yjh
     */
    public function getEstimateMoneyTypeOne($condition){
        $sql = 'select  weixin_id,wechat_id,sum(order_money) as order_money 
                from (SELECT weixin_id,wechat_id,customer_service_id,cname,SUM(order_money)*estimate_rate AS order_money 
                FROM place_norm_order_manage as a 
                left join customer_service_manage as c on c.id=a.customer_service_id 
                WHERE ' . $condition.') t1 
                group by weixin_id';
        $info = Yii::app()->db->createCommand($sql)->queryAll();
        return $info;
    }


    /**
     * 类型二
     * 获取预估发货金额通过（微信号和日期分组）
     * author: yjh
     */
    public function getEstimateMoneyTypeTwo($condition){
        $sql = 'select  weixin_id,wechat_id,order_date,sum(order_money) as order_money 
                FROM (
                SELECT weixin_id,order_date,wechat_id,customer_service_id,cname,SUM(order_money)*estimate_rate AS order_money 
                FROM place_norm_order_manage as a 
                left join customer_service_manage as c on c.id=a.customer_service_id 
                WHERE ' . $condition.'
                ) t 
                group by weixin_id,order_date';
        $info = Yii::app()->db->createCommand($sql)->queryAll();
        return $info;
    }
    /**
     * 类型三
     * 获取预估发货金额
     * author: yjh
     */
    public function getEstimateInfoTypeThree($condition){
        $sql = 'select  addfan_date,weixin_id,SUM(order_count) AS estimate_count,SUM(order_money) as estimate_money 
                from 
                 (
                   SELECT addfan_date,weixin_id,COUNT(a.id)*estimate_rate AS order_count,SUM(order_money)*estimate_rate AS order_money 
                   FROM place_norm_order_manage as a 		
                   LEFT JOIN customer_service_manage AS c ON c.id = a.customer_service_id  
                   WHERE ' . $condition.'
                  ) t1 
                group by addfan_date,weixin_id';
        $info = Yii::app()->db->createCommand($sql)->queryAll();
        return $info;

    }


    public function getSumOrderMoney($condition,$select='')
    {
        $select_def = "SUM(order_money)*estimate_rate as total_money,COUNT(a.id)*estimate_rate as order_count";
        if ($select) {
            $select_def .=','.$select;
        }
        $sql = ' SELECT '.$select_def.' FROM place_norm_order_manage as a LEFT JOIN customer_service_manage AS c ON c.id = a.customer_service_id  and c.status=0 WHERE ' . $condition;
        $info = Yii::app()->db->createCommand($sql)->queryAll();
        return $info;

    }

}