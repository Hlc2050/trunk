<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/22
 * Time: 18:32
 */

class PlanMonthGroupUser extends CActiveRecord
{
    public function tableName()
    {
        return '{{plan_month_group_user}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    //获取plan_month_group相关联的数据
    public function getUserData($id){
        $user_data = $this->getTgName($id);
        $sql = "select * from plan_month_group where groupid=".$user_data['groupid'].' and month='.$user_data['month'];
        $data['listdata']['list'] = Yii::app()->db->createCommand($sql)->queryAll();
        $data['listdata']['num'] = count($data['listdata']['list']);
        $data['listdata']['name'] = $user_data['groupname'];
        $data['listdata']['month'] = $user_data['month'];
        $data['listdata']['remark'] = $user_data['remark'];
        $data['listdata']['id'] = $user_data['id'];
        $data['listdata']['status'] = $user_data['status'];
        $data['listdata']['groupid'] = $user_data['groupid'];

        return $data;
    }

    //获取推广人员的真实名字
    public function getTgName($id = ''){
        if($id == ''){
            $sql = "select a.*,b.groupname from plan_month_group_user as a left join cservice_group as b on b.groupid=a.groupid";
            $data = Yii::app()->db->createCommand($sql)->queryAll();
        }else{
            $sql = "select a.*,b.groupname from plan_month_group_user as a left join cservice_group as b on b.groupid=a.groupid where id=".$id;
            $list = Yii::app()->db->createCommand($sql)->queryAll();
            $data = $list[0];
        }

        return $data;
    }

    //判断是否要补写当前月份计划 0:需要 1.不需要
    public function getSupplement(){
        $tg_id = Yii::app()->admin_user->uid;
        $group_ids = AdminUser::model()->get_manager_group($tg_id);
        $str = '';
        foreach ($group_ids as $val){
            $str .= $val.',';
        }
        $str = rtrim($str,',');

        $sql = "select * from plan_month_group_user where groupid in (".$str.")";
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
        }else {
            $result = 0;
        }

        return $result;
    }
}