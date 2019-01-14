<?php

/**
 * 客服部商品关系表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/12/14
 * Time: 14:18
 */
class CustomerServiceRelation extends CActiveRecord
{
    public function tableName()
    {
        return '{{customer_service_relation}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /*
     * 获取客服部对应商品
     */
    public function getGoodsIds($id)
    {
        $goodsArr = array();
        if (!$id) return '没传入客服部id';
        $result = $this->findAll('cs_id=:cs_id', array(':cs_id' => $id));
        foreach ($result as $key => $val) {
            $goodsArr[] = $val->goods_id;
        }
        return $goodsArr;
    }

    /*
     * 获取客服部对应商品
     */
    public function getGoodsList($id)
    {
        $goodsArr = array();
        if (!$id) return '没传入客服部id';
        $result = $this->findAll('cs_id=:cs_id', array(':cs_id' => $id));
        foreach ($result as $key => $val) {
            $goodsArr[$key]['id'] = $val->goods_id;
            $goodsArr[$key]['goods_id'] = $val->goods_id;
            $goodsArr[$key]['goods_name'] = Goods::model()->findByPk($val->goods_id)->goods_name;
        }
        return $goodsArr;
    }


}