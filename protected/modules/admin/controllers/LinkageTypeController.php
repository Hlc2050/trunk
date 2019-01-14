<?php

/**
 */

class LinkageTypeController extends AdminController{
	public function actionIndex(){
		
		//搜索
		$params['where']='';
		if($this->get('search_type')=='keys' && $this->get('search_txt')){
			$params['where'] .=" and(a.linkage_type_name like '%".$this->get('search_txt')."%') ";
		}else if($this->get('search_type')=='id'  && $this->get('search_txt')){ //网点ID
			$params['where'] .=" and(a.linkage_type_id=".intval($this->get('search_txt')).") ";
		}
		$params['order']="  order by a.linkage_type_id      ";
		$params['pagesize']=Yii::app()->params['management']['pagesize'];
		$params['pagebar']=1;
		$params['smart_order']=1;
		$page['listdata']=Dtable::model(LinkageType::model()->tableName())->listdata($params);
		$this->render('index',array('page'=>$page));		
	}		
			
	public function actionUpdate(){ 
		$page=array();
		$id=$this->get('id',0);
		//显示表单
		if(!isset($_POST['id'])){
			//如果有get.id为修改，否则判断为新增;
			if($id){
							
				$info=$this->toArr(LinkageType::model()->findByPk($id));
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
			$field['linkage_type_name']=$this->post('linkage_type_name');
			if($field['linkage_type_name']==''){
				$this->msg(array('state'=>0,'msgwords'=>'类别分类名称不能为空'));
			}
			$field['linkage_type_name']=$this->post('linkage_type_name');
			$field['linkage_type_order']=$this->post('linkage_type_order',0);
			//如果有post.id 为保存修改，否则为保存新增
			if($id){
				
				$dbresult=LinkageType::model()->updateAll($field,"linkage_type_id=$id");  //修改记录
				$msgarr=array('state'=>1,'url'=>$this->createUrl('linkageType/index').'?p='.$_GET['p'].''); //保存的话，跳转到之前的列表
				$logs='修改了类别分类 ID:'.$id.''.$field['linkage_type_name'].' ';
			}else{	
				
				$post=$this->data('LinkageType',$field);
				$dbresult=$post->save();
				$id=$post->primaryKey;
				$msgarr=array('state'=>1);  //新增的话跳转会添加的页面
				$logs="添加了类别分类ID：$dbresult".$field['linkage_type_name'];
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
			$m=LinkageType::model()->findByPk($id);
			if(!$m) continue;
			$m->delete();
			
		}
		$this->logs('删除了类别分类ID（'.$idstr.'）');
		$this->msg(array('state'=>1));	
	}
	public function ActionSaveOrder(){
		
		$listorders=$this->get('listorders',array());
		foreach($listorders as $id=>$order){
			$m=LinkageType::model()->findByPk($id);
			if(!$m) continue;
			$m->linkage_type_order=$order;
			$m->save();		
		}
		$this->logs('修改了类别分类的排序');
		$this->msg(array('state'=>1));
	}
	/*
	 重置联动菜单的层级
	*/
	public function actionResetLinkageDeep(){
		$linkage_type_id=intval($this->get('linkage_type_id'));
		$sql="update linkage set linkage_deep=1 where parent_id=0 ";
		Yii::app()->db->createCommand($sql)->execute();
		$sql="select * from linkage where parent_id=0 and linkage_type_id=$linkage_type_id";
		$a['list']=Yii::app()->db->createCommand($sql)->queryAll();  //更改数字就可以	
		foreach($a['list'] as $r){
			$sql="update linkage set linkage_deep=2 where parent_id=".$r['linkage_id']." ";
			Yii::app()->db->createCommand($sql)->execute();
			$sql=" select * from linkage where parent_id=".$r['linkage_id']." ";
			$a['list']=Yii::app()->db->createCommand($sql)->queryAll();
			foreach($a['list'] as $r){
				$sql="update linkage set linkage_deep=3 where parent_id=".$r['linkage_id']." ";
				Yii::app()->db->createCommand($sql)->execute();
				$sql=" select * from linkage where parent_id=".$r['linkage_id']." ";
				$a['list']=Yii::app()->db->createCommand($sql)->queryAll();
				foreach($a['list'] as $r){
					$sql="update linkage set linkage_deep=4 where parent_id=".$r['linkage_id']." ";
					Yii::app()->db->createCommand($sql)->execute();
					$sql=" select * from linkage where parent_id=".$r['linkage_id']." ";
					$a['list']=Yii::app()->db->createCommand($sql)->queryAll();
					foreach($a['list'] as $r){
						$sql="update linkage set linkage_deep=5 where parent_id=".$r['linkage_id']." ";
						Yii::app()->db->createCommand($sql)->execute();
						$sql=" select * from linkage where parent_id=".$r['linkage_id']." ";
						$a['list']=Yii::app()->db->createCommand($sql)->queryAll();
					}
				}
				
			}
			
		}
		
		$this->msg(array('state'=>1));
	}

}
?>