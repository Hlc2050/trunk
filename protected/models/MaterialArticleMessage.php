<?php
/**
 * 图文删除信息
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/2
 * Time: 14:18
 */
class MaterialArticleMessage extends CActiveRecord{
    public function tableName() {
        return '{{material_article_message}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }



}