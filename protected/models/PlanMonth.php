<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/17
 * Time: 16:50
 */
class PlanMonth extends CActiveRecord
{
    public function tableName()
    {
        return '{{plan_month}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @param $tg_uid int 推广人员id
     * @param $month int 时间戳
     * @param $new_status int 新状态
     */
    public function updateMonthStatus($tg_uid,$month,$new_status)
    {
        PlanMonth::model()->updateAll(array('status'=>$new_status),'tg_uid='.$tg_uid.' and month='.$month);
    }


}