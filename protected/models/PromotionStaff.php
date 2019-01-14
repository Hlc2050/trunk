<?php
/**
 * 推广人员表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/2
 * Time: 14:18
 */

class PromotionStaff extends CActiveRecord{
    public function tableName() {
        return '{{promotion_staff_manage}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    /**
     * 获取推广人员列表
     * @param $type int
     * @param $support int 支持人员是否可查看关联数据
     * @return array|mixed|null
     * author: yjh
     */
    public function getPromotionStaffList($type=0,$support=0)
    {
        if ($type == 1) {
            $data = $this->findAll();
        } else {
            //查看人员权限
            $adminController = new AdminController(0);
            $result = $adminController->data_authority($support);
            if ($result != 0) {
                $result = rtrim($result,',');
                $data = $this->findAll(" user_id in ($result)");
            } else  $data = $this->findAll();
        }
        return $data;
    }
    /**
     * 获取推广人员列表
     * @return array|mixed|null
     * author: finn
     */
    public function getListUser(){
        $sql="select * from promotion_staff_manage as a left join cservice as b on b.csno=a.user_id";
        $a=Yii::app()->db->createCommand($sql)->queryAll();
        return $a;
    }


    /**
     * 通过组别id获取推广人员
     * @param $promotion_group_id
     * @return array|string
     * author: yjh
     */
    public function getPromotionStaffByPg($promotion_group_id){
        $promotionStaffArr = array();
        if (!$promotion_group_id) $sql='';
        else $sql='promotion_group_id='.$promotion_group_id;
        $result = $this->findAll($sql);
        foreach ($result as $key => $val) {
            $promotionStaffArr[$key]['promotion_group_id'] = $val->promotion_group_id;
            $promotionStaffArr[$key]['user_id'] = $val->user_id;
            $sql="select csname_true from cservice where csno=".$val->user_id;
            $a=Yii::app()->db->createCommand($sql)->queryAll();
//            include_once  $_SERVER['DOCUMENT_ROOT']."/protected/modules/mobile/models/AdminUser.php";
//            $promotionStaffArr[$key]['user_name'] = AdminUser::model()->getUserNameByPK($val->user_id);
            $promotionStaffArr[$key]['user_name'] = $a[0]['csname_true'];
        }
        return $promotionStaffArr;
    }


    public function getTgNames($ids){
        $sql="select id,user_id,name from promotion_staff_manage where user_id in($ids)";
        $result = Yii::app()->db->createCommand($sql)->queryAll();
        $ret = array();
        foreach ($result as $item) {
            $ret[$item['user_id']]=$item['name'];
        }
        return $ret;
    }

    /**
     * 获取推广人员名称
     * @param $pk
     * @return mixed
     * author: hlc
     */
    public function getPName($id){
        $sql="select id,user_id,name from promotion_staff_manage where user_id =".$id;
        $result = Yii::app()->db->createCommand($sql)->queryAll();
        $data =  $result[0]['name'];

        return $data;
    }


    /**
     * 查找组长负责部门的推广人员
     * @param $manager_id int 用户id
     * @return array
     */
    public function getPromotionStaffByManager($manager_id)
    {
        $groups = AdminGroup::model()->getGroupId($manager_id);
        $promotion_staff = array();
        if ($groups) {
            $sql="select sno  from cservice_groups where groupid in($groups)";
            $user = Yii::app()->db->createCommand($sql)->queryAll();
            $user_ids = array_column($user,'sno');
            if ($user_ids) {
                $ids = implode(',',$user_ids);
                $sql="select id,user_id,name from promotion_staff_manage where user_id in ($ids)";
                $result = Yii::app()->db->createCommand($sql)->queryAll();
                foreach ($result as $item) {
                    $promotion_staff[]=array(
                        'user_id'=>$item['user_id'],
                        'name'=>$item['name'],
                    );
                }
            }
        }
        return $promotion_staff;

    }
    //获取所有推广人员
    public function getPromotionStaff(){
        $temp = array();
        $data =Dtable::toArr($this->findAll());
        foreach ($data as $value){
            $temp[$value['user_id']] = $value['name'];
        }
        return $temp;
    }


}