<?php
/**
 * Created by PhpStorm.
 * User: fang
 * Date: 2017/2/24
 * Time: 16:12
 */
class StatVoteDetail extends CActiveRecord{
    public function tableName() {
        return '{{stat_vote_detail}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

}
