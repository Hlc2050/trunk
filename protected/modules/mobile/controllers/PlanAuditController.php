<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/28
 * Time: 17:00
 */

class PlanAuditController extends AdminController
{
    public function actionIndex()
    {
        $query = $this->checkPermission();
        $unAuditPlan = array();
        $type = array(
            'plan_week_user' =>1,
            'plan_week_group' =>2,
            'plan_month_user' =>3,
            'plan_month_group_user' =>4,
        );
        $clum = array(
            'plan_week_user' =>",tg_uid as relation_id,start_date as plan_time",
            'plan_week_group' =>",group_id as relation_id,start_date as plan_time ",
            'plan_month_user' =>",tg_uid as relation_id,month as plan_time ",
            'plan_month_group_user' =>",groupid as relation_id,month as plan_time ",
        );
        foreach ($query['table'] as $value) {
            $sql = "SELECT id,update_time,through_time".$clum[$value]."  FROM ".$value.$query['where'][$value];
            $plan = Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($plan as $p) {
                $p['type'] = $type[$value];
                $unAuditPlan[] = $p;
            }
        }
        $page['list'] = $unAuditPlan;
        $tg_group = Dtable::toArr(AdminGroup::model()->findAll());
        $page['groups'] = array_combine(array_column($tg_group, 'groupid'), array_column($tg_group, 'groupname'));
        $users = Dtable::toArr(AdminUser::model()->findAll());
        $page['users'] = array_combine(array_column($users, 'csno'), array_column($users, 'csname_true'));
        $this->render('index', array('page' => $page));
    }

    private function checkPermission()
    {
        //判断审核人员
        $user_id = Yii::app()->mobile->uid;
        $group_id = AdminUser::model()->get_manager_group($user_id);
        //是否需要审核人员审核
        $is_audit = intval(Yii::app()->params['remind']['Individual_plan_auditor']);
        //审核人员配置
        $audit_user = intval(Yii::app()->params['remind']['group_plan_auditor']);
        //组长权限
        $permission = 1;
        //审核人员可审核组权限
        $permission = ($is_audit==0 && $audit_user==$user_id) ? 2:$permission;
        //审核人员可审核组+个人权限
        $permission = ($is_audit && $audit_user==$user_id) ? 3:$permission;
        $where['plan_month_user'] = $where['plan_week_user']= $where['plan_month_group_user']= $where['plan_week_group'] = ' where 1 ';
        $table = array();
        $group_user = array();
        if ($group_id) {
            $user_group = Dtable::toArr(AdminUserGroup::model()->findAll('groupid in ('.implode(',',$group_id).')'));
            $group_user = array_unique(array_column($user_group,'sno'));
        }
        switch ($permission) {
            case 1:
                if ($group_user) {
                    $table[] = 'plan_month_user';
                    $table[] = 'plan_week_user';
                    $where['plan_month_user'] .= ' and ( status=0 and tg_uid in ('.implode(',',$group_user).') )';
                    $where['plan_week_user'] .= ' and ( status=0 and tg_uid in ('.implode(',',$group_user).') )';
                }
                break;
            case 2:
                if ($group_user) {
                    $table[] = 'plan_month_user';
                    $table[] = 'plan_week_user';
                    $where['plan_month_user'] .= ' and ( status=0 and tg_uid in ('.implode(',',$group_user).') ) and status=0';
                    $where['plan_week_user'] .= ' and ( status=0 and tg_uid in ('.implode(',',$group_user).') ) and status=0';
                }
                $table[] = 'plan_month_group_user';
                $table[] = 'plan_week_group';
                break;
            case 3:
                $table[] = 'plan_month_user';
                $table[] = 'plan_week_user';
                $table[] = 'plan_month_group_user';
                $table[] = 'plan_week_group';
                if ($group_user) {
                    $where['plan_month_user'] .= ' and (( status=0 and tg_uid in ('.implode(',',$group_user).') ) or ( status=2) )';
                    $where['plan_week_user'] .= ' and (( status=0 and tg_uid in ('.implode(',',$group_user).') ) or ( status=11) )';
                } else {
                    $where['plan_month_user'] .= ' and status=2 ';
                    $where['plan_week_user'] .= ' and status=11 ';
                }
        }
        $where['plan_month_group_user'] .= ' and  status=2 ';
        $where['plan_week_group'] .= ' and status=0 ';

        return array('table'=>$table,'where'=>$where);
    }


    public function actionAudit()
    {
        $type = intval($_REQUEST['type']) ;
        $id = intval($_REQUEST['id']);

        if (!$_POST) {
            $service = Dtable::toArr(CustomerServiceManage::model()->findAll());
            $page['service_group'] = array_combine(array_column($service, 'id'), array_column($service, 'cname'));
            $page['title'] = $_GET['title'];
            $detail = array();
            switch ($type) {
                case 1:
                    $model = PlanWeekUser::model()->findByPk($id);
                    $detail = Dtable::toArr(PlanWeekUserDetail::model()->findAll('week_id='.$id.' order by date asc'));
                    break;
                case 2:
                    $model = PlanWeekGroup::model()->findByPk($id);
                    $detail = Dtable::toArr(PlanWeekGroupDetail::model()->findAll('week_id='.$id.' order by date asc'));
                    break;
                case 3:
                    $model = PlanMonthUser::model()->findByPk($id);
                    $tg_uid = $model->tg_uid;
                    $time = $model->month;
                    $page['detail'] = Dtable::toArr(PlanMonth::model()->findAll('tg_uid='.$tg_uid.' and month='.$time));
                    break;
                case 4:
                    $model = PlanMonthGroupUser::model()->findByPk($id);
                    $group_id = $model->groupid;
                    $time = $model->month;
                    $page['detail'] = Dtable::toArr(PlanMonthGroup::model()->findAll('groupid='.$group_id.' and month='.$time));
                    break;
                default:
                    break;
            }
            $page['week_plan'] = Dtable::toArr($model);
            if ($type == 1 || $type == 2) {
                $page['start_date'] = $model->start_date;
                foreach ($detail as $key=>$value) {
                    $page['detail'][$value['service_group']][$value['date']] = $value;
                }
            }
            $page['id'] = $id;
            $page['type'] = $type;
            $this->render('audit', array('page' => $page));
        }
    }


    public function actionAuditEdit()
    {
        //判断审核人员
        $user_id = Yii::app()->mobile->uid;
        //是否需要审核人员审核
        $is_audit = intval(Yii::app()->params['remind']['Individual_plan_auditor']);
        //审核人员配置
        $audit_user = intval(Yii::app()->params['remind']['group_plan_auditor']);
        $type = $_GET['type'];
        $status = $_GET['status'];
        $id = $_GET['id'];
        if ($status!=1 && $status!=2) {
            die(json_encode(array('state' => 0, 'msgwords' => '审核状态有误！')));
        }
        $unthrough_msg = trim($this->get('unthrough_msg'));
        if (!$id) {
            die(json_encode(array('state' => 0, 'msgwords' => '未选择计划！')));
        }
        if (!$unthrough_msg && $status==2) {
            die(json_encode(array('state' => 0, 'msgwords' => '请输入拒绝理由！')));
        }
        $table = array(
           1=> 'plan_week_user',
           2=> 'plan_week_group',
            3=>'plan_month_user',
            4=>'plan_month_group_user',
        );
        $sql = 'select * from '.$table[$type].' where id= '.$id;
        $plan = Yii::app()->db->createCommand($sql)->queryAll();
        $plan = $plan[0];
        $old_through_time = $plan['through_time'];
        if (!$plan) {
            die(json_encode(array('state' => 0, 'msgwords' => '计划不存在！')));
        }
        //判断审核人员
        $group_id = AdminUser::model()->get_manager_group($user_id);
        //是否需要审核人员审核
        $is_audit = intval(Yii::app()->params['remind']['Individual_plan_auditor']);
        //审核人员配置
        $audit_user = intval(Yii::app()->params['remind']['group_plan_auditor']);
        $all_through =0;
        if ($type == 1 || $type== 3) {
            if ($is_audit == 1) {
                if (!$group_id && $user_id!=$audit_user) {
                    die(json_encode(array('state' => 0, 'msgwords' => '您没有审核权限！')));
                }
                if ($user_id!=$audit_user) {
                    $all_through = 0;
                }else {
                    $all_through = 1;
                }
            }else {
                if (!$group_id) {
                    die(json_encode(array('state' => 0, 'msgwords' => '您没有审核权限！')));
                }
                $all_through = 1;
            }
        }
        if ($type == 2 || $type == 4) {
            if ($user_id!=$audit_user) {
                die(json_encode(array('state' => 0, 'msgwords' => '您没有审核权限！')));
            }
            $all_through = 1;
        }
        $new_status = $this->getAuditStatus($type,$status,$all_through);
        $update_data['status'] = $new_status;
        if ($status == 2) {
            $update_data['unthrough_msg'] = $unthrough_msg;
        }
        if ($all_through== 1 && $status == 1) {
            $update_data['through_time'] = time();
        }
        $res = Yii::app()->db->createCommand()->update($table[$type], $update_data, 'id=:id', array(':id'=>$id));
        $mask = $status==1?'审批通过':'审批未通过';
        if ($type == 1 || $type==2) {
            $time = $plan['start_date'];
            $relation_id = $plan['tg_uid'] ? $plan['tg_uid']:$plan['group_id'];
            PlanWeekLog::model()->addPlanWeekLog($id,$type,$relation_id,$mask,$time,1);
        }
        if ($type == 3 || $type==4) {
            $time = $plan['month'];
            $relation_id = $plan['tg_uid'] ? $plan['tg_uid']:$plan['groupid'];
            PlanMonthLog::model()->addPlanMonthLog($id,$type-2,$relation_id,$mask,$time,1);
        }
        //发送消息
        $name = '';
        $send_user = 0;
        if ($type == 1 || $type == 3) {
            $send_user = $relation_id;
            $info = AdminUser::model()->findByPk($relation_id);
            $name = $info->csname_true;
        }
        if ($type == 2 || $type == 4) {
            $info = AdminGroup::model()->findByPk($relation_id);
            $send_user = $info->manager_id;
            $name = $info->groupname;
        }
        $add_mask ='';
        if ($old_through_time < $plan->update_time && $old_through_time>0) {
            $add_mask = '(变更)';
        }
        $send = new SendMessage();
        if ($all_through == 1 || $status == 2) {
            $send->sendPlanMsg($send_user,$time,$id,$name,$type,2,$add_mask.','.$mask);
        }
        if ($all_through == 0 && $status == 1) {
            $send->sendPlanMsg($audit_user,$time,$id,$name,$type,1,$add_mask.',需要您审批');
        }
        if ($type == 1) {
            PlanWeekUserDetail::model()->updateUserPlanStatus($id,$new_status);
        }
        if ($type == 2) {
            PlanWeekGroupDetail::model()->updateGroupPlanStatus($id,$new_status);
        }
        if ($type == 3) {
            PlanMonth::model()->updateMonthStatus($plan['tg_uid'],$plan['month'],$new_status);
        }

        if ($type == 4) {
            PlanMonthGroup::model()->updateMonthGroupStatus($plan['groupid'],$plan['month'],$new_status);
        }

        die(json_encode(array('state' => 1, 'msgwords' => '审核状态修改成功！')));

    }

    private function getAuditStatus($type,$status,$all_through=0)
    {
        $new_status = 0;
        switch ($type) {
            case 1:
                if ($status == 1) {
                    $new_status = $all_through==1 ?12:11;
                }
                if ($status == 2) {
                    $new_status = $all_through==1 ?22:21;
                }
                break;
            case 2:
                $new_status = $status;
                break;
            case 3:
                if ($status == 1) {
                    $new_status = ($all_through==1) ?4:2;
                }
                if ($status == 2) {
                    $new_status = ($all_through==1) ?3:1;
                }
                break;
            case 4:
                $new_status = ($status == 1) ?4:3;
                break;
        }
        return $new_status;

    }

}