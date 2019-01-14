<?php

class DomainPromotionChange extends CActiveRecord
{
    public function tableName()
    {
        return '{{domain_promotion_change}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /*
     *
     *  推广列表修改或修正的时候保存新域名
     *  推广改成多域名后此方法已不适用 lxj 2019-1-2
     *  @param $domain 最新的域名
     */
    public function change($promotion_id, $domain, $auto = 0)
    {
        //if(!$domain) return false;
        $sql = "select * from domain_promotion_change where promotion_id='$promotion_id' order by id desc limit 0,1 ";
        $a = Yii::app()->db->createCommand($sql)->queryAll();//print_r($a);die();
        $from_domain = '';
        if (count($a)) {
            // die($a[0]['domain'].':'.$domain);
            if ($a[0]['domain'] == $domain) {  //没有发生改变,无效修改
                return false;
            }
            $from_domain = $a[0]['domain'];
        }
        $m = new $this;
        $m->domain = $domain;
        $m->from_domain = $from_domain;
        $m->type = 0;
        $m->type = $auto;
        $m->promotion_id = $promotion_id;
        $m->create_time = time();
        $m->save();
    }

    /*
    *
    *  域名列表修改或修正的时候保存新域名
    *  @param $domain 最新的域名
    */
    public function domainchange($domain, $from_domain, $promotion_id)
    {
        if (!$domain) return false;
        if ($domain == $from_domain) return false;
        $m = new $this;
        $m->domain = $domain;
        $m->from_domain = $from_domain;
        $m->type = 0;
        $m->promotion_id = $promotion_id;
        $m->create_time = time();
        $m->save();
    }

    /**
     * 根据域名获取推广id
     * 先找该日期前的新域名，
     * @param $domain
     * @param $stat_date
     * @return string
     * author: yjh
     */
    public function getPromotionID($domain, $stat_date)
    {
        $sql = "select * from domain_promotion_change where domain ='$domain' and create_time <$stat_date+86400 order by create_time desc";
        $originDomainList = Yii::app()->db->createCommand($sql)->queryAll();
        if (!$originDomainList) return -1;
        foreach ($originDomainList as $value) {
            $promotion_id = $value['promotion_id'];
            $create_time = $value['create_time'];
            $lastDomain = Dtable::toArr(DomainPromotionChange::model()->find("from_domain ='$domain' and promotion_id =$promotion_id and create_time >$create_time order by create_time ASC"));
            if (!$lastDomain) break;
            if ($stat_date >= strtotime(date('Y-m-d',$value['create_time'])) && $stat_date <= strtotime(date('Y-m-d',$lastDomain['create_time']))) break;
            $promotion_id = -2;
        }
        return $promotion_id;
    }

    /**
     * 添加推广域名使用记录
     * @param int $promotion_id  推广id
     * @param array $data 旧域名,新域名数组
     * @param int $type 类型（0=人工,1=自动）
     * @author lxj 2018-12-26
     */
    public function addChangeLogs($promotion_id,$data=array(),$type=0)
    {
        $add_data = array();
        $time = time();
        foreach ($data as $value) {
            $add_data[] = array(
                'from_domain'=>$value['from_domain'],
                'domain'=>$value['domain'],
                'create_time'=>$time,
                'type'=>$type,
                'promotion_id'=>$promotion_id,
            );
        }
        if ($add_data) {
            $rows = array('from_domain', 'domain','create_time','type','promotion_id');
            helper::batch_insert_data('domain_promotion_change',$rows,$add_data);
        }
    }

}