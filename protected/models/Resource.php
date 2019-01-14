<?php
class Resource extends CActiveRecord{
	public function tableName() {
		return '{{resource_list}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	
}