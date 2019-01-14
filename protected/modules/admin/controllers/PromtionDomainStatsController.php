<?php
/**
 * 推广人员域名统计
 * User: hlc
 * Date: 2018/12/29
 * Time: 9:58
 */
class PromtionDomainStatsController extends AdminController{
    public function actionIndex(){
        //搜索
        $temp = array();
        $params = '';

        if ($this->get('status')!= '') {
            $params .= " and(a.status  =" . $this->get('status') . ") ";
        }
        if ($this->get('type')!= '') {
            $params .= " and(a.application_type =" . $this->get('type') . ") ";
        }
        if ($this->get('is_https')!= '') {
            $params .= " and(a.is_https like '%" . trim($this->get('is_https')) . "%') ";
        }
        $sql = 'select a.*,b.name from domain_list as a
                left join promotion_staff_manage as b on b.user_id=a.uid
                where a.uid !=0 '.$params.' order by uid';
        $table_list = Yii::app()->db->createCommand($sql)->queryAll();

        //域名类型；0、推广域名，1、跳转域名，2、白域名
        foreach ($table_list as $value){
            $key= $value['uid'];
            $temp[$key]['name'] = $value['name'];
            if($value['domain_type'] == 0){
                $temp[$key]['promotion_domain_num'] +=1;
            }else{
                $temp[$key]['promotion_domain_num'] += 0;
            }
            if($value['domain_type'] == 1){
                $temp[$key]['jump_domain_num'] +=1;
            }else{
                $temp[$key]['jump_domain_num'] += 0;
            }
            if($value['domain_type'] == 3){
                $temp[$key]['short_domain_num'] +=1;
            }else{
                $temp[$key]['short_domain_num'] += 0;
            }
        }

        $data= helper::getPage($this->get('p'),20,$temp);

        $this->render('index',array('data'=>$data));
    }
}