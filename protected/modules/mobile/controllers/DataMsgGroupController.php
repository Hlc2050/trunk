<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/26
 * Time: 18:15
 */

class DataMsgGroupController extends AdminController
{
    public function actionIndex()
    {
        $user_id = Yii::app()->mobile->uid;
        $manage_group = Dtable::toArr(AdminGroup::model()->findAll("manager_id=$user_id"));
        $groups = $page['service_group'] = array_combine(array_column($manage_group, 'groupid'), array_column($manage_group, 'manager_id'));
        $tap_id = $_REQUEST['tab_id'] ? intval($_REQUEST['tab_id']):0;
        $page['tab_id'] = $tap_id;
        $service_group = Dtable::toArr(CustomerServiceManage::model()->findAll());
        $service = $page['service_group'] = array_combine(array_column($service_group, 'id'), array_column($service_group, 'cname'));
        $page['service_group'] = $service;
        switch ($tap_id) {
            case 0:
                $page['info'] = $this->getGroupServiceData($groups);
                break;
            case 1:
                $page['info'] = $this->getGroupServiceData($groups, 2);
                break;
            case 2:
                $page['info'] = $this->getServicePlanData($service, $groups);
                break;

        }
        $this->render('index', array('page' => $page));
    }

    public function actionGroupUserData()
    {
        $service_id = intval($_GET['service_id']);
        $service_group = Dtable::toArr(CustomerServiceManage::model()->findByPk($service_id));
        $user_id = Yii::app()->mobile->uid;
        $manage_group = Dtable::toArr(AdminGroup::model()->findAll("manager_id=$user_id"));
        $groups  = array_combine(array_column($manage_group, 'groupid'), array_column($manage_group, 'groupname'));
        $page['service_info'] = $service_group;
        $page['group_info'] = $groups;
        if ($groups) {
            $user_groups =  Dtable::toArr(AdminUserGroup::model()->findAll('groupid in ('.implode(',',array_keys($groups)).') group by sno'));
            $page['user_group'] = array_combine(array_column($user_groups, 'sno'), array_column($user_groups, 'groupid'));
            $user_ids = array_unique(array_column($user_groups,'sno'));
            if ($user_ids) {
                $admin = Dtable::toArr(AdminUser::model()->findAll('csno in ('.implode(',',$user_ids).')'));
                $users = $page['users'] = array_combine(array_column($admin, 'csno'), array_column($admin, 'csname_true'));
                $data_type = $_GET['data_type'] ? intval($_GET['data_type']):1;
                $page['data_type'] = $data_type;
                $page['info'] = $this->gerServiceUserData($users,1);
            }
        }
        $this->render('groupUserData', array('page' => $page));
    }

    private function getGroupServiceData($groups,$data_type=1)
    {
        $date = $_REQUEST['date'] ? $_REQUEST['date']:strtotime(date('Y-m-d',strtotime('-1 day'))) ;
        $order_type = $_REQUEST['order_type'] ? intval($_REQUEST['order_type'] ):1;
        $sort = $order_type == 1 ? SORT_ASC:SORT_DESC;
        if ($groups) {
            $group_ids = array_keys($groups);
            $param_plan['condition'] = ' 1 ';
            $param_data['condition'] = ' 1 ';
            $param_plan['condition'] .= ' and date = '.$date;
            $param_data['condition'] .= ' and date = '.$date;
            $param_plan['condition'] .= ' and group_id in ('.implode(',',$group_ids).')';
            $param_data['condition'] .= ' and group_id in ('.implode(',',$group_ids).')';
            $data_output = DataPracticalOutput::model()->findAll(
                array(
                    'select' => 'sum(output) as output,service_id ',
                    'condition' => $param_data['condition'],
                    'group' => 'service_id',
                )
            );
            $data_fans = DataPracticalFans::model()->findAll(
                array(
                    'select' => 'sum(fans) as fans,service_id ',
                    'condition' => $param_data['condition'],
                    'group' => 'service_id',
                )
            );
            $plan = PlanWeekGroupDetail::model()->findAll(
                array(
                    'select' => 'sum(fans_count) as fans_count,sum(output) as output,service_group ',
                    'condition' => $param_plan['condition'].' and status=1',
                    'group' => 'service_group',
                )
            );
            $service_output = array();
            $service_fans = array();
            $service_plan = array();
            foreach ($data_output as $value) {
                $service_output[$value['service_id']] = $value['output'];
            }
            foreach ($data_fans as $value) {
                $service_fans[$value['service_id']] = $value['fans'];
            }
            foreach ($plan as $value) {
                $date_fans = $service_fans[$value['service_group']] ? $service_fans[$value['service_group']]:0;
                $date_output = $service_output[$value['service_group']]? $service_output[$value['service_group']]:0;
                $service_plan['list'][] = array(
                    'service_group' => $value['service_group'],
                    'plan_fans' => $value['fans_count'],
                    'plan_output' => $value['output'],
                    'data_fans' => $date_fans,
                    'data_output' => $date_output,
                    'fans_radio' => $value['fans_count']>0 ?round(($date_fans-$value['fans_count'])*100/$value['fans_count'],2):'0',
                    'output_radio' => $value['output']>0 ?round(($date_output-$value['output'])*100/$value['output'],2):'0',
                    'output_dif' => $date_output-$value['output'],
                    'fans_dif' => $date_fans-$value['fans_count'],
                );
            }

            if ($data_type == 1) {
                array_multisort(array_column($service_plan['list'],'fans_dif'),$sort,$service_plan['list']);
            }
            if ($data_type == 2) {
                array_multisort(array_column($service_plan['list'],'output_dif'),$sort,$service_plan['list']);
            }
        }
        $service_plan['date'] = $date;
        $service_plan['order_type'] = $order_type;
        return $service_plan;
    }


    private function getServicePlanData($service,$groups)
    {
        $date = $_REQUEST['date'] ? $_REQUEST['date']:strtotime(date('Y-m-d',strtotime('-1 day'))) ;
        if ($groups) {
            $service_ids = array_keys($service);
            $param_plan['condition'] = ' 1 ';
            $param_data['condition'] = ' 1 ';
            $start_date = $date-3*24*60*60;
            $param_data['condition'] .= ' and ( date between '.$start_date.' and  '.$date.')';
            $param_plan['condition'] .= ' and date = '.$date;
            $param_data['condition'] .= ' and group_id in ('.implode(',',array_keys($groups)).')';
            $param_plan['condition'] .= ' and group_id in ('.implode(',',array_keys($groups)).')';
            $param_plan['condition'] .= ' and service_group in ('.implode(',',$service_ids).')';
            $param_data['condition'] .= ' and service_id in ('.implode(',',$service_ids).')';
            $service_output = array();
            $service_fans = array();
            $service_plan = array();
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
                    'select' => 'sum(fans_count) as fans_count,sum(output) as output,service_group ',
                    'condition' => $param_plan['condition'].' and status=1',
                    'group' => 'service_group',
                )
            );

            foreach ($data_output as $value) {
                $service_output[$value['service_id']][$value['date']] = $value['output'];
            }
            foreach ($data_fans as $value) {
                $service_fans[$value['service_id']][$value['date']] = $value['fans'];
            }
            foreach ($plan as $value) {
                $pre_data = array();
                $total_fans = 0;
                $total_output = 0;
                for ($i=0;$i<3;$i++) {
                    $date_i = $start_date+$i*24*60*60;
                    $date_fans = $service_fans[$value['service_group']][$date_i] ? $service_fans[$value['service_group']][$date_i]:0;
                    $date_output = $service_output[$value['service_group']][$date_i]? $service_output[$value['service_group']][$date_i]:0;
                    $total_fans += $date_fans;
                    $total_output += $date_output;
                    $pre_data[] = array(
                        'date' =>$date_i,
                        'date_str' =>date('m-d',$date_i),
                        'date_fans' =>$date_fans,
                        'date_output' =>$date_output,
                    );
                }
                $service_plan['list'][] = array(
                    'service_name' => $service[$value['service_group']],
                    'service_group' => $value['service_group'],
                    'plan_fans' => $value['fans_count'],
                    'plan_output' => $value['output'],
                    'pre_data' => $pre_data,
                    'fans_avg' => round($total_fans/3,2),
                    'output_avg' => round($total_output/3,2),
                );
            }
        }
        $service_plan['date'] = $date;
        return $service_plan;
    }


    private function gerServiceUserData($users,$data_type)
    {
        $service_id = intval($_GET['service_id']);
        $user_ids = array_keys($users);
        $param_plan['condition'] = ' 1 ';
        $param_data['condition'] = ' 1 ';
        $date = $_REQUEST['date'] ? $_REQUEST['date']:strtotime(date('Y-m-d',strtotime('-1 day'))) ;
        $param_data['condition'] .= ' and date = '.$date;
        $param_plan['condition'] .= ' and date = '.$date;
        $param_data['condition'] .= ' and service_id ='.$service_id;
        $param_plan['condition'] .= ' and service_group ='.$service_id;
        $param_data['condition'] .= ' and tg_uid in ('.implode(',',$user_ids).')';
        $param_plan['condition'] .= ' and tg_uid in ('.implode(',',$user_ids).')';
        $data_output = DataPracticalOutput::model()->findAll(
            array(
                'select' => 'sum(output) as output,tg_uid ',
                'condition' => $param_data['condition'],
                'group' => 'tg_uid',
            )
        );
        $data_fans = DataPracticalFans::model()->findAll(
            array(
                'select' => 'sum(fans) as fans,tg_uid ',
                'condition' => $param_data['condition'],
                'group' => 'tg_uid',
            )
        );
        $plan = PlanWeekUserDetail::model()->findAll(
            array(
                'select' => 'sum(fans_count) as fans_count,sum(output) as output,tg_uid ',
                'condition' => $param_plan['condition'].' and status=12',
                'group' => 'tg_uid',
            )
        );
        $service_output = array();
        $service_fans = array();
        $service_plan = array();
        foreach ($data_output as $value) {
            $service_output[$value['tg_uid']] = $value['output'];
        }
        foreach ($data_fans as $value) {
            $service_fans[$value['tg_uid']] = $value['fans'];
        }
        foreach ($plan as $value) {
            $date_fans = $service_fans[$value['tg_uid']] ? $service_fans[$value['tg_uid']]:0;
            $date_output = $service_output[$value['tg_uid']]? $service_output[$value['tg_uid']]:0;
            $service_plan['list'][] = array(
                'user_name' => $users[$value['tg_uid']],
                'tg_uid' => $value['tg_uid'],
                'plan_fans' => $value['fans_count'],
                'plan_output' => $value['output'],
                'data_fans' => $date_fans,
                'data_output' => $date_output,
                'fans_radio' => $value['fans_count']>0 ?round(($date_fans-$value['fans_count'])*100/$value['fans_count'],2):'0',
                'output_radio' => $value['output']>0 ?round(($date_output-$value['output'])*100/$value['output'],2):'0',
                'output_dif' => $date_output-$value['output'],
                'fans_dif' => $date_fans-$value['fans_count'],
            );
        }
        $order_type = $_REQUEST['order_type'] ? intval($_REQUEST['order_type'] ):1;
        $sort = $order_type == 1 ? SORT_ASC:SORT_DESC;
        if ($data_type == 1) {
            array_multisort(array_column($service_plan['list'],'fans_dif'),$sort,$service_plan['list']);
        }
        if ($data_type == 2) {
            array_multisort(array_column($service_plan['list'],'output_dif'),$sort,$service_plan['list']);
        }
        $service_plan['date'] = $date;
        $service_plan['order_type'] = $order_type;
        return $service_plan;
    }

}