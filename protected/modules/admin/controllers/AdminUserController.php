<?php
class AdminUserController extends AdminController{

	public function actionIndex(){
			
		$params['where']='';
		if($this->get('search_type')=='keys' && $this->get('search_txt')){
			$params['where'] =" and(a.csname like '%".$this->get('search_txt')."%') ";
		}else if($this->get('search_type')=='id'  && $this->get('search_txt')){ //网点ID
			$params['where'] =" and(a.csno=".intval($this->get('search_txt')).") ";
		}
		$params['order']="  order by a.csno    ";
		$params['pagesize']=Yii::app()->params['management']['pagesize'];
		$params['select']="a.*,b.groupname";
		$params['join']="left join cservice_group as b on b.groupid=a.groupid ";
		$params['pagebar']=1;
		$params['smart_order']=1;
		$page['listdata']=Dtable::model('cservice')->listdata($params);
		$this->render('index',array('page'=>$page));
	}	
	public function actionUpdate(){
		$page=array();
		$id=$this->get('id',0);
		//显示表单
		if(!isset($_POST['id'])){
			//如果有get.id为修改，否则判断为新增;
			if($id){
				$info=$this->toArr(AdminUser::model()->findByPk($id));
				if(count($info)==0){
					$this->msg(array('state'=>0,'msgwords'=>'不存在'));
				}
				$page['info']=$info;
				$page['admin_roles']=AdminUser::get_user_role($id);
				$page['admin_groups']=AdminUser::model()->get_user_group($id);
			}else{
				$page['admin_roles']=array();
				$page['admin_groups']=array();
			}
			$roles=AdminRole::get_role();
			$page['roles']=array();
			foreach($roles as $r){
				$r['checked']=0;
				foreach($page['admin_roles'] as $r2){
					if($r2['role_id']==$r['role_id']){
						$r['checked']=1;
						break;
					}
				}
				$page['roles'][]=$r;
			}
			$groups=AdminGroup::model()->getGroup();
			$page['groups']=array();
			foreach($groups as $r){
				$r['checked']=0;
				foreach($page['admin_groups'] as $r2){
					if($r2['groupid']==$r['groupid']){
						$r['checked']=1;
						break;
					}
				}
				$page['groups'][]=$r;
			}

			foreach($page['groups'] as $r){
				$new_r=$r;
				$new_r['id']=$r['groupid'];
				$new_r['parentid']=$r['parent_id'];
				$new_r['groupname']=$r['groupname'];
                $new_r['checked']=$r['checked']?'checked':'';

				$categorys[]=$new_r;
			}
			$str  = "<div>
						<div> \$spacer<label><input \$checked type=checkbox  name='groups[]' value='\$groupid' ></label>\$groupname (部门负责人:\$csname \$csname_true)</div>
					</div>";
			$tree=new tree();
			$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
			$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
			$tree->init($categorys);
			$category_code = $tree->get_tree(0, $str);

			$page['groups_tree']=$category_code;

            $page['userAuths']=AdminUser::model()->getAuths($id);

            $page['userPrivateAuths']=AdminUser::model()->getPrivateAuth($id);

            //print_r($page['userPrivateAuths']);//die();

	
		}else{//判断为保存
			$id=$_POST['id']=intval($_POST['id']);
			//处理需要的字段
			$field=array();
			if($_POST['id']==Yii::app()->params['management']['super_admin_id']){
				$this->msg(array('state'=>0,'msgwords'=>'此为网站管理帐号不能更改'));
			}
			$field['csname']=$this->post('csname');
			$cspwd=isset($_POST['cspwd'])?($_POST['cspwd']):'';
			if(verify::check_username($field['csname'])==0){
				$this->msg(array('state'=>0,'msgwords'=>'管理员帐号不合法'));
			}
			if($cspwd!=''){
				if(verify::check_password($cspwd)==0){
					$this->msg(array('state'=>0,'msgwords'=>'密码要在6-12位'));
				}
				$field['cspwd']=AdminUser::password($cspwd);
			}
			if(!$_POST['csname_true'])
				$this->msg(array('state'=>0,'msgwords'=>'真实姓名未填！'));
			$field['csname_true']=$_POST['csname_true'];
			$k001=$this->query(" select count(1) as total from cservice where csname='".$field['csname']."' and csno!=$id ");
			if($k001[0]['total']>0){
				$this->msg(array('state'=>0,'msgwords'=>'该帐号已存在！'));
			}
			$field['groupid']=$this->post('groupid',0);
			$field['csemail']=$this->post('csemail');
			$field['csmobile']=$this->post('csmobile');
			//如果有post.id 为保存修改，否则为保存新增
			if($id){	
				$dbresult=AdminUser::model()->updateAll($field,"csno=$id");  //修改记录
				$msgarr=array('state'=>1,'url'=>$this->createUrl('adminUser/index').'?p='.$_GET['p'].''); //保存的话，跳转到之前的列表
				$logs='修改了系统用户 ID:'.$id.chr(10).$field['csname'].' ';
			}else{
				if(verify::check_password($cspwd)==0){
					$this->msg(array('state'=>0,'msgwords'=>'密码要在6-12位'));
				}
				$post=$this->data('AdminUser',$field);
				$dbresult=$post->save();
				$id=$post->primaryKey;
				$msgarr=array('state'=>1);  //新增的话跳转会添加的页面
				$logs="添加了系统用户ID：$id".chr(10).$field['csname'];
			}
			if($dbresult===false){
				//错误返回
				$this->msg(array('state'=>0));
			}else{
				//新增和修改之后的动作
				$roles=$this->post('roles',array());
				AdminUser::model()->save_user_roles($id,$roles);
				$groups=$this->post('groups',array());
				AdminUserGroup::model()->save_groups($id,$groups);


                $role_leve_arr1=isset($_POST['role_levels1'])&&is_array($_POST['role_levels1'])?$_POST['role_levels1']:array(); //页面
                $role_leve_arr2=isset($_POST['role_levels2'])&&is_array($_POST['role_levels2'])?$_POST['role_levels2']:array(); //动作

				AdminUser::model()->saveUserAuth($id,$role_leve_arr1,$role_leve_arr2);


				$this->logs($logs);
				//成功跳转提示
				$this->msg($msgarr);
			}
	
		}
		$this->render('update',array('page'=>$page));
	}
	public function actionDelete(){
		$logs="删除了系统用户：";
		$ids=isset($_GET['ids'])&&$_GET['ids']!=''?$_GET['ids']:'';
		$ids=explode(',',$ids);
		foreach($ids as $id){	
			$id=intval($id);
			if($id==1) $this->msg(array('state'=>0,'msgwords'=>'不能删除admin用户'));
			$m=AdminUser::model()->findByPk($id);
			$name = $m->csname;
			$m->delete();
			$logs.=$id.chr(10).$name.",";
		}
		//die();
		$this->logs($logs);
		$this->msg(array('state'=>1));	
	}
	public function ActionSaveOrder(){
		foreach($_POST['listorders'] as $id=>$order){
			//AdminUser::model()->updateAll(array('norder'=>intval($order)),"csno=".intval($id)."");  //修改记录
		}
		$this->logs('修改了内链的排序');
		$this->msg(array('state'=>1));
	}

	public function ActionChangeState(){

		$idstr=$this->get('ids');
		$ids=explode(',',$idstr);
		$ustate=intval($this->get('ustate'));
		if($ustate == 1)$logs="冻结了系统用户：";
		else $logs="启用了系统用户：";

		foreach($ids as $id){
			$id=intval($id);
			if($id==1) $this->msg(array('state'=>0,'msgwords'=>'不能冻结admin用户'));
			$m=AdminUser::model()->findByPk($id);
			$name = $m->csname;
			$m->csstatus=$ustate;
			$m->save();
			$logs.=$id.chr(10).$name.",";
		}
		//die();
		$this->logs($logs);
		$this->msg(array('state'=>1));
	}

}
?>