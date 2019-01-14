<?php

/**
 * 用户动作权限管理器
 * Class AdminFunctionController
 */
class AdminFunctionController extends AdminController{
	public $module_id=0;
	public $module;
	public function init(){
		parent::init();
		$this->module_id=intval($this->get('module_id'));
		$m=AdminModules::model()->findByPk($this->module_id);
		if(!$m){
			$this->msg(array('state'=>-1,'msgwords'=>'请选择模块页面'));
		}
		$this->module=$m;
	}
	
	public function actionIndex(){
		//搜索
		$params['where']='';

		$params['where'] .=" and(a.module_id=".$this->module_id.") ";
		
		$params['order']="  order by a.displayorder,a.id asc      ";
		$params['pagesize']=Yii::app()->params['management']['pagesize'];
		$params['pagebar']=1;
		$params['smart_order']=1;
		$page['listdata']=Dtable::model(AdminFunction::model()->tableName())->listdata($params);
		$this->render('index',array('page'=>$page));
		
	}	
	

	public function actionAdd(){
		$page=array();
		$module_id=$this->module_id;
		//显示表单
		if(!$_POST){
			$this->render('update',array('page'=>$page));exit;
		}
		$info=new AdminFunction();
		$info->function_name=$this->post('function_name');
		if($info->function_name==''){
			$this->msg(array('state'=>0,'msgwords'=>'名称不能为空'));
		}
		$info->displayorder=intval($this->post('displayorder'));
		$info->module_id=$module_id;
		$info->authority_id=$this->get('authority_id');
		$info->param_type=intval($this->get('param_type'));
		$info->param_name=$this->get('param_name');
		$info->param_value=$this->get('param_value');
		$dbresult=$info->save();
		$id=$info->primaryKey;
		$msgarr=array('state'=>1);  //新增的话跳转会添加的页面
		$logs="添加了动作权限ID：$dbresult".$info->function_name;
		if($dbresult===false){
			//错误返回
			$this->msg(array('state'=>0));
		}else{
			//新增和修改之后的动作

			$this->logs($logs);
			//成功跳转提示
			$this->msg($msgarr);
		}

	}
	public function actionEdit(){ 
		$page=array();
		$module_id=$this->module_id;
		$id=$this->get('id');
		$info=AdminFunction::model()->findByPk($id);
		if(!$info){
			$this->msg(array('state'=>0,'msgwords'=>'不存在'));
		}
		//显示表单
		if(!$_POST){
			$info=$this->toArr($info);
			$page['info']=$info;
			$this->render('update',array('page'=>$page));exit;
		}
		$id=$_POST['id']=intval($_POST['id']);
		//处理需要的字段
		$field=array();
		$info->function_name=$this->post('function_name');
		if($info->function_name==''){
			$this->msg(array('state'=>0,'msgwords'=>'名称不能为空'));
		}
		$info->displayorder=intval($this->post('displayorder'));
		$info->module_id=$module_id;
		$info->authority_id=$this->get('authority_id');
		$info->param_type=intval($this->get('param_type'));
		$info->param_name=$this->get('param_name');
		$info->param_value=$this->get('param_value');
		$dbresult=$info->save();
		$msgarr=array('state'=>1,'url'=>$this->createUrl('adminFunction/index').'?module_id='.$module_id.'&p='.$_GET['p'].''); //保存的话，跳转到之前的列表
		$logs='修改了动作权限 ID:'.$id.''.$info->function_name.' ';
		if($dbresult===false){
			//错误返回
			$this->msg(array('state'=>0));
		}else{
			//新增和修改之后的动作

			$this->logs($logs);
			//成功跳转提示
			$this->msg($msgarr);
		}
	
		
	}
	public function actionDelete(){
		$idstr=$this->get('ids');
		$ids=explode(',',$idstr);
		foreach($ids as $id){	
			$m=AdminFunction::model()->findByPk($id);
			if(!$m) continue;
			$m->delete();
			
		}
		$this->logs('删除了动作权限ID（'.$idstr.'）');
		$this->msg(array('state'=>1));	
	}
	public function actionSaveOrder(){
		$listorders=$this->get('listorders',array());
		foreach($listorders as $id=>$order){
			$m=AdminFunction::model()->findByPk($id);
			if(!$m) continue;
			$m->displayorder=$order;
			$m->save();		
		}
		$this->logs('修改了动作权限的排序');
		$this->msg(array('state'=>1));
	}

}
?>