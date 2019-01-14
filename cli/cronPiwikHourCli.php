<?php
include(dirname(__FILE__).'/init.php');
include(dirname(__FILE__) . '/dbMysql.php');
include(dirname(__FILE__) . '/dbPiwikMysql.php');

//本页为在CLI模式下执行 该文件的
//error_reporting(E_ALL & ~E_STRICT);  //php 5.4 以上 对方法重写参数需要一致，不一致需要加上 默认值,问题出现在 Dtable里面
$dbm=new dbMysql($config['db_mysql']['default']);
$dbm_piwik=new dbPiwikMysql($config['db_piwik']['default']);
$smartCollect=new piwikHourCli($dbm,$dbm_piwik,$config);
$smartCollect->run();

echo '---------------------------completed!';

//
class piwikHourCli {
	public $dbm;
	public $config;
	function __construct($dbm,$dbm_piwik,$config){
		$this->dbm=$dbm;
		$this->dbm_piwik=$dbm_piwik;
		$this->config=$config;
	}

	public function run()
	{
		$dates=strtotime(date('Y-m-d H:00'));
		$date_pv=strtotime(date('Y-m-d'));
		$date_pv_end=strtotime(date('Y-m-d'))+86400-1;
		$dates_stat=$dates-3600+1;
		$datetimes = date('Y-m-d H:00', time() - 9 * 3600);
		$datetime_e = date('Y-m-d H:i:s', strtotime($datetimes) + 3600 - 1);
		$sql = "SELECT a.domain,b.idsite,b.finance_pay_id,b.id as bid,a.id as aid FROM domain_list as a LEFT JOIN promotion_manage as b ON a.id=b.domain_id WHERE b.status=0";
		$domain_pro = $this->dbm->doSql($sql);
		/*$sql_change = "SELECT a.from_domain as domain,b.idsite,b.id as bid,b.domain_id as aid FROM domain_promotion_change as a LEFT JOIN promotion_manage as b ON b.id=a.promotion_id where create_time betwwen '$dates_stat' and '$dates'";
		$domain_change = $this->dbm->doSql($sql_change);
		if ( $domain_change != ''){
			$domain_pro = array_merge($domain_pro,$domain_change);
		}*/
		$sql_change = "SELECT a.domain,c.finance_pay_id,c.idsite,c.id as bid,b.id as aid FROM domain_promotion_change as a LEFT JOIN domain_list as b ON a.domain=b.domain LEFT JOIN promotion_manage as c ON a.promotion_id=c.id WHERE a.create_time between '$dates_stat' and '$dates'";
		$domain_change = $this->dbm->doSql($sql_change);
		foreach ( $domain_change as $k ){
			$domain_pro[] = $k;
		}
		foreach ($domain_pro as $k => $v) {
			$domain = str_replace('http://', '', $domain_pro[$k]['domain']);
			$config =require(dirname(__FILE__) . '/../protected/config/params.php');
			$config = $config['basic'];
			/*//获取PV
			$url_pv = $config['piwik_url'] . "?module=API&method=Actions.get&idSite={$domain_pro[$k]['idsite']}&period=range&date=lastMinutes60&format=JSON&token_auth=" . $config['piwik_token'];
			$data_pv=file_get_contents($url_pv);
			$data_pv=json_decode($data_pv,true);*/
			//获取UV
			$url_uv = $config['piwik_url'] . "?module=API&method=Live.getCounters&idSite={$domain_pro[$k]['idsite']}&lastMinutes=60&format=JSON&token_auth=" . $config['piwik_token'];
			$data_uv=file_get_contents($url_uv);
			$data_uv=json_decode($data_uv,true);
			//获取IP
			$sql_piwik_hour = "select * from piwik_log_visit where idsite={$domain_pro[$k]['idsite']} and visit_first_action_time between '$datetimes' and '$datetime_e' group by location_ip";
			$piwil_visit = $this->dbm_piwik->doSql($sql_piwik_hour);
			$sql_flow = "select * from piwik_hour_data where domain='{$domain}' and idsite='{$domain_pro[$k]['idsite']}' and stat_date between '$dates_stat' and '$dates'";
			$stat_piwik=$this->dbm->doSql($sql_flow);
			$sql_pv = "select pv from piwik_hour_data where domain='{$domain}' and idsite='{$domain_pro[$k]['idsite']}' and stat_date between '$date_pv' and '$date_pv_end'";
			$stat_pv=$this->dbm->doSql($sql_pv);
			//获取微信号长按次数
			$sql_wl = "select id from static_longpress where domain_id='{$domain_pro[$k]['aid']}' and type=0 and times between '$dates_stat' and '$dates'";
			$wechat_longpress=$this->dbm->doSql($sql_wl);
			//获取二维码长按次数
			$sql_qrl = "select id from static_longpress where domain_id='{$domain_pro[$k]['aid']}' and type=1 and times between '$dates_stat' and '$dates'";
			$qr_longpress=$this->dbm->doSql($sql_qrl);
			//获取打款相吸信息
			$sql_infance = "select * from finance_pay where id='{$domain_pro[$k]['finance_pay_id']}' ";
			$infanceInfo=$this->dbm->doSql($sql_infance);
			if (count($stat_piwik) > 0) continue;
			$data=array();
			$data['ip'] = count($piwil_visit);
			$data['pv'] = $data_uv[0]['action'];
			$data['uv'] = $data_uv[0]['visitors'];
			$data['wechat_touch'] = count($wechat_longpress);
			$data['qr_code_click'] = count($qr_longpress);
			$data['stat_date'] = $dates;
			$data['create_date'] = time();
			$data['idsite'] = $domain_pro[$k]['idsite'];
			$data['domain'] = $domain;
			$data['promotion_id'] = $domain_pro[$k]['bid'];
			$data['domain_id'] = $domain_pro[$k]['aid'];
			$data['partner_id'] = $infanceInfo[0]['partner_id'];
			$data['channel_id'] = $infanceInfo[0]['channel_id'];
			$data['wechat_group_id'] = $infanceInfo[0]['weixin_group_id'];
			$data['charging_type'] = $infanceInfo[0]['charging_type'];
			$this->dbm->insert('piwik_hour_data',$data);

		}
	}

}