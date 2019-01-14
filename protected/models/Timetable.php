<?php
class Timetable extends CActiveRecord{
	public function tableName() {
		return '{{timetable}}';
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
     * 统计每日排期
     * @param $condition,$order
     * @return mixed
     */
    public function getDayCountTotal($condition,$join='',$order='') {
        if (!$condition) {
            $condition = '1';
        }
        $sql = 'SELECT time,SUM(count) as day_count FROM timetable as a '.$join.'
                WHERE 1 '.$condition.' and a.count>=0 and a.status=0 
                GROUP BY time '.$order;
        $result = Yii::app()->db->createCommand($sql)->queryAll();
	    return $result;
    }
    /**
     * 查询排期表数据
     * @param $select,$condition,$order
     * @return mixed
     */
    public function getTimetableDate($select,$condition,$order='') {
        if (!$condition) {
            $condition = '1';
        }
        if (!$select) {
            $select = '*';
        }
        $sql = 'SELECT '.$select.' FROM timetable 
                WHERE '. $condition . $order;
        $result = Yii::app()->db->createCommand($sql)->queryAll();
        return $result;
    }
    /**
     * 获取非推广状态微信号及其排期
     */
    public function getAotuAddWechat($begin_time,$end_time) {
        $sql = " SELECT t.wid,t.status,ut.type_id FROM timetable as t
                  LEFT JOIN user_timetable_type as ut on t.wid = ut.wid and ut.start_time = ".$begin_time." and ut.end_time = ".$end_time."
         WHERE t.status<> 0 and t.time = ( SELECT MAX(time) FROM timetable where time < ".$begin_time.")";
        $result = Yii::app()->db->createCommand($sql)->queryAll();
        return $result;
    }
    /**
     * 获取周日期
     * @param $date日期
     */
    public function getWeekDate($date='') {
        if ($date) {
            $week = date('w',strtotime($date));
        } else {
            $week = date('w');
        }
        // 周一
        $star_time = date('Y-m-d', strtotime('+' . 1 - $week . ' days'));
        $date = array();
        for ( $i = 0; $i <= 6; $i++) {
            $date[$i] = date('Y-m-d',strtotime($star_time)+$i*24*60*60);
        }
        return $date;
    }
    /**
     * 生成时间段内排期数值
     * @param $start_date 开始日期
     * @param $end_date  结束日期
     * @param $count  数值
     * @param $type 排期类行 1每天 2单排 3双排
     * @param $add_type 0全部排期,1普通排期
     * @return array
     */
    public function createDayCount($start_date,$end_date,$count,$type=1,$add_type=0) {
        $date_num = array_reverse(PartnerCost::model()->getDateInfoTwo($start_date,$end_date));
        $date_taimetable = array();
        for ($i = 0; $i < count($date_num); $i++) {
            $new_time = intval(date('d', $date_num[$i]));
            if( $add_type == 1 && $date_num[$i] < strtotime(date('Y-m-d'), time())) {
                $date_count = '-1';
            } else {
                switch ($type) {
                    case 1://每天
                        $date_count = $count;
                        break;
                    case 2;//单排
                        $date_count = ($new_time) % 2 == 1 ? $count : 0;
                        break;
                    case 3;
                        $date_count = ($new_time) % 2 == 0 ? $count : 0;
                        break;
                    default:
                        break;
                }
            }
            $date_taimetable[$i] = array(
                'time'=>$date_num[$i],
                'count'=>$date_count,
            );
        }
        return $date_taimetable;
    }
}
