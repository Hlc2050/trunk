<?php

class ParamsController extends AdminController
{

    public function actionIndex()
    {
        //搜索
        $params['where'] = '';
        $group_id = $this->get('group_id');
        $page['params_groups'] = ParamsGroup::model()->getAll();

        if ($group_id) {
            $params['where'] .= " and(a.group_id=$group_id)";
        } else {
            if (count($page['params_groups'])) {
                header("location:" . $this->createUrl('params/index') . "?group_id=" . $page['params_groups'][0]['id']);
            }
        }

        $params['order'] = "  order by a.displayorder,a.id      ";
        $params['pagesize'] = 100;
        $params['join'] = "left join params_group  as b on b.id=a.group_id ";
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $params['select'] = ' a.*';
        $page['listdata'] = Dtable::model(Params::model()->tableName())->listdata($params);

        $this->render('index', array('page' => $page));

    }

    public function actionAdd()
    {
        $page = array();
        $id = $this->get('id');
        //显示表单
        if (!$_POST) {
            $this->render('update', array('page' => $page));
            exit();
        }

        //处理需要的字段
        $info = new Params();
        $info->param_desc = $this->post('param_desc');
        $info->param_name = $this->post('param_name');
        $info->param_value = $this->post('param_value');
        if ($info->param_desc == '') {
            $this->msg(array('state' => 0, 'msgwords' => '参数名称不能为空'));
        }
        if ($info->param_name == '') {
            $this->msg(array('state' => 0, 'msgwords' => '参数属性不能为空'));
        }

        $info->group_id = $this->post('group_id');
        $info->displayorder = 50;
        $dbresult = $info->save();
        $id = $info->primaryKey;
        $msgarr = array('state' => 1);  //新增的话跳转会添加的页面
        $logs = "添加了参数ID：$dbresult" . $info->param_desc;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            //新增和修改之后的动作
            $this->actionCreate();
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }


    }

    public function actionCreate()
    {
        $file = dirname(__FILE__) . "/../../../config/params.php";
        $config = array();

        $a = ParamsGroup::model()->getAll();

        foreach ($a as $r) {
            $b = Params::model()->findAllByAttributes(array('group_id' => $r['id']));
            foreach ($b as $r2) {
                $config[$r['group_param_name']][$r2['param_name']] = "'" . $r2['param_value'] . ">>>";
            }

        }
        $configStr = print_r($config, 1);
        $configStr = str_replace(array('[', ']'), "'", $configStr);
        $configStr = str_replace('>>>', "',", $configStr);
        $configStr = preg_replace('~,(\s*)\)~', ',$1),', $configStr);

        $configStr = '<?php
        return ' . $configStr . '
        ?>';
        
        helper::file_save($file, $configStr);

        $this->msg(array('state' => 1));

    }

    public function actionEdit()
    {
        $page = array();
        $id = $this->get('id');
        $info = Params::model()->findByPk($id);
        //显示表单
        if (!$_POST) {
            $info = $this->toArr($info);
            if (!$info) {
                $this->msg(array('state' => 0, 'msgwords' => '文档不存在'));
            }
            $page['info'] = $info;
            $this->render('update', array('page' => $page));
            exit();

        }
        //处理需要的字段
        $info->param_desc = $this->post('param_desc');
        $info->param_name = $this->post('param_name');
        $info->param_value = $this->post('param_value');
        if ($info->param_desc == '') {
            $this->msg(array('state' => 0, 'msgwords' => '参数名称不能为空'));
        }
        if ($info->param_name == '') {
            $this->msg(array('state' => 0, 'msgwords' => '参数属性不能为空'));
        }

        $info->group_id = $this->post('group_id');
        $dbresult = $info->save();
        $msgarr = array('state' => 1,); //保存的话，跳转到之前的列表
        $logs = '修改了参数 ID:' . $id . '' . $info->param_desc . ' ';
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            //新增和修改之后的动作
            $this->actionCreate();
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    public function actionDelete()
    {

        $idstr = $this->get('ids');
        $ids = explode(',', $idstr);
        foreach ($ids as $id) {
            $m = Params::model()->findByPk($id);
            if (!$m) continue;
            $m->delete();

        }
        $this->logs('删除了参数ID（' . $idstr . '）');
        $this->msg(array('state' => 1));
    }

    /*
     * 修改配置文件
     */

    public function actionSaveOrder()
    {

        $listorders = $this->get('listorders', array());
        foreach ($listorders as $id => $order) {
            $m = Params::model()->findByPk($id);
            if (!$m) continue;
            $m->displayorder = $order;
            $m->save();
        }
        $this->logs('修改了参数的排序');
        $this->msg(array('state' => 1));
    }

}

?>