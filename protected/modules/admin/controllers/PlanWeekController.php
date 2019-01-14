<?php
/**
 * Created by PhpStorm.
 * User: lxj
 * Date: 2018/8/16
 * Time: 10:47
 */

class PlanWeekController extends AdminController
{
    public function actionIndex()
    {
        $param_group = array();
        $page = array();
        $uid = Yii::app()->admin_user->uid;
        $group_id = AdminUser::model()->get_manager_group($uid);
        //登录用户角色:1推广人员 2.推广组长 3.审核人员 4.超级管理员
        $user_type = 0;
        $is_manege = 0;

        $is_audit_user = 0;
        //是否可添加个人计划
        $show_add_user_plan = 0;
        //是否可添加组计划
        $show_add_group_plan = 0;
        $is_tg = AdminUser::model()->isUserPromotionStaff($uid);
        if ($is_tg) {
            $user_type = 1;
            $show_add_user_plan = 1;
            $param_group[1] = array('value' => 1, 'txt' => '个人计划');
        }
        if ($group_id) {
            $show_add_user_plan = 1;
            $show_add_group_plan = 1;
            $user_type = 2;
            $param_group[0] = array('value'=>0,'txt'=>'待我审核的(个人)');
            $param_group[3] = array('value' => 3, 'txt' => '组计划');
            $param_group[1] = array('value' => 1, 'txt' => '个人计划');
            $is_manege = 1;
        }
        //是否需要审核人员审核
        $is_audit = intval(Yii::app()->params['remind']['Individual_plan_auditor']);
        //审核人员配置
        $audit_user = intval(Yii::app()->params['remind']['group_plan_auditor']);
        //审核人员个人计划审核权限
        if ($uid == $audit_user && $is_audit == 1) {
            $param_group[0] = array('value' => 0, 'txt' => '待我审核的(个人)');
            $param_group[1] = array('value' => 1, 'txt' => '个人计划');
        }
        //推广组审核权限
        if ($uid == $audit_user) {
            $user_type = 3;
            $is_audit_user = 1;
            $param_group[2] = array('value' => 2, 'txt' => '待我审核的(组)');
            $param_group[3] = array('value' => 3, 'txt' => '组计划');
        }
        //判断是否为超级管理员
        if($uid==Yii::app()->params['management']['super_admin_id'] ) $user_type =4;
        //判断权限是否为超级管理员权限
        $urole = AdminUser::model()->get_user_role($uid);
        foreach ($urole as $val){
            if($val['role_id'] == 1) $user_type =4;
        }
        if ($user_type == 4) {
            $param_group[1] = array('value' => 1, 'txt' => '个人计划');
            $param_group[3] = array('value' => 3, 'txt' => '组计划');
        }
        if ($param_group) {
            $param_group[4] = array('value' => 4, 'txt' => '提交记录');
        }
        ksort($param_group);
        $page['params_groups'] = $param_group;
        $first = array_shift($param_group);
        $page['tab_id'] = $this->get('tab_id') ? $this->get('tab_id') : $first['value'];
        $service_group = Dtable::toArr(CustomerServiceManage::model()->findAll('state_cooperation=0'));
        $page['service_group'] = array_combine(array_column($service_group, 'id'), array_column($service_group, 'cname'));
        $page['week_dates'] = helper::getWeekDate();
        $mgroup = Dtable::toArr(AdminGroup::model()->findAll());
        $page['manage_group'] = array_combine(array_column($mgroup, 'groupid'), array_column($mgroup, 'groupname'));
        if ($user_type == 2) {
            foreach ($page['manage_group'] as $key=>$value) {
                if (!in_array($key,$group_id)) {
                    unset($page['manage_group'][$key]);
                }
            }
        }
        switch ($page['tab_id']) {
            //待审核个人计划
            case 0:
                if ($uid == $audit_user && $is_audit == 1) {
                    $is_manege =0;
                }
                $page['info'] = $this->getUnauditUserPlan($is_manege,$group_id);
                $admin = Dtable::toArr(AdminUser::model()->findAll());
                $page['user_name'] = array_combine(array_column($admin, 'csno'), array_column($admin, 'csname_true'));
                break;
            //个人计划
            case 1:
                if (($uid == $audit_user && $is_audit == 1) || $user_type == 4) $is_manege=2;
                $page['info'] = $this->getUserPlan();
                $admin = Dtable::toArr(AdminUser::model()->findAll());
                $page['user_name'] = array_combine(array_column($admin, 'csno'), array_column($admin, 'csname_true'));
                $page['hide_add_curent'] = 0;
                $curent_week_plan = PlanWeekUser::model()->find(' tg_uid=' . $uid . ' and start_date=' . $page['week_dates'][0]);
                if ($curent_week_plan && !$group_id) {
                    $page['hide_add_curent'] = 1;
                }
                break;
            //待审核推广组计划
            case 2:
                $page['info'] = $this->getUnauditGroupPlan();
                break;
            //推广组计划
            case 3:
                $group = Dtable::toArr(AdminGroup::model()->findAll());
                $page['groups'] = array_combine(array_column($group, 'groupid'), array_column($group, 'groupname'));
                foreach ($group as $key=>$value) {
                    if ($value['manager_id'] !=$uid ){
                        unset($group[$key]);
                    }
                }
                if ($user_type == 4) $is_audit_user = 1;
                $manage_group = array_combine(array_column($group, 'groupid'), array_column($group, 'groupname'));
                $page['info'] = $this->getGroupPlan($manage_group);
                $page['hide_add_curent'] = 1;

                if ($manage_group) {
                    $curent_week_plan = PlanWeekGroup::model()->findAll(' start_date=' . $page['week_dates'][0].' and group_id in ('.implode(',',array_keys($manage_group)).')');
                    if (count($manage_group) > count($curent_week_plan)) {
                        $page['hide_add_curent'] = 0;
                    }
                }
                break;
            //提交记录
            case 4:
                $admin = Dtable::toArr(AdminUser::model()->findAll());
                $page['user_name'] = array_combine(array_column($admin, 'csno'), array_column($admin, 'csname_true'));
                if ($is_audit_user == 1) $is_manege=2;
                $page['info'] = $this->getPlanLog();
                break;
        }
        $page['user_type'] = $user_type;
        $page['show_add_user_plan'] = $show_add_user_plan;
        $page['show_add_group_plan'] = $show_add_group_plan;
        $this->render('index', array('page' => $page));

    }


    /**
     * 添加个人周计划
     * @author lxj
     */
    public function actionAddUserPlan()
    {
        $uid = Yii::app()->admin_user->uid;
        $type = $this->get('type') ? $this->get('type') : 1;
        $page['type'] = $type;
        $page['is_manage'] = 0;
        $group_id = AdminUser::model()->get_manager_group($uid);
        $admin = AdminUser::model()->findByPk($uid);
        $page['user_name'] = $admin->csname_true;
        if (!$_POST) {
            $add_week = $type == 1 ? 1 : 0;
            $page['week_dates'] = helper::getWeekDate($add_week);
            $start_date = $page['week_dates'][0];
            $page['cservice_group'] = CustomerServiceManage::model()->findAll('state_cooperation=0');
            if (!$group_id) {
                $week_log = PlanWeekUser::model()->find('tg_uid = ' . $uid . ' and start_date=' . $start_date);
                if ($week_log) {
                    $this->msg(array('state' => -2, 'msgwords' => '该周期已添加过计划！','url'=>$this->get('backurl')));
                }
                $curent_week_plan = PlanWeekUser::model()->find(' tg_uid=' . $uid . ' and start_date=' . $page['week_dates'][0] - 7 * 24 * 60 * 60);
                if ($curent_week_plan) { //当前登录用户不为推广组长
                    $page['hide_add_curent'] = 1;
                } else {
                    $page['hide_add_curent'] = 0;
                }

            } else { //当前登录用户为推广组长
                $page['is_manage'] = 1;
                $page['hide_add_curent'] = 0;
                $group_users = PromotionStaff::model()->getPromotionStaffByManager($uid);
                $user_ids = array_unique(array_column($group_users,'user_id'));
                $user_ids[] = $uid;
                $plan = Dtable::toArr(PlanWeekUser::model()->findAll('start_date='.$page['week_dates'][0].' and tg_uid in ('.implode(',',$user_ids).')'));
                $plan_user = array_unique(array_column($plan,'tg_uid'));
                $un_plan_user = array();
                foreach ($user_ids as $value) {
                    if (!in_array($value,$plan_user)) {
                        $un_plan_user[] = $value;
                    }
                }
                $un_plan_user = array_unique($un_plan_user);
                if ($un_plan_user) {
                    $page['plan_user'] = Dtable::toArr(AdminUser::model()->findAll('csno in ('.implode(',',$un_plan_user).')'));

                }
            }

            $this->render('addUserPlan', array('page' => $page));
            exit();
        }

        $is_manege = 0;
        $user_id = $uid;
        if ($group_id) {
            $is_manege = 1;
            $user_id = $this->get('tg_uid');
        }
        $detail = $this->planUserAddData(0,$is_manege);

        if ($detail) {
            //写入周排期详细数据
            $row = array('tg_uid', 'service_group', 'fans_count', 'output', 'date', 'week_id','status','weChat_num');
            $res = helper::batch_insert_data('plan_week_user_detail', $row, $detail);
            if ($res) {
                $logs = "添加了推广人员(".$user_id.")周计划";
                $this->logs($logs);
                $this->msg(array('state' => 1, 'msgwords' => '添加成功！','url'=>$this->get('backurl')));
            }
        } else {
            $this->msg(array('state' => 0, 'msgwords' => '数据为空！'));
        }


    }


    /**
     * 编辑个人周计划
     * @author lxj
     */
    public function actionEditUserPlan()
    {
        $uid = Yii::app()->admin_user->uid;
        $week_id = $this->get('week_id');
        $week_plan = PlanWeekUser::model()->findByPk($week_id);
        if (!$week_plan) {
            $this->msg(array('state' => 0, 'msgwords' => '该计划不存在！'));
        }
        $week_tg = $week_plan->tg_uid;
        if ($week_tg != $uid) {
            $this->msg(array('state' => 0, 'msgwords' => '没有权限修改其他推广人员的计划！'));
        }
        if (!$_POST) {
            $week_date = array();
            $start_date = $week_plan->start_date;
            for ($i = 0; $i < 7; $i++) {
                $week_date[$i] = $start_date + $i * 24 * 60 * 60;
            }
            $details = PlanWeekUserDetail::model()->findAll('week_id=' . $week_id . ' order by date asc');
            $detail_plan = array();
            foreach ($details as $value) {
                $detail_plan[$value['service_group']][$value['date']] = array(
                    'fans_count' => $value['fans_count'],
                    'output' => $value['output'],
                    'weChat_num' => $value['weChat_num'],
                );
            }
            $service = Dtable::toArr(CustomerServiceManage::model()->findAll('state_cooperation=0'));
            $service_group = array_combine(array_column($service, 'id'), array_column($service, 'cname'));
            $page['detail_plan'] = $detail_plan;
            $page['week_dates'] = $week_date;
            $page['service_group'] = $service_group;
            $page['week_plan'] = Dtable::toArr($week_plan);
            $admin = AdminUser::model()->findByPk($uid);
            $page['user_name'] = $admin->csname_true;
            $this->render('editUserPlan', array('page' => $page));
            exit();
        }
        $group_id = AdminUser::model()->get_manager_group($uid);
        $is_manage = 0;
        if ($group_id) {
            $is_manage =1;
        }
        $detail = $this->planUserAddData($week_id,$is_manage);
        if ($detail) {
            //写入周排期详细数据
            $row = array('tg_uid', 'service_group', 'fans_count', 'output', 'date', 'week_id','status','weChat_num');
            $res = helper::batch_insert_data('plan_week_user_detail', $row, $detail);
            if ($res) {
                $logs = "修改了个人周计划：" . $week_id;
                $this->logs($logs);
                $this->msg(array('state' => 1, 'msgwords' => '修改成功！','url'=>$this->get('backurl')));
            }
        } else {
            $this->msg(array('state' => 0, 'msgwords' => '数据为空！'));
        }

    }


    /**
     * 添加推广组周计划
     * @author lxj
     */
    public function actionAddGroupPlan()
    {
        $uid = Yii::app()->admin_user->uid;
        $manage_group = Dtable::toArr(AdminGroup::model()->findAll("manager_id=$uid"));
        $type = $this->get('type') ? $this->get('type') : 1;
        $page['type'] = $type;
        $group_id = $_POST['group_id'];
        if (!$_POST) {
            $add_week = $type == 1 ? 1 : 0;
            $page['week_dates'] = helper::getWeekDate($add_week);
            $curent_week_plan = PlanWeekUser::model()->find(' tg_uid=' . $uid . ' and start_date=' . $page['week_dates'][0] - 7 * 24 * 60 * 60);
            if ($curent_week_plan) {
                $page['hide_add_curent'] = 1;
            } else {
                $page['hide_add_curent'] = 0;
            }
            $page['cservice_group'] = CustomerServiceManage::model()->findAll('state_cooperation=0');
            $page['manage_group'] = array_combine(array_column($manage_group, 'groupid'), array_column($manage_group, 'groupname'));

            $this->render('addGroupPlan', array('page' => $page));
            exit();
        }

        $detail = $this->planGroupAddData();

        if ($detail) {
            //写入周排期详细数据
            $row = array('group_id', 'service_group','old_fans_plan', 'fans_count', 'old_output_plan','output','old_weChat_num','weChat_num', 'date', 'week_id');
            $res = helper::batch_insert_data('plan_week_group_detail', $row, $detail);
            if ($res) {
                $logs = "添加了推广组(".implode(',',$group_id).")周计划";
                $this->logs($logs);
                $this->msg(array('state' => 1, 'msgwords' => '添加成功！','url'=>$this->get('backurl')));
            }
        } else {
            $this->msg(array('state' => 0, 'msgwords' => '数据为空！'));
        }


    }


    /**
     * 编辑推广组周计划
     * @author lxj
     */
    public function actionEditGroupPlan()
    {
        $uid = Yii::app()->admin_user->uid;
        $week_id = $this->get('week_id');
        $week_plan = PlanWeekGroup::model()->findByPk($week_id);
        if (!$week_plan) {
            $this->msg(array('state' => 0, 'msgwords' => '该计划不存在！'));
        }
        $week_group_id = $week_plan->group_id;
        $uid = Yii::app()->admin_user->uid;
        $manage_group = Dtable::toArr(AdminGroup::model()->findAll("manager_id=$uid"));
        $page['manage_group'] = array_combine(array_column($manage_group, 'groupid'), array_column($manage_group, 'groupname'));
        if (!in_array($week_group_id, array_keys($page['manage_group']))) {
            $this->msg(array('state' => 0, 'msgwords' => '没有权限修改其他推广组计划！'));
        }
        if (!$_POST) {
            $week_date = array();
            $start_date = $week_plan->start_date;
            for ($i = 0; $i < 7; $i++) {
                $week_date[$i] = $start_date + $i * 24 * 60 * 60;
            }
            $details = PlanWeekGroupDetail::model()->findAll('week_id=' . $week_id . ' order by date asc');
            $detail_plan = array();
            foreach ($details as $value) {
                $detail_plan[$value['service_group']][$value['date']] = array(
                    'fans_count' => $value['fans_count'],
                    'output' => $value['output'],
                    'weChat_num' => $value['weChat_num'],
                );
            }
            $service = Dtable::toArr(CustomerServiceManage::model()->findAll('state_cooperation=0'));
            $service_group = array_combine(array_column($service, 'id'), array_column($service, 'cname'));
            $page['detail_plan'] = $detail_plan;
            $page['week_dates'] = $week_date;
            $page['service_group'] = $service_group;
            $page['week_plan'] = Dtable::toArr($week_plan);
            $this->render('editGroupPlan', array('page' => $page));
            exit();
        }
        $detail = $this->planGroupEditData($week_id);
        if ($detail) {
            //写入周排期详细数据
            $row = array('group_id', 'service_group', 'fans_count', 'output','weChat_num', 'date', 'week_id');
            $res = helper::batch_insert_data('plan_week_group_detail', $row, $detail);
            if ($res) {
                $logs = "修改了推广组周计划：" . $week_id;
                $this->logs($logs);
                $this->msg(array('state' => 1, 'msgwords' => '修改成功！','url'=>$this->get('backurl')));
            }
        } else {
            $this->msg(array('state' => 0, 'msgwords' => '数据为空！'));
        }

    }


    public function actionGetGroupPlanTotal()
    {
        $group_id = intval($this->get('group_id'));
        $service = intval($this->get('service_id'));
        $start_date = intval($this->get('start_date'));
        $group_user = Dtable::toArr(AdminUserGroup::model()->findAll('groupid='.$group_id));
        $users = array_column($group_user,'sno');
//        for ($i=0;$i<7;$i++) {
//            $page['week_dates'][$i] = $start_date+$i*24*60*60;
//        }

        $group_service_palns = array();
        if ($users) {
            $week_plan = Dtable::toArr(PlanWeekUser::model()->findAll(' status=12 and tg_uid in (' . implode(',',$users) . ') and start_date=' . $start_date));
            $week_ids = array_column($week_plan,'id');
            if ($week_ids) {
                $sql = 'select SUM(output) AS total_output,SUM(fans_count) AS total_fans,SUM(weChat_num) AS weChat_num, service_group, date from plan_week_user_detail 
                        where service_group = '.$service.' and week_id in ('.implode(',',$week_ids).') 
                        group by service_group,date order by date asc';
                $plan_detail = Yii::app()->db->createCommand($sql)->queryAll();
                foreach ($plan_detail as $value) {
                    $group_service_palns[] = array(
                        'output' => $value['total_output'],
                        'fans_count' => $value['total_fans'],
                        'weChat_num' => $value['weChat_num'],
                    );
                }
            }
        }
        if (empty($plan_detail)) {
            for ($i=0;$i<7;$i++) {
                $group_service_palns[] = array(
                    'output' => 0,
                    'fans_count' => 0,
                    'weChat_num' => 0,
                );
            }
        }

        die(json_encode($group_service_palns));

    }


    public function actionGetGroupPlanData(){
        $group_id = intval($this->get('group_id'));
        $data = CustomerServiceManage::model()->findAll('state_cooperation=0');
        $start_date = intval($this->get('start_date'));
        $group_user = Dtable::toArr(AdminUserGroup::model()->findAll('groupid='.$group_id));
        $users = array_column($group_user,'sno');
        $group_service_palns = array();

        foreach ($data as $val){
            if ($users) {
                $week_plan = Dtable::toArr(PlanWeekUser::model()->findAll(' status=12 and tg_uid in (' . implode(',',$users) . ') and start_date=' . $start_date));
                $week_ids = array_column($week_plan,'id');
                if ($week_ids) {
                    $sql = 'select SUM(output) AS total_output,SUM(fans_count) AS total_fans,SUM(weChat_num) AS weChat_num, service_group, date from plan_week_user_detail 
                        where service_group = '.$val['id'].' and week_id in ('.implode(',',$week_ids).') 
                        group by service_group,date order by date asc';
                    $plan_detail = Yii::app()->db->createCommand($sql)->queryAll();
                    foreach ($plan_detail as $value) {
                        $group_service_palns[$val['id']][] = array(
                            'output' => $value['total_output'],
                            'fans_count' => $value['total_fans'],
                            'weChat_num' => $value['weChat_num'],
                        );
                    }
                }
            }
            if (empty($plan_detail)) {
                for ($i=0;$i<7;$i++) {
                    $group_service_palns[$val['id']][] = array(
                        'output' => 0,
                        'fans_count' => 0,
                        'weChat_num' => 0,
                    );
                }
            }
        }

        die(json_encode($group_service_palns));
    }

    /**
     * 个人周计划审核
     * @author lxj
     */
    public function actionAuditUserPlan()
    {
        $week_id = $this->get('week_id');
        //1通过2拒绝
        $status = $this->get('status')?intval($this->get('status')):'';
        if ($status!=1 && $status!=2) {
            die(json_encode(array('state' => 0, 'msgwords' => '审核状态有误！')));
        }
        $unthrough_msg = trim($this->get('unthrough_msg'));
        if (!$week_id) {
            die(json_encode(array('state' => 0, 'msgwords' => '未选择计划！')));
        }
        if (!$unthrough_msg && $status==2) {
            die(json_encode(array('state' => 0, 'msgwords' => '请输入拒绝理由！')));
        }
        $plan = PlanWeekUser::model()->findByPk($week_id);
        if (!$plan) {
            die(json_encode(array('state' => 0, 'msgwords' => '计划不存在！')));
        }
        //判断审核人员
        $user_id = Yii::app()->admin_user->uid;
        $group_id = AdminUser::model()->get_manager_group($user_id);
        //是否需要审核人员审核
        $is_audit = intval(Yii::app()->params['remind']['Individual_plan_auditor']);
        //审核人员配置
        $audit_user = intval(Yii::app()->params['remind']['group_plan_auditor']);
        if ($is_audit == 1) {
            if (!$group_id && $user_id!=$audit_user) {
                die(json_encode(array('state' => 0, 'msgwords' => '您没有审核权限！')));
            }
        }else {
            if (!$group_id) {
                die(json_encode(array('state' => 0, 'msgwords' => '您没有审核权限！')));
            }
        }

        //判断推广组长可审核推广人员
        if ($user_id!=$audit_user) {
            $users = Dtable::toArr(AdminUserGroup::model()->findAll('groupid in ('.implode(',',$group_id).')'));
            $manage_user = array_unique(array_column($users,'sno'));
            $manage_user[] = $user_id;
            if (!in_array($plan->tg_uid,$manage_user)) {
                die(json_encode(array('state' => 0, 'msgwords' => '没有审核该推广人员计划权限！')));
            }
        }
        //审核人员为推广组长
        $status_user = 1;
        if ($audit_user == 0 || $user_id == $audit_user || $is_audit==0) {
            $status_user = 2;
        }
        $new_status = intval($status.$status_user);
        $plan->status = $new_status;
        if ($status == 2) {
            $plan->unthrough_msg = $unthrough_msg;
        }
        $old_through_time = $plan->through_time;
        //已结束审核流程
        if ($new_status == 12) {
            $plan->through_time = time();
        }
        $plan->save();
        $action_status = $status==1?'通过了':'拒绝了';
        $logs = $action_status."个人周计划:" . $week_id;
        $this->logs($logs);
        $mask = $status==1?'审批通过':'审批未通过';
        PlanWeekLog::model()->addPlanWeekLog($week_id,1,$plan->tg_uid,$mask,$plan->start_date);
        //发送消息
        $send = new SendMessage();
        $add_mask ='';
        if ($old_through_time < $plan->update_time && $old_through_time>0) {
            $add_mask = '(变更)';
        }
        $info = AdminUser::model()->findByPk($plan->tg_uid);
        $name = $info->csname_true;
        PlanWeekUserDetail::model()->updateUserPlanStatus($week_id,$new_status);
        if ($new_status == 12 || $status== 2) {
            $send->sendPlanMsg($plan->tg_uid,$plan->start_date,$week_id,$name,1,2,$add_mask.','.$mask);
            die(json_encode(array('state' => 1, 'msgwords' => '审核状态修改成功！')));
        }
        if ($new_status == 11 ) {
            $add_msg_content = $add_mask.'，需要您审批';
            $send = new SendMessage();
            $send->sendPlanMsg($audit_user,$plan->start_date,$week_id,$name,1,1,$add_msg_content);
        }
        die(json_encode(array('state' =>1, 'msgwords' => '审批成功！')));
    }


    /**
     * 推广组计划审核
     * @author lxj
     */
    public function actionAuditGroupPlan()
    {
        $week_id = $this->get('week_id');
        //1通过2拒绝
        $status = $this->get('status')?intval($this->get('status')):'';
        if ($status!=1 && $status!=2) {
            die(json_encode(array('state' => 0, 'msgwords' => '审核状态有误！')));
        }
        $unthrough_msg = trim($this->get('unthrough_msg'));
        if (!$week_id) {
            die(json_encode(array('state' => 0, 'msgwords' => '未选择计划！')));
        }
        if (!$unthrough_msg && $status==2) {
            die(json_encode(array('state' => 0, 'msgwords' => '请输入拒绝理由！')));
        }
        $plan = PlanWeekGroup::model()->findByPk($week_id);
        if (!$plan) {
            die(json_encode(array('state' => 0, 'msgwords' => '计划不存在！')));
        }
        //判断审核人员
        $user_id = Yii::app()->admin_user->uid;
        //审核人员配置
        $audit_user = intval(Yii::app()->params['remind']['group_plan_auditor']);
        if ($user_id!=$audit_user) {
            die(json_encode(array('state' => 0, 'msgwords' => '您没有审核推广组计划权限！')));
        }
        $plan->status = $status;
        if ($status == 2) {
            $plan->unthrough_msg = $unthrough_msg;
        }
        //已结束审核流程
        $old_through_time =  $plan->through_time;
        if ($status == 1) {
            $plan->through_time = time();
        }
        $plan->save();
        $action_status = $status==1?'通过了':'拒绝了';
        $logs = $action_status."推广组周计划:" . $week_id;
        $this->logs($logs);
        $mask = $status==1?'审批通过':'审批未通过';
        PlanWeekLog::model()->addPlanWeekLog($week_id,2,$plan->group_id,$mask,$plan->start_date);
        //修改详细计划审批状态
        PlanWeekGroupDetail::model()->updateGroupPlanStatus($week_id,$status);
        //查询推广组长
        $group_id = $plan->group_id;
        $ghroup_info = AdminGroup::model()->findByPk($group_id);
        $name = $ghroup_info->groupname;
        $add_mask ='';
        if ($old_through_time < $plan->update_time && $old_through_time>0) {
            $add_mask = '(变更)';
        }
        $send = new SendMessage();
        $send->sendPlanMsg($ghroup_info->manager_id,$plan->start_date,$week_id,$name,2,3,$add_mask.','.$mask);
        die(json_encode(array('state' => 1, 'msgwords' => '审核状态修改成功！')));
    }


    /**
     * 个人计划添加数据
     * @param $week_id int 修改的计划周期，编辑时传入
     * @return array
     * @author lxj
     */
    private function planUserAddData($week_id = 0,$is_manege)
    {
        $type = $_POST['type'];
        $user_id = 0;
        if ($is_manege == 0) {
            $user_id = Yii::app()->admin_user->uid;
        }
        $status = 0;
        if ($is_manege == 1) {
            if (!$user_id && $week_id==0) {
                $user_id = $this->get('tg_uid');
                if (!$user_id) {
                    $this->msg(array('state' => 0, 'msgwords' => '请先选择推广人员！'));
                }
            }
            if ($week_id) {
                $user_id = Yii::app()->admin_user->uid;
            }
            $is_audit = intval(Yii::app()->params['remind']['Individual_plan_auditor']);
            $audit = intval(Yii::app()->params['remind']['group_plan_auditor']);
            $uid  = Yii::app()->admin_user->uid;
            if ($is_audit == 1) {
                if ($audit!=$uid) {
                    $status = 11;
                }else {
                    $status = 12;
                }
            } else {
                $status = 12;
            }
        }
        //下周计划
        $add_week = $type == 1 ? 1 : 0;
        if ($week_id == 0) {
            $week_date = helper::getWeekDate($add_week);
            $start_date = $week_date[0];
            $week_log = PlanWeekUser::model()->find('tg_uid = ' . $user_id . ' and start_date=' . $start_date);
            if ($week_log) {
                $this->msg(array('state' => -2, 'msgwords' => '该周期已添加过计划！','url'=>$this->get('backurl')));
            }
        }
        $service_group = $_POST['cservice_group'];
        foreach ($service_group as $value) {
            if (!$value) {
                $this->msg(array('state' => 0, 'msgwords' => '请选择客服部！'));
            }
        }
        if (count($service_group) <= 0) {
            $this->msg(array('state' => 0, 'msgwords' => '请至少添加一个客服部数据！'));
        }
        if ($week_id!=0) {
            $week_plan = PlanWeekUser::model()->findByPk($week_id);
            if (!$week_plan) {
                $this->msg(array('state' => 0, 'msgwords' => '该计划不存在！'));
            }
            $start_date = $week_plan->start_date;
            $old_status = $week_plan->status;
            $old_through_time = $week_plan->through_time;
            for ($i=0;$i<7;$i++) {
                $week_date[$i] = $start_date+$i*24*60*60;
            }
            //删除已添加的每天排期
            $res = PlanWeekUserDetail::model()->deleteAll('week_id=' . $week_id);
//            if (!$res) {
//                $this->msg(array('state' => 0, 'msgwords' => '删除数据失败！'));
//            }
        } else {
            $week_plan = new PlanWeekUser();
            //写入周排期记录表
            $week_plan->tg_uid = $user_id;
            $week_plan->start_date = $start_date;
            $week_plan->add_time = time();
        }

        $week_plan->mask = $_POST['mask'];
        $week_plan->update_time = time();
        $week_plan->status = $status;

        if ($status == 12) {
            $week_plan->through_time = time();
        }
        $week_plan->save();
        $week_plan_id = $week_plan->primaryKey;
        $add_msg_content = '';
        $send_msg = 0;
        //写入操作日志
        if ($week_id == 0) {
            PlanWeekLog::model()->addPlanWeekLog($week_plan_id,1,$user_id,'添加计划',$start_date);
        } else {
            if ($old_through_time!= 0 && $old_through_time<$week_plan->update_time) {
                $add_msg_content .= '(变更)';
                PlanWeekLog::model()->addPlanWeekLog($week_plan_id,1,$user_id,'提交变更计划',$start_date);
            }else {
                PlanWeekLog::model()->addPlanWeekLog($week_plan_id,1,$user_id,'修改计划',$start_date);
            }
        }
        //查询推广组长
        $groups = AdminUser::model()->get_user_group($user_id);
        $group_manage = array_unique(array_column($groups,'manager_id'));
        $user_info = AdminUser::model()->findByPk($user_id);
        $name = $user_info->csname_true;
        if ($groups && $status==0) {
            $add_msg_content .= '，需要您审批';
            $send = new SendMessage();
            $send->sendPlanMsg($group_manage,$start_date,$week_plan_id,$name,1,1,$add_msg_content);
        }
        if ($status == 11) {
            //审核人员配置
            $audit_user = intval(Yii::app()->params['remind']['group_plan_auditor']);
            $add_msg_content .= '，需要您审批';
            $send = new SendMessage();
            $send->sendPlanMsg($audit_user,$start_date,$week_plan_id,$name,1,1,$add_msg_content);
        }
        //处理写入周排期详情数据
        $detail = array();
        foreach ($service_group as $value) {
            for ($i = 0; $i < 7; $i++) {
                $detail[] = array(
                    'tg_uid' => $user_id,
                    'service_group' => $value,
                    'fans_count' => intval($_POST['fans_' . $value][$i]),
                    'output' => intval($_POST['output_' . $value][$i]),
                    'date' => $week_date[$i],
                    'week_id' => $week_plan_id,
                    'status'=>$status,
                    'weChat_num'=>intval($_POST['wechat_' . $value][$i]),
                );
            }
        }
        return $detail;
    }


    /**
     * 个人周计划列表数据
     * @return mixed
     * @author lxj
     */
    private function getUserPlan()
    {
        $uid = Yii::app()->admin_user->uid;
        $where = ' 1 ';
        $L_where = ' 1 ';
        if ($this->get('status') != '') {
            $where.= ' and status ='.$this->get('status');
        }
        $date_start = $this->get('date_start');

        if($date_start){
            $where.= ' and start_date ='.strtotime($date_start);
            $page['date_start'] = $this->get('date_start');
            for ($i=0;$i<7;$i++) {
                $week_date[$i] = strtotime($date_start)+$i*24*60*60;
            }
            $L_where.= ' and date ='.$week_date[0];
        }else{
            $week_date = strtotime('+1 week last monday');
            $L_where.= ' and date ='.$week_date;
        }

        if ($this->get('group_id') != '') {
            $group_id = $this->get('group_id');
            $gusers = Dtable::toArr(AdminUserGroup::model()->findAll('groupid='.$group_id));
            $user_ids = array_unique(array_column($gusers,'sno'));
            if ($user_ids) {
                $where.= ' and tg_uid in ('.implode(',',$user_ids).') ';
            } else {
                $where.= ' and tg_uid = 0 ';
            }
        }
        if ($this->get('user_id')) {
            $where.= ' and tg_uid ='.intval($this->get('user_id'));
            $L_where .= ' and tg_uid=' . intval($this->get('user_id'));
        }

        if($this->get('csid')) {
            $L_where .= ' and service_group=' . $this->get('csid');
        }

        $groupBy = ' group by week_id';
        $L_details = PlanWeekUserDetail::model()->findAll($L_where.$groupBy);
        $string = '';
        foreach ($L_details as $value){
            $string .= $value['week_id'].',';
        }
        $string = rtrim($string,',');
        $promotionStafflist = PromotionStaff::model()->getPromotionStaffList(1);
        $order_by = ' order by id desc';
        //用户可查看的推广人员
        $user_ids = AdminUser::model()->user_plan_data_authority($uid);
        if ($user_ids !== 0) {
            $user_ids[] = $uid;
            $where.= ' and tg_uid in ('.implode(',',$user_ids).')';
            foreach ($promotionStafflist as $key=>$value) {
                if (!in_array($value['user_id'],$user_ids)) {
                    unset($promotionStafflist[$key]);
                }
            }
        }
        if($string){
            $where.= ' and id in('.$string.')';
            $page['listdata'] = Dtable::toArr(PlanWeekUser::model()->findAll($where.$order_by));
        }
        $W_where = '';
        $detail_plan = array();
        if ($page['listdata']) {
            $week_ids = array_column($page['listdata'], 'id');
            $W_where .= 'week_id in (' . implode(',', $week_ids) . ')';
            $order = ' order by date asc';
            $details = PlanWeekUserDetail::model()->findAll($W_where.$order);
            foreach ($details as $value) {
                if($this->get('csid')){
                    if($this->get('csid') == $value['service_group'])
                    $detail_plan[$value['week_id']][$value['service_group']][$value['date']] = array(
                        'fans_count' => $value['fans_count'],
                        'output' => $value['output'],
                        'weChat_num' => $value['weChat_num'],
                    );
                }else{
                    $detail_plan[$value['week_id']][$value['service_group']][$value['date']] = array(
                        'fans_count' => $value['fans_count'],
                        'output' => $value['output'],
                        'weChat_num' => $value['weChat_num'],
                    );
                }
            }

        }

        $page['detail_plan'] = $detail_plan;
        $page['promotions_staff'] = $promotionStafflist;

        return $page;
    }

    /**
     * 组周计划添加数据
     * @return array
     * @author lxj
     */
    private function planGroupAddData()
    {
        $type = $_POST['type'];
        $group_id = $_POST['group_id'];
        //下周计划
        $add_week = $type == 1 ? 1 : 0;
        $week_date = helper::getWeekDate($add_week);
        $start_date = $week_date[0];
        /********  数据检查 start *********/
        if (count($group_id) <= 0) {
            $this->msg(array('state' => 0, 'msgwords' => '请至少添加一个推广组数据！'));
        }
        $user_id = Yii::app()->admin_user->uid;
        $mgroup = Dtable::toArr(AdminGroup::model()->findAll("manager_id=$user_id"));
        $manage_group = array_combine(array_column($mgroup, 'groupid'), array_column($mgroup, 'groupname'));
        $mids = array_keys($manage_group);
        $detail = array();
        foreach ($group_id as $value) {
            if (!in_array($value, $mids)) {
                $this->msg(array('state' => 0, 'msgwords' => '您只能添加自己的推广组数据！'));
            }
            $group_service = $_POST['cservice_group_' . $value];
            foreach ($group_service as $ser) {
                if (!$ser) {
                    $this->msg(array('state' => 0, 'msgwords' => '请选择客服部！'));
                }
            }
            if (count($group_service) <= 0) {
                $this->msg(array('state' => 0, 'msgwords' => $manage_group[$value] . '请至少选择一个客服部！'));
            }
            $group_plan = PlanWeekGroup::model()->find('group_id='.$value.' and start_date='.$start_date);
            if ($group_plan) {
                $this->msg(array('state' => 0, 'msgwords' => $manage_group[$value] . '在该周期内已有计划！'));
            }
            $week_plan = new PlanWeekGroup();
            //写入周排期记录表
            $week_plan->group_id = $value;
            $week_plan->start_date = $start_date;
            $week_plan->add_time = time();
            $week_plan->mask = $_POST['mask'];
            $week_plan->update_time = time();
            $week_plan->status = 0;
            $week_plan->save();
            $week_plan_id = $week_plan->primaryKey;
            foreach ($group_service as $ser) {
                for ($i = 0; $i < 7; $i++) {
                    $detail[] = array(
                        'group_id' => intval($value),
                        'service_group' => intval($ser),
                        'old_fans_count' => intval($_POST['oldfans_' . $value . '_' . $ser][$i]),
                        'fans_count' => intval($_POST['fans_' . $value . '_' . $ser][$i]),
                        'old_output' => intval($_POST['oldoutput_' . $value . '_' . $ser][$i]),
                        'output' => intval($_POST['output_' . $value . '_' . $ser][$i]),
                        'old_weChat_num' => intval($_POST['oldowechat_' . $value . '_' . $ser][$i]),
                        'weChat_num' => intval($_POST['wechat_' . $value . '_' . $ser][$i]),
                        'date' => $week_date[$i],
                        'week_id' => $week_plan_id,
                    );
                }
            }

            //审核人员配置
            $audit_user = intval(Yii::app()->params['remind']['group_plan_auditor']);
            //写入操作日志
            PlanWeekLog::model()->addPlanWeekLog($week_plan_id,2,$value,'添加计划',$start_date);
            //查询推广组
            $ghroup_info = AdminGroup::model()->findByPk($value);
            $name = $ghroup_info->groupname;
            $add_msg_content = '，需要您审批';
            $send = new SendMessage();
            $send->sendPlanMsg($audit_user,$start_date,$week_plan_id,$name,2,1,$add_msg_content);
        }
        /********  数据检查 end *********/

        return $detail;

    }

    /**
     * 组周计划修改数据
     * @return array
     * @author lxj
     */
    private function planGroupEditData($week_id)
    {
        $type = $_POST['type'];
        $group_id = $_POST['group_id'];
        /********  数据检查 start *********/
        if (!$group_id) {
            $this->msg(array('state' => 0, 'msgwords' => '请选择一个推广组！'));
        }
        $user_id = Yii::app()->admin_user->uid;
        $mgroup = Dtable::toArr(AdminGroup::model()->findAll("manager_id=$user_id"));
        $manage_group = array_combine(array_column($mgroup, 'groupid'), array_column($mgroup, 'groupname'));
        $mids = array_keys($manage_group);
        $detail = array();
        if (!in_array($group_id, $mids)) {
            $this->msg(array('state' => 0, 'msgwords' => '您只能修改自己的推广组数据！'));
        }
        $group_service = $_POST['cservice_group'];
        foreach ($group_service as $ser) {
            if (!$ser) {
                $this->msg(array('state' => 0, 'msgwords' => '请选择客服部！'));
            }
        }
        if (count($group_service) <= 0) {
            $this->msg(array('state' => 0, 'msgwords' => '请至少选择一个客服部！'));
        }
        foreach ($group_service as $ser) {
            if (!$ser) {
                $this->msg(array('state' => 0, 'msgwords' => '请选择客服部！'));
            }
        }
        /********  数据检查 end *********/
        if (count($group_service) <= 0) {
            $this->msg(array('state' => 0, 'msgwords' =>  '请至少选择一个客服部！'));
        }
        $week_plan = PlanWeekGroup::model()->findByPk($week_id);
        if (!$week_plan) {
            $this->msg(array('state' => 0, 'msgwords' => '该组计划不存在！'));
        }
        $old_status = $week_plan->status;
        $old_through_time = $week_plan->through_time;
        $first_day = $week_plan->start_date;
        //写入周排期记录表
        $week_plan->mask = $_POST['mask'];
        $week_plan->update_time = time();
        $week_plan->status = 0;
        $week_plan->save();
        $week_plan_id = $week_plan->primaryKey;
        PlanWeekGroupDetail::model()->deleteAll('week_id=' . $week_id);
        foreach ($group_service as $ser) {
            for ($i = 0; $i < 7; $i++) {
                $detail[] = array(
                    'group_id' => intval($group_id),
                    'service_group' => intval($ser),
                    'fans_count' => intval($_POST['fans_'.$ser][$i]),
                    'output' => intval($_POST['output_'.$ser][$i]),
                    'weChat_num' => intval($_POST['wechat_'.$ser][$i]),
                    'date' => $first_day+$i*24*60*60,
                    'week_id' => $week_plan_id,
                );
            }
        }
        $add_msg_content = '';
        if ($old_through_time != 0 && $old_through_time<$week_plan->update_time) {
            $add_msg_content .="(变更)";
            PlanWeekLog::model()->addPlanWeekLog($week_plan_id,2,$group_id,'提交变更计划',$first_day);
        }else {
            PlanWeekLog::model()->addPlanWeekLog($week_plan_id,2,$group_id,'修改计划',$first_day);
        }
        //审核人员配置
        $audit_user = intval(Yii::app()->params['remind']['group_plan_auditor']);
        //查询推广组长
        $ghroup_info = AdminGroup::model()->findByPk($group_id);
        $name = $ghroup_info->groupname;
        $add_msg_content .= '，需要您审批';
        $send = new SendMessage();
        $send->sendPlanMsg($audit_user,$first_day,$week_plan_id,$name,2,1,$add_msg_content);
        return $detail;

    }


    private function getGroupPlan($manage_group)
    {
        $page = array();
        $uid = Yii::app()->admin_user->uid;

        $where = ' 1 ';
        if ($this->get('status') != '') {
            $where.= ' and status ='.$this->get('status');
        }
        if ($this->get('group_id') != '') {
            $group_id = $this->get('group_id');
            $where.= ' and group_id='.$group_id;
        }
        if($this->get('date_start_group')){
            $where.= ' and start_date ='.strtotime($this->get('date_start_group'));
            $page['date_start_group'] = $this->get('date_start_group');
        }
        //用户可查看组别
        $group_ids = AdminUser::model()->group_plan_data_authority($uid);
        if ($group_ids !== 0) {
            if ($group_ids) {
                $where.= ' and group_id in (' . implode(',', $group_ids) . ') ';
            } else {
                $where.= ' and group_id=0 ';
            }
        }
        $page['listdata'] = Dtable::toArr(PlanWeekGroup::model()->findAll($where));
        $detail_plan = array();
        if ($page['listdata']) {
            $week_ids = array_column($page['listdata'], 'id');
            if($this->get('csids')){
                $details = PlanWeekGroupDetail::model()->findAll('week_id in (' . implode(',', $week_ids) . ') and service_group='.$this->get('csids').' order by date asc');
            }else{
                $details = PlanWeekGroupDetail::model()->findAll('week_id in (' . implode(',', $week_ids) . ') order by date asc');
            }

            foreach ($details as $value) {
                $detail_plan[$value['week_id']][$value['group_id']][$value['service_group']][$value['date']] = array(
                    'old_fans_count' => $value['old_fans_plan'],
                    'fans_count' => $value['fans_count'],
                    'old_output' => $value['old_output_plan'],
                    'output' => $value['output'],
                    'old_weChat_num' => $value['old_weChat_num'],
                    'weChat_num' => $value['weChat_num'],
                );
            }

        }
        $page['detail_plan'] = $detail_plan;
        $page['manage_group'] = $manage_group;

        return $page;
    }


    /**
     * @param $is_manage int 0:审核人员
     * @param $group array 组长负责推广组
     * @return mixed
     */
    private function getUnauditUserPlan($is_manage,$group)
    {
        $uid = Yii::app()->admin_user->uid;
        $audit_date = array();
        $group_date = array();
        if ($is_manage == 0) {
            $audit_date = Dtable::toArr(PlanWeekUser::model()->findAll('status=11 order by id desc '));
        }
        if ($group) {
            $group_users = Dtable::toArr(AdminUserGroup::model()->findAll('groupid in ('.implode(',',$group).')'));
            $user_ids = array_unique(array_column($group_users,'sno'));
            if ($user_ids) {
                $group_date = Dtable::toArr(PlanWeekUser::model()->findAll('tg_uid in ('.implode(',',$user_ids).') and status =0 order by id desc '));
            }
        }

        $page['listdata'] = array_merge($audit_date,$group_date);
        $detail_plan = array();
        if ($page['listdata']) {
            $week_ids = array_column($page['listdata'], 'id');
            $details = PlanWeekUserDetail::model()->findAll('week_id in (' . implode(',', $week_ids) . ') order by date asc');
            foreach ($details as $value) {
                $detail_plan[$value['week_id']][$value['service_group']][$value['date']] = array(
                    'fans_count' => $value['fans_count'],
                    'output' => $value['output'],
                    'weChat_num' => $value['weChat_num'],
                );
            }

        }
        $page['detail_plan'] = $detail_plan;
        return $page;
    }

    /**
     * @return mixed
     */
    private function getUnauditGroupPlan()
    {
        $page['listdata'] = Dtable::toArr(PlanWeekGroup::model()->findAll('status=0 order by id desc '));
        $detail_plan = array();
        if ($page['listdata']) {
            $week_ids = array_column($page['listdata'], 'id');
            $details = PlanWeekGroupDetail::model()->findAll('week_id in (' . implode(',', $week_ids) . ') order by date asc');
            foreach ($details as $value) {
                $detail_plan[$value['week_id']][$value['service_group']][$value['date']] = array(
                    'fans_count' => $value['fans_count'],
                    'output' => $value['output'],
                    'weChat_num' => $value['weChat_num'],
                );
            }

        }
        $page['detail_plan'] = $detail_plan;
        return $page;
    }


    private function getPlanLog()
    {
        $user_id = Yii::app()->admin_user->uid;
        $user_ids = AdminUser::model()->user_plan_data_authority($user_id);
        $groups = AdminUser::model()->get_manager_group($user_id);
        $params['where'] = '';
        if ($user_ids !== 0) {
            $user_ids[] = $user_id;
            if ($groups) {
                $params['where'] .= ' and (( relation_id in ('.implode(',',$user_ids).') and plan_type=1 ) or ( relation_id in ('.implode(',',$groups).') and plan_type=2 ))';
            }else {
                $params['where'] .= ' and relation_id='.$user_id.' and plan_type=1';
            }
        }
        $title = trim($this->get('title'));
        $title = str_replace('-排期计划','',$title);
        if ($title) {
            $params['where'] .= ' and ( title like \'%'.$title.'%\' )';
        }
        $page['title'] = $title;
        $params['order'] = "  order by a.id desc    ";
        $params['pagesize'] =  1000;
        $params['pagebar'] = 1;
        $params['select'] = "a.*";
        $params['smart_order'] = 1;
        $page['listdata']= Dtable::model(PlanWeekLog::model()->tableName())->listdata($params);;
        return $page;
    }

}