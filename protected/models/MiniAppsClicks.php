<?php
class MiniAppsClicks extends CActiveRecord{
	public function tableName() {
		return '{{mini_apps_clicks}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

}
