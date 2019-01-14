<?php
class LinkageType extends CActiveRecord{
	public function tableName() {
		return '{{linkage_type}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

}
