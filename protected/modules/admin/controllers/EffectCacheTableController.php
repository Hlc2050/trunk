<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/17
 * Time: 11:14
 */

/**
 *
 *author：
 **/
class EffectCacheTableController extends AdminController
{
    public function actionIndex()
    {
        $page = $this->getChannelData();
        $page['params_groups'] = vars::$fields['effect_cache_table'];
        $this->render('index', array('page' => $page));
    }


    private function getChannelData()

    {
        $page = array();
        $params['where'] = '';
        $option['where'] = '';
        $condition = $this->get('condition') ? $this->get('condition') : "channel_id";
        $field_option = "&condition=" . $condition;
        $field_option .= "&group_id=" . $this->get('group_id');

        //发货日期
        $start_delivery_date = $end_addfan_date = '';
        //开始日期不为空但是结束日期为空，结束日期为当天
        if ($this->get('start_delivery_date') != null && $this->get('end_delivery_date') == null) {
            $start_delivery_date = strtotime($this->get('start_delivery_date'));
            $end_addfan_date = strtotime('now');
            //开始日期为空但是结束日期不为空,开始日期是结束日期前一周
        } elseif ($this->get('start_delivery_date') == null && $this->get('end_delivery_date') != null) {
            $start_delivery_date = strtotime($this->get('end_delivery_date')) - 86400 * 7;
            $end_addfan_date = strtotime($this->get('end_delivery_date'));
            //开始日期和结束日期都不为空
        } elseif ($this->get('start_delivery_date') != null && $this->get('end_delivery_date') != null) {
            $start_delivery_date = strtotime($this->get('start_delivery_date'));
            $end_addfan_date = strtotime($this->get('end_delivery_date'));
        } else {
            //如果没有搜索条件加粉日期，则加粉日期在本月1号到昨天
            $firstDay = date('Y-m-01', strtotime("now"));
            $start_delivery_date = strtotime($firstDay);
            $end_addfan_date = strtotime(date('Y-m-d', strtotime("now -1 day")));
        }

        $params['where'] .= " and (delivery_date>=" . $start_delivery_date . " and delivery_date<=" . $end_addfan_date . ")";;
        $page['start_delivery_date'] = date('Y-m-d', $start_delivery_date);
        $page['end_delivery_date'] = date('Y-m-d', $end_addfan_date);

        $field_option .= "&start_delivery_date=" . $page['start_delivery_date'] . "&end_delivery_date=" . $page['end_delivery_date'];

        //加粉日期
        $start_time = $end_time = '';
        //开始日期不为空但是结束日期为空，结束日期为当天
        if ($this->get('start_addfan_date') != null && $this->get('end_addfan_date') == null) {
            $start_time = strtotime($this->get('start_addfan_date'));
            $end_time = strtotime('now');

            //开始日期为空但是结束日期不为空,开始日期是结束日期前一周
        } elseif ($this->get('start_addfan_date') == null && $this->get('end_addfan_date') != null) {
            $start_time = strtotime($this->get('end_addfan_date')) - 86400 * 7;
            $end_time = strtotime($this->get('end_addfan_date'));
            //开始日期和结束日期都不为空
        } elseif ($this->get('start_addfan_date') != null && $this->get('end_addfan_date') != null) {
            $start_time = strtotime($this->get('start_addfan_date'));
            $end_time = strtotime($this->get('end_addfan_date'));
        } else {
            //如果没有搜索条件，则加粉日期在本月1号到昨天
            $firstDay = date('Y-m-01', strtotime("now"));
            $start_time = strtotime($firstDay);
            $end_time = strtotime(date('Y-m-d', strtotime("now -1 day")));
        }

        //如果没有搜索条件加粉日期，则加粉日期在在本月1-31号,加粉日期减5天
        $option['where'] .= " and (stat_date>=" . ($start_time - 5 * 86400) . " and stat_date<= " . $end_time . ")";
        $params['where'] .= " and (addfan_date>=" . $start_time . " and addfan_date<= " . $end_time . ")";

        $page['start_addfan_date'] =  date('Y-m-d', $start_time);
        $page['end_addfan_date'] = date('Y-m-d', $end_time);

        $field_option .= "&start_addfan_date=" . $page['start_addfan_date'] . "&end_addfan_date=" . $page['end_addfan_date'];

        //推广人员
        $get_promotion_id = $this->data_authority();
        if ($get_promotion_id !== 0) {
            $params['where'] .= " and tg_uid in (" . $get_promotion_id . ") ";
            $option['where'] .= " and tg_uid in (" . $get_promotion_id . ")";
            $field_option .= "&user_id in (" . $get_promotion_id . ") ";
        }
        if ($this->get('user_id') != '') {
            $option['where'] .= " and tg_uid = " . intval($this->get('user_id')) . "";
            $params['where'] .= " and tg_uid = " . intval($this->get('user_id')) . "";
            $field_option .= "&user_id=" . $this->get('user_id');
        }

        //商品
        if ($this->get('goods_id') != '') {
            $option['where'] .= " and goods_id = " . intval($this->get('goods_id')) . "";
            $params['where'] .= " and goods_id = " . intval($this->get('goods_id')) . "";
            $field_option .= "&goods_id=" . $this->get('goods_id');
        }

        $temp_partner = array();
        //合作商
        if ($this->get('partner_name') != '') {
            $partner_name = Partner::model()->findAll('name like ' . "'%" . $this->get('partner_name') . "%'");
            if ($partner_name != null) {
                $count_partner = count($partner_name);
                for ($i = 0; $i < $count_partner; $i++) {
                    $temp_partner[] = $partner_name[$i]['id'];
                }
//                $option['where'] .= " and partner_id in (" . $temp_partner_id . ")";
                $field_option .= "&partner_id=" . $this->get('partner_id');
            } else {
                $this->msg(array('state' => 0, 'msgwords' => '没有该合作商！'));
            }
        }

        $temp_channel = array();
        //渠道名称
        if ($this->get('channel_name') != '') {
            $channel_name = Channel::model()->findAll('channel_name like ' . "'%" . $this->get('channel_name') . "%'");
            if ($channel_name != null) {
                $count_channel = count($channel_name);

                for ($i = 0; $i < $count_channel; $i++) {
                    $temp_channel[] = $channel_name[$i]['id'];
                }
//                $option['where'] .= " and channel_id = " . intval($channel_name['id']) . "";
                $field_option .= "&channel_id=" . $this->get('channel_id');
            } else {
                $this->msg(array('state' => 0, 'msgwords' => '没有该渠道名称！'));
            }
        }

        //业务类型
        if ($this->get('bsid') != '') {
            $params['where'] .= " and business_type = " . intval($this->get('bsid')) . "";
            $field_option .= "&business_type=" . $this->get('business_type');
        }
        //计费方式
        if ($this->get('chgId') != '') {
            $params['where'] .= " and charging_type = " . intval($this->get('chgId')) . "";
            $field_option .= "&charging_type=" . $this->get('charging_type');
        }

        $where_delivery = ltrim(trim($params['where'], " "), "and");

        //订单发货
        $sql = "select delivery_date,addfan_date,weixin_id,wechat_id,business_type,charging_type,goods_id,customer_service_id,tg_uid,sum(delivery_money) as delivery_money,count(id) as delivery_count from delivery_norm_order_manage  where " . $where_delivery . " GROUP BY weixin_id,addfan_date";
        $delivery_data = Yii::app()->db->createCommand($sql)->queryAll();

        //页面展示
        $page['field_option'] = $field_option;

        $temp = array();
        $temp_info = array();

        $where = ltrim(trim($option['where'], " "), "and");

        //成本明细
        $sql_condtion = "select weixin_id,stat_date,channel_id,partner_id from stat_cost_detail where " . $where;
        $data = Yii::app()->db->createCommand($sql_condtion)->queryAll();


        $w_arr = array();
        //成本明细的微信号和上线日期组成的key
        foreach ($data as $k => $v) {
            $key = $v['weixin_id'] . "_" . $v['stat_date'];
            //相同的key注销
            if (key_exists($key, $temp)) {
                unset($temp[$key]);
                $w_arr[] = $key;
            } else {
                $temp[$key] = $v;
            }
        }

        $null_data['delivery_count'] = null;
        $null_data['delivery_money'] = null;

        foreach ($delivery_data as $k => $val) {
            for ($i = 0; $i <= 5; $i++) {
                //订单发货的数据去成本明细找，循环五次，找不到为空
                $addfan_date = $val['addfan_date'] - 86400 * $i;
                $key = $val['weixin_id'] . '_' . $addfan_date;
                //如果这个key在成本明细有两条，属于未归属数据
                if (in_array($key, $w_arr)) {
                    $null_data['delivery_count'] += $val['delivery_count'];
                    $null_data['delivery_money'] += $val['delivery_money'];
                    break;
                }
                //订单发货的key在成本明细的key里面寻找，如果找得到就归属
                if (key_exists($key, $temp)) {
                    $temp_info[$k] = $val;
                    $temp_info[$k]['channel_id'] = $temp[$key]['channel_id'];
                    $temp_info[$k]['partner_id'] = $temp[$key]['partner_id'];
                    break;
                }
                //找不到就未归属
                if (5 == $i) {
                    $null_data['delivery_count'] += $val['delivery_count'];
                    $null_data['delivery_money'] += $val['delivery_money'];
                }
            }
        }

        //归属数据和为归属数据合并
        $temp_data = array_merge($temp_info, array($null_data));

        $b = array();
        foreach ($temp_data as $v) {
            //partner_id是否在搜索条件内
            if($temp_partner && !in_array($v['partner_id'],$temp_partner)){
               continue;
            }
            //channel_id是否在搜索条件内
            if($temp_channel && !in_array($v['channel_id'],$temp_channel)){
                continue;
            }

            $page['listdata']['delivery_money']  += $v['delivery_money'];
            $page['listdata']['delivery_count']  += $v['delivery_count'];
            //把传过来的condition的id当成b数组的键
            if (!isset($b[$v[$condition]])) $b[$v[$condition]] = $v;
            else {
                $b[$v[$condition]]["delivery_money"] += $v['delivery_money'];
                $b[$v[$condition]]["delivery_count"] += $v['delivery_count'];
            }
        }

        //排序
        $temp_data = $b;
        if ($this->get('delivery_count_asc') != null) {
            $asc = $this->get('delivery_count_asc');
        } elseif ($this->get('delivery_money_asc') != null) {
            $asc = $this->get('delivery_money_asc');
        }

        //合作商和渠道页面传过来的值（排序用）
        $field = $this->get('field');
        if ($field && $asc != 0) {
            foreach ($temp_data as $arr2) {
                $flag[] = $arr2[$field];
            }
            if ($asc == 1) {
                array_multisort($flag, SORT_ASC, $temp_data);
            } else
                array_multisort($flag, SORT_DESC, $temp_data);
        }

        $partner_Ids = implode(',', array_unique(array_column($temp_data, 'partner_id')));
        $channel_Ids = implode(',', array_unique(array_column($temp_data, 'channel_id')));
        $goods_Ids = implode(',', array_unique(array_column($temp_data, 'goods_id')));
        $business_Type = implode(',', array_unique(array_column($temp_data, 'business_type')));

        if ($partner_Ids) {
            $page['listdata']['partner_name'] = Partner::model()->getPartnerNames($partner_Ids);
        }
        if ($channel_Ids) {
            $page['listdata']['channel_name'] = Channel::model()->getChannelNames($channel_Ids);
        }
        if ($goods_Ids) {
            $page['listdata']['goods_name'] = Goods::model()->getGoodsNames($goods_Ids);
        }
        if ($business_Type) {
            $page['listdata']['business_name'] = BusinessTypes::model()->getBNames($business_Type);
        }

        $page['listdata']['list'] = $temp_data;

        return $page;
    }
}