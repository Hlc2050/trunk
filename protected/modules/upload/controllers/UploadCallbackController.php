<?php
class UploadCallbackController extends CController {
	public function actionIndex(){
		if(!isset($_GET['params'])) $_GET['params']='';
		$params=$_GET['params'];
		$params=preg_replace('~(\\\")~','"',$params);
		echo('<script>window.parent.callback_upload(\''.$params.'\');</script>');
	}
}