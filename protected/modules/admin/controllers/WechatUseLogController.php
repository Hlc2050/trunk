<?php
/**
 * 微信号使用记录控制器
 * User: Administrator
 * Date: 2018/1/26
 * Time: 13:46
 */
class WechatUseLogController extends AdminController
{
    public function actionIndex()
    {
        $page = $this->getLogData();
        $this->render('index',array('page'=>$page));
    }
    private function getLogData()
    {
        //搜索
        $params['where'] = '';

        if ($this->get('wechat_id') != '') $params['where'] .= " and(a.wechat_id like '%" . $this->get('wechat_id') . "%') ";
        if ($this->get('dt_id') != '') $params['where'] .= " and(a.department_id = '" . $this->get('dt_id') . "') ";
        if ($this->get('csid') != '') $params['where'] .= " and(a.customer_service_id = '" . $this->get('csid') . "') ";
        if ($this->get('bs_id') != '') $params['where'] .= " and(a.business_type = " . $this->get('bs_id') . ") ";
        if ($this->get('charge_id') != '') $params['where'] .= " and(a.charging_type = " . $this->get('charge_id') . ") ";
        if ($this->get('goods_id') != '') $params['where'] .= " and(g.id = '" . $this->get('goods_id') . "') ";
        if ($this->get('character_id') != '') $params['where'] .= " and(l.linkage_id = '" . $this->get('character_id') . "') ";
        if ($this->get('promotion_staff_id') != '') $params['where'] .= " and(a.promotion_staff_id = '" . $this->get('promotion_staff_id') . "') ";
        if ($this->get('start_date') != '') $params['where'] .= " and(a.begin_time >= " . strtotime($this->get('start_date')) . ") ";
        if ($this->get('end_date') != '') $params['where'] .= " and(a.end_time <= " . strtotime($this->get('end_date')) . " and a.end_time != 0) ";

        $params['order'] = "  order by id desc    ";
        $params['pagesize'] =  Yii::app()->params['management']['pagesize'];

        $params['join'] = "
		left join goods as g on g.id=a.goods_id
		left join linkage as l on l.linkage_id=a.character_id
		left join business_types as b on b.bid=a.business_type
		left join customer_service_manage as c on c.id=a.customer_service_id
		left join promotion_staff_manage as p on p.user_id=a.promotion_staff_id
		left join cservice_group as s on s.groupid=a.department_id
		";

        $params['pagebar'] = 1;

        $params['select'] = " a.*,s.groupname as department_name,g.goods_name,linkage_name as character_name,bname as business_type,c.cname as customer_service,p.name as promotion_staff";
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(WeChatUseLog::model()->tableName())->listdata($params);
        return $page;
    }

    /*
     * 根据商品id获取商品形象
     * @param int $goods_id 商品id
     */
    public function getCharacterByGoodId($goods_id)
    {
        $goods_characters = Goods::model()->findByPk($goods_id);
        $characters = explode(',',$goods_characters['characters']);
        $res = array();
        foreach ($characters as $val) {
            $res[] = array(
                'linkage_id' => $val,
                'linkage_name' => Linkage::model()->getCharacterById($val)
            );
        }
        return $res;
    }
    /*
     * 根据商品id参数获取形象列表
     * @param int $goods_id 商品id
     */
    public function actionGetCharacter()
    {
        $characterIds = array();
        if ($this->get('goods_id')) {
            $data = Goods::model()->findByPk($this->get('goods_id'));
            $goodsInfo = $this->toArr($data);
            if (empty($goodsInfo)) echo CHtml::tag('option', array('value' => ''), CHtml::encode('全部'), true);
            $characterIds = explode(',', $goodsInfo['characters']);
        } else {
            $charas = Linkage::model()->get_linkage_data(19);
            $characterIds = array_column($charas,'linkage_id');
        }
        echo CHtml::tag('option', array('value' => ''), CHtml::encode('全部'), true);
        foreach ($characterIds as $key => $val) {
            echo CHtml::tag('option', array('value' => $val), CHtml::encode(Linkage::model()->getCharacterById($val)), true);
        }
    }

    /*
   * 根据业务类型id获取计费方式
   * @param int $goods_id 商品id
   */
    public function getChargeByBid($bid)
    {
        $bid_charges = Dtable::toArr(BusinessTypeRelation::model()->findAll(" bid = '".$bid."'"));
        foreach ($bid_charges as $k => $v) {
            $bid_charges[$k]['cname'] = vars::get_field_str('charging_type', $v['charging_type']);
        }
        return $bid_charges;
    }
    /*
     * 根据商品id参数获取计费方式
     * @param int $goods_id 商品id
     */
    public function actionGetChargeType()
    {
        if ($this->get('bs_id')) {
            $bid_charges = Dtable::toArr(BusinessTypeRelation::model()->findAll(" bid = '".$this->get('bs_id')."'"));
        } else {
            $bid_charges = vars::$fields['charging_type'];
            foreach ($bid_charges as $key=>$value) {
                $bid_charges[$key]['charging_type'] = $value['value'];
            }
        }
        if (empty($bid_charges)) echo CHtml::tag('option', array('value' => ''), CHtml::encode('计费方式'), true);
        else {
            echo CHtml::tag('option', array('value' => ''), CHtml::encode('计费方式'), true);
            foreach ($bid_charges as $key => $val) {
                echo CHtml::tag('option', array('value' => $val['charging_type']), CHtml::encode(vars::get_field_str('charging_type', $val['charging_type'])), true);
            }
        }
    }

}