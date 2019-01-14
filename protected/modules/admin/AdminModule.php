<?php
class AdminModule extends  CWebModule{
	public function init()
	{

		// this method is called when the module is being created
		// you may place code here to customize the module or the application
	
		// import the module-level models and components
		$this->setImport(array(
				'admin.models.*',
				'admin.components.*',
				'admin.widget.*',
		));
		Yii::app()->user->loginUrl = '/admin/site/login';
		//这里重写父类里的组件
		//如有需要还可以参考API添加相应组件
		Yii::app()->setComponents(array(
		'errorHandler'=>array(
		'class'=>'CErrorHandler',
		'errorAction'=>'admin/site/error',
		),
		'admin_user'=>array(
		'class'=>'AdminWebUser',//后台登录类实例
		'stateKeyPrefix'=>'admin',//后台session前缀
		'loginUrl'=>Yii::app()->createUrl('admin/site/login'),
		// 'returnUrl'=>Yii::app()->createUrl('admin/node/index'),
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
				//                if(!$this->allowIp(Yii::app()->request->userHostAddress) && $route!=='default/error')
					//                    throw new CHttpException(403,"You are not allowed to access this page.");
				$publicPages=array(
						'site/login',
						'post/VerifyCode',
				);
				if(Yii::app()->admin_user->isGuest && !in_array($route,$publicPages)){
					Yii::app()->admin_user->loginRequired();
				}else{
					if(!Yii::app()->admin_user->isGuest && !$this->check_auth($controller,$action)){
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
		$uid=Yii::app()->admin_user->uid;
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


		$levels=Yii::app()->admin_user->mylevel;//echo $auth_tag.'<br>';
		$levels=is_array($levels)?$levels:array();//print_r($levels);//die();
		foreach ($levels as $r){
			$r['authority_name']=strtolower($r['authority_name']);
			$a2Arr=explode(',',$r['authority_name']);
			/*
			if(in_array($r['authority_name'],$a2Arr) && $r['param_type']==0){  //如果有这个权限标识，无参数的话
				return true;
    				
    		}else if(in_array($r['authority_name'],$a2Arr) &&  $r['param_type']==3){  //如果有这个权限标识， 无参数，但是与其他标识相同
    			
    			
			}else if(in_array($r['authority_name'],$a2Arr)) {
				$param_name=$r['param_name'];//echo $r['authority_name'].'zz';
				$param_type=$r['param_type'];
				$param_value=$r['param_value'];
				if($param_name){
					待修改
					switch($param_type){
						case 1: $getvalue=$this->get($param_name); break;
						case 2: $getvalue=$this->post($param_name);break;
						default:die('param type='.$param_type.' 未定义');return;
					}
					if($param_value!=''){  //如果不为空
						if($getvalue==$param_value){
							return true;
						}
					}else{

						return true;

					}

					return true;
					
					
					
//				}

			}

			*/
			if(in_array($auth_tag,$a2Arr)){
				return true;
			}
			
			
			
			
		}
		return false;
		
	}
	//允许全体管理员使用的方法
	public static $public_user_auth=array(
			'site_*',
			'post_verifyCode',
			'frame_*',
			'infoCategory_showCategoryLeftmenu',
			'infoCategory_getCateTotalInfo',
			'info_cutImage',
	
	);
	
	//获取get
	public function get($key, $default = null){
		return Yii::app()->request->getParam($key, $default);
	}
	//获取post
	public function post($key, $default = null){
		return Yii::app()->request->getPost($key, $default);
	}
}