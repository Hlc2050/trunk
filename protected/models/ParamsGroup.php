<?php
class ParamsGroup extends CActiveRecord{
	public function tableName() {
		return '{{params_group}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

	public function getAll(){
		$a=$this->findAll();
		return helper::dbobjectToArray($a);
	}

}
