<?php

/**
 * 业务类型关联计费方式表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/12/23
 * Time: 14:18
 */
class BusinessTypeRelation extends CActiveRecord
{
    public function tableName()
    {
        return '{{business_type_relation}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getChargingTypes($id)
    {
        $chargingTypesArr = array();
        if (!$id) return '未传入id';
        $result = $this->findAll('bid=:bid', array(':bid' => $id));
        foreach ($result as $key => $val) {
            $chargingTypesArr[] = $val->charging_type;
        }
        return $chargingTypesArr;
    }

    public function getChargeTypes($id)
    {
        $result=array();
        $list = vars::$fields['charging_type'];
        if(!$id) return 0;
        $data = $this->model()->getChargingTypes($id);
        foreach ($list as $key=>$val){
            if(in_array($val['value'],$data))
                $result[]=$val;
        }
        return $result;
    }
}