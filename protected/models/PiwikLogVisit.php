<?php
class PiwikLogVisit extends CActiveRecord{
	public function tableName() {
		return '{{piwik_log_visit}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

}
