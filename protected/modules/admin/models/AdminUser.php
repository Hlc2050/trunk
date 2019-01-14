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
     * 通过部门负责人获取部门名字
     * @param $mamager
     * author: hlc
     */
    public function get_group_name($manager_id){
        $mgroups = AdminGroup::model()->findAll("manager_id=$manager_id");
        $groupidArr = array();
        foreach ($mgroups as $r){
            $groupidArr[]=$r['groupname'];
        }
        return $groupidArr;
    }

	/**
	 * 获取所有用户（下拉框用）
	 * @param int $type
	 * @param int $support 是否可查看支持人员关联数据
	 * @return array|mixed|null
	 * author: yjh
	 */
	public function get_all_user($type=0,$support=0){
		if ($type == 1) {
			$data = $this->findAll();
		} else {
			//查看人员权限
			$adminController = new AdminController(0);
			$result = $adminController->data_authority($support);
			if ($result != 0) {
				$data = $this->findAll(" csno in ($result)");
			} else  $data = $this->findAll();

		}
		return $data;
	}

    public function getUserNames($ids){
        $sql="select csno,csname_true from cservice where csno in($ids)";
        $result = Yii::app()->db->createCommand($sql)->queryAll();
        $ret = array();
        foreach ($result as $item) {
            $ret[$item['csno']]=$item['csname_true'];
        }
        return $ret;
    }

    /**
     * 用户是否为推广组长以上级别
     * @param $user_id int
     * @return boolean
     */
    public function user_high_permission($user_id)
    {
        $is_high_permission = 0;
        $ugroups = $this->get_user_group($user_id);
        foreach ($ugroups as $group) {
            if ($group['groupid'] == 1 || $group['groupid'] == 3) {
                $is_high_permission = 1;
                return $is_high_permission;
            }
        }
        $mgroups = $this->get_manager_group($user_id);
        foreach ($mgroups as $group) {
            if ($group['groupid'] == 1 || $group['groupid'] == 3) {
                $is_high_permission = 1;
                return $is_high_permission;
            }
        }
        return $is_high_permission;
    }

    /**
     * 用户是否为推广人员
     * @param $uid
     * @return bool
     */
    public function isUserPromotionStaff($uid)
    {
        $staff = PromotionStaff::model()->find('user_id='.$uid);
        if ($staff) {
            return true;
        }else {
            return false;
        }

    }

    /**
     * 通过用户判断权限
     * $authority 0：审核人兼组长  1:审核人  2.组长 3.组员 4.非推广人员
     * @param $mamager
     * author: hlc
     */
    public function getUserAuthority($user_id)
    {
        //获取个人计划评审人
        $personl_checker = Yii::app()->params['remind']['Individual_plan_auditor'];
        //获取组计划审核人
        $group_checker = Yii::app()->params['remind']['group_plan_auditor'];
        //获取该部门的负责人
        $mamager = AdminGroup::model()->findAll();
        $array = array();
        foreach ($mamager as $val){
            $array[] = $val['manager_id'];
        }

        $temp = array();
        $tg_data = AdminUser::model()->findAll();

        foreach ($tg_data as $val){
            $temp[] = $val['csno'];
        }
        //个人计划审核选择是到审核人
        if($personl_checker == 1){
            if(in_array($user_id,$array) && $user_id == $group_checker){
                $authority = 0;
            }elseif($user_id == $group_checker){
                $authority = 1;
            }elseif(in_array($user_id,$array)){
                $authority = 2;
            }elseif(in_array($user_id,$temp)){
                $authority = 3;
            }else{
                $authority = 4;
            }
            //个人计划审核的选择是只到组长
        }elseif($personl_checker == 0){
            if(in_array($user_id,$array) && $user_id == $group_checker){
                $authority = 0;
            }elseif(in_array($user_id,$array)){
                $authority = 2;
            }elseif(in_array($user_id,$temp)){
                $authority = 3;
            }else{
                $authority = 4;
            }
        }
        return  $authority;
    }

    /**
     * 通过用户判断权限
     * $authority 0：超级管理员  1:组长  2.组员
     * @param $mamager
     * author: hlc
     */
    public function getUserAu(){
        $user_id = Yii::app()->admin_user->uid;
        $mamager = AdminGroup::model()->findAll();
        $array = array();
        foreach ($mamager as $val){
            $array[] = $val['manager_id'];
        }

        $temp = array();
        $tg_data = PromotionStaff::model()->findAll();

        foreach ($tg_data as $val){
            $temp[] = $val['user_id'];
        }
        if (in_array($user_id,$array)){
            $authority = 1;
        }elseif(in_array($user_id,$temp)){
            $authority = 2;
        }else{
            $authority = 0;
        }

        return $authority;
    }

    /**
     * 组长数据
     * author: hlc
     */
    public function getPersonCheckData(){
        $uid = Yii::app()->admin_user->uid;
        $arr = '';
        $person = AdminGroup::model()->findAll('manager_id='.$uid);
        if($person){
            foreach ($person as $val){
                $arr .= $val['groupid'].',';
            }
            $arr = rtrim($arr,",");
            $data = AdminUserGroup::model()->getUsersByGroups($arr);
        }else{
            $ret = AdminGroup::model()->findAll();
            foreach ($ret as $val){
                $arr .= $val['groupid'].',';
            }
            $arr = rtrim($arr,",");
            $data = AdminUserGroup::model()->getUsersByGroups($arr);
        }

        return $data;
    }

    /**
     *审核人组数据
     * author: hlc
     */
    public function getGroupCheckData(){
        $arr = array();
        $group = AdminGroup::model()->findAll();

        foreach ($group as $val){
            $arr[]= $val['groupid'];
        }

        return $arr;
    }

    /**
     *组长下所有人员的数据
     * author: hlc
     */

     public function getCsno($params = array()){
         $arr = '';
         foreach ($params as $key=>$val){
             $arr.= $val.',';
         }
         $arr = rtrim($arr,",");

         $sql = "select distinct a.sno,b.csname_true from cservice_groups as a left join cservice as b on b.csno=a.sno where a.groupid in (".$arr.")";
         $data = Yii::app()->db->createCommand($sql)->queryAll();

         return $data;
     }

    /**
     * 个人计划用户可查看人员
     * @param $uid
     * @return array|int
     */
     public function user_plan_data_authority($uid)
    {
        //判断是否为超级管理员
        if($uid==Yii::app()->params['management']['super_admin_id'] ) return 0;
        //判断权限是否为超级管理员权限
        $urole = AdminUser::model()->get_user_role($uid);
        foreach ($urole as $val){
            if($val['role_id'] == 1) return 0;
        }
        //是否需要审核人员审核
        $is_audit = intval(Yii::app()->params['remind']['Individual_plan_auditor']);
        //审核人员配置
        $audit_user = intval(Yii::app()->params['remind']['group_plan_auditor']);
        if ($audit_user == $uid) {
            return 0;
        }
        $uids = array();
        $groups = $this->get_manager_group($uid);
        if ($groups) {
            $group_id = implode(',',$groups);
            $uids = AdminUserGroup::model()->getUsersByGroups($group_id);
        }
        return $uids;
    }

    public function group_plan_data_authority($uid)
    {
        //判断是否为超级管理员
        if($uid==Yii::app()->params['management']['super_admin_id'] ) return 0;
        //判断权限是否为超级管理员权限
        $urole = AdminUser::model()->get_user_role($uid);
        foreach ($urole as $val){
            if($val['role_id'] == 1) return 0;
        }
        //审核人员配置
        $audit_user = intval(Yii::app()->params['remind']['group_plan_auditor']);
        if ($audit_user == $uid) {
            return 0;
        }
        $group_ids = $this->get_manager_group($uid);
        return $group_ids;

    }
}