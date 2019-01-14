<?php

class PlanWeekUser extends CActiveRecord
{
    public function tableName()
    {
        return '{{plan_week_user}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
