<?php
/**
 * 微信号投入控制器
 * User: fang
 * Date: 2016/12/23
 * Time: 14:06
 */
class WechatPutController extends AdminController{
    public function actionIndex(){
        $params['where'] = '';

        $_GET['id'] = isset($_GET['id']) ? $_GET['id'] : '';
        //搜索
        $start_date = $this->get('start_date')?strtotime($this->get('start_date')):strtotime(date('Ymd',strtotime("-9 day")));
        $end_date = $this->get('end_date')?strtotime($this->get('end_date')):strtotime(date('Ymd'));;

        //客服部
        if ($this->get('csid') != '') {
            $params['where'] .= " and(a.customer_service_id = '" . $this->get('csid') . "') ";
        }
        //计费方式
        if ($this->get('chgId') != '') {
            $params['where'] .= " and(a.charging_type = '" . $this->get('chgId') . "') ";
        }
        //业务类型
        if ($this->get('business_type') != '') {
            $params['where'] .= " and(a.business_type = '" . $this->get('business_type') . "') ";
        }
        //推广用户
        if ($this->get('user_id') != '') $params['where'] .= " and(a.promotion_staff_id = '" . $this->get('user_id') . "') ";

        $params['order'] = "  order by id desc      ";
        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['join'] = "
		left join promotion_staff_manage as g on g.user_id=a.promotion_staff_id
		";
        $params['select'] = "a.id,a.wechat_id,g.name";
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
    
        $allData = $this->getWechatPutData($start_date,$end_date);
        $page['listdata'] = Dtable::model('wechat')->listdata($params);
   
        $this->render('index', array('page' => $page,'allData'=>$allData));
    }

    public function getWechatPutData($start_date,$end_date){
        $temp_money = $temp_fixed =array();
        $condition = '';

        if($this->get('user_id')) $condition .= " and tg_uid=".intval($this->get('user_id'));
        if($this->get('csid')) $condition .= " and customer_service_id=".intval($this->get('csid'));
        if($this->get('business_type')) $condition .= " and business_type=".intval($this->get('business_type'));
        if($this->get('chgId')) $condition .= " and charging_type=".intval($this->get('chgId'));

        $sql = "select weixin_id,sum(money) as money,stat_date from stat_cost_detail where stat_date between $start_date and $end_date ".$condition." group by weixin_id,stat_date";
        $money =  Yii::app()->db->createCommand($sql)->queryAll();

        if ($money) {
            foreach ($money as $value) {
                $key = $value['stat_date'] . "_" . $value['weixin_id'];
                $temp_money[$key] = $value;
            }
        }
        $allData['money'] = $temp_money;
        $sql = "select weixin_id,sum(fixed_cost) as fixed_cost,stat_date from fixed_cost_new where stat_date between $start_date and $end_date".$condition." group by weixin_id,stat_date";
        $fixed =  Yii::app()->db->createCommand($sql)->queryAll();
        if ($fixed) {
            foreach ($fixed as $value) {
                $key = $value['stat_date'] . "_" . $value['weixin_id'];
                $temp_fixed[$key] = $value;
            }
        }
        $allData['fixed'] = $temp_fixed;

        return $allData;
    }
}