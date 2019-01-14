<?php
class MiniAppsManage extends CActiveRecord{
	public function tableName() {
		return '{{mini_apps_manage}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

}
