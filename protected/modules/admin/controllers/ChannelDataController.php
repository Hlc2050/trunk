<?php
/**
 * 渠道数据表控制器
 * User: fang
 * Date: 2017/1/9
 */
class ChannelDataController extends AdminController{
    public function actionIndex(){
        $params['where'] = '';
        if ($this->get('business_type') != '') {
            $params['where'] .= " and( a.business_type=" . intval($this->get('business_type')) . ") ";
        }
        if ($this->get('search_type') == 'partner_name' && $this->get('search_txt')) {
            $params['where'] .= " and(c.name like '%" . $this->get('search_txt') . "%') ";
        } else if ($this->get('search_type') == 'channel_name' && $this->get('search_txt')) {
            $params['where'] .= " and(b.channel_name like '%" . $this->get('search_txt') . "%') ";
        } else if ($this->get('search_type') == 'channel_code' && $this->get('search_txt')) {
            $params['where'] .= " and(b.channel_code like '%" . $this->get('search_txt') . "%') ";
        }
        //查看人员权限
        $result = $this->data_authority();
        if( $result != 0){
            $params['where'] .= " and(f.sno in ($result)) ";
        }
        $params['join'] = " LEFT JOIN channel as b ON a.channel_id = b.id 
                            LEFT JOIN partner as c ON a.partner_id=c.id 
                            LEFT JOIN finance_pay as f ON f.id=a.finance_pay_id 
                            LEFT JOIN material_article_template as m ON m.id=a.material_article_id 
                            LEFT JOIN business_types as n ON n.bid=a.business_type 
                            ";
        $params['order'] = "  order by id desc    ";
        $params['select'] = " a.*,m.article_code,b.channel_name,b.channel_code,c.name,f.sno,f.pay_money,f.online_date,n.bname";
        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(ChannelData::model()->tableName())->listdata($params);
        $page['listdata']['url'] = urlencode('http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"]);

        $this->render('index',array('page'=>$page));
    }

    /**
     * 添加渠道数据
     */
    public function actionAdd(){
        $page=array();
        $id=$this->get('id');
        //显示表单
        if(!$_POST){
            $this->render('update',array('page'=>$page));
            exit();
        }
        //处理需要的字段
        $info=new ChannelData();
        $info->finance_pay_id = $this->post('fpay_id');
        $info->partner_id = intval($this->post('partnerId'));
        $info->online_date = intval($this->post('online_date'));
        $info->channel_id = intval($this->post('channel_id'));
        $infancePayInfo = InfancePay::model()->findByPk($info->finance_pay_id);
        $info->business_type = $infancePayInfo->business_type;
        $info->charging_type = $infancePayInfo->charging_type;
        $info->wechat_group_id = $infancePayInfo->weixin_group_id;
        $info->fans = intval($this->post('fans'));
        $info->man_fans = $this->post('man_fans');
        $info->women_fans = $this->post('women_fans');
        if(!$info->partner_id){
            $this->msg(array('state'=>0,'msgwords'=>'请选择合作商！'));
        }
        if(!$info->channel_id){
            $this->msg(array('state'=>0,'msgwords'=>'请选择渠道！'));
        }
        if($info->man_fans+$info->women_fans>1 && $info->man_fans>0 && $info->women_fans>0){
            $this->msg(array('state'=>0,'msgwords'=>'该男女粉比率不符合要求！'));
        }
        $info->add_fans = $this->post('add_fans');
        if($info->add_fans>1 && $info->add_fans<0){
            $this->msg(array('state'=>0,'msgwords'=>'该进粉率不符合要求！'));
        }
        $info->first = $this->post('first');
        $info->second = $this->post('second');
        $info->third = $this->post('third');
        $info->fourth = $this->post('fourth');
        $info->fifth = $this->post('fifth');
        $info->read_num = $this->post('read_num');
        $info->material_article_id = $this->get('material_article_id');
        $m = MaterialArticleTemplate::model()->findByPk($this->get('material_article_id'));
        $info->article_code = $m->article_code;
        $info->article_type = $m->article_type;
        $info->update_date=time();
        $dbresult=$info->save();
        $id=$info->primaryKey;
        $logs="添加了渠道数据：".$id;
        $this->logs($logs);
        $msgarr=array('state'=>1,'url'=>$this->createUrl('channelData/index').'?p='.$_GET['p'].'');
        if($dbresult===false){
            //错误返回
            $this->msg(array('state'=>0));
        }else{
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 修改渠道数据
     */
    public function actionEdit(){
        $page=array();
        $id=$this->get('id');
        $info=ChannelData::model()->findByPk($id);
        if(!$info){
            $this->msg(array('state'=>0,'msgwords'=>'不存在'));
        }
        //显示表单
        if(!$_POST){
            $page['info']=$this->toArr($info);
            $financePayInfo = InfancePay::model()->findByPk($info->finance_pay_id);
            $page['info']['partner'] = Partner::model()->findByPk($financePayInfo->partner_id)->name;
            $page['info']['channel_code'] = Channel::model()->findByPk($financePayInfo->channel_id)->channel_code;
            $page['info']['channel_name'] = Channel::model()->findByPk($financePayInfo->channel_id)->channel_name;
            $page['info']['online_date'] = date("Y-m-d", $financePayInfo->online_date);
            $page['info']['charging_type'] = vars::get_field_str('charging_type', $financePayInfo->charging_type);
            $page['info']['business_type'] = BusinessTypes::model()->findByPk($financePayInfo->business_type)->bname;
            $page['info']['unit_price'] = $financePayInfo->unit_price;
            $page['info']['formula'] = vars::get_field_str('charging_formula', $financePayInfo->charging_type);
            $page['info']['wechat_group'] = WeChatGroup::model()->findByPk($financePayInfo->weixin_group_id)->wechat_group_name;
            $this->render('update',array('page'=>$page));exit();

        }
        //处理数据
        $info->fans = intval($this->post('fans'));
        $info->man_fans = $this->post('man_fans');
        $info->women_fans = $this->post('women_fans');
        if($info->man_fans+$info->women_fans>1){
            $this->msg(array('state'=>0,'msgwords'=>'该男女粉比率不符合要求！'));
        }
        $info->add_fans = $this->post('add_fans');
        if($info->add_fans>1 || $info->add_fans<0){
            $this->msg(array('state'=>0,'msgwords'=>'该进粉率不符合要求！'));
        }
        $info->first = $this->post('first');
        $info->second = $this->post('second');
        $info->third = $this->post('third');
        $info->fourth = $this->post('fourth');
        $info->fifth = $this->post('fifth');
        $info->read_num = $this->post('read_num');
        $info->material_article_id = $this->get('material_article_id');
        $info->article_code = MaterialArticleTemplate::model()->findByPk($this->get('material_article_id'))->article_code;
        $cost_detail=InfancePay::model()->findAll("channel_id=".$info->channel_id." and partner_id = ".$info->partner_id." and online_date = ".$info->online_date);
        $cost=0;
        foreach ($cost_detail as $k=>$v){
            $cost+=$cost_detail[$k]['pay_money'];
        }
        $price=$cost/($info->fans)*10000;
        $info->price = $price;
        $info->update_date=time();
        $dbresult=$info->save();
        $id=$info->primaryKey;
        $logs="修改了渠道数据：".$id;
        $this->logs($logs);
        $msgarr = array('state' => 1, 'url' => $this->get('backurl')); //保存的话，跳转到之前的列表
        if($dbresult===false){
            //错误返回
            $this->msg(array('state'=>0));
        }else{
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 删除渠道数据
     */
    public function actionDelete(){
        $ids=isset($_GET['ids'])&&$_GET['ids']!=''?$_GET['ids']:'';
        $ids=explode(',',$ids);
        $logs="删除了渠道数据：";
        foreach($ids as $id){
            $id=intval($id);
            $m=ChannelData::model()->findByPk($id);
            $m->delete();
            $logs.=$m->id.",";
        }
        //die();
        $this->logs($logs);
        $this->msg(array('state'=>1,'url' => $this->get('url')));
    }

    /**
     * AJAX获取合作商对应渠道
     * author: yjh
     */
    public function actionGetChannel()
    {
        if ($this->post('partnerId')) {
            $data = Channel::model()->findAll('partner_id = :partner_id', array(':partner_id' => $this->post('partnerId')));
            if (empty($data)) echo CHtml::tag('option', array('value' => ''), CHtml::encode('没有渠道信息，请选择其他合作商'), true);

            foreach ($data as $key => $val) {
                echo CHtml::tag('option', array('value' => $val['id']), CHtml::encode($val['channel_name']), true);
            }
        }
    }

    /**
     * AJAX获取合作商对应渠道
     * author: yjh
     */
    public function actionGetOnlineDate()
    {
        if ($this->post('channel_id')) {
            $data = InfancePay::model()->findAll('channel_id = :channel_id', array(':channel_id' => $this->post('channel_id')));
            if (empty($data)) echo CHtml::tag('option', array('value' => ''), CHtml::encode('没有上线日期'), true);
            $data = $this->toArr($data);
            foreach ($data as $key => $val) {
                echo CHtml::tag('option', array('value' => $val['online_date']), CHtml::encode(date('Y-m-d', $val['online_date'])), true);
            }
        } else {
            echo CHtml::tag('option', array('value' => ''), CHtml::encode('没有渠道名称'), true);
        }
    }

    /**
     * AJAX获取合作商对应渠道
     * author: yjh
     */
    public function actionGetOtherData()
    {
        if ($this->post('channel_id') && $this->post('onlineDate')) {
            $channelData = Channel::model()->findByPk($this->post('channel_id'));
            $date = InfancePay::model()->find('channel_id = :channel_id and online_date = :online_date', array(':channel_id' => $this->post('channel_id'), 'online_date' => $this->post('onlineDate')));
            if (empty($date)) {
                $result = array('fpay_id' => '', 'channelCode' => $channelData->channel_code, 'chgId' => '无数据', 'unitPrice' => '无数据', 'formula' => '无数据', 'wechat_group' => '无数据');
                echo json_encode($result);
            }
            $result = array(
                'fpay_id' => $date->id,
                'channelCode' => $channelData->channel_code,
                'chgId' => vars::get_field_str('charging_type', $date->charging_type),
                'unitPrice' => $date->unit_price,
                'business_type' => $date->business_type,
                'formula' => vars::get_field_str('charging_formula', $date->charging_type),
                'wechat_group' => WeChatGroup::model()->findByPk($date->weixin_group_id)->wechat_group_name
            );
            echo json_encode($result);
        } else {
            $result = array('fpay_id' => '', 'channelCode' => '', 'chgId' => '', 'unitPrice' => '', 'formula' => '', 'wechat_group' => '','business_type' => '');
            echo json_encode($result);
        }
    }
}