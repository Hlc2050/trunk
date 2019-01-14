<?php
/**
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/7
 * Time: 11:10
 */
class OrderTemplete extends CActiveRecord{
    public function tableName() {
        return '{{order_templete}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    /**
     * 获取下单模板
     * @return array|mixed|null
     * author: yjh
     */
    public function getOrderList($where){
        $orderList= OrderTemplete::model()->findAll("1 ".$where);
        return $orderList;
    }

    /**
     * 获取模板的商品信息，以及是否有推荐
     * @param $templete_id
     * author: yjh
     */
    public function getPackageInfo($templete_id){
        $sql='select a.recommends,a.goods_templete,b.package_id,b.price as package_price,c.name as package_name from order_templete as a 
              left join package_relation as b on b.package_group_id = a.package_gid 
              left join package_manage as c on c.id = b.package_id
              where a.id='.$templete_id.' order by b.package_id desc';
        $res = Yii::app()->db->createCommand($sql)->queryAll();
        $recommends=array();
        if($res) $recommends=helper::decbin_digit($res[0]['recommends'],6);
        foreach ($res as $k=> $val){
            $res[$k]['recommend']=$recommends[$k];
        }
        return $res;
    }

}