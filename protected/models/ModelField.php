<?php
class ModelField extends CActiveRecord{
	public function tableName() {
		return '{{model_field}}';
	}
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}	
	public function get_model_field($model_id){	
		$sql="select * from model_field where model_id=$model_id order by field_order,field_id  ";
		$d=Yii::app()->db->createCommand($sql)->queryAll();
		$re=array();
		foreach($d as $b){
			$b['setting']=json_decode($b['setting'],true);
			$b['setting']['default_value']=isset($b['setting']['default_value'])?urldecode($b['setting']['default_value']):'';
			$b['setting']['ini_value']=isset($b['setting']['ini_value'])?urldecode($b['setting']['ini_value']):'';
			$re[]=$b;
		}
		return $re;
	}

	
}