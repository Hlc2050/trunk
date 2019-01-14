<?php
/**
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/7
 * Time: 11:10
 */
class FailOrderLogs extends CActiveRecord{

    public function tableName() {
        return '{{fail_order_logs}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

}