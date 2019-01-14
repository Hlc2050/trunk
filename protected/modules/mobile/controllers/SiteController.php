<?php

class SiteController extends AdminController
{
    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function actionIndex()
    {
        if (Yii::app()->mobile->isGuest) {
            $this->redirect(array('site/login'));
        } else {
            $this->redirect(array('frame/index'));
        }
    }

    public function actionLogin()
    {
        if (!Yii::app()->mobile->isGuest) {
            $this->redirect(array('frame/index'));
        }
        $uname = isset($_POST['uname']) ? $_POST['uname'] : '';
        $upass = isset($_POST['upass']) ? $_POST['upass'] : '';
        if(-1!=$this->openid && !empty($this->openid)){
            $res = AdminUser::model()->find(" openid = '{$this->openid}' ");
            if($res)
            {
                $uname=$res['csname'];
                $upass=$res['cspwd'];
                if ($res['csstatus'] == 1) {
                    $this->mobileMsg(array('status'=>0,'content'=>'账号禁止登录'));
                }
                $model = new AdminLoginForm;
                $model->username = $uname;
                if ($model->login()) {
                    $this->logs('微信授权登录成功');
                    $this->mobileMsg(array('status'=>1,'content'=>'微信授权登录成功','url'=>$this->createUrl('frame/index')));
                }

            }

        }
        common_login:
        // collect user input data
        if (!empty($uname)) {
            $model = new AdminLoginForm;
            $model->username = $uname;
            $model->password = $upass;
            $user_model = AdminUser::model()->findByAttributes(array('csname' => $model->username));
            if ($user_model == null) {
                $this->mobileMsg(array('status'=>0,'content'=>'用户不存在','url'=>$this->createUrl('site/login')));
            }
            if ($user_model->cspwd != AdminUser::password($upass)) {
                $this->mobileMsg(array('status'=>0,'content'=>'密码不正确','url'=>$this->createUrl('site/login')));
            }
            if ($user_model->csstatus == 1) {
                $this->mobileMsg(array('status'=>0,'content'=>'账号禁止登录','url'=>$this->createUrl('site/login')));
            }
            if($user_model->openid != $_SESSION['mobile_openid']){
                $this->addOpenid($user_model->csno);
            }
            if ($model->login()) {
                $this->logs('登录成功');
                $this->mobileMsg(array('status'=>1,'content'=>'登录成功','url'=>$this->createUrl('frame/index')));
            }
        }
        $this->render('login');
    }

    //首次登录添加openid
    public function addOpenid($csno)
    {
        if (!empty($_SESSION['mobile_openid'])) {
            $field = array();
            $field['openid'] = $_SESSION['mobile_openid'];
            AdminUser::model()->updateAll($field, "csno={$csno}");
        }
    }


    public function actionLogout()
    {
        $this->logs('退出系统');
        Yii::app()->mobile->logout();
        $this->mobileMsg(array('status'=>1,'content'=>'注销成功','url'=>'site/login'));
    }

    public function actionUnbind()
    {
        $uid=Yii::app()->mobile->uid;

        Yii::app()->mobile->logout();
        $user=AdminUser::model()->findByPk($uid);
        $user->openid='';
        $user->save();
        $this->mobileMsg(array('status'=>1,'content'=>'解除绑定成功','url'=>'site/login'));
    }

    /**
     * 发送post请求
     * @param string $url 请求地址
     * @param array $post_data post键值对数据
     * @return string
     */
    function send_post($url, $post_data)
    {
        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postdata,
                'timeout' => 15 * 60 // 超时时间（单位:s）    
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }
}