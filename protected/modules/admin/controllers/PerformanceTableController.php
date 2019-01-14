<?php
/**
 * 业绩表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2017/3/16
 * Time: 15:58
 */
class PerformanceTableController extends AdminController
{
    public function actionIndex()
    {
        $page=$this->getPerformanceData();
        $this->render('index', array('page' => $page));
    }
    private function getPerformanceData(){
        $params = array();
        $params['start_date'] = $this->get('start_date');
        $params['end_date'] = $this->get('end_date');
        $params['csid'] = $this->get('csid');
        $params['goodsid'] = $this->get('goods_id');
        $params['pgid'] = $this->get('pgid');
        $params['tgid'] = $this->get('tg_id');
        $params['bsid'] = $this->get('bsid');
        $params['chg_id'] = $this->get('chg_id');
        $tid = $this->get('tid')?$this->get('tid'):0;
        if($tid==1)
            $data = StatCostDetail::model()->getPerfTableData($params);
        elseif($tid==2)
            $data = OrdersSortByDay::model()->getPerfTableData($params);
        else{
            $s_data = StatCostDetail::model()->getPerfTableData($params);
            $o_data = OrdersSortByDay::model()->getPerfTableData($params);
            $data=$this->integratData($s_data,$o_data);
        }

        return $data;
    }
    /**
     * 整合电销微销数据
     * @param $s_data
     * @param $o_data
     * author: yjh
     */
    private function integratData($s_data,$o_data)
    {
        $data=array();
        $data['first_day'] = $s_data['first_day'];
        $data['last_day'] = $s_data['last_day'];
        foreach ($s_data['info'] as $key=>$value){
            $stat_date = $value['stat_date'];
            $fans_count = $value['fans_count']+$o_data['info'][$key]['fans_count'];

            $money = $value['money']+$o_data['info'][$key]['money'];
            $order_count = $value['order_count']+$o_data['info'][$key]['order_count'];
            $order_money = $value['order_money']+$o_data['info'][$key]['order_money'];
            //ROI
            $ROI = $money == 0 ? 0 : round($order_money * 100 / $money); //ROI
            //订单转化率
            $order_cor = $fans_count == 0 ? 0 : round($order_count * 100 / $fans_count, 1);
            //客单价
            $unit = $order_count == 0 ? 0 : round($order_money / $order_count);
            //进粉成本
            $fans_cost = $fans_count == 0 ? 0 : round($money / $fans_count);
            //均粉产出
            $fans_avg = $fans_count == 0 ? 0 : round($order_money / $fans_count);

            $data['info'][$key]['stat_date'] = $stat_date;
            $data['info'][$key]['fans_count'] = $fans_count;
            $data['info'][$key]['money'] = $money;
            $data['info'][$key]['order_count'] = $order_count;
            $data['info'][$key]['order_money'] = $order_money;
            $data['info'][$key]['ROI'] = $ROI;
            $data['info'][$key]['order_cor'] = $order_cor;
            $data['info'][$key]['unit'] = $unit;
            $data['info'][$key]['fans_cost'] = $fans_cost;
            $data['info'][$key]['fans_avg'] = $fans_avg;
        }
        return $data;

    }


}