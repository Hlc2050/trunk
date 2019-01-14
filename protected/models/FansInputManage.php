<?php
/**
 * 粉丝录入管理
 * Created by PhpStorm.
 * User: yjh
 * Date: 2017/01/05
 * Time: 14:18
 */
class FansInputManage extends CActiveRecord{
    public function tableName() {
        return '{{fans_input_manage}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    /**
     * 类型二
     * 获取进粉量（通过微信时间分组）
     * @param $condition
     * @return mixed
     * author: yjh
     */
    public function getFansTypeTwo($condition){
        $sql = 'SELECT weixin_id,addfan_date,wechat_id,SUM(addfan_count) as fans_count 
                FROM fans_input_manage as a 
                WHERE ' . $condition;
        $info = Yii::app()->db->createCommand($sql)->queryAll();
        return $info;
    }

    /**
     * 类型三
     * 获取进粉量
     * @param $condition
     * @return mixed
     * author: yjh
     */
    public function getFansTypeThree($condition){
        $sql = 'SELECT  addfan_date,weixin_id,SUM(addfan_count) as fans_count 
                FROM fans_input_manage as a 
                WHERE ' . $condition;
        $info = Yii::app()->db->createCommand($sql)->queryAll();
        return $info;
    }
    /**
     * 统计每日进粉量
     * @param $condition,$order
     * @return mixed
     */
    public function getDayTotalFans($condition,$order='') {
        $sql = 'SELECT  addfan_date,SUM(addfan_count) as day_fans
                FROM fans_input_manage as a 
                WHERE ' . $condition .
                ' GROUP BY addfan_date '.$order;
        $info = Yii::app()->db->createCommand($sql)->queryAll();
        return $info;
    }

    /*
     * 获取总数
     */
     public function getTotal($name,$condition='',$join =''){
         $params['where'] = '';
         $sql = "select sum(a.$name) as $name from fans_input_manage as a " . $join . " where 1 " . $condition;
         $totalInfo = Yii::app()->db->createCommand($sql)->queryAll();

         $ret = empty($totalInfo) ? 0 : $totalInfo[0][$name];
         return $ret;
     }

}