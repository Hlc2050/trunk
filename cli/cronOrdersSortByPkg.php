<?php
include(dirname(__FILE__) . '/init.php');
include(dirname(__FILE__) . '/dbMysql.php');
include(dirname(__FILE__) . '/dbOrder.php');
//本页为在CLI模式下执行 该文件的
//error_reporting(E_ALL & ~E_STRICT);  //php 5.4 以上 对方法重写参数需要一致，不一致需要加上 默认值,问题出现在 Dtable里面
$dbm = new dbMysql($config['db_mysql']['default']);
$order_dbm = new dbOrder($config['db_order']['default']);
$smartCollect = new ordersSortByDay($dbm, $order_dbm);
$smartCollect->run();

echo '---------------------------completed!';
exit;
//订单按天存储
class ordersSortByDay
{

    function __construct($dbm, $order_dbm)
    {
        $this->dbm = $dbm;
        $this->order_dbm = $order_dbm;
        $this->dx_bid = 10;

    }

    public function run()
    {
        $stat_date = $_GET['date'];
        $yesterdayPkgData = $this->get_yesterday_pkg_data($stat_date);
        if ($yesterdayPkgData) {
            $table_name='orders_sort_by_pkg';
            $rows=array_keys(reset($yesterdayPkgData));
            $ret=$this->batch_insert_data($table_name,$rows,$yesterdayPkgData);
            if($ret) {
                echo 'Success!';
            }else{
                echo 'Failed!';
            }
        }

    }

    /**
     * 整理昨日数据
     * 按日期下单商品分组
     * author: yjh
     */
    private function get_yesterday_pkg_data($stat_date)
    {

        //获取当天进线数据，除了发货量和发货金额
        $sql = 'SELECT a.package_id,b.customer_service_id,count(*) AS in_count
                 from order_goods_manage as a  left join order_manage as b on a.order_id=b.id left join order_assistant as c on a.order_id=c.order_id
                 where business_type='.$this->dx_bid.' and o_date = ' . $stat_date . '
                 group by a.package_id,b.customer_service_id';
        $inInfo = $this->order_dbm->doSql($sql);

        //获取发货量以及发货金额
        $sql = 'SELECT a.package_id,b.customer_service_id,count(*) AS out_count,sum(a.real_price) as delivery_money
                 from order_goods_manage as a  left join order_manage as b on a.order_id=b.id left join order_assistant as c on a.order_id=c.order_id
                 where  order_status=1 and business_type='.$this->dx_bid.' and d_date = ' . $stat_date . '
                 group by a.package_id,b.customer_service_id';
        $outInfo = $this->order_dbm->doSql($sql);

        if ($inInfo) {
            $tempInfo = array();
            foreach ($inInfo as $value) {
                $key = $value['package_id'].'_'.$value['customer_service_id'];
                $tempInfo[$key] = $value;
                $tempInfo[$key]['stat_date']=strtotime($stat_date);
            }
            $inInfo = $tempInfo;
        }

        if ($outInfo) {
            $tempInfo = array();
            foreach ($outInfo as $value) {
                $key = $value['package_id'].'_'.$value['customer_service_id'];
                $tempInfo[$key] = $value;
                $tempInfo[$key]['stat_date']=strtotime($stat_date);
            }
            $outInfo = $tempInfo;
        }
        if ($inInfo) {
            foreach ($inInfo as $key => $value) {

                if (array_key_exists($key, $outInfo)) {
                    $inInfo[$key]['out_count'] = $outInfo[$key]['out_count'];
                    $inInfo[$key]['delivery_money'] = $outInfo[$key]['delivery_money'];
                    unset($outInfo[$key]);
                } else {
                    $inInfo[$key]['out_count'] = 0;
                    $inInfo[$key]['delivery_money'] = 0;
                }

            }
        }
        if ($outInfo) {
            foreach ($outInfo as $key => $value) {
                $inInfo[$key] = $value;
                $inInfo[$key]['in_count'] = 0;
            }
        }
        return $inInfo;
    }
    /**
     * 批量插入数据库
     * @param array $rows
     * @param array $arr
     * @return string
     * author: yjh
     */
    private function batch_insert_data($table, array $rows, array $arr)
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
                $this->batch_insert_data($table, $rows, $arr);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }


    }


}