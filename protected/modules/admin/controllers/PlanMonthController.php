<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/17
 * Time: 9:11
 */

class PlanMonthController extends AdminController
{
    public function actionIndex()
    {
        $params_groups = array();
        $id = Yii::app()->admin_user->uid;
        $authority = AdminUser::model()->getUserAuthority($id);

        $is_tg = AdminUser::model()->isUserPromotionStaff($id);
        //是否可添加个人计划
        $show_add_user_plan = 0;
        //是否可添加组计划
        $show_add_group_plan = 0;
        if ($is_tg) {
            $show_add_user_plan = 1;
            $params_groups = array(vars::$fields['plan_manage'][1],vars::$fields['plan_manage'][3]);
        }
        if ($authority==0 || $authority==2) {
            $show_add_user_plan = 1;
            $show_add_group_plan = 1;
        }
        $is_super_admin = 0;
        //判断是否为超级管理员
        if($id==Yii::app()->params['management']['super_admin_id'] ) $is_super_admin =1;
        //判断权限是否为超级管理员权限
        $urole = AdminUser::model()->get_user_role($id);
        foreach ($urole as $val){
            if($val['role_id'] == 1) $is_super_admin =1;
        }
        if ($authority != 3 || $is_super_admin) {
            $page = $this->getExportData();
            $data = $this->getGroupData();
            $check = $this->getCheckData($page, $data);

            if($authority == 0 || $is_super_admin){
                $mgroup = Dtable::toArr(AdminGroup::model()->findAll());
            }elseif($authority == 2){
                $mgroup = Dtable::toArr(AdminGroup::model()->findAll("manager_id=$id"));
            }
            $page['manage_group'] = array_combine(array_column($mgroup, 'groupid'), array_column($mgroup, 'groupname'));
        } else {
            $page = $this->getExportData();
        }
        if ($authority < 3) {
            $params_groups = vars::$fields['plan_manage'];
        }
        if ($is_super_admin == 1) {
            $params_groups = array(vars::$fields['plan_manage'][1],vars::$fields['plan_manage'][2],vars::$fields['plan_manage'][3]);
        }
        ksort($params_groups);
        $page['params_groups'] = $params_groups;
        $first = array_shift($params_groups);
        $group_id = $this->get('group_id') ? $this->get('group_id') : $first['value'];

        $promotionStafflist= array();
        if($authority == 0 || $is_super_admin == 1){
            $promotionStafflist= Dtable::toArr(PromotionStaff::model()->getPromotionStaffList(1));
        } elseif($authority == 2){
            $promotionStafflist= PromotionStaff::model()->getPromotionStaffByManager($id);
            $user_ids = array_column($promotionStafflist,'user_id');
            if (!in_array($id,$user_ids)) {
                $user_name = AdminUser::model()->getUserNameByPK($id);
                $promotionStafflist[] = array(
                    'user_id' => $id,
                    'name' => $user_name,
                );
            }
        }


        $page['promotions_staff'] = $promotionStafflist;
        $page['is_super_admin'] = $is_super_admin;
        $page['group_id'] = $group_id;
        $page['show_add_user_plan'] = $show_add_user_plan;
        $page['show_add_group_plan'] = $show_add_group_plan;
        $log_dada = array();
        if ($group_id == 3) {
            $log_dada = $this->getLog();
        }

        $this->render('index', array('page' => $page, 'data' => $data, 'check' => $check, 'log' => $log_dada));
    }

    public function actionAdd()
    {
        $id = Yii::app()->admin_user->uid;
        $authority = AdminUser::model()->getUserAuthority($id);
        $name = AdminUser::model()->getUserNameByPK($id);
        if (!$_POST) {
            $month = $this->get('month');
            if($authority !=3){
                $select['list'] = PromotionStaff::model()->getPromotionStaffByManager($id);
                $select['list'][] = array('user_id'=>$id,'name'=>$name);
            }else{
                $select['list'][] = array('user_id'=>$id,'name'=>$name);
            }

            $this->render('W_PersonalPlan', array('month' => $month, 'select' => $select));
        }

        if ($_POST) {
            $status = 0;
            if ($authority == 2) $status = 2;
            if ($authority == 0) $status = 4;
            if ($_POST['tg_uid']) {
                $tg_uid = $_POST['tg_uid'];
            } else {
                $tg_uid = Yii::app()->admin_user->uid;
            }
            $ret = PlanMonthUser::model()->findBySql('select id from plan_month_user where tg_uid=' . $tg_uid . ' and month=' . strtotime($_POST['month']));
            if ($ret) {
                $this->msg(array('state' => 0, 'msgwords' => $_POST['month'] . '计划已存在'));
                exit;
            }

            foreach ($_POST['csid'] as $k => $v) {
                $info = new PlanMonth();
                $info->tg_uid = $tg_uid;
                $info->cs_id = $_POST['csid'][$k];
                $info->fans_plan = $_POST['fans_count'][$k];
                $info->output_plan = $_POST['output'][$k];
                $info->weChat_num = $_POST['weChat_num'][$k];
                $info->create_time = strtotime("now");
                $info->update_time = strtotime("now");
                $info->month = strtotime($_POST['month']);
                $info->status = $status;
                $info->save();
            }


            $temp = new PlanMonthUser();
            $temp->tg_uid = $tg_uid;
            $temp->month = strtotime($_POST['month']);
            $temp->remark = $_POST['remark'];
            $temp->add_time = strtotime("now");
            $temp->update_time = strtotime("now");
            $temp->status = $status;
            if ($status == 4) {
                $temp->through_time = time();
            }
            $temp->save();
            $month_id = $temp->primaryKey;
            $name = AdminUser::model()->find('csno=' . $tg_uid);
            $title = $name->csname_true.'-'.$_POST['month'].  '-'.'进粉计划';
            $log = new PlanMonthLog();
            $log->addPlanMonthLog($month_id,1,$tg_uid,"新计划",strtotime($_POST['month']));

            //查询推广组长
            $user_id = $_POST['tg_uid'];
            $groups = AdminUser::model()->get_user_group($user_id);
            $group_manage = array_unique(array_column($groups, 'manager_id'));
            $add_msg_content = '';
            $user_info = AdminUser::model()->findByPk($user_id);
            $name = $user_info->csname_true;
            $add_msg_content .= '，需要您审批';
            if ($groups && $status==0) {
                $send = new SendMessage();
                $send->sendPlanMsg($group_manage, strtotime($_POST['month']), $temp->primaryKey, $name, 3, 1, $add_msg_content);
            }
            if ($status == 2) {
                $audit_user = intval(Yii::app()->params['remind']['group_plan_auditor']);
                $send = new SendMessage();
                $send->sendPlanMsg($audit_user,strtotime($_POST['month']),$temp->primaryKey,$name,3,1,$add_msg_content);
            }
            $this->msg(array('state' => 1, 'msgwords' => '个人计划添加成功', 'url' => '/admin/planMonth/index?group_id=1'));

        }
    }

    public function actionAddGroup()
    {
        $id = Yii::app()->admin_user->uid;
        $authority = AdminUser::model()->getUserAuthority($id);
        if (!$_POST) {
            $month = $this->get('month');
            $group_ids = AdminUser::model()->get_manager_group($id);
            $group = array();
            foreach ($group_ids as $key=>$val){
                $group[$val] = AdminUserGroup::model()->getUsers($val);
            }

            $arr = array();
            $params['where'] = '';
            $params['where'] .= ' and status=4 and month='.strtotime($month);
            $params['group'] = " group by cs_id,month,tg_uid";
            $params['order'] = "  order by a.create_time";
            $params['pagebar'] = 1;

            $params['select'] = " sum(a.fans_plan) as fans_plans , sum(a.output_plan) as output_plans,sum(a.weChat_num) as weChat_num,a.*";
            $sql = " select ".$params['select']." from plan_month as a  where 1 ".$params['where'].$params['group']. $params['order'];
            $page = Yii::app()->db->createCommand($sql)->queryAll();

            foreach ($group as $key=>$val){
                foreach ($page as $value){
                    if(in_array($value['tg_uid'],$val)){
                        $arr[$key][$value['cs_id']]['weChat_num'] += $value['weChat_num'];
                        $arr[$key][$value['cs_id']]['fans_plans'] += $value['fans_plans'];
                        $arr[$key][$value['cs_id']]['output_plans'] += $value['output_plan'];
                    }
                }
            }

            $this->render('W_GroupPlan', array('month' => $month,'arr'=>$arr));
        }

        if ($_POST) {
            $status = 0;
            if ($authority == 2) $status = 2;
            if ($authority == 0) $status = 4;
            foreach ($_POST['cs_id'] as $k => $v) {

                    $ret = PlanMonthGroupUser::model()->findBySql('select * from plan_month_group_user where groupid=' . $_POST['group_ids'][$k] . ' and month=' . strtotime($_POST['month'][$k]));
                    if (strtotime($_POST['month'][$k]) == $ret['month']) {
                        $this->msg(array('state' => 0, 'msgwords' => $_POST['group_names'][$k] . $_POST['month'][$k] . '计划已存在'));
                    }
            }
            $tg_uid = Yii::app()->admin_user->uid;


            foreach ($_POST['cs_id'] as $k => $v) {
                    $cs_id = $_POST['cs_id'][$k];
                    $old_weChat_num = $_POST['old_weChat_num'][$k];
                    $weChat_num = $_POST['weChat_num'][$k];
                    $old_fans_plan = $_POST['old_fans_count'][$k];
                    $fans_plan = $_POST['fans_count'][$k];
                    $old_output_plan = $_POST['old_output'][$k];
                    $output_plan = $_POST['output'][$k];
                    $num = count($cs_id);
                    for ($i = 0; $i < $num; $i++) {
                        $info = new PlanMonthGroup();
                        $info->groupid = $_POST['group_ids'][$k];
                        $info->cs_id = $cs_id[$i];
                        $info->old_weChat_num = $old_weChat_num[$i];
                        $info->weChat_num = $weChat_num[$i];
                        $info->old_fans_plan = $old_fans_plan[$i];
                        $info->fans_plan = $fans_plan[$i];
                        $info->old_output_plan = $old_output_plan[$i];
                        $info->output_plan = $output_plan[$i];
                        $info->create_time = strtotime("now");
                        $info->update_time = strtotime("now");
                        $info->status = $status;
                        $info->month = strtotime($_POST['month'][$k]);
                        $info->save();
                    }
            }

            if ($_POST) {
                foreach ($_POST['cs_id'] as $k => $v) {
                        $temp = new planMonthGroupUser();
                        $temp->groupid = $_POST['group_ids'][$k];
                        $temp->month = strtotime($_POST['month'][$k]);
                        $temp->remark = $_POST['remark'];
                        $temp->status = $status;
                        $temp->add_time = strtotime("now");
                        $temp->update_time = strtotime("now");
                        $temp->save();
                        $month_id = $temp->primaryKey;
                        $log = new PlanMonthLog();
                        $log->addPlanMonthLog($month_id,2,$_POST['group_ids'][$k],"新计划",strtotime($_POST['month'][$k]));
                }


                //发送消息
                $groups = array_unique($_POST['group_ids']);
                if ($groups) {
                    $ghroup_info = Dtable::toArr(AdminGroup::model()->findAll('groupid in (' . implode(',', $groups) . ')'));
                    $group_name = array_combine(array_column($ghroup_info, 'groupid'), array_column($ghroup_info, 'groupname'));
                    //审核人员配置
                    $audit_user = intval(Yii::app()->params['remind']['group_plan_auditor']);
                    //查询推广组
                    foreach ($groups as $key => $value) {
                        $add_msg_content = '，需要您审批';
                        $send = new SendMessage();
                        $send->sendPlanMsg($audit_user, strtotime($_POST['month'][$key]), 0, $group_name[$value], 4, 1, $add_msg_content);
                    }

                }

                $this->msg(array('state' => 1, 'msgwords' => '组计划添加成功', 'url' => '/admin/planMonth/index?group_id=2'));
            }
        }
    }

    public function actionEdit()
    {
        $id = Yii::app()->admin_user->uid;
        $authority = AdminUser::model()->getUserAuthority($id);
        $page = array();
        if (!$_POST) {
            $id = $this->get('id');
            if ($id) {
                $update = PlanMonthUser::model()->getUserData($id);
                $this->render('update', array('update' => $update));
            } else {
                $this->render('L_PersonalPlan', array('page' => $page));
            }
        }

        if ($_POST) {
            $status = 0;
            if ($authority == 2) $status = 2;
            if ($authority == 0) $status = 4;
            if (count($_POST['csid']) != count(array_unique($_POST['csid']))) {
                $this->msg(array('state' => 0, 'msgwords' => '客服部重复！'));
            }

            foreach ($_POST['ids'] as $k => $v) {
                $list = PlanMonth::model()->findByPk($_POST['ids'][$k]);
                $list->delete();
            }

            foreach ($_POST['csid'] as $k => $v) {
                $info = new PlanMonth();
                $info->tg_uid = $id;
                $info->cs_id = $_POST['csid'][$k];
                $info->fans_plan = $_POST['fans_count'][$k];
                $info->output_plan = $_POST['output'][$k];
                $info->weChat_num = $_POST['weChat_num'][$k];
                $info->create_time = strtotime("now");
                $info->update_time = strtotime("now");
                $info->month = $_POST['month'];
                $info->save();
            }

            $info = PlanMonthUser::model()->findByPk($_POST['id']);
            $info->remark = $_POST['remark'];
            $info->status = $status;
            $info->update_time = strtotime("now");
            $info->save();

            $log = new PlanMonthLog();
            if($info->update_time > $info->through_time && $info->through_time != 0){
                $mask = "计划变更";
            }else{
                $mask= "修改计划";
            }
            $log->addPlanMonthLog($_POST['id'],1,$info->tg_uid,$mask,$info->month);
            
            //查询推广组长
            $user_id = $info->tg_uid;
            $groups = AdminUser::model()->get_user_group($user_id);
            $group_manage = array_unique(array_column($groups, 'manager_id'));
            $add_msg_content = '';
            $user_info = AdminUser::model()->findByPk($user_id);
            $name = $user_info->csname_true;
            if ($info->through_time < $info->update_time && $info->through_time != 0) {
                $add_msg_content .= '(计划变更)';
            }
            if ($groups && $status == 0) {
                $add_msg_content .= '，需要您审批';
                $send = new SendMessage();
                $send->sendPlanMsg($group_manage, $info->month, $info->primaryKey, $name, 3, 1, $add_msg_content);
            }
            if ($status == 2) {
                $audit_user = intval(Yii::app()->params['remind']['group_plan_auditor']);
                $send = new SendMessage();
                $add_msg_content .= '，需要您审批';
                $send->sendPlanMsg($audit_user,$info->month,$info->primaryKey,$name,3,1,$add_msg_content);
            }
            $this->msg(array('state' => 1, 'msgwords' => '个人计划修改成功', 'url' => '/admin/planMonth/index?group_id=1'));
        }
    }

    public function actionEditGroup()
    {
        $data = array();
        if (!$_POST) {
            $id = $this->get('id');
            if ($id) {
                $updateGroup = PlanMonthGroupUser::model()->getUserData($id);

                $this->render('updateGroup', array('updateGroup' => $updateGroup));
            } else {
                $this->render('L_GroupPlan', array('page' => $data));
            }
        }

        if ($_POST) {
            $id = Yii::app()->admin_user->uid;
            $authority = AdminUser::model()->getUserAuthority($id);

            foreach ($_POST['ids'] as $k => $v) {
                $list = PlanMonthGroup::model()->findByPk($_POST['ids'][$k]);
                $list->delete();
            }

            foreach ($_POST['csid'] as $k => $v) {
                $info = new PlanMonthGroup();
                $info->groupid = $_POST['groupid'];
                $info->cs_id = $_POST['csid'][$k];
                $info->weChat_num = $_POST['weChat_num'][$k];
                $info->fans_plan = $_POST['fans_plan'][$k];
                $info->output_plan = $_POST['output_plan'][$k];
                $info->create_time = strtotime("now");
                $info->update_time = strtotime("now");
                $info->month = $_POST['month'];
                if($authority == 0){
                    $info->status = 4;
                }elseif($authority == 2){
                    $info->status = 2;
                }
                $info->save();
            }

            $info = PlanMonthGroupUser::model()->findByPk($_POST['id']);
            $info->remark = $_POST['remark'];
            if($authority == 0){
                $info->status = 4;
            }elseif($authority == 2){
                $info->status = 2;
            }
            $info->update_time = strtotime("now");
            $info->save();

            $uid = Yii::app()->admin_user->uid;
            $log = new PlanMonthLog();
            if($info->update_time > $info->through_time && $info->through_time != 0){
                $mask = "计划变更";
            }else {
                $mask = "修改计划";
            }
            $log->addPlanMonthLog($_POST['id'],2,$info->groupid,$mask,$info->month);

            //发送消息
            $group = $info->groupid;
            $add_msg_content = '';
            if ($group) {
                $ghroup_info = AdminGroup::model()->findByPk($group);
                $group_name = $ghroup_info->groupname;
                //审核人员配置
                $audit_user = intval(Yii::app()->params['remind']['group_plan_auditor']);
                if ($info->through_time < $info->update_time && $info->through_time != 0) {
                    $add_msg_content .= '(计划变更)';
                }
                $add_msg_content .= '，需要您审批';
                $send = new SendMessage();
                $send->sendPlanMsg($audit_user, $info->month, $info->primaryKey, $group_name, 4, 1, $add_msg_content);

            }
            $this->msg(array('state' => 1, 'msgwords' => '组计划修改成功', 'url' => '/admin/planMonth/index?group_id=2'));
        }
    }


    public function actionAgree()
    {
        $id = $this->post('id');
        $month = $this->post('month');
        $type = $this->post('type');

        $uid = Yii::app()->admin_user->uid;
            $authority = AdminUser::model()->getUserAuthority($uid);
            $mask = '审批通过';
            if ($type == 'p') {
                $ret = PlanMonthUser::model()->findBySql('select id,through_time,month,update_time from plan_month_user where tg_uid=' . $id . ' and month=' . $month);
                if ($authority == 0 || $authority == 1) $ret->status = 4;
                if ($authority == 2) $ret->status = 2;
                if ($ret->status ==4) {
                    $ret->through_time = strtotime("now");
                }
                $ret->save();

                $log = new PlanMonthLog();
                $log->addPlanMonthLog($ret->id,1,$id,"审核通过",$ret->month);

                $old_through_time = $ret->through_time;
                //发送消息
                $info = AdminUser::model()->findByPk($id);
                $name = $info->csname_true;
                $add_mask = '';
                if ($old_through_time < $ret->update_time && $old_through_time > 0) {
                    $add_mask = '(计划变更)';
                }
                PlanMonth::model()->updateMonthStatus($id,$ret->month,$ret->status);
                //发送消息
                $send = new SendMessage();
                //是否需要审核人员审核
                $is_audit = intval(Yii::app()->params['remind']['Individual_plan_auditor']);
                //审核人员配置
                $audit_user = intval(Yii::app()->params['remind']['group_plan_auditor']);
                if ($ret->status == 4 ) {
                    $send->sendPlanMsg($id,$ret->month,$ret->id,$name,3,2,$add_mask.','.$mask);
                }
                if ($ret->status == 2 &&  $is_audit==1) {
                    $add_msg_content = $add_mask.'，需要您审批';
                    $send = new SendMessage();
                    $send->sendPlanMsg($audit_user,$ret->month,$ret->id,$name,3,1,$add_msg_content);
                }
            }

            if ($type == 'g') {
                $ret = PlanMonthGroupUser::model()->findBySql('select * from plan_month_group_user where groupid=' . $id . ' and month=' . $month);
                if ($authority == 0 || $authority == 1) $ret->status = 4;
                if ($authority == 2) $ret->status = 2;
                $ret->through_time = strtotime("now");
                $ret->save();

                $log = new PlanMonthLog();
                $log->addPlanMonthLog($ret->id,2,$id,"审核通过",$ret->month);

                $old_through_time = $ret->through_time;
                //发送消息
                $group_info = AdminGroup::model()->findByPk($id);
                $name = $group_info->groupname;
                $add_mask = '';
                if ($old_through_time < $ret->update_time && $old_through_time > 0) {
                    $add_mask = '(变更)';
                }
                //更改详情状态
                PlanMonthGroup::model()->updateMonthGroupStatus($id,$ret->month,$ret->status);
                //发送消息
                $send = new SendMessage();
                $send->sendPlanMsg($group_info->manager_id, $ret->month, $ret->id, $name, 4, 2, $add_mask . ',' . $mask);
            }


    }

    public function actionRefuse()
    {
        $status = $this->post('status');
        $id = $this->post('id');
        $month = $this->post('month');
        $type = $this->post('type');
        $unthrough_msg = trim($this->post('unthrough_msg'));


            $uid = Yii::app()->admin_user->uid;
            $authority = AdminUser::model()->getUserAuthority($uid);
            $mask = '审批未通过';
            if ($type == 'p') {
                $ret = PlanMonthUser::model()->findBySql('select * from plan_month_user where tg_uid=' . $id . ' and month=' . $month);
                if ($authority == 0 || $authority == 1) $ret->status = 3;
                if ($authority == 2) $ret->status = 1;
                $ret->unthrough_msg = $unthrough_msg;
                $ret->save();

                $log = new PlanMonthLog();
                $log->addPlanMonthLog($ret->id,1,$id,"审批未通过",$ret->month);

                $old_through_time = $ret->through_time;
                //发送消息

                $info = AdminUser::model()->findByPk($id);
                $name = $info->csname_true;
                $add_mask = '';
                if ($old_through_time < $ret->update_time && $old_through_time > 0) {
                    $add_mask = '(变更)';
                }
                PlanMonth::model()->updateMonthStatus($id,$ret->month,$ret->status);
                //发送消息
                $send = new SendMessage();
                $send->sendPlanMsg($id, $ret->month, $ret->id, $name, 3, 2, $add_mask . ',' . $mask);
            }

            if ($type == 'g') {
                $ret = PlanMonthGroupUser::model()->findBySql('select * from plan_month_group_user where groupid=' . $id . ' and month=' . $month);
                if ($authority == 0 || $authority == 1) $ret->status = 3;
                if ($authority == 2) $ret->status = 1;
                $ret->unthrough_msg = $unthrough_msg;
                $ret->save();

                $log = new PlanMonthLog();
                $log->addPlanMonthLog($ret->id,2,$id,"审核未通过",$ret->month);

                $old_through_time = $ret->unthrough_msg;
                //发送消息
                $info = AdminGroup::model()->findByPk($id);
                $name = $info->groupname;
                $add_mask = '';
                if ($old_through_time < $ret->update_time && $old_through_time > 0) {
                    $add_mask = '(变更)';
                }
                //更改详情状态
                PlanMonthGroup::model()->updateMonthGroupStatus($id,$ret->month,$ret->status);
                //发送消息
                $send = new SendMessage();
                $send->sendPlanMsg($id, $ret->month, $ret->id, $name, 4, 2, $add_mask . ',' . $mask);
            }
    }

    public function getExportData()
    {
        $where = '';
        $w_arr = array();
        $data_list = PlanMonthUser::model()->getTgName();

        if($this->get('date_start_person')){
            $date_start = strtotime($this->get('date_start_person'));
            $where .= ' and month='.$date_start;
            $w_arr['date_start_person'] = $this->get('date_start_person');
        }

        if( $this->get('cs_id')){
            $where .= ' and cs_id='.$this->get('cs_id');
        }

        if($this->get('status')== 1){
            $where .= ' and status=0';
        }else if($this->get('status')== 2){
            $where .= ' and status in(1,3)';
        }else if($this->get('status')== 3){
            $where .= ' and status in(2,4)';
        }

        if($this->get('user_id')){
            $where .= ' and tg_uid ='.$this->get('user_id');
        }

        $data = PlanMonth::model()->findAll('1 '.$where.' order by update_time desc');
        $uid = Yii::app()->admin_user->uid;

        $temp = array();
        $arr = array();

        //可查看人员权限
        $user_ids = AdminUser::model()->user_plan_data_authority($uid);
        foreach ($data_list as $k => $v) {
            $key = $v['tg_uid'] . "_" . $v['month'];
            if ($user_ids === 0) {
                $temp[] = $key;
                $arr[$key] = array('remark' => $v['remark'], 'id' => $v['id'], 'name' => $v['csname_true'], 'status' => $v['status'], 'tg_uid' => $v['tg_uid'], 'update_time' => $v['update_time'], 'through_time' => $v['through_time'],'unthrough_msg'=>$v['unthrough_msg']);
            } else {
                $user_ids[] = $uid;
                if (in_array($v['tg_uid'], $user_ids)) {
                    $temp[] = $key;
                    $arr[$key] = array('remark' => $v['remark'], 'id' => $v['id'], 'name' => $v['csname_true'], 'status' => $v['status'], 'tg_uid' => $v['tg_uid'], 'update_time' => $v['update_time'], 'through_time' => $v['through_time'],'unthrough_msg'=>$v['unthrough_msg']);

                }
            }
        }

        foreach ($data as $val) {
            $key = $val['tg_uid'] . "_" . $val['month'];
            if (in_array($key, $temp)) {
                $w_arr['list'][$arr[$key]['status'] . '_' . $key . '_p']['data'][] = array(
                    'fans_plan' => $val['fans_plan'],
                    'output_plan' => $val['output_plan'],
                    'weChat_num' => $val['weChat_num'],
                    'cs_id' => $val['cs_id']);

                $w_arr['list'][$arr[$key]['status'] . '_' . $key . '_p']['month'] = $val['month'];
                $w_arr['list'][$arr[$key]['status'] . '_' . $key . '_p']['status'] = $arr[$key]['status'];
                $w_arr['list'][$arr[$key]['status'] . '_' . $key . '_p']['update_time'] = $arr[$key]['update_time'];
                $w_arr['list'][$arr[$key]['status'] . '_' . $key . '_p']['through_time'] = $arr[$key]['through_time'];
                $w_arr['list'][$arr[$key]['status'] . '_' . $key . '_p']['num'] = count($w_arr['list'][$arr[$key]['status'] . '_' . $key . '_p']['data']);
                $w_arr['list'][$arr[$key]['status'] . '_' . $key . '_p']['tg_uid'] = $arr[$key]['tg_uid'];
                $w_arr['list'][$arr[$key]['status'] . '_' . $key . '_p']['id'] = $arr[$key]['id'];
                $w_arr['list'][$arr[$key]['status'] . '_' . $key . '_p']['remark'] = $arr[$key]['remark'];
                $w_arr['list'][$arr[$key]['status'] . '_' . $key . '_p']['name'] = $arr[$key]['name'];
                $w_arr['list'][$arr[$key]['status'] . '_' . $key . '_p']['unthrough_msg'] = $arr[$key]['unthrough_msg'];
            }
        }

        return $w_arr;
    }

    public function getGroupData($params = array())
    {
        $uid = Yii::app()->admin_user->uid;
        //获取个人计划评审人
        $group_checker = Yii::app()->params['remind']['group_plan_auditor'];
        $user_group = array();
        //不为审核人
        if ($group_checker != $uid) {
            $group = Dtable::toArr(AdminGroup::model()->findAll('manager_id=' . $uid));
            $user_group = array_column($group, 'groupid');
        }
        //是审核人
        if ($group_checker == $uid) {
            $group = Dtable::toArr(AdminGroup::model()->findAll());
            $user_group = array_column($group, 'groupid');
        }
        $data_list = PlanMonthGroupUser::model()->getTgName('');

        $where = '';
        $w_arr = array();

        if($this->get('date_start_group')){
            $date_start = strtotime($this->get('date_start_group'));
            $where .= ' and month='.$date_start;
            $w_arr['date_start_group'] = $this->get('date_start_group');
        }

        if( $this->get('cs_id')){
            $where .= ' and cs_id='.$this->get('cs_id');
        }

        if($this->get('status')== 1){
            $where .= ' and status=0';
        }else if($this->get('status')== 2){
            $where .= ' and status in(1,3)';
        }else if($this->get('status')== 3){
            $where .= ' and status in(2,4)';
        }

        if($this->get('groupid')){
            $where .= ' and groupid='.$this->get('groupid');
        }

        $data = PlanMonthGroup::model()->findAll('1 '.$where.' order by update_time desc');

        //用户可查看组数据
        $group_ids = AdminUser::model()->group_plan_data_authority($uid);

        $temp = array();
        $arr = array();
        foreach ($data_list as $v) {
            $key = $v['groupid'] . "_" . $v['month'];
            if ($group_ids === 0) {
                $temp[] = $key;
                $arr[$key] = array('remark' => $v['remark'], 'id' => $v['id'], 'name' => $v['groupname'], 'status' => $v['status'], 'update_time' => $v['update_time'], 'through_time' => $v['through_time'],'unthrough_msg'=>$v['unthrough_msg']);
            }else {
                if (in_array($v['groupid'], $group_ids)) {
                    $temp[] = $key;
                    $arr[$key] = array('remark' => $v['remark'], 'id' => $v['id'], 'name' => $v['groupname'], 'status' => $v['status'], 'update_time' => $v['update_time'], 'through_time' => $v['through_time'],'unthrough_msg'=>$v['unthrough_msg']);
                }
            }
        }

        foreach ($data as $val) {
            $key = $val['groupid'] . "_" . $val['month'];

            if (in_array($key, $temp)) {
                $w_arr['list'][$arr[$key]['status'] . '_' . $key . '_g']['data'][] = array(
                    'weChat_num' =>  $val['weChat_num'],
                    'fans_plan' =>  $val['fans_plan'],
                    'output_plan' =>  $val['output_plan'],
                    'cs_id' => $val['cs_id']);

                $w_arr['list'][$arr[$key]['status'] . '_' . $key . '_g']['month'] = $val['month'];
                $w_arr['list'][$arr[$key]['status'] . '_' . $key . '_g']['num'] = count($w_arr['list'][$arr[$key]['status'] . '_' . $key . '_g']['data']);
                $w_arr['list'][$arr[$key]['status'] . '_' . $key . '_g']['status'] = $arr[$key]['status'];
                $w_arr['list'][$arr[$key]['status'] . '_' . $key . '_g']['update_time'] = $arr[$key]['update_time'];
                $w_arr['list'][$arr[$key]['status'] . '_' . $key . '_g']['through_time'] = $arr[$key]['through_time'];
                $w_arr['list'][$arr[$key]['status'] . '_' . $key . '_g']['id'] = $arr[$key]['id'];
                $w_arr['list'][$arr[$key]['status'] . '_' . $key . '_g']['remark'] = $arr[$key]['remark'];
                $w_arr['list'][$arr[$key]['status'] . '_' . $key . '_g']['name'] = $arr[$key]['name'];
                $w_arr['list'][$arr[$key]['status'] . '_' . $key . '_g']['unthrough_msg'] = $arr[$key]['unthrough_msg'];
            }

        }

        return $w_arr;
    }

    public function getCheckData($array = array(), $params = array())
    {
        $arr = array();
        $id = Yii::app()->admin_user->uid;
        $authority = AdminUser::model()->getUserAuthority($id);
        //用户为组长及审核人时，获取组推广人员
        $group_user = array();
        if ($authority == 0) {
            $mamager = AdminGroup::model()->findAll('manager_id='.$id);
            $group = array();
            foreach ($mamager as $val){
                $group[] = $val['groupid'];
            }
            $group = implode(',',$group);
            $group_user = AdminUserGroup::model()->getUsersByGroups($group);
        }
        $uid = Yii::app()->admin_user->uid;
        //获取个人计划评审人
        $group_checker = Yii::app()->params['remind']['group_plan_auditor'];
        //获取个人计划评审人
        $personl_checker = Yii::app()->params['remind']['Individual_plan_auditor'];
        foreach ($array['list'] as $key => $value) {
            $first_num = substr($key, 0, 1);
            $key_array = explode('_',$key);
            $plan_user = $key_array[1];
            //组长及审核人
            if ($authority == 0) {
                //用户是否为组长组员
                $is_group = 0;
                if (in_array($plan_user,$group_user)) {
                    $is_group=1;
                }
                //用户为组长组员，则可查看未审核过的数据
                if ($is_group == 1 && $first_num == 0) {
                    $arr['list'][$key] = $value;
                }
            }
            //审核人
            if ($personl_checker==1 && $group_checker==$uid) {
                if ($first_num == 2) {
                    $arr['list'][$key] = $value;
                }
            }
            //组长
            if ($authority == 2) {
                if ($first_num == 0 ) {
                    $arr['list'][$key] = $value;
                }
            }
        }
        $temp = array();
        $uid = Yii::app()->admin_user->uid;
        //获取个人计划评审人
        $group_checker = Yii::app()->params['remind']['group_plan_auditor'];
        foreach ($params['list'] as $key => $value) {
            $first_number = substr($key, 0, 1);
            $key_array = explode('_',$key);
            $plan_user = $key_array[1];
            //组长及审核人
            if ($authority == 0) {
                //用户是否为组长组员
                $is_group = 0;
                if (in_array($plan_user,$group_user)) {
                    $is_group=1;
                }
                //用户为组长组员，则可查看未审核过的数据
                if ($is_group == 1 && $first_num == 0) {
                    $temp['list'][$key] = $value;
                }
            }
                //审核人
                if ($personl_checker==1 && $group_checker==$uid) {
                    if ($first_number == 2) {
                        $temp['list'][$key] = $value;
                    }
                }
                //组长
                if ($authority == 2) {
                    if ($first_number == 0 ) {
                        $temp['list'][$key] = $value;
                    }
            }
        }

        $check = array_merge_recursive($arr, $temp);

        return $check;
    }

    public function getLog()
    {
        $id = Yii::app()->admin_user->uid;
        $params['where'] = '';

        $user_ids = AdminUser::model()->user_plan_data_authority($id);
        $groups = AdminUser::model()->get_manager_group($id);
        if ($user_ids !== 0) {
            $user_ids[] = $id;
            if ($groups) {
                $params['where'] .= ' and (( relation_id in ('.implode(',',$user_ids).') and plan_type=1 ) or ( relation_id in ('.implode(',',$groups).') and plan_type=2 ))';
            }else {
                $params['where'] .= ' and relation_id='.$id.' and plan_type=1';
            }
        }

        if (trim($this->get('title'))) {
            $params['where'] .= " and(title like '%" . trim($this->get('title')) . "%') ";
        }

        $params['order'] = "  order by add_time desc";
        $params['pagebar'] = 1;
        $params['pagesize'] = 1000;
        $params['select'] = "*";
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(PlanMonthLog::model()->tableName())->listdata($params);
        $page['listdata']['url'] = urlencode('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"]);

        return $page;
    }

    public function actionGetGroupPlanTotal()
    {
        $group_id = intval($this->get('group_id'));
        $service = intval($this->get('service_id'));
        $month = strtotime($this->get('month') . '-01');
        $group_user = Dtable::toArr(AdminUserGroup::model()->findAll('groupid=' . $group_id));
        $users = array_column($group_user, 'sno');
        $group_service_palns = array();
        if ($users) {
            $week_plan = Dtable::toArr(PlanMonthUser::model()->findAll(' status=4 and tg_uid in (' . implode(',', $users) . ') and month=' . $month));
            $tg_uid = array_column($week_plan, 'tg_uid');
            if ($tg_uid) {
                $sql = 'select SUM(output_plan) AS total_output,SUM(fans_plan) AS total_fans,SUM(weChat_num) AS total_weChat_num, cs_id from plan_month 
                        where cs_id = ' . $service . ' and month=' . $month . ' and tg_uid in (' . implode(',', $tg_uid) . ') 
                        group by cs_id ';

                $plan_detail = Yii::app()->db->createCommand($sql)->queryAll();
                foreach ($plan_detail as $value) {
                    $group_service_palns = array(
                        'output' => $value['total_output'],
                        'fans_count' => $value['total_fans'],
                        'weChat_num' => $value['total_weChat_num'],
                    );
                }
            }
        }
        if (empty($plan_detail)) {
            $group_service_palns = array(
                'output' => 0,
                'fans_count' => 0,
                'weChat_num' => 0,
            );
        }
        die(json_encode($group_service_palns));

    }
}