<?php
/**
 * 素材图片关系表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/2
 * Time: 14:18
 */
class MaterialPicRelation extends CActiveRecord{
    public function tableName() {
        return '{{material_pic_relation}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

}