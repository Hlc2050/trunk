<?php
class WeChatChangeLog extends CActiveRecord{
	public function tableName() {
		return '{{wechat_change_log}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

}