<?php

/**
 * 操作日志控制器
 * Class AdminAclogController
 */
class AdminAclogController extends  AdminController{
	public function actionIndex(){
		
		$params['where']='';
		if($this->get('search_type')=='keys' && $this->get('search_txt')){
			$params['where'] =" and(a.log_details like '%".$this->get('search_txt')."%') ";
		}else if($this->get('search_type')=='id'  && $this->get('search_txt')){ //网点ID
			$params['where'] =" and(a.log_id=".intval($this->get('search_txt')).") ";
		}		
	    $params['order']="  order by a.log_id desc    ";
	    $params['pagesize']=Yii::app()->params['management']['pagesize'];
	    $params['join']="left join cservice as b on b.csno=a.sno ";
	    $params['pagebar']=1;
	    $params['select']="a.*,b.csname";
	    $params['smart_order']=1;   
	    $page['listdata']=Dtable::model('cservice_aclog')->listdata($params);
		$this->render('index',array('page'=>$page));
	}

}
?>