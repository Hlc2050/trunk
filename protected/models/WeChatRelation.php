<?php
/**
 * 微信小组与微信号关联表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/24
 * Time: 14:18
 */
class WeChatRelation extends CActiveRecord{
    public function tableName() {
        return '{{wechat_relation}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }
    public function getWeChatIds($id){
        $wechatArr =array();
        if(!$id) return '该小组下没有微信号';
        $result=$this->findAll('wechat_group_id=:wid',array(':wid'=>$id));
        foreach ($result as $key=>$val){
            $wechatArr[]=$val->wid;
        }
        return $wechatArr;
    }

}