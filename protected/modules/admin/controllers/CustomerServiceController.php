<?php

/**
 * 客服部管理
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/12/13
 * Time: 17:53
 */
class CustomerServiceController extends AdminController
{
    /**
     * 客服部列表页
     * author: yjh
     */
    public function actionIndex()
    {
        //搜索
        $params['where'] = '';
        if ($this->get('state_cooperation') != null) {
            $params['where'] .= 'and state_cooperation='.intval($this->get('state_cooperation'));
        }
        $params['order'] = "  order by id desc    ";
        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(CustomerServiceManage::model()->tableName())->listdata($params);
        foreach ($page['listdata']['list'] as $key => $val) {
            $goodsList = $this->toArr(CustomerServiceRelation::model()->findAll('cs_id=:id', array(':id' => $val['id'])));
            $page['listdata']['list'][$key]['goodsList']  = Goods::model()->getGoodsNameByIds($goodsList);//微信号
        }
        $this->render('index', array('page' => $page));
    }

    /**
     * 新增客服部
     * author: yjh
     */
    public function actionAdd()
    {
        $page = array();
        if (!$_POST) {
            $this->render('update', array('page' => $page));
            exit;
        }
        //表单验证
        $info = new CustomerServiceManage();
        $info->cname = $this->post('cname');
        $result = CustomerServiceManage::model()->count('cname=:cname', array(':cname' => $info->cname));
        if ($info->cname == '') $this->msg(array('state' => 0, 'msgwords' => '客服部不能为空！'));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '此客服部名称已存在，请重新输入！'));
        if ($this->post('goodsList') == '') $this->msg(array('state' => 0, 'msgwords' => '至少选择一种商品！'));
        
        $info->estimate_rate = $this->post('estimate_rate');
        if ($info->estimate_rate == '') $this->msg(array('state' => 0, 'msgwords' => '预计发货率未填！'));
        if(!is_numeric($info->estimate_rate)) $this->msg(array('state' => 0, 'msgwords' => '预计发货率必须填数字！'));
        if($info->estimate_rate < 0 || $info->estimate_rate > 100)$this->msg(array('state' => 0, 'msgwords' => '预计发货率范围为[0-100]！'));
        $info->status = $this->post('status');
        $info->state_cooperation = $this->get('state_cooperation');
        $info->update_time = time();
        $info->create_time = time();
        $dbresult = $info->save();
        $id = $info->primaryKey;
        foreach ( $this->post('goodsList') as $val){
            $cInfo = new CustomerServiceRelation();
            $cInfo->goods_id = $val;
            $cInfo->cs_id = $id;
            $cInfo->create_time = time();
            $cInfo->save();
        }
        $msgarr = array('state' => 1, 'url' => $this->createUrl('customerService/index') . '?p=' . $_GET['p'] . '');

        $logs = "添加了新的客服部信息：" . $info->cname;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            //新增和修改之后的动作
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 修改客服部
     * author: yjh
     */
    public function actionEdit()
    {
        $page = array();
        $info = CustomerServiceManage::model()->findByPk($this->get('id'));

        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }

        if (!$_POST) {
            $page['info'] = $this->toArr($info);
            $cInfo = Dtable::toArr(CustomerServiceRelation::model()->findAll('cs_id='.$this->get('id')));
            $goodsArr=array();
            foreach ($cInfo as $key=>$val)
            {
                $goodsArr[]=$val['goods_id'];
            }
            $page['info']['goodsArr'] = $goodsArr;
            $this->render('update', array('page' => $page));
            exit;
        }
        //表单验证
        $info->cname = $this->post('cname');
        $result = CustomerServiceManage::model()->count('cname=:cname and id!=:id', array(':cname' => $info->cname, 'id' => $this->get('id')));
        if ($info->cname == '') $this->msg(array('state' => 0, 'msgwords' => '客服部不能为空！'));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '此客服部名称已存在，请重新输入！'));
        if ($this->post('goodsList') == '') $this->msg(array('state' => 0, 'msgwords' => '至少选择一种商品！'));

        $info->estimate_rate = $this->post('estimate_rate');
        $info->state_cooperation = $this->get('state_cooperation');
        if ($info->estimate_rate == '') $this->msg(array('state' => 0, 'msgwords' => '预计发货率未填！'));
        if(!is_numeric($info->estimate_rate)) $this->msg(array('state' => 0, 'msgwords' => '预计发货率必须填数字！'));
        if($info->estimate_rate < 0 || $info->estimate_rate > 100)$this->msg(array('state' => 0, 'msgwords' => '预计发货率范围为[0-100]！'));
        $info->update_time = time();
        $dbresult = $info->save();
        $id = $info->primaryKey;
       CustomerServiceRelation::model()->deleteAll('cs_id='.$this->get('id'));
        foreach ( $this->post('goodsList') as $val){
            $cInfo = new CustomerServiceRelation();
            $cInfo->goods_id = $val;
            $cInfo->cs_id = $id;
            $cInfo->create_time = time();
            $cInfo->save();
        }

        $msgarr = array('state' => 1, 'url' => $this->createUrl('customerService/index') . '?p=' . $_GET['p'] . ''); //保存的话，跳转到之前的列表
        $logs = "修改了客服部信息：" . $info->cname;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            //新增和修改之后的动作
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 删除客服部
     * author: yjh
     */
    public function actionDelete()
    {
        if ($this->get('id') == '') $this->msg(array('state' => 0, 'msgwords' => '未传入数据'));
        $id = $this->get('id');
        $info = CustomerServiceManage::model()->findByPk($id);
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }
        //判断删除条件 客服部正在使用的不能删除
        $result = WeChat::model()->count("customer_service_id=:cs_id", array(":cs_id" => $id));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '此客服部在微信列表中使用不能删除！'));
        $result = StatCostDetail::model()->count("customer_service_id=:cs_id", array(":cs_id" => $id));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '成本明细中用到该客服部信息不能删除！'));
        CustomerServiceRelation::model()->deleteAll('cs_id=:cs_id',array(':cs_id'=>$id));

        $info->delete();
        $this->logs("删除了客服部：".$info->cname);
        $this->msg(array('state' => 1, 'msgwords' => '删除了客服部【'.$info->cname.'】成功！'));


    }

    /**
     * Ajax获取商品列表
     * author: yjh
     */
    public function actionGetGoods(){
        if (isset($_POST['cs_id'])) {
            $data = Dtable::toArr(CustomerServiceRelation::model()->findAll('cs_id='.$_POST['cs_id']));
            if (empty($data)) echo CHtml::tag('option', array('value' => ''), CHtml::encode('没有商品信息'), true);

            foreach ($data as $key => $val) {
                echo CHtml::tag('option', array('value' => $val['goods_id']), CHtml::encode(Goods::model()->findByPk($val['goods_id'])->goods_name), true);
            }
        }
    }
}