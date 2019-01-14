<?php
/**
 * Created by PhpStorm.
 * User: fang
 * Date: 2017/2/24
 * Time: 16:12
 */
class Questionnaire extends CActiveRecord{
    public function tableName() {
        return '{{material_questionnaire}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    /**
     * 通过商品类别id获取问卷列表
     * @param $cat_id
     * @return array
     * author: yjh
     */
    public function getPSQByCatId($cat_id){
        $result=$this->findAll("cat_id=($cat_id)");
        return $result;
    }

    /**
     * 获取问卷内容
     * @param $pk
     * @return array
     * author: yjh
     */
    public function getPSQListByPk($pk){
        $quss = Dtable::toArr(Quest::model()->findAll("qus_id=".$pk));
        if(empty($quss)) return false;
        $quss[0]['vote_title']=$this->model()->findByPk($pk)->vote_title;
        return $quss;
    }
    
    

}
