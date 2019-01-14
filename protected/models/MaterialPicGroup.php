<?php
/**
 * 素材图片组别管理表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/2
 * Time: 14:18
 */
class MaterialPicGroup extends CActiveRecord{
    public function tableName() {
        return '{{material_pic_group}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function getPicGroupList()
    {
        $picGroupList = self::model()->findAll();
        return $picGroupList;
    }
}