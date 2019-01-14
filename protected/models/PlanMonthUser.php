<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/20
 * Time: 15:05
 */

class PlanMonthUser extends CActiveRecord
{
    public function tableName()
    {
        return '{{plan_month_user}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
     //获取plan_month相关联的数据
    public function getUserData($id){
        $user_data = $this->getTgName($id);
        $sql = "select * from plan_month where tg_uid=".$user_data['tg_uid'].' and month='.$user_data['month'];
        $data['listdata']['list'] = Yii::app()->db->createCommand($sql)->queryAll();
        $data['listdata']['num'] = count($data['listdata']['list']);
        $data['listdata']['name'] = $user_data['csname_true'];
        $data['listdata']['month'] = $user_data['month'];
        $data['listdata']['remark'] = $user_data['remark'];
        $data['listdata']['id'] = $user_data['id'];
        $data['listdata']['status'] = $user_data['status'];
        $data['listdata']['weChat_num'] = $user_data['weChat_num'];

        return $data;
    }

    //获取推广人员的真实名字
    public function getTgName($id = ''){
        if($id == ''){
            $sql = "select a.*,b.csname_true from plan_month_user as a left join cservice as b on b.csno=a.tg_uid ";
            $data = Yii::app()->db->createCommand($sql)->queryAll();
        }else{
            $sql = "select a.*,b.csname_true from plan_month_user as a left join cservice as b on b.csno=a.tg_uid where id=".$id;
            $list = Yii::app()->db->createCommand($sql)->queryAll();
            $data = $list[0];
        }

        return $data;
    }

    //获取当前推广人员所属的组
    public function getUserGroup($uid = ''){
        $group_id = AdminGroup::model()->find('sno='.$uid);
        return $group_id;
    }

    //判断是否要补写当前月份计划 0:需要 1.不需要
    public function getSupplement(){
        $tg_id = Yii::app()->admin_user->uid;
        $sql = "select * from plan_month_user where tg_uid=".$tg_id;
        $data = Yii::app()->db->createCommand($sql)->queryAll();
        if($data){
            $timestrap = date("Y-m", strtotime("now"));
            foreach ($data as $v){
                if($timestrap == date('Y-m',$v['month'])){
                    $result = 1;
                    break;
                }else{
                    $result = 0;
                }
            }
        }

        return $result;
    }

}