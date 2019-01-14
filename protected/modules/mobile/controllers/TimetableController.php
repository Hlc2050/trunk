<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/7
 * Time: 10:17
 */

class TimetableController extends AdminController
{
    public function actionIndex()
    {
        $page = $this->getWechatTimetable();
        $this->render('index', array( 'page' => $page));
        exit();
    }



    //获取排期表
    public function getWechatTimetable($type = 0)
    {
        date_default_timezone_set('PRC');
        $week = date('w');
        $start_time = !$this->get('start_date') ? strtotime(date('Y-m-d', strtotime('+' . 1 - $week . ' days'))) : strtotime($this->get('start_date'));
        $end_time = !$this->get('end_date') ? strtotime(date('Y-m-d', strtotime('+' . 7 - $week . ' days'))) : strtotime($this->get('end_date'));
        $params['where'] = 'and (a.time between ' . $start_time . ' and ' . $end_time . ')';
        $promotion_group = $this->getUserPromotionGroup('add');
        $group_ids = array_column($promotion_group, 'linkage_id');

        $params['where'] .= 'and (p.promotion_group_id in ( ' . implode(',', $group_ids) . '))';
        if ($this->get('wechat_id')) {
            $wechat_str = str_replace('，', ',', $this->get('wechat_id'));
            if (stripos($wechat_str, ',') !== false) {
                $wechat_arr = explode(',', $wechat_str);
                foreach ($wechat_arr as $key => $value) {
                    $wechat_arr[$key] = '\'' . $value . '\'';
                }
                $wechat_str = implode(',', $wechat_arr);
                $params['where'] .= ' and (w.wechat_id in (' . $wechat_str . '))';
            } else $params['where'] .= 'and (w.wechat_id like \'%' . $this->get('wechat_id') . '%\')';
        }
        if ($this->get('status') || $this->get('status') === '0') $params['where'] .= 'and (w.timetable_status = ' . $this->get('status') . ')';
        if ($this->get('tg_id')) $params['where'] .= 'and (w.promotion_staff_id = ' . $this->get('tg_id') . ')';
        if ($this->get('csid')) $params['where'] .= 'and (c.id = ' . $this->get('csid') . ')';
        if ($this->get('goods_id')) $params['where'] .= 'and (g.id = ' . $this->get('goods_id') . ')';
        if ($this->get('pgid')) $params['where'] .= 'and (p.promotion_group_id = ' . $this->get('pgid') . ')';
        // 获取排期表微信号 start
        $params['group'] = 'group by a.wid';
        $params['join'] = '
                left join wechat as w on a.wid=w.id
                left join customer_service_manage as c on w.customer_service_id=c.id
                left join goods as g on w.goods_id=g.id
                left join promotion_staff_manage as p on w.promotion_staff_id=p.user_id';
        $params['order'] = "  order by a.upd_time desc,a.id asc ";
        $params['pagesize'] = 20;
        $params['pagebar'] = 1;
        $params['page_type'] = 1;
        $params['select'] = "a.wid,w.id,w.wechat_id,w.timetable_status,g.goods_name,c.cname,p.name as ps_name";
        $page['listdata'] = Dtable::model(Timetable::model()->tableName())->listdata($params);
        //获取排期表微信号 end
        $wechat_ids = array();
        //排期表微信号数组
        $wechat_timetable = array();
        foreach ($page['listdata']['list'] as $value) {
            $wechat_ids[] = $value['wid'];
            $wechat_timetable[$value['wid']]['id'] = $value['id'];
            $wechat_timetable[$value['wid']]['timetable_status'] = vars::get_field_str('timetable_status', $value['timetable_status']);//状态;
            $wechat_timetable[$value['wid']]['wechat_id'] = $value['wechat_id'];
            $wechat_timetable[$value['wid']]['cname'] = $value['cname'];
            $wechat_timetable[$value['wid']]['ps_name'] = $value['ps_name'];
            $wechat_timetable[$value['wid']]['goods_name'] = $value['goods_name'];
            $wechat_timetable[$value['wid']]['count_total'] = 0;
        }
        if (!empty($wechat_ids)) {
            $sql = 'select time.*,t.name as type_name,t.type_id from timetable as time
                left join user_timetable_type as ut on time.user_type_id=ut.id
                left join timetable_type as t on ut.type_id=t.type_id
                where time.wid in (' . implode(',', $wechat_ids) . ') and (time.time between ' . $start_time . ' and ' . $end_time . ')
                ';
            $table_list = Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($table_list as $key => $val) {
                $wechat_timetable[$val['wid']]['type_name'] = $val['type_name'];
                $wechat_timetable[$val['wid']]['type_id'] = $val['type_id'];
                $week_day = ($val['time'] - $start_time) / 86400 + 1;// 求记录排期时间为周几
                $wechat_timetable[$val['wid']]['table_list'][$week_day] = array(
                    'date' => date('m-d', $val['time']),
                    'count' => $val['status'] == 0 ? $val['count'] : vars::get_field_str('timetable_status', $val['status']),
                );
                if ($val['status'] == 0 && $val['count'] > 0) {
                    $total_count = $wechat_timetable[$val['wid']]['count_total'] + intval($val['count']);
                    $wechat_timetable[$val['wid']]['count_total'] = $total_count;
                }
            }
        }
        // 按日期统计总排期
        $where = $params['where'];
        $join = $params['join'];
        $day_count = Timetable::model()->getDayCountTotal($where, $join);
        $day_date = array_column($day_count, 'time');
        $day_value = array_column($day_count, 'day_count');
        $day_total = array_combine($day_date, $day_value);
        $page['start_time'] = date('Y-m-d', $start_time);
        $page['end_time'] = date('Y-m-d', $end_time);
        $day_num = PartnerCost::model()->getDateInfoTwo($page['start_time'], $page['end_time']);
        foreach ($day_num as $value) {
            $day_total[$value] = isset($day_total[$value]) ? $day_total[$value] : 0;
        }
        $page['wechat_timetable'] = $wechat_timetable;
        $page['day_count'] = $day_total;
        $page['count_total'] = array_sum($day_total);
        $page['listdata']['url'] = urlencode('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"]);
        return $page;
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
            echo CHtml::tag('option', array('value' => ''), '全部', true);
            foreach ($data as $key => $val) {
                echo CHtml::tag('option', array('value' => $val['user_id']), CHtml::encode($val['user_name']), true);
            }
        }
    }
    /**
     * 根据用户权限获取推广组数据
     * @param  $auth_type string 需判断的权限类型 'add'创建,'edit'编辑
     */
    public function getUserPromotionGroup($auth_type)
    {
        $super_auth = 0;
        $com_auth = 0;
        if ($auth_type == 'add') {
            // 用户编辑权限
            $super_auth = $this->check_u_menu(array('auth_tag' => 'timetable_allAdd', 'echo' => 0));
            $com_auth = $this->check_u_menu(array('auth_tag' => 'timetable_comAdd', 'echo' => 0));
        } elseif ($auth_type == 'edit') {
            // 用户编辑权限
            $super_auth = $this->check_u_menu(array('auth_tag' => 'timetable_editList', 'echo' => 0));
            $com_auth = $this->check_u_menu(array('auth_tag' => 'timetable_comEdit', 'echo' => 0));
        }
        $promotionGrouplist = array();
        if (!$super_auth && $com_auth) {
            // 查询登录用户所属推广组
            $promotion = PromotionStaff::model()->find('user_id = ' . Yii::app()->admin_user->uid);
            $promotion_group_id = $promotion ? $promotion['promotion_group_id'] : 0;
            $promotionGroup = Linkage::model()->find('linkage_id = ' . $promotion_group_id);
            if ($promotionGroup) $promotionGrouplist[$promotionGroup['linkage_id']] = array(
                'linkage_name' => $promotionGroup['linkage_name'],
                'linkage_id' => $promotionGroup['linkage_id']
            );
            else $promotionGrouplist[0] = array(
                'linkage_name' => '无',
                'linkage_id' => 0,
            );
        } else {
            $promotionGrouplist = Linkage::model()->getPromotionGroupList();
        }
        return $promotionGrouplist;
    }

}