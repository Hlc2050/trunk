<?php

/**
 * 推广测试人员关联
 * User: hlc
 * Date: 2018/11/14
 * Time: 10:09
 */
class PromotionAuController extends AdminController
{
    public function actionIndex()
    {
        $promotionStaffArr = $this->getPromotionStaff();
        $this->render('index',array('promotionStaff'=>$promotionStaffArr));
    }

    /**
     * 支持人员权限添加
     */
    public function actionAdd(){
        $arr  = explode('/,',$_GET['data']);
        $test_staff_id = array();
        foreach ($arr as $value) {
            $arr1 = explode(':', $value);
            $arr2 = explode(':', $arr1[1]);
            $arr3 = explode(',', $arr2[0]);
            $num = count($arr3);
            $result = PromotionStaffRelation::model()->findAll('promotion_staff_id=' . $arr1[0]);

            if (empty($result)) {
                for ($i = 0; $i < $num; $i++) {
                    if ($arr1[1] != null) {
                        $test_staff_id[] = array($arr1[0], $arr3[$i]);
                    }
                }
            } else {
                PromotionStaffRelation::model()->deleteAll('promotion_staff_id=' . $arr1[0]);
                for ($i = 0; $i < $num; $i++) {
                    if ($arr1[1] != null) {
                        $test_staff_id[] = array($arr1[0], $arr3[$i]);
                    }
                }
            }
        }

        $row = array('promotion_staff_id','test_staff_id');
        $result = helper::batch_insert_data('promotion_staff_relation',$row,$test_staff_id);
        if($result) $this->msg(array('state'=>1,'msgwords'=>'添加成功'));
    }

    /**
     * ajax获取select2数据
     */
    public function actionGetData(){
        $result = PromotionStaffRelation::model()->findAll();
        $promotionStaffArr = $this->getPromotionStaff();
        $temp = array();
        foreach ($result as $key=>$value){
            $temp[$value['promotion_staff_id']][] = array(
                'id' => $value['test_staff_id'],
                'text' => $promotionStaffArr[$value['test_staff_id']],
        );
        }

        echo json_encode($temp);
    }

    /**
     * 获取推广人员数据
     */
    function getPromotionStaff(){
        $promotionStaffArr = PromotionStaff::model()->getPromotionStaffList(1);
        $promotionStaffArr = $this->changeType($promotionStaffArr,'user_id','name');
        return $promotionStaffArr;
    }

    /**
     * 修改数据格式 $str1 =$key $str2=$value
     */
    function changeType($array = array(),$str1 = '',$str2 = ''){
        $temp = array();
        foreach ($array as $value){
            $temp[$value[$str1]] = $value[$str2];
        }
        return $temp;
    }
}