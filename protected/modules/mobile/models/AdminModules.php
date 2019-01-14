<?php
class AdminModules extends CActiveRecord{
	public $menuCates=array();
	public function tableName() {
		return '{{cservice_modules}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	
	public function __construct($scenario='insert'){
		parent::__construct($scenario);
		$tmp_cates=$this->getAllModules();
		foreach($tmp_cates as $r){
			$this->menuCates[$r['id']]=$r;
		}
	}
	
	public static function getAllModules(){
		$sql="select * from cservice_modules order by displayorder,id asc   ";
		$data=Yii::app()->db->createCommand($sql)->queryAll();
		return $data;
	}
	
	
	//取分类的父分类（所有上级），返回数组
	public function cate_father($id){//echo($id.'<br>');
		$top_cate=array();
		array_push($top_cate,$this->menuCates[$id]);
		$tmp=array(0=>array());
		$parent_id=$this->menuCates[$id]['parent_id'];
		if(intval($parent_id)>0){
			$top_cate=$this->cate_father($parent_id);
			array_push($top_cate,$this->menuCates[$id]);
		}
		return $top_cate;
	}
	//取分类的子分类，返回数组，树状
	public function cate_son($id=0){
		$ret=array();
		foreach($this->menuCates as $c){
			if($c['parent_id']==$id){
				$c['son']=$this->cate_son($c['id']);
				array_push($ret,$c);
			}
		}
		//$ret=helper::array_sort($ret,'displayorder');
		return $ret;
	}
	//取某个分类所有的子分类，作为数组，不是树状
	function cate_son_arr($parent_id){
		$bb=array();
		foreach($this->menuCates as $c){
			if($c['parent_id']==$parent_id){
				$bb[]=$c['id'];
				$son=$this->cate_son_arr($c['id']);
				$bb=array_merge($bb,$son);
			}
		}
		return $bb;
	}
	//取分类的平级分类，返回数组
	public function cate_brother($id){
		$pson=$this->cate_son($this->menuCates[$id]['parent_id']);
		$brother=array();
		foreach($pson as $c){
			array_push($brother,$c);
		}
		return $brother;
	}
	
	
	public  function category_tree($select_cate_id){
		$catearr=$this->menuCates;
		$menuCates=array();
		foreach($catearr as $r){
			$new_r['id']=$r['id'];
			$new_r['parentid']=$r['parent_id'];
			$new_r['cname']=$r['name'];
			$menuCates[]=$new_r;
		}
		$str  = "<option value=\$id \$selected>\$spacer\$cname</option>";
		$tree=new tree();
		$tree->init($menuCates);
		//print_r($menuCates);
		$category_code = $tree->get_tree(0, $str,$select_cate_id);
		return $category_code;
	
	}
	
}