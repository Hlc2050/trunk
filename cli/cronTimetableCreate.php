<?php
/**
 * Created by PhpStorm.
 * User: lxj
 * Date: 2017/12/1
 * Time: 11:35
 */
include(dirname(__FILE__) . '/init.php');
include(dirname(__FILE__) . '/dbMysql.php');
//本页为在CLI模式下执行 该文件的
//error_reporting(E_ALL & ~E_STRICT);  //php 5.4 以上 对方法重写参数需要一致，不一致需要加上 默认值,问题出现在 Dtable里面
$dbm = new dbMysql($config['db_mysql']['default']);
$createCollect = new timetableCreate($dbm);
$createCollect->run();

echo '---------------------------completed!';
exit;
class timetableCreate
{

    function __construct($dbm)
    {
        $this->dbm = $dbm;
        $week = date('w',time());
        if ($week != '1') {
            $begin_sql_date = strtotime(date('Ymd',strtotime('-'.$week .'day')))-6*24*60*60;
        } else {
            $begin_sql_date = strtotime(date('Ymd',strtotime('-1 week')));
        }
        $end_sql_date = $begin_sql_date+6*24*60*60;
        // 本周日期起始
        $begin_insert_day = $end_sql_date+24*60*60;
        $end_insert_day = $begin_insert_day+6*24*60*60;
        $this->begin_sql_date = $begin_sql_date;
        $this->end_sql_date = $end_sql_date;
        $this->begin_insert_day = $begin_insert_day;
        $this->end_insert_day = $end_insert_day;
    }
    public function run()
    {
        $insert_data = $this->getInsertWeekData();
        if ($insert_data) {
            $new_insert_data = $this->insertUserTable($insert_data);
            $keys = array_keys($new_insert_data[0]);
            $table_name='timetable';
            $ret=$this->insertTableData($table_name,$keys,$new_insert_data);
            if($ret) {
                echo 'Success!';
            }else{
                echo 'Failed!';
            }
        }
    }

    /**
     *  获取需要插入期排期表的微信号数据
     */
    private function getInsertWeekData()
    {

        // 获取非推广状态微信号
        $sql = 'select wid from timetable where time = '.$this->end_sql_date .' and status <> 0';
        $unpro_wechats = $this->dbm->doSql($sql);
        // 获取本周已有排期微信号
        $sql = 'select wid from user_timetable_type where start_time = '.$this->begin_insert_day.' and end_time = '.$this->end_insert_day;
        $exit_wechats = $this->dbm->doSql($sql);
        $un_insert_wx = array_column(array_merge($unpro_wechats,$exit_wechats),'wid');
        $un_insert_wx = array_unique($un_insert_wx);
        // 获取上一周期微信号排期数据
        $sql = 'select type_id,count,wid from user_timetable_type where start_time = '.$this->begin_sql_date.' and end_time = '.$this->end_sql_date;
        $table_type = $this->dbm->doSql($sql);
        // 过滤不可自动创建排期的微信号后的数据
        $filter_data = array();
        foreach ($table_type as $value) {
            if (!in_array($value['wid'],$un_insert_wx)) {
                $filter_data[] = $value;
            }
        }
        return $filter_data;
    }
    /**
     * 插入user_timetable_type表,并返回插入排期表数据
     * @param array $insert_data
     * @return array
     */
    private function insertUserTable($insert_data)
    {
        $new_array = array();
        foreach ($insert_data as $key=>$value) {
            $row = array(
                'wid'=>$value['wid'],
                'count'=>$value['count'],
                'type_id'=>$value['type_id'],
                'start_time'=>$this->begin_insert_day,
                'end_time'=>$this->end_insert_day,
            );
            $user_type_id = $this->insertRow($row);
            $new_array = $this->getDateCount($user_type_id,$row);
        }
        return $new_array;
    }

    /**
     * 批量插入数据库
     * @param array $rows
     * @param array $arr
     * @return string
     */
    private function insertTableData($table, array $rows, array $arr)
    {
        $max_insert_num = 100;
        $num = count($arr);
        $t_arr = $arr;
        if ($num >= $max_insert_num) {
            $arr = array_splice($t_arr, 2);

        }
        $sql = 'INSERT INTO ' . $table . ' (';
        foreach ($rows as $value) {
            $sql .= '`' . $value . '`,';
        }
        $sql = rtrim($sql, ',');
        $sql .= ') VALUES ';
        foreach ($t_arr as $k => $item) {
            $sql .= '(';
            foreach ($item as $value) {
                $sql .= '\'' . $value . '\',';
            }
            $sql = rtrim($sql, ',');
            $sql .= '),';
        }
        $sql = rtrim($sql, ',');
        try {
            $this->dbm->doSql($sql);

            if ($num >= $max_insert_num) {
                $this->insertTableData($table, $rows, $arr);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     *  插入用户每周排期表
     * @param array $row
     * @return int
     */
    private function insertRow($row)
    {
        $this->dbm->insert('user_timetable_type',$row);
        $sql = "select id from user_timetable_type where wid = ".$row['wid']." and start_time = ".$this->begin_insert_day." and end_time = ".$this->end_insert_day;
        $ret =  $this->dbm->doSql($sql);
        return $ret[0]['id'];
    }

    /**
     * 获取插入数据
     * @param $user_type_id int
     * @param $row array
     * $row['wid'] 微信号id
     * $row['count'] 排期数值
     * $row['type_id'] 排期类型id
     */
    private function getDateCount($user_type_id,$row)
    {
        static $date_taimetable = array();
        for ($i = 0; $i < 7; $i++) {
            $new_date = $this->begin_insert_day + $i*24*60*60;
            $new_time = intval(date('d', $new_date));
            switch ($row['type_id']) {
                case 1://每天
                    $date_count = $row['count'];
                    break;
                case 2;//单排
                    $date_count = ($new_time) % 2 == 1 ? $row['count'] : 0;
                    break;
                case 3;
                    $date_count = ($new_time) % 2 == 0 ? $row['count'] : 0;
                    break;
                default:
                    break;
            }
            $date_taimetable[] = array(
                'time'=>$new_date,
                'count'=>$date_count,
                'wid'=>$row['wid'],
                'user_type_id'=>$user_type_id,
                'status'=>0,
                'add_time'=>time(),
                'upd_time'=>time(),
            );
        }
        return $date_taimetable;
    }

}