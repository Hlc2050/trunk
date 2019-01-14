<?php

/**
 * Created by PhpStorm.
 * User: fang
 * Date: 2017/4/10
 * Time: 9:22
 */
class ApiController extends HomeController
{
    //进度表对象接口

    public function actionScheduleList()
    {
        //对象列表
        $scheduleList = Linkage::model()->getScheduleList();
        //商品类别列表
        $goodsList = Linkage::model()->getGoodsCategoryList();
        $scheduleList = $scheduleList + $goodsList;
        helper::json(0, $scheduleList);
    }

    //获取进度表数据接口
    public function actionScheduleData()
    {

        $stat_date = $_POST['stat_date'];
        $end_date = $_POST['end_date'];
        $schedule_id = $_POST['sc_id'];
        if (date('m', $stat_date) != date('m', $end_date)) {
            $oneMonthAgo = mktime(0, 0, 0, date('n', $stat_date), 1, date('Y', $stat_date));
            $year = date('Y', $oneMonthAgo);
            $month = date('n', $oneMonthAgo);
            $end_date = strtotime(date('Y-m-t', strtotime($year . "-{$month}-1")));
        }
        //获取目标投产比的linkage_id
        $linkage_id = Linkage::model()->get_linkage_id(26, '目标投产比');

        $target_time = strtotime(date('Y-m', $stat_date));

        //获取目标发货金额
        $deliveinfo = ScheduleManage::model()->findByAttributes(array('schedule_id' => $schedule_id, 'target_time' => $target_time));
        //获取目标投产比
        $putinfo = ScheduleManage::model()->findByAttributes(array('schedule_id' => $linkage_id['linkage_id'], 'target_time' => $target_time));

        //获取推广组别
        $page['sname'] = Linkage::model()->get_name($schedule_id);
        if ($page['sname'] == '整体目标') {

            $allInfo = Goods::model()->getAllMoney($stat_date,$end_date);
            //获取发货金额之和
            $delivery_money = $allInfo['delivery_money'];
            //获取投入金额之和
            $input_money = $allInfo['putin_money'];

        } else {
            $tg_group_uid = Linkage::model()->get_linkage_id(24, $page['sname']);
            if ($tg_group_uid) {
                $tg_group_uid['linkage_id'];
                $condition = "promotion_group_id='{$tg_group_uid['linkage_id']}' and delivery_date between '{$stat_date}' and '{$end_date}'";
                $delivery_money = Goods::model()->getDeliveryMoneyByPromGroup($condition);
                $delivery_money = $delivery_money['delivery_money']?$delivery_money['delivery_money']:0;
                $condition1 = "promotion_group_id = '{$tg_group_uid['linkage_id']}' and a.stat_date between '{$stat_date}' and '{$end_date}'";
                $input_money = Goods::model()->getPutinMoneyByPromGroup($condition1);
                $input_money = $input_money['putin_money']?$input_money['putin_money']:0;

            } else {
                $condition = "cat_id ='{$schedule_id}' and delivery_date between '{$stat_date}' and '{$end_date}'";
                $delivery_money = Goods::model()->getDeliveryMoneyByGoods($condition);
                $delivery_money = $delivery_money['delivery_money']?$delivery_money['delivery_money']:0;
                $condition1 = "b.cat_id in ({$schedule_id}) and a.stat_date between '{$stat_date}' and '{$end_date}'";
                $input_money = Goods::model()->getPutInMoneyByGoods($condition1);
                $input_money = $input_money[$schedule_id]['putin_money']?$input_money[$schedule_id]['putin_money']:0;
            }


        }
        $scInfo = array(
            'p_target_type' => $putinfo->target_type,
            'p_target_a' => $putinfo->target_a,
            'p_target_b' => $putinfo->target_b,
            'p_target_c' => $putinfo->target_c,
            'd_target_type' => $deliveinfo->target_type,
            'd_target_a' => $deliveinfo->target_a,
            'd_target_b' => $deliveinfo->target_b,
            'd_target_c' => $deliveinfo->target_c,
            'input_money' => $input_money,
            'delivery_money' => $delivery_money,
            'stat_date' => $stat_date,
            'end_date' => $end_date,
            'sc_id' => $schedule_id
        );
        helper::json(0, $scInfo);
    }

    //商品类型列表接口
    public function actionGetGoodsType()
    {
        $goodsList = Linkage::model()->getGoodsCategoryList();
        helper::json(0, $goodsList);
    }
    //月投占比数据接口
    public function actionGetMouthPro(){
        $stat_date = $_POST['stat_date'];
        $end_date = $_POST['end_date'];
        $gd_id = $_POST['gd_id'];
        /*$stat_date = 1488297600;
        $end_date = 1490975999;
        $gd_id = 0;*/
        $gdArr = array();
        if( $gd_id == 0 ){
            $condition = "b.order_date between '{$stat_date}' and '{$end_date}'";
            $condition1 = "a.stat_date between '{$stat_date}' and '{$end_date}'";
            $gdArr = Linkage::model()->getGoodsKeyList();
        }else{
            $condition = "c.cat_id in ({$gd_id}) and b.order_date between '{$stat_date}' and '{$end_date}'";
            $condition1 = "b.cat_id in ({$gd_id}) and a.stat_date between '{$stat_date}' and '{$end_date}'";
            $gdArr = explode(',',$gd_id);
            if (in_array(0,$gdArr))$gdArr = Linkage::model()->getGoodsKeyList();
        }
        $order = Goods::model()->getPlaceMoneyByGoods($condition);
        $putIn = Goods::model()->getPutInMoneyByGoods($condition1);
        $MouthInfo = array();
        foreach ( $gdArr as $k){
            $MouthInfo[$k]['cat_name'] = Linkage::model()->get_name($k);
            $MouthInfo[$k]['order'] = array_key_exists($k,$order)?$order[$k]['order_money']/100:0;
            $MouthInfo[$k]['putin_money'] = array_key_exists($k,$putIn)?$putIn[$k]['putin_money']:0;
        }
        helper::json(0, $MouthInfo);
    }
    //周投产比数据接口
    public function actionGetWeekPro(){
        $date = $_POST['date'];
        $goods_id = $_POST['gd_id'];
        $weekPro = array();
        for ($i = 0; $i < 5; $i++) {
            $stat_date = $date - (($i + 1) * 7 - 1) * 86400;
            $end_date = $date - ($i * 7 - 1) * 86400 - 1;
            $gdArr = array();
            if( $goods_id == 1 ){
                $condition = "b.order_date between '{$stat_date}' and '{$end_date}'";
                $condition1 = "a.stat_date between '{$stat_date}' and '{$end_date}'";
                $gdArr = Linkage::model()->getGoodsKeyList();
            }else{
                $condition = "c.cat_id in ({$goods_id}) and b.order_date between '{$stat_date}' and '{$end_date}'";
                $condition1 = "b.cat_id in ({$goods_id}) and a.stat_date between '{$stat_date}' and '{$end_date}'";
            }
            $order = Goods::model()->getPlaceMoneyByGoods($condition);
            $putIn = Goods::model()->getPutInMoneyByGoods($condition1);
            if ( $goods_id == 1){
                foreach ($gdArr as $k){
                    $order[1]['order_money'] += $order[$k]['order_money'];
                    $putIn[1]['putin_money'] += $putIn[$k]['putin_money'];
                }
            }
            $weekPro[] = array('putin_week_money' => $putIn?$putIn[$goods_id]['putin_money']:0, 'delivery_week_money' => $order?$order[$goods_id]['order_money']/100:0, 'date' => $end_date);

        }
        krsort($weekPro);
        helper::json(0, $weekPro);
    }
    //月投占比数据接口
    public function actionGetMouthPro1()
    {
        $stat_date = $_POST['stat_date'];
        $end_date = $_POST['end_date'];
        $gd_id = $_POST['gd_id'];
        //$gd_id = array("0"=>0,"1"=>767507,"2"=>767508);
        //$gd_id = array("0"=>767507,"1"=>767508);
        $gd_id = explode(',', $gd_id);
        $order = array();
        $d_sum = 0;
        $input_money =0;
        foreach ($gd_id as $a) {
            if ($a == 0) {
                $goodsList = Linkage::model()->getGoodsCategoryList();
                foreach ($goodsList as $k => $v) {
                    $goods = Goods::model()->findAllByAttributes(array('cat_id' => $k));
                    if ($goods) {
                        foreach ($goods as $key => $val) {
                            $customerList = CustomerServiceManage::model()->getCustomerServiceList();
                            $goods_sum = 0;
                            foreach ($customerList as $cL => $cc) {
                                $goods_order = $this->getInMoney($goods[$key]->id, $cc['id'], $cc['estimate_rate'], $stat_date, $end_date);
                                $goods_sum += $goods_order;
                            }
                            $d_sum += $goods_sum;
                            $input_money = $this->getOutMoney($goods[$key]->id, $stat_date, $end_date);
                            $order += array($k => array('order' => $goods_sum, 'goods' => $v, 'input_money' => $input_money));
                            $goods_sum = 0;
                            $input_money = 0;
                        }
                    } else {
                        $order += array($k => array('order' => 0, 'goods' => $v, 'input_money' => 0));
                    }

                };
                break;
            } elseif ($a == 1) {
                break;
            } else {
                $goods = Goods::model()->findAllByAttributes(array('cat_id' => $a));
                $goods_name = Linkage::model()->get_name($a);
                if ($goods) {
                    foreach ($goods as $key => $val) {
                        $customerList = CustomerServiceManage::model()->getCustomerServiceList();
                        $goods_sum = 0;
                        foreach ($customerList as $cL => $cc) {
                            $goods_order = $this->getInMoney($goods[$key]->id, $cc['id'], $cc['estimate_rate'], $stat_date, $end_date);
                            $goods_sum += $goods_order;
                        }
                        $d_sum += $goods_sum;

                        $input_money += $this->getOutMoney($goods[$key]->id, $stat_date, $end_date);
                        $order += array($a => array('order' => $goods_sum, 'goods' => $goods_name, 'input_money' => $input_money));
                        $input_money = 0;
                        $goods_sum = 0;
                    }
                } else {
                    $order += array($a => array('order' => 0, 'goods' => $goods_name, 'input_money' => 0));
                }

            }

        }
        helper::json(0, $order);
    }

    //周投产比数据接口
    public function actionGetWeekPro1()
    {
        $date = $_POST['date'];
        $goods_id = $_POST['gd_id'];
        $weekPro = array();
        //投入金额
        $putin_week_money = 0;
        //产出金额
        $delivery_week_money = 0;
        for ($i = 0; $i < 5; $i++) {
            $stat_date = $date - (($i + 1) * 7 - 1) * 86400;
            $end_date = $date - ($i * 7 - 1) * 86400 - 1;
            if ( $goods_id==1 ){
                $goods = Goods::model()->findAll();
            }else{
                $goods = Goods::model()->findAllByAttributes(array('cat_id' => $goods_id));
            }
            $goods_name = Linkage::model()->get_name($goods_id);
            $goods_sum = 0;
            foreach ($goods as $k => $v) {
                //获取客服部信息  获取对应预估发货率
                $customerList = CustomerServiceManage::model()->getCustomerServiceList();
                foreach ($customerList as $cL => $cc) {
                    $goods_order = $this->getInMoney($v['id'], $cc['id'], $cc['estimate_rate'], $stat_date, $end_date);
                    $goods_sum += $goods_order;
                }
                $putin_week_money += $this->getOutMoney($v['id'], $stat_date, $end_date);
                $delivery_week_money += $goods_sum;
                $goods_sum = 0;
            }
            $weekPro[] = array('putin_week_money' => $putin_week_money, 'delivery_week_money' => $delivery_week_money, 'date' => $end_date);
            $putin_week_money = 0;
            $delivery_week_money = 0;
        }
        krsort($weekPro);
        helper::json(0, $weekPro);
    }

    //获取投入金额
    public function getOutMoney($goods_id, $stat_date, $end_date)
    {
        //获取对应的成本明细列表
        $sql = " SELECT SUM(money) as money from stat_cost_detail where goods_id='{$goods_id}' 
                                    and stat_date between '{$stat_date}' and '{$end_date}'";
        $input_stat_money = Yii::app()->db->createCommand($sql)->queryAll();
        //获取对应的修正成本列表
        $sql = " SELECT SUM(fixed_cost) as fixed_cost from fixed_cost_new where goods_id='{$goods_id}' 
                                    and stat_date between '{$stat_date}' and '{$end_date}'";
        $input_fixed_money = Yii::app()->db->createCommand($sql)->queryAll();
        $input_money = $input_stat_money[0]['money'] + $input_fixed_money[0]['fixed_cost'];
        return $input_money;
    }

    //获取产出数据
    public function getInMoney($goods_id, $customer_service_id, $estimate_rate, $stat_date, $end_date)
    {
        $sql = "select SUM(order_money) as order_money from (SELECT * from place_indep_order_manage WHERE goods_id='{$goods_id}' and order_date between '1488297600' and '1490975999') as a 
                                          where customer_service_id='{$customer_service_id}' and order_date between '{$stat_date}' and '{$end_date}'";
        $order_indep_money = Yii::app()->db->createCommand($sql)->queryAll();
        $sql = "select SUM(order_money) as order_money from (SELECT * from place_norm_order_manage WHERE goods_id='{$goods_id}' and order_date between '1488297600' and '1490975999') as a 
                                          where customer_service_id='{$customer_service_id}' and order_date between '{$stat_date}' and '{$end_date}'";
        $order_norm_money = Yii::app()->db->createCommand($sql)->queryAll();
        $goods_order = ($order_indep_money[0]['order_money'] + $order_norm_money[0]['order_money']) * $estimate_rate / 100;
        return $goods_order;
    }

    /**
     * 获取运营表数据
     * 游俊鸿
     * author: yjh
     */
    public function actionGetOperateData()
    {
        $params = array();
        $params['start_date'] = $this->get('start_date');
        $params['end_date'] = $this->get('end_date');
        $params['csid'] = $this->get('csid');
        $params['goodsid'] = $this->get('goodsid');
        $params['pgid'] = $this->get('pgid');
        $params['tgid'] = $this->get('tgid');
        $params['bsid'] = 0;
        $params['chg_id'] = '';
        $ret = StatCostDetail::model()->getOperateTableData($params);
        helper::json(0, $ret['info']);
    }

    /**
     * 获取运营表数据
     * 游俊鸿
     * author: yjh
     */
    public function actionGetPerfData()
    {
        $params = array();
        $params['start_date'] = $this->get('start_date');
        $params['end_date'] = $this->get('end_date');
        $params['csid'] = $this->get('csid');
        $params['goodsid'] = $this->get('goodsid');
        $params['pgid'] = $this->get('pgid');
        $params['tgid'] = $this->get('tgid');
        $params['bsid'] = 0;
        $params['chg_id'] = '';
        $ret = StatCostDetail::model()->getPerfTableData($params);
        helper::json(0, $ret['info']);
    }

    //获取搜索条件

    /**
     * 获取客服部列表
     * author: yjh
     */
    public function actionGetCustomerList()
    {
        $ret = CustomerServiceManage::model()->getCustomerServiceList();
        helper::json(0, $ret);
    }

    /**
     * 获取推广小组
     * author: yjh
     */
    public function actionGetPromotionGroups()
    {
        $ret = Linkage::model()->getPromotionGroupList();
        helper::json(0, $ret);
    }

    /**
     * 获取业务类型
     * author: yjh
     */
    public function actionGetBssTypes()
    {
        $ret = Dtable::toArr(BusinessTypes::model()->findAll());
        helper::json(0, $ret);
    }

    /**
     * 获取商品列表
     * author: yjh
     */
    public function actionGetGoodsList()
    {
        $csid = $this->get('csid');
        $ret = $csid ? CustomerServiceRelation::model()->getGoodsList($csid) : Dtable::toArr(Goods::model()->findAll());
        helper::json(0, $ret);
    }


    /**
     * 获取推广人员
     * author: yjh
     */
    public function actionGetPromotionStaff()
    {
        $ret = PromotionStaff::model()->getPromotionStaffByPg();
        helper::json(0, $ret);

    }

    /**
     * 获取计费方式
     * author: yjh
     */
    public function actionGetChargeTypes()
    {
        $business_type = $this->get('business_type');
        $ret = $business_type ? BusinessTypeRelation::model()->getChargeTypes($business_type) : array();
        helper::json(0, $ret);

    }
//    public function actionApi() {
//        $url = $this->get('url');
//        $uri = "skey=%40crypt_d1652e6_892ae520759a682be7bb8fb4656d21a1&sid=GDCqZzO4pzThRp0x&uin=836832720&deviceid=e115655038867604&synckey=1_656065952%7C2_656066315%7C3_656066266%7C11_656066287%7C13_655870131%7C203_1502855587%7C1000_1502844121%7C1001_1502844152&_=1502858331704"; //微信网页版里面复制那个url
//        $cookie = "pgv_pvid=3675592031; RK=iC/CaQ/P7q; pgv_pvi=5565337600; webwxuvid=8d70e00919feeb89fa08ff633c44dde99e6679b89156c5fe22d656065c29cb1677322ca482015b8fa024d5b6b339289a; ptcz=a9de17d1187d666006ead1a5f436fa7c95b763e596b921421024948be99912c2; pt2gguin=o3002779054; pgv_si=s1060750336; mm_lang=zh_CN; webwx_auth_ticket=CIsBEP+b9ZIMGoAB/61uObtkEbs25gP2/1HV837QGlIWHrRA9Bki+4O5k1fAgpZ2DcPdISfxIsaV8QegmEoVmE/z7k6flsAxd1jt9z4wAc+gvbdCuc5i0ygo7ofCtyH251FkHwV0mQKWcKxj0axSgra1MXAkWrTJjzYClt5JC4NALrtUhWcr99J2GIU=; wxloadtime=1502858312_expired; wxpluginkey=1502844121; wxuin=836832720; wxsid=GDCqZzO4pzThRp0x; webwx_data_ticket=gSdkSCRC1xzh1IikZShqo2zc"; //
//        $api = "https://wx.qq.com/cgi-bin/mmwebwx-bin/webwxcheckurl?requrl=" . $url . "&" . $uri;
//        $curl = curl_init();
//
//        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
//        curl_setopt($curl, CURLOPT_URL, $api);
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
//        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
//        curl_setopt($curl, CURLOPT_HEADER, true);
//        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0);
//        curl_setopt($curl, CURLOPT_MAXREDIRS, 1);
//        $res = curl_exec($curl);
//        curl_close($curl);
//        helper::json(0, $res);
//    }
}