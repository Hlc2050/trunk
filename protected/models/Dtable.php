<?php
/**
 * @author caihaibin
 * @qq 243008827
 * 这个类是最新完善的，动态类似处理，
 * 可重复使用而不会影响其它的调用。
 * 是居家旅行必备之良品
 */
class Dtable extends CActiveRecord{
	private static $_models=array();
	private $_i_tableName;
	private static $tableName;
	private static $_i_md=array();
	private $_md;
	public function getMetaData()
	{
		if($this->_md!==null)
			return $this->_md;
		else
			return $this->_md=self::model(self::tableName(),get_class($this))->_md;
	}
	public static function model($table_name='',$className=__CLASS__)
	{
		self::$tableName = $table_name;
		if(isset(self::$_models[$table_name])){
			return self::$_models[$table_name];
		}else
		{
			$model=self::$_models[$table_name]=new $className(null);
			$model->_md=new CActiveRecordMetaData($model);
			$model->attachBehaviors($model->behaviors());
			return $model;
		}
	}

	public function __construct($table_name = '') {
		if ($table_name === null) {
			parent::__construct ( null );
		} else {
			self::$tableName = $table_name;
			parent::__construct ();
		}
	}

	public function tableName() {
		if (! isset ( $this->_i_tableName )) {
			$this->_i_tableName = self::$tableName;
		}
		return $this->_i_tableName;
	}
	public function listdata($params=array()){
		$re=array();
		$default_params['where']=" where 1 ";
		$default_params['select']=" * ";
		$default_params['from']=self::$tableName.' as a ';
		$default_params['order']="";
		$default_params['join']='';
		$default_params['group']='';
		$default_params['p']='';
		$default_params['pagesize']=20;
		$default_params['smart_order']=1;
		$default_params['pagebar']=0;
		$default_params['debug']=0;
		$default_params['pageshow']='manage';
		foreach($default_params as $k=>$r){
			if($k=='where' && isset($params['where'])){
				$params[$k] =$r.$params[$k];
			}else{
				$params[$k]=isset($params[$k])?' '.$params[$k].' ':$r;
			}
		}
		$offset=0;
		if($params['pagebar']){
			$offset=($_GET['p']-1)*$params['pagesize'];
		}

			
		//智能排序
		if($params['smart_order']){
			$orderf=isset($_GET['order'])?$_GET['order']:'';
			if($orderf){
				$xu=isset($_GET['xu'])?$_GET['xu']:'';
				if($xu=='desc'){
					$params['order']=" order by ".$orderf." desc ";
				}else if($xu=='asc'){
					$params['order']=" order by ".$orderf." asc ";
				}
			}
		}
		//取消获取数量限制
		if(trim($params['pagesize']) == '*'){
		    $limt = '';

        }else{
            $limt = " limit ".$offset.','.$params['pagesize'];
        }

		$sql="select  ".$params['select']." from ".$params['from'].$params['join'].$params['where'].$params['group'].$params['order'].$limt;

		if($params['group']){
			$csql="select count(*) as total from (select  count(*) as total from ".$params['from'].$params['join'].$params['where'].$params['group'].') as ttt';
		}else{
			$csql="select  count(*) as total from ".$params['from'].$params['join'].$params['where'].$params['group'];
		}
		if($params['debug']==1){
			echo ($sql);
			echo $csql;
			die();
		}

		if(!isset($params['page_type']))
		    $params['page_type']=0;
		$listdata=Yii::app()->db->createCommand($sql)->queryAll();
		$pagearr=array();
		if($params['pagebar']){
			$total=Yii::app()->db->createCommand($csql)->queryAll();
			$pagearr=helper::pagehtml(array('total'=>$total[0]['total'],"pagesize"=>$params['pagesize'],"show"=>$params['pageshow'],"page_type"=>$params['page_type']));
		}
		$re['list']=$listdata;
		$re['total']=$total[0]['total'];
		$re['pagearr']=$pagearr;
		return $re;
	}

	public function orderdata($params=array()){
		$re=array();
		$default_params['where']=" where 1 ";
		$default_params['select']=" * ";
		$default_params['from']=self::$tableName.' as a ';
		$default_params['order']="";
		$default_params['join']='';
		$default_params['group']='';
		$default_params['p']='';
		$default_params['pagesize']=20;
		$default_params['smart_order']=1;
		$default_params['pagebar']=0;
		$default_params['debug']=0;
		$default_params['pageshow']='manage';
		foreach($default_params as $k=>$r){
			if($k=='where' && isset($params['where'])){
				$params[$k] =$r.$params[$k];
			}else{
				$params[$k]=isset($params[$k])?' '.$params[$k].' ':$r;
			}
		}
		$offset=0;
		if($params['pagebar']){
			$offset=($_GET['p']-1)*$params['pagesize'];
		}


		//智能排序
		if($params['smart_order']){
			$orderf=isset($_GET['order'])?$_GET['order']:'';
			if($orderf){
				$xu=isset($_GET['xu'])?$_GET['xu']:'';
				if($xu=='desc'){
					$params['order']=" order by ".$orderf." desc ";
				}else if($xu=='asc'){
					$params['order']=" order by ".$orderf." asc ";
				}
			}
		}

		$sql="select  ".$params['select']." from ".$params['from'].$params['join'].$params['where'].$params['group'].$params['order']." limit ".$offset.','.$params['pagesize'];
		if($params['group']){
			$csql="select count(*) as total from (select  count(*) as total from ".$params['from'].$params['join'].$params['where'].$params['group'].') as ttt';
		}else{
			$csql="select  count(*) as total from ".$params['from'].$params['join'].$params['where'].$params['group'];
		}
		if($params['debug']==1){
			echo ($sql);
			echo $csql;
			die();
		}

		$listdata=Yii::app()->order_db->createCommand($sql)->queryAll();
		$pagearr=array();
		if($params['pagebar']){
			$total=Yii::app()->order_db->createCommand($csql)->queryAll();
			$pagearr=helper::pagehtml(array('total'=>$total[0]['total'],"pagesize"=>$params['pagesize'],"show"=>$params['pageshow']));
		}
		$re['list']=$listdata;
		$re['total']=$total[0]['total'];
		$re['pagearr']=$pagearr;
		return $re;
	}
	
	//将  findAll 的结果集 取出记录，转为数组
	public static function toArr($result){
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
}
?>