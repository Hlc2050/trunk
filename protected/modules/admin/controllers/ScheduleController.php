<?php

/**
 * 计划表管理
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/12/15
 * Time: 17:53
 */
class ScheduleController extends AdminController
{
    /**
     * 计划列表页
     * author: yjh
     */
    public function actionIndex()
    {
        //搜索
        $params['where'] = '';
        $params['order'] = "  order by id desc    ";
        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(ScheduleManage::model()->tableName())->listdata($params);
        foreach ($page['listdata']['list'] as $key => $val) {
            $page['listdata']['list'][$key]['sname'] = Linkage::model()->get_name($val['schedule_id']);//微信号
        }
        $this->render('index', array('page' => $page));
    }

    /**
     * 新增计划
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
        $info = new ScheduleManage();
        $info->target_time = strtotime($this->post('target_time'));
        if ($info->target_time == '') $this->msg(array('state' => 0, 'msgwords' => '日期不能为空！'));
        $info->schedule_id = $this->post('schedule_id');
        if ($info->schedule_id == '') $this->msg(array('state' => 0, 'msgwords' => '对象不能为空！'));
        $result = ScheduleManage::model()->findByAttributes(array('target_time' => $info->target_time, 'schedule_id' => $info->schedule_id));
        if ($result) $this->msg(array('state' => 0, 'msgwords' => '' . date('Y-m',$info->target_time) . '的' . Linkage::model()->get_name($info->schedule_id) . '已经添加过了，请直接修改！'));
        $info->target_a = $this->post('target_a');
        $info->target_b = $this->post('target_b');
        $info->target_c = $this->post('target_c');
        if ($info->target_a == '') $this->msg(array('state' => 0, 'msgwords' => '目标A不能为空！'));
        if (!is_numeric($info->target_a)) $this->msg(array('state' => 0, 'msgwords' => '目标A必须填数字！'));
        if ($info->target_b == '') $this->msg(array('state' => 0, 'msgwords' => '目标B不能为空！'));
        if (!is_numeric($info->target_b)) $this->msg(array('state' => 0, 'msgwords' => '目标B必须填数字！'));
        if ($info->target_c == '') $this->msg(array('state' => 0, 'msgwords' => '目标C不能为空！'));
        if (!is_numeric($info->target_c)) $this->msg(array('state' => 0, 'msgwords' => '目标C必须填数字！'));

        $info->target_type = $this->post('target_type');
        $info->update_time = time();
        $info->create_time = time();
        $dbresult = $info->save();
        $id = $info->primaryKey;

        $msgarr = array('state' => 1, 'url' => $this->createUrl('schedule/index') . '?p=' . $_GET['p'] . '');

        $logs = "添加了新的计划表信息：ID:$id";
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
     * 修改计划
     * author: yjh
     */
    public function actionEdit()
    {
        $page = array();
        $info = ScheduleManage::model()->findByPk($this->get('id'));

        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }

        if (!$_POST) {
            $page['info'] = $this->toArr($info);
            $this->render('update', array('page' => $page));
            exit;
        }
        //表单验证
        //表单验证

        $info->target_time = strtotime($this->post('target_time'));
        if ($info->target_time == '') $this->msg(array('state' => 0, 'msgwords' => '日期不能为空！'));
        $info->schedule_id = $this->post('schedule_id');
        if ($info->schedule_id == '') $this->msg(array('state' => 0, 'msgwords' => '对象不能为空！'));
        $result = ScheduleManage::model()->find('target_time=:target_time and schedule_id=:schedule_id and id!=:id', array(':target_time' => $info->target_time, ':schedule_id' => $info->schedule_id, ':id' => $this->get('id')));
        if ($result) $this->msg(array('state' => 0, 'msgwords' => '' . date('Y-m',$info->target_time) . '的' . Linkage::model()->get_name($info->schedule_id) . '已经添加过了，请直接修改！'));
        $info->target_a = $this->post('target_a');
        $info->target_b = $this->post('target_b');
        $info->target_c = $this->post('target_c');
        if ($info->target_a == '') $this->msg(array('state' => 0, 'msgwords' => '目标A不能为空！'));
        if (!is_numeric($info->target_a)) $this->msg(array('state' => 0, 'msgwords' => '目标A必须填数字！'));
        if ($info->target_b == '') $this->msg(array('state' => 0, 'msgwords' => '目标B不能为空！'));
        if (!is_numeric($info->target_b)) $this->msg(array('state' => 0, 'msgwords' => '目标B必须填数字！'));
        if ($info->target_c == '') $this->msg(array('state' => 0, 'msgwords' => '目标C不能为空！'));
        if (!is_numeric($info->target_c)) $this->msg(array('state' => 0, 'msgwords' => '目标C必须填数字！'));

        $info->target_type = $this->post('target_type');
        $info->update_time = time();
        $dbresult = $info->save();
        $id = $info->primaryKey;

        $msgarr = array('state' => 1, 'url' => $this->createUrl('schedule/index') . '?p=' . $_GET['p'] . ''); //保存的话，跳转到之前的列表
        $logs = "修改了计划表信息：ID:$id";
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
     * 删除计划
     * author: yjh
     */
    public function actionDelete()
    {
        if ($this->get('id') == '') $this->msg(array('state' => 0, 'msgwords' => '未传入数据'));
        $id = $this->get('id');
        $info = ScheduleManage::model()->findByPk($id);
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }

        $info->delete();
        $this->logs("删除了计划表：ID:$id");
        $this->msg(array('state' => 1, 'msgwords' => '删除了计划表成功！'));


    }


}