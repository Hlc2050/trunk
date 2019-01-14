<?php
/**
 * Created by PhpStorm.
 * User: fang
 * Date: 2017/2/24
 * Time: 16:12
 */
class Quest extends CActiveRecord{
    public function tableName() {
        return '{{material_question_bank}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

}
