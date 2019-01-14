<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class AdminUserIdentity extends CUserIdentity
{
    private $_id;
    public $user;
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		$users=array(
			// username => password
			'demo'=>'demo',
			'admin'=>'admin',
		);
        $user = AdminUser::model()->findByAttributes(array('csname' => $this->username));
        if ($user === null){
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        }else{
        	$ugroups=AdminUser::model()->get_user_group($user->csno);//print_r($ugroups);die();
        	$u_private_role=AdminUser::get_user_role($user->csno); //查询私有的角色

			$groupidArr=array();
			$groupnameArr=array();
			foreach($ugroups as $r){
				$groupidArr[]=$r['groupid'];
				$groupnameArr[]=$r['groupname'];
			}
        	$uroledata=AdminGroupRole::get_group_role(implode(',',$groupidArr)); //查询该组的角色

			$u_private_auth=AdminUser::model()->getPrivateAuth($user->csno);
        	$my_role=array(); //我的所有角色
        	foreach($u_private_role as $r){
        		$my_role[$r['role_id']]=$r;	
        	}
        	foreach($uroledata as $r){
        		$my_role[$r['role_id']]=$r;
        	}

			$uroles=array();
        	$uroles_temp=AdminGroupRole::role_arr($my_role); //把角色查询结果转换成 权限数组

			foreach($uroles_temp as $k=>$r){
				$uroles[$r['authority_name']]=$r;
			}
			foreach($u_private_auth as $k=>$r){
				$uroles[$r['authority_name']]=$r;
			}

			//print_r($u_private_role);
        	//print_r($uroles);die();

            $this->_id = $user->csno;
            $this->username = $user->csname;
            $this->errorCode = self::ERROR_NONE;
       		if(!$ugroups && $user->csno!=Yii::app()->params['management']['super_admin_id']){
       			die('该用户没有部门信息');
       		}
       		
       		if(!$ugroups && $user->csno==Yii::app()->params['management']['super_admin_id']){
       			$groupname='-';
       		}else{
       			$groupname=implode(',',$groupnameArr);
       		}
       		
            //以下值可用 Yii:app()->user->属性 获取
       		$this->setState('isGuest',0);
            $this->setState('uid',$user->csno);
            $this->setState('uname',$user->csname);
			$this->setState('uname_true',$user->csname_true);
            $this->setState('mylevel',$uroles);
            $this->setState('groupname',$groupname);
            $this->setState('cate_id',0);
          //  $this->setState('ustate',$user->ustate);
          //  $this->setState('cate_id',$user->cate_id);
           // $this->setState('role',$user->role);
        }
		return !$this->errorCode;
	}

    public function getId()
    {
        return $this->_id;
    }
    public function getUser()
    {
    	return $this->user;
    }
    
}