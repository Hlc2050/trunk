<?php
/**
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/7
 * Time: 11:10
 */
class PackageManage extends CActiveRecord{
    public function tableName() {
        return '{{package_manage}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    /**
     * 获取ids对应套餐名集合
     * @param $ids
     * @return array
     * author: yjh
     */
    public function getPackageNames($ids){
        $sql="select id,name from package_manage where id in($ids)";
        $result = Yii::app()->db->createCommand($sql)->queryAll();
        $ret = array();
        foreach ($result as $item) {
            $ret[$item['id']]=$item['name'];
        }
        return $ret;
    }

    /**
     * 获取商品
     * @return array
     * author: yjh
     */
    public function getPackageNameList(){
        $ret=Dtable::toArr($this->findAll());
        $ret=array_column($ret,'name');
        return $ret;
    }
    
}