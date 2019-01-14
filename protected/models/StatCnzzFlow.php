<?php
class StatCnzzFlow extends CActiveRecord{
	public function tableName() {
		return '{{stat_cnzz_flow}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

}
