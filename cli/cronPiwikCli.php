<?php
include(dirname(__FILE__).'/init.php');
include(dirname(__FILE__) . '/dbMysql.php');
include(dirname(__FILE__) . '/dbPiwikMysql.php');

//本页为在CLI模式下执行 该文件的
//error_reporting(E_ALL & ~E_STRICT);  //php 5.4 以上 对方法重写参数需要一致，不一致需要加上 默认值,问题出现在 Dtable里面
$dbm=new dbMysql($config['db_mysql']['default']);
$dbm_piwik=new dbPiwikMysql($config['db_piwik']['default']);
$smartCollect=new piwikCli($dbm,$dbm_piwik,$config);
$smartCollect->run();

echo '---------------------------completed!';

//
class piwikCli {
	public $dbm;
	public $config;
	function __construct($dbm,$dbm_piwik,$config){
		$this->dbm=$dbm;
		$this->dbm_piwik=$dbm_piwik;
		$this->config=$config;
	}

	public function run()
	{
		$datetime = date('Y-m-d', strtotime('-1 day'));
		$dates=strtotime($datetime);
		$datetimes = date('Y-m-d 16:00:00', strtotime('-2 day'));
		$datetime_e = date('Y-m-d 16:00:00',strtotime('-1 day'));
		$sql = "select * from `piwik_site`";
		$piwil = $this->dbm_piwik->doSql($sql);
		foreach ($piwil as $k => $v) {
			$domain = str_replace('http://', '', $piwil[$k]['main_url']);
			$config =require(dirname(__FILE__) . '/../protected/config/params.php');
			$config = $config['basic'];
			$url = $config['piwik_url'] . "?module=API&method=API.get&idSite={$piwil[$k]['idsite']}&period=day&date={$datetime}&format=JSON&token_auth=" . $config['piwik_token'];
			$data_piwik = file_get_contents($url);
			$data_piwik = json_decode($data_piwik, true);
			$sql_piwik = "select * from piwik_log_visit where idsite={$piwil[$k]['idsite']} and visit_first_action_time between '$datetimes' and '$datetime_e' group by location_ip";
			$piwil_visit = $this->dbm_piwik->doSql($sql_piwik);
			$sql_flow = "select * from stat_piwik_flow where domain='{$domain}' and idsite='{$piwil[$k]['idsite']}' and stat_date='{$dates}'";
			$stat_piwik=$this->dbm->doSql($sql_flow);
			//$stat_piwik = StatPiwikFlow::model()->findByAttributes(array('domain' => $domain, 'idsite' => $piwil[$k]['idsite'], 'stat_date' => strtotime($datetime)));
			if (count($stat_piwik) > 0) continue;
			$data=array();
			$data['ip'] = count($piwil_visit);
			$data['pv'] = $data_piwik['nb_pageviews'];
			$data['uv'] = $data_piwik['nb_uniq_visitors'];
			$data['stat_date'] = strtotime($datetime);
			$data['creat_date'] = time();
			$data['idsite'] = $piwil[$k]['idsite'];
			$data['domain'] = $domain;
			$this->dbm->insert('stat_piwik_flow',$data);
		}
	}

}