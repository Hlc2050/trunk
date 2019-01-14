<?php

/**
 * This is the model class for table "t_admin_user".
 *
 * The followings are the available columns in table 't_admin_user':
 * @property integer $id
 * @property string $user
 * @property string $password
 */
class AdminUser extends CActiveRecord
{
	public function tableName() {
		return '{{cservice}}';
	}
    public static function model($className=__CLASS__)
   	{
   		return parent::model($className);
   	}
   	public static function password($upass){
   		return md5(md5(md5($upass)));
   	}
   	
   	public static function get_user_role($sno){
   		$sql="select  a.*,b.* from cservice_roles as a left join cservice_role as b on b.role_id=a.role_id where a.sno='$sno' ";
   		$data=Yii::app()->db->createCommand($sql)->queryAll();
   		return $data;
   	}

	public static function get_user_group($sno){
		$sql="select  a.*,b.* from cservice_groups as a left join cservice_group as b on b.groupid=a.groupid where a.sno='$sno' ";
		$data=Yii::app()->db->createCommand($sql)->queryAll();
		return $data;
	}
   	/** @params int $id 用户id
   	 *  @params array $roles 角色id的数组 
   	 * */
   	public function save_user_roles($id,$roles=array()){	
   		if(!is_array($roles)) return false;	
   		$arr001=Dtable::model('cservice_roles')->findAll("sno=$id");
   		$idarr=array();
   		foreach($arr001 as $r){
   			$idarr[]=$r['role_id'];
   		}   		 		
   		foreach($idarr as $idw){  //遍历 清除不存在的 数据
   			if(!in_array($idw,$roles)){ //老的数组 的信息ID 是否 在新的数组上
   				$mg=Dtable::model('cservice_roles')->findByAttributes(array('role_id'=>$idw,'sno'=>$id));
   				if(!$mg) continue;
   				$mg->delete(); 
   			}
   		}	
   		foreach($roles as $r){
   			$post=Dtable::model('cservice_roles')->findByAttributes(array('sno'=>$id,'role_id'=>$r));
   			if(!$post){
   				$post=new Dtable('cservice_roles');
   				$post->sno=$id;
   				$post->role_id=$r;
   				$post->save();
   			}
   		}	
   		return true;
   	}

	public function get_users(){
		$a=Dtable::toArr($this->findAll());
		return $a;
	}


	public function getAuths($csno){
		if(!$csno) return ;
		$user=AdminUser::model()->findByPk($csno);
		$u_private_role=AdminUser::get_user_role($user->csno); //查询私有的角色
		$uroledata=AdminGroupRole::get_group_role($user->groupid); //查询该组的角色
		$my_role=array(); //我的所有角色
		foreach($u_private_role as $r){
			$my_role[$r['role_id']]=$r;
		}
		foreach($uroledata as $r){
			$my_role[$r['role_id']]=$r;
		}

		$uroles=AdminGroupRole::role_arr($my_role);
		return $uroles;
	}

	public function saveUserAuth($csno,$role_leve_arr1,$role_leve_arr2){
		//先处理菜单
		$sql="select * from cservice_role_authority where role_id='".$csno."' and type=1 ";
		$arr001=Yii::app()->db->createCommand($sql)->queryAll();
		$idarr=array();
		foreach($arr001 as $r){
			$idarr[]=$r['authority_id'];
		}

		foreach($idarr as $idw){  //遍历 清除不存在的 数据
			if(!in_array($idw,$role_leve_arr1)){ //老的数组 的信息ID 是否 在新的数组上
				$sql="delete from cservice_service_authority where  csno='$csno' and authority_id='".$idw."' and type=1 ";
				Yii::app()->db->createCommand($sql)->execute();
			}
		}

		foreach($role_leve_arr1 as $r){
			$post=Dtable::model('cservice_service_authority')->findByAttributes(array('csno'=>$csno,'authority_id'=>$r,'type'=>1));
			if(!$post){
				$post=new Dtable('cservice_service_authority');
				$post->csno=$csno;
				$post->authority_id=$r;
				$post->type=1;
				$post->created=time();
				$post->save();
			}
		}
		//处理动作
		$sql="select * from cservice_service_authority where csno='".$csno."' and type=2 ";
		$arr001=Yii::app()->db->createCommand($sql)->queryAll();
		$idarr=array();
		foreach($arr001 as $r){
			$idarr[]=$r['authority_id'];
		}

		foreach($idarr as $idw){  //遍历 清除不存在的 数据
			if(!in_array($idw,$role_leve_arr2)){ //老的数组 的信息ID 是否 在新的数组上
				$sql="delete from cservice_service_authority  where  csno='$csno' and authority_id='".$idw."' and type=2 ";
				Yii::app()->db->createCommand($sql)->execute();
			}
		}


		//处理模块
		$sql="select * from cservice_service_authority where csno='".$csno."' and type=1 ";
		$arr001=Yii::app()->db->createCommand($sql)->queryAll();
		$idarr=array();
		foreach($arr001 as $r){
			$idarr[]=$r['authority_id'];
		}

		foreach($idarr as $idw){  //遍历 清除不存在的 数据
			if(!in_array($idw,$role_leve_arr1)){ //老的数组 的信息ID 是否 在新的数组上
				$sql="delete from cservice_service_authority  where  csno='$csno' and authority_id='".$idw."' and type=1 ";
				Yii::app()->db->createCommand($sql)->execute();
			}
		}

		foreach($role_leve_arr2 as $r){
			$post=Dtable::model('cservice_service_authority')->findByAttributes(array('csno'=>$csno,'authority_id'=>$r,'type'=>2));
			if(!$post){
				$post=new Dtable('cservice_service_authority');
				$post->csno=$csno;
				$post->authority_id=$r;
				$post->type=2;
				$post->created=time();
				$post->save();
			}
		}
	}


	public function getPrivateAuth($csno){
		if(!$csno) return ;
		$sql="select * from cservice_service_authority where csno=$csno";
		$u_private_auth=Yii::app()->db->createCommand($sql)->queryAll();
		$re=array();
		foreach($u_private_auth as $r2){
			if($r2['type']==1){
				$m=AdminModules::model()->findByPk($r2['authority_id']);
				if(!$m) continue;
				$a['authority_name']=$m->mname;
				$a['authority_id']=$m->id;
				$a['param_type']=1;
				$a['param_name']='';
				$a['param_value']='';
			}else if($r2['type']==2){
				$m=AdminFunction::model()->findByPk($r2['authority_id']);
				if(!$m) continue;
				$a['authority_name']=$m->authority_id;
				$a['authority_id']=$m->id;
				$a['param_type']=2;
				$a['param_name']=$m->param_name;
				$a['param_value']=$m->param_value;
			}

			$re[]=$a;
		}
		return $re;
	}

	/*
	 * 获取用户真实名字
	 */
	public function getUserNameByPK($pk){
		$data =$this->findByPk($pk);
		return $data->csname_true;
	}

	/**
	 * 通过部门负责人获取部门id
	 * @param $mamager
	 * author: yjh
	 */
	public function get_manager_group($manager_id){
		$mgroups = AdminGroup::model()->findAll("manager_id=$manager_id");
		$groupidArr = array();
		foreach ($mgroups as $r){
			$groupidArr[]=$r['groupid'];
		}
		return $groupidArr;
	}

	/**
	 * 获取所有用户（下拉框用）
	 * @param int $type
	 * @return array|mixed|null
	 * author: yjh
	 */
	public function get_all_user($type=0){
		if ($type == 1) {
			$data = $this->findAll();
		} else {
			//查看人员权限
			$adminController = new AdminController(0);
			$result = $adminController->data_authority();
			if ($result != 0) {
				$data = $this->findAll(" csno in ($result)");
			} else  $data = $this->findAll();

		}
		return $data;
	}


}