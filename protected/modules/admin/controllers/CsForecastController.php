<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/28
 * Time: 11:26
 */
class CsForecastController extends AdminController{

    public function actionIndex(){
        $authority = AdminUser::model()->getUserAu();
        if($authority == 0){
            if($this->get('tg') && $this->get('tg_id') && $this->get('promotion_group')) {
               $data = $this->getPersonData();
            }else if($this->get('tg') && $this->get('tg_id') == '' && $this->get('promotion_group')){
               $data = $this->getPersonData();
           }else{
                $data = $this->getGroupData();
            }
        }elseif ($authority == 1){
            if($this->get('tg_id')) {
                $data = $this->getPersonData();
            } else{
                $data = $this->getGroupData();
            }
        }elseif ($authority == 2){
            $data = $this->getPersonData();
        }

        $this->render('index',array('data'=>$data));
    }

    public function getGroupData(){

        $page = array();
        $params['where'] = '';
        $user_id = Yii::app()->admin_user->uid;
        $authority = AdminUser::model()->getUserAu();

        $params['where'] .= ' and a.status = 1';
        if($authority == 0){
            if($this->get('promotion_group')){
                $params['where'] .= ' and a.group_id='.$this->get('promotion_group');
            }
        }elseif ($authority == 1){
            $group_ids = AdminUser::model()->get_manager_group($user_id);
            if ($group_ids) {
                $params['where'] .= ' and a.group_id  in ('.implode(',',$group_ids).')';
            }
        }
        $date =$this->get('end_date')?$this->get('end_date'):date('Y-m-d',strtotime('now'));
        $start_date = strtotime($date);
        $before_thirty_day = $start_date-86400*30;
        $params['where'] .= ' and ( a.date between '.$before_thirty_day .' and '.$start_date .')';


        $params['group'] = " group by service_group,date";
        $params['order'] = "  order by a.date";
        $params['pagebar'] = 1;
        $params['join'] = "
        left join customer_service_manage as b on b.id=a.service_group
		";

        $params['select'] = " sum(a.fans_count) as fans_counts , sum(a.output) as outputs,sum(a.weChat_num) as weChat_num,b.cname,a.service_group,a.date";
        $sql = " select ".$params['select']." from plan_week_group_detail as a ".$params['join']." where 1 ".$params['where'].$params['group']. $params['order'];
        $page['listdata']['list'] = Yii::app()->db->createCommand($sql)->queryAll();
        $page['listdata']['url'] = urlencode('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"]);
        foreach ($page['listdata']['list'] as $val){
            $temp['list'][$val['service_group']]['name'] = $val['cname'];
            $temp['list'][$val['service_group']][$val['date']] = array('fans_counts'=>$val['fans_counts'],'outputs'=>$val['outputs'],'id'=>$val['id'],'weChat_num'=>$val['weChat_num']);
        }
        $temp['time'] = $start_date;

        return $temp;
    }

    public function getPersonData()
    {
        $data = array();
        $params['where'] = '';

        $params['where'] .= ' and a.status = 12';
        $authority = AdminUser::model()->getUserAu();
        if($authority == 0){
            if($this->get('tg') && $this->get('tg_id')) {
                $params['where'] .= ' and tg_uid='.$this->get('tg_id');
            }else if($this->get('tg') && $this->get('tg_id') == ''){
                $tg_id = AdminUser::model()->find('csname_true like"' . '%' . $this->get('tg') . '%' . '"');
                if($tg_id){
                    $params['where'] .=  " and a.tg_uid =" . $tg_id['csno'];
                }else{
                    $this->msg(array('state' => 0, 'msgwords' => '推广人员不存在'));
                }
            }
        }elseif ($authority == 1){
            $user_id = $this->get('tg_id');
            if($user_id){
                $params['where'] .= ' and a.tg_uid in ('.$user_id.')';
            }else{
                $tg_ids = AdminUser::model()->getPersonCheckData();
                $str = '';
                foreach ($tg_ids as $val){
                    $str .= $val.',';
                }
                $str = rtrim($str,',');
                $params['where'] .= ' and a.tg_uid in ('.$str.')';
            }
        }elseif ($authority == 2){
            $user_id = Yii::app()->admin_user->uid;
            $params['where'] .= ' and a.tg_uid ='.$user_id;
        }
        $end_date =strtotime($this->get('end_date'));
        $date =$end_date?$end_date:strtotime('now');
        $before_thirty_day = $end_date-86400*30;

        $params['where'] .= ' and a.date between '.$before_thirty_day.' and '.$date ;
        $params['group'] = " group by service_group,date";
        $params['order'] = "  order by a.date";
        $params['pagebar'] = 1;
        $params['join'] = "
        left join customer_service_manage as b on b.id=a.service_group
		";
        $params['select'] = " sum(a.fans_count) as fans_counts , sum(a.output) as outputs,sum(a.weChat_num) as weChat_num,a.service_group,a.date,b.cname";
        $sql = " select ".$params['select']." from plan_week_user_detail as a ".$params['join']." where 1 ".$params['where'].$params['group']. $params['order'];
        $data['listdata']['list'] = Yii::app()->db->createCommand($sql)->queryAll();
        $data['listdata']['url'] = urlencode('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"]);
        if($date){
            foreach ($data['listdata']['list'] as $val){
                $arr['list'][$val['service_group']]['name'] = $val['cname'];
                $arr['list'][$val['service_group']][$val['date']] = array('fans_counts'=>$val['fans_counts'],'outputs'=>$val['outputs'],'id'=>$val['id'],'weChat_num'=>$val['weChat_num']);
            }
        }
        $arr['time'] = $date;
        $arr['tg'] = $this->get('tg');

        return $arr;
    }
}