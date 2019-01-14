<?php
class MobileModule extends  CWebModule{
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application
	
		// import the module-level models and components
		$this->setImport(array(
				'mobile.models.*',
				'mobile.components.*',
				'mobile.widget.*',
		));
		Yii::app()->user->loginUrl = '/mobile/site/login';
		//这里重写父类里的组件
		//如有需要还可以参考API添加相应组件
		Yii::app()->setComponents(array(
		'errorHandler'=>array(
		'class'=>'CErrorHandler',
		'errorAction'=>'mobile/site/error',
		),
		'mobile'=>array(
		'class'=>'AdminWebUser',//后台登录类实例
		'stateKeyPrefix'=>'mobile',//后台session前缀
		'loginUrl'=>Yii::app()->createUrl('mobile/site/login'),
		),
		), false);

		//页码
		$_GET['p']=isset($_GET['p'])&&$_GET['p']>=1?intval($_GET['p']):1;
		
	}
	
	public function beforeControllerAction($controller, $action){
		if(parent::beforeControllerAction($controller, $action)){
			// this method is called before any module controller action is performed
			// you may place customized code here
			if(parent::beforeControllerAction($controller, $action)){
				$route=$controller->id.'/'.$action->id;
				$publicPages=array(
						'site/login',
				);
				if(Yii::app()->mobile->isGuest && !in_array($route,$publicPages)){
					Yii::app()->mobile->loginRequired();
				}else{
					if(!Yii::app()->mobile->isGuest && !$this->check_auth($controller,$action)){
						die('no access');
					}
					return true;
					
				}
			}
			return true;
		}else{
			return false;
	
		}
	}
	
	private function check_auth($controller,$action){
		$auth_tag=strtolower($controller->id.'_'.$action->id);//die($auth_tag);
		
		foreach(PublicAuth::$public_user_auth as $r){
			if(stripos($r,'*')){
				if(preg_match('~'.$r.'~i',$auth_tag)){
					return true;
				}
			}else{
				if(strtolower($r)==strtolower($auth_tag)){
					return true;
				}
			}
		}
		$uid=Yii::app()->mobile->uid;
		$ugroups=AdminUser::model()->get_user_group($uid);
		$groupidArr=array();
		foreach($ugroups as $r){
			$groupidArr[]=$r['groupid'];
		}

		if($uid==Yii::app()->params['management']['super_admin_id']
		){
			return true;
		}
		//判断权限是否为超级管理员权限
		$urole = AdminUser::model()->get_user_role($uid);
		foreach ($urole as $val){
			if($val['role_id'] == 1){
				return true;
			}
		}


		$levels=Yii::app()->mobile->mylevel;//echo $auth_tag.'<br>';
		$levels=is_array($levels)?$levels:array();//print_r($levels);//die();
		foreach ($levels as $r){
			$r['authority_name']=strtolower($r['authority_name']);
			$a2Arr=explode(',',$r['authority_name']);
			if(in_array($auth_tag,$a2Arr)){
				return true;
			}
			
		}
		return false;
		
	}

	//获取get
	public function get($key, $default = null){
		return Yii::app()->request->getParam($key, $default);
	}
	//获取post
	public function post($key, $default = null){
		return Yii::app()->request->getPost($key, $default);
	}
}