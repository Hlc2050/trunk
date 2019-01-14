<?php
class UploadModule extends  CWebModule{
	public function init(){
		$this->setImport(array(
				'upload.models.*',
				'upload.components.*',
				'upload.widget.*',
		));
		
		Yii::app()->errorHandler->errorAction = '/upload/site/error';
		Yii::app()->defaultController = 'upload/site';
		Yii::app()->user->loginUrl = '/upload/site/login';
	}
	
}