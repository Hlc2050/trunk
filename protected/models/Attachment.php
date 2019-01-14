<?php
class Attachment extends CActiveRecord{
	public function tableName() {
		return '{{resource_list}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public static function get_table($tagid){
		return substr(strval($tagid),-1,1);
	}
	//取得图片的缩略图(根据上传的名字前加 thumb_ 就可以，上传的时候已经生成了) 
	public static function simg($img){
		$img=preg_replace('~([^\/][\w]{6,}\.)~','thumb_$1',$img);
		return $img;
	}
	
	//
	public static function get_attachment($tagid){
		//$tableid=self::get_table($tagid);
	    //$table='ffgh_attachment_'.$tableid;
	    $table="resource_list";
	    $data=Yii::app()->db->createCommand()->from($table)->where("fromid='".$tagid."'")->order("resource_order,resource_id ")->queryAll();
		$re=array();
		foreach($data as $r){
			$r['resource_url']=$r['resource_url'];
			$r['resource_id']=$r['resource_id'];
			$r['resource_order']=$r['resource_order'];
			$re[]=$r;
		}
	    return $re;		
	}
	
	
	//通用保存图片集，上传图片的时候就已经会再资源表里 写入一条记录，这里只需要通过 resource_url 去查找 附件，然后修改 fromid
	public function save_attachment($tagid,$relation_arr){
		$relation_data=array();
		$tableid=self::get_table($tagid);
		//$table='ffgh_attachment_'.$tableid;
		$table="resource_list";
		if(isset($relation_arr['resource_id'])){
			foreach($relation_arr['resource_id'] as $k=>$r){
				if(!trim($relation_arr['resource_url'][$k])){
					continue;
				}
				$relation_data[]=array(
					'resource_id'=>intval($r),
					'resource_order'=>$relation_arr['resource_order'][$k],
					'resource_url'=>urldecode($relation_arr['resource_url'][$k]),
					'mark'=>urldecode(isset($relation_arr['mark'][$k])?intval($relation_arr['mark'][$k]):0),
				);
			}
		}
		$tag_id=$tagid;
		$oldarr=self::get_attachment($tagid); 
		//print_r($relation_data);print_r($oldarr);die();
		foreach($oldarr as $r){  //遍历 清除不存在的 数据
			$in=0;//老的数组 的信息ID 是否 在新的数组上
			$fdata=array();
			foreach($relation_data as $r2){
				if($r2['resource_id']==$r['resource_id'] ){
					$in=1;
					$fdata=$r2;
					break;
				}	
			}	
			$post=Dtable::model($table)->findByAttributes(array('resource_id'=>$r['resource_id']));
			if($in==0){		
				$post->delete();
			}else{
				$post->resource_url=$fdata['resource_url'];
				$post->resource_order=$fdata['resource_order'];
				$post->mark=$fdata['mark'];
				$post->save();
			}
		}
		foreach($relation_data as $r){
			$post=Dtable::model($table)->findByAttributes(array('fromid'=>$tagid,'resource_url'=>$r['resource_url']));
			if(!$post){
				$post=new Dtable($table);		
				$post->resource_url=$r['resource_url'];
			}
			$post->fromid=$tagid;
			$post->mark=$r['mark'];
			$post->resource_order=$r['resource_order'];
			$post->save();
		}
		return true;
	}
	
	
	
	
}