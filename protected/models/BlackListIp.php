<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/2
 * Time: 11:10
 */
class BlackListIp extends CActiveRecord
{
    public function tableName()
    {
        return '{{black_list_ip}}';
    }
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * 获取ip黑名单
     * @return array
     * author: yjh
     */
    public function getIpBlackList(){
        $ret=Dtable::toArr($this->findAll());
        $ret=array_column($ret,'ip_adress');
        return $ret;
    }
}