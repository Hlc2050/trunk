<?php
/**
 * 订单发货管理表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2017/01/04
 * Time: 14:18
 */
class DeliveryNormOrderManage extends CActiveRecord{
    public function tableName() {
        return '{{delivery_norm_order_manage}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }
   
}