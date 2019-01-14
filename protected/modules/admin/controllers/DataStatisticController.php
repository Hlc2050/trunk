<?php
/**
 * Created by PhpStorm.
 * User: lxj
 * Date: 2018/8/26
 * Time: 13:24
 */

class DataStatisticController extends AdminController
{
    public function actionIndex()
    {
        $param_group[0] = array('value'=>0,'txt'=>'客服部');
        $param_group[1] = array('value'=>1,'txt'=>'推广组');
        $param_group[2] = array('value' =>2, 'txt' => '推广人员');
        $page['params_groups'] = $param_group;
        $tap_id = $this->get('tab_id') ? intval($this->get('tab_id')):0;
        $page['tab_id'] = $tap_id;
        $service_group = Dtable::toArr(CustomerServiceManage::model()->findAll());
        $service = $page['service_group'] = array_combine(array_column($service_group, 'id'), array_column($service_group, 'cname'));
        switch ($tap_id) {
            case 0:
                $page['info'] = $this->getServiceData($service);
                break;
            case 1:
                $group = Dtable::toArr(AdminGroup::model()->findAll());
                $groups = $page['service_group'] = array_combine(array_column($group, 'groupid'), array_column($group, 'groupname'));
                $page['info'] = $this->getGroupData($service,$groups);
                break;
            case 2:
                $group = Dtable::toArr(AdminGroup::model()->findAll());
                $groups = $page['service_group'] = array_combine(array_column($group, 'groupid'), array_column($group, 'groupname'));
                $admins = Dtable::toArr(AdminUser::model()->findAll());
                $users = $page['service_user'] = array_combine(array_column($admins, 'csno'), array_column($admins, 'csname_true'));
                $page['info'] = $this->getUserData($service,$groups,$users);
                break;
        }

        $this->render('index', array('page' => $page));
    }



    public function actionServiceDetail()
    {
        $date = $this->get('date');
        $service_id = intval($this->get('service_id'));
        $service_info = Dtable::toArr(CustomerServiceManage::model()->findByPk($service_id));
        $param_plan['condition'] = ' date = '.$date.' and service_group ='.$service_id;
        $param_data['condition'] = ' date = '.$date.' and service_id ='.$service_id;
        $data_output = DataPracticalOutput::model()->findAll(
            array(
                'select' => 'output,service_id,date,group_id,tg_uid',
                'condition' => $param_data['condition'],
            )
        );
        $data_output = Dtable::toArr($data_output);
        //推广组实际产值
        $data_output_group = array();
        //用户实际产值
        $data_output_user = array();
        foreach ($data_output as $value) {
            if (!isset($data_output_group[$value['group_id']])) {
                $data_output_group[$value['group_id']] = 0;
            }
            $data_output_group[$value['group_id']]+=$value['output'];
            if (isset($data_output_user[$value['tg_uid']])) {
                $data_output_user[$value['tg_uid']] = 0;
            }
            $data_output_user[$value['tg_uid']]+=$value['output'];
        }
        $data_fans = DataPracticalFans::model()->findAll(
            array(
                'select' => 'fans,service_id,date,group_id,tg_uid',
                'condition' => $param_data['condition'],
            )
        );
        $data_fans = Dtable::toArr($data_fans);
        //推广组实际进粉
        $data_fans_group = array();
        //个人实际进粉
        $data_fans_user = array();
        foreach ($data_fans as $value) {
            if (!isset($data_fans_group[$value['group_id']])) {
                $data_fans_group[$value['group_id']] = 0;
            }
            $data_fans_group[$value['group_id']] += $value['fans'];
            if (!isset($data_fans_user[$value['tg_uid']])) {
                $data_fans_user[$value['tg_uid']] = 0;
            }
            $data_fans_user[$value['tg_uid']] += $value['fans'];
        }
        $group_plan = PlanWeekGroupDetail::model()->findAll(
            array(
                'select' => 'fans_count,output,service_group,date,group_id ',
                'condition' => $param_plan['condition'] .' and status=1',
            )
        );
        $group_plan = Dtable::toArr($group_plan);
        $user_plan = PlanWeekUserDetail::model()->findAll(
            array(
                'select' => 'fans_count,output,service_group,date,tg_uid ',
                'condition' => $param_plan['condition'].' and status=12',
            )
        );
        $user_plan = Dtable::toArr($user_plan);
        $page['service'] = array(
            'date'=>date('m-d',$date),
            'service_name'=>$service_info['cname'],
            'plan_fans'=>$group_plan ? array_sum(array_column($group_plan,'fans_count')):0,
            'plan_output'=>$group_plan ? array_sum(array_column($group_plan,'output')):0,
            'data_fans'=>$data_fans ? array_sum(array_column($data_fans,'fans')):0,
            'data_output'=>$data_output ? array_sum(array_column($data_output,'output')):0,
        );
        $page['service']['fans_radio'] = $page['service']['plan_fans']>0?round(($page['service']['data_fans']-$page['service']['plan_fans'])*100/$page['service']['plan_fans'],2):0;
        $page['service']['output_radio'] = $page['service']['plan_output']>0?round(($page['service']['data_output']-$page['service']['plan_output'])*100/$page['service']['plan_output'],2):0;
        //推广组数据
        foreach ($group_plan as $value) {
            $group_output = $data_output_group[$value['group_id']] ?  $data_output_group[$value['group_id']]:0;
            $group_fans = $data_fans_group[$value['group_id']] ?  $data_fans_group[$value['group_id']]:0;
            $page['group'][$value['group_id']] = array(
                'group_id'=>$value['group_id'],
                'plan_fans'=>$value['fans_count'],
                'plan_output'=>$value['output'],
                'date_output'=>$group_output,
                'data_fans'=>$group_fans,
                'fans_radio'=>$value['fans_count']>0?round(($group_fans-$value['fans_count'])*100/$value['fans_count'],2):0,
                'output_radio'=>$value['output']>0?round(($group_output-$value['output'])*100/$value['output'],2):0,
            );
        }
        $users = array_unique(array_column($user_plan,'tg_uid'));
        $groups = array_unique(array_column($group_plan,'group_id'));
        $user_group_id = array();
        if ($users) {
            $user_group = Dtable::toArr(AdminUserGroup::model()->findAll(' sno in ('.implode(',',$users).') group by sno'));
            $user_group_id = array_combine(array_column($user_group,'sno'),array_column($user_group,'groupid'));
        }
        //推广人员
        foreach ($user_plan as $value) {
            $user_output = $data_output_user[$value['tg_uid']] ?  $data_output_user[$value['tg_uid']]:0;
            $user_fans = $data_fans_user[$value['tg_uid']] ?  $data_fans_user[$value['tg_uid']]:0;
            $page['user'][$user_group_id[$value['tg_uid']]][$value['tg_uid']] = array(
                'tg_uid'=>$value['tg_uid'],
                'plan_fans'=>$value['fans_count'],
                'plan_output'=>$value['output'],
                'date_output'=>$user_output,
                'data_fans'=>$user_fans,
                'fans_radio'=>$value['fans_count']>0?round(($user_fans-$value['fans_count'])*100/$value['fans_count'],2):0,
                'output_radio'=>$value['output']>0?round(($user_output-$value['output'])*100/$value['output'],2):0,
            );
        }
        if ($users) {
            $admins = Dtable::toArr(AdminUser::model()->findAll('csno in ('.implode(',',$users).')'));
            $page['user_name'] = array_combine(array_column($admins,'csno'),array_column($admins,'csname_true'));
        }
        if ($groups) {
            $group = Dtable::toArr(AdminGroup::model()->findAll('groupid in ('.implode(',',$groups).')'));
            $page['group_name'] = array_combine(array_column($group,'groupid'),array_column($group,'groupname'));
        }
        $this->render('serviceDetail', array('page' => $page));
    }


    private function getServiceData($service)
    {
        $service_ids = array_keys($service);
        $date_limit = $this->getSearchDate();
        $param_plan['condition'] = ' 1 ';
        $param_data['condition'] = ' 1 ';
        if ($date_limit['start_date']) {
            $param_plan['condition'] .= ' and date >= '.$date_limit['start_date'];
            $param_data['condition'] .= ' and date >= '.$date_limit['start_date'];
        }
        if ($date_limit['end_date']) {
            $param_plan['condition'] .= ' and date <= '.$date_limit['end_date'];
            $param_data['condition'] .= ' and date <= '.$date_limit['end_date'];
        }
        if ($this->get('service_id')) {
            $param_plan['condition'] .= ' and service_group = '.intval($this->get('service_id'));
            $param_data['condition'] .= ' and service_id = '.intval($this->get('service_id'));
        }else {
            $param_plan['condition'] .= ' and service_group in ('.implode(',',$service_ids).')';
            $param_data['condition'] .= ' and service_id in ('.implode(',',$service_ids).')';
        }
        $data_output = DataPracticalOutput::model()->findAll(
            array(
                'select' => 'sum(output) as output,service_id,date ',
                'condition' => $param_data['condition'],
                'group' => 'service_id,date',
            )
        );
        $data_fans = DataPracticalFans::model()->findAll(
            array(
                'select' => 'sum(fans) as fans,service_id,date ',
                'condition' => $param_data['condition'],
                'group' => 'service_id,date',
            )
        );
        $plan = PlanWeekGroupDetail::model()->findAll(
            array(
                'select' => 'sum(fans_count) as fans_count,sum(output) as output,service_group,date ',
                'condition' => $param_plan['condition'].' and status=1',
                'group' => 'service_group,date',
            )
        );
        $service_output = array();
        $service_fans = array();
        $service_plan = array();
        foreach ($data_output as $value) {
            $service_output[$value['service_id']][$value['date']] = $value['output'];
        }
        foreach ($data_fans as $value) {
            $service_fans[$value['service_id']][$value['date']] = $value['fans'];
        }
        $total_plan_fans = 0;
        $total_plan_output = 0;
        $total_data_fans = 0;
        $total_data_output = 0;
        foreach ($plan as $value) {
            $date_fans = $service_fans[$value['service_group']][$value['date']] ? $service_fans[$value['service_group']][$value['date']]:0;
            $date_output = $service_output[$value['service_group']][$value['date']] ? $service_output[$value['service_group']][$value['date']]:0;
            $service_plan['list'][] = array(
                'service_name' => $service[$value['service_group']],
                'service_group' => $value['service_group'],
                'date' => date('m-d',$value['date']),
                'date_time' => $value['date'],
                'plan_fans' => $value['fans_count'],
                'plan_output' => $value['output'],
                'data_fans' => $date_fans,
                'date_output' => $date_output,
                'fans_radio' => $value['fans_count']>0 ?round(($date_fans-$value['fans_count'])*100/$value['fans_count'],2):'0',
                'output_radio' => $value['output']>0 ?round(($date_output-$value['output'])*100/$value['output'],2):'0',
            );
            $total_plan_fans +=  $value['fans_count'];
            $total_plan_output +=  $value['output'];
            $total_data_fans += $date_fans;
            $total_data_output += $date_output;
        }
        $service_plan['total']['fans_radio'] =  $total_plan_fans>0 ?round(($total_data_fans-$total_plan_fans)*100/$total_plan_fans,2):'0';
        $service_plan['total']['output_radio'] =  $total_plan_output>0 ?round(($total_data_output-$total_plan_output)*100/$total_plan_output,2):'0';
        $service_plan['total']['plan_fans'] =  $total_plan_fans;
        $service_plan['total']['plan_output'] =  $total_plan_output;
        $service_plan['total']['data_output'] =  $total_data_output;
        $service_plan['total']['data_fans'] =  $total_data_fans;
        $service_plan['start_date'] = $date_limit['start_date'];
        $service_plan['end_date'] = $date_limit['end_date'];
        $service_plan['service_id'] = $this->get('service_id')? intval($this->get('service_id')):0;
        return $service_plan;
    }


    private function getGroupData($service,$groups)
    {
        $service_ids = array_keys($service);
        $group_ids = array_keys($groups);
        $date_limit = $this->getSearchDate();
        $param_plan['condition'] = ' 1 ';
        $param_data['condition'] = ' 1 ';
        if ($date_limit['start_date']) {
            $param_plan['condition'] .= ' and date >= '.$date_limit['start_date'];
            $param_data['condition'] .= ' and date >= '.$date_limit['start_date'];
        }
        if ($date_limit['end_date']) {
            $param_plan['condition'] .= ' and date <= '.$date_limit['end_date'];
            $param_data['condition'] .= ' and date <= '.$date_limit['end_date'];
        }
        if ($this->get('service_id')) {
            $param_plan['condition'] .= ' and service_group = '.intval($this->get('service_id'));
            $param_data['condition'] .= ' and service_id = '.intval($this->get('service_id'));
        }else {
            $param_plan['condition'] .= ' and service_group in ('.implode(',',$service_ids).')';
            $param_data['condition'] .= ' and service_id in ('.implode(',',$service_ids).')';
        }
        if ($this->get('group_id')) {
            $param_plan['condition'] .= ' and group_id = '.intval($this->get('group_id'));
            $param_data['condition'] .= ' and group_id = '.intval($this->get('group_id'));
        }else {
            $param_plan['condition'] .= ' and group_id in ('.implode(',',$group_ids).')';
            $param_data['condition'] .= ' and group_id in ('.implode(',',$group_ids).')';
        }
        $data_output = DataPracticalOutput::model()->findAll(
            array(
                'select' => 'sum(output) as output,service_id,group_id,date ',
                'condition' => $param_data['condition'],
                'group' => 'service_id,group_id,date',
            )
        );
        $data_fans = DataPracticalFans::model()->findAll(
            array(
                'select' => 'sum(fans) as fans,service_id,group_id,date ',
                'condition' => $param_data['condition'],
                'group' => 'service_id,group_id,date',
            )
        );
        $plan = PlanWeekGroupDetail::model()->findAll(
            array(
                'select' => 'sum(fans_count) as fans_count,sum(output) as output,service_group,group_id,date ',
                'condition' => $param_plan['condition'] .' and status=1',
                'group' => 'service_group,group_id,date',
            )
        );
        $service_output = array();
        $service_fans = array();
        $service_plan = array();
        foreach ($data_output as $value) {
            $service_output[$value['service_id']][$value['group_id']][$value['date']] = $value['output'];
        }
        foreach ($data_fans as $value) {
            $service_fans[$value['service_id']][$value['group_id']][$value['date']] = $value['fans'];
        }
        $total_plan_fans = 0;
        $total_plan_output = 0;
        $total_data_fans = 0;
        $total_data_output = 0;
        foreach ($plan as $value) {
            $date_fans = $service_fans[$value['service_group']][$value['group_id']][$value['date']] ? $service_fans[$value['service_group']][$value['group_id']][$value['date']]:0;
            $date_output = $service_output[$value['service_group']][$value['group_id']][$value['date']] ? $service_output[$value['service_group']][$value['group_id']][$value['date']]:0;
            $service_plan['list'][] = array(
                'service_name' => $service[$value['service_group']],
                'service_group' => $value['service_group'],
                'group_id' => $value['group_id'],
                'group_name' => $groups[$value['group_id']],
                'date' => date('m-d',$value['date']),
                'date_time' => $value['date'],
                'plan_fans' => $value['fans_count'],
                'plan_output' => $value['output'],
                'data_fans' => $date_fans,
                'date_output' => $date_output,
                'fans_radio' => $value['fans_count']>0 ?round(($date_fans-$value['fans_count'])*100/$value['fans_count'],2):'0',
                'output_radio' => $value['output']>0 ?round(($date_output-$value['output'])*100/$value['output'],2):'0',
            );
            $total_plan_fans +=  $value['fans_count'];
            $total_plan_output +=  $value['output'];
            $total_data_fans += $date_fans;
            $total_data_output += $date_output;
        }
        $service_plan['total']['fans_radio'] =  $total_plan_fans>0 ?round(($total_data_fans-$total_plan_fans)*100/$total_plan_fans,2):'0';
        $service_plan['total']['output_radio'] =  $total_plan_output>0 ?round(($total_data_output-$total_plan_output)*100/$total_plan_output,2):'0';
        $service_plan['total']['plan_fans'] =  $total_plan_fans;
        $service_plan['total']['plan_output'] =  $total_plan_output;
        $service_plan['total']['data_output'] =  $total_data_output;
        $service_plan['total']['data_fans'] =  $total_data_fans;
        $service_plan['start_date'] = $date_limit['start_date'];
        $service_plan['end_date'] = $date_limit['end_date'];
        $service_plan['service_id'] = $this->get('service_id')? intval($this->get('service_id')):0;
        return $service_plan;
    }

    private function getUserData($service,$groups,$users)
    {
        $service_ids = array_keys($service);
        $user_ids = array_keys($users);
        $date_limit = $this->getSearchDate();
        $user_group_id = array();
        if ($user_ids) {
            $user_group = Dtable::toArr(AdminUserGroup::model()->findAll(' sno in ('.implode(',',$user_ids).') group by sno'));
            $user_group_id = array_combine(array_column($user_group,'sno'),array_column($user_group,'groupid'));
        }
        $param_plan['condition'] = ' 1 ';
        $param_data['condition'] = ' 1 ';
        if ($date_limit['start_date']) {
            $param_plan['condition'] .= ' and date >= '.$date_limit['start_date'];
            $param_data['condition'] .= ' and date >= '.$date_limit['start_date'];
        }
        if ($date_limit['end_date']) {
            $param_plan['condition'] .= ' and date <= '.$date_limit['end_date'];
            $param_data['condition'] .= ' and date <= '.$date_limit['end_date'];
        }
        if ($this->get('service_id')) {
            $param_plan['condition'] .= ' and service_group = '.intval($this->get('service_id'));
            $param_data['condition'] .= ' and service_id = '.intval($this->get('service_id'));
        }else {
            $param_plan['condition'] .= ' and service_group in ('.implode(',',$service_ids).')';
            $param_data['condition'] .= ' and service_id in ('.implode(',',$service_ids).')';
        }
        if ($this->get('group_id')) {
            $select_group = intval($this->get('group_id'));
            $adminGroup = Dtable::toArr(AdminUserGroup::model()->findAll(' groupid='.$select_group));
            $group_user = array_unique(array_column($adminGroup,'sno'));
            if ($group_user) {
                $param_plan['condition'] .= ' and tg_uid in ('.implode(',',$group_user).')';
                $param_data['condition'] .= ' and tg_uid in ('.implode(',',$group_user).')';
            }
        }
        if ($this->get('user_id')) {
            $param_plan['condition'] .= ' and tg_uid = '.intval($this->get('user_id'));
            $param_data['condition'] .= ' and tg_uid = '.intval($this->get('user_id'));
        }
        $data_output = DataPracticalOutput::model()->findAll(
            array(
                'select' => 'sum(output) as output,service_id,tg_uid,date ',
                'condition' => $param_data['condition'],
                'group' => 'service_id,tg_uid,date',
            )
        );
        $data_fans = DataPracticalFans::model()->findAll(
            array(
                'select' => 'sum(fans) as fans,service_id,tg_uid,date ',
                'condition' => $param_data['condition'],
                'group' => 'service_id,tg_uid,date',
            )
        );
        $plan = PlanWeekUserDetail::model()->findAll(
            array(
                'select' => 'fans_count,output,service_group,tg_uid,date ',
                'condition' => $param_plan['condition'].' and status=12',
            )
        );
        $service_output = array();
        $service_fans = array();
        $service_plan = array();
        foreach ($data_output as $value) {
            $service_output[$value['service_id']][$value['tg_uid']][$value['date']] = $value['output'];
        }
        foreach ($data_fans as $value) {
            $service_fans[$value['service_id']][$value['tg_uid']][$value['date']] = $value['fans'];
        }
        $total_plan_fans = 0;
        $total_plan_output = 0;
        $total_data_fans = 0;
        $total_data_output = 0;
        foreach ($plan as $value) {
            $date_fans = $service_fans[$value['service_group']][$value['tg_uid']][$value['date']] ? $service_fans[$value['service_group']][$value['tg_uid']][$value['date']]:0;
            $date_output = $service_output[$value['service_group']][$value['tg_uid']][$value['date']] ? $service_output[$value['service_group']][$value['tg_uid']][$value['date']]:0;
            $service_plan['list'][] = array(
                'service_name' => $service[$value['service_group']],
                'service_group' => $value['service_group'],
                'group_id' => $user_group_id[$value['tg_uid']],
                'group_name' => $groups[$user_group_id[$value['tg_uid']]],
                'user_id' => $value['tg_uid'],
                'user_name' => $users[$value['tg_uid']],
                'date' => date('m-d',$value['date']),
                'date_time' => $value['date'],
                'plan_fans' => $value['fans_count'],
                'plan_output' => $value['output'],
                'data_fans' => $date_fans,
                'date_output' => $date_output,
                'fans_radio' => $value['fans_count']>0 ?round(($date_fans-$value['fans_count'])*100/$value['fans_count'],2):'0',
                'output_radio' => $value['output']>0 ?round(($date_output-$value['output'])*100/$value['output'],2):'0',
            );
            $total_plan_fans +=  $value['fans_count'];
            $total_plan_output +=  $value['output'];
            $total_data_fans += $date_fans;
            $total_data_output += $date_output;
        }
        $service_plan['total']['fans_radio'] =  $total_plan_fans>0 ?round(($total_data_fans-$total_plan_fans)*100/$total_plan_fans,2):'0';
        $service_plan['total']['output_radio'] =  $total_plan_output>0 ?round(($total_data_output-$total_plan_output)*100/$total_plan_output,2):'0';
        $service_plan['total']['plan_fans'] =  $total_plan_fans;
        $service_plan['total']['plan_output'] =  $total_plan_output;
        $service_plan['total']['data_output'] =  $total_data_output;
        $service_plan['total']['data_fans'] =  $total_data_fans;
        $service_plan['start_date'] = $date_limit['start_date'];
        $service_plan['end_date'] = $date_limit['end_date'];
        $service_plan['service_id'] = $this->get('service_id')? intval($this->get('service_id')):0;
        return $service_plan;
    }

    private function getSearchDate()
    {
        $date = array();
        $start_date = $this->get('start_date') ? strtotime($this->get('start_date')):0;
        $end_date = $this->get('end_date') ? strtotime($this->get('end_date')):time();
        $date['start_date'] = $start_date;
        $date['end_date'] = $end_date;
        return $date;
    }


}