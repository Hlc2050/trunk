<?php
/**
 * Created by PhpStorm.
 * User: fang
 * Date: 2017/1/12
 * Time: 15:01
 */
class StatPiwikFlow extends CActiveRecord{
    public function tableName() {
        return '{{stat_piwik_flow}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

}