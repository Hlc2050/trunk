<?php
class StatChannel extends CActiveRecord{
	public function tableName() {
		return '{{stat_channel}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

}
