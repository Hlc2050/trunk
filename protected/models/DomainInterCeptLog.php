<?php
class DomainInterCeptLog extends CActiveRecord{
	public function tableName() {
		return '{{domain_intercept_log}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

}
