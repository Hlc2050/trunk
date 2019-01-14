<?php

class DataMsgPushLog extends CActiveRecord
{
    public function tableName()
    {
        return '{{data_msg_push_log}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @param array $service_id
     * @param $send_user
     */
    public function addPushLog($service_id){
        $log = new DataMsgPushLog();
        $log->service_id = $service_id;
        $log->add_time = time();
        $log->user_id = Yii::app()->admin_user->uid;
        $log->save();
    }
}
