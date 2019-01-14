<?php
/**
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/7
 * Time: 11:10
 */
class PackageGroupManage extends CActiveRecord{
    public function tableName() {
        return '{{package_group_manage}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }
    
}