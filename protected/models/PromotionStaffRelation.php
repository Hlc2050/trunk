<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/16
 * Time: 9:48
 */

class PromotionStaffRelation extends CActiveRecord
{
    public function tableName()
    {
        return '{{promotion_staff_relation}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getRelationByStaff($staff_id)
    {
        $users = $this->findAll('promotion_staff_id = '.$staff_id);
        $promotion_ids = array();
        foreach ($users as $value) {
            $promotion_ids[] = $value['test_staff_id'];
        }
        return $promotion_ids;
    }
}