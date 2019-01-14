<?php
class AdminRole extends CActiveRecord{
	public function tableName() {
		return '{{cservice_role}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public static function get_role(){
		$sql="select * from cservice_role  ";
		$data=Yii::app()->db->createCommand($sql)->queryAll();
		return $data;
	
	}

	public function get_user_roles_name(array $arr){
		$nameArr=array();
		foreach ($arr as $val){
			$nameArr[] = $this->findByPk($val['role_id'])->role_name;
		}
		return $nameArr;
	}
	
}