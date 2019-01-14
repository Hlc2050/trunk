<?php
class FileListController extends AdminController{
	public function actionIndex(){
		$page['files']=array();
		if(!isset($_GET['basedir'])) $_GET['basedir']='';
		$basedir=dirname(__FILE__).'/../../../../themes/default/views/'.$_GET['basedir'];
		$filelisting=array();
		$handle=opendir($basedir);
		while ($file = readdir($handle)) {
			$f=array();
			if ($file=="." or $file==".." or $file=='admin') {
				 
			}else{
				$f['file_time']=filemtime($basedir.'/'.$file);
				$f['file_name']=$file;
				$f['is_forder']=is_dir($basedir.'/'.$file)?1:0;
				$filelisting[]=$f;
			}
		}
		$page['files']['list']=$filelisting;//print_r($page);
		$page['basedir']=$_GET['basedir'];
		$this->render('index',array('page'=>$page));
	}
	public function actionGetTplList(){
		$basedir=dirname(__FILE__).'/../../../../themes/default/views/common/listvar';
		$filelisting=array();
		$handle=opendir($basedir);
		while ($file = readdir($handle)) {
			$f=array();
			if ($file=="." or $file==".." or $file=='admin') {
					
			}else{
				if(is_dir($basedir.'/'.$file)) continue;
				$f['time']=filemtime($basedir.'/'.$file);
				$f['file']=$file;
				$style=$this->getName($basedir.'/'.$file);
				if(!isset($style['name'])){
					$style['name']=$file;
				}
				$f['name']=$style['name'];	
				$filelisting[]=$f;
			}
		}
		$list=$filelisting;
	    $this->msg(array('state'=>1,'list'=>$list,'type'=>'json'));
	}
	private function getName($file){
		$a=file_get_contents($file);
		if(!$a){
			return '';
		}
		if(!preg_match('~\/\*(\{[\w\W]*?\})\*\/~',$a,$result)){
			return '';
		}
		$data=$result[1];
		$style=json_decode($data,1);
		if(!$style){
			return '';
		}
		return $style;
	}
	
}