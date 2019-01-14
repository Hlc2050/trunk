<?php

class PlanWeekUserDetail extends CActiveRecord
{
    public function tableName()
    {
        return '{{plan_week_user_detail}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function updateUserPlanStatus($week_id,$new_status)
    {
        PlanWeekUserDetail::model()->updateAll(array('status'=>$new_status),'week_id='.$week_id);
    }
}
