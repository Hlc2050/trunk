<?php

/**
 * 业务运营表
 * Class BssOperationTableController
 */
class BssOperationTableController extends AdminController
{
    public function actionIndex()
    {
        $page = $this->getBssData();
        $this->render('index', array('page' => $page));
    }


    /*
     * AJAX获取客服部对应商品
     * author: yjh
     */
    public function actionGetGoodsByCs()
    {
        if ($this->get('csid')) {
            $data = CustomerServiceRelation::model()->getGoodsList($this->get('csid'));
            if (empty($data)) echo CHtml::tag('option', array('value' => ''), CHtml::encode('没有商品'), true);
            echo CHtml::tag('option', array('value' => ''), CHtml::encode('全部'), true);
            foreach ($data as $key => $val) {
                echo CHtml::tag('option', array('value' => $val['goods_id']), CHtml::encode($val['goods_name']), true);
            }
        } else {
            $data = Goods::model()->findAll();
            if (empty($data)) echo CHtml::tag('option', array('value' => ''), CHtml::encode('没有商品'), true);
            echo CHtml::tag('option', array('value' => ''), CHtml::encode('全部'), true);
            foreach ($data as $key => $val) {
                echo CHtml::tag('option', array('value' => $val['id']), CHtml::encode($val['goods_name']), true);
            }
        }

    }

    /*
    * AJAX获取部门对应推广人员
    * author: yjh
    */
    public function actionGetPromotionStaffByPg()
    {
        if (isset($_POST['pgid'])) {
            $data = PromotionStaff::model()->getPromotionStaffByPg($_POST['pgid']);
            if (empty($data)) echo CHtml::tag('option', array('value' => ''), CHtml::encode('没有推广人员'), true);
            echo CHtml::tag('option', array('value' => ''), CHtml::encode('全部'), true);
            foreach ($data as $key => $val) {
                echo CHtml::tag('option', array('value' => $val['user_id']), CHtml::encode($val['user_name']), true);
            }
        }
    }

    /**
     * AJAX获取计费方式
     * author: yjh
     */
    public function actionGetChargingTypes()
    {
        if ($this->post('bsid')) {
            $data = BusinessTypeRelation::model()->getChargeTypes($this->post('bsid'));
            echo CHtml::tag('option', array('value' => ''), CHtml::encode('全部'), true);
            foreach ($data as $key => $val) {
                echo CHtml::tag('option', array('value' => $val['value']), CHtml::encode($val['txt']), true);
            }
        } else {
            echo CHtml::tag('option', array('value' => ''), CHtml::encode('全部'), true);
        }
    }
    /**
     * AJAX获取计费方式
     * author: yjh
     */
    public function actionGetBusinessTypes()
    {
        if ($this->post('tid')) {
            $data = BusinessTypes::model()->getBsTypesByTid($this->post('tid'));
            echo CHtml::tag('option', array('value' => ''), CHtml::encode('全部'), true);
            foreach ($data as $key => $val) {
                echo CHtml::tag('option', array('value' => $val['bid']), CHtml::encode($val['bname']), true);
            }
        } else {
            echo CHtml::tag('option', array('value' => ''), CHtml::encode('全部'), true);
        }
    }
    private function getBssData()
    {
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
            $data = StatCostDetail::model()->getOperateTableData($params);
        elseif($tid==2)
            $data = OrdersSortByDay::model()->getOperateTableData($params);
        else{
            $s_data = StatCostDetail::model()->getOperateTableData($params);
            $o_data = OrdersSortByDay::model()->getOperateTableData($params);
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
            $estimate_count = $value['estimate_count']+$o_data['info'][$key]['estimate_count'];
            $estimate_money = $value['estimate_money']+$o_data['info'][$key]['estimate_money'];
            $money = $value['money']+$o_data['info'][$key]['money'];
            $order_count = $value['order_count']+$o_data['info'][$key]['order_count'];
            $order_money = $value['order_money']+$o_data['info'][$key]['order_money'];
            //ROI
            $ROI = $money == 0 ? 0 : round($estimate_money * 100 / $money); //ROI
            //订单转化率
            $order_cor = $fans_count == 0 ? 0 : round($estimate_count * 100 / $fans_count, 1);
            //客单价
            $unit = $estimate_count == 0 ? 0 : round($estimate_money / $estimate_count);
            //进粉成本
            $fans_cost = $fans_count == 0 ? 0 : round($money / $fans_count);
            //均粉产出
            $fans_avg = $fans_count == 0 ? 0 : round($estimate_money / $fans_count);

            $data['info'][$key]['stat_date'] = $stat_date;
            $data['info'][$key]['fans_count'] = $fans_count;
            $data['info'][$key]['estimate_count'] = $estimate_count;
            $data['info'][$key]['estimate_money'] = $estimate_money;
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