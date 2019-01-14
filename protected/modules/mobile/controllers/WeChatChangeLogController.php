<?php
class WeChatChangeLogController extends  AdminController{
	public function actionIndex(){
		
		$params['where']='';
		if($this->get('search_type')=='keys' && $this->get('search_txt')){
			$params['where'] =" and(a.log_details like '%".$this->get('search_txt')."%') ";
		}else if($this->get('search_type')=='id'  && $this->get('search_txt')){ //网点ID
			$params['where'] =" and(a.log_id=".intval($this->get('search_txt')).") ";
		}else if($this->get('search_type')=='weixin_id'  && $this->get('search_txt')){
			$params['where'] =" and(a.weixin_id=".intval($this->get('search_txt')).") ";
		}
		//操作用户
		if ($this->get('user_id') != '') $params['where'] .= "and (a.sno='".$this->get('user_id')."')";

		if ($this->get('weixin_id') != '') $params['where'] .= "and (a.weixin_id='".$this->get('weixin_id')."')";

	    $params['order']="  order by a.log_id desc    ";
	    $params['pagesize']=Yii::app()->params['management']['pagesize'];
	    $params['join']="left join cservice as b on b.csno=a.sno ";
	    $params['pagebar']=1;
	    $params['select']="a.*,b.csname_true";
	    $params['smart_order']=1;   
	    $page['listdata']=Dtable::model('wechat_change_log')->listdata($params);
		$this->render('index',array('page'=>$page));
	}

}
?>