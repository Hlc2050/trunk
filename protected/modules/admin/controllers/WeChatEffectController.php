<?php

/**
 * 微信效果表
 * Class BssOperationTableController
 */
class WeChatEffectController extends AdminController
{
    public function actionIndex()
    {
        $page = $this->getWechatEffectData();
        $this->render('index', array('page' => $page));
    }

    private function getWechatEffectData(){
        $data = array();//数据汇总
        $date = helper::makeup_data($this->get('start_date'), $this->get('end_date'));
        $data['first_day'] = date("Y-m-d", $date['start_day']);
        $data['last_day'] = date("Y-m-d", $date['end_day']);
        $start_day = $date['start_day'];
        $end_day = $date['end_day'];
        $params  = "1";
        $where = "";
        //客服部
        if ($this->get('csid')) {
            $params .= " and a.customer_service_id='" . $this->get('csid')."'";
            $where .= " and customer_service_id='" . $this->get('csid')."'";
        }
        //商品
        if ($this->get('goods_id') != 0) {
            $where .= " and goods_id='" . $this->get('goods_id')."'";
            $params .= " and a.goods_id='" . $this->get('goods_id')."'";
        }
        //推广人员
        $get_promotion_id = $this->data_authority(1);
        if ($get_promotion_id !== 0) {
            $where .= " and promotion_staff_id in (" . $get_promotion_id . ") ";
            $params .= " and tg_uid in (" . $get_promotion_id . ") ";
        }
        if ($this->get('tg_id')) {
            $where .= " and promotion_staff_id='". $this->get('tg_id')."'";
            $params .= " and tg_uid='" . $this->get('tg_id')."'";
        }
        if($this->get('wechat_id') != ''){
            $where .= " and wechat_id  like '%" . $this->get('wechat_id') . "%'";
        }
        $weChatList = WeChat::model()->getWechatList($where. " order by id desc");
        $wechatArr=array_column($weChatList, 'id');
        $wechatStr=implode(',',$wechatArr);
        if($wechatStr) $params.= " and weixin_id in (".$wechatStr.")";
        else $params.= " and weixin_id =0";
        $condition =  $params . " and order_date between $start_day and $end_day  group by weixin_id,customer_service_id";
        $sql = 'select  weixin_id,wechat_id,sum(order_money) as order_money from (SELECT weixin_id,wechat_id,customer_service_id,cname,SUM(order_money)*estimate_rate AS order_money FROM place_norm_order_manage as a left join customer_service_manage as c on c.id=a.customer_service_id WHERE ' . $condition.') t1 group by weixin_id';
        $normOrderInfo = Yii::app()->db->createCommand($sql)->queryAll();
        //$normOrderInfo = PlaceNormOrderManage::model()->getEstimateMoneyTypeOne($condition);
        $wechatArr_1=array_column($normOrderInfo, 'weixin_id');
        $normOrderInfo = array_combine($wechatArr_1, $normOrderInfo);

        $condition = $params . " and order_date between $start_day and $end_day group by weixin_id,customer_service_id";
        $sql = 'select  weixin_id,wechat_id,sum(order_money) as order_money from (SELECT weixin_id,wechat_id,customer_service_id,cname,SUM(order_money)*estimate_rate AS order_money FROM place_indep_order_manage as a left join customer_service_manage as c on c.id=a.customer_service_id WHERE ' . $condition.') t1 group by weixin_id';
        $indepOrderInfo = Yii::app()->db->createCommand($sql)->queryAll();
        //$indepOrderInfo = PlaceIndepOrderManage::model()->getEstimateMoneyTypeOne($condition);
        $wechatArr_2=array_column($indepOrderInfo, 'weixin_id');
        $indepOrderInfo = array_combine($wechatArr_2, $indepOrderInfo);

        $condition = $params . " and addfan_date between $start_day and $end_day group by weixin_id";
        $sql = 'SELECT weixin_id,wechat_id,SUM(addfan_count) as fans_count FROM fans_input_manage as a WHERE ' . $condition;
        $fansInfo = Yii::app()->db->createCommand($sql)->queryAll();
        //my_print($fansInfo);
        $wechatArr_3=array_column($fansInfo, 'weixin_id');
        $fansInfo = array_combine($wechatArr_3, $fansInfo);
        //投入金额
        $condition = $params . " and stat_date between $start_day and $end_day group by weixin_id";
        $sql = 'SELECT weixin_id,b.wechat_id,SUM(money) AS money FROM stat_cost_detail as a left join wechat as b on b.id=a.weixin_id WHERE ' . $condition;
        $moneyInfo = Yii::app()->db->createCommand($sql)->queryAll();
        $wechatArr_4=array_column($moneyInfo, 'weixin_id');
        $moneyInfo = array_combine($wechatArr_4, $moneyInfo);

        $sql = 'SELECT id, weixin_id,SUM(fixed_cost) AS fixed_cost  FROM fixed_cost_new as a WHERE ' . $condition;
        $fixedCostInfo = Yii::app()->db->createCommand($sql)->queryAll();
        $wechatArr_5=array_column($fixedCostInfo, 'weixin_id');
        $fixedCostInfo = array_combine($wechatArr_5, $fixedCostInfo);

//        $weChatList = WeChat::model()->getWechatList($where. " order by id desc");
//        $wechatArr_6=array_column($weChatList, 'id');
//        $weChatList = array_combine($wechatArr_6, $weChatList);

//        if(!empty($where)){
//            $wechatArr = array_unique(array_merge($wechatArr_6,$wechatArr_5,$wechatArr_4, $wechatArr_3,$wechatArr_2,$wechatArr_1));
//        }else $wechatArr = $wechatArr_6;
        $tempData  = array();
        foreach ($wechatArr as $value){
            $id = $value;
            $tempData[$id]['wechat_id'] = array_key_exists($id, $weChatList)?$weChatList[$id]['wechat_id']:WeChat::model()->findByPk($id)->wechat_id;
            $tempData[$id]['estimate_money'] = array_key_exists($id, $normOrderInfo)?$normOrderInfo[$id]['order_money']:0;
            $tempData[$id]['estimate_money'] += array_key_exists($id, $indepOrderInfo)?$indepOrderInfo[$id]['order_money']:0;
            $tempData[$id]['fans_count'] = array_key_exists($id, $fansInfo)?$fansInfo[$id]['fans_count']:0;
            $tempData[$id]['money'] = array_key_exists($id, $moneyInfo)?$moneyInfo[$id]['money']:0;
            $tempData[$id]['money'] += array_key_exists($id, $fixedCostInfo)?$fixedCostInfo[$id]['fixed_cost']:0;
            $tempData[$id]['fans_cost'] =$tempData[$id]['fans_count']==0?0:round( $tempData[$id]['money'] / $tempData[$id]['fans_count']);//平均进粉成本
            $tempData[$id]['fans_avg'] = $tempData[$id]['fans_count']==0?0:round($tempData[$id]['estimate_money']*0.01 / $tempData[$id]['fans_count']);//均粉产出
            $tempData[$id]['estimate_money'] = $tempData[$id]['estimate_money']*0.01;//均粉产出
        }
        $data['info']=$tempData;
        return $data;

    }
}