<?php

/**
 * 渠道缓存效果表控制器
 * User: Administrator
 * Date: 2018/4/17
 * Time: 11:14
 */
class EffectCacheTableController extends AdminController
{
    public function actionIndex()
    {
        $page = $this->getChannelData();
        $page['params_groups'] = vars::$fields['effect_cache_table'];
        $this->render('index', array('page' => $page));
    }

    /**
     * 获取发货日期
     */
    private function getDate($start, $end)
    {
        $date = array();
        //开始日期不为空但是结束日期为空，结束日期为当天
        if ($start != null && $end == null) {
            $date['start_date'] = strtotime($start);
            $date['end_date'] = strtotime('now');
            //开始日期为空但是结束日期不为空,开始日期是结束日期前一周
        } elseif ($start == null && $end != null) {
            $date['start_date'] = strtotime($end) - 86400 * 7;
            $date['end_date'] = strtotime($end);
            //开始日期和结束日期都不为空
        } elseif ($start != null && $end != null) {
            $date['start_date'] = strtotime($start);
            $date['end_date'] = strtotime($end);
        } else {
            //如果没有搜索条件加粉日期，则加粉日期在本月1号到昨天
            $firstDay = date('Y-m-01', strtotime("now"));
            $date['start_date'] = strtotime($firstDay);
            $date['end_date'] = strtotime(date('Y-m-d', strtotime("now -1 day")));
        }

        return $date;
    }

    /**
     * 获取搜索条件
     */
    private function getCondtion()
    {
        $data = $page = $temp = $temp_info = array();
        $params = $option = '';

        $condition = $this->get('condition') ? $this->get('condition') : "channel_id";
        $field_option = "&condition=" . $condition;
        $field_option .= "&group_id=" . $this->get('group_id');

        //获取发货日期
        $Deliverydate = $this->getDate($this->get('start_delivery_date'), $this->get('end_delivery_date'));
        $page['start_delivery_date'] = date('Y-m-d', $Deliverydate['start_date']);
        $page['end_delivery_date'] = date('Y-m-d', $Deliverydate['end_date']);

        $params .= " and (delivery_date>=" . $Deliverydate['start_date'] . " and delivery_date<=" . $Deliverydate['end_date'] . ")";;
        $field_option .= "&start_delivery_date=" . date('Y-m-d', $Deliverydate['start_date']) . "&end_delivery_date=" . date('Y-m-d', $Deliverydate['end_date']);

        //加粉日期
        $Fansdate = $this->getDate($this->get('start_addfan_date'), $this->get('end_addfan_date'));
        $page['start_addfan_date'] = date('Y-m-d', $Fansdate['start_date']);
        $page['end_addfan_date'] = date('Y-m-d', $Fansdate['end_date']);
        //如果没有搜索条件加粉日期，则加粉日期在在本月1-31号,加粉日期减5天
        $params .= " and (addfan_date>=" . $Fansdate['start_date'] . " and addfan_date<= " . $Fansdate['end_date'] . ")";
        $option .= " and (stat_date>=" . ($Fansdate['start_date'] - 5 * 86400) . " and stat_date<= " . $Fansdate['end_date'] . ")";
        $field_option .= "&start_addfan_date=" . $page['start_addfan_date'] . "&end_addfan_date=" . $page['end_date'];

        //推广人员
        $get_promotion_id = $this->data_authority();
        if ($get_promotion_id !== 0) {
            $params .= " and tg_uid in (" . $get_promotion_id . ") ";
            $option .= " and tg_uid in (" . $get_promotion_id . ")";
            $field_option .= "&user_id in (" . $get_promotion_id . ") ";
        }
        if ($this->get('user_id') != '') {
            $params .= " and tg_uid = " . intval($this->get('user_id')) . "";
            $option .= " and tg_uid = " . intval($this->get('user_id')) . "";
            $field_option .= "&user_id=" . $this->get('user_id');
        }

        //商品
        if ($this->get('goods_id') != '') {
            $params .= " and goods_id = " . intval($this->get('goods_id')) . "";
            $option .= " and goods_id = " . intval($this->get('goods_id')) . "";
            $field_option .= "&goods_id=" . $this->get('goods_id');
        }

        //业务类型
        if ($this->get('bsid') != '') {
            $params .= " and business_type = " . intval($this->get('bsid')) . "";
            $field_option .= "&business_type=" . $this->get('business_type');
        }
        //计费方式
        if ($this->get('chgId') != '') {
            $params .= " and charging_type = " . intval($this->get('chgId')) . "";
            $field_option .= "&charging_type=" . $this->get('charging_type');
        }

        $data['one'] = trim($params, " ");
        $data['two'] = ltrim(trim($option, " "), "and");
        $data['three'] = $field_option;

        return $data;
    }

    /**
     * 获取数据
     */
    private function getChannelData()
    {
        $page = $temp = $temp_info = $temp_channel = $temp_partner = $w_arr = $delivery_data = array();
        //搜索条件
        $group = $this->get('condition') ? $this->get('condition') : "channel_id";
        $conditon = $this->getCondtion();
        $params['where'] = $conditon['one'];
        $option = $conditon['two'];
        $field_option = $conditon['three'];
        //合作商
        if ($this->get('partner_name') != '') {
            $partner_name = Partner::model()->findAll('name like ' . "'%" . $this->get('partner_name') . "%'");
            if ($partner_name == null) $this->msg(array('state' => 0, 'msgwords' => '没有该合作商！'));
            $count_partner = count($partner_name);
            for ($i = 0; $i < $count_partner; $i++) {
                $temp_partner[] = $partner_name[$i]['id'];
            }
            $field_option .= "&partner_id=" . $this->get('partner_id');
        }
        //渠道名称
        if ($this->get('channel_name') != '') {
            $channel_name = Channel::model()->findAll('channel_name like ' . "'%" . $this->get('channel_name') . "%'");
            if ($channel_name == null) $this->msg(array('state' => 0, 'msgwords' => '没有该渠道名称！'));
            $count_channel = count($channel_name);
            for ($i = 0; $i < $count_channel; $i++) {
                $temp_channel[] = $channel_name[$i]['id'];
            }
            $field_option .= "&channel_id=" . $this->get('channel_id');
        }

        //订单发货
        $params['pagesize'] = '*';
        $params['group'] = "group by weixin_id,addfan_date";
        $params['order'] = "order by id desc";
        $params['select'] = "id,delivery_date,addfan_date,weixin_id,wechat_id,business_type,charging_type,goods_id,customer_service_id,tg_uid,sum(delivery_money) as delivery_money,count(id) as delivery_count";
        $page['listdata'] = Dtable::model(DeliveryNormOrderManage::model()->tableName())->listdata($params);
        $page['field_option'] = $field_option;

        //成本明细
        $sql_condtion = "select weixin_id,stat_date,channel_id,partner_id from stat_cost_detail where " . $option;
        $data = Yii::app()->db->createCommand($sql_condtion)->queryAll();

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
        //获取归属和未归属数据
        foreach ($page['listdata']['list'] as $k => $val) {
            //订单发货的数据去成本明细找，循环五次，找不到为空
            for ($i = 0; $i <= 5; $i++) {
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

        //归属数据和未归属数据合并
        $temp_data = array_merge($temp_info, array($null_data));
        //添加发货量和发货金额
        foreach ($temp_data as $v) {
            //partner_id是否在搜索条件内
            if ($temp_partner && !in_array($v['partner_id'], $temp_partner)) continue;
            //channel_id是否在搜索条件内
            if ($temp_channel && !in_array($v['channel_id'], $temp_channel)) continue;
            $page['listdata']['delivery_money'] += $v['delivery_money'];
            $page['listdata']['delivery_count'] += $v['delivery_count'];
            if (!isset($delivery_data[$v[$group]])) $delivery_data[$v[$group]] = $v;
            else {
                $delivery_data[$v[$group]]["delivery_money"] += $v['delivery_money'];
                $delivery_data[$v[$group]]["delivery_count"] += $v['delivery_count'];
            }
        }

        $temp_data = $delivery_data;
        //排序
        $xu = $this->get('xu');
        $order = $this->get('order');
        $sort = $xu == 'asc' ? 'SORT_DESC' : 'SORT_DESC';
        array_multisort(array_column($temp_data, $order), $sort, $temp_data);

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