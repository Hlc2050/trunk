<?php

/**
 * 微信号预估发货金额表
 * Created by PhpStorm.
 * User: fang
 * Date: 2016/12/30
 * Time: 16:27
 */
class EffectWechatDeliverController extends AdminController
{

    public function actionIndex()
    {
        $page = $this->getDeliveInfo();
        $this->render('index', array('page' => $page));
    }

    /**
     * 微信号预估发货金额
     * @return array
     * author: yjh
     */
    private function getDeliveInfo()
    {
        $start = microtime();
        $data = array();
        $params = "1";
        $where = '';
        //推广人员
        if ($this->get('user_id') != '') {
            $params .= " and a.tg_uid = " . $this->get('user_id');
            $where .= " and promotion_staff_id = " . $this->get('user_id');
        }
        //业务类型
        if ($this->get('business_type') != '') {
            $params .= " and a.business_type = " . $this->get('business_type');
            $where .= " and business_type = " . $this->get('business_type');
        }
        //商品
        if ($this->get('goods_id') != 0) {
            $params .= " and a.goods_id=" . $this->get('goods_id');
            $where .= " and goods_id=" . $this->get('goods_id');
        }
        //客服部
        if ($this->get('csid') != '') {
            $params .= " and a.customer_service_id = " . $this->get('csid');
            $where .= " and customer_service_id = " . $this->get('csid');
        }
        //计费方式
        if ($this->get('chg_id') != '' && $this->get('chg_id') != 0) {
            $params .= " and a.charging_type = " . $this->get('chg_id');
            $where .= " and charging_type = " . $this->get('chg_id');
        }
        if ($this->get('wechat_id') != '') {
            $where .= " and wechat_id  like '%" . $this->get('wechat_id') . "%'";
        }
        //获取时间段
        $date_num = PartnerCost::model()->getDateInfo($this->get('start_date'), $this->get('end_date'));
        $condition = $params . " and order_date between  ".end($date_num)." and $date_num[0] group by weixin_id,customer_service_id,order_date order by weixin_id desc,order_date desc";
        //订单下单信息
        $normOrderInfo = PlaceNormOrderManage::model()->getEstimateMoneyTypeTwo($condition);
        $wechatArr_1 = array_column($normOrderInfo, 'weixin_id');

        $key_arr1 = $key_arr2  = array();
        if ($normOrderInfo) {
            foreach ($normOrderInfo as $value) {
                $key_arr1[] = '"' . $value['weixin_id'] . "_" . $value['order_date'] . '"';
            }
            $normOrderInfo = array_combine($key_arr1, $normOrderInfo);
        }
        $indepOrderInfo = PlaceIndepOrderManage::model()->getEstimateMoneyTypeTwo($condition);
        if ($indepOrderInfo) {
            foreach ($indepOrderInfo as $value) {
                $key_arr2[] = '"' . $value['weixin_id'] . "_" . $value['order_date'] . '"';
            }
            $indepOrderInfo = array_combine($key_arr2, $indepOrderInfo);
        }
        $wechatArr_2 = array_column($indepOrderInfo, 'weixin_id');

        $weChatList = WeChat::model()->getWechatList($where . " order by id desc");
        $wechatArr_3 = array_column($weChatList, 'id');
        $weChatList = array_combine($wechatArr_3, $weChatList);

        if (!empty($where)) {
            $wechatArr = array_unique(array_merge($wechatArr_3, $wechatArr_2, $wechatArr_1));
        } else $wechatArr = $wechatArr_3;
        $tempData = array();
        foreach ($wechatArr as $value) {
            $id = $value;
            $tempData[$id][] = array_key_exists($id, $weChatList) ? $weChatList[$id]['wechat_id'] : WeChat::model()->findByPk($id)->wechat_id;
            foreach ($date_num as $v) {
                $key = '"' . $value . "_" . $v . '"';
                $normEstimateMoney = array_key_exists($key, $normOrderInfo) ? $normOrderInfo[$key]['order_money'] : 0;
                $indepEstimateMoney = array_key_exists($key, $indepOrderInfo) ? $indepOrderInfo[$key]['order_money'] : 0;
                $tempData[$id][] = round(($normEstimateMoney + $indepEstimateMoney)*0.01);
            }
        }

        $end = microtime();
        //my_print($end - $start);
        $data['info'] = $tempData;
        return $data;
    }

}
