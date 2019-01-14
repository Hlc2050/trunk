<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/2
 * Time: 11:10
 */
class BlackListPhone extends CActiveRecord
{
    public function tableName()
    {
        return '{{black_list_phone}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);

    }

    /**
     * 获取手机黑名单
     * @return array
     * author: yjh
     */
    public function getPhoneBlackList(){
        $ret=Dtable::toArr($this->findAll());
        $ret=array_column($ret,'phone');
        return $ret;
    }
}