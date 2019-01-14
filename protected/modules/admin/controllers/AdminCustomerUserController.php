<?php

/**
 * 客服账号管理控制器
 * User: hlc
 * Time: 2019年1月8日17:54:34
 */
class AdminCustomerUserController extends AdminController
{
    public function actionIndex()
    {
        $params['where'] = '';
        $params['where'] .= ' and a.service=1';
        if ($this->get('csid')) {
            $params['where'] = " and(a.csdepartment = " . $this->get('csid') . ") ";
        }
        $params['order'] = "  order by a.csno    ";
        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['select'] = "a.*,b.cname";
        $params['join'] = "left join customer_service_manage as b on b.id=a.csdepartment ";
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model('cservice')->listdata($params);
        $page['listdata']['url'] = urlencode('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"]);
        $this->render('index', array('page' => $page));
    }

    /**
     * 新建账号
     */
    public function actionAdd()
    {
        if ($_POST) {
            $field = array();

            $service_name = $this->get('service_name');
            $pwd = $this->get('serviece_pwd');
            $csid = $this->get('csid');

            //搜索条件判断
            $ret = AdminUser::model()->find('csname=' . '"' . $service_name . '"');
            if ($ret != null) $this->msg(array('state' => 0, 'msgwords' => $service_name . '已存在'));
            //验证密码的合法性
            verify::check_password($pwd) != 0 ? $field['cspwd'] = AdminUser::password($pwd) : $this->msg(array('state' => 0, 'msgwords' => '密码要在6-12位'));

            $info = new AdminUser();
            $info->csname = $service_name;
            $info->csdepartment = $csid;
            $info->cspwd = $field['cspwd'];
            $info->service = 1;
            $info->save();

            $ret = AdminUser::model()->find('csname=' . '"' . $service_name . '"');
            $name = $ret['csno'];
            $menu = explode(',', yii::app()->params['customer_config']['menu']);
            $action = explode(',', yii::app()->params['customer_config']['action']);
            //添加动作、模块
            AdminUser::model()->saveUserAuth($name, $menu, $action);
            $this->logs('客服账号注册成功');
            $this->msg(array('state' => 1, 'msgwords' => '客服账号注册成功', 'url' => 'index'));
        }

        $this->render('add');
    }

    /**
     * 修改密码
     */
    public function actionUpdate()
    {
        $csname = $this->get('csname');
        if ($_POST) {
            $name = $this->get('name');
            $new_pwd = $this->get('new_pwd');
            $ret = AdminUser::model()->find('csname=' . '"' . $name . '"');
            if (!$ret) $this->msg(array('state' => 0, 'msgwords' => '没有该客服人员'));
            if (AdminUser::password($this->get('old_pwd')) != $ret['cspwd'])
                $this->msg(array('state' => 0, 'msgwords' => '原密码输入错误'));

            $ret->cspwd = AdminUser::password($new_pwd);
            $ret->save();
            $this->logs('客服账号密码修改成功');
            $this->msg(array('state' => 1, 'msgwords' => '密码修改成功', 'url' => 'index'));

        }
        $this->render('update', array('csname' => $csname));
    }

    /**
     * 更改状态 启用：1 暂停:0
     */
    public function actionSwitch()
    {
        $state = $this->get('state');
        $name = $this->get('csname');
        $ret = AdminUser::model()->find('csname=' . '"' . $name . '"');
        if($state == '' || $name == '' || !$ret)  $this->msg(array('state' => 0, 'msgwords' => ' 错误', 'url' => 'index'));
        
        $ret->csstatus = $state == 0? 1:0;
        $ret->save();
        $this->logs('客服账号更改状态成功');
        $this->redirect(array('adminCustomerUser/index'));
    }
}