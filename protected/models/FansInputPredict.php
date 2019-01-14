<?php
/**
 * 粉丝录入管理
 * Created by PhpStorm.
 * User: yjh
 * Date: 2017/01/05
 * Time: 14:18
 */
class FansInputPredict extends CActiveRecord{
    public function tableName() {
        return '{{fans_input_predict}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }
}