<?php
/**
 * 客服数据监测-数据推送
 * Created by PhpStorm.
 * User: lxj
 * Date: 2018/8/23
 * Time: 10:29
 */

class DataPushController extends AdminController
{

    public function actionIndex()
    {
        $sql = array(
            'select'=>' id,cname,status',
        );
        $service = Dtable::toArr(CustomerServiceManage::model()->findAll($sql));
        $norm_service = array();
        $indep_service = array();
        $service_info = array();
        foreach ($service as $value) {
            $service_info[$value['id']]['cname'] = $value['cname'];
            if ($value['status'] == 1) {
                $indep_service[] = $value['id'];
            }
            if ($value['status'] == 0) {
                $norm_service[] = $value['id'];
            }
        }
        $today = strtotime(date('Y-m-d',time()));
        $indep_place_ser = array();
        $indep_delivery_ser = array();
        $norm_place_ser = array();
        $norm_delivery_ser = array();
        $fans_ser = array();
        $log_ser = array();
        //独立客服订单今日更新
        if ($indep_service) {
            $indep_sql = array(
                'select'=> 'customer_service_id,create_time',
                'condition'=> 'create_time >='.$today.' and customer_service_id in ('.implode(',',$indep_service).') ',
        );
            $indep_place =   Dtable::toArr(PlaceIndepOrderManage::model()->findAll($indep_sql));
            $indep_place_ser = array_unique(array_column($indep_place,'customer_service_id'));
            $indep_delivery =   Dtable::toArr(DeliveryIndepOrderManage::model()->findAll($indep_sql));
            $indep_delivery_ser = array_unique(array_column($indep_delivery,'customer_service_id'));
        }

        //普通客服部订单今日更新
        if ($indep_service) {
            $indep_sql = array(
                'select'=> 'customer_service_id,create_time',
                'condition'=> 'create_time >='.$today.' and customer_service_id in ('.implode(',',$norm_service).') ',
            );
            $norm_place =  Dtable::toArr(PlaceNormOrderManage::model()->findAll($indep_sql));
            $norm_place_ser = array_unique(array_column($norm_place,'customer_service_id'));
            $norm_delivery =   Dtable::toArr(DeliveryNormOrderManage::model()->findAll($indep_sql));
            $norm_delivery_ser = array_unique(array_column($norm_delivery,'customer_service_id'));
        }
        //进粉数据今日更新
        $all_service = array_keys($service_info);
        if ($all_service) {
            $fans_sql = array(
                'select'=> 'customer_service_id,create_time',
                'condition'=> 'create_time >='.$today.' and customer_service_id in ('.implode(',',$all_service).') ',
            );
            $fans =  Dtable::toArr(FansInputManage::model()->findAll($fans_sql));
            $fans_ser = array_unique(array_column($fans,'customer_service_id'));
            //推送更新
            $log_sql = array(
                'select'=> 'service_id,add_time',
                'condition'=> 'add_time >='.$today,
            );
            $logs =  Dtable::toArr(DataMsgPushLog::model()->findAll($log_sql));
            $log_ser = array_unique(array_column($logs,'service_id'));
        }

        $place_ser = array_merge($norm_place_ser,$indep_place_ser);
        $delivery_ser = array_merge($indep_delivery_ser,$norm_delivery_ser);
        //进粉及订单都有更新的客服部
        $update_service  = array_intersect($fans_ser,$place_ser,$delivery_ser);
        foreach ($service_info as $key=>$value) {
            $service_info[$key]['is_update'] =0;
            $service_info[$key]['is_push'] =0;
            if (in_array($key,$update_service) ) {
                $service_info[$key]['is_update'] =1;
            }
            if (in_array($key,$log_ser) || in_array(0,$log_ser)) {
                $service_info[$key]['is_push'] =1;
            }
        }
        $page['service'] = $service_info;
        if (in_array(0,$log_ser)) {
            $page['update_all'] = 1;
        }
        $this->render('index', array('page' => $page));
    }


    public function actionPush()
    {

        //进粉数据浮动配置
        $remind = Yii::app()->params['remind'];
        $high_fans = $remind['fans_up'];
        $low_fans = $remind['Fans_down'];
        //产值数据浮动提醒配置
        $high_output = $remind['output_up'];
        $low_output = $remind['output_down'];
        //推送总开关
        $is_push = intval($remind['all_warn']);
        if ($is_push == 0) {
            $this->msg(array('state' => 0, 'msgwords' => '未开启推送总开关！'));
        }
        $push_service = explode(',',$remind['cservice_warner']);
        $group = Dtable::toArr(AdminGroup::model()->findAll());

        //推广人员推送开关
        $push_tg = $remind['tg_warner'];
        $date = strtotime(date('Y-m-d',strtotime('-1 day')));
        $today = strtotime(date('Y-m-d',time()));
        $data_where = ' date='.$date.' and add_time>'.$today;
        $plan_where = ' date='.$date;
        $service_id = $this->get('service_id') ?$this->get('service_id') :0 ;
        if ($service_id) {
            $data_where .= ' and service_id='.$service_id;
            $plan_where .= ' and service_group='.$service_id;
        }
        $output = DataPracticalOutput::model()->findAll($data_where);
        $fans = DataPracticalFans::model()->findAll($data_where);
        $group_plan = PlanWeekGroupDetail::model()->findAll($plan_where.' and status=1');
        $user_plan = array();
        if ($push_tg) {
            $user_plan = PlanWeekUserDetail::model()->findAll($plan_where.' and status=12');
        }
        $output_service = array();
        $output_group = array();
        $output_user = array();
        $fans_service = array();
        $fans_group = array();
        $fans_user = array();
        $plan_service = array();
        $plan_group = array();
        $plan_user = array();
        foreach ($output as $value) {
            $output_service[$value['service_id']][] = $value['output'];
            $output_group[$value['group_id']][$value['service_id']][] = $value['output'];
            $output_user[$value['tg_uid']][$value['service_id']][] = $value['output'];
        }
        foreach ($fans as $value) {
            $fans_service[$value['service_id']][] = $value['fans'];
            $fans_group[$value['group_id']][$value['service_id']][] = $value['fans'];
            $fans_user[$value['tg_uid']][$value['service_id']][] = $value['fans'];
        }
        foreach ($group_plan as $value) {
            $plan_service[$value['service_group']][] = array('fans_count'=>$value['fans_count'],'output'=>$value['output']);
            $plan_group[$value['group_id']][$value['service_group']][] = array('fans_count'=>$value['fans_count'],'output'=>$value['output']);
        }
        foreach ($user_plan as $value) {
            $plan_user[$value['tg_uid']][$value['service_group']][] = array('fans_count'=>$value['fans_count'],'output'=>$value['output']);
        }
        /***********************1.向客服部数据推送人员推送客服部数据**************************/
        $alarm_service_fans_num = 0;
        $alarm_service_output_num = 0;
        foreach ($plan_service as $key=>$value) {
            $total_output = array_sum($output_service[$key]);
            $total_fans = array_sum($fans_service[$key]);
            $total_plan_output = array_sum(array_column($value,'output'));
            $total_plan_fans = array_sum(array_column($value,'fans_count'));
            $output_radio = ($total_output-$total_plan_output)*100/$total_plan_output;
            $fans_radio = ($total_fans-$total_plan_fans)*100/$total_plan_fans;

            if ( $fans_radio>$high_fans || $fans_radio<$low_fans) {
                $alarm_service_fans_num++;
            }
            if ( $output_radio>$high_output || $fans_radio<$low_output) {
                $alarm_service_output_num++;
            }
        }
        $service_msg = array();
        //客服部预警数据
        foreach ($push_service as $value) {
            $msg ='';
            if ($service_id) {
                if ($alarm_service_fans_num>0) {
                    $msg .='实际进粉数据异常'.chr(10);
                }
                if ($alarm_service_output_num>0) {
                    $msg .='产值数据异常'.chr(10);
                }
            }else{
                if ($alarm_service_fans_num>0) {
                    $msg .='共有'.$alarm_service_fans_num.'个客服部实际进粉据异常'.chr(10);
                }
                if ($alarm_service_output_num>0) {
                    $msg .='共有'.$alarm_service_output_num.'个客服部产值数据异常'.chr(10);
                }
            }
            $service_msg[] = array(
                'msg' =>$msg,
                'uid' =>$value,
                'type' =>1,
            );
        }
        /***********************2.向推广组长推送推广组客服部数据**************************/
        $alarm_group_fans_num = array();
        $alarm_group_output_num = array();
        $plan_groups = array();
        foreach ($plan_group as $key=>$value) {
            foreach ($value as $g=>$s) {
                $total_output = array_sum($output_group[$key][$g]);
                $total_fans = array_sum($fans_group[$key][$g]);
                $total_plan_output = array_sum(array_column($s,'output'));
                $total_plan_fans = array_sum(array_column($s,'fans_count'));
                $output_radio = ($total_output-$total_plan_output)*100/$total_plan_output;
                $fans_radio = ($total_fans-$total_plan_fans)*100/$total_plan_fans;

                if (!isset($alarm_group_fans_num[$key])) {
                    $alarm_group_fans_num[$key] = 0;
                }
                if (!isset($alarm_group_output_num[$key])) {
                    $alarm_group_output_num[$key] = 0;
                }
                if ( $fans_radio>$high_fans || $fans_radio<$low_fans) {
                    $alarm_group_fans_num[$key] = $alarm_group_fans_num[$key]+1;
                }
                if ( $output_radio>$high_output || $output_radio<$low_output) {
                    $alarm_group_output_num[$key] = $alarm_group_output_num[$key]+1;
                }
            }
            $plan_groups[] = $key;
        }

        $push_manage = array();
        foreach ($group as $value) {
            if (in_array($value['groupid'],$plan_groups)) {
                $push_manage[$value['manager_id']][] = $value['groupid'];
            }
        }
        $group_msg = array();
        foreach ($push_manage as $key=>$value) {
            $total_fans_alarm[$key] = 0;
            $total_output_alarm[$key] = 0;
            foreach ($value as $g) {
                $total_fans_alarm[$key] +=  $alarm_group_fans_num[$g];
                $total_output_alarm[$key] +=  $alarm_group_output_num[$g];
            }
            $msg ='';
            if ($service_id) {
                if ($total_fans_alarm[$key]>0) {
                    $msg .='实际进粉数据异常'.chr(10);
                }
                if ($total_output_alarm[$key]>0) {
                    $msg .='产值数据异常'.chr(10);
                }
            }else{
                if ($total_fans_alarm[$key]>0) {
                    $msg .='共有'.$total_fans_alarm[$key].'个客服部实际进粉据异常;'.chr(10);
                }
                if ($total_output_alarm[$key]>0) {
                    $msg .='共有'.$total_output_alarm[$key].'个客服部产值数据异常;'.chr(10);
                }
            }
            $group_msg[] = array(
                'msg' =>$msg,
                'uid' =>$key,
                'type' =>2,
            );
        }

        /***********************2.向推广人员推送推广人员客服部数据**************************/
        $user_msg = array();
        //推广人员预警
        $push_user = array();
        if ($push_tg == 1) {
            $alarm_user_fans_num = array();
            $alarm_user_output_num = array();
            foreach ($plan_user as $key=>$value) {
                foreach ($value as $g=>$s) {
                    $total_output = array_sum($output_group[$key][$g]);
                    $total_fans = array_sum($fans_group[$key][$g]);
                    $total_plan_output = array_sum(array_column($s,'output'));
                    $total_plan_fans = array_sum(array_column($s,'fans_count'));
                    $output_radio = ($total_output-$total_plan_output)*100/$total_plan_output;
                    $fans_radio = ($total_fans-$total_plan_fans)*100/$total_plan_fans;
                    if (!isset($alarm_group_fans_num[$key])) {
                        $alarm_user_fans_num[$key] = 0;
                    }
                    if (!isset($alarm_group_output_num[$key])) {
                        $alarm_user_output_num[$key] = 0;
                    }
                    if ( $fans_radio>$high_fans || $fans_radio<$low_fans) {
                        $alarm_user_fans_num[$key] = $alarm_user_fans_num[$key]+1;
                    }
                    if ( $output_radio>$high_output || $output_radio<$low_output) {
                        $alarm_user_output_num[$key] = $alarm_user_output_num[$key]+1;
                    }
                }
                $push_user[] = $key;
            }
            //客服部预警数据
            $push_user = array_unique($push_user);
            foreach ($push_user as $value) {
                $total_fans_alarm[$value] = 0;
                $total_output_alarm[$value] = 0;
                $msg ='';
                if ($service_id) {
                    if ($alarm_user_fans_num[$value]>0) {
                        $msg .='实际进粉数据异常'.chr(10);
                    }
                    if ($alarm_user_output_num[$value]>0) {
                        $msg.='产值数据异常'.chr(10);
                    }
                }else{
                    if ($alarm_user_fans_num[$value]>0) {
                        $msg.='共有'.$alarm_user_fans_num[$value].'个客服部实际进粉据异常'.chr(10);
                    }
                    if ($alarm_user_output_num[$value]>0) {
                        $msg .='共有'.$alarm_user_output_num[$value].'个客服部产值数据异常'.chr(10);
                    }
                }
                $user_msg[] = array(
                    'msg' =>$msg,
                    'uid' =>$value,
                    'type' =>3,
                );
            }

        }

        $all_mssg = array_merge($service_msg,$group_msg,$user_msg);
        $send = new SendMessage();
        if ($service_id) {
            $service = CustomerServiceManage::model()->findByPk($service_id);
            $service_name = $service->cname;
            $send->sendDataMsg('今日'.$service_name.'进粉数据已生成，请及时查看',$all_mssg);
        } else {
            $send->sendDataMsg('今日进粉数据已生成，请及时查看',$all_mssg);
        }
        DataMsgPushLog::model()->addPushLog($service_id);
        $this->msg(array('state' => 1, 'msgwords' => '数据推送成功！'));
        exit();
    }


}