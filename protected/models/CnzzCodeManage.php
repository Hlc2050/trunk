<?php
/**
 * 总统计管理表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/2
 * Time: 14:18
 */
class CnzzCodeManage extends CActiveRecord{
    public function tableName() {
        return '{{cnzz_code_manage}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

}