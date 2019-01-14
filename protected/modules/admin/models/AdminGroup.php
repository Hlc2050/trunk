<?php

class AdminGroup extends CActiveRecord
{
    public function tableName()
    {
        return '{{cservice_group}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function category_tree($select_cate_id)
    {
        $catearr = Dtable::toArr(AdminGroup::model()->findAll());
        $categorys = array();
        foreach ($catearr as $r) {
            $new_r['id'] = $r['groupid'];
            $new_r['parentid'] = $r['parent_id'];
            $new_r['groupname'] = $r['groupname'];
            $categorys[] = $new_r;
        }
        $str = "<option value=\$id \$selected>\$spacer\$groupname</option>";
        $tree = new tree();
        $tree->init($categorys);
        //print_r($categorys);
        $category_code = $tree->get_tree(0, $str, $select_cate_id);
        return $category_code;

    }

    public function getGroup()
    {
        $sql = "select a.*,b.csname,b.csname_true from cservice_group as a left join cservice as b on b.csno=a.manager_id ";
        $a = Yii::app()->db->createCommand($sql)->queryAll();
        return $a;
    }


    /**
     * 找到该部门下的所有部门
     * @param $group_id int 部门id
     * author: yjh
     * date: 2017-2-16
     */
    public function get_children_groups($group_id)
    {
        $childrenGroupArr = array(0=>$group_id);
        $m = $this::model()->findAll("parent_id=$group_id");

        if (empty($m)) return $childrenGroupArr;

        foreach ($m as $r) {
            $mgroupArr = $this->get_children_groups($r->groupid);
            $childrenGroupArr = array_merge($childrenGroupArr,$mgroupArr);
        }
       

        return $childrenGroupArr;
    }

    //通过groupid获取部门名字
    public function getGroupName($group_id){
        $ret = $this->findByPk($group_id);
        $data = $ret['groupname'];

        return $data;
    }

    public function getGroupId($manager_id){
        $ret = $this->findAll('manager_id='.$manager_id);
        $temp = '';
        foreach ($ret as $value){
            $temp .=$value['groupid'].',';
        }
        $arr = rtrim($temp,',');

        return $arr;
    }
}