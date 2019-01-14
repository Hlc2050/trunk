<?php
class ParamsGroupController extends AdminController{

	public function actionIndex(){

		//搜索
		$params['where']='';
		if($this->get('search_type')=='keys' && $this->get('search_txt')){
			$params['where'] .=" and(a.param_name like '%".$this->get('search_txt')."%') ";
		}else if($this->get('search_type')=='id'  && $this->get('search_txt')){ //网点ID
			$params['where'] .=" and(a.id=".intval($this->get('search_txt')).") ";
		}
		$params['order']="  order by a.displayorder,a.id      ";
		$params['pagesize']=Yii::app()->params['management']['pagesize'];
		$params['pagebar']=1;
		$params['smart_order']=1;
		$page['listdata']=Dtable::model(ParamsGroup::model()->tableName())->listdata($params);
		$this->render('index',array('page'=>$page));

	}

	public function actionAdd(){
		$page=array();
		//显示表单
		if(!$_POST){

			$this->render('update',array('page'=>$page));exit;

		}

		$info=new ParamsGroup();
		$info->group_name=$this->post('group_name');
		if($info->group_name==''){
			$this->msg(array('state'=>0,'msgwords'=>'参数分组名称不能为空'));
		}
		$info->group_param_name=$this->post('group_param_name');
		$info->isshow=intval($this->post('isshow'));
		$info->displayorder=intval($this->post('displayorder'));
		$dbresult=$info->save();
		$id=$info->primaryKey;
		$msgarr=array('state'=>1);  //新增的话跳转会添加的页面
		$logs="添加了参数分组ID：$dbresult".$info->group_param_name;
		if($dbresult===false){
			//错误返回
			$this->msg(array('state'=>0));
		}else{
			//新增和修改之后的动作
			$this->logs($logs);
			//成功跳转提示
			$this->msg($msgarr);
		}
		$this->render('update',array('page'=>$page));
	}

	public function actionEdit(){
		$page=array();
		$id=$this->get('id',0);
		$info=ParamsGroup::model()->findByPk($id);
		if(!$info){
			$this->msg(array('state'=>0,'msgwords'=>'数据不存在'));
		}
		//显示表单
		if(!$_POST){
			$page['info']=$this->toArr($info);
			$this->render('update',array('page'=>$page));exit;
		}


		$info->group_name=$this->post('group_name');
		if($info->group_name==''){
			$this->msg(array('state'=>0,'msgwords'=>'参数分组名称不能为空'));
		}
		$info->group_param_name=$this->post('group_param_name');
		$info->isshow=intval($this->post('isshow'));
		$info->displayorder=intval($this->post('displayorder'));
		$dbresult=$info->save();
		//如果有post.id 为保存修改，否则为保存新增
		$msgarr=array('state'=>1,); //保存的话，跳转到之前的列表
		$logs='修改了参数分组 ID:'.$id.''.$info->group_param_name.' ';
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
			$m=ParamsGroup::model()->findByPk($id);
			if(!$m) continue;
			$m->delete();

		}
		$this->logs('删除了参数分组ID（'.$idstr.'）');
		$this->msg(array('state'=>1));
	}
	public function ActionSaveOrder(){

		$listorders=$this->get('listorders',array());
		foreach($listorders as $id=>$order){
			$m=ParamsGroup::model()->findByPk($id);
			if(!$m) continue;
			$m->displayorder=$order;
			$m->save();
		}
		$this->logs('修改了参数分组的排序');
		$this->msg(array('state'=>1));
	}

}
?>