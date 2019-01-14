<?php

/**
 * 用户组别控制器
 * Class AdminGroupController
 */
class AdminGroupController extends  AdminController{

	public function actionIndex(){

		$categorys=array();
		$catearr=AdminGroup::model()->getGroup();

		$page['idarr']=array();
		foreach($catearr as $r){
			$new_r=$r;
			$new_r['id']=$r['groupid'];
			$new_r['parentid']=$r['parent_id'];
			$new_r['groupname']=$r['groupname'];
			$new_r['str_manage'] ='
					<a href="'.$this->createUrl('adminGroup/update',array('parent_id'=>$r['groupid'])).'">添加子部门</a>
					<a href="'.$this->createUrl('adminGroup/update',array('id'=>$r['groupid'])).'">修改</a>
				    <a href="'.$this->createUrl('adminGroup/delete',array('id'=>$r['groupid'])).'" onclick="return confirm(\'确定删除吗\')">删除</a>';

			$categorys[]=$new_r;
		}
		$str  = "<tr>
						<td>\$id</td>
						<td class='alignleft'>\$spacer\$groupname</td>
						<td>\$csname \$csname_true</td>
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

	public function actionUpdate(){
		$page=array();
		$id=$_GET['id']=isset($_GET['id'])?intval($_GET['id']):0;
		//显示表单
		if(!isset($_POST['id'])){
			//如果有get.id为修改，否则判断为新增;
			if($id){
				if($id==Yii::app()->params['management']['super_group_id']){
					$this->msg(array('state'=>0,'msgwords'=>'超级管理组无法修改'));
				}
				$sql="select a.* from cservice_group as a   where a.groupid=".$id." ";//die($sql);
				$info=$this->query($sql);
				if(count($info)==0){
					$this->msg(array('state'=>0,'msgwords'=>'管理组不存在'));
				}
				$page['info']=$info[0];
				$parent_id=$page['info']['parent_id'];
				//print_r($page['admin_group']);
				$page['group_roles']=AdminGroupRole::get_group_role($id);
			}else{
				$parent_id=$this->get('parent_id');
				$page['group_roles']=array();
			}
			$sql="select * from cservice_group_role as a left join cservice_role as b on b.role_id=a.role_id where a.groupid=".$id;
			$page['myroles']=$this->query($sql);
			$roles=AdminRole::get_role();
			$page['roles']=array();
			foreach($roles as $r){
				$r['checked']=0;
				foreach($page['group_roles'] as $r2){
					if($r2['role_id']==$r['role_id']){
						$r['checked']=1;
						break;
					}
				}
				$page['roles'][]=$r;
			}

			$page['categorys']=AdminGroup::model()->category_tree($parent_id);

		}else{//判断为保存
			$id=$_POST['id']=intval($_POST['id']);
			//处理需要的字段
			$field=array();
			if($id==Yii::app()->params['management']['super_group_id']){
				$this->msg(array('state'=>0,'msgwords'=>'超级管理组无法修改'));
			}
			$field['groupname']=isset($_POST['groupname'])?$_POST['groupname']:0;
			$field['parent_id']=$this->post('parent_id');
			$field['manager_id']=$this->post('manager_id');
			if($field['groupname']==''){
				$this->msg(array('state'=>0,'msgwords'=>'组名称不能为空'));
			}

			//如果有post.id 为保存修改，否则为保存新增
			if($id){
				$result = AdminGroup::model()->count('groupname=:groupname and groupid!=:groupid', array(':groupname' =>$field['groupname'],':groupid'=>$id));
				if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '此部门已存在，请重新输入！'));
				if($field['parent_id']==$id){
					$this->msg(array('state'=>0,'msgwords'=>'上级部门不能为自己'));
				}
				$dbresult=AdminGroup::model()->updateAll($field,"groupid=$id");  //修改记录
				$msgarr=array('state'=>1,'url'=>$this->createUrl('adminGroup/index').'?p='.$_GET['p'].''); //保存的话，跳转到之前的列表
				$logs='修改了管理组 ID:'.$id.''.$field['groupname'].' ';
			}else{
				$result = AdminGroup::model()->count('groupname=:groupname', array(':groupname' =>$field['groupname']));
				if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '此部门已存在，请重新输入！'));
				$post=$this->data('AdminGroup',$field);
				$dbresult=$post->save();
				$id=$post->primaryKey;
				$msgarr=array('state'=>1);  //新增的话跳转会添加的页面
				$logs="添加了管理组ID：$id".$field['groupname'];
			}
			if($dbresult===false){
				//错误返回
				$this->msg(array('state'=>0));
			}else{
				//新增和修改之后的动作
				$roles=isset($_POST['roles'])&&is_array($_POST['roles'])?$_POST['roles']:array();
				$sql="select * from cservice_group_role where groupid='".$id."'";
				$arr001=$this->query($sql);
				$idarr=array();
				foreach($arr001 as $r){
					$idarr[]=$r['role_id'];
				}

				foreach($idarr as $idw){  //遍历 清除不存在的 数据
					if(!in_array($idw,$roles)){ //老的数组 的信息ID 是否 在新的数组上
						$sql="delete from cservice_group_role where  role_id='$idw' and groupid='".$id."' ";
						Yii::app()->db->createCommand($sql)->execute();
					}
				}

				foreach($roles as $r){
					$post=AdminGroupRole::model()->findByAttributes(array('groupid'=>$id,'role_id'=>$r));
					if(!$post){
						$post=new AdminGroupRole();
						$post->groupid=$id;
						$post->role_id=$r;
						$post->save();
					}
				}



				$this->logs($logs);
				//成功跳转提示
				$this->msg($msgarr);
			}

		}
		$this->render('update',array('page'=>$page));
	}
	public function actionDelete(){
		$id=isset($_GET['id'])&&$_GET['id']!=''?intval($_GET['id']):0;
		if($id==Yii::app()->params['management']['super_group_id']){
			$this->msg(array('state'=>0,'msgwords'=>'超级管理组无法删除'));
		}
		$admin_group=AdminGroup::model()->findByPk($id);
		$admin_group->delete();
		$this->msg(array('state'=>1));
	}

}
?>