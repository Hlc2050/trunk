<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/20
 * Time: 15:05
 */

class PlanMonthGroup extends CActiveRecord
{
    public function tableName()
    {
        return '{{plan_month_group}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @param $group_id int 分组id
     * @param $month int 时间
     * @param $new_status int 新状态
     */
    public function updateMonthGroupStatus($group_id,$month,$new_status)
    {
        PlanMonthGroup::model()->updateAll(array('status'=>$new_status),'groupid='.$group_id.' and month='.$month);
    }
}