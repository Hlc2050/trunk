<?php

class PlanWeekLog extends CActiveRecord
{
    public function tableName()
    {
        return '{{plan_week_log}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @param int $week_id
     * @param $plan_type
     * @param $relation_id
     * @param $mask
     * @param $start_date
     */
    public function addPlanWeekLog($week_id=0,$plan_type,$relation_id,$mask,$start_date,$is_mobile=0){
        $name = '';
        if ($plan_type == 1) {
            $user_name = AdminUser::model()->findByPk($relation_id);
            $name = $user_name->csname_true;
        }
        if ($plan_type == 2) {
            $group_name = AdminGroup::model()->findByPk($relation_id);
            $name = $group_name->groupname;
        }
        $month_week = helper::getDateMonthWeek($start_date);
        $time_str = $month_week['year'].'年'.$month_week['month'].'月第'.$month_week['week'].'周';
        $title = $name.'-'.$time_str;
        $log = new PlanWeekLog();
        $log->week_id = $week_id;
        $log->plan_type = $plan_type;
        $log->title = $title;
        if (!$is_mobile) {
            $log->user_id = Yii::app()->admin_user->uid;
        }else {
            $log->user_id = Yii::app()->mobile->uid;
        }
        $log->mask = $mask;
        $log->add_time = time();
        $log->relation_id = $relation_id;
        $log->save();
    }
}
