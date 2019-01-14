<?php
/**
 * Created by PhpStorm.
 * User: fang
 * Date: 2017/1/9
 * Time: 17:55
 */
class ChannelData extends CActiveRecord{
    public function tableName() {
        return '{{channel_data}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    /**
     * 效果表获取渠道数据
     * @param $condition
     * author: yjh
     */
    public function getChannelData($condition){
        $sql = "SELECT a.online_date,m.article_code,m.article_type,a.wechat_group_id,finance_pay_id,a.channel_id,a.fans AS fan,a.wechat_group_id,count(wid) AS wcount,read_num  AS uv 
                FROM channel_data AS a LEFT JOIN wechat_relation AS b ON b.wechat_group_id=a.wechat_group_id LEFT JOIN material_article_template as m ON m.id=a.material_article_id
                WHERE" . $condition;
        $info = Yii::app()->db->createCommand($sql)->queryAll();
        return $info;
    }

}