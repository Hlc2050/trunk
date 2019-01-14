<?php

class PlanMonthLog extends CActiveRecord
{
    public function tableName()
    {
        return '{{plan_month_log}}';
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
     * @param $month
     */
    public function addPlanMonthLog($week_id=0,$plan_type,$relation_id,$mask,$month,$is_mobile=0){
        $name = '';
        if ($plan_type == 1) {
            $user_name = AdminUser::model()->findByPk($relation_id);
            $name = $user_name->csname_true;
        }
        if ($plan_type == 2) {
            $group_name = AdminGroup::model()->findByPk($relation_id);
            $name = $group_name->groupname;
        }
        $month_week = helper::getDateMonthWeek($month);
        $time_str = $month_week['year'].'-'.$month_week['month'];
        $title = $name.'-'.$time_str.'-进粉计划';
        $log = new PlanMonthLog();
        $log->month_id = $week_id;
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
