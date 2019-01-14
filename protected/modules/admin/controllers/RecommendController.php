<?php
class RecommendController extends AdminController{
	public function actionIndex(){
		
		$params['order']="  order by a.recommend_order desc      ";
		$params['pagesize']=Yii::app()->params['management']['pagesize'];
		$params['pagebar']=1;
		$params['smart_order']=1;
		$page['listdata']=Dtable::model('recommend')->listdata($params);
		$this->render('index',array('page'=>$page));
		
	}	
	public function actionUpdate(){ 
		$page=array();
		$id=$this->get('id',0);
		//显示表单
		if(!isset($_POST['id'])){
			//如果有get.id为修改，否则判断为新增;
			if($id){
							
				$info=$this->toArr(Recommend::model()->findByPk($id));
				if(!$info){
					$this->msg(array('state'=>0,'msgwords'=>'文档不存在'));
				}
				$page['info']=$info;
				//print_r($page['recommend']);
			}else{
				
			}
	
		}else{//判断为保存
			$id=$_POST['id']=intval($_POST['id']);
			//处理需要的字段
			$field=array();
			$field['recommend_name']=$this->post('recommend_name');
			if($field['recommend_name']==''){
				$this->msg(array('state'=>0,'msgwords'=>'推荐位名称不能为空'));
			}
			$field['table_name']=$this->post('table_name');
			$field['id_field']=$this->post('id_field');
			$field['name_field']=$this->post('name_field');
			$field['inid']=$this->post('inid');
			$field['recommend_order']=intval($this->post('recommend_order'));
			//如果有post.id 为保存修改，否则为保存新增
			if($id){
				
				$dbresult=Recommend::model()->updateAll($field,"recommend_id=$id");  //修改记录
				$msgarr=array('state'=>1,'url'=>$this->createUrl('recommend/index').'?p='.$_GET['p'].''); //保存的话，跳转到之前的列表
				$logs='修改了推荐位 ID:'.$id.''.$field['recommend_name'].' ';
			}else{	
				
				$post=$this->data('Recommend',$field);
				$dbresult=$post->save();
				$id=$post->primaryKey;
				$msgarr=array('state'=>1);  //新增的话跳转会添加的页面
				$logs="添加了推荐位ID：$dbresult".$field['recommend_name'];
			}
			if($dbresult===false){
				//错误返回
				$this->msg(array('state'=>0));
			}else{
				//新增和修改之后的动作
	
				$this->logs($logs);
				//成功跳转提示
				$this->msg($msgarr);
			}
	
		}
		$this->render('update',array('page'=>$page));
	}
	
	public function actionDelete(){
		
		$idstr=$this->get('ids');
		$ids=explode(',',$idstr);
		foreach($ids as $id){	
			$m=Recommend::model()->findByPk($id);
			if(!$m) continue;
			$m->delete();
			
		}
		$this->logs('删除了推荐位ID（'.$idstr.'）');
		$this->msg(array('state'=>1));	
	}
	public function ActionSaveOrder(){
		
		$listorders=$this->get('listorders',array());
		foreach($listorders as $id=>$order){
			$m=Recommend::model()->findByPk($id);
			if(!$m) continue;
			$m->recommend_order=$order;
			$m->save();		
		}
		$this->logs('修改了推荐位的排序');
		$this->msg(array('state'=>1));
	}
	public function actionGetRecommendData(){
		$page['recommend_data']=array();
		$table_name=isset($_GET['table_name'])?$_GET['table_name']:'';
		$id_field=isset($_GET['id_field'])?$_GET['id_field']:'';
		$name_field=isset($_GET['name_field'])?$_GET['name_field']:'';
		$inid=isset($_GET['inid'])?$_GET['inid']:'';
		if($table_name && $id_field && $name_field && $inid){
			$a=$this->query("select $id_field,$name_field from $table_name where $id_field in($inid) order by substring_index('$inid',$id_field,1) desc");
			$page['recommend_data']=helper::array_sort($a,'info_id','desc');
			//$page['recommend_data']=$a;
		    	
		}	
		echo json_encode($page['recommend_data']);
		die();
	}
	public function actionGetRecommendList(){
		$re['list']=Recommend::model()->RecommendList();
		die(json_encode($re));
	}
}
?>