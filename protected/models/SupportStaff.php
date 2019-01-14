<?php
/**
 * 支持人员表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/2
 * Time: 14:18
 */
class SupportStaff extends CActiveRecord{
    public function tableName() {
        return '{{support_staff_manage}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    /**
     * 获取支持人员列表
     * @return array|mixed|null
     * author: yjh
     */
    public function getSupportStaffList(){
        $data = $this->findAll();
        return $data;
    }
    
    


}