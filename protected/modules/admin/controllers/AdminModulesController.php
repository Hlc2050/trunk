<?php

/**
 * 菜单控制器
 * Class AdminModulesController
 */
class AdminModulesController extends  AdminController{

	public function actionIndex(){
		 
		$categorys=array();
		$catearr=AdminModules::model()->menuCates;
		$page['idarr']=array();
		foreach($catearr as $r){
			$new_r=$r;
			$page['idarr'][]=$r['id'];
			$new_r['id']=$r['id'];
			$new_r['parentid']=$r['parent_id'];
			$new_r['cname']=$r['name'];
			$new_r['mname']=$r['mname'];
			$new_r['fonticon']=$r['fonticon'];
			$new_r['url']=$r['url'];
			$funs=AdminFunction::model()->getAllName($r['id']);
			$new_r['someName']=$funs?'('.helper::cut_str(implode(',',$funs),40).')':'';
			$new_r['corder']=$r['displayorder'];
			$new_r['str_manage'] ='
					<a onclick="return dialog_frame(this,900,500);" href="'.$this->createUrl('adminFunction/index',array('module_id'=>$r['id'])).'">功能动作管理</a>  
				    <a href="'.$this->createUrl('adminModules/edit',array('id'=>$r['id'])).'">修改</a>  
				    <a href="'.$this->createUrl('adminModules/delete',array('id'=>$r['id'])).'" onclick="return confirm(\'确定删除吗\')">删除</a>';
			$new_r['displays']=$r['display']?'<font color=red>√</font>':'<font color="#cccccc">×</font>';
			$categorys[]=$new_r;
		}
		$str  = "<tr>
						<td><input name='listorders[\$id]' type='text' size='3' value='\$corder' class='input-text-c'></td>
						<td>\$id</td>
						<td class='alignleft'>\$spacer <i class='fa fa-\$fonticon'></i> \$cname <span style='color:#999'>\$someName</span></td>
						<td >\$mname</td>
						<td >\$url</td>
						<td >\$displays</td>
						<td>\$str_manage</td>
					</tr>";
		$tree=new tree();
		$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		$tree->init($categorys);
		$category_code = $tree->get_tree(0, $str);
		$page['categorys']=$category_code;
		$this->render('index',array('page'=>$page));
	}

	public function actionAdd(){
		$page=array();
		$id=$this->get('id');
		//显示表单
		if(!$_POST){
			$page['categorys']=AdminModules::model()->category_tree(0);
			$this->render('update',array('page'=>$page));exit;
	
		}
		
		$info=new AdminModules();
		$info->name=$this->post('name');
		if($info->name==''){
			$this->msg(array('state'=>0,'msgwords'=>'名称不能为空'));
		}
		
		$info->parent_id=intval($this->post('parent_id'));
		
		if($info->parent_id==$id && $id>0){
			$this->msg(array('state'=>0,'msgwords'=>'不能设自己为父模块'));
		}
		
		$info->mname=$this->post('mname');
		$info->fonticon=$this->post('fonticon');
		$info->url=$this->post('url');
		$info->displayorder=intval($this->post('displayorder'));
		$info->display=intval($this->post('display'));
		$dbresult=$info->save();
		$id=$info->primaryKey;
		$msgarr=array('state'=>1);  //新增的话跳转会添加的页面
		$logs="添加了页面权限ID：$dbresult".$info->name;
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
		$id=$this->get('id');
		$info=AdminModules::model()->findByPk($id);
		if(!$info){
			$this->msg(array('state'=>0,'msgwords'=>'文档不存在'));
		}
		if(!$_POST){
			//如果有get.id为修改，否则判断为新增;
			$page['info']=$this->toArr($info);
			$sid=$info->parent_id;
			$page['categorys']=AdminModules::model()->category_tree($sid);
			$this->render('update',array('page'=>$page));exit;
		}
		$info->name=$this->post('name');
		if($info->name==''){
			$this->msg(array('state'=>0,'msgwords'=>'名称不能为空'));
		}
			
		$info->parent_id=intval($this->post('parent_id'));
			
		if($info->parent_id==$id && $id>0){
			$this->msg(array('state'=>0,'msgwords'=>'不能设自己为父模块'));
		}
			
		$info->mname=$this->post('mname');
		$info->fonticon=$this->post('fonticon');
		$info->url=$this->post('url');
		$info->displayorder=intval($this->post('displayorder'));
		$info->display=intval($this->post('display'));
		$dbresult=$info->save();
		$msgarr=array('state'=>1,'url'=>$this->createUrl('adminModules/index').'?p='.$_GET['p'].''); //保存的话，跳转到之前的列表
		$logs='修改了页面权限 ID:'.$id.''.$info->name.' ';
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
		$id=isset($_GET['id'])&&$_GET['id']!=''?intval($_GET['id']):0;
		if($id==Yii::app()->params['management']['super_group_id']){
			$this->msg(array('state'=>0,'msgwords'=>'超级管理组无法删除'));
		}
		$m=AdminModules::model()->findByPk($id);
		$m->delete();
		$this->msg(array('state'=>1));	
	}
	
	public function ActionSaveOrder(){
		$listorders=$this->get('listorders',array());
		foreach($listorders as $id=>$order){
			$m=AdminModules::model()->findByPk($id);
			if(!$m) continue;
			$m->displayorder=$order;
			$m->save();
		}
		$this->logs('修改了页面权限的排序');
		$this->msg(array('state'=>1));
	}
	public function ActionFont(){
		$page=array();
		$this->render('font',array('page'=>$page));
	}

}
?>