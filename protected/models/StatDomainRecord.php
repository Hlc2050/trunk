<?php
class StatDomainRecord extends CActiveRecord{
	public function tableName() {
		return '{{stat_domain_record}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

}
