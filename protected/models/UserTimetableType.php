<?php
class UserTimetableType extends CActiveRecord{
	public function tableName() {
		return '{{user_timetable_type}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
    public function primaryKey()
    {
        // 对于复合主键，要返回一个类似如下的数组
         return array('id');
    }
    /**
     * 获取微信号最后排期周期数据
     * @param $condition
     * @param $start_date
     * @return mixed
     */
    public function getWechatLastTimetable($condition,$start_date) {
        if (!$condition) {
            $condition = '1';
        }
        $sql = 'SELECT t.*,tt.status,tt.time FROM user_timetable_type as t
                LEFT JOIN timetable as tt on tt.user_type_id = t.id and tt.time = t.end_time
                WHERE '. $condition . ' and end_time = (select Max(end_time) from user_timetable_type as mt where t.wid = mt.wid and mt.end_time < '.$start_date.')';
        $result = Yii::app()->db->createCommand($sql)->queryAll();
        return $result;
    }
}
