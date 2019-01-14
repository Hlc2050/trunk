<?php
/**
 * 微信小组状态改变记录表
 * Created by PhpStorm.
 * User: lxj
 */
class WeChatGroupStatusLog extends CActiveRecord{
    public function tableName() {
        return '{{wechat_group_status_log}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

}