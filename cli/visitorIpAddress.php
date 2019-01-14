<?php
include(dirname(__FILE__) . '/init.php');
include(dirname(__FILE__) . '/dbMysql.php');
ini_set('max_execution_time', '0');
//本页为在CLI模式下执行 该文件的
//error_reporting(E_ALL & ~E_STRICT);  //php 5.4 以上 对方法重写参数需要一致，不一致需要加上 默认值,问题出现在 Dtable里面
$dbm = new dbMysql($config['db_mysql']['default']);
$smartCollect = new visitorIpAddress($dbm, $config);
$smartCollect->index();
exit;

//访问IP地址检测录入
class visitorIpAddress
{
    public $dbm;
    public $config;

    function __construct($dbm, $config)
    {
        $this->dbm = $dbm;
        $this->config = $config;
    }

    public function index(){
        echo date("Y-m-d H:i:s")." 程序开始执行\r\n";
        $pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;
        $visit_date = isset($_GET['visit_date']) ? intval($_GET['visit_date']) : 0;
        $data_type = isset($_GET['data_type']) ? intval($_GET['data_type']) : 0;
        if($pid < 1){
            echo "请输入pid参数!";
            exit;
        }
        if($visit_date < 1){
            echo "请输入访问日期参数!";
            exit;
        }
        $where = '`pid`='.$pid.' AND `visit_date`='.$visit_date;
        if($data_type){
            $where .= ' AND `data_type`='.$data_type;
        }
        $select_sql = 'SELECT visit_ip FROM ims_statics_pid_type_ip_visit WHERE '.$where;
        echo '执行语句：'.$select_sql."\r\n";
        $result = $this->dbm->doSql($select_sql);
        if(count($result) > 0){
            foreach($result as $key=>$value){
                $check_long2ip_sql = 'SELECT id FROM ims_ip_house WHERE `ip2long`='.$value['visit_ip'];
                $result_return = $this->dbm->doSql($check_long2ip_sql);
                if(!$result_return){
                    $ip = long2ip($value['visit_ip']);
                    $check_ip_api = $this->config['check_ip_url'].'?ip='.$ip;
                    $res = curl_get($check_ip_api);
                    // 检查是否有错误发生
                    if ($res == 'curl_error') {
                        break;
                    }
                    $res_info = json_decode($res, true);
                    if($res_info['code'] == 0 && $res_info['data']['region_id']){
                        $isp_type_array = array('移动'=>1,'电信'=>2, '联通'=>3, '腾讯'=>4);
                        if(isset($isp_type_array[$res_info['data']['isp']]) && $isp_type_array[$res_info['data']['isp']]){
                            $isp_type = $isp_type_array[$res_info['data']['isp']];
                        }else{
                            $isp_type = 99;
                        }
                        $insert_sql = 'INSERT INTO ims_ip_house (`ip`,`ip2long`,`region`,`city`,`county`,`isp_type`,`create_time`) VALUE ("'.$ip.'","'.$value['visit_ip'].'","'.$res_info['data']['region'].'","'.$res_info['data']['city'].'","'.$res_info['data']['county'].'","'.$isp_type.'","'.time().'")';
                        $this->dbm->doSql($insert_sql);
                    }
                    sleep(1);
                }
            }
        }
        echo date("Y-m-d H:i:s")." 程序执行结束\r\n";
    }
}