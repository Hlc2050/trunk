<?php
/**
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/7
 * Time: 11:10
 */
class OrderGoodsManage extends CActiveRecord{
    public function getDbConnection() {
        return Yii::app()->getOrderDb();
    }
    public function tableName() {
        return '{{order_goods_manage}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }
    
}