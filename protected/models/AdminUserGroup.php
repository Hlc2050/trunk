<?php
class AdminUserGroup extends CActiveRecord{
	public function tableName() {
		return '{{cservice_groups}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

	/** @params int $id 用户id
	 *  @params array $groups 分组id的数组
	 * */
	public function save_groups($id,$groups=array()){
		if(!is_array($groups)) return false;
		$arr001=$this->findAll("sno=$id");
		$idarr=array();
		foreach($arr001 as $r){
			$idarr[]=$r['groupid'];
		}
		foreach($idarr as $idw){  //遍历 清除不存在的 数据
			if(!in_array($idw,$groups)){ //老的数组 的信息ID 是否 在新的数组上
				$mg=$this->findByAttributes(array('groupid'=>$idw,'sno'=>$id));
				if(!$mg) continue;
				$mg->delete();
			}
		}
		foreach($groups as $r){
			$post=$this->findByAttributes(array('sno'=>$id,'groupid'=>$r));
			if(!$post){
				$post=new $this;
				$post->sno=$id;
				$post->groupid=$r;
				$post->save();
			}
		}
		return true;
	}

	public function getUsersByGroups($groups){
		$userInfo = Dtable::toArr($this->model()->findAll("groupid in ($groups)"));
		$userArr=array();
		foreach ($userInfo as $v){
			$userArr[]=$v['sno'];
		}
		return array_unique($userArr);
	}

    /**
     * 部门查看权限
     * @param $type 是否不验证权限
     * @return array
     * author lxj
     */
    public function get_all_Group($type)
    {
        //
        if ($type == 1) {
            $groups = Dtable::toArr(AdminGroup::model()->findAll());
        } else {
            $uid=Yii::app()->admin_user->uid;
            //判断是否为超级管理员
            $is_super_admin = 0;
            if($uid==Yii::app()->params['management']['super_admin_id'] ) $is_super_admin = 1;
            //判断权限是否为超级管理员权限
            $urole = AdminUser::model()->get_user_role($uid);
            foreach ($urole as $val){
                if($val['role_id'] == 1) $is_super_admin = 1;
            }
            $groups = array();
            if ($is_super_admin == 1) {
                $groups = Dtable::toArr(AdminGroup::model()->findAll());
                return $groups;
            }

            //获取登录人员所在部门及以下部门
            $ugroups=AdminUser::model()->get_user_group($uid);
            $ugroupArr=$groupArr=array();
            foreach($ugroups as $r){
                $ugroupArr[]=$r['groupid'];
            }
            $mgroupArr = AdminUser::model()->get_manager_group($uid);
            $ugroupArr = empty($mgroupArr)?$ugroupArr:array_unique(array_merge($ugroupArr,$mgroupArr));
            foreach ($ugroupArr as $value){
                $groupArr = array_merge($groupArr,AdminGroup::model()->get_children_groups($value));
            }
            $groupArr = array_merge($groupArr,$mgroupArr);
            $groupStr = implode(',',array_unique($groupArr));
            if ($groupStr) {
                $groups = Dtable::toArr(AdminGroup::model()->findAll('groupid in ('.$groupStr.')'));
            }
        }
        return $groups;
    }

   //获取权限标识
    public function getGroupId($tg_uid){
        $data = $this->findAll();
        $arr = array();
        $ret = array();
        foreach ($data as $val){
            $arr[$val['sno']][] = $val['groupid'];
        }

        if($tg_uid == 1) {
            $temp = AdminGroup::model()->findAll('manager_id='.$tg_uid);
            foreach ($temp as $val){
                if($val['manager_id'] == $tg_uid){
                    $ret[] = $val['groupid'];
                }
            }
        }else{
            foreach ($arr as $key=>$value){
                if($key == $tg_uid){
                    $ret[] = $arr[$key][0];
                }
            }
        }

        return $ret;
    }

    //获取该部门的组员
    public function getUsers($group_id){
        $data = $this->findAll();
        $arr = array();

        foreach ($data as $val){
            if($group_id == $val['groupid']){
                $arr[] = $val['sno'];
            }

        }
        return $arr;
    }
}
