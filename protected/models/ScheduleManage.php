<?php

/**
 * 计划表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/12/17
 * Time: 14:18
 */
class ScheduleManage extends CActiveRecord
{
    public function tableName()
    {
        return '{{schedule_manage}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

}