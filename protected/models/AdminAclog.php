<?php
class AdminAclog extends CActiveRecord{
	public function tableName() {
		return '{{cservice_aclog}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	//插入后台操作日志
	public function logs($logs_content){
		$post=new AdminAclog();
		$post->sno=1;
		$post->log_time=time();
		$post->log_ip=helper::getip();
		$post->log_details=$logs_content;
		$post->save();

	}

	
}