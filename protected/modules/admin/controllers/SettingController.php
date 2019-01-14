<?php
class SettingController extends AdminController{
	public function actionIndex(){
		$this->render('index');
	}
	public function actionSave(){		
		$file=dirname(__FILE__)."/../../../config/params.php";
		$config=helper::get_contents($file);
		//基本设置
		foreach($_POST as $k=>$post){
			if(!is_array($post)) continue;
			foreach($post as $k2=>$s){
				$s=str_replace("'",'',$s);
				$config=preg_replace("~('".$k."'[\w\W]*?)'".$k2."'=>'(.*?)'~","$1'".$k2."'=>'".$s."'",$config);
			}
		}
		helper::file_save($file,$config);
		
		$this->msg(array('state'=>1));
	}
}
?>