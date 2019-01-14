<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/26
 * Time: 9:50
 */

class CsTimetableController extends AdminController
{
    public function actionIndex(){
        $this->render('index');
    }

    /**
     * 推广组数据
     * author: hlc
     */
   public function actionPromotionGroup()
    {
        $page = array();
        $params['where'] = '';
        $p = $this->get('group_id');
        $page['group_id'] = $p;
        $start_time = $this->get('start_date');
        $end_time = $this->get('end_date');

        if($start_time){
            $str_start = strtotime($start_time);
            $page['start_date'] = $str_start;
        }

        if($end_time){
            $str_end = strtotime($end_time);
            $page['end_date'] = $str_end;
        }

        if ($start_time && $end_time == ''){
            $now = date("Y-m-d",strtotime("now"));
            $end_date = strtotime("now");
            $page['end_date'] = $end_date;
            $page['date_difference']  = ($end_date-$str_start)/86400;
            $params['where'] .=  " and a.date between " . $str_start . " and " . $end_date;
        }elseif ($start_time && $end_time && ($str_end-$str_start)>0){
            $page['date_difference']  = ($str_end-$str_start)/86400;
            $params['where'] .=  " and a.date between " . $str_start . " and " . $str_end;
        }elseif ($start_time && $end_time && ($str_end-$str_start)==0){
            $page['date_difference'] = 0;
            $params['where'] .=  " and a.date = " . $str_start ;
        }elseif ($start_time && $end_time && ($str_end-$str_start)<0){
            $this->msg(array('state' => 0, 'msgwords' => '请选择正确的日期顺序'));
        }elseif ($start_time == '' && $end_time){
            $sql = 'SELECT min(date) as date FROM plan_week_group_detail';
            $min_time = Yii::app()->db->createCommand($sql)->queryAll();
            $str_start  =$min_time[0]['date'];
            $start_date = date("Y-m-d",$str_start);
            $page['start_date'] = $str_start;
            $page['date_difference']  = ($str_end-$str_start)/86400;
            $params['where'] .=  " and a.date between " . $str_start . " and " . $str_end;
        }

        if($this->get('promotion_group1') && $this->get('promotion_group2')){
            $params['where'] .=  " and a.group_id in (" . $this->get('promotion_group1').",".$this->get('promotion_group2').")" ;
        }
        if($start_time || $end_time){
        if($this->get('csid')){
            $params['where'] .=  " and a.service_group =" . $this->get('csid');
        }
            $params['where'] .=  " and a.status = 1" ;

            $params['group'] = " group by service_group,date,group_id";
            $params['order'] = "  order by a.date";
            $params['select'] = " sum(a.fans_count) as fans_counts , sum(a.output) as outputs,sum(a.weChat_num) as weChat_num,a.*";
            $sql = " select ".$params['select']." from plan_week_group_detail as a ".$params['join']." where 1 ".$params['where'].$params['group']. $params['order'];
            $page['listdata']['list'] = Yii::app()->db->createCommand($sql)->queryAll();
        }

       $arr = array();
       foreach ($page['listdata']['list'] as $value) {
           $arr['list'][$value['service_group']][$value['group_id']][$value['date']]= $value;
       }

       $data = $arr;

       $this->render('index',array('page'=>$page,'data'=>$data));
    }

     /**
     * 推广人员数据
     * author: hlc
     */
    public function actionPromotioner()
    {
        $page = array();
        $params['where'] = '';
        $p = $this->get('group_id');
        $page['group_id'] = $p;
        $start_time = $this->get('start_date');
        $end_time = $this->get('end_date');
        if($start_time){
            $str_start =  strtotime($start_time);
            $page['start_date'] = $str_start;
        }

        if($end_time){
            $str_end = strtotime($end_time);
            $page['end_date'] = $str_end;
        }

        if ($start_time && $end_time == ''){
            $now = date("Y-m-d",strtotime("now"));
            $end_date = strtotime("now");
            $page['end_date'] = $end_date;
            $page['date_difference']  = ($now-$str_start)/86400;
            $params['where'] .=  " and a.date between " . $str_start . " and " . $end_date;
        }elseif ($start_time && $end_time && ($str_end-$str_start)>0){
            $page['date_difference']  = ($str_end-$str_start)/86400;
            $params['where'] .=  " and a.date between " . $str_start . " and " . $str_end;
        }elseif ($start_time && $end_time && ($str_end-$str_start)==0){
            $page['date_difference']  = ($str_end-$str_start)/86400;
            $params['where'] .=  " and a.date = ".$str_start;
        }elseif ($start_time && $end_time && ($str_end-$str_start)<0){
            $this->msg(array('state' => 0, 'msgwords' => '请选择正确的日期顺序'));
        }elseif ($start_time == '' && $end_time){
            $params['where'] .=  " and a.date <= " .$str_end ;
        }

        $promotion_group = $this->get('promotion_group');
        if($promotion_group){
            $params['where'] .=  " and d.groupid in (" . $promotion_group.")" ;
        }

            $tgid = $this->get('tg_id');
            $tg=$this->get('tg');
            $page['tg'] = $tg;
            if($tgid && $tg){
                $tgids = AdminUserGroup::model()->getGroupId($tgid);
                if(in_array($promotion_group,$tgids) || $promotion_group == ''){
                    $params['where'] .=  " and a.tg_uid =" .$tgid;
                }
            }elseif($this->get('tg_id')=='' && $this->get('tg')){
                $tg_id = AdminUser::model()->find('csname_true like"' . '%' . $tg . '%' . '"');
                if($tg_id){
                    $tgids = AdminUserGroup::model()->getGroupId($tg_id['csno']);
                    if(in_array($promotion_group,$tgids) || $promotion_group == ''){
                        $params['where'] .=  " and a.tg_uid =" . $tg_id['csno'];
                    }
                }else{
                    $this->msg(array('state' => 0, 'msgwords' => '推广人员不存在'));
                }
            }

                $params['where'] .=  " and a.status = 12" ;

                $params['group'] = " group by service_group,tg_uid,date";
                $params['order'] = "  order by a.date";
                $params['join'] = "
                 left join customer_service_manage as b on b.id=a.service_group
                 left join cservice as c on c.csno=a.tg_uid
                 left join cservice_groups as d on d.sno=a.tg_uid
                 left join cservice_group as f on f.groupid=d.groupid
                ";
                $params['pagebar'] = 1;
                $params['select'] = " sum(a.fans_count) as fans_counts , sum(a.output) as outputs,sum(a.weChat_num) as weChat_num,a.*,b.cname,c.csname_true,f.groupname,d.groupid";

               if ($start_time || $end_time) {
                   $sql = " select ".$params['select']." from plan_week_user_detail as a ".$params['join']." where 1 ".$params['where'].$params['group']. $params['order'];
                   $page['listdata']['list'] = Yii::app()->db->createCommand($sql)->queryAll();
               }


        $arr = array();
        foreach ($page['listdata']['list'] as $value) {
            $arr['list'][$value['service_group']][$value['group_id']][$value['date']]= $value;
            $page['listdata']['all_weChat_num']+=$value['weChat_num'];
            $page['listdata']['all_fans_counts']+=$value['fans_counts'];
            $page['listdata']['all_outputs']+=$value['outputs'];
        }

        $data = $arr;

        $this->render('index',array('page'=>$page,'data'=>$data));
    }

    public function actionGetTgPeople()
    {
        //下单商品模糊查询
        if (isset($_GET['jsoncallback'])) {
            if($this->get('group_id')){
                $sql = "select b.sno,a.csname_true from cservice as a left join cservice_groups as b on a.csno=b.sno where b.groupid=".$this->get('group_id');
                $data = Yii::app()->db->createCommand($sql)->queryAll();
                $this->msg(array('state' => 1, 'data' => $data, 'type' => 'jsonp'));
            }
        }
    }
}