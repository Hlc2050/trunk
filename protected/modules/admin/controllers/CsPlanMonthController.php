<?php
/**
 * 客服部月目标控制器
 * User: lxj
 * Date: 2018/8/30
 * Time: 10:55
 */
class CsPlanMonthController extends AdminController{
    public function actionIndex(){
        $page = $this->getExprotData();

        $service_id = $this->get('service_id');
        if($service_id != ''){
            $this->render('location_deviation',array('page'=>$page));
        }else{
            $page['params_groups'] = array(array('value'=>0,'txt'=>'整体'));
            $this->render('index',array('page'=>$page));
        }

    }

    public function getExprotData(){
        $date = $this->getDateLimit();
        $start_date = $date['start_date'];
        $end_date = $date['end_date'];
        $page = array();
        $params['condition'] = ' 1 ';
        $params['condition'] .= ' and month='.$start_date;
        $params['condition'] .= ' and status=4';
        $params['group'] = 'cs_id';
        $params['order'] = "  month ";
        $params['select'] = " sum(fans_plan) as fans_plan , sum(output_plan) as output_plan,cs_id ";
        //计划值
        $plan = Dtable::toArr(PlanMonth::model()->findAll($params));
        //实际进粉值
        $sql_f = 'select sum(a.fans) as fans,a.service_id from data_practical_fans as a WHERE date between '.$start_date.' and '.$end_date.' group by service_id';
        $fans = Yii::app()->db->createCommand($sql_f)->queryAll();
        $service_fans = array();
        foreach ($fans as $value) {
            $service_fans[$value['service_id']] = $value['fans'];
        }
        //实际产值
        $sql_o = 'select sum(a.output) as output,a.service_id from data_practical_output as a  WHERE date between '.$start_date.' and '.$end_date.' group by service_id';
        $outpus = Yii::app()->db->createCommand($sql_o)->queryAll();
        $service_output = array();
        foreach ($outpus as $value) {
            $service_output[$value['service_id']] = $value['output'];
        }
        $service_group = Dtable::toArr(CustomerServiceManage::model()->findAll());
        $service = $page['service_group'] = array_combine(array_column($service_group, 'id'), array_column($service_group, 'cname'));
        $service_plan = array();
        foreach ($plan as $value) {
            $date_fans = $service_fans[$value['cs_id']]? $service_fans[$value['cs_id']]:0;
            $date_output = $service_output[$value['cs_id']]? $service_output[$value['cs_id']]:0;
            $service_plan['list'][] = array(
                'service_name' => $service[$value['cs_id']],
                'service_group' => $value['cs_id'],
                'fans' => $value['fans_plan'],
                'output' => $value['output_plan'],
                'data_fans' => $date_fans,
                'date_output' => $date_output,
                'fans_radio' => $value['fans_plan']>0 ?round($date_fans*100/$value['fans_plan'],2):'0',
                'output_radio' => $value['output_plan']>0 ?round($date_output*100/$value['output_plan'],2):'0',
            );
        }
        $service_plan['start_date'] = $start_date;
        return $service_plan;
    }

    /**
     * 获取所选月份开始和结束日期
     */
    private function getDateLimit()
    {
        $ser_time = date('Y-m-d',time());
        $month = $this->get('start_date');
        if($month){
            $ser_time = $month.'-01';
        }
        $start_date = strtotime(date('Y-m-01', strtotime($ser_time)));
        $end_date = strtotime(date('Y-m-d', strtotime(date('Y-m-01', strtotime($ser_time)) . ' +1 month -1 day')));
        return $date = array('start_date'=>$start_date,'end_date'=>$end_date);
    }

    public function actionGetServiceDetail()
    {
        $date = $this->getDateLimit();
        $start_date = $date['start_date'];
        $end_date = $date['end_date'];
        $page['month'] = date('m',$start_date);
        $date = $this->get('date');
        $service_id = intval($this->get('service_id'));
        $service_info = Dtable::toArr(CustomerServiceManage::model()->findByPk($service_id));
        $param_plan['condition'] = ' month = '.$start_date.' and cs_id ='.$service_id;
        $param_data['condition'] = ' date between  '.$start_date.' and '.$end_date;
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
        $group_plan = PlanMonthGroup::model()->findAll(
            array(
                'select' => 'fans_plan,output_plan,cs_id,groupid ',
                'condition' => $param_plan['condition'].' and status=4',
            )
        );
        $group_plan = Dtable::toArr($group_plan);
        $user_plan = PlanMonth::model()->findAll(
            array(
                'select' => 'fans_plan,output_plan,cs_id,tg_uid ',
                'condition' => $param_plan['condition'].' and status=4',
            )
        );
        $user_plan = Dtable::toArr($user_plan);
        $page['service'] = array(
            'date'=>date('m-d',$date),
            'service_name'=>$service_info['cname'],
            'plan_fans'=>$user_plan ? array_sum(array_column($user_plan,'fans_plan')):0,
            'plan_output'=>$user_plan ? array_sum(array_column($user_plan,'output_plan')):0,
            'data_fans'=>$data_fans ? array_sum(array_column($data_fans,'fans')):0,
            'data_output'=>$data_output ? array_sum(array_column($data_output,'output')):0,
        );
        $page['service']['fans_radio'] = $page['service']['plan_fans']>0?round(($page['service']['data_fans'])*100/$page['service']['plan_fans'],2):0;
        $page['service']['output_radio'] = $page['service']['plan_output']>0?round(($page['service']['data_output'])*100/$page['service']['plan_output'],2):0;
        //推广组数据
        foreach ($group_plan as $value) {
            $group_output = $data_output_group[$value['groupid']] ?  $data_output_group[$value['groupid']]:0;
            $group_fans = $data_fans_group[$value['groupid']] ?  $data_fans_group[$value['groupid']]:0;
            $page['group'][$value['groupid']] = array(
                'group_id'=>$value['groupid'],
                'plan_fans'=>$value['fans_plan'],
                'plan_output'=>$value['output_plan'],
                'date_output'=>$group_output,
                'data_fans'=>$group_fans,
                'fans_radio'=>$value['fans_plan']>0?round($group_fans*100/$value['fans_plan'],2):0,
                'output_radio'=>$value['output_plan']>0?round($group_output*100/$value['output_plan'],2):0,
            );
        }
        $users = array_unique(array_column($user_plan,'tg_uid'));
        $groups = array_unique(array_column($group_plan,'groupid'));
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
                'plan_fans'=>$value['fans_plan'],
                'plan_output'=>$value['output_plan'],
                'date_output'=>$user_output,
                'data_fans'=>$user_fans,
                'fans_radio'=>$value['fans_plan']>0?round($user_fans*100/$value['fans_plan'],2):0,
                'output_radio'=>$value['output_plan']>0?round($user_output*100/$value['output_plan'],2):0,
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
        $this->render('location_deviation', array('page' => $page));
    }

}