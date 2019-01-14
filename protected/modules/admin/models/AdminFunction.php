<?php
class AdminFunction extends CActiveRecord{
	public function tableName() {
		return '{{cservice_function}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	
	
	public function getAllName($module_id){
		$data=helper::dbobjectToArray($this->findAllByAttributes(array('module_id'=>$module_id)));
		$nameArr=array();
		foreach($data as $r){
			$nameArr[]=$r['function_name'];
		}
		return $nameArr;
	}
	
	public function getFunctions($module_id){
		$data=helper::dbobjectToArray($this->findAllByAttributes(array('module_id'=>$module_id)));

		return $data;
	}
}