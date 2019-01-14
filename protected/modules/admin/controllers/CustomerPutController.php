<?php
/**
 * 客服投放控制器
 * User: fang
 * Date: 2017/1/3
 * Time: 18:06
 */

class CustomerPutController extends AdminController{
    public function actionIndex(){
        $_GET['id'] = isset($_GET['id']) ? $_GET['id'] : '';
        //搜索
        $params['where'] = '';
        //计费方式
        if ($this->get('chgId') != '') {
            $params['where'] .= " and(charging_type = " . intval($this->get('chgId')) . ") ";
        }
        //业务类型
        if ($this->get('business_type') != '') {
            $params['where'] .= " and(business_type = " . intval($this->get('business_type')) . ") ";
        }
        //商品
        if ($this->get('goods_id') != '') {
            $params['where'] .= " and(goods_id = " . intval($this->get('goods_id')) . ") ";
            $sql = "select cs_id from customer_service_relation where goods_id=".intval($this->get('goods_id'));
            $info = Yii::app()->db->createCommand($sql)->queryAll();
            $keyArr=array_column($info, 'cs_id');
            if(empty($keyArr)){
                $params['where'] =" and(e.id =0) ";
            }else{
                $keyStr = implode(',',array_unique($keyArr) );
                $params['where'] =" and(e.id in ($keyStr)) ";

            }
        }

        $params['group']=" group by customer_service_id  ";
        $params['order'] = "  order by id desc      ";
        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['join'] = "
		left join customer_service_manage as e on e.id=a.customer_service_id
		";
        $params['select'] = "a.*,e.cname";
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        //$params['debug']=1;
        $page['listdata'] = Dtable::model(StatCostDetail::model()->tableName())->listdata($params);
        $this->render('index', array('page' => $page));
    }

}
