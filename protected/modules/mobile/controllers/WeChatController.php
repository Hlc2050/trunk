<?php

/**
 * 微信处理
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/1
 * Time: 10:13
 */
class WeChatController extends AdminController
{
    /**
     * 微信号列表查询
     * author: yjh
     */
    public function actionIndex()
    {
        $page = $this->getWechatData();
        $this->render('index', array('page' => $page));
    }


    /**
     * 修改微信号
     * author: yjh
     */
    public function actionEdit()
    {
        $page = array();
        $id = $this->get('id');
        $info = WeChat::model()->findByPk($id);
        if (!$info) {
            $this->mobileMsg(array('status' => 0, 'content' => '数据不存在'));
        }

        //修改前的数据
        $old_wechat_id = $info->wechat_id;
        $old_goods_id = $info->goods_id;
        $old_character_id = $info->character_id;
        $old_customer_service_id = $info->customer_service_id;
        $old_promotion_staff_id = $info->promotion_staff_id;
        $old_department_id = $info->department_id;
        $old_business_type = $info->business_type;
        $old_charging_type = $info->charging_type;
        $old_status = $info->status;
        $old_land_url = $info->land_url;
        $wlogs = '';
        //显示表单
        if (!$_POST) {
            $page['info'] = $this->toArr($info);
            $goodsInfo = $this->toArr(Goods::model()->findByPk($page['info']['goods_id']));
            $characterIds = explode(',', $goodsInfo['characters']);
            foreach ($characterIds as $val) {
                $page['info']['characterList'][] = array(
                    'id' => $val,
                    'name' => Linkage::model()->getCharacterById($val)
                );
            }
            $departmentList = AdminUser::model()->get_user_group($page['info']['promotion_staff_id']);
            $goodsList = CustomerServiceRelation::model()->getGoodsList($page['info']['customer_service_id']);
            $page['info']['change_logs'] = $this->get_change_logs($id);
            $page['info']['departmentList'] = $departmentList;
            $page['info']['goodsList'] = $goodsList;
            $page['info']['chargingTypeList'] = Dtable::toArr(BusinessTypeRelation::model()->findAll('bid=:bid', array(':bid' => $page['info']['business_type'])));
            foreach ($page['info']['chargingTypeList'] as $k => $v) {
                $page['info']['chargingTypeList'][$k]['cname'] = vars::get_field_str('charging_type', $v['charging_type']);
            }
            $this->render('update', array('page' => $page));
            exit;
        }
        //表单验证
        // 需插入微信号使用记录的数据
        $log_data = array();
        $log_data['wx_id'] = $id;
        /*微信号ID*/
        $info->wechat_id = trim($this->post('wechat_id'));
        $result = WeChat::model()->count('wechat_id=:wechat_id and id!=:id', array(':wechat_id' => $info->wechat_id, 'id' => $id));
        if ($info->wechat_id == '') $this->mobileMsg(array('status' => 0, 'content' => '未填微信号！'));
        if ($result > 0) $this->mobileMsg(array('status' => 0, 'content' => '此微信号已存在，请重新输入！'));
        $wlogs .= $old_wechat_id == $info->wechat_id ? '' : "微信号id：'$old_wechat_id' 变更为 '$info->wechat_id';<br/>";
        // 微信号变更，则插入微信号使用记录表
        $log_data['wechat_id'] = $info->wechat_id;
        $old_data['wechat_id'] = $old_wechat_id;
        /*客服部ID*/
        $info->customer_service_id = $this->post('customer_service_id');
        if ($info->customer_service_id == '') $this->mobileMsg(array('status' => 0, 'content' => '未选择客服部！'));
        if ($old_customer_service_id != $info->customer_service_id) {
            $old_cname = CustomerServiceManage::model()->getCSName($old_customer_service_id);
            $cname = CustomerServiceManage::model()->getCSName($info->customer_service_id);
            $wlogs .= "客服部：'$old_cname' 变更为 '$cname';<br/>";
        }
        $log_data['customer_service_id'] = $info->customer_service_id;
        $old_data['customer_service_id'] = $old_customer_service_id;
        /*商品ID*/
        $info->goods_id = $this->post('goods_id');
        if ($info->goods_id == '') $this->mobileMsg(array('status' => 0, 'content' => '未选择商品！'));
        if ($old_goods_id != $info->goods_id) {
            $old_good_name = Goods::model()->getGoodsName($old_goods_id);
            $good_name = Goods::model()->getGoodsName($info->goods_id);
            $wlogs .= "商品：'$old_good_name' 变更为 '$good_name';<br/>";
        }
        $log_data['goods_id'] = $info->goods_id;
        $old_data['goods_id'] = $old_goods_id;
        /*形象ID*/
        $info->character_id = $this->post('character_id');
        if ($info->character_id == '') $this->mobileMsg(array('status' => 0, 'content' => '未选择形象！'));
        if ($old_character_id != $info->character_id) {
            $old_character = Linkage::model()->get_name($old_character_id);
            $character = Linkage::model()->get_name($info->character_id);
            $wlogs .= "形象：'$old_character' 变更为 '$character';<br/>";
        }
        $log_data['character_id'] = $info->character_id;
        $old_data['character_id'] = $old_character_id;
        /*推广人员ID*/
        $info->promotion_staff_id = $this->post('promotion_staff_id');
        if ($info->promotion_staff_id == '') $this->mobileMsg(array('status' => 0, 'content' => '未选择推广人员！'));
        if ($old_promotion_staff_id != $info->promotion_staff_id) {
            $old_promotion_staff = PromotionStaff::model()->find(array('condition' => 'user_id=:user_id', 'params' => array(':user_id' => $old_promotion_staff_id)))->name;
            $promotion_staff = PromotionStaff::model()->find(array('condition' => 'user_id=:user_id', 'params' => array(':user_id' => $info->promotion_staff_id)))->name;
            $wlogs .= "推广人员：'$old_promotion_staff' 变更为 '$promotion_staff';<br/>";
        }
        $log_data['promotion_staff_id'] = $info->promotion_staff_id;
        $old_data['promotion_staff_id'] = $old_promotion_staff_id;
        /*业务类型*/
        $info->business_type = $this->post('business_type');
        if ($info->business_type == '') $this->mobileMsg(array('status' => 0, 'content' => '未选择业务类型！'));
        if ($old_business_type != $info->business_type) {
            $old_business = BusinessTypes::model()->findByPk($old_business_type)->bname;
            $business_type = BusinessTypes::model()->findByPk($info->business_type)->bname;
            $wlogs .= "业务：'$old_business' 变更为 '$business_type';<br/>";
        }
        $log_data['business_type'] = $info->business_type;
        $old_data['business_type'] = $old_business_type;
        /*计费方式*/
        $info->charging_type = $this->post('charging_type');
        if ($info->charging_type == '') $this->mobileMsg(array('status' => 0, 'content' => '未选择计费方式！'));
        if ($old_charging_type != $info->charging_type) {
            $old_charging = vars::get_field_str('charging_type', $old_charging_type);
            $charging_type = vars::get_field_str('charging_type', $info->charging_type);
            $wlogs .= "计费方式：'$old_charging' 变更为 '$charging_type';<br/>";
        }
        $log_data['charging_type'] = $info->charging_type;
        $old_data['charging_type'] = $old_charging_type;
        /*部门ID*/
        $info->department_id = $this->post('department_id');
        if ($info->department_id == '') $this->mobileMsg(array('status' => 0, 'content' => '未选择部门！'));
        if ($old_department_id != $info->department_id) {
            $old_department_name = AdminGroup::model()->findByPk($old_department_id)->groupname;
            $department_name = AdminGroup::model()->findByPk($info->department_id)->groupname;
            $wlogs .= "部门：'$old_department_name' 变更为 '$department_name';<br/>";
        }
        $log_data['department_id'] = $info->department_id;
        $old_data['department_id'] = $old_department_id;
        /*状态*/
        $info->status = $this->post('status');
        if ($info->status == '') $this->mobileMsg(array('status' => 0, 'content' => '未选择状态！'));
        if ($old_status != $info->status) {
            $old_status = vars::get_field_str('weChat_status', $old_status);
            $status = vars::get_field_str('weChat_status', $info->status);
            $wlogs .= "状态：'$old_status' 变更为 '$status';";
        }
        /*落地页*/
        $info->land_url = $this->post('land_url');
        if ($old_land_url != $info->land_url) {
            $wlogs .= "落地页：'$old_land_url' 变更为 '$info->land_url';";
        }
        $info->update_time = time();
        $info->create_time = time();
        $dbresult = $info->save();
        $id = $info->primaryKey;
        $referer=$this->post('referer')?$this->post('referer'):'wechat/index';

        $msgarr = array('state' => 1, 'content' => '修改微信号成功！','url'=>$referer); //保存的话，跳转到之前的列表
        $logs = "修改了微信号信息：" . $info->wechat_id;
        if ($dbresult === false) {
            //错误返回
            $this->mobileMsg(array('status' => 0, 'content' => '修改微信号失败！'));
        } else {
            //新增和修改之后的动作
            $this->logs($logs);
            if ($wlogs != '') {
                $this->wLogs($wlogs, $id);
                // 更新微信号使用记录表 lxj
                $this->createOldLog($old_data, $id);
                $this->updateWechatUseLog($log_data, 1);
            }
            //成功跳转提示
            $this->mobileMsg($msgarr);
        }
    }


    public function get_change_logs($w_id)
    {
        $where = " a.weixin_id=" . $w_id;

        $sql=" select a.*,b.csname_true from wechat_change_log as a left join cservice as b on b.csno=a.sno where".$where;
        $change_logs=Yii::app()->db->createCommand($sql)->queryAll();
        return $change_logs;
    }


    /**
     * 获取微信号数据
     * @param int $type
     * author: yjh
     */
    private function getWechatData()
    {
        //搜索
        $params['where'] = '';
        //$params['where'] .= " and(status!=4) ";
        if ($this->get('wechat_id') != '') $params['where'] .= " and(a.wechat_id like '%" . $this->get('wechat_id') . "%') ";
        if ($this->get('dt_id') != '') $params['where'] .= " and(a.department_id = '" . $this->get('dt_id') . "') ";
        if ($this->get('cs_id') != '') $params['where'] .= " and(a.customer_service_id = '" . $this->get('cs_id') . "') ";
        if ($this->get('bs_id') != '') $params['where'] .= " and(a.business_type = " . $this->get('bs_id') . ") ";
        if ($this->get('status') !== null && $this->get('status') !== '') $params['where'] .= " and(a.status = '" . $this->get('status') . "') ";
        if ($this->get('goods_id') != '') $params['where'] .= " and(a.goods_id =" . $this->get('goods_id') . ") ";
        if ($this->get('character_id') != '') $params['where'] .= " and(a.character_id =" . $this->get('character_id') . ") ";
        if ($this->get('promotion_staff_id') != '') $params['where'] .= " and(a.promotion_staff_id = '" . $this->get('promotion_staff_id') . "') ";

        $params['order'] = "  order by id desc    ";
        $params['pagesize'] = 20;

        $params['join'] = "
		left join goods as g on g.id=a.goods_id
		left join linkage as l on l.linkage_id=a.character_id
		left join business_types as b on b.bid=a.business_type
		left join customer_service_manage as c on c.id=a.customer_service_id
		left join promotion_staff_manage as p on p.user_id=a.promotion_staff_id
		left join cservice_group as s on s.groupid=a.department_id
		";

        $params['pagebar'] = 1;
        $params['page_type'] = 1;

        $params['select'] = " a.*,s.groupname as department_name,g.goods_name,linkage_name as character_name,bname as business_type,c.cname as customer_service,p.name as promotion_staff";
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(WeChat::model()->tableName())->listdata($params);
        foreach ($page['listdata']['list'] as $key => $val) {
            $page['listdata']['list'][$key]['charging_type'] = vars::get_field_str('charging_type', $val['charging_type']);
            $page['listdata']['list'][$key]['status'] = vars::get_field_str('weChat_status', $val['status']);//状态
        }
        return $page;
    }

    /**
     * AJAX获取商品对应形象列表
     * author: yjh
     */
    public function actionGetCharacter()
    {
        if ($this->get('goods_id')) {
            $data = Goods::model()->findByPk($this->get('goods_id'));
            $goodsInfo = $this->toArr($data);
            if (empty($goodsInfo)) echo CHtml::tag('option', array('value' => ''), CHtml::encode('没有形象信息，请选择其他商品'), true);
            $characterIds = explode(',', $goodsInfo['characters']);
            echo CHtml::tag('option', array('value' => ''), CHtml::encode('全部'), true);

            foreach ($characterIds as $key => $val) {
                echo CHtml::tag('option', array('value' => $val), CHtml::encode(Linkage::model()->getCharacterById($val)), true);
            }
        }
    }

    /**
     * AJAX获取业务类型对应计费方式
     * author: yjh
     */
    public function actionGetChargingType()
    {
        if ($this->get('business_type')) {
            $chargingTypes = Dtable::toArr(BusinessTypeRelation::model()->findAll('bid=:bid', array(':bid' => $this->get('business_type'))));
            if (empty($chargingTypes)) echo CHtml::tag('option', array('value' => ''), CHtml::encode('没有计费方式，请选择其他业务类型'), true);

            foreach ($chargingTypes as $key => $val) {
                echo CHtml::tag('option', array('value' => $val['charging_type']), CHtml::encode(vars::get_field_str('charging_type', $val['charging_type'])), true);
            }
        }
    }


    /**
     * AJAX获取推广人员对应部门
     * author: yjh
     */
    public function actionGetDepartment()
    {
        if ($this->get('promotion_staff_id')) {
            $data = AdminUser::model()->get_user_group($this->get('promotion_staff_id'));
            if (empty($data)) echo CHtml::tag('option', array('value' => ''), CHtml::encode('没有部门信息，请选择其他推广人员'), true);

            foreach ($data as $key => $val) {
                echo CHtml::tag('option', array('value' => $val['groupid']), CHtml::encode($val['groupname']), true);
            }
        }
    }

    /*
     * 添加/更新微信使用记录表记录
     * @author lxj
     * @param $data array 插入数据数组
     * @param $action 0:只插入数据,1插入+更新数据
     */
    public function updateWechatUseLog($data, $action = 0)
    {
        $inser_param = array('wx_id', 'wechat_id', 'customer_service_id', 'goods_id', 'character_id', 'business_type', 'department_id', 'promotion_staff_id', 'charging_type');
        foreach ($inser_param as $value) {
            if (!array_key_exists($value, $data)) {
                $this->mobileMsg(array('status' => 0, 'content' => "未传入全部参数"));
                exit();
            }
        }
        if ($action == 1) {
            $use_log = WeChatUseLog::model()->find('wx_id=' . $data['wx_id'] . ' and end_time=0 ');
            if ($use_log) {
                $use_log->end_time = strtotime(date('Y-m-d', time()));
                $use_log->save();
            }
        }
        $use_log = new WeChatUseLog();
        $use_log->wx_id = $data['wx_id'];
        $use_log->wechat_id = $data['wechat_id'];
        $use_log->customer_service_id = $data['customer_service_id'];
        $use_log->goods_id = $data['goods_id'];
        $use_log->character_id = $data['character_id'];
        $use_log->business_type = $data['business_type'];
        $use_log->department_id = $data['department_id'];
        $use_log->promotion_staff_id = $data['promotion_staff_id'];
        $use_log->charging_type = $data['charging_type'];
        $use_log->begin_time = strtotime(date('Y-m-d', time()));
        $use_log->save();
    }

    /*
     * 更新数据时微信号未有使用记录，创建使用记录
     * @param $data 更新前数据
     * @param $wx_id 微信id
     */
    public function createOldLog($data, $wx_id)
    {
        $log = WeChatUseLog::model()->find('wx_id = ' . $wx_id);
        if (!$log) {
            $inser_param = array('wechat_id', 'customer_service_id', 'goods_id', 'character_id', 'business_type', 'department_id', 'promotion_staff_id', 'charging_type');
            foreach ($inser_param as $value) {
                if (!array_key_exists($value, $data)) {
                    $this->mobileMsg(array('status' => 0, 'content' => "未传入全部参数"));
                    exit();
                }
            }
            $log = new WeChatUseLog();
            $log->wx_id = $wx_id;
            $log->wechat_id = $data['wechat_id'];
            $log->customer_service_id = $data['customer_service_id'];
            $log->goods_id = $data['goods_id'];
            $log->character_id = $data['character_id'];
            $log->business_type = $data['business_type'];
            $log->department_id = $data['department_id'];
            $log->promotion_staff_id = $data['promotion_staff_id'];
            $log->charging_type = $data['charging_type'];
            $log->begin_time = strtotime('1997-01-01');
            $log->end_time = strtotime(date('Y-m-d', time()));
            $log->save();
        }
    }

    /*
     * AJAX获取客服部对应商品
     * author: yjh
     */
    public function actionGetGoodsByCs()
    {
        if ($this->get('csid')) {
            $data = CustomerServiceRelation::model()->getGoodsList($this->get('csid'));
            if (empty($data)) echo CHtml::tag('option', array('value' => ''), CHtml::encode('没有商品'), true);
            echo CHtml::tag('option', array('value' => ''), CHtml::encode('全部'), true);
            foreach ($data as $key => $val) {
                echo CHtml::tag('option', array('value' => $val['goods_id']), CHtml::encode($val['goods_name']), true);
            }
        } else {
            $data = Goods::model()->findAll();
            if (empty($data)) echo CHtml::tag('option', array('value' => ''), CHtml::encode('没有商品'), true);
            echo CHtml::tag('option', array('value' => ''), CHtml::encode('全部'), true);
            foreach ($data as $key => $val) {
                echo CHtml::tag('option', array('value' => $val['id']), CHtml::encode($val['goods_name']), true);
            }
        }

    }

    /**
     * Ajax获取商品列表
     * author: yjh
     */
    public function actionGetGoods()
    {
        if (isset($_POST['cs_id'])) {
            $data = Dtable::toArr(CustomerServiceRelation::model()->findAll('cs_id=' . $_POST['cs_id']));
            if (empty($data)) echo CHtml::tag('option', array('value' => ''), CHtml::encode('没有商品信息'), true);

            foreach ($data as $key => $val) {
                echo CHtml::tag('option', array('value' => $val['goods_id']), CHtml::encode(Goods::model()->findByPk($val['goods_id'])->goods_name), true);
            }
        }
    }

}