<?php

/**
 * 微信表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/2
 * Time: 14:18
 */
class WeChat extends CActiveRecord
{
    public function tableName()
    {
        return '{{wechat}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * 获取微信名ID
     * author: yjh
     */
    public function getWeChatNameByIds($idsArr)
    {
        if ($idsArr == '') return '无微信号';
        foreach ($idsArr as $key => $val) {
            $data = $this->findByPk($val['wid']);
            $weChatIdsArr[] = $data->wechat_id;
        }
        return implode('&nbsp;&nbsp;', $weChatIdsArr);
    }

    public function getWeixins($weixin_group_id){
        $weixin_group_id=$weixin_group_id;
        $sql="select b.*,a.wid as weixin_id from wechat_relation as a
						left join  wechat as b on b.id=a.wid  where wechat_group_id= $weixin_group_id
 ";
        $a=Yii::app()->db->createCommand($sql)->queryAll();
        return $a;
    }

    /**
     * 获取所有微信号
     * @return array
     * author: yjh
     */
    public function getWechatList($sql=''){
        $wechatList = Dtable::toArr($this->findAll("1 ".$sql));
        return $wechatList;
    }
}