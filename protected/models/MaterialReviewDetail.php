<?php
/**
 * Created by PhpStorm.
 * User: fang
 * Date: 2017/3/23
 * Time: 10:12
 */
class MaterialReviewDetail extends CActiveRecord{
    public function tableName() {
        return '{{material_review_detail}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

}
