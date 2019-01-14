<?php
class SiteController extends AdminController{
	public function filters(){
		return array(
			'accessControl',
		);
	}
	public function actionIndex(){

		if(Yii::app()->admin_user->isGuest){
			$this->redirect(array('site/login'));
		}else{
//			echo Yii::app()->admin_user->uname;
			$this->redirect(array('frame/index'));
		}
	}

	public function actionLogin(){
//	    my_print($_POST);die;

        $Agent = $_SERVER['HTTP_USER_AGENT'];
        $_SERVER['HTTP_HOST'] == yii::app()->params['customer_config']['domain']? $type = 1:$type= 0;
        $secret_agent=Yii::app()->params['management']['user-agent'];
        if($secret_agent){
            if($Agent!=$secret_agent){
                $this->msg(array('state'=>-2,'url'=>'http://www.baidu.com','msgwords'=>'404 NOT FOUND!'));
            }
        }

        $this->admin_style='default';
		if(!Yii::app()->admin_user->isGuest && $type == 0){
			$this->redirect(array('frame/index'));
		}
		$model = new AdminLoginForm;
        // collect user input data
        $uname=isset($_POST['uname'])?$_POST['uname']:'';
		$upass=isset($_POST['upass'])?$_POST['upass']:'';
		$rancode=isset($_POST['rancode'])?$_POST['rancode']:'';
        if (isset($_POST['uname'])) {
            $model->username=$uname;
            $model->password=$upass;
            if($type == 1){
                if($rancode==''){
                    $this->msg(array('state'=>0,'msgwords'=>'验证码不能为空'));
                }
                if(!isset($_SESSION['rancode'])||$_SESSION['rancode']!=$rancode){
                    	$this->msg(array('state'=>0,'msgwords'=>'验证码无效'));
                }
            }
            $user_model = AdminUser::model ()->findByAttributes (array ('csname' => $model->username,'service' => $type));
			if ($user_model == null) {
				$this->msg(array('state'=>0,'msgwords'=>'账号不存在'));
			}
			if ($user_model->cspwd != AdminUser::password ( $upass )) {
				$this->msg(array('state'=>0,'msgwords'=>'密码不正确'));
			}
			if($user_model->csstatus==1){
				$this->msg(array('state'=>0,'msgwords'=>'账号禁止登录'));
			}
			// print_r($user_model);die('aa');
			if ($model->login ()) {
				$attributes = array (
					'last_loginip' => ip2long ( Yii::app ()->request->userHostAddress ),
					'last_logindate' => date ( 'Y-m-d H:i:s', time () )
				);
				$this->logs('登录成功');
				$this->msg(array('state'=>1,'msgwords'=>'登录成功','url'=>$this->createUrl('frame/index')));

			}
		}
        $type == 1?$this->render('customer'):$this->render('login');
	}


	public function actionEditPassword(){
		$uname=Yii::app()->admin_user->uname;
		if(isset($_POST['old_upass'])){
			$old_upass=$this->post('old_upass');
			$upass=$this->post('new_upass');
			if($old_upass==''){
				$this->msg(array("state"=>0,"msgwords"=>'原始密码不能为空'));
			}
			if(verify::check_password($upass)==0){
				$this->msg(array("state"=>0,"msgwords"=>'新密码不合法'));
			}
			$old_upass=AdminUser::password($old_upass);
			$upass=AdminUser::password($upass);
			$m=AdminUser::model()->findByAttributes(array('csname'=>$uname,'cspwd'=>$old_upass));
			if(!$m){
				$this->msg(array("state"=>0,"msgwords"=>'原始密码不正确'));
			}
			$m->cspwd=$upass;
			$dbresult=$m->save();
			if($dbresult){
				$this->logs('修改了密码');
				$this->msg(array('state'=>1,'msgwords'=>'密码修改成功'));
			}else{
				$this->msg(array('state'=>0,'msgwords'=>'操作失败，未知原因'));
			}
		}
		$this->render('editPassword');
	}
	public function actionLogout(){
		$this->logs('退出系统');
		Yii::app()->admin_user->logout();
		$this->redirect('site/login');
	}

}