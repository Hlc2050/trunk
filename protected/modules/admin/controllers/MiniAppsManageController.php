<?php

/**
 * 小程序管理
 * Created by PhpStorm.
 * User: yjh
 * Date: 2017/8/16
 * Time: 09:26
 */
class MiniAppsManageController extends AdminController
{
    /**
     * 小程序列表
     * author: yjh
     */
    public function actionIndex()
    {

        $page = array();
        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['pagebar'] = 1;

        $params['select'] = "a.id,a.app_name,a.create_time,a.official_accounts,a.click_num,a.status,w.wechat_group_name";
        $params['join'] = "
        		left join wechat_group as w on w.id=a.wechat_group_id
                ";
        $page['listdata'] = Dtable::model(MiniAppsManage::model()->tableName())->listdata($params);
        $page['listdata']['url'] = urlencode('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"]);

        $this->render('index', array('page' => $page));
        exit;
    }

    /**
     * 新增小程序
     * author: yjh
     */
    public function actionAdd()
    {
        $page = array();

        if (!$_POST) {
            $this->render('update', array('page' => $page));
            exit;
        }
        $info = new MiniAppsManage();
        $info->app_name = $this->get('app_name');
        $info->origin_id = $this->get('origin_id');
        $info->appid = $this->get('appid');
        $info->secret = $this->get('secret');
        $info->official_accounts = $this->get('official_accounts');
        $info->fpay_id = $this->get('fpay_id');
        $info->wechat_group_id = $this->get('wechat_group_id');
        $info->kefu_type = $this->get('kefu_type');
        $info->kefu_content = $this->get('kefu_content');

        if ($info->app_name == '') $this->msg(array('state' => 0, 'msgwords' => '小程序名称未填写'));
        if ($info->origin_id == '') $this->msg(array('state' => 0, 'msgwords' => '原始ID未填写'));
        if ($info->official_accounts == '') $this->msg(array('state' => 0, 'msgwords' => '关联公众号未填写'));
        if ($info->wechat_group_id == '') $this->msg(array('state' => 0, 'msgwords' => '打款id不存在'));
        if ($info->kefu_content == ''&&  $info->kefu_type==1) $this->msg(array('state' => 0, 'msgwords' => '客服文字不能为空'));
        $info->click_num = 0;
        $info->status = 0;
        $info->update_time = time();
        $info->create_time = time();
        $dbresult = $info->save();
        $id = $info->primaryKey;
        $msgarr = array('state' => 1, 'url' => $this->createUrl('miniAppsManage/index') . '?p=' . $_GET['p'] . '');
        $logs = "添加了小程序：$id 、" . $info->app_name;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }

    }

    /**
     * 编辑小程序
     * author: yjh
     */
    public function actionEdit()
    {
        $page = array();
        $id = $this->get('id');
        $info = MiniAppsManage::model()->findByPk($id);
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }
        if (!$_POST) {
            $page['info'] = $this->toArr($info);
            $this->render('update', array('page' => $page));
            exit;
        }
        $info->app_name = $this->get('app_name');
        $info->origin_id = $this->get('origin_id');
        $info->appid = $this->get('appid');
        $info->secret = $this->get('secret');
        $info->official_accounts = $this->get('official_accounts');
        $info->fpay_id = $this->get('fpay_id');
        $info->wechat_group_id = $this->get('wechat_group_id');
        $info->kefu_type = $this->get('kefu_type');
        $info->kefu_content = $this->get('kefu_content');

        if ($info->app_name == '') $this->msg(array('state' => 0, 'msgwords' => '小程序名称未填写'));
        if ($info->origin_id == '') $this->msg(array('state' => 0, 'msgwords' => '原始ID未填写'));
        if ($info->official_accounts == '') $this->msg(array('state' => 0, 'msgwords' => '关联公众号未填写'));
        if ($info->wechat_group_id == '') $this->msg(array('state' => 0, 'msgwords' => '打款id不存在'));
        if ($info->kefu_content == ''&&  $info->kefu_type==1) $this->msg(array('state' => 0, 'msgwords' => '客服文字不能为空'));

        $info->update_time = time();
        $redis_flag = Yii::app()->params['basic']['is_redis'];
        //判断redis是否有数据
        if ($redis_flag == 1) {
            $ret = Yii::app()->redis->getValue('mininAppsInfo:id:' . $id);
            if ($ret) Yii::app()->redis->deleteValue('mininAppsInfo:id:' . $id);
            Yii::app()->redis->deleteValues('*'.$info->origin_id.'*');

        }

        $dbresult = $info->save();
        $id = $info->primaryKey;
        $msgarr = array('state' => 1, 'url' => $this->createUrl('miniAppsManage/index') . '?p=' . $_GET['p'] . '');
        $logs = "修改了小程序：$id 、" . $info->app_name;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 编辑小程序导航内容
     * author: yjh
     */
    public function actionContent()
    {
        $page = array();
        $id = $this->get('id');
        $info = MiniAppsManage::model()->findByPk($id);
        if (!$_POST) {
            $page['info'] = $this->toArr($info);
            $this->render('content', array('page' => $page));
            exit;
        }
        $info->content_one = $this->get('content_one');
        $info->content_two = $this->get('content_two');
        $info->content_three = $this->get('content_three');
        $info->is_consult_one = $this->get('is_consult_one');
        $info->is_consult_two = $this->get('is_consult_two');
        $info->is_consult_three = $this->get('is_consult_three');
        if ($info->content_one == '') $this->msg(array('state' => 0, 'msgwords' => '首页内容未填写'));
        if ($info->content_two == '') $this->msg(array('state' => 0, 'msgwords' => '客服反馈内容未填写'));
        if ($info->content_three == '') $this->msg(array('state' => 0, 'msgwords' => '联系我们内容未填写'));
        if ($info->status == 0) $info->status = 1;
        $info->update_time = time();
        $redis_flag = Yii::app()->params['basic']['is_redis'];
        //判断redis是否有数据
        if ($redis_flag == 1) {
            $ret = Yii::app()->redis->getValue('mininAppsInfo:id:' . $id);
            if ($ret) Yii::app()->redis->deleteValue('mininAppsInfo:id:' . $id);
        }
        $dbresult = $info->save();
        $id = $info->primaryKey;
        $msgarr = array('state' => 1, 'url' => $this->createUrl('miniAppsManage/index') . '?p=' . $_GET['p'] . '');
        $logs = "编辑了小程序的导航内容：$id 、" . $info->app_name;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 删除小程序
     * author: yjh
     */
    public function actionDelete()
    {
        if ($this->get('id') == '') $this->msg(array('state' => 0, 'msgwords' => '未传入数据'));
        $id = $this->get('id');
        $info = MiniAppsManage::model()->findByPk($id);
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }
        if ($info->status == 2) {
            $this->msg(array('state' => 0, 'msgwords' => '该小程序正在上线，不能删除'));
        }
        $redis_flag = Yii::app()->params['basic']['is_redis'];
        //判断redis是否有数据
        if ($redis_flag == 1) {
            $ret = Yii::app()->redis->getValue('mininAppsInfo:id:' . $id);
            if ($ret) Yii::app()->redis->deleteValue('mininAppsInfo:id:' . $id);
        }
        $info->delete();
        $this->logs("删除了小程序：.$id 、" . $info->app_name);
        $this->msg(array('state' => 1, 'msgwords' => '删除了小程序：【' . $info->app_name . '】成功！'));
    }

    /**
     * 修改小程序状态
     * author: yjh
     */
    public function actionStatus()
    {
        $id = $this->get('id');
        $status = $this->get('status');
        if (!$id || !$status) $this->msg(array('state' => 0, 'msgwords' => '参数不完整'));
        //下线能改成下线，其他都是改成上线 2:上线；3：下线
        $status = $status == 2 ? 3 : 2;
        $info = MiniAppsManage::model()->findByPk($id);
        if(empty($info->appid)||empty($info->secret))
            $this->msg(array('state' => 0, 'msgwords' => '小程序配置项未填写完整'));
        $info->status = $status;
        $info->update_time = time();
        $redis_flag = Yii::app()->params['basic']['is_redis'];
        //判断redis是否有数据
        if ($redis_flag == 1) {
            $ret = Yii::app()->redis->getValue('mininAppsInfo:id:' . $id);
            if ($ret) Yii::app()->redis->deleteValue('mininAppsInfo:id:' . $id);
        }
        $dbresult = $info->save();
        $id = $info->primaryKey;
        $msgarr = array('state' => 1, 'url' => $this->createUrl('miniAppsManage/index') . '?p=' . $_GET['p'] . '');
        $logs = "修改了小程序的状态：$id 、" . vars::get_field_str('miniApps_status', $status);
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 客服点击统计表
     * author: yjh
     */
    public function actionClicksStatTable(){
        $page=array();
        $id=$this->get('id');
        if(!$id) $this->msg(array('state' => 0, 'msgwords' => '未传入小程序id'));
        $start_date=strtotime($this->get('start_date'));
        $end_date=strtotime($this->get('stat_date'));
        if(!$start_date&&!$end_date){
            $end_date=strtotime(date('Ymd',time()));
            $start_date=$end_date-86400*6;
        }elseif(!$start_date){
            $start_date=$end_date-86400*6;
        }elseif (!$end_date){
            $end_date=$start_date+86400*6;
        }
//        else{
//            if(($end_date-$start_date)/86400>30){
//                $start_date=$end_date-86400*30;
//            }
//        }
        $page['start_date']=date('Y-m-d',$start_date);
        $page['end_date']=date('Y-m-d',$end_date);
        $page['appsInfo']=$this->toArr(MiniAppsManage::model()->findByPk($id));
        $sql="SELECT * FROM mini_apps_clicks WHERE miniapp_id=$id AND date BETWEEN $start_date AND $end_date";
        $page['data']=Yii::app()->db->createCommand($sql)->queryAll();
        $this->render('clicksDetail', array('page' => $page));
    }


}