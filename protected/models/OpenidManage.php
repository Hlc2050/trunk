<?php
class OpenidManage extends CActiveRecord{
    public function tableName() {
        return '{{openid_manage}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }
    
}