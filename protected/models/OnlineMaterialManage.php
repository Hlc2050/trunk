<?php
/**
 * 上线素材图文管理表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/23
 * Time: 10:18
 */
class OnlineMaterialManage extends CActiveRecord{
    public function tableName() {
        return '{{online_material_manage}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

}