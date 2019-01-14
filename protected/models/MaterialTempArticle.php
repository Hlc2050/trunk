<?php
/**
 * 素材图文另存为缓存管理表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/2
 * Time: 14:18
 */
class MaterialTempArticle extends CActiveRecord{
    public function tableName() {
        return '{{material_temp_article}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

}