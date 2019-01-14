<?php
class SendMessage {

    /**
     * @param $user_id
     * @param $start_date
     * @param $week_id
     * @param $name
     * @param $plan_type int 1个人周 2推广组周 3个人月 4 推广组月
     * @param $msg_type
     * @param string $add_msg
     */
    public function sendPlanMsg($user_id,$start_date,$week_id,$name,$plan_type,$msg_type,$add_msg='')
    {
        if (!$user_id) {
            return false;
        }
        $dateMonthWeek = helper::getDateMonthWeek($start_date);
        $time_type = '周计划';
        if ($plan_type ==1 || $plan_type == 2) {
            $time = $dateMonthWeek['year'].'年'.$dateMonthWeek['month'].'月第'.$dateMonthWeek['week'].'周-排期计划';
        } else {
            $time_type = '月计划';
            $time = $dateMonthWeek['year'].'年'.$dateMonthWeek['month'].'月-排期计划';
        }
        $where = ' 1 ';
        if (is_array($user_id)) {
            $where .= ' and system_user in ('.implode(',',$user_id).')';
        }else {
            $where .= ' and system_user='.$user_id;
        }
        $open = Dtable::toArr(OpenidManage::model()->findAll($where));
        $open_ids = array();
        foreach ($open as $value) {
            $open_ids[] = array('opend_id'=>$value['openid'],'user_id'=>$value['system_user']);
        }
        $msg = $name.'-'.$time.$add_msg;
        $server = Yii::app()->params['remind']['msg_detail_url'];
        $url = array();
        switch ($msg_type) {
            //待审批提醒
            case 1:
                $url = $server.'/mobile/planAudit/index';
                break;
            //个人计划审核结果提醒
            case 2:
                foreach ($user_id as $value) {
                    $url= '';
                }
                break;
            //推广组计划审核结果提醒
            case 3:
                foreach ($user_id as $value) {
                    $url = '';
                }
                break;
        }
        $type = '';
        if ($plan_type ==1) {
            $type='个人';
        }
        if ($plan_type ==2) {
            $type='推广组';
        }
        foreach ($open_ids as $open) {
            helper::sendEvent($open['opend_id'],$type.$time_type.'消息提醒',$msg,date('Y-m-d H:i:s', time()),$url);
        }
    }


    /**
     * @param $msg_title string
     * @param $user_msg array
     */

    public function sendDataMsg($msg_title,$user_msg)
    {
        $user_id = array_unique(array_column($user_msg,'uid'));
        if (!$user_id) {
            exit();
        }
        $server = Yii::app()->params['remind']['msg_detail_url'];
        $type_url = array(
            1=>$server.'/mobile/dataMsg/serviceData',
            2=>$server.'/mobile/dataMsgGroup/index',
            3=>$server.'/mobile/dataMsgUser/index',
        );
        $where = ' 1 ';
        if (is_array($user_id)) {
            $where .= ' and system_user in ('.implode(',',$user_id).')';
        }else {
            $where .= ' and system_user='.$user_id;
        }
        $open = Dtable::toArr(OpenidManage::model()->findAll($where));
        $open_ids = array();
        foreach ($open as $value) {
            $open_ids[$value['system_user']][] = $value['openid'];
        }
        foreach ($user_msg as $msg) {
            $uid = $msg['uid'];
            if ($open_ids[$uid]) {
                foreach ($open_ids[$uid] as $open) {
                    helper::sendEvent($open,'客服部数据监测',$msg_title.chr(10).$msg['msg'],date('Y-m-d H:i:s', time()),$type_url[$msg['type']]);
                }
            }
        }

    }
}