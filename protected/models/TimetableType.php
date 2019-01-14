<?php
class TimetableType extends CActiveRecord{
	public function tableName() {
		return '{{timetable_type}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
    public function getTypeCounts($type_id){
        if(!$type_id) return '没有选择排期类型！';
        $counts = $this->find('type_id=:type_id',array(':type_id'=>$type_id));
        if(!$counts) return '该排期类型没有数值！';
        $counts_array = explode(',',$counts);
        $res = array();
        foreach ($counts_array as $value){
            $res[] = array('count'=>$value, 'value'=>$value);
        }
        return $res;
    }
}
