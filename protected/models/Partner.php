<?php
/**
 * 合作商表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/2
 * Time: 14:18
 */
class Partner extends CActiveRecord{
    public function tableName() {
        return '{{partner}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    
    public function getIdByName($name){
        $m = $this->find("name='$name'");
        return $m->id;
    }
    public function getNameById($id){
        $m = $this->findByPk($id);
        return $m->name;
    }

    public function getPartnerNames($ids){
        $sql="select id,name from partner where id in($ids)";
        $result = Yii::app()->db->createCommand($sql)->queryAll();
        $ret = array();
        foreach ($result as $item) {
            $ret[$item['id']]=$item['name'];
        }
        return $ret;
    }
}