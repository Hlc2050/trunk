<?php
class AdminRoleAuthority extends CActiveRecord{
	public function tableName() {
		return '{{cservice_role_authority}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public static function get_role_auth($role_id){	
		$sql="select * from cservice_role_authority where role_id='$role_id' ";
		$data=Yii::app()->db->createCommand($sql)->queryAll();
		return $data;
	}
	
}