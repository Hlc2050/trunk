<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/16
 * Time: 9:48
 */

class PromotionUserRelation extends CActiveRecord
{
    public function tableName()
    {
        return '{{promotion_user_relation}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getRelationByUser($user_id)
    {
        $users = $this->findAll('user_id = '.$user_id);
        $promotion_ids = array();
        foreach ($users as $value) {
            $promotion_ids[] = $value['promotion_user_id'];
        }
        return $promotion_ids;
    }
}