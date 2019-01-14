<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/7
 * Time: 10:17
 */

class TimetableTypeController extends AdminController
{
    public function actionIndex(){
        //显示表单
        $type_info = TimetableType::model()->findAll();
        $this->render('index', array('type_info'=>$type_info));
        exit();
    }
    public function actionEdit(){
        // 更新符合指定条件和主键的行
        foreach ($_POST['type_id'] as $key=>$item){
            $dbresult =  TimetableType::model()->find('type_id = :type_id',array(':type_id'=>$item['type_id']));
            $dbresult->count = $_POST['count'][$key];
            $dbresult->save();
        }
        $logs = "编辑排期类型数值";
        $msgarr = array('state' => 1, 'url' => $this->createUrl('timetableType/index'));
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
    public function actionGetTypeValue()
    {
        $type_id = $this->get('type_id');
        $type_value = TimetableType::model()->find(array(
            'select' => 'count',
            'condition' => 'type_id=:type_id',
            'params' => array(':type_id' => $type_id),
        ));
        if (empty($type_value)) {
            echo CHtml::tag('option', array('value' => ''), CHtml::encode('该排期类型没有数值'), true);
        } else {
            echo CHtml::tag('option', array('value' => ''), CHtml::encode('-请选择-'), true);
            $values = explode(',',$type_value['count']);
            foreach ($values as $item) {
                echo CHtml::tag('option', array('value' => $item), CHtml::encode($item), true);
            }
        }
    }
    public function actionGetTypeValueBYtid()
    {
        if ($this->post('type_id')) {
            $data = TimetableType::model()->findByPk($this->post('type_id'));
            if (empty($data)) echo CHtml::tag('option', array('value' => ''), CHtml::encode('没有数值'), true);
            echo CHtml::tag('option', array('value' => ''), '数值', true);
            $data = explode(',',$data['count']);
            foreach ($data as $key => $val) {
                echo CHtml::tag('option', array('value' => $val), CHtml::encode($val), true);
            }
        } else {
            $data = TimetableType::model()->findByPk(1);
            if (empty($data)) echo CHtml::tag('option', array('value' => ''), CHtml::encode('没有数值'), true);
            echo CHtml::tag('option', array('value' => ''), '数值', true);
            $data = explode(',',$data['count']);
            foreach ($data as $key => $val) {
                echo CHtml::tag('option', array('value' => $val), CHtml::encode($val), true);
            }
        }

    }
    public function actionGetTypeValueAjax()
    {
        $type_id = $this->get('type_id');
        $type_value = TimetableType::model()->find(array(
            'select' => 'count',
            'condition' => 'type_id=:type_id',
            'params' => array(':type_id' => $type_id),
        ));
        $res = [];
        if (empty($type_value)) {
            $res = array('status' => 0);
        } else {
            $res = array(
                'status' => 1,
                'counts' => $type_value['count']
            );
        }
        echo CJSON::encode($res);
    }

}