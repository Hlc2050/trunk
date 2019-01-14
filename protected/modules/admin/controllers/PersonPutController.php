<?php
/**
 * 人员投放控制器
 * User: fang
 * Date: 2016/12/30
 * Time: 9:11
 */
class PersonPutController extends AdminController{
    public function actionIndex(){
        $_GET['id'] = isset($_GET['id']) ? $_GET['id'] : '';
        //搜索
        $params['where'] = '';
        //计费方式
        if ($this->get('chgId') != '') {
            $params['where'] .= " and(charging_type = " . $this->get('chgId') . ") ";
        }
        //业务类型
        if ($this->get('business_type') != '') {
            $params['where'] .= " and(business_type = " . $this->get('business_type') . ") ";
        }
        $params['group']=" group by tg_uid  ";
        $params['order'] = "  order by id asc      ";
        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['join'] = "
		left join promotion_staff_manage as g on g.user_id=a.tg_uid
		";
        $params['select'] = "a.*,g.name";
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        //$params['debug']=1;
        $page['listdata'] = Dtable::model(StatCostDetail::model()->tableName())->listdata($params);
        $this->render('index', array('page' => $page));
    }
}
