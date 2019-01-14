<?php

/**
 * 域名推广记录控制器
 */
class DomainPromotionChangeController extends AdminController
{
    public function actionIndex()
    {
        //搜索
        $params['where'] = '';
        if ($this->get('search_type') == 'keys' && $this->get('search_txt')) {
            $params['where'] .= " and(a.domain like '%" . $this->get('search_txt') . "%' or a.from_domain like '%" . $this->get('search_txt') . "%' ) ";
        } else if ($this->get('search_type') == 'promotion_id' && $this->get('search_txt')) {
            $params['where'] .= " and(a.promotion_id=" . intval($this->get('search_txt')) . " ) ";
        }
        if ($this->get('promotion_id')) {
            $params['where'] .= " and(a.promotion_id=" . intval($this->get('promotion_id')) . " ) ";
        }
        $params['order'] = "  order by id desc      ";
        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['join'] = "";
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(DomainPromotionChange::model()->tableName())->listdata($params);

        $this->render('index', array('page' => $page));
    }
}
?>