<?php
class AdminController extends CController{
    //public $layout=false;
    public $admin_style='default';
    public $layout=2;
    public $page_time_start=0; //页面执行时间

    public $AdminStyleArray=array();
    public function init(){

        $this->page_time_start=helper::getmicrotime();
        $this->AdminStyleArray=array(
            array(
                'style_name'=>'默认',
                'style_folder'=>'default',
                'style_color'=>'gray'
            ),
            array(
                'style_name'=>'蓝色',
                'style_folder'=>'blue',
                'style_color'=>'blue'
            ),
            array(
                'style_name'=>'土豪金',
                'style_folder'=>'orange',
                'style_color'=>'orange'
            ),
            array(
                'style_name'=>'绿色',
                'style_folder'=>'green',
                'style_color'=>'green'
            ),

        );
        if(isset($_COOKIE['admin_style'])){
            foreach($this->AdminStyleArray as $r){
                if($r['style_folder']==$_COOKIE['admin_style']){
                    $this->admin_style=$_COOKIE['admin_style'];
                    break;
                }
            }

        }
        if(!isset($_GET['layout'])){
            $_GET['layout']=Yii::app()->params['management']['layout'];
            $this->layout=Yii::app()->params['management']['layout'];

        }else if(!$_GET['layout']) {
            $this->layout = Yii::app()->params['management']['layout'];
        }


    }

    public function actionError(){
        $msg['icon']='error';
        $msg['msgwords']='出现错误';
        $this->render('msg',array(
            'msg'=>$msg,
        ));
    }
    /**提示界面
     * @params $params['state'] 0=失败，并且浏览器后退一步,1=成功，并且跳转到上一页,-1=不进行页面跳转，只显示 msgwords,-2=错误，停止
     * @params $params['url']  强制跳转的url
     * @params $params['msgwords'] 显示的文字
     * @params $params['jscode']  自定义js行为
     * @params $params['type']   类型，默认页面,可选 json,xml,jsonp';
     * @atention 如果制定了url话跳转到 url
     * 如果 msgwords 提示文字有的话，则显示提示文字
     * 如果
     */
    public function msg($params=array()){

        $params['state']=isset($params['state'])?$params['state']:1;
        $params['url']=isset($params['url'])?$params['url']:(isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'');
        $params['msgwords']=isset($params['msgwords'])?$params['msgwords']:'';
        $params['jscode']=isset($params['jscode'])?$params['jscode']:'';
        $params['type']=isset($params['type'])?$params['type']:'';
        $msgwords_bak=$params['msgwords'];
        $jscode='';
        if($params['state']==0){
            $params['msgwords']=$params['msgwords']?$params['msgwords']:'操作失败';
            $params['icon']='error';
            $jscode="<script>jQuery('body').on('click', '.aui_dialog', function() {history.go(-1)});</script>";
            //$jscode='<script>setTimeout(function (){history.go(-1);},1000)</script>';
        }else if($params['state']==1){
            $params['msgwords']=$params['msgwords']?$params['msgwords']:'操作成功';
            $params['icon']='succeed';
            $jscode='<script>setTimeout(function (){window.location="'.$params['url'].'";},1000)</script>';
        }else if($params['state']==-1){
            $jscode='';
            $params['msgwords']=$msgwords_bak?$msgwords_bak:'操作停止';
            $params['icon']='question';
        }else if($params['state']==-2){
            $jscode='';
            $params['msgwords']=$msgwords_bak?$msgwords_bak:'操作停止';
            $params['icon']='error';
            $jscode='<script>setTimeout(function (){window.location="'.$params['url'].'";},1000)</script>';

        }
        if(!$params['jscode']){
            $params['jscode']=$jscode;
        }
        if($params['type']=='json'){
            die(json_encode($params));
        }
        if($params['type']=='jsonp'){
            $jsoncallback=isset($_GET['jsoncallback'])?$_GET['jsoncallback']:'';
            die($jsoncallback.'('.json_encode($params).')');
        }

        $this->renderPartial('/site/msg',array(
            'msg'=>$params,
        ));
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
            $this->msg(array('state'=>-2,'msgwords'=>'无权限'));
            return false;
        }
    }
    //判断我的权限，是否显示 按钮 之类的代码
    public function check_u_menu($params){
        $uid=Yii::app()->admin_user->uid;

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


        $levels=Yii::app()->admin_user->mylevel;//print_r($levels);
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
    //取得某张表数据 返回 option
    public  function get_option($params){
        $options='';
        $id_field_name=isset($params['id_field_name'])&&$params['id_field_name']!=''?$params['id_field_name']:'';
        $txt_field_name=isset($params['txt_field_name'])&&$params['txt_field_name']!=''?$params['txt_field_name']:'';
        $txt_field_name2=isset($params['txt_field_name2'])&&$params['txt_field_name2']!=''?$params['txt_field_name2']:'';
        $table_name=isset($params['table_name'])&&$params['table_name']!=''?$params['table_name']:'';
        $select_value=isset($params['select_value'])&&$params['select_value']!=''?$params['select_value']:'';
        $wheresql=isset($params['wheresql'])&&$params['wheresql']!=''?$params['wheresql']:'';
        $ordersql=isset($params['ordersql'])&&$params['ordersql']!=''?$params['ordersql']:'';

        $sql="select * from ".$table_name." ".$wheresql." ".$ordersql;
        $rsarrs=Yii::app()->db->createCommand($sql)->queryAll();
        if(count($rsarrs)){
            foreach ($rsarrs as $rs){
                $options .='<option title="'.$rs[$txt_field_name].'" value="'.$rs[$id_field_name].'"  '.($select_value==$rs[$id_field_name]?'selected':'').' >'.$rs[$txt_field_name].''.($txt_field_name2?'-'.$rs[$txt_field_name2]:'').'</option>';
            }
        }
        return $options;
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
        $post->sno=Yii::app()->admin_user->uid;
        $post->accode=Yii::app()->controller->id.'->'.$this->getAction()->getId();
        $post->log_time=time();
        $post->log_ip=helper::getip();
        $post->log_details=$logs_content;
        $post->save();
    }

    //增加微信修改操作日志
    public function wLogs($logs_content,$weixin_id){
        $post=new WeChatChangeLog();
        $post->sno=Yii::app()->admin_user->uid;
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
     * 获取时间间隔sql语句
     * @param $sql_field
     * @param string $start_time
     * @param string $end_time
     * @return string
     * author: yjh
     */
    public function getTimeIntervalSql($sql_field,$start_time='',$end_time=''){
        $params='';
        if ($start_time &&  $end_time) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time);
            $params = " and($sql_field>=$start_time  and $sql_field<=$end_time) ";
        } elseif ($start_time) { //
            $start_time = strtotime($start_time);
            $params = " and($sql_field>=$start_time) ";
        }elseif($end_time){
            $end_time = strtotime($end_time);
            $params = " and($sql_field<=$end_time) ";
        }
        return $params;
    }

    /**
     * 人员数据查看权限
     * @param $support int 支持人员是否可查看关联推广人员数据0否，1是
     * @return array
     * author: yjh
     */
    public function data_authority($support=0){
        $uid=Yii::app()->admin_user->uid;
        //判断开关是否开启
        if(Yii::app()->params['management']['authority_switch'] == 0)return 0;
        //判断是否为超级管理员
        if($uid==Yii::app()->params['management']['super_admin_id'] ) return 0;
        //判断权限是否为超级管理员权限
        $urole = AdminUser::model()->get_user_role($uid);
        foreach ($urole as $val){
            if($val['role_id'] == 1) return 0;
        }
        //支持人员角色
        $is_support = 0;
        foreach ($urole as $val) {
            if($val['role_id'] == 21){
                $is_support=1;
                break;
            }
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
        //获取可以查看数据的人员
        if(!empty($groupStr)){
            $userStr .= ",".implode(',',AdminUserGroup::model()->getUsersByGroups($groupStr));
        }
        //支持人员可查看关联的推广人员数据
        if ($support == 1 && $is_support == 1) {
            $pros = PromotionUserRelation::model()->getRelationByUser($uid);
            if ($pros) {
                $userStr .= ",".implode(',',$pros);
            }
        }
        return $userStr;
    }

    /**
     * 转换字节数为其他单位
     *
     *
     * @param   string $filesize  字节大小
     * @return  string 返回大小
     */
    function sizecount($filesize) {
        if ($filesize >= 1073741824) {
            $filesize = round($filesize / 1073741824 * 100) / 100 .' GB';
        } elseif ($filesize >= 1048576) {
            $filesize = round($filesize / 1048576 * 100) / 100 .' MB';
        } elseif($filesize >= 1024) {
            $filesize = round($filesize / 1024 * 100) / 100 . ' KB';
        } else {
            $filesize = $filesize.' Bytes';
        }
        return $filesize;
    }

}