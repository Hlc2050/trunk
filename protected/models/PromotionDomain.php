<?php
/**
 * 推广-域名关联表
 * Created by lxj.
 * Date: 2018/12/20
 * Time: 9:38
 */

class PromotionDomain extends CActiveRecord{
    public function tableName() {
        return '{{promotion_domain_rel}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    /**
     * 根据域名id查询推广
     * @param $domain_id
     * @param string $where
     * @param string $select
     * @return array
     */
    public function getPromotionByDomain($domain_id,$where='',$select='')
    {
        $default_select = 'p.*,a.domain_id';
        if ($select) {
            $default_select = $select;
        }
        if (is_array($domain_id)) {
            $condition = ' a.domain_id in ('.implode(',',$domain_id).') ';
        }else {
            $condition = ' a.domain_id= '.$domain_id;
        }
        if ($where){
            $condition .= ' and '.$where;
        }
        $promotions = Yii::app()->db->createCommand()
            ->select($default_select)
            ->from('promotion_domain_rel a')
            ->join('promotion_manage p', 'a.promotion_id=p.id')
            ->where($condition)
            ->queryAll();
        return $promotions;
    }

    /**
     * 替换推广域名
     * @param int $pro_id 推广id
     * @param int $domain_id 旧域名id
     * @param int $new_domain 新域名id
     */
    public function replacePromotionDomain($pro_id,$domain_id,$new_domain){
        //删除旧的关联关系
        $this->deleteAll('promotion_id=:promotion_id and domain_id=:domain_id',array(':promotion_id'=>$pro_id,':domain_id'=>$domain_id));
        //新增关联
        $m = new $this;
        $m->promotion_id = $pro_id;
        $m->domain_id = $new_domain;
        $m->create_time = time();
        $m->save();
    }

    /**
     * 查询域名推广id
     * @param $domain_id
     * @return array
     */
    public function getProIdsByDomain($domain_id)
    {
        $pros = Dtable::toArr($this->findAll('domain_id=:domain_id',array(':domain_id'=>$domain_id)));
        return array_column($pros,'promotion_id');
    }


    /**
     * 更新推广域名
     * @param $pro_id int 推广id
     * @param $domains array 域名id
     */
    public function updateProDomains($pro_id,$domains)
    {
        $this->deleteAll('promotion_id='.$pro_id);
        $time = time();
        $add_data = array();
        foreach ($domains as $d) {
            $add_data[] = array(
                'promotion_id'=>$pro_id,
                'domain_id'=>$d,
                'create_time'=>$time,
            );
        }
        if ($add_data){
            $rows = array('promotion_id','domain_id','create_time');
            helper::batch_insert_data('promotion_domain_rel',$rows,$add_data);
        }
    }

    /**
     * 查询推广域名列表
     * @param $pro_id int 推广id
     * @return array
     */
    public function getPromotionDomains($pro_id)
    {
        $domains = $this->findAll('promotion_id=:promotion_id',array(':promotion_id'=>$pro_id));
        $domains = Dtable::toArr($domains);
        return array_column($domains,'domain_id');
    }

    /**
     * 删除推广使用的域名
     * @param $pro_id int 推广id
     * @author lxj
     */
    public function deleteProDomains($pro_id)
    {
        $domain_ids = $this->getPromotionDomains($pro_id);
        $update_data = array(
            'status'=>0,
            'promotion_type'=>0,
            'update_time'=>time(),
        );
        //将域名状态修改为备用
        if ($domain_ids) {
            DomainList::model()->updateDomains($domain_ids,$update_data,0);
        }
        //删除推广-域名关联
        $this->deleteAll('promotion_id='.$pro_id);

    }

    /**
     * 查询多个推广域名信息
     * @param $pro_ids array 推广id数组
     * @author lxj
     */
    public function getPromotionsDomains($pro_ids)
    {
        if (!is_array($pro_ids)) {
            $condition = 'a.promotion_id='.$pro_ids;
        }else {
            $condition = 'a.promotion_id in ('.implode(',',$pro_ids).')';
        }
        $pro_domain = array();
        $domain_list = Yii::app()->db->createCommand()
            ->select('a.promotion_id, a.domain_id, d.domain,d.is_public_domain,d.is_https,d.status as domain_status')
            ->from('promotion_domain_rel a')
            ->join('domain_list d', 'a.domain_id=d.id')
            ->where($condition)
            ->queryAll();
        foreach ($domain_list as $value) {
            $pro_domain[$value['promotion_id']][] = $value;
        }
        return $pro_domain;
    }
}