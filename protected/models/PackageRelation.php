<?php
/**
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/7
 * Time: 11:10
 */
class PackageRelation extends CActiveRecord{
    public function tableName() {
        return '{{package_relation}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    /**
     *  根据分组获取下单商品
     *
     */
    public function getPackageList($group_id)
    {
        $packageList = array();

        $sql= 'select a.*,b.name from package_relation as a left join package_manage as b on b.id=a.package_id where a.package_group_id='.$group_id;
        $res = Yii::app()->db->createCommand($sql)->queryAll();

        foreach ($res as $k=>$value) {
            $packageList[$k]['id'] = $value['package_id'];
            $packageList[$k]['name'] = $value['name'];
        }

        return $packageList;
    }







    
}