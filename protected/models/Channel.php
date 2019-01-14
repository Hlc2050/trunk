<?php
/**
 * 渠道表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/2
 * Time: 14:18
 */
class Channel extends CActiveRecord{
    public function tableName() {
        return '{{channel}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function getChannelCodeList(){
        $channelCodeList = Dtable::toArr($this->findAll());
        return $channelCodeList;
    }

    public function getChannelList($partner_id){
        $channelList=array();
        $sql="select id from channel where partner_id=".$partner_id;
        $result = Yii::app()->db->createCommand($sql)->queryAll();
        foreach ($result as $val){
            $channelList[] = $val['id'];
        }
        return  $channelList;
    }

    public function getChannelCode($pk){
        $info = $this::model()->findByPk($pk);
        return $info->channel_code;

    }
    public function getChannelName($pk){
        $info = $this::model()->findByPk($pk);
        return $info->channel_name;

    }

    public function getChannelCodes($ids){
        $sql="select id,channel_code from channel where id in($ids)";
        $result = Yii::app()->db->createCommand($sql)->queryAll();
        $ret = array();
        foreach ($result as $item) {
            $ret[$item['id']]=$item['channel_code'];
        }
        return $ret;
    }

    public function getChannelNames($ids){
        $sql="select id,channel_name from channel where id in($ids)";
        $result = Yii::app()->db->createCommand($sql)->queryAll();
        $ret = array();
        foreach ($result as $item) {
            $ret[$item['id']]=$item['channel_name'];
        }
        return $ret;
    }

}