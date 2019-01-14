<?php
class Params extends CActiveRecord{
	public function tableName() {
		return '{{params}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

}
