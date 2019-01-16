<?php
/**
 * 微信号打卡控制器
 * User: fang
 * Date: 2016/12/30
 * Time: 16:27
 */
class WechatCardController extends AdminController{
    public function actionIndex(){
        //搜索
        $params['where'] = '';
        $start_date = $this->get('start_date')?strtotime($this->get('start_date')):strtotime(date('Ymd',strtotime("-9 day")));
        $end_date = $this->get('end_date')?strtotime($this->get('end_date')):strtotime(date('Ymd'));;
        //友盟数据
        $page['data'] = $this->getTimeData($start_date,$end_date);
        /*echo "<pre>";
        var_dump($page['data']);
        echo "</pre>";*/
        //客服部
        if ($this->get('csid') != '') {
            $params['where'] .= " and(customer_service_id = " . $this->get('csid') . ") ";
        }
        $params['order'] = "  order by id desc      ";
        //$params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(WeChat::model()->tableName())->listdata($params);
        $this->render('index', array('page' => $page));
    }

    /**
     * 获取对应时间段内的数据
     */
    public function getTimeData($start,$end){
        if($this->get('csid')) $condition = " AND customer_service_id={$this->get('csid')}";
        $sql="select * from stat_cost_detail WHERE business_type=1 and stat_date BETWEEN $start AND $end ".$condition;
        $data['cnzz']=Yii::app()->db->createCommand($sql)->queryAll();
        return $data;
    }
}
