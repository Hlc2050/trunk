<?php
/**
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/7
 * Time: 11:10
 */
class OrdersSortByPkg extends CActiveRecord{

    public function tableName() {
        return '{{orders_sort_by_pkg}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }


    /**
     * 按日期统计商品进线量、发货量
     */
    public function getTotalGroupByDate($condition)
    {
        $sql = ' select SUM(out_count) as out_count,SUM(in_count) as in_count,stat_date  from orders_sort_by_pkg where 1 '.$condition.' group by stat_date order by package_id,stat_date asc';
        $package_info = Yii::app()->db->createCommand($sql)->queryAll();
        return $package_info;
    }

    /**
     * 获取客服部所有下单商品的数据
     * author: yjh
     */
    public function getPkgInfoByCsId($condition){
        $sql=' select SUM(out_count) as out_count,SUM(in_count) as in_count,stat_date,package_id,b.name as package_name,customer_service_id
               from orders_sort_by_pkg as a 
               left join package_manage as b on b.id = a.package_id where '.$condition.' group by package_id';
        $package_info = Yii::app()->db->createCommand($sql)->queryAll();
        return $package_info;
    }


    /**
     * 获取下单商品的所有客服部数据
     * author: yjh
     */
    public function getCsInfoByPkgId($condition){
        $sql=' select SUM(out_count) as out_count,SUM(in_count) as in_count,stat_date,package_id,customer_service_id,b.cname
               from orders_sort_by_pkg as a 
               left join customer_service_manage as b on b.id=a.customer_service_id
               where '.$condition.'  group by customer_service_id';
        $package_info = Yii::app()->db->createCommand($sql)->queryAll();
        return $package_info;
    }

    /**
     *  统计商品总进线量、发货量、发货金额
     * @param $join string
     * @param $condition string
     */
    public function getPackageTotal($join = '',$condition = '')
    {
        $sql = " select SUM(a.delivery_money) as delivery_money ,SUM(a.out_count) as out_count,SUM(a.in_count) as in_count from orders_sort_by_pkg as a " .$join. " where 1" .$condition;
        $total = Yii::app()->db->createCommand($sql)->queryAll();
        return $total;
    }

}