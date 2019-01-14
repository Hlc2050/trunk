<?php
class PiwikSite extends CActiveRecord{
	public function tableName() {
		return '{{piwik_site}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

}
