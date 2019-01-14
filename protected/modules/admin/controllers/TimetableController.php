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
        // 用户创建权限
        $add_auth = '';
        if ($this->check_u_menu(array('auth_tag' => 'timetable_comAdd', 'echo' => 0))) {
            $add_auth = 'timetable_comAdd';
        }
        if ($this->check_u_menu(array('auth_tag' => 'timetable_allAdd', 'echo' => 0))) {
            $add_auth = 'timetable_allAdd';
        };
        $edit_auth = $this->getUserEditAuth();
        $page = $this->getWechatTimetable();
        $this->render('index', array('add_auth' => $add_auth, 'edit_auth' => $edit_auth, 'page' => $page));
        exit();
    }

    //创建全部排期
    public function actionAllAdd()
    {
        date_default_timezone_set('PRC');
        if (!$_POST) {
            $types = TimetableType::model()->findAll(array('select' => 'type_id,name'));//排期类型
            $this->render('allAdd', array('types' => $types));
            exit();
        } else
            $this->createTimetable(0);
    }

    //创建普通排期
    public function actionComAdd()
    {
        date_default_timezone_set('PRC');
        if (!$_POST) {
            $week = date('w');
            $star_time = date('Y-m-d', strtotime('+' . 1 - $week . ' days'));
            $end_time = date('Y-m-d', strtotime('+' . 7 - $week . ' days'));
            $types = TimetableType::model()->findAll(array('select' => 'type_id,name'));//排期类型
            $this->render('comAdd', array('types' => $types, 'star_time' => $star_time, 'end_time' => $end_time));
            exit();
        } else
            $this->createTimetable(1);
    }

    // 批量编辑排期(超级)
    public function actionEditList()
    {
        date_default_timezone_set('PRC');
        if (!$_POST) {
            $formate_start_date = str_replace('.', '-', $this->get('start_time'));
            $formate_end_date = str_replace('.', '-', $this->get('end_time'));
            $start_time = strtotime($formate_start_date);
            $end_time = strtotime($formate_end_date);
            $timetable_list = $this->getEditList();
            $types = TimetableType::model()->findAll(array('select' => 'type_id,name'));//排期类型
            $datelist = $this->getEditDate($formate_start_date, $formate_end_date);
            $this->render('editList', array('timetable_list' => $timetable_list, 'start_time' => $start_time,
                'end_tome' => $end_time, 'types' => $types, 'datelist' => $datelist, 'action' => $this->createUrl('editList')));
            exit();
        } else {
            $this->editTimetable(0);
        }
    }

    // 批量编辑排期(普通)
    public function actionComEdit()
    {
        if (!$_POST) {
            $end_date = strtotime($this->get('end_time'));
            $time = strtotime(date('Y-m-d', time()));
            if ($time > $end_date) {
                $this->msg(array('state' => 0, 'msgwords' => '您没有权限编辑之前日期的排期数据！'));
                exit();
            }
            $start_date = str_replace('.', '-', $this->get('start_time'));
            $formate_end_date = str_replace('.', '-', $this->get('end_time'));
            $start_time = strtotime($start_date);
            $end_time = strtotime($formate_end_date);
            $today = strtotime(date('Y-m-d', time()));
            if ($today > $start_time) $start_date = date('Y-m-d', time());
            $datelist = $this->getEditDate($start_date, $formate_end_date);
            $timetable_list = $this->getEditList(1);
            $types = TimetableType::model()->findAll(array('select' => 'type_id,name'));//排期类型
            $this->render('editList', array('timetable_list' => $timetable_list, 'start_time' => $start_time,
                'end_tome' => $end_time, 'types' => $types, 'datelist' => $datelist, 'action' => $this->createUrl('comEdit')));
            exit();
        } else {
            $this->editTimetable(1);
        }
    }

    // 批量删除排期
    public function actionDelete()
    {
        date_default_timezone_set('PRC');
        $wechat_id = $this->get('wechat_id');
        $start_time = strtotime(str_replace('.', '-', $this->get('start_time')));
        $end_time = strtotime(str_replace('.', '-', $this->get('end_time')));
        $wechats = WeChat::model()->findAll('id in (' . $wechat_id . ') ');
        $timetable_count = Timetable::model()->findall(
            array(
                'select' => 'wid',
                'condition' => 'wid in (' . $wechat_id . ') and time>:end_time',
                'params' => array(':end_time' => $end_time),
                'group' => "wid"
            )
        );
        if (!empty($timetable_count)) {
            $has_time_list = array();
            foreach ($timetable_count as $value) {
                $wechat_info = WeChat::model()->findByPk($value['wid']);
                $has_time_list[] = $wechat_info['wechat_id'];
            }
            $this->msg(
                array(
                    'state' => 0,
                    'msgwords' => '微信号' . implode(',', $has_time_list) . '此排期之后有其他排期，请先删除之后的排期',
                    'jscode' => "<script>jQuery('body').on('click', '.aui_dialog', function() {window.location='" . $this->get('url') . "'});</script>"
                )
            );//错误返回
            exit();
        } else {
            UserTimetableType::model()->deleteAll("wid in (" . $wechat_id . ") and (start_time=:start_time and end_time=:end_time)", array(
                ':start_time' => $start_time, ':end_time' => $end_time
            ));
            $delete = Timetable::model()->deleteAll("wid in (" . $wechat_id . ") and (time between :start_time and :end_time)", array(
                ':start_time' => $start_time, ':end_time' => $end_time
            ));
            $wechat_id_list = array();
            foreach ($wechats as $value) {
                $wechat_id_list[] = $value['wechat_id'];
                $new_status = Timetable::model()->find(
                    array(
                        'select' => 'status',
                        'condition' => 'wid = ' . $value['id'] . ' and time < ' . $start_time,
                        'order' => 'time desc '
                    )
                );
                $status = isset($new_status['status']) ? $new_status['status'] : 0;
                $update_wechat = WeChat::model()->findByPk($value['id']);
                $update_wechat->timetable_status = $status;
                $update_wechat->save();
            }
            $logs = "删除排期:日期：" . date('Y/m/d', $start_time) . "-" . date('Y/m/d', $start_time + 6 * 86400) . ";微信号：" . implode(',', $wechat_id_list);
            $msgarr = array('state' => 1);
            if ($delete === false) {
                //错误返回
                $this->msg(array('state' => 0));
            } else {
                //新增和修改之后的动作
                $this->logs($logs);
                //成功跳转提示
                $this->msg($msgarr);
            }
        }
    }

    //获取搜索微信号
    public function actionGetWachat()
    {
        $where = "  status = 0";
        if ($this->post('csid')) { //客服部
            $where .= " and customer_service_id= " . $this->post('csid');
        }
        if ($this->post('goods_id')) {
            $where .= " and goods_id= " . $this->post('goods_id');
        }
        if ($this->post('pgid') != '' && !$this->post('tg_id')) { //推广组
            if ($this->post('pgid') === "0") $where .= " and ( promotion_staff_id = 0 )";
            else {
                $promotionStaffArr = PromotionStaff::model()->getPromotionStaffByPg($this->post('pgid'));
                if (!$promotionStaffArr) $where .= " and promotion_staff_id = 0 ";
                else {
                    $user_ids = array_column($promotionStaffArr, 'user_id', 'user_id');
                    $where .= " and promotion_staff_id in (" . implode(',', $user_ids) . ")";
                }
            }
        }
        if ($this->post('tg_id')) { //推广人员
            $where .= " and promotion_staff_id = " . $this->post('tg_id');
        }
        $wechat_list = WeChat::model()->findAll(
            array(
                'select' => 'id,wechat_id',
                'condition' => $where
            )
        );
        echo CJSON::encode($wechat_list);
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
                $wechat_arr = array_unique(explode("\r\n", $this->get('wechat_id')));
                $temp = '';
                $num = count($wechat_arr);
                for($i=0;$i<$num;$i++){
                    if( $i < ($num-1)){
                        $temp .='\''. $wechat_arr[$i]. '\''.",";
                    }else{
                        $temp .= '\''.$wechat_arr[$i]. '\'';
                    }
                }
                $params['where'] .= ' and (w.wechat_id in (' . $temp . '))';
            } else $params['where'] .= 'and (w.wechat_id like \'%' . $this->get('wechat_id') . '%\')';


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
        $params['pagesize'] = $type == 0 ? Yii::app()->params['management']['pagesize'] : 20000;
        $params['pagebar'] = 1;
        $params['select'] = "a.wid,w.id,w.wechat_id,w.timetable_status,g.goods_name,c.cname,p.name as ps_name";
        $params['smart_order'] = 1;
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

    //排期表导出
    public function actionExport()
    {
        $headerlist = array('微信号','微信号状态','推广人员','客服部','商品');
        $date_num = PartnerCost::model()->getDateThTwo($this->get('start_date'), $this->get('end_date'));
        $date_num = array_reverse($date_num, true);
        $headerlist = array_merge($headerlist,$date_num);
        array_push($headerlist,'合计');
        //读出数据
        $data = $this->getWechatTimetable(1);
        // 合计行
        $total_row = array('','','','','合计');
        foreach ($date_num as $k => $v) {
            $total_row[] = $data['day_count'][$k];
        }
        foreach ($total_row as $key=>$value) {
            $total_row[$key] = iconv('utf-8','gbk',$value);
        }
        array_push($total_row,$data['count_total']);
        // 数据列表
        $export_row = array();
        $export_row[0] = $total_row;
        foreach ($data['wechat_timetable'] as $key => $val) {
            $row = array($val['wechat_id'],$val['timetable_status'],$val['ps_name'],$val['cname'],$val['goods_name']);
            $i = 0;
            $count = array();
            foreach ($date_num as $value) {
                $count[] = (!isset($val['table_list'][$i + 1]) || $val['table_list'][$i + 1]['count'] < 0) ? '--' : $val['table_list'][$i + 1]['count'];
                $i++;
            }
            $row = array_merge($row,$count);
            array_push($row,$val['count_total']);
            foreach ($row as $k=>$v) {
                $row[$k] = iconv('utf-8','gbk',$v);
            }
            $export_row[] = $row;
        }
        helper::downloadCsv($headerlist,$export_row,'排期表-' . date("Ymj"));
    }

    /**
     *批量编辑时获取微信号列表
     * @param $auth_type 0：超级编辑权限 1：普通编辑权限
     */
    public function getEditList($auth_type = 0)
    {
        date_default_timezone_set('PRC');
        $wechat_id = $this->get('wechat_id');
        $start_time = strtotime(str_replace('.', '-', $this->get('start_time')));
        $end_time = strtotime(str_replace('.', '-', $this->get('end_time')));
        $sql = 'select time.*,time.id as time_id,time.status as time_status,ut.count as type_count,t.name as type_name,ut.type_id,w.*,g.goods_name,c.cname,p.name as ps_name from timetable as time
                left join wechat as w on time.wid=w.id
                left join customer_service_manage as c on w.customer_service_id=c.id
                left join goods as g on w.goods_id=g.id
                left join promotion_staff_manage as p on w.promotion_staff_id=p.user_id
                left join user_timetable_type as ut on time.user_type_id=ut.id
                left join timetable_type as t on ut.type_id=t.type_id
                where time.wid in (' . $wechat_id . ') and (time.time between ' . $start_time . ' and ' . $end_time . ') order by time.time desc
                ';
        $table_list = Yii::app()->db->createCommand($sql)->query();
        $timetable_list = [];
        $wechat_user = [];
        $edit_auth = $this->getUserEditAuth();
        $today = strtotime(date('Y-m-d', time()));
        foreach ($table_list as $key => $val) {
            if (!in_array($val['wid'], $wechat_user)) {
                $wechat_user[] = $val['wid'];
                $timetable_list[$val['wid']]['wid'] = $val['id'];
                $timetable_list[$val['wid']]['time_id'] = $val['id'];
                $timetable_list[$val['wid']]['wechat_id'] = $val['wechat_id'];
                $timetable_list[$val['wid']]['goods_name'] = $val['goods_name'];
                $timetable_list[$val['wid']]['cname'] = $val['cname'];
                $timetable_list[$val['wid']]['ps_name'] = $val['ps_name'];
                $timetable_list[$val['wid']]['type_name'] = $val['type_name'];
                $timetable_list[$val['wid']]['type_id'] = $val['type_id'];
                $timetable_list[$val['wid']]['type_count'] = $val['type_count'];
                $timetable_list[$val['wid']]['count_total'] = 0;
                $timetable_list[$val['wid']]['default_status'] = 0;
                if ($auth_type == 1) $timetable_list[$val['wid']]['default_time'] = $today;
                else $timetable_list[$val['wid']]['default_time'] = $start_time;
            }
            $week_day = ($val['time'] - $start_time) / 86400 + 1;//求记录排期时间为周几
            $editable = 1;
            if ($edit_auth == 'timetable_comEdit' && $val['time'] < $today) {
                $editable = 0;
            }
            $timetable_list[$val['wid']]['table_list'][$week_day] = array(
                'date' => date('m/d', $val['time']),
                'count_show' => $val['time_status'] == 0 ? $val['count'] : vars::get_field_str('timetable_status', $val['time_status']),
                'status' => $val['time_status'],
                'count' => $val['count'],
                'editable' => $editable
            );
            if ($val['time_status'] != 0) {
                $timetable_list[$val['wid']]['default_status'] = $val['time_status'];
                $timetable_list[$val['wid']]['default_time'] = $val['time'];
            } elseif ($val['count'] >= 0) {
                $timetable_list[$val['wid']]['count_total'] += $val['count'];
            }
        }
        return $timetable_list;
    }

    /**
     * 创建条件判断
     * @param $wechat_ids
     * @param $start_time
     * @param $end_time
     * @param $auth_type 创建权限类型 0：全部排期 1：普通排期
     */
    private function isAddAllowed($wechat_ids, $wechat_list, $start_time, $end_time, $auth_type = 0)
    {
        $user_timetable = array(); //已创建排期微信号
        $wxids = array_column($wechat_list, 'id');
        // 不存在或状态不为推广微信号
        $exist_wechat = array_column($wechat_list, 'wechat_id');
        $wx_str=implode(',',$exist_wechat);//这里的分隔符自己定para-1
        $wx_lowercase=strtolower($wx_str);
        $exit_lower = explode(',',$wx_lowercase);
        $unexit = array_diff($wechat_ids, $exit_lower);
        if (!empty($unexit)) {
            $this->msg(array('state' => 0, 'msgwords' => '创建失败,微信号' . implode(',', $unexit) . '不存在或不为推广状态'));//错误返回
            exit();
        }
        // 没有权限创建的微信号
        if ($auth_type == 1) {
            $sql = 'select w.wechat_id  from wechat as w
                    left join promotion_staff_manage as p on w.promotion_staff_id = p.user_id 
                    where  p.promotion_group_id = ( select promotion_group_id from promotion_staff_manage as up where up.user_id = ' . Yii::app()->admin_user->uid . ')
                    and w.id in (' . implode(',', $wxids) . ')';
            $auth_user = Yii::app()->db->createCommand($sql)->queryAll();
            $auth_user = array_column($auth_user, 'wechat_id');
            // 将查询结果微信号id转化成小写
            $auth_str=implode(',',$auth_user);
            $auth_lowercase=strtolower($auth_str);
            $exit_auth = explode(',',$auth_lowercase);
            $unAuth_user = array_diff($wechat_ids, $exit_auth);
            if (!empty($unAuth_user)) {
                $this->msg(array('state' => 0, 'msgwords' => '创建失败,您没有创建微信号' . implode(',', $unAuth_user) . '排期表的权限'));//错误返回
                exit();
            }
        }
        // 已创建排期微信号(创建周期开始日期在当前时间前)
        $today = strtotime(date('Y-m-d', time()));
        $id_wechatid = array_combine($wxids, $exist_wechat);
        $wechat_table = Yii::app()->db->createCommand('select DISTINCT(wid) from user_timetable_type where start_time < ' . $today . ' and ( start_time = ' . $start_time . ' ) and ( end_time =' . $end_time . ') and wid in (' . implode(',', $wxids) . ')')->queryAll();
        $has_table_wechat = array_column($wechat_table, 'wid');
        foreach ($has_table_wechat as $v) {
            $user_timetable[] = $id_wechatid[$v];
        }
        if (!empty($user_timetable)) {
            $this->msg(array('state' => 0, 'msgwords' => '创建失败,微信号' . implode(',', $user_timetable) . '在该时间段已创建排期'));//错误返回
            exit();
        }
    }

    /**
     * 根据用户权限获取推广组数据
     * @param  $auth_type 需判断的权限类型 'add'创建,'edit'编辑
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
            $promotionGrouplist[]['linkage_name'] = '全部';
            $promotionGrouplist2 = Linkage::model()->getPromotionGroupList();
            $promotionGrouplist = array_merge($promotionGrouplist, $promotionGrouplist2);
        }
        return $promotionGrouplist;
    }

    // 判断用户编辑权限
    public function getUserEditAuth()
    {
        $edit_auth = '';
        if ($this->check_u_menu(array('auth_tag' => 'timetable_comEdit', 'echo' => 0))) {
            $edit_auth = 'timetable_comEdit';
        }
        if ($this->check_u_menu(array('auth_tag' => 'timetable_editList', 'echo' => 0))) {
            $edit_auth = 'timetable_editList';
        };
        return $edit_auth;
    }

    /**
     *保存批量编辑的排期数据
     * @param  $auth_type 0:超级编辑权限 1:普通编辑权限
     * @return void
     */
    private function editTimetable($auth_type = 1)
    {
        $start_time = $this->post('start_time');
        //需要更新的排期表数据
        $timetable_date = array();
        //需要更新的用户排期类型表数据
        $type_date = array();
        //需要更新后期排期状态的数据
        $status_date = array();
        //需要更新微信号排期状态的数据
        $wechat_date = array();

        foreach ($_POST as $key => $value) {
            $date_type = explode('_', $key);
            if (count($date_type) == 3) {
                if ($date_type[0] == 'count') {
                    //排期微信号
                    $timetable_date[$date_type[1]][$date_type[2]]['wid'] = $date_type[1];
                    //排期时间
                    $timetable_date[$date_type[1]][$date_type[2]]['time'] = $start_time + ($date_type[2] - 1) * 86400;
                    //排期数值
                    $timetable_date[$date_type[1]][$date_type[2]]['count'] = $value;
                } elseif ($date_type[0] == 'status' && $date_type[1] != 'sel') {
                    //排期状态
                    $timetable_date[$date_type[1]][$date_type[2]]['status'] = $value;
                } elseif ($date_type[0] == 'type') {
                    $type_date[$date_type[2]]['type'] = $value;
                    $type_date[$date_type[2]]['wid'] = $date_type[2];
                    $wechat_date[] = $date_type[2];
                }
            }
            if (count($date_type) == 2) {
                if ($date_type[0] == 'count') {
                    $type_date[$date_type[1]]['count'] = $value;
                }
            }
        }

        foreach ($type_date as $value) {

            $user_timetable_type = UserTimetableType::model()->find('wid = ' . $value['wid'] . ' and start_time = ' . $start_time);
            if ($value['count'] != '') {
                $user_timetable_type->count = $value['count'];
                $user_timetable_type->type_id = $value['type'];
            }
            $user_timetable_type->save();
        }

        foreach ($timetable_date as $key => $value) {
            foreach ($value as $item) {
                $timetable = Timetable::model()->find('wid = ' . $item['wid'] . ' and time = ' . $item['time']);
                $timetable->status = $item['status'];
                $timetable->count = $item['count'];
                $timetable->upd_time = time();
                $timetable->save();
            }
        }
        $wechat_list = array();
        foreach ($wechat_date as $value) {
            $wechat = WeChat::model()->find('id =' . $value);
            $wechat_list[] = $wechat['wechat_id'];
        }

        // 修改微信号排期表状态
        $update_res = $this->changeWechatTimeStatus($wechat_date);
        $start_date = date('Y-m-d', $start_time);
        if ($auth_type == 1) {
            $today = strtotime(date('Y-m-d', time()));
            if ($today > $start_time) $start_date = date('Y-m-d', $today);
        }
        $end_date = date('Y-m-d', $start_time + 6 * 24 * 60 * 60);
        $logs = "修改排期表:日期：" . $start_date . "至" . $end_date . ";微信号：" . implode(',', $wechat_list);
        $default_url = $this->createUrl('timetable/index?start_date=' . $start_date . '&end_date=' . $end_date);
        $backurl = explode('p=', $this->post('backurl'));
        $msgarr = array('state' => 1, 'url' => $backurl ? $backurl[0] : $default_url);
        if ($update_res === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            //新增和修改之后的动作
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 创建排期时删除微信号已创建的排期
     * @param $wechat_ids array
     * @param $start_date int
     * @param $end_date int
     */
    private function delRepeatTimetable($wechat_ids, $start_date, $end_date)
    {
        //  获取在该周期内已创建排期的微信号
        if (!empty($wechat_ids)) {
            $del_timetable = Dtable::toArr(UserTimetableType::model()->findAll(
                array(
                    'select' => 'id,wid',
                    'condition' => 'start_time = ' . $start_date . ' and end_time = ' . $end_date . ' and wid in (' . implode(',', $wechat_ids) . ')'
                )
            ));
            UserTimetableType::model()->deleteAll('start_time = ' . $start_date . ' and end_time = ' . $end_date . ' and wid in (' . implode(',', $wechat_ids) . ')');
            $user_type_ids = array_column($del_timetable, 'id');
            if (!empty($user_type_ids)) {
                Timetable::model()->deleteAll(' user_type_id in (' . implode(',', $user_type_ids) . ')');
            }
        }
    }

    /**
     * 修改微信号状态
     * @param  $wids array 需要修改的微信号数组
     */
    private function changeWechatTimeStatus($wids)
    {
        // 修改微信号排期状态
        foreach ($wids as $value) {
            $status = Timetable::model()->find(
                array(
                    'select' => 'status',
                    'condition' => 'wid = ' . $value . ' and t.time = ( select MAX(ut.end_time) from user_timetable_type as ut  where ut.wid = ' . $value . ')'
                )
            );
            $wechat = WeChat::model()->findByPk($value);
            $wechat->timetable_status = $status['status'];
            $wechat->save();
        }
    }


    /**
     * 获取编辑状态日期列表
     * @param  $start_date
     * @param  $end_date
     * @return array
     */
    public function getEditDate($start_date, $end_date)
    {
        $dateList = array_reverse(PartnerCost::model()->getDateInfoTwo($start_date, $end_date));
        $week = array();
        foreach ($dateList as $value) {
            $day_week = date('w', $value);
            if ($day_week === "0") $week[6] = $value;
            else $week[$day_week - 1] = $value;
        }
        return $week;
    }

    /**
     *  插入排期数据
     *  @param  $auth_type int 0创建全部排期权限,1创建普通排期权限
     */
    public function createTimetable($auth_type = 0) {
        $wechat_ids = array_unique(explode(',', strtolower($this->post('wechat_select'))));
        $star_time = strtotime($this->post('start_date')); //开始时间
        $end_time = strtotime($this->post('end_date')); //结束时间
        $wechat_arr = array();
        foreach ($wechat_ids as $k => $v) {
            $wechat_arr[$k] = '\'' . $v . '\'';
        }
        $wechat_list = Dtable::toArr(WeChat::model()->findAll(array('select' => ' id,wechat_id ', 'condition' => 'status = 0 and wechat_id in (' . implode(',', $wechat_arr) . ')')));
        // 判断所选微信号是否可创建，不可创建提示原因
        $this->isAddAllowed($wechat_ids, $wechat_list, $star_time, $end_time,$auth_type);
        $wxids = array_column($wechat_list, 'id');
        // 删除已创建的排期
        $this->delRepeatTimetable($wxids, $star_time, $end_time);
        // 创建排期
        $insert_array = array();
        foreach ($wechat_list as $wechat) {
            $type = $this->post('type');//排期类型
            $count = $this->post('count');//排期数值
            //处理排期类型及数值 start
            $user_type = new UserTimetableType();
            $user_type->type_id = $type;
            $user_type->wid = $wechat['id'];
            $user_type->start_time = $star_time;
            $user_type->end_time = $end_time;
            $user_type->count = $count;
            $user_type->save();
            // 求排期日期及数值
            $date_count = Timetable::model()->createDayCount($this->post('start_date'), $this->post('end_date'), $count, $type, $auth_type);
            foreach ($date_count as $v) {
                $item = array($wechat['id'], $v['time'], $v['count'], 0, $user_type->id, time(), time());
                $insert_array[] = '(' . implode(',', $item) . ')';
            }
            //处理排期类型及数值 end
        }
        $sql = "INSERT INTO timetable (wid,time,count,status,user_type_id,add_time,upd_time) VALUES " . implode(',', $insert_array);
        $action = Yii::app()->db->createCommand($sql)->query();
        // 修改微信号排期表状态
        $this->changeWechatTimeStatus($wxids);
        $log_action = '创建排期表:';
        if ($auth_type == 1) {
            $log_action = '创建普通排期表:';
        }
        $logs = $log_action."日期：" . date('Y/m/d', $star_time) . "-" . date('Y/m/d', $end_time) . ";微信号：" . implode(',', array_column($wechat_list, 'wechat_id'));
        $msgarr = array('state' => 1, 'url' => $this->createUrl('timetable/index?start_date=' . date('Y-m-d', $star_time) . '&end_date=' . date('Y-m-d', $end_time)));
        if (!isset($action) || $action === false) {
            // 错误返回
            $this->msg(array('state' => 0));
        } else {
            //新增和修改之后的动作
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }
}