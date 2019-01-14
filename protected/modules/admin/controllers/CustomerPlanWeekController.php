<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/23
 * Time: 9:45
 */

class CustomerPlanWeekController extends AdminController{
    public function actionIndex()
    {
        $where = '1';
        $date = $temp = $service_id = $arr = array();
        $csid = $this->get('csid');
        $day = strtotime($this->get('date_start'));
        $start_time = $day?$day:time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600;
        $end_time = $start_time+7*24*60*60;
        //获取本周日期
        for ($i=0 ;$i<7; $i++) {
            $week = $start_time+$i*24*60*60;
            $date[] = date('Ymd',$week);
            $month_day[] = date('m',$week).'月'.date('d',$week).'日';
        }

        $where .= ' and date between '.date('Ymd',$start_time).' and '.date('Ymd',$end_time);
        if($csid) $where .= ' and service_id ='.$csid;
        $data = FinanceServiceData::model()->findAll($where);

        foreach ($data as $value){
            $key = $value['service_id'];
            if(!in_array($key,$service_id)) {
                $service_id[] = $key;
            }

            foreach ($date as $v) {
               if ($value['date'] == $v) {
                  $str = explode(',',$value['wechat_list']);
                        //$temp[客服部id][日期]
                        foreach ($str as $val){
                        if (!in_array($val, $temp[$key][$v]['wechat_list'])) {
                            $temp[$key][$v]['wechat_list'][]=$val;
                            $temp[$key][$v]['num'] = count($temp[$key][$v]['wechat_list']);
                        }
                    }
                   $temp[$key][$v]['fans'] += $value['service_fans_input'];
                }
            }
            }

        foreach($service_id as $value){
            foreach ($date as $v) {
                if(isset($temp[$value][$v])){
                    $arr[$value]['date'][$v]['num'] = $temp[$value][$v]['num'];
                    $arr[$value]['date'][$v]['fans'] = $temp[$value][$v]['fans'];
                }else{
                    $arr[$value]['date'][$v]['num'] = 0;
                    $arr[$value]['date'][$v]['fans'] = 0;
                }
                $arr[$value]['service_name'] = CustomerServiceManage::model()->getCSName($value);
            }
        }

        $this->render('index',array('data'=>$arr,'date'=>$date,'month_day'=>$month_day));
    }
}