<?php
/**
 * 商品表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/2
 * Time: 14:18
 */
class Goods extends CActiveRecord{
    public function tableName() {
        return '{{goods}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    /**
     * 获取商品名称
     * @param $pk
     * @return mixed
     * author: yjh
     */
    public function getGoodsName($pk){
        $data = $this->findByPk($pk);
        return $data->goods_name;
    }
    /**
     * 获取商品列表
     * @param $pk
     * @return mixed
     * author: yjh
     */
    public function getGoodsList(){
        $data = Dtable::toArr($this->findAll());
        $goodsList =array();
        foreach ($data as $key=>$val){
            $goodsList[$val['id']]=$val['goods_name'];
        }
        return $goodsList;
    }
    /**
     * 获取商品列表
     * @param $pk
     * @return mixed
     * author: fang
     */
    public function getGoodsAllList(){
        $data = Dtable::toArr($this->findAll());
        $goodsList =array();
        foreach ($data as $key=>$val){
            $goodsList[$key]=$val;
        }
        return $goodsList;
    }

    /**
     * 获取微信名ID
     * author: yjh
     */
    public function getGoodsNameByIds($idsArr)
    {

        if ($idsArr == '') return '无商品';
        $goodsArr=array();
        foreach ($idsArr as $key => $val) {
            $data = $this->findByPk($val['goods_id']);
            $goodsArr[] = $data->goods_name;
        }
        return implode('&nbsp;&nbsp;', $goodsArr);
    }
    /**
     * 获取商品类型下单金额
     * author: fang
     */
    public function getPlaceMoneyByGoods($condition){
        $sql = "SELECT sum(order_money) as order_money,cat_id FROM (
                  SELECT c.cat_id,b.customer_service_id,d.estimate_rate,SUM(b.order_money)*d.estimate_rate AS order_money 
                  FROM place_indep_order_manage AS b 
                  LEFT JOIN goods AS c ON c.id = b.goods_id 
                  LEFT JOIN customer_service_manage AS d ON d.id = b.customer_service_id
		          WHERE ".$condition."
		          GROUP BY b.goods_id,b.customer_service_id
		          ) AS a 
               GROUP BY cat_id;";
        $order_indep_money = Yii::app()->db->createCommand($sql)->queryAll();
        $keyArr1=array_column($order_indep_money,'cat_id');
        $order_indep_money = array_combine($keyArr1,$order_indep_money);

        $sql = "SELECT sum(order_money) as order_money,cat_id FROM (
                  SELECT c.cat_id,b.customer_service_id,d.estimate_rate,SUM(b.order_money)*d.estimate_rate AS order_money 
                  FROM place_norm_order_manage AS b 
                  LEFT JOIN goods AS c ON c.id = b.goods_id 
                  LEFT JOIN customer_service_manage AS d ON d.id = b.customer_service_id
		          WHERE ".$condition."
		          GROUP BY b.goods_id,b.customer_service_id
		          ) AS a 
               GROUP BY cat_id;";
        $order_norm_money = Yii::app()->db->createCommand($sql)->queryAll();
        $keyArr2=array_column($order_norm_money,'cat_id');
        $order_norm_money = array_combine($keyArr2,$order_norm_money);

        $keyArr = array_unique(array_merge($keyArr1,$keyArr2));

        $data =array();
        foreach ($keyArr as $val){
            $data[$val]['cat_name'] = Linkage::model()->get_name($val);
            $data[$val]['order_money']=array_key_exists($val,$order_indep_money)?$order_indep_money[$val]['order_money']:0;
            $data[$val]['order_money']+=array_key_exists($val,$order_norm_money)?$order_norm_money[$val]['order_money']:0;
        }

        return $data;
    }
    /**
     * 获取商品类型投入金额
     * author: fang
     */
    public function getPutInMoneyByGoods($condition){
        $sql = "select SUM(money) AS money,cat_id from stat_cost_detail as a 
                LEFT JOIN goods as b on a.goods_id=b.id 
                where  ".$condition."
                group by cat_id;";
        $putin_stat_money = Yii::app()->db->createCommand($sql)->queryAll();
        $keyArr1=array_column($putin_stat_money,'cat_id');
        $putin_stat_money = array_combine($keyArr1,$putin_stat_money);

        $sql = "select SUM(fixed_cost) AS money,cat_id from fixed_cost_new as a 
                LEFT JOIN goods as b on a.goods_id=b.id 
                where  ".$condition."
                group by cat_id;";
        $putin_fixed_money = Yii::app()->db->createCommand($sql)->queryAll();
        $keyArr2=array_column($putin_fixed_money,'cat_id');
        $putin_fixed_money = array_combine($keyArr1,$putin_fixed_money);

        $keyArr = array_unique(array_merge($keyArr1,$keyArr2));

        $data =array();
        foreach ($keyArr as $val){
            $data[$val]['cat_name'] = Linkage::model()->get_name($val);
            $data[$val]['putin_money'] = array_key_exists($val,$putin_stat_money)?$putin_stat_money[$val]['money']:0;
            $data[$val]['putin_money'] += array_key_exists($val,$putin_fixed_money)?$putin_fixed_money[$val]['money']:0;
        }
        return $data;
    }
    /**
     * 获取商品类型发货金额
     * author: fang
     */
    public function getDeliveryMoneyByGoods($condition){
        $sql = "select SUM(delivery_money) as delivery_money,cat_id from delivery_indep_order_manage as a 
                LEFT JOIN goods as b on a.goods_id=b.id 
                where  ".$condition."
                group by cat_id;";
        $putin_indep_money = Yii::app()->db->createCommand($sql)->queryAll();
        $sql = "select SUM(delivery_money) as delivery_money,cat_id from delivery_norm_order_manage as a 
                LEFT JOIN goods as b on a.goods_id=b.id 
                where  ".$condition."
                group by cat_id;";
        $putin_norm_money = Yii::app()->db->createCommand($sql)->queryAll();
        $data =array();
        $data['delivery_money'] = $putin_indep_money?$putin_indep_money[0]['delivery_money']:0;
        $data['delivery_money'] += $putin_norm_money?$putin_norm_money[0]['delivery_money']:0;
        return $data;
    }
    /**
     * 获取商品类型发货金额
     * author: fang
     */
    public function getDeliveryMoneyByPromGroup($condition){
        $sql = "select SUM(delivery_money) as delivery_money from delivery_indep_order_manage as a 
                LEFT JOIN promotion_staff_manage AS b ON a.tg_uid = b.user_id
                where  ".$condition."
                group by promotion_group_id;";
        $putin_indep_money = Yii::app()->db->createCommand($sql)->queryAll();
        $sql = "select SUM(delivery_money) as delivery_money from delivery_norm_order_manage as a 
                LEFT JOIN promotion_staff_manage AS b ON a.tg_uid = b.user_id
                where  ".$condition."
                group by promotion_group_id;";
        $putin_norm_money = Yii::app()->db->createCommand($sql)->queryAll();
        $data =array();
        $data['delivery_money'] = $putin_indep_money?$putin_indep_money[0]['delivery_money']:0;
        $data['delivery_money'] += $putin_norm_money?$putin_norm_money[0]['delivery_money']:0;
        return $data;
    }
    /**
     * 获取商品类型投入金额
     * author: fang
     */
    public function getPutinMoneyByPromGroup($condition){
        $sql = "select SUM(money) AS money from stat_cost_detail as a 
                LEFT JOIN promotion_staff_manage AS b ON a.tg_uid = b.user_id
                where  ".$condition."
                group by promotion_group_id;";
        $putin_indep_money = Yii::app()->db->createCommand($sql)->queryAll();
        $sql = "select SUM(fixed_cost) AS money from fixed_cost_new as a 
                LEFT JOIN promotion_staff_manage AS b ON a.tg_uid = b.user_id
                where  ".$condition."
                group by promotion_group_id;";
        $putin_norm_money = Yii::app()->db->createCommand($sql)->queryAll();
        $data =array();
        $data['putin_money'] = $putin_indep_money?$putin_indep_money[0]['money']:0;
        $data['putin_money'] += $putin_norm_money?$putin_norm_money[0]['money']:0;
        return $data;
    }
    /**
     * 获取整体数据
     * author: fang
     */
    public function getAllMoney($stat_date,$end_date){
        $sql = " SELECT SUM(delivery_money) as delivery_money from delivery_indep_order_manage where 
                     delivery_date between '{$stat_date}' and '{$end_date}'";
        $delivery_indep_money = Yii::app()->db->createCommand($sql)->queryAll();
        //的订单发货列表
        $sql = " SELECT SUM(delivery_money) as delivery_money from delivery_norm_order_manage where 
                     delivery_date between '{$stat_date}' and '{$end_date}'";
        $delivery_norm_money = Yii::app()->db->createCommand($sql)->queryAll();
        //获取成本明细列表
        $sql = " SELECT SUM(money) as money from stat_cost_detail where  
                     stat_date between '{$stat_date}' and '{$end_date}'";
        $input_cost_money = Yii::app()->db->createCommand($sql)->queryAll();
        //获取修正成本列表
        $sql = " SELECT SUM(fixed_cost) as fixed_cost from fixed_cost_new where  
                      stat_date between '{$stat_date}' and '{$end_date}'";
        $input_fixed_money = Yii::app()->db->createCommand($sql)->queryAll();
        $data =array();
        $data['delivery_money'] = $delivery_indep_money[0]['delivery_money'] + $delivery_norm_money[0]['delivery_money'];
        $data['putin_money'] += $input_cost_money[0]['money'] + $input_fixed_money[0]['fixed_cost'];
        return $data;

    }

    /**
     * 获取id对应的商品名集合
     * @param $ids
     * @return array
     * author: yjh
     */
    public function getGoodsNames($ids){
        $sql="select id,goods_name from goods where id in($ids)";
        $result = Yii::app()->db->createCommand($sql)->queryAll();
        $ret = array();
        foreach ($result as $item) {
            $ret[$item['id']]=$item['goods_name'];
        }
        return $ret;
    }

}