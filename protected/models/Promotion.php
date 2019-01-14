<?php
/**
 * 推广信息表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/2
 * Time: 14:18
 */
class Promotion extends CActiveRecord{
    public function tableName() {
        return '{{promotion_manage}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    //获取渠道编码和渠道名称
    public function getChannelData($id){
        $temp = array();
        $ret = $this->findByPk($id);
        $channel_data = Channel::model()->findByPk($ret['channel_id']);
        $temp['channel_code'] = $channel_data['channel_code'];
        $temp['channel_name'] = $channel_data['channel_name'];
        return $temp;
    }

    public function status($domain_id,$status){
        $m=$this->findByAttributes(array('domain_id'=>$domain_id));
        if(!$m) {
            $m = new $this;
        }
        $m->domain_id=$domain_id;
        $m->status=$status;
        $m->save();
    }
    public function ByInfanceDel($id){
        $m=$this->findByAttributes(array('finance_pay_id'=>$id));
        if(!$m) {
            return false;
        }
        //删除推广
        $m->delete();
        //删除推广关联域名
        PromotionDomain::model()->deleteProDomains($m->id);
        //修改跳转域名、白域名状态
        $goto_domain = $m->goto_domain_id;
        $white_domain_id = $m->is_white_domain == 0 ? $m->white_domain_id:0;
        $update = array(
            'status'=>0,
            'update_time'=>time(),
        );
        $update_domain = array();
        //白域名及跳转域名是否有其他推广在使用
        if ($goto_domain) {
            $p = $this->find('(promotion_type =0 or promotion_type=3) and status!=1 and goto_domain_id='.$goto_domain);
            if (!$p){
                $update_domain[] = $goto_domain;
            }
        }
        if ($white_domain_id) {
            $p = $this->find('(promotion_type =0 or promotion_type=3) and status!=1 and is_white_domain=0 and white_domain_id='.$white_domain_id);
            if (!$p){
                $update_domain[] = $white_domain_id;
            }
        }
        //修改白域名及跳转域名状态
        if ($update_domain) {
            DomainList::model()->updateDomains($update_domain,$update,0);
        }
    }

    /**
     * 判断该渠道是否在使用
     * @param $channel_id
     * @return bool
     * author: yjh
     */
    public function isChannelOnline($channel_id,$pk=0){
        $where="";
        if($pk) $where=" and finance_pay_id!=".$pk;
        $info=$this->find("channel_id=$channel_id and status !=1".$where);
        if($info) return false;
        return true;

    }

}