<?php
/**
 * 素材图文管理表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/2
 * Time: 14:18
 */
class MaterialArticleTemplate extends CActiveRecord{
    public function tableName() {
        return '{{material_article_template}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

}