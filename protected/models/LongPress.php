<?php
/**
 * 推广信息表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/2
 * Time: 14:18
 */
class LongPress extends CActiveRecord{
    public function tableName() {
        return '{{static_longpress}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

}