<?php
/**
 * 素材评论管理表
 * User: fang
 * Date: 2017/3/23
 * Time: 10:12
 */
class MaterialReview extends CActiveRecord{
    public function tableName() {
        return '{{material_review}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }
    /**
     * 获取评论列表
     * @param $pk
     * @return array
     * author: fang
     */
    public function getReviewListByPk($pk){
        $quss = Dtable::toArr(MaterialReview::model()->findAll());
        if(empty($quss)) return false;
        $quss[0]['vote_title']=$this->model()->findByPk($pk)->vote_title;
        return $quss;
    }


    /**
     * 获取除论坛的评论列表
     * @return array|mixed|null
     * author: yjh
     */
    public function getReviewList($where){
        $reviewList= MaterialReview::model()->findAll("review_type!=1".$where);
        return $reviewList;
    }
    

    /**
     * 获取论坛评论列表
     * @return array|mixed|null
     * author: yjh
     */
    public function getForumReviewList(){
        $reviewList= MaterialReview::model()->findAll("review_type=1");
        return $reviewList;
    }
}
