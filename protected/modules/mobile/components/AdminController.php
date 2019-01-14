<?php
class AdminController extends MobileController {
	//public $layout=false;
	public $admin_style='default';
	public $layout=2;
	public $page_time_start=0; //页面执行时间

	public $AdminStyleArray=array();
	public function init(){
        parent::init();
		$this->page_time_start=helper::getmicrotime();
    }

	public function actionError(){
        $this->mobileMsg(array('status'=>0,'content'=>'出现错误'));

	}

    /**
     * 手机弹窗提示
     * @param array $params
     * author: yjh
     */
	public function mobileMsg($params=array()){
        $params['status']=isset($params['status'])?$params['status']:1;
        if(1==$params['status']) {
            $params['content']=isset($params['content'])?$params['content']:'成功';

            $this->renderPartial('/common/success-dialog', array(
                'msg' => $params,
            ));
        }else{
            $params['content']=isset($params['content'])?$params['content']:'出错了';

            $this->renderPartial('/common/warning-dialog', array(
                'msg' => $params,
            ));
        }
        exit();
    }
	//返回json数据
	public function echoJson($state,$msgwords='',$data=array()){

		if($state<1){
			$msgwords=$msgwords?$msgwords:'error';
		}else if($state>=1){
			$msgwords=$msgwords?$msgwords:'ok';
		}
		$params['state']=$state;
		$params['msgwords']=$msgwords;
		$params['data']=$data;
		die(json_encode($params));
	}


	//返回后台组权限和用户权限
	//$$auth_tag 权限标识
	public function auth_action($auth_tag){
		//判断功能权限
		if(!$this->check_u_menu(array('auth_tag'=>$auth_tag,'echo'=>0)))
		{
			$this->mobileMsg(array('state'=>0,'content'=>'无权限'));
			return false;
		}
	}
	//判断我的权限，是否显示 按钮 之类的代码
	public function check_u_menu($params){

		$uid=Yii::app()->mobile->uid;

		$ugroups=AdminUser::model()->get_user_group($uid);

        $groupidArr=array();
		foreach($ugroups as $r){
			$groupidArr[]=$r['groupid'];
		}

		$code=isset($params['code'])?$params['code']:'';
		$auth_tag=strtolower($params['auth_tag']);
		$echo=isset($params['echo'])?$params['echo']:1;
		$params['param_type']=isset($params['param_type'])?$params['param_type']:2;
		$params['param_name']=isset($params['param_name'])?$params['param_name']:'';
		$params['param_value']=isset($params['param_value'])?$params['param_value']:'';


		if($params['param_name']){
			$params['param_type']=$params['param_type']?$params['param_type']:1;//如果不传入param_type  ，则默认为get
		}

		if($uid==Yii::app()->params['management']['super_admin_id'] ){
			if($echo==1){
				echo $code;
			}
			return true;
		}
		//判断权限是否为超级管理员权限
		$urole = AdminUser::model()->get_user_role($uid);
		foreach ($urole as $val){
			if($val['role_id'] == 1){
				if($echo==1){
					echo $code;
				}
				return true;
			}
		}


		foreach(PublicAuth::$public_user_auth as $r){
			if(stripos($r,'*')){
				if(preg_match('~'.$r.'~i',$auth_tag)){
					if($echo==1){
						echo $code;
					}
					return true;
				}
			}else{
				if(strtolower($r)==strtolower($auth_tag)){
					if($echo==1){
						echo $code;
					}
					return true;
				}
			}
		}


		$levels=Yii::app()->mobile->mylevel;//print_r($levels);
		foreach($levels as $r){
			$r['authority_name']=strtolower($r['authority_name']);//echo $auth_tag.':'.$r['authority_name'].'<br>';
			$a2Arr=explode(',',$r['authority_name']);
			if(in_array($auth_tag,$a2Arr) && $params['param_type']==$r['param_type'] && $params['param_value']==$r['param_value']){
				echo $code;
				return true;
			}


		}
		//没有权限
		return false;
	}

	/**修改和新增的时候 对象赋值
	 * @params $model 模型名称
	 * @params $data  要保存或修改的数据
	 * @params $Dtable 没有模型的表(动态表支持)
	 * */
	public function data($model,$field=array(),$Dtable=''){
		if($Dtable==''){
			$post=new $model();
		}else{
			$post=new $model($Dtable);
		}
		foreach($field as $k=>$r){
			$post->$k=$r;
		}
		return $post;
	}

	//将  findAll 的结果集 取出记录，转为数组
	public function toArr($result){
		if(!$result){
			return array();
		}
		$re=array();
		if(isset($result->attributes)){
			$re=$result->attributes;
		}else{
			foreach($result as $r){
				$re[]=$r->attributes;
			}
		}
		return $re;

	}
	//插入后台操作日志
	function logs($logs_content){
		$post=new AdminAclog();
		$post->sno=Yii::app()->mobile->uid;
		$post->accode=Yii::app()->controller->id.'->'.$this->getAction()->getId();
		$post->log_time=time();
		$post->log_ip=helper::getip();
		$post->log_details=$logs_content;
		$post->save();

	}

	//增加微信修改操作日志
	public function wLogs($logs_content,$weixin_id){
		$post=new WeChatChangeLog();
		$post->sno=Yii::app()->mobile->uid;
		$post->log_time=time();
		$post->weixin_id=$weixin_id;
		$post->log_details=$logs_content;
		$post->save();
	}
	
	/**************/
	//数据读取
	public function query($sql){
		return Yii::app()->db->createCommand($sql)->queryAll();
	}
	//获取get
	public function get($key, $default = null){
		return Yii::app()->request->getParam($key, $default);
	}
	//获取post
	public function post($key, $default = null){
		return Yii::app()->request->getPost($key, $default);
	}
	public function getRunTime(){
		$data_fill_time=helper::getmicrotime()-$this->page_time_start;
		return substr($data_fill_time/1000,0,6).'s';
	}


	/**
	 * 人员数据查看权限
	 * @return array
	 * author: yjh
	 */
	public function data_authority(){
		$uid=Yii::app()->mobile->uid;
		//判断开关是否开启
		if(Yii::app()->params['management']['authority_switch'] == 0)return 0;
		//判断是否为超级管理员
		if($uid==Yii::app()->params['management']['super_admin_id'] ) return 0;
		//判断权限是否为超级管理员权限
		$urole = AdminUser::model()->get_user_role($uid);
		foreach ($urole as $val){
			if($val['role_id'] == 1) return 0;
		}

		//获取登录人员所在部门及以下部门
		$ugroups=AdminUser::model()->get_user_group($uid);
		$ugroupArr=$groupArr=array();
		foreach($ugroups as $r){
			$ugroupArr[]=$r['groupid'];
		}
		$mgroupArr = AdminUser::model()->get_manager_group($uid);
		$ugroupArr = empty($mgroupArr)?$ugroupArr:array_unique(array_merge($ugroupArr,$mgroupArr));
		foreach ($ugroupArr as $value){
			$groupArr = array_merge($groupArr,AdminGroup::model()->get_children_groups($value));
		}
		$unique_arr = array_unique ( $groupArr );
		$repeat_arr = array_diff_assoc ( $groupArr, $unique_arr );
		$groupArr = array_diff($groupArr,$ugroupArr);
		$groupArr = array_merge($groupArr,$repeat_arr);
		$groupArr = array_merge($groupArr,$mgroupArr);
		$groupStr = implode(',',array_unique($groupArr));
		$userStr = $uid;
		if(!empty($groupStr)){
			$userStr .= ",".implode(',',AdminUserGroup::model()->getUsersByGroups($groupStr));


		}
		//获取可以查看数据的人员

		return $userStr;
	}




}