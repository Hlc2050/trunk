<?php
class DomainInterceptDetail extends CActiveRecord{
	public function tableName() {
		return '{{domain_intercept_detail}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

	/**
	 * 储存拦截替换信息
	 * @param $data
	 * author: yjh
	 */
	public function insertInterceptDomain($data){
	    $time = time();
	    //备案检测替换时为避免两台服务器时间不一致，采用检测服务器的时间
	    if ($_GET['time'] && $data['detection_type'] == 1) {
            $time = $_GET['time'];
        }
		$info= new DomainInterceptDetail();
		$info->domain_id = $data['domain_id'];
		$info->new_domain_id = $data['new_domain_id']?$data['new_domain_id']:0;
		$info->domain_type = $data['domain_type'];
		$info->uid = $data['uid'];
		$info->line = $data['line'];
		$info->detection_type = $data['detection_type'];
		$info->promotion_id = $data['promotion_id']?$data['promotion_id']:0;
		$info->log_promotion_id = $data['log_promotion_id']?$data['log_promotion_id']:'';
		$info->promotion_domain = $data['promotion_domain']?$data['promotion_domain']:0;
		$info->is_white_domain = $data['is_white_domain']?$data['is_white_domain']:0;
		$info->mark = $data['mark']?"替换失败,".$data['mark']:"替换成功";
		$info->time = $time;
		$info->save();
	}


}
