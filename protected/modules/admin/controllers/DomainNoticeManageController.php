<?php

/**
 * 域名替换微信通知管理
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/1
 * Time: 10:13
 */
class DomainNoticeManageController extends AdminController
{

    public function actionIndex()
    {
        //搜索
        $params['where']='';
        if($this->get('search_type')=='keys' && $this->get('search_txt')){
            $params['where'] =" and(name like '%".$this->get('search_txt')."%') ";
        }else if($this->get('search_type')=='id'  && $this->get('search_txt')){
            $params['where'] =" and(id=".intval($this->get('search_txt')).") ";
        }
        $params['order']="  order by id desc    ";
        $params['pagesize']=Yii::app()->params['management']['pagesize'];
        $params['pagebar']=1;
        $params['smart_order']=1;
        $page['listdata']=Dtable::model(OpenidManage::model()->tableName())->listdata($params);
        $this->render('index',array('page'=>$page));
    }

    /**
     * 添加通知人员及表单处理
     * author: yjh
     */
    public function actionAdd()
    {
        $page = array();
        //显示表单
        if (!$_POST) {
            $this->render('update',array('page'=>$page));
            exit;
        }
        //表单验证
        $info = new OpenidManage();
        $info->name = $this->post('name');
        $info->openid = $this->post('openid');
        $info->system_user = $this->post('system_user');

        if($info->name=='')$this->msg(array('state'=>0,'msgwords'=>'姓名不能为空'));
        if($info->openid=='')$this->msg(array('state'=>0,'msgwords'=>'openid不能为空'));

        $info->update_time = time();
        $info->create_time = time();

        $dbresult=$info->save();
        $id=$info->primaryKey;
        $msgarr=array('state'=>1,'url'=>$this->createUrl('domainNoticeManage/index').'?&p='.$_GET['p'].''); //保存的话，跳转到之前的列表
        $logs="添加了新的微信通知人员：".$info->name;
        if($dbresult===false){
            $this->msg(array('state'=>0));//错误返回
        }else{
            //新增和修改之后的动作
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 修改通知人员及表单处理
     * author: yjh
     */
    public function actionEdit(){
        $page = array();
        $id = $this->get('id');
        $info=OpenidManage::model()->findByPk($id);
        if(!$info){
            $this->msg(array('state'=>0,'msgwords'=>'数据不存在'));
        }
        //显示表单
        if (!$_POST) {
            //如果有get.id为修改，否则判断为新增;
            $page['info']=$this->toArr($info);
            $this->render('update',array('page'=>$page));
            exit;
        }
        //表单验证
        $info->name = $this->post('name');
        $info->openid = $this->post('openid');
        $info->system_user = $this->post('system_user');
        if($info->name=='')$this->msg(array('state'=>0,'msgwords'=>'姓名不能为空'));
        if($info->openid=='')$this->msg(array('state'=>0,'msgwords'=>'openid不能为空'));

        $info->update_time = time();
        $dbresult=$info->save();
        $msgarr=array('state'=>1,'url'=>$this->createUrl('domainNoticeManage/index').'?&p='.$_GET['p'].''); //保存的话，跳转到之前的列表
        $logs="修改了微信通知人员：".$info->name;
        if($dbresult===false){
            $this->msg(array('state'=>0));  //错误返回
        }else{
            //新增和修改之后的动作
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 删除通知人员
     * author: yjh
     */
    public function actionDelete(){
        if($this->get('id') =='')  $this->msg(array('state' => 0, 'msgwords' => '未传入数据'));
        $id = $this->get('id');
        $info = OpenidManage::model()->findByPk($id);
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }

        $info->delete();
        $this->logs("删除了通知人员：".$info->name);
        $this->msg(array('state' => 1, 'msgwords' => '删除通知人员【'.$info->name.'】成功！'));
    }


}