<?php

/**
 * 业务类型管理
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/12/23
 * Time: 17:53
 */
class BusinessTypesManageController extends AdminController
{
    /**
     * 业务类型列表
     * author: yjh
     */
    public function actionIndex()
    {
        //搜索
        $params['join'] = "LEFT JOIN cservice as c ON a.sno=c.csno";
        $params['order'] = "  order by a.bid desc    ";
        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $params['select']="*,c.csname_true";
        $page['listdata'] = Dtable::model(BusinessTypes::model()->tableName())->listdata($params);
        foreach ($page['listdata']['list'] as $key => $val) {
            $chargingTypeArrTemp = Dtable::toArr(BusinessTypeRelation::model()->findAll('bid=:bid', array(':bid' => $val['bid'])));
            $chargingTypeArr=array();
            foreach ($chargingTypeArrTemp as $k => $v ){
                $chargingTypeArr[]=vars::get_field_str('charging_type', $v['charging_type']);
            }
            $page['listdata']['list'][$key]['chargingTypes'] = implode(',', $chargingTypeArr);//微信号
            //$page['listdata']['list'][$key]['csname_true'] = AdminUser::model()->findByPk($val['sno'])->csname_true;//微信号
        }
        $this->render('index', array('page' => $page));
    }

    /**
     * 新增业务类型
     * author: yjh
     */
    public function actionAdd()
    {

        $page = array();
        if (!$_POST) {
            $this->render('update', array('page' => $page));
            exit;
        }
        $info = new BusinessTypes();
        $info->bname = $this->post('bname');
        $result = BusinessTypes::model()->count('bname=:bname', array(':bname' => $info->bname));
        if($info->bname == '') $this->msg(array('state' => 0, 'msgwords' => '业务类型不能为空！'));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '此业务类型已存在，请重新输入！'));
        if(!$this->post('chargingTypes')) $this->msg(array('state' => 0, 'msgwords' => '没有选计费方式！'));
        $info->sno=Yii::app()->admin_user->id;
        $info->update_time=time();
        $info->create_time=time();
        $dbresult = $info->save();
        $id = $info->primaryKey;
        foreach ($this->post('chargingTypes') as $val){
            $rInfo = new BusinessTypeRelation();
            $rInfo->bid=$id;
            $rInfo->charging_type=$val;
            $rInfo->create_time=time();
            $rInfo->save();
        }
        $msgarr = array('state' => 1, 'url' => $this->createUrl('businessTypesManage/index') . '?p=' . $_GET['p'] . ''); //保存的话，跳转到之前的列表
        $logs = "新增了新的业务类型：" . $info->bname;
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
     * 修改业务类型
     * author: yjh
     */
    public function actionEdit()
    {
        $page = array();
        $id = $this->get('bid');
        $info = BusinessTypes::model()->findByPk($id);
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }

        if (!$_POST) {
            $page['info'] = $this->toArr($info);
            $page['info']['chargingTypeArr'] = BusinessTypeRelation::model()->getChargingTypes($id);//explode(',', $page['info']['wechat_ids']);

            $this->render('update', array('page' => $page));
            exit;
        }
        //表单验证
        $info->bname = $this->post('bname');
        $result = BusinessTypes::model()->count('bname=:bname and bid!=:bid', array(':bname' => $info->bname,':bid'=>$id));

        if($info->bname == '') $this->msg(array('state' => 0, 'msgwords' => '业务类型不能为空！'));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '此业务类型已存在，请重新输入！'));
        if(!$this->post('chargingTypes')) $this->msg(array('state' => 0, 'msgwords' => '没有选计费方式！'));
        $info->sno=Yii::app()->admin_user->id;
        $info->update_time=time();
        $dbresult = $info->save();
        $id = $info->primaryKey;
        BusinessTypeRelation::model()->deleteAll('bid=:bid',array(':bid'=>$id));

        foreach ($this->post('chargingTypes') as $val){
            $rInfo = new BusinessTypeRelation();
            $rInfo->bid=$id;
            $rInfo->charging_type=$val;
            $rInfo->create_time=time();
            $rInfo->save();
        }
        $msgarr = array('state' => 1, 'url' => $this->createUrl('businessTypesManage/index') . '?p=' . $_GET['p'] . ''); //保存的话，跳转到之前的列表
        $logs = "修改了新的业务类型：" . $info->bname;
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
     * 删除业务类型
     * author: yjh
     */
    public function actionDelete()
    {
        if ($this->get('bid') == '') $this->msg(array('state' => 0, 'msgwords' => '未传入数据'));
        $id = $this->get('bid');
        $info = BusinessTypes::model()->findByPk($id);
        if (!$info) $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        BusinessTypeRelation::model()->deleteAll('bid=:bid',array(':bid'=>$id));
        $info->delete();
        $this->msg(array('state' => 1, 'msgwords' => '删除业务类型【' . $info->bname . '】成功！'));


    }


}