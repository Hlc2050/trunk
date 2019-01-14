<?php
/**
 * 推广周计划控制器
 * User: Administrator
 * Date: 2018/11/23
 * Time: 9:41
 */

class PromotionWeekPlanController extends AdminController{
    public function actionIndex()
    {
        $where = '1';
        $date = $temp = $Sno_service = $arr = array();

        $day = strtotime($this->get('date_start'));
        $csid = $this->get('csid');
        $user_id = $this->get('user_id');
        $start_time = $day?$day:time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600;
        $end_time = $start_time+7*24*60*60;
        //获取本周日期
        for ($i=0 ;$i<7; $i++) {
            $week = $start_time+$i*24*60*60;
            $date[] = date('Ymd',$week);
            $month_day[] = date('m',$week).'月'.date('d',$week).'日';
        }

        $where .= ' and date between '.date('Ymd',$start_time).' and '.date('Ymd',$end_time);
        $where .= ' order by sno asc';

        if($csid) $where .= ' and service_id ='.$csid;
        if($user_id) $where .= ' and sno ='.$user_id;

        $data = FinanceServiceData::model()->findAll($where);
        $CSManage =CustomerServiceManage::model()->getCustomerName();
        $PromotionStaff = PromotionStaff::model()->getPromotionStaff();

        foreach ($data as $value){
            $key = $value['service_id'].'_'.$value['sno'];
            if(!in_array($key,$Sno_service)) {
                $Sno_service[] = $key;
            }

            foreach ($date as $v) {
                //相同日期的微信号个数
                if ($value['date'] == $v) {
                    $str = explode(',',$value['wechat_list']);
                    foreach ($str as $val){
                        //不同的微信号相加
                        if (!in_array($val, $temp[$key][$v]['wechat_list'])) {
                            $temp[$key][$v]['wechat_list'][]=$val;
                            $temp[$key][$v]['num'] = count($temp[$key][$v]['wechat_list']);
                        }
                    }
                    $temp[$key][$v]['fans'] += $value['service_fans_input'];
                }
            }
        }

        foreach($Sno_service as $value){
            $str = explode('_',$value);
            foreach ($date as $v) {
                $arr[$value]['date'][$v]['num'] = isset($temp[$value][$v])?$temp[$value][$v]['num']:0;
                $arr[$value]['date'][$v]['fans'] = isset($temp[$value][$v])?$temp[$value][$v]['fans']:0;
                $arr[$value]['service_name'] = $CSManage[$str[0]]?$CSManage[$str[0]]:'';
                $arr[$value]['sno'] = $PromotionStaff[$str[1]]?$PromotionStaff[$str[1]]:'';
            }
        }

        $this->render('index',array('data'=>$arr,'date'=>$date,'month_day'=>$month_day));
    }
}