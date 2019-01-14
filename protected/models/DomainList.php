<?php
/**
 * Created by PhpStorm.
 * User: fang
 * Date: 2016/11/7
 * Time: 11:10
 */
class DomainList extends CActiveRecord{
    public function tableName() {
        return '{{domain_list}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }
    public function getId($domain){
        $m=$this->findByAttributes(array('domain'=>$domain));
        $id=$m->id;
        return $id;
    }
    public function status($domain,$status){
        $m=$this->findByAttributes(array('domain'=>$domain));
        if(!$m) {
            $m = new $this;
        }
        $m->domain=$domain;
        $m->status=$status;
        $m->save();
    }
    public function ByIdStatus($id,$status){
        $m=$this->findByPk($id);
        if(!$m) {
            return false;
        }
        $m->status=$status;
        $m->save();
    }
    public function domainCount($domain,$id){
        $sql="SELECT COUNT(id) as count FROM `domain_list` WHERE `domain`='{$domain}' and `id` !='{$id}'";
        $result=Yii::app()->db->createCommand($sql)->queryAll();
        $result=$result[0]['count'];
        return $result;
    }
    public function domainAddCount($domain){
        $m=$this->findByAttributes(array('domain'=>$domain));
        $result=count($m);
        return $result;
    }

    /**
     * 获取跳转域名
     * @param $uid
     * @return array
     * author: yjh
     */
    public function getGotoDomains($uid,$type= ''){
        //静态类型推广可使用普通、静态跳转域名
        if ($type == 0) {
            $result=Dtable::toArr($this->findAll('uid='.$uid.' and application_type=0 and domain_type=1 and status in(0,1)'));
        }else {
            $result=Dtable::toArr($this->findAll('uid='.$uid.'  and domain_type=1 and status in(0,1)'));
        }

        foreach ($result as $k=>$v) {
            if($v['is_https']==1){
                $result[$k]['domain']=$v['domain'];
            }
            if ($v['application_type']==1) {
                $result[$k]['domain'].='(静态)';
            }
        }
        return $result;
    }

    /**
     * 获取白域名
     * @param $uid
     * @return array
     * author: yjh
     */
    public function getWhiteDomains($uid){
        $result=Dtable::toArr($this->findAll('uid='.$uid.' and domain_type=2  and status in(0,1)'));
        foreach ($result as $k=>$v) {
            if($v['is_https']==1){
                $result[$k]['domain']=$v['domain'].'(https)';
            }
        }
        return $result;
    }


    /**
     * 获取域名
     * @param $pk
     * @return mixed|null
     * author: yjh
     */
    public function getDomainByPk($pk){
        $m=$this->findByPk($pk);
        return $m->domain;
    }

    /**
     * 检测域名拦截
     * @param $domain
     * @return int
     * author: yjh
     */
    public function checkDomain($domain){
        //财神到判断域名
        $url = 'http://mm.xjich.net/index.php?s=/Home/Page/wx_lanjie/domain/'.$domain;
        $html = file_get_contents($url);
        $html = iconv("gb2312", "utf-8//IGNORE",$html);
        
        switch($html)
        {
            case '[0]':
                $now_status = 0;
                break;
            case '[1]':
                $now_status = 1;
                break;
            case '[2]':
                $now_status = 2;
                break;
            case '[3]':
                $now_status = 3;
                break;
            default:
                $now_status = 0;
        }
        return $now_status;

    }

    /**
     * 批量更新域名信息
     * @param $domain_ids int|array 域名id
     * @param $data array (name=>value)
     * @param $new_status int 新状态 0备用，1正常
     * @param $check_status int 是否检查上线状态 1：是 0否
     */
    public function updateDomains($domain_ids,$data,$new_status,$check_status = 1) {
        if(!is_array($domain_ids)) {
            $condition = 'id='.$domain_ids;
        }else {
            $condition = ' id in ('.implode(',',$domain_ids).') ';
        }
        //修改为备用时，判断是否需要仅修改正常状态域名
        if ($new_status === 0 && $check_status == 1) {
            $condition .= ' and status=1 ';
        }
        $this->updateAll($data,$condition);
    }

    /**
     * 获取域名公众号状况 是否是公众号 0：否；1：是；
     */
    public function getIsPublic(){
        $temp = array();
        $data = Dtable::toArr($this->findAll());
        foreach ($data as $value){
            $temp[$value['domain']] = $value['is_public_domain'];
        }

      return  $temp;
    }
}