<?php
//支持人员权限表
/**
 * Created by PhpStorm.
 * User: hlc
 * Date: 2018/11/14
 * Time: 10:09
 */

class SupporterAuController extends AdminController
{

    public function actionIndex()
    {
        $supportStafflist = SupportStaff::model()->getSupportStaffList();
        $supportStafflist = $this->changeType($supportStafflist,'user_id','name');
        $promotionStaffArr = $this->getPromotionStaff();
        $this->render('index',array('supportStaff'=>$supportStafflist,'promotionStaff'=>$promotionStaffArr));
    }

    /**
     * 支持人员权限添加
     */

    public function actionAdd(){
        $arr  = explode('/,',$_GET['data']);
        $promotion_user_id = array();
        foreach ($arr as $value){
                $arr1 = explode(':',$value);
                $arr2 = explode(':',$arr1[1]);
                $arr3 = explode(',',$arr2[0]);
                $num=count($arr3);
                $result = PromotionUserRelation::model()->findAll('user_id='.$arr1[0]);
                if(empty($result)){
                    for ($i=0;$i<$num;$i++){
                        if($arr1[1] != null){
                        $promotion_user_id[] = array($arr1[0],$arr3[$i]);
                        }
                    }
                }else{
                    PromotionUserRelation::model()->deleteAll('user_id='.$arr1[0]);
                    for ($i=0;$i<$num;$i++){
                        if($arr1[1] != null){
                            $promotion_user_id[] = array($arr1[0],$arr3[$i]);
                        }
                }
            }
        }
        $row = array('user_id','promotion_user_id');
        $result = helper::batch_insert_data('promotion_user_relation',$row,$promotion_user_id);
        if($result)  $this->msg(array('state'=>1,'msgwords'=>'添加成功'));

    }

    /**
     * ajax获取select2数据
     */

    public function actionGetData(){
        $result = PromotionUserRelation::model()->findAll('user_id='.$_POST['supportStaff']);
        $promotionStaffArr = $this->getPromotionStaff();
        $temp = array();
        foreach ($result as $key=>$value){
            $temp[$key]['id'] = $value['promotion_user_id'];
            $temp[$key]['text'] = $promotionStaffArr[$value['promotion_user_id']];
        }
          echo json_encode($temp);
    }

    //获取推广人员数据
    function getPromotionStaff(){
        $promotionStaffArr = PromotionStaff::model()->getPromotionStaffList(1);
        $promotionStaffArr = $this->changeType($promotionStaffArr,'user_id','name');
        return $promotionStaffArr;
    }

    //修改数据格式 $str1 =$key $str2=$value
    function changeType($array = array(),$str1 = '',$str2 = ''){
        $temp = array();
        foreach ($array as $value){
            $temp[$value[$str1]] = $value[$str2];
        }
        return $temp;
    }
}