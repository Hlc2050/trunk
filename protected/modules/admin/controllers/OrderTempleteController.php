<?php

/**
 * 下单项目
 * Created by PhpStorm.
 * User: yjh
 * Date: 2017/7/5
 * Time: 10:13
 */
class OrderTempleteController extends AdminController
{
    /**
     * 下单项目列表
     * author: yjh
     */
    public function actionIndex()
    {
        $page = array();
        $params['where'] = '';
        if ($this->get('order_title') != '') {
            $params['where'] .= " and(order_title like '%" . $this->get('order_title') . "%') ";
        }
        $params['order'] = "  order by id desc    ";
        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $params['select'] = "a.*,b.id as bid";
        $params['join'] = "left join package_group_manage as b on b.id=package_gid ";
        $page['listdata'] = Dtable::model('order_templete')->listdata($params);
        $page['listdata']['url'] = urlencode('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"]);
        $this->render('index', array('page' => $page));
    }

    /**
     *新增商品组列表
     * author: hlc
     */
    public function actionGetPackages()
    {
        $group_id = $this->get('group_id');

        if (!$group_id) $this->msg(array('state' => 0, 'msgwords' => '无该商品组'));

        $string = '';
        $sql = "select a.*,b.name from package_relation as a left join package_manage as b on a.package_id=b.id  where a.package_group_id=" . $group_id . ' order by a.package_id desc';
        $data = Yii::app()->db->createCommand($sql)->queryAll();

        foreach ($data as $val) {
            $string .= '<tr><td>' . $val['name'] . '<input hidden value="' . $val['package_id'] . '" name="package_id[]">' . '<input hidden value="' . $val['name'] . '" name="package_name[]">' .
                '</td><td>' . $val['price'] . '<input hidden value="' . $val['price'] . '" name="price[]">' . '</td>';
            $string .= '<td><input value="1" type="checkbox" name="recommend_' . $val['package_id'] . '"/>&nbsp;推荐</td>';
            $string .= '</tr>';
        }

        echo $string;
    }

    /**
     * 新增下单项目
     * author: yjh
     */
    public function actionAdd()
    {
        $page = array();
        //显示表单
        if (!$_POST) {
            $this->render('update', array('page' => $page));
            exit;
        }
        //表单验证
        $info = new OrderTemplete();
        $info->order_title = trim($this->post('order_title'));//下单标题
        if ($info->order_title == '') $this->msg(array('state' => 0, 'msgwords' => '未填下单标题'));
        $ret = OrderTemplete::model()->count('order_title=:order_title', array(':order_title' => $info->order_title));
        if ($ret > 0) $this->msg(array('state' => 0, 'msgwords' => '此下单标题列表中已存在'));
        $info->price_color = trim($this->post('price_color'));//价格颜色
        $info->obtn_text = trim($this->post('obtn_text'));//下单提交按钮内容
        if ($info->obtn_text == '') $this->msg(array('state' => 0, 'msgwords' => '下单提交按钮内容'));
        $info->order_tips = trim($this->post('order_tips'));//提示信息
        $info->success_info = trim($this->post('success_info'));//下单成功信息
        if ($info->success_info == '') $this->msg(array('state' => 0, 'msgwords' => '未填下单成功信息'));
        $info->fail_info = trim($this->post('fail_info'));//下单成功信息
        if ($info->fail_info == '') $this->msg(array('state' => 0, 'msgwords' => '未填下单失败信息'));
        $info->is_suspend = $this->post('is_suspend');//是否开启悬浮
        $info->is_dobber = $this->post('is_dobber');//是否开启浮标
        $info->is_carousel = $this->post('is_carousel');//是否开启轮播
        $info->font_color = $this->post('font_color');//整体字体颜色
        $info->overall_color = $this->post('overall_color');//整体颜色
        $info->package_gid = $this->post('package_gid');//套餐组
        $info->goods_templete = $this->post('goods_templete');// 下单框样式模板

        if ($info->package_gid == '') $this->msg(array('state' => 0, 'msgwords' => '未选择套餐组'));
        $tempPackages = array();
        $packages = $this->post('package_id');

        foreach ($packages as $k => $v) {
            $package_id = trim($v);
            $key = 'recommend_' . $package_id;
            $recommend = $this->post($key) ? 1 : 0;
            $tempPackages[$k] = $recommend;
        }
        $str = helper::bindec_digit($tempPackages, 6);
        $info->recommends = $str;

        if ($info->is_suspend == 1) {
            $info->suspend_img = $this->post('suspend_img');//底部悬浮图片
        }
        if ($info->is_dobber == 1) {
            $info->dobber_img = $this->post('dobber_img');//浮标图片
        }
        $tempPackages = array();
        $packages = $this->post('package_id');
        $recommends = $this->post('recommend');
        foreach ($packages as $k => $v) {
            $package_id = trim($v);
            $recommend = trim($recommends[$k]);
            $tempPackages[$k]['package_id'] = $package_id;
            $tempPackages[$k]['recommend'] = $recommend;
        }
        if (empty($tempPackages))
            $this->msg(array('state' => 0, 'msgwords' => '未填写任何套餐！'));
        $info->update_time = time();
        $info->create_time = time();
        $dbresult = $info->save();
        $id = $info->primaryKey;
        foreach ($tempPackages as $key => $value) {
            $m = new OrderPackageRelation();
            $m->package_id = $value['package_id'];
            $m->recommend = $value['recommend'];
            $m->order_templete_id = $id;
            $m->save();
        }
        $logs = "添加了下单模板：" . $info->order_title;
        $this->logs($logs);
        $msgarr = array('state' => 1, 'url' => $this->createUrl('orderTemplete/index') . '?p=' . $_GET['p'] . '');  //新增的话跳转会添加的页面
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 编辑下单模板
     * author: yjh
     */
    public function actionEdit()
    {
        $page = array();
        $id = $this->get('id');
        $info = OrderTemplete::model()->findByPk($id);
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }
        if (!$_POST) {
            $page['info'] = $this->toArr($info);
            $ret = OrderTemplete::model()->getPackageInfo($id);
            $page['packages'] = $ret;
            $this->render('update', array('page' => $page));
            exit;
        }
        $info->order_title = trim($this->post('order_title'));//下单标题
        if ($info->order_title == '') $this->msg(array('state' => 0, 'msgwords' => '未填下单标题'));
        $ret = OrderTemplete::model()->count('order_title=:order_title and id!=:id', array(':order_title' => $info->order_title, ':id' => $id));
        if ($ret > 0) $this->msg(array('state' => 0, 'msgwords' => '此下单标题列表中已存在'));
        $info->price_color = trim($this->post('price_color'));//价格颜色
        $info->goods_color = trim($this->post('goods_color'));//商品颜色
        $info->obtn_text = trim($this->post('obtn_text'));//下单提交按钮内容
        if ($info->obtn_text == '') $this->msg(array('state' => 0, 'msgwords' => '下单提交按钮内容'));
        $info->order_tips = trim($this->post('order_tips'));//提示信息
        $info->success_info = trim($this->post('success_info'));//下单成功信息
        if ($info->success_info == '') $this->msg(array('state' => 0, 'msgwords' => '未填下单成功信息'));
        $info->fail_info = trim($this->post('fail_info'));//下单成功信息
        if ($info->fail_info == '') $this->msg(array('state' => 0, 'msgwords' => '未填下单失败信息'));
        $info->is_suspend = $this->post('is_suspend');//是否开启悬浮
        $info->is_dobber = $this->post('is_dobber');//是否开启浮标
        $info->is_carousel = $this->post('is_carousel');//是否开启轮播
        $info->font_color = $this->post('font_color');//整体字体颜色
        $info->overall_color = $this->post('overall_color');//整体颜色
        $info->package_gid = $this->post('package_gid');//套餐组
        $info->goods_templete = $this->post('goods_templete');// 下单框样式模板
        if ($info->package_gid == '') $this->msg(array('state' => 0, 'msgwords' => '未选择套餐组'));
        if ($info->is_suspend == 1) {
            $info->suspend_img = $this->post('suspend_img');//底部悬浮图片
        }
        if ($info->is_dobber == 1) {
            $info->dobber_img = $this->post('dobber_img');//浮标图片
        }
        //二进制转十位数
        $recommends = array();
        $packages = $this->post('package_id');
        foreach ($packages as $k => $v) {
            $package_id = trim($v);
            $key = 'recommend_' . $package_id;
            $recommend = $this->post($key) ? 1 : 0;
            $recommends[$k] = $recommend;
        }
        $str = helper::bindec_digit($recommends, 6);
        $info->recommends = $str;
        $info->update_time = time();
        $info->create_time = time();
        $dbresult = $info->save();
        $logs = "修改了下单模板：" . $info->order_title;
        $this->logs($logs);
        $msgarr = array('state' => 1, 'url' => $this->get('backurl'));
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    public function actionGetOrderTempletes()
    {
        $where = '';
        if ($this->get('search_type') == 'keys' && $this->get('search_txt')) {
            $where = " and order_title like '%" . $this->get('search_txt') . "%' ";
        }

        $temp_data = $this->toArr(OrderTemplete::model()->getOrderList($where));
        if (isset($_GET['jsoncallback'])) {
            $data['list'] = $temp_data;
            $this->msg(array('state' => 1, 'data' => $data, 'type' => 'jsonp'));
        }
    }

}