<?php

/**
 * 客服部管理表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/12/16
 * Time: 14:18
 */
class CustomerServiceManage extends CActiveRecord
{
    public function tableName()
    {
        return '{{customer_service_manage}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getCustomerServiceList(){
        $customerServiceList = array();
        $result = Dtable::toArr($this->findAll());
        foreach ($result as $key => $val) {
            $customerServiceList[$key] = $val;
        }
        return $customerServiceList;
    }

    /**
     * 获取客服部名称
     * @param $pk
     * @return mixed
     * author: yjh
     */
    public function getCSName($pk){
        $data = $this->findByPk($pk);
        return $data->cname;
    }

    public function getCSStr(){
        $data = Dtable::toArr($this->findAll('status=1'));
        $result = array();
        foreach ($data as $value){
            $result[]=$value['id'];
        }
        return implode(',',$result );
    }
    public function getCSNames($ids){
        $sql="select id,cname from customer_service_manage where id in($ids)";
        $result = Yii::app()->db->createCommand($sql)->queryAll();
        $ret = array();
        foreach ($result as $item) {
            $ret[$item['id']]=$item['cname'];
        }
        return $ret;
    }

    //获取所有客服部名称
    public function getCustomerName(){
        $temp = array();
        $data =Dtable::toArr($this->findAll());
        foreach ($data as $value){
            $temp[$value['id']] = $value['cname'];
        }
        return $temp;
    }
}