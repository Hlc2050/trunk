<?php
/**
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/7
 * Time: 11:10
 */
class OrdersSortByDay extends CActiveRecord{

    public function tableName() {
        return '{{orders_sort_by_day}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }
    public function getOrderCache($condition)
    {
        $sql = 'SELECT partner_id,channel_id,customer_service_id,weixin_id,wechat_id,package_id,goods_id,SUM(in_count) as in_count,SUM(sout_count) as sout_count,
                SUM(sdelivery_money) as sdelivery_money,stat_date 
            FROM orders_sort_by_day WHERE '.$condition;
        $result = Yii::app()->db->createCommand($sql)->queryAll();
        return $result;
    }

    /**
     * 按日期统计商品进线量、发货量
     */
    public function getTotalGroupByDate($condition)
    {
        $sql = ' select SUM(sout_count) as sout_count,SUM(in_count) as in_count,stat_date  from orders_sort_by_day where 1 '.$condition.' group by stat_date order by package_id,stat_date asc';
        $package_info = Yii::app()->db->createCommand($sql)->queryAll();
        return $package_info;
    }

    /**
     *  统计商品总进线量、发货量、发货金额
     * @param $join string
     * @param $condition string
     */
    public function getPackageTotal($join = '',$condition = '')
    {
        $sql = " select SUM(a.delivery_money) as delivery_money ,SUM(a.out_count) as out_count,SUM(a.in_count) as in_count from orders_sort_by_day as a " .$join. " where 1" .$condition;
        $total = Yii::app()->db->createCommand($sql)->queryAll();
        return $total;
    }

    /**
     * 电销业务运营表数据
     * @param array $params
     * @return array
     * author: yjh
     */
    public function getOperateTableData($params = array())
    {
        //数据集合
        $data = array();
        $date = helper::get_right_date($params['start_date'], $params['end_date']);
        $data['first_day'] = date("Y-m-d", $date['first_day']);
        $data['last_day'] = date("Y-m-d", $date['last_day']);


        //搜索条件
        $orderParams = '';
        //客服部
        if ($params['csid']) {
            $orderParams .= " and customer_service_id=" . $params['csid'];
        }
        //商品
        if ($params['goodsid'] != 0) {
            $orderParams .= " and goods_id=" . $params['goodsid'];
        }
        //推广人员
        if ($params['tgid'] != 0) {
            $orderParams .= " and tg_uid=" . $params['tgid'];
        } elseif ($params['pgid'] != 0) {
            $promotionStaffArr = PromotionStaff::model()->getPromotionStaffByPg($params['pgid']);
            if (!$promotionStaffArr) $orderParams .= " and tg_uid=0";
            else {
                $str = implode(',', array_column($promotionStaffArr, 'user_id', 'user_id'));
                $orderParams .= " and tg_uid in (" . $str . ")";
            }
        }
        //业务类型
        $orderParams .= " and business_type=".Yii::app()->params['basic']['dx_bid'];

        //计费方式
        if ($params['chg_id'] !== null && $params['chg_id'] !== '') {
            $orderParams .= " and charging_type=" . $params['chg_id'];
        }
        $key = 0;
        //按日期循环
        for ($temp_date = $date['first_day']; $temp_date <= $date['last_day']; $temp_date = $temp_date + 86400) {
            $condition = "stat_date = " . $temp_date . $orderParams;
            $ordersInfo=Dtable::toArr(OrdersSortByDay::model()->findAll($condition));
            //预估发货量 预估发货金额（独立客服部和普通客服部）
            $fans_count = $order_count = $order_money = $estimate_count = $estimate_money = 0;
            if($ordersInfo){
                //进线量
                $fans_count=array_sum(array_column($ordersInfo,'in_count'));
                //发货量
                $order_count=array_sum(array_column($ordersInfo,'out_count'));
                $order_money=array_sum(array_column($ordersInfo,'delivery_money'));
                foreach ($ordersInfo as $value){
                    $customerServiceInfo = CustomerServiceManage::model()->findByPk($value['customer_service_id']);
                    $estimate_count += $value['out_count'] * 0.01 * $customerServiceInfo->estimate_rate;
                    $estimate_money += $value['delivery_money'] * 0.01 * $customerServiceInfo->estimate_rate;
                }
            }
            //投入金额(成本明细+修正成本)
            $condition = "stat_date=" . $temp_date . $orderParams;
            $sql = 'SELECT SUM(money) AS money FROM stat_cost_detail WHERE ' . $condition;
            $moneyInfo = Yii::app()->db->createCommand($sql)->queryAll();
            $sql = 'SELECT SUM(fixed_cost) AS fixed_cost FROM fixed_cost_new WHERE ' . $condition;
            $fixedCostInfo = Yii::app()->db->createCommand($sql)->queryAll();
            $money = $moneyInfo[0]['money'] + $fixedCostInfo[0]['fixed_cost'];
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

            $data['info'][$key]['stat_date'] = date('Y-m-d', $temp_date);
            $data['info'][$key]['fans_count'] = $fans_count;
            $data['info'][$key]['estimate_count'] = $estimate_count;
            $data['info'][$key]['estimate_money'] = $estimate_money;
            $data['info'][$key]['money'] = $money;
            $data['info'][$key]['ROI'] = $ROI;
            $data['info'][$key]['order_cor'] = $order_cor;
            $data['info'][$key]['unit'] = $unit;
            $data['info'][$key]['fans_cost'] = $fans_cost;
            $data['info'][$key]['fans_avg'] = $fans_avg;
            $data['info'][$key]['order_count'] = $order_count;
            $data['info'][$key]['order_money'] = $order_money;
            $key++;
        }

        return $data;
    }

    /**
     * 业绩表数据
     * @param array $params
     * @return array
     * author: yjh
     */
    public function getPerfTableData($params = array())
    {
        //数据集合
        $data = array();
        //可跨月查询
        $date = helper::get_right_date($params['start_date'], $params['end_date']);
        $data['first_day'] = date("Y-m-d", $date['first_day']);
        $data['last_day'] = date("Y-m-d", $date['last_day']);
        //搜索条件
        $orderParams = '';
        //客服部
        if ($params['csid']) {
            $orderParams .= " and customer_service_id=" . $params['csid'];
        }
        //商品
        if ($params['goodsid'] != 0) {
            $orderParams .= " and goods_id=" . $params['goodsid'];
        }
        //推广人员
        if ($params['tgid'] != 0) {
            $orderParams .= " and tg_uid=" . $params['tgid'];
        } elseif ($params['pgid'] != 0) {
            $promotionStaffArr = PromotionStaff::model()->getPromotionStaffByPg($params['pgid']);
            if (!$promotionStaffArr) $orderParams .= " and tg_uid=0";
            else {
                $str = implode(',', array_column($promotionStaffArr, 'user_id', 'user_id'));
                $orderParams .= " and tg_uid in (" . $str . ")";
            }
        }
        //业务类型
        $orderParams .= " and business_type=".Yii::app()->params['basic']['dx_bid'];

        //计费方式
        if ($params['chg_id'] !== null && $params['chg_id'] !== '') {
            $orderParams .= " and charging_type=" . $params['chg_id'];
        }
        $key = 0;
        //按日期循环
        for ($temp_date = $date['first_day']; $temp_date <= $date['last_day']; $temp_date = $temp_date + 86400) {
            //进粉量
            $condition = "stat_date = " . $temp_date . $orderParams;
            $ordersInfo=Dtable::toArr(OrdersSortByDay::model()->findAll($condition));
            //预估发货量 预估发货金额（独立客服部和普通客服部）
            $fans_count = $order_count = $order_money = $estimate_count = $estimate_money = 0;
            if($ordersInfo){
                //进线量
                $fans_count=array_sum(array_column($ordersInfo,'in_count'));
                //发货量
                $order_count=array_sum(array_column($ordersInfo,'out_count'));
                $order_money=array_sum(array_column($ordersInfo,'delivery_money'));
            }
            //投入金额(成本明细+修正成本)
            $condition = "stat_date=" . $temp_date . $orderParams;
            $sql = 'SELECT SUM(money) AS money FROM stat_cost_detail WHERE ' . $condition;
            $moneyInfo = Yii::app()->db->createCommand($sql)->queryAll();
            $sql = 'SELECT SUM(fixed_cost) AS fixed_cost FROM fixed_cost_new WHERE ' . $condition;
            $fixedCostInfo = Yii::app()->db->createCommand($sql)->queryAll();
            $money = $moneyInfo[0]['money'] + $fixedCostInfo[0]['fixed_cost'];
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

            $data['info'][$key]['stat_date'] = date('Y-m-d', $temp_date);
            $data['info'][$key]['fans_count'] = $fans_count;
            $data['info'][$key]['money'] = $money;
            $data['info'][$key]['ROI'] = $ROI;
            $data['info'][$key]['order_cor'] = $order_cor;
            $data['info'][$key]['unit'] = $unit;
            $data['info'][$key]['fans_cost'] = $fans_cost;
            $data['info'][$key]['fans_avg'] = $fans_avg;
            $data['info'][$key]['order_count'] = $order_count;
            $data['info'][$key]['order_money'] = $order_money;
            $key++;
        }

        return $data;
    }
}