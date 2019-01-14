<?php

/**
 * 业务类型表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/12/23
 * Time: 14:18
 */
class BusinessTypes extends CActiveRecord
{
    public function tableName()
    {
        return '{{business_types}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getNameByPk($pk){
        $bname = $this->findByPk($pk)->bname;
        return $bname;
    }

    public function getDXBusinessTypes(){
       $ret =$this->findAll('bid!='.Yii::app()->params['basic']['dx_bid']);
       return $ret;
    }

    public function getBsTypesByTid($tid){
        if($tid==1) $ret = $this->findAll('bid!='.Yii::app()->params['basic']['dx_bid']);
        else $ret = $this->findAll('bid='.Yii::app()->params['basic']['dx_bid']);
        return $ret;
    }

    public function getBNames($ids){
        $sql="select bid,bname from business_types where bid in($ids)";
        $result = Yii::app()->db->createCommand($sql)->queryAll();
        $ret = array();
        foreach ($result as $item) {
            $ret[$item['bid']]=$item['bname'];
        }
        return $ret;
    }


}