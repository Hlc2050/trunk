<?php

class PlanWeekGroup extends CActiveRecord
{
    public function tableName()
    {
        return '{{plan_week_group}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
