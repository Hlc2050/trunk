<?php
class WeChatUseLog extends CActiveRecord{
	public function tableName() {
		return '{{wechat_use_log}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

	/*
	 * 通过微信主键id及日期匹配该微信信息
	 * @author lxj
	 * @param int $wx_id 微信id
	 * @param int $date 日期的时间戳
	 */
	public function getWechatInfo($wx_id,$date)
    {
        $res_info = array();
        // 若时间为空或日期超出当前日期，返回空
        if (empty($date) || $date>time()) {
            return $res_info;
        }
        $sql = "select * from wechat_use_log where wx_id = ".$wx_id." and begin_time <= ".$date." and (end_time >= ".$date." or end_time = 0 ) order by id desc limit 1";
        $wechat_info = Yii::app()->db->createCommand($sql)->queryAll();
        $wechat_info = $wechat_info[0];
        // 若该微信号在加粉日期时间段内没有使用记录，且使用记录为空，则查询微信号信息表
        // 使用记录不为空，则为未归属数据
        if (!$wechat_info) {
            $use_log = WeChatUseLog::find('wx_id = '.$wx_id);
            if (!$use_log) $wechat_info = WeChat::model()->findByPk($wx_id);
            else return $res_info;

        }
        if ($wechat_info) {
            $res_info = array(
                'customer_service_id'=>$wechat_info['customer_service_id'],// 部门id
                'goods_id'=>$wechat_info['goods_id'],// 商品id
                'character_id'=>$wechat_info['character_id'],// 形象id
                'business_type'=>$wechat_info['business_type'],// 业务类型id
                'department_id'=>$wechat_info['department_id'],// 推广部门id
                'promotion_staff_id'=>$wechat_info['promotion_staff_id'],// 推广人员id
                'charging_type'=>$wechat_info['charging_type'],// 计费方式id
            );
        }
        return $res_info;
    }

}