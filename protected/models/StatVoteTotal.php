<?php
/**
 * 问卷统计表
 * Created by PhpStorm.
 * User: fxz
 * Date: 2017/02/28
 * Time: 14:35
 */
class StatVoteTotal extends CActiveRecord{
    public function tableName() {
        return '{{stat_vote_total}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

}