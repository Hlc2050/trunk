<?php
class AdminGroupRole extends CActiveRecord{
	public function tableName() {
		return '{{cservice_group_role}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public static function get_group_role($groupid){
		$sql="select * from cservice_group_role where groupid in('$groupid') ";
		$data=Yii::app()->db->createCommand($sql)->queryAll();
		return $data;		
	}
	//将角色的查询结果转换成权限数组集
	public static function role_arr($data){
		$re=array();
		foreach($data as $r){
			$autharr=AdminRoleAuthority::get_role_auth($r['role_id']);
			
			foreach($autharr as $r2){
				if($r2['type']==1){
					$m=AdminModules::model()->findByPk($r2['authority_id']);
					if(!$m) continue;
					$a['authority_name']=$m->mname;
					$a['authority_id']=$m->id;
					$a['param_type']=1;
					$a['param_name']='';
					$a['param_value']='';
				}else if($r2['type']==2){
					$m=AdminFunction::model()->findByPk($r2['authority_id']);
					if(!$m) continue;
					$a['authority_name']=$m->authority_id;
					$a['authority_id']=$m->id;
					$a['param_type']=2;
					$a['param_name']=$m->param_name;
					$a['param_value']=$m->param_value;
				}
				
				$re[]=$a;
			}
		}
		return $re;		
	}
	
	
	
}