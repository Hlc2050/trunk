<?php

/**
 * 渠道类型设置控制器
 * User: hlc
 * Date: 2018/11/22
 * Time: 11:19
 */
class ChannelTypeManageController extends AdminController
{
    public function actionIndex()
    {
        $temp = array();

        $data = ChannelType::model()->findAll();

        foreach ($data as $key => $value) {
            $temp[$key]['id'] = $value['id'];
            $temp[$key]['fans_input'] = $value['fans_input'];
            $temp[$key]['type_name'] = $value['type_name'];
            if (unserialize($value['type_rule'])) {
                $str = '';
                foreach (unserialize($value['type_rule']) as $v) {
                    $str .= $v . ',';
                }
                $str = rtrim($str, ',');
                $temp[$key]['type_rule'] = $str;
            }
        }

        $this->render('index', array('data' => $temp));
    }

    /**
     * 添加渠道类型
     */
    public function actionAdd()
    {
        if ($_POST) {
            $type = $this->get('type');
            $fans_input = $this->get('fans_input');
            $standard = serialize($_POST['standard']);

            $info = new ChannelType();
            $info->type_name = $type;
            $info->fans_input = $fans_input;
            $info->type_rule = $standard;
            $info->add_time = time();
            $info->update_time = time();
            $info->save();

            $this->msg(array('state' => 1, 'msgwords' => '添加成功！'));
        }
        $this->render('add');
    }

    /**
     * 修改渠道类型
     */
    public function actionEdit()
    {
        $id = $this->get('id');
        if ($id == '') $this->msg(array('state' => 0, 'msgwords' => '无该条记录！'));
        if ($_POST) {
            $type = $this->get('type');
            $fans_input = $this->get('fans_input');
            $standard = serialize($_POST['standard']);

            $info = ChannelType::model()->find('id=' . $id);
            $info->type_name = $type;
            $info->fans_input = $fans_input;
            $info->type_rule = $standard;
            $info->update_time = time();
            $info->update();

            $this->msg(array('state' => 1, 'msgwords' => '修改成功！', 'url' => 'index'));
        } else {
            $temp = array();
            $str = '';

            $data = ChannelType::model()->find('id=' . $id);
            if (!$data) $this->msg(array('state' => 0, 'msgwords' => '无该渠道类型', 'url' => 'index'));
            $temp['id'] = $data['id'];
            $temp['fans_input'] = $data['fans_input'];
            $temp['type_name'] = $data['type_name'];
            if (unserialize($data['type_rule'])) {
                foreach (unserialize($data['type_rule']) as $v) {
                    $str .= $v . ',';
                }
                $str = rtrim($str, ',');
                $temp['type_rule'] = $str;
            }
        }

        $this->render('add', array('edit' => $temp));
    }

    /**
     * 删除渠道类型
     */
    public function actionDel()
    {
        $id = $this->get('id');
        if ($id == '') $this->msg(array('state' => 0, 'msgwords' => '无该条记录！'));
        $result = Channel::model()->findAll('type_id=' . $id);
        if ($result) $this->msg(array('state' => 0, 'msgwords' => '渠道类型已关联！', 'url' => 'index'));
        $info = ChannelType::model()->find('id=' . $id);
        $info->delete();

        $this->msg(array('state' => 1, 'msgwords' => '删除成功！', 'url' => 'index'));
    }

    /**
     * 判断渠道类型名称是否已存在
     */
    public function actionIsSameName()
    {
        $type = $this->get('type');
        $result = ChannelType::model()->find('type_name like "' . $type . '"');
        if (!empty($result)) echo 'exist';
    }
}