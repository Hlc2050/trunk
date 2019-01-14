<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/22
 * Time: 14:19
 */
class ChannelType extends CActiveRecord
{
    public function tableName()
    {
        return '{{channel_type}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}