<?php

class PlanWeekGroupDetail extends CActiveRecord
{
    public function tableName()
    {
        return '{{plan_week_group_detail}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function updateGroupPlanStatus($week_id,$new_status)
    {
        $plan = PlanWeekGroupDetail::model()->updateAll(array('status'=>$new_status),'week_id='.$week_id);
    }
}
