<?php
include(dirname(__FILE__) . '/init.php');
include(dirname(__FILE__) . '/dbMysql.php');
ini_set('max_execution_time', '0');
//本页为在CLI模式下执行 该文件的
//error_reporting(E_ALL & ~E_STRICT);  //php 5.4 以上 对方法重写参数需要一致，不一致需要加上 默认值,问题出现在 Dtable里面
$dbm = new dbMysql($config['db_mysql']['default']);
$smartCollect = new visitorStatics($dbm, $config);
$smartCollect->index();
exit;

//白名单、落地统计
class visitorStatics
{
    public $dbm;
    public $config;

    function __construct($dbm, $config)
    {
        $this->dbm = $dbm;
        $this->config = $config;
    }

    public function index(){
        echo date("Y-m-d H:i:s")." Goto/白名单队列程序开始执行\r\n";
        
        $redis_class = new Redis();
        $redis_class->connect('10.27.232.49', 6379);
        $redis_class->auth('xmzkifenx:xuxulike'); //密码验证
        //$redis->select(2);//选择数据库2
        
        while (1){
            $key = 'jump_ground_visit_queue';  //value 推广ID_类型（1白名单 2goto 3落地）_时间_IP_UA
            //redis队列取出详细
            $redis_result = unserialize($redis_class->rpop($key));
            if($redis_result){
                $pid_info = explode("_:_", $redis_result);
                $pid = isset($pid_info[0]) ? $pid_info[0] : 0;
                $data_type = isset($pid_info[1]) ? $pid_info[1] : 0;
                $visit_time = isset($pid_info[2]) ? $pid_info[2] : 0;
                $visit_date = date("Ymd", $visit_time);
                $ip = isset($pid_info[3]) ? $pid_info[3] : '';
                $ua = isset($pid_info[4]) ? addslashes($pid_info[4]) : '';
                $request_url = isset($pid_info[5]) ? addslashes($pid_info[5]) : '';
                $ip2long = ip2long($ip) ? ip2long($ip) : 0;
                //匹配goto到落地的用时ms
                $stime = $etime = 0;
                preg_match('/st=(.*)&nt=(.*)/', $request_url, $time_array);
                if(isset($time_array[1]) && $time_array[1]){
                    $stime = $time_array[1];
                }
                if(isset($time_array[2]) && $time_array[2]){
                    $etime = $time_array[2];
                }
                $use_time_ms = $etime - $stime;
                $use_time_s = round($use_time_ms, -3) / 1000;

                $table_qianzhui = 'ims_jump_detail_log_';
                //创建新表 以天为单位切割
                $now_month = date("Ymd");
                $new_table_name = $table_qianzhui.$now_month;
                $create_table_sql = "CREATE TABLE `".$new_table_name."` (
                    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `pid` int(11) NOT NULL DEFAULT '0' COMMENT '推广ID',
                    `data_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '数据类型【1白名单 2goto 3落地】',
                    `use_time_ms` int(11) NOT NULL DEFAULT '0' COMMENT 'GOTO至落地时常【单位：毫秒】',
                    `use_time_s` int(11) NOT NULL DEFAULT '0' COMMENT 'GOTO至落地时常【单位：秒】',
                    `visit_time` int(11) NOT NULL DEFAULT '0' COMMENT '访问时间【时间戳】',
                    `visit_date` int(11) NOT NULL DEFAULT '0' COMMENT '访问日期【20180808】',
                    `visit_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '访问IP【ip2long】',
                    `user_agent` varchar(300) NOT NULL,
                    `request_url` varchar(500) NOT NULL DEFAULT '' COMMENT '请求URL',
                    PRIMARY KEY (`id`),
                    KEY `pid` (`pid`),
                    KEY `visit_date` (`visit_date`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='访问信息详细日志表';";
                $last_create_table_key = 'last_create_table';
                $old_table_name = $redis_class->get($last_create_table_key);
                if($new_table_name != $old_table_name){
                    $this->dbm->doSql($create_table_sql);
                    $redis_class->set($last_create_table_key, $new_table_name);
                    //删除7天前的表
                    $last_seven_date = date("Ymd",strtotime("-8 day"));
                    $last_seven_table_name = $table_qianzhui.$last_seven_date;
                    $delete_table_sql = 'DROP TABLE '.$last_seven_table_name;
                    if($this->dbm->doSql("SHOW TABLES LIKE '". $last_seven_table_name."'")){
                        $this->dbm->doSql($delete_table_sql);
                    }
                }

                //根据访问时间，插入对应日期的详细日志表
                $visit_day = date("Ymd", $visit_time);
                $detail_log_table = $table_qianzhui.$visit_day;

                //redis取出 记录详细
                /*
                $insert_info = array();
                $insert_info['pid'] = $pid;
                $insert_info['data_type'] = $data_type;
                $insert_info['visit_time'] = $visit_time;
                $insert_info['visit_date'] = $visit_date;
                $insert_info['visit_ip'] = $ip2long;
                $insert_info['user_agent'] = $ua;
                $insert_info['request_url'] = $request_url;
                $this->dbm->insert($detail_log_table, $insert_info);
                */
                $insert_sql = 'INSERT INTO '.$detail_log_table.' (`pid`,`data_type`,`use_time_ms`,`use_time_s`,`visit_time`,`visit_date`,`visit_ip`,`user_agent`,`request_url`) VALUE ("'.$pid.'","'.$data_type.'","'.$use_time_ms.'","'.$use_time_s.'","'.$visit_time.'","'.$visit_date.'","'.$ip2long.'","'.$ua.'","'.$request_url.'");';
                $this->dbm->doSql($insert_sql);

                //更新渠道-类型-每日统计
                $table = 'ims_statics_pid_type_visit';
                $update_sql = 'UPDATE '.$table.' SET `total_visit_num`=`total_visit_num`+1,`update_time`='.time().' WHERE `visit_date`='.$visit_date.' AND `pid`='.$pid.' AND `data_type`='.$data_type; 
                $result = $this->dbm->doSql($update_sql);
                if(!$result){
                    /*
                    $insert_info = array();
                    $insert_info['pid'] = $pid;
                    $insert_info['data_type'] = $data_type;
                    $insert_info['visit_date'] = $visit_date;
                    $insert_info['total_visit_num'] = 1;
                    $insert_info['create_time'] = time();
                    $this->dbm->insert($table, $insert_info);
                    */
                    $insert_sql = "INSERT INTO ".$table." (`pid`,`data_type`,`visit_date`,`total_visit_num`,`create_time`) VALUE ('".$pid."','".$data_type."','".$visit_date."','1','".time()."')";
                    $this->dbm->doSql($insert_sql);
                }

                //更新渠道-类型-IP-每日统计
                //根据访问时间，更新对应月份数据表
                //每月1号创建新表 以月为单位切割
                $ip_table_qianzhui = 'ims_statics_pid_type_ip_visit_';
                $now_day = date("d");
                if($now_day == '01'){
                    $now_month = date("Ym");
                    $new_ip_table_name = $ip_table_qianzhui.$now_month;
                    $last_create_ip_table_key = 'last_create_ip_table';
                    $old_ip_table_name = $redis_class->get($last_create_ip_table_key);
                    if($new_ip_table_name != $old_ip_table_name){
                        $create_table_sql = "CREATE TABLE `".$new_ip_table_name."` (
                            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                            `pid` int(11) NOT NULL DEFAULT '0' COMMENT '推广ID',
                            `data_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '数据类型【1白名单 2goto 3落地】',
                            `visit_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '访问IP【ip2long】',
                            `visit_date` int(11) NOT NULL DEFAULT '0' COMMENT '访问日期【20180808】',
                            `total_visit_num` int(11) NOT NULL DEFAULT '0' COMMENT '访问次数',
                            `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
                            `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
                            PRIMARY KEY (`id`),
                            KEY `pid` (`pid`),
                            KEY `ptid` (`pid`,`data_type`,`visit_ip`,`visit_date`) USING BTREE
                          ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='渠道类型IP访问统计表';";
                        $this->dbm->doSql($create_table_sql);
                        $redis_class->set($last_create_ip_table_key, $new_ip_table_name);
                    }
                }
                //当前访问数据的月份
                $visit_month = date("Ym", $visit_time);
                if($visit_month == '201807'){
                    $now_ip_table = 'ims_statics_pid_type_ip_visit';
                }else{
                    $now_ip_table = $ip_table_qianzhui.$visit_month;
                }
                $update_sql = 'UPDATE '.$now_ip_table.' SET `total_visit_num`=`total_visit_num`+1,`update_time`='.time().' WHERE `pid`='.$pid.' AND `data_type`='.$data_type.' AND `visit_ip`='.$ip2long.' AND `visit_date`='.$visit_date; 
                //echo $update_sql."\r\n";
                $result = $this->dbm->doSql($update_sql);
                if(!$result){
                    /*
                    $insert_info = array();
                    $insert_info['pid'] = $pid;
                    $insert_info['data_type'] = $data_type;
                    $insert_info['visit_date'] = $visit_date;
                    $insert_info['visit_ip'] = $ip2long;
                    $insert_info['total_visit_num'] = 1;
                    $insert_info['create_time'] = time();
                    $this->dbm->insert($table, $insert_info);
                    */
                    $insert_sql = "INSERT INTO ".$now_ip_table." (`pid`,`data_type`,`visit_date`,`visit_ip`,`total_visit_num`,`create_time`) VALUE ('".$pid."','".$data_type."','".$visit_date."','".$ip2long."','1','".time()."')";
                    //echo $insert_sql."\r\n";
                    $this->dbm->doSql($insert_sql);
                }
            }else{
                //sleep(60);
                echo date("Y-m-d H:i:s")." Goto/白名单队列程序执行完毕 退出程序\r\n";
                exit;
            }
        }
    }

}