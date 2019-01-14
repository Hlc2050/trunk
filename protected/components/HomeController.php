<?php
class HomeController extends CController{
	//public $layout=false;
	public $layout=2;
	public $page_time_start=0; //页面执行时间
	public $from='pc';  //来自终端
	public $now_city=array(); //城市区域
	public $sessionid='';
	public function init(){
		//header('location:/admin/frame/index');exit;
		$this->page_time_start=helper::getmicrotime();	
		//页码
		$_GET['p']=isset($_GET['p'])&&$_GET['p']>=1?intval($_GET['p']):1;

		
		if(!defined('CSSURL')){
			define('CSSURL',Yii::app()->params['basic']['cssurl']);
		}
		if(!defined('SITENAME')){
			define('SITENAME',Yii::app()->params['basic']['sitename']);
		}
	}
	
	public function filters() {
		return array(
				'accessAuth',
		);
	}
	public function filterAccessAuth($filterChain) {
		$filterChain->run();
	}
	
	public function get_city(){
		//获取当前用户的城市
		$ip=helper::getip();
		//$city=helper::convertip($ip,'city');
                $ip_search_url = 'http://ip.taobao.com/service/getIpInfo.php?ip='.$ip;
                $address_info_result = helper::get_curl_contents($ip_search_url);
                $address_info = json_decode($address_info_result, true);
                $city = $address_info['data']['city'];
		//$m=Linkage::model()->findByAttributes(array('linkage_name'=>$city,'linkage_type_id'=>1));
		$m=Linkage::model()->find('linkage_name LIKE "'.$city.'%" AND `linkage_type_id`=1');
		if(!$m){
			$page['now_city']=array('linkage_id'=>'52','linkage_name'=>'北京','linkage_name_py'=>'beijing','parent_id'=>2,'area'=>2);
		}else{
			$page['now_city']=Dtable::toArr($m);
		}
		//print_r($page['now_city']);die();
		 
		 
		 
		return $page['now_city'];
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
     * @params $params['type']   类型，默认页面,可选 json,xml,getjson';
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
    		$jscode='<script>setTimeout(function (){history.go(-1);},1000)</script>';
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
    	}
    	if(!$params['jscode']){
    		$params['jscode']=$jscode;
    	}
    	if($params['type']=='json'){
    		die(json_encode($params));
    	}
    	if($params['type']=='getjson'){
    		$jsoncallback=$this->get('jsoncallback');
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
	
    
}