<?php

/**
 * 商品处理
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/1
 * Time: 10:13
 */
class GoodsController extends AdminController
{
    /**
     * 商品列表页
     * author: yjh
     */
    public function actionIndex()
    {
        //搜索
        $params['where'] = '';
        //$params['where'] .= " and(delete_status!=1) ";
        if ($this->get('search_type') == 'keys' && $this->get('search_txt')) {
            $params['where'] = " and(goods_name like '%" . $this->get('search_txt') . "%') ";
        } else if ($this->get('search_type') == 'id' && $this->get('search_txt')) { //网点ID
            $params['where'] = " and(id=" . intval($this->get('search_txt')) . ") ";
        }

        $params['order'] = "  order by id desc    ";
        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(Goods::model()->tableName())->listdata($params);
        foreach ($page['listdata']['list'] as $key => $val) {
            $characterTemp = explode(',', $val['characters']);
            $character = array();
            foreach ($characterTemp as $v) {
                $character[] = Linkage::model()->getCharacterById($v);
            }
            $page['listdata']['list'][$key]['service_group'] = vars::get_field_str('sex', $val['service_group']);//状态
            $page['listdata']['list'][$key]['characters'] = implode(' ', $character);
            $page['listdata']['list'][$key]['cat_name'] = Linkage::model()->getCharacterById($val['cat_id']);
        }
        $this->render('index', array('page' => $page));
    }

    /**
     * 新增商品以及处理表单
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
        $info = new Goods();
        $info->goods_name = $this->post('goods_name');
        $result = Goods::model()->count('goods_name=:goods_name', array(':goods_name' => $info->goods_name));
        if ($info->goods_name == '') $this->msg(array('state' => 0, 'msgwords' => '商品名称不能为空'));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '此商品名称已存在，请重新输入！'));
        if ($this->post('character_list') == '') $this->msg(array('state' => 0, 'msgwords' => '形象未选'));
        $info->characters = implode(',', $this->post('character_list'));
        $info->cat_id = $this->post('cat_id');
        if ($info->cat_id == '') $this->msg(array('state' => 0, 'msgwords' => '商品类别未选'));
        $info->remark = $this->post('remark');
        $info->service_group = $this->post('service_group');
        $info->update_time = time();
        $info->create_time = time();
        $dbresult = $info->save();
        $id = $info->primaryKey;
        $msgarr = array('state' => 1, 'url' => $this->createUrl('goods/index') . '?p=' . $_GET['p'] . ''); //保存的话，跳转到之前的列表
        $logs = "添加了商品信息：" . $info->goods_name;
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
     * 编辑商品及处理表单
     * author: yjh
     */
    public function actionEdit()
    {
        $page = array();
        $info = Goods::model()->findByPk($this->get('id'));

        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }

        if (!$_POST) {
            $page['info'] = $this->toArr($info);
            $this->render('update', array('page' => $page));
            exit;
        }
        //表单验证
        $info->goods_name = $this->post('goods_name');
        $result = Goods::model()->count('goods_name=:goods_name and id!=:id', array(':goods_name' => $info->goods_name, ':id' => $this->get('id')));
        if ($info->goods_name == '') $this->msg(array('state' => 0, 'msgwords' => '商品名称不能为空'));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '此商品名称已存在，请重新输入！'));
        if ($this->post('character_list') == '') $this->msg(array('state' => 0, 'msgwords' => '形象未选'));
        $info->characters = implode(',', $this->post('character_list'));
        $info->cat_id = $this->post('cat_id');
        if ($info->cat_id == '') $this->msg(array('state' => 0, 'msgwords' => '商品类别未选'));
        $info->remark = $this->post('remark');
        $info->service_group = $this->post('service_group');
        $info->update_time = time();
        $dbresult = $info->save();

        $msgarr = array('state' => 1, 'url' => $this->createUrl('goods/index') . '?p=' . $_GET['p'] . ''); //保存的话，跳转到之前的列表
        $logs = "修改了商品信息：" . $info->goods_name;
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
     * 删除商品
     * author: yjh
     */
    public function actionDelete()
    {
        if ($this->get('id') == '') $this->msg(array('state' => 0, 'msgwords' => '未传入数据'));
        $id = $this->get('id');
        $info = Goods::model()->findByPk($id);
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }
        $result = WeChat::model()->count("goods_id=:goods_id", array(":goods_id" => $id));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '此商品在微信列表中使用不能删除！'));
        $result = StatCostDetail::model()->count("goods_id=:goods_id", array(":goods_id" => $id));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '成本明细中用到该商品信息不能删除！'));

        $info->delete();
        //删掉客服部选择此商品
        CustomerServiceRelation::model()->deleteAll("goods_id=:goods_id", array(":goods_id" => $id));
        $this->logs("删除了商品：".$info->goods_name);
        $this->msg(array('state' => 1, 'msgwords' => '删除商品【'.$info->goods_name.'】成功！'));


    }
}