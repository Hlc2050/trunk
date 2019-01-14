<?php
/**
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/7
 * Time: 11:10
 */
class OrderAssistant extends CActiveRecord{
    public function getDbConnection() {
        return Yii::app()->getOrderDb();
    }
    public function tableName() {
        return '{{order_assistant}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }
    
}