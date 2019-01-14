<?php
/**
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/7
 * Time: 11:10
 */
class OrderPackageRelation extends CActiveRecord{
    public function tableName() {
        return '{{order_package_relation}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }
    
}