<?php

/**
 * 进线观察表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2017/11/3
 * Time: 10:13
 */
class OrderObserveTableController extends AdminController
{

    /**
     * 首页
     * author: yjh
     */
    public function actionIndex()
    {
        $page = array();
        $params['where'] = $this->getCondition();
        //合作商 渠道名称 渠道编码 推广小组 推广人员 客服部 商品
        $params['order'] = "  order by a.channel_id desc    ";
//        $params['pagesize'] = 300;
        $params['pagebar'] = 1;
        $params['group'] = " group by a.channel_id,a.customer_service_id";
        $params['select'] = "COUNT(*) as in_count,a.promotion_staff_id,a.partner_id,a.channel_id,a.goods_id,a.customer_service_id,o_date,a.promotion_id";
        $page['listdata'] = Dtable::model('order_manage')->orderdata($params);
        $page['listdata']['url'] = urlencode('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . '/admin/orderObserveTable/index');
        $partnerIds = implode(',', array_unique(array_column($page['listdata']['list'], 'partner_id')));
        $channelIds = implode(',', array_unique(array_column($page['listdata']['list'], 'channel_id')));
        $cslIds = implode(',', array_unique(array_column($page['listdata']['list'], 'customer_service_id')));
        $tgUids = implode(',', array_unique(array_column($page['listdata']['list'], 'promotion_staff_id')));

        if ($partnerIds)
            $page['listdata']['partnerNames'] = Partner::model()->getPartnerNames($partnerIds);
        if ($channelIds) {
            $page['listdata']['channelCodes'] = Channel::model()->getChannelCodes($channelIds);
            $page['listdata']['channelNames'] = Channel::model()->getChannelNames($channelIds);
        }
        if ($cslIds)
            $page['listdata']['csNames'] = CustomerServiceManage::model()->getCSNames($cslIds);
        if ($tgUids)
            $page['listdata']['tgNames'] = PromotionStaff::model()->getTgNames($tgUids);
        $this->render('index', array('page' => $page));

    }

    /**
     * 渠道每小时详情
     * author: yjh
     */
    public function actionChannelDetail()
    {
        $page = array();
        $stat_date = date('Ymd', time());
        $channel_id = $this->get('channel_id');
        $csid = $this->get('csid');
        if (!$channel_id) $this->msg(array('state' => 0, 'msgwords' => '未传入渠道id！'));
        if (!$csid) $this->msg(array('state' => 0, 'msgwords' => '未传入客服部id！'));
        $sql = "select channel_id,customer_service_id ,count(*) as in_count,o_hour from order_manage where o_date='$stat_date' and channel_id=" . $channel_id . " and customer_service_id=" . $csid . " group by o_hour order by o_hour asc ";
        $info = Yii::app()->order_db->createCommand($sql)->queryAll();
        $keyArr = array_column($info, 'o_hour');
        $info = array_combine($keyArr, $info);
        $ret = array();
        for ($i = 0; $i < 24; $i++) {
            $t_hour = $stat_date . sprintf("%02d", $i);
            if (array_key_exists($t_hour, $info)) {
                $ret[$i] = $info[$t_hour];
            } else {
                $ret[$i]['channel_id'] = $channel_id;
                $ret[$i]['customer_service_id'] = $csid;
                $ret[$i]['in_count'] = $info[$t_hour]['in_count']?$info[$t_hour]['in_count']:0;
                $ret[$i]['o_hour'] = $t_hour;
            }
            $page['total_count']+= $ret[$i]['in_count'];

        }

        $page['info'] = $ret;

        $this->render('channelDetail', array('page' => $page));

    }

    /**
     * 商品每小时详情
     * author: yjh
     */
    public function actionPackageDetail()
    {
        $page = array();
        $stat_date = date('Ymd', time());
        $channel_id = $this->get('channel_id');
        $csid = $this->get('csid');
        if (!$channel_id) $this->msg(array('state' => 0, 'msgwords' => '未传入渠道id！'));
        if (!$csid) $this->msg(array('state' => 0, 'msgwords' => '未传入客服部id！'));
        $sql = "select b.package_id,count(*) as in_count,o_hour from order_manage as a left join order_goods_manage as b on b.order_id =a.id where  o_date='$stat_date' and  channel_id=" . $channel_id . " and customer_service_id=" . $csid . " group by o_hour,b.package_id order by o_hour asc ";
        $info = Yii::app()->order_db->createCommand($sql)->queryAll();
        $page['packages'] = array_unique(array_column($info, 'package_id'));
        $keyArr = array();
        if ($info) {
            foreach ($info as $value) {
                $keyArr[] = $value['o_hour'] . '_' . $value['package_id'];
            }
            $info = array_combine($keyArr, $info);
        }

        $ret=array();
        for ($i = 0; $i < 24; $i++) {
            foreach ($page['packages'] as $v) {
                $t_hour = $stat_date . sprintf("%02d", $i);
                $key = $t_hour . '_' . $v;
                if (array_key_exists($key, $info)) {
                    $ret[$i][$v] = $info[$key];
                } else {
                    $ret[$i][$v]['package_id'] = $v;
                    $ret[$i][$v]['in_count'] = $info[$key]['in_count']?$info[$key]['in_count']:0;
                    $ret[$i][$v]['o_hour'] = $t_hour;
                }
                $page['total_count'][$v]+=$ret[$i][$v]['in_count'];

            }
        }
        $page['info'] = $ret;
        $this->render('packageDetail', array('page' => $page));
    }

    /**
     * 获取搜索条件
     * @return string
     * author: yjh
     */
    private function getCondition()
    {
        $condition = '';
        $stat_date = date('Ymd', time());
        $condition =" and o_date='$stat_date'";

        if ($this->get('csid') != '') $condition = "and(a.customer_service_id = " . $this->get('csid') . ")";
        if ($this->get('partner_name') != '') {
            $sql = "select id from `partner` where name like '%" . $this->get('partner_name') . "%'";
            $partnerIds = $this->query($sql);
            if ($partnerIds) {
                $partnerIds = array_column($partnerIds, 'id');
                $condition .= " and(a.partner_id in (" . implode(',', $partnerIds) . ")) ";
            } else
                $condition .= " and(a.partner_id =0) ";
        }

        if ($this->get('search_type') == 'channel_code' && $this->get('search_txt')) {
            $sql = "select id from `channel` where channel_code like '%" . $this->get('search_txt') . "%'";
            $channelIds = $this->query($sql);
            if ($channelIds) {
                $channelIds = array_column($channelIds, 'id');
                $condition .= " and(a.channel_id in (" . implode(',', $channelIds) . ")) ";
            } else
                $condition .= " and(a.channel_id =0) ";
        } else if ($this->get('search_type') == 'channel_name' && $this->get('search_txt')) {
            $sql = "select id from `channel` where channel_name like '%" . $this->get('search_txt') . "%'";
            $channelIds = $this->query($sql);
            if ($channelIds) {
                $channelIds = array_column($channelIds, 'id');
                $condition .= " and(a.channel_id in (" . implode(',', $channelIds) . ")) ";
            } else
                $condition .= " and(a.channel_id =0) ";
        }
        $pgid = $this->get('pgid');
        $tg_id = $this->get('tg_id');
        //推广人员
        if ($tg_id != 0) {
            $condition .= " and promotion_staff_id=" . $tg_id;
        } elseif ($pgid != 0) {
            $promotionStaffArr = PromotionStaff::model()->getPromotionStaffByPg($pgid);
            if (!$promotionStaffArr) $condition .= " and tg_uid=0";
            else {
                $str = implode(',', array_column($promotionStaffArr, 'user_id', 'user_id'));
                $condition .= " and promotion_staff_id in (" . $str . ")";
            }
        }
        //查看人员权限
        $result = $this->data_authority();
        if ($result != 0) {
            $condition .= " and promotion_staff_id in ($result) ";
        }


        return $condition;
    }

}