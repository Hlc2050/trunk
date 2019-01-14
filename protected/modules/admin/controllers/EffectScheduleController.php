<?php

/**
 * 进度表管理
 * Created by PhpStorm.
 * User:fang
 * Date: 2017/02/20
 * 2018.12.6 修改
 */
class EffectScheduleController extends AdminController
{
    /**
     * 进度表页
     * author: fang
     */
    public function actionIndex()
    {
        //搜索
        $params['where'] = '';

        $stat_date = strtotime($this->get('start_plan_date')  );
        $end_date = strtotime($this->get('end_plan_date')  );
        $schedule_id = $this->get('schedule_id');
        if ( date('m',$stat_date) != date('m',$end_date) ){
            $oneMonthAgo = mktime(0, 0, 0, date('n', $stat_date), 1, date('Y', $stat_date));
            $year = date('Y', $oneMonthAgo);
            $month = date('n', $oneMonthAgo);
            $end_date = strtotime(date( 'Y-m-t',strtotime($year."-{$month}-1") ));
        }
        //时间进度
        $total_day = date('t',$stat_date);
        $select_day = ($end_date - $stat_date)/86400+1;
        $dateInfo['sc_t'] =  $total_day - $select_day;
        $dateInfo['sc_days'] = $total_day!=0?round($select_day/$total_day*100,1):0;

        //获取目标投产比的linkage_id
        $linkage_id = Linkage::model()->get_linkage_id(26,'目标投产比');

        $target_time = strtotime( date('Y-m',strtotime( $this->get('start_plan_date') )) );

        //获取目标发货金额
        $deliveinfo = ScheduleManage::model()->findByAttributes( array('schedule_id' => $this->get('schedule_id') ,'target_time' => $target_time) );

        //获取目标投产比
        $putinfo = ScheduleManage::model()->findByAttributes( array('schedule_id' => $linkage_id['linkage_id'] ,'target_time' => $target_time) );

        //获取推广组别
        $page['sname'] = Linkage::model()->get_name( $schedule_id );
        if ($page['sname'] == '整体目标') {
            $allInfo = Goods::model()->getAllMoney($stat_date,$end_date);
            //获取发货金额之和
            $delivery_money = $allInfo['delivery_money'];
            //获取投入金额之和
            $input_money = $allInfo['putin_money'];
        } else {
            $tg_group_uid = Linkage::model()->get_linkage_id(24, $page['sname']);
            $condition = $condition1 = '1';
            if ($tg_group_uid) {
                $tg_group_uid['linkage_id'];
                if ($schedule_id && $stat_date && $end_date){
                    $condition .= " and promotion_group_id='{$tg_group_uid['linkage_id']}' and delivery_date between '{$stat_date}' and '{$end_date}'";
                    $delivery_money = Goods::model()->getDeliveryMoneyByPromGroup($condition);
                    $delivery_money = $delivery_money['delivery_money']?$delivery_money['delivery_money']:0;
                }

                if ($schedule_id && $stat_date && $end_date){
                    $condition1 .= " and promotion_group_id = '{$tg_group_uid['linkage_id']}' and a.stat_date between '{$stat_date}' and '{$end_date}'";
                    $input_money = Goods::model()->getPutinMoneyByPromGroup($condition1);
                    $input_money = $input_money['putin_money']?$input_money['putin_money']:0;
                }

            } else {
                if ($schedule_id && $stat_date && $end_date){
                    $condition .= " and cat_id ='{$schedule_id}' and delivery_date between '{$stat_date}' and '{$end_date}'";
                    $delivery_money = Goods::model()->getDeliveryMoneyByGoods($condition);
                    $delivery_money = $delivery_money['delivery_money']?$delivery_money['delivery_money']:0;
                }
                if ($schedule_id && $stat_date && $end_date){
                    $condition1 .= " and b.cat_id in ({$schedule_id}) and a.stat_date between '{$stat_date}' and '{$end_date}'";
                    $input_money = Goods::model()->getPutInMoneyByGoods($condition1);
                    $input_money = $input_money[$schedule_id]['putin_money']?$input_money[$schedule_id]['putin_money']:0;
                }
            }
        }

        $info_cost = array(
            'input_money' => $input_money,
            'delivery_money' => $delivery_money
        );

        $this->render('index', array('deliveinfo' => $deliveinfo,'putinfo' => $putinfo,'info_cost' => $info_cost,'dateInfo'=>$dateInfo));
    }
}