<?php

/**
 * 进粉表
 * Created by PhpStorm.
 * User: fang
 * Date: 2016/12/30
 * Time: 16:27
 */
class EffectInFansController extends AdminController
{
    public function actionIndex()
    {
        $page = $this->getFansInfo();
        $this->render('index', array('page' => $page));
    }

    public function actionExport(){
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="进粉表-' . date('Ymd', time()) . '.csv"');
        header('Cache-Control: max-age=0');
        //打开PHP文件句柄,php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'a');
        $start_time = $this->get('start_date');
        $end_time = $this->get('end_date');
        // 表头行
        $headlist = array();
        $headlist[] = '微信号ID';
        //获取时间段
        $date_num = PartnerCost::model()->getDateThTwo($start_time, $end_time);
        foreach ($date_num as $value) {
            $headlist[] = $value;
            $headlist[] = $value;
        }
        $headlist[] = '合计(粉)';
        $headlist[] = '合计(排)';
        //输出Excel列名信息
        foreach ($headlist as $key => $value) {
            //CSV的Excel支持GBK编码，一定要转换，否则乱码
            $headlist[$key] = iconv('utf-8', 'gbk', $value);
        }
        //将数据通过fputcsv写到文件句柄
        fputcsv($fp, $headlist);

        //计数器
        $num = 0;

        //每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 10000;

        //逐行取出数据，不浪费内存
        $data = $this->getFansInfo();
        // 顶部统计行
        $total_row = array();
        $total_row[] = '合计';
        foreach ($date_num as $key=>$value) {
            $total_row[] = isset($data['day_fans_count'][$key])? $data['day_fans_count'][$key]:"0";
            $total_row[] = isset($data['day_timetable_count'][$key])? $data['day_timetable_count'][$key]:"0";
        }
        $total_row[] = $data['total_fans'];
        $total_row[] = $data['total_count'];
        foreach ($total_row as $key => $value) {
            //CSV的Excel支持GBK编码，一定要转换，否则乱码
            $total_row[$key] = iconv('utf-8', 'gbk', $value);
        }
        //将数据通过fputcsv写到文件句柄
        fputcsv($fp, $total_row);
        // 进粉表数据
        $export_date = $data['info'];
        foreach ($export_date as $key=>$value) {
            $num++;

            //刷新一下输出buffer，防止由于数据过多造成问题
            if ($limit == $num) {
                ob_flush();
                flush();
                $num = 0;
            }
            $row = array();
            $row[] = $value[0];
            for ($i=1; $i<=count($date_num); $i++) {
                $row[] = $value[$i]['fans_count'];
                $row[] = $value[$i]['timetable_count'];
            }
            $row[] = $value[$i];
            $row[] = $value[$i+1];
            foreach ($row as $k => $v) {
                $row[$k] = iconv('utf-8', 'gbk', $v);
            }
            fputcsv($fp, $row);
        }
    }


    /**
     * 优化进粉表性能
     * @return array
     * author: yjh
     */
    private function getFansInfo()
    {
        $data = array();
        $params = "1";
        $where = '';
        date_default_timezone_set('PRC');
        $week = date('w');
        $start_time = $this->get('start_date')? $this->get('start_date'):date('Y-m-d',strtotime('+'. 1-$week .' days'));
        $end_time = $this->get('end_date')? $this->get('end_date'):date('Y-m-d',strtotime('+'. 7-$week .' days'));
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
            $params .= " and a.wechat_id like '%" . $this->get('wechat_id') . "%'";
            $where .= " and wechat_id  like '%" . $this->get('wechat_id') . "%'";
        }
        //获取时间段
        $date_num = PartnerCost::model()->getDateInfoTwo($start_time, $end_time);
        $condition = $params . " and addfan_date between  " . end($date_num) . " and $date_num[0]  group by weixin_id,addfan_date order by weixin_id desc,addfan_date desc";
        $fansCountInfo = FansInputManage::model()->getFansTypeTwo($condition);
        $key_arr1 = $key_arr2 = array();
        if ($fansCountInfo) {
            foreach ($fansCountInfo as $value) {
                $key_arr1[] = '"' . $value['weixin_id'] . "_" . $value['addfan_date'] . '"';
            }
            $fansCountInfo = array_combine($key_arr1, $fansCountInfo);
        }
        $wechatArr_1 = array_column($fansCountInfo, 'weixin_id');
        $weChatList = WeChat::model()->getWechatList($where . " order by id desc");
        $wechatArr_2 = array_column($weChatList, 'id');
        $weChatList = array_combine($wechatArr_2, $weChatList);
        if (!empty($where)) {
            $wechatArr = array_unique(array_merge($wechatArr_2, $wechatArr_1));
        } else $wechatArr = $wechatArr_2;
        // 获取排期表数据
        $select = 'wid,time,count,status';
        $where_t = ' time between '.strtotime($start_time).' and '.strtotime($end_time);
        $timetable_date = Timetable::model()->getTimetableDate($select,$where_t);
        $timetable_wechat = array();
        foreach ($timetable_date as $value) {
            $timetable_wechat[intval($value['wid'])][$value['time']] = array(
                'count'=>$value['count'],
                'status'=>$value['status']
            );
        }
        $tempData = array();
        foreach ($wechatArr as $value) {
            $id = $value;
            $tempData[$id][] = array_key_exists($id, $weChatList) ? $weChatList[$id]['wechat_id'] : WeChat::model()->findByPk($id)->wechat_id;
            $total = $timetable_total = 0;
            foreach ($date_num as $v) {
                $key = '"' . $value . "_" . $v . '"';
                $timetable_count = '--';
                if (array_key_exists($id, $timetable_wechat)) {
                    if( $timetable_wechat[$id][$v]['status'] != 0) {
                        $timetable_count = vars::get_field_str('timetable_status', $timetable_wechat[$id][$v]['status']);
                    } elseif ( $timetable_wechat[$id][$v]['count'] >= 0 ) {
                        $timetable_count = $timetable_wechat[$id][$v]['count'];
                        $timetable_total += $timetable_wechat[$id][$v]['count'];
                    }
                }
                $fans_count = array_key_exists($key, $fansCountInfo) ? $fansCountInfo[$key]['fans_count'] : 0;
                $total += $fans_count;
                $tempData[$id][] = array(
                    'fans_count'=>$fans_count,
                    'timetable_count'=>$timetable_count,
                );
            }
            $tempData[$id][] = $total;
            $tempData[$id][] = $timetable_total;
        }
        $day_fans = array();
        $day_count = array();
        if (!empty($wechatArr)) {
            // 进粉表数量日期统计
            $where = ' (addfan_date between '.strtotime($start_time).' and '.strtotime($end_time).') and (weixin_id in ('.implode(',',$wechatArr).'))';
            $order = ' order by addfan_date desc';
            $day_fans_input = FansInputManage::model()->getDayTotalFans($where,$order);
            foreach ($day_fans_input as $value) {
                $day_fans[$value['addfan_date']] = $value['day_fans'];
            }
            // 排期表数量日期统计
            $where = ' and (time between '.strtotime($start_time).' and '.strtotime($end_time).') and (wid in ('.implode(',',$wechatArr).'))';
            $order = ' order by time desc';
            $day_timetable_count = Timetable::model()->getDayCountTotal($where,'',$order);
            foreach ($day_timetable_count as $value) {
                $day_count[$value['time']] = $value['day_count'];
            }
        }
        $data['info'] = $tempData;
        $data['start_time'] = $start_time;
        $data['end_time'] = $end_time;
        $data['day_fans_count'] = $day_fans;
        $data['day_timetable_count'] = $day_count;
        $data['total_fans'] = array_sum($day_fans);
        $data['total_count'] = array_sum($day_count);
        return $data;
    }

    private function date_range($s_date,$e_date){
        $start_m = date('m',strtotime($s_date));
        $end_m = date('m',strtotime($e_date));
        $max_d=10;
        if ( $start_m && $end_m ){
            $total_d=(strtotime($s_date)-strtotime($e_date))/(24*60*60)+1;
            $total_show=$total_d>$max_d?$max_d:$total_d;
        }else{
            $total_show = $max_d;
        }
        $date_s = array();
        for ( $i=0; $i<$total_show; $i++ ){
            if($end_m) {
                $date_s[] = $end_m - (24 * 60 * 60) * $i;
            }

        }

        $date_range=array();
        $max_d=7;
        if($s_date&&$e_date){
            $total_d=(strtotime($s_date)-strtotime($e_date))/(24*60*60)+1;
            $total_d=$total_d>$max_d?$max_d:$total_d;
            $date_range['s_date']=strtotime($s_date);
            $date_range['e_date']= $date_range['s_date']+(24*60*60)*$total_d;
        }elseif ($s_date){
            $date_range['s_date']=strtotime($s_date);
            $date_range['e_date']= $date_range['s_date']+(24*60*60)*$max_d;
        }elseif ($e_date){
            $date_range['e_date']=strtotime($e_date);
            $date_range['s_date']= $date_range['e_date']-(24*60*60)*$max_d;
        }else{
            $date_range['e_date']=strtotime(date('Y-m-d',time()));
            $date_range['s_date']= $date_range['e_date']-(24*60*60)*$max_d;
        }
        return $date_range;
    }
}
