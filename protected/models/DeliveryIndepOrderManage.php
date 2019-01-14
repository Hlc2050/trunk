<?php
/**
 * 独立订单发货管理表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/12/29
 * Time: 14:18
 */
class DeliveryIndepOrderManage extends CActiveRecord{
    public function tableName() {
        return '{{delivery_indep_order_manage}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }
   
}