<?php

/**
 * 推广列表管理器
 * User: yjh
 * Date: 2016/11/10
 * Time: 11:26
 */
class PromotionController extends AdminController
{
    /**
     * 推广列表
     * author: yjh
     */
    public function actionIndex()
    {
        $page = $this->getExportData();
        $this->render('index', array('page' => $page));
    }

    /**
     * 推广添加，表单处理
     * author: yjh
     */
    public function actionAdd()
    {
        $page = array();
        //显示表单
        if (!$_POST) {
            $province_data = Linkage::model()->findAll('parent_id = 1');
            $this->render('update', array('page' => $page, 'province_data'=>$province_data));
            exit;
        }
        $domain_ids = trim($this->post('domain_ids'),',')?explode(',',trim($this->post('domain_ids'),',')):0;

        $info = new Promotion();
        //$info->promotion_staff_id = $this->post('tg_id');
        $info->finance_pay_id = $this->post('fpay_id');
//        $info->domain_id = $this->post('domain_id');
        $info->channel_id = $this->post('channel_id');
        $info->independent_cnzz = $this->post('indCnzz');
        $info->promotion_type = $this->post('promotion_type');
        $info->total_cnzz = $this->post('totalCnzz');
        $info->origin_template_id = $this->post('article_id');
        $info->goto_domain_id = $this->post('goto_domain_id');
        $info->url_rule = $this->post('rule');
//        $info->cnzz_code_id = $this->post('cnzz_code_id');
        $info->is_pc_show = $this->post('is_pc_show');
        $info->pc_url = $this->post('pc_url');
        //关闭白域名功能 lxj 2019-01-03
        $info->is_white_domain = 1;
        $info->white_domain_id = 0;
        $address_provinces = $this->post('provinces');
        $address_citys = $this->post('citys');
        $info->minus_proportion = $this->post('minus_proportion');
        $info->line_type =  $this->post('line_type');
        if ($info->line_type == 1) {
            $info->origin_template_id = 0;
            $info->is_white_domain = 1;
        }
        
        //表单验证
        $result = Promotion::model()->count('finance_pay_id=:fpay_id ', array(':fpay_id' => $info->finance_pay_id));
        if ($info->finance_pay_id == '') $this->msg(array('state' => 0, 'msgwords' => '上线信息未选择完整'));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '该打款信息已经推广过了，请重新添加'));
        if ($info->is_pc_show == 1) {
            if ($info->pc_url == '') $this->msg(array('state' => 0, 'msgwords' => '未填写PC端访问网址'));
        }
        if (is_int($info-> minus_proportion)) {
            $this->msg(array('state' => 0, 'msgwords' => '扣量比例不是数字'));
        }
        if ($info->promotion_type != 1) {
            if (!$domain_ids) $this->msg(array('state' => 0, 'msgwords' => '未选择推广域名'));
            //获取统计piwik 域名id
//            $idSite = $this->getIdSite($domain);
//            $info->idsite = $idSite;
//            $info->three_cnzz = $this->getPiwikJS($idSite);
        }

        if ($info->promotion_type == 0 || $info->promotion_type == 3) {
            if ($info->goto_domain_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择跳转域名'));
            //if ($info->white_domain_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择白域名'));
            if ($info->origin_template_id == '' && $info->line_type == 0) $this->msg(array('state' => 0, 'msgwords' => '未选择模板'));
        }


//如果是链接类型是普通类型
        if($info->line_type == 0) {
            if (!$info->origin_template_id) {
                $info->origin_template_id = MaterialArticleTemplate::model()->find()->id;
            }
//往上线素材表添加数据 返回id存储
            $articleInfo = $this->toArr(MaterialArticleTemplate::model()->findByPk($info->origin_template_id));
            if (empty($articleInfo)) {
                $this->msg(array('state' => 0, 'msgwords' => '不存在该模板'));
            }

            if ($articleInfo['article_type'] != 3) {
                $onlineArticleInfo = new OnlineMaterialManage();
                $onlineArticleInfo->promotion_id = 0;
                $onlineArticleInfo->channel_id = $info->channel_id;
                $onlineArticleInfo->partner_id = Channel::model()->findByPk($info->channel_id)->partner_id;
                $onlineArticleInfo->origin_template_id = $articleInfo['id'];
                $onlineArticleInfo->article_title = $articleInfo['article_title'];
                $onlineArticleInfo->content = $articleInfo['content'];
                $onlineArticleInfo->tag = $articleInfo['tag'];
                $onlineArticleInfo->cat_id = $articleInfo['cat_id'];
                $onlineArticleInfo->suspension_text = $articleInfo['suspension_text'];
                $onlineArticleInfo->support_staff_id = $articleInfo['support_staff_id'];
                $onlineArticleInfo->article_code = $articleInfo['article_code'];
                $onlineArticleInfo->article_type = $articleInfo['article_type'];
                $onlineArticleInfo->cover_url = $articleInfo['cover_url'];
                $onlineArticleInfo->top_img = $articleInfo['top_img'];
                $onlineArticleInfo->avater_img = $articleInfo['avater_img'];
                $onlineArticleInfo->idintity = $articleInfo['idintity'];
                $onlineArticleInfo->first_audio = $articleInfo['first_audio'];
                $onlineArticleInfo->second_audio = $articleInfo['second_audio'];
                $onlineArticleInfo->third_audio = $articleInfo['third_audio'];
                $onlineArticleInfo->top_text = $articleInfo['top_text'];
                $onlineArticleInfo->addfans_type = $articleInfo['addfans_type'];
                $onlineArticleInfo->addfans_text = $articleInfo['addfans_text'];
                $onlineArticleInfo->level_tag = $articleInfo['level_tag'];
                $onlineArticleInfo->top_color = $articleInfo['top_color'];
                $onlineArticleInfo->avater_tag = $articleInfo['avater_tag'];
                $onlineArticleInfo->psq_id = $articleInfo['psq_id'];
                $onlineArticleInfo->promotion_staff_id = $this->post('tg_id');
                $onlineArticleInfo->review_id = $articleInfo['review_id'];
                $onlineArticleInfo->bottom_type = $articleInfo['bottom_type'];
                $onlineArticleInfo->xingxiang = $articleInfo['xingxiang'];
                $onlineArticleInfo->is_vote = $articleInfo['is_vote'];
                $onlineArticleInfo->vote_id = $articleInfo['vote_id'];
                $onlineArticleInfo->is_order = $articleInfo['is_order'];
                $onlineArticleInfo->order_id = $articleInfo['order_id'];
                $onlineArticleInfo->pop_time = $articleInfo['pop_time'];
                $onlineArticleInfo->char_intro = $articleInfo['char_intro'];
                $onlineArticleInfo->chat_content = $articleInfo['chat_content'];
                $onlineArticleInfo->is_hide_title = $articleInfo['is_hide_title'];
                $onlineArticleInfo->is_fill = $articleInfo['is_fill'];
                $onlineArticleInfo->descriptive_statement = $articleInfo['descriptive_statement'];
                $onlineArticleInfo->update_time = time();
                $onlineArticleInfo->create_time = time();
                $onlineArticleInfo->save();
                $onlineArticleId = $onlineArticleInfo->primaryKey;
                //其他判断
                $info->article_id = $onlineArticleId;//
                $info->status = 0;//上线
                $info->create_time = time();
                $dbresult = $info->save();
                $id = $info->primaryKey;
//将推广id再存入上线素材表中
                $onlineArticleInfo->promotion_id = $id;
                $onlineArticleInfo->save();
            } else {
                $result = MaterialArticleTemplate::model()->findAll('article_code=:article_code', array(":article_code" => $articleInfo['article_code']));
                foreach ($result as $value) {
                    $onlineArticleInfo = new OnlineMaterialManage;
                    $onlineArticleInfo->promotion_id = 0;
                    $onlineArticleInfo->channel_id = $info->channel_id;
                    $onlineArticleInfo->partner_id = Channel::model()->findByPk($info->channel_id)->partner_id;
                    $onlineArticleInfo->origin_template_id = $articleInfo['id'];
                    $onlineArticleInfo->article_title = $value['article_title'];
                    $onlineArticleInfo->content = $value['content'];
                    $onlineArticleInfo->tag = $value['tag'];
                    $onlineArticleInfo->review_id = $value['review_id'];
                    $onlineArticleInfo->cat_id = $value['cat_id'];
                    $onlineArticleInfo->suspension_text = $value['suspension_text'];
                    $onlineArticleInfo->article_code = $value['article_code'];
                    $onlineArticleInfo->support_staff_id = $value['support_staff_id'];
                    $onlineArticleInfo->article_type = $value['article_type'];
                    if ($value['is_main_page'] == 1) {
                        $onlineArticleInfo->promotion_staff_id = $this->post('tg_id');
                        $onlineArticleInfo->descriptive_statement = $value['descriptive_statement'];
                    }
                    $onlineArticleInfo->top_img = $value['top_img'];
                    $onlineArticleInfo->avater_img = $value['avater_img'];
                    $onlineArticleInfo->idintity = $value['idintity'];
                    $onlineArticleInfo->first_audio = $value['first_audio'];
                    $onlineArticleInfo->second_audio = $value['second_audio'];
                    $onlineArticleInfo->third_audio = $value['third_audio'];
                    $onlineArticleInfo->top_text = $value['top_text'];
                    $onlineArticleInfo->addfans_type = $value['addfans_type'];
                    $onlineArticleInfo->addfans_text = $value['addfans_text'];
                    $onlineArticleInfo->psq_id = $value['psq_id'];
                    $onlineArticleInfo->top_color = $value['top_color'];
                    $onlineArticleInfo->avater_tag = $value['avater_tag'];
                    $onlineArticleInfo->level_tag = $value['level_tag'];
                    $onlineArticleInfo->bottom_type = $value['bottom_type'];
                    $onlineArticleInfo->xingxiang = $value['xingxiang'];
                    $onlineArticleInfo->is_vote = $value['is_vote'];
                    $onlineArticleInfo->vote_id = $value['vote_id'];
                    $onlineArticleInfo->order_id = $value['order_id'];
                    $onlineArticleInfo->is_order = $value['is_order'];
                    $onlineArticleInfo->pop_time = $value['pop_time'];
                    $onlineArticleInfo->char_intro = $value['char_intro'];
                    $onlineArticleInfo->chat_content = $value['chat_content'];
                    $onlineArticleInfo->cover_url = $value['cover_url'];
                    $onlineArticleInfo->payment = $value['payment'];
                    $onlineArticleInfo->is_hide_title = $value['is_hide_title'];
                    $onlineArticleInfo->is_fill = $value['is_fill'];
                    $onlineArticleInfo->is_main_page = $value['is_main_page'];
                    $onlineArticleInfo->link = $value['link'];
                    $onlineArticleInfo->descriptive_statement = $value['descriptive_statement'];
                    $onlineArticleInfo->update_time = time();
                    $onlineArticleInfo->create_time = time();
                    $onlineArticleInfo->save();
                    //其他判断
                    if ($value['is_main_page'] == 1) {
                        $onlineArticleId = $onlineArticleInfo->primaryKey;
                        $info->article_id = $onlineArticleId;//
                        $info->status = 0;//上线
                        $info->create_time = time();
                        $dbresult = $info->save();
                    }
                    //将推广id再存入上线素材表中
                    $id = $info->primaryKey;
                    $onlineArticleInfo->promotion_id = $id;
                    $onlineArticleInfo->save();
                }
            }
        }else{
            $dbresult = $info->save();
            $id = $info->primaryKey;
        }


        //cnzz扣量地区设置
        $address_string = $linkage_ids_string = '';
        foreach($address_provinces as $key=>$value){
            $province_id = intval($value);
            if($province_id > 0){
                $city_id = intval($address_citys[$key]);
                if($city_id > 0){
                    $address_string .= $province_id.'-'.$city_id.';';
                    $linkage_ids_string .= $province_id.','.$city_id.',';
                }else{
                    $province_citys = Linkage::model()->findAll('parent_id='.$province_id);
                    if($province_citys){
                        $address_string .= $province_id.'-0;';
                        foreach($province_citys as $k=>$v){
                            $linkage_ids_string .= $v['linkage_id'].',';
                        }
                    }
                }
            }
        }
        $address_string = rtrim($address_string, ';');
        $linkage_ids_string = rtrim($linkage_ids_string, ',');
        $DeductionAddressRel = new PromotionDeductionAddressRel();
        $DeductionAddressRel->promotion_id = $onlineArticleInfo->promotion_id;
        $DeductionAddressRel->province_city_rel = $address_string;
        $DeductionAddressRel->province_city_ids = $linkage_ids_string;
        $DeductionAddressRel->create_time = time();
        $DeductionAddressRel->save();
        
        /***修改其他的状态 域名状态***/
        if ($info->promotion_type == 0 || $info->promotion_type == 3) {
            $gotoDomainInfo = DomainList::model()->findByPk($info->goto_domain_id);
            if ($gotoDomainInfo->status == 0) {
                $gotoDomainInfo->status = 1;
                $gotoDomainInfo->update_time = time();
                $gotoDomainInfo->save();
            }
            if ($info->white_domain_id != 0) {
                $whiteDomainInfo = DomainList::model()->findByPk($info->white_domain_id);
                if ($whiteDomainInfo->status == 0) {
                    $whiteDomainInfo->status = 1;
                    $whiteDomainInfo->update_time = time();
                    $whiteDomainInfo->save();
                }
            }
        }
        if ($domain_ids) {
            //更新推广-域名关联表
            PromotionDomain::model()->updateProDomains($id,$domain_ids);
            //修改所选择域名的状态、推广类型、更新时间
            $update_data = array(
                'status'=>1,
                'promotion_type'=>$this->post('promotion_type'),
                'update_time'=>time(),
            );
            DomainList::model()->updateDomains($domain_ids,$update_data,1);
        }
        $msgarr = array('state' => 1, 'url' => $this->createUrl('promotion/index') . '?p=' . $_GET['p'] . '');
        $logs = "添加了新的推广信息：" . $id;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            //添加域名推广记录 edit lxj 2018-12-26
            $domain_log = array();
            if ($domain_ids) {
                $domainInfo = Dtable::toArr(DomainList::model()->findAll(
                    array(
                        'select'=>'domain',
                        'condition'=>' id in ('.implode(',',$domain_ids).')'
                    )
                ));
                foreach ($domainInfo as $value) {
                    $domain_log[] = array(
                        'from_domain'=>'',
                        'domain'=>$value['domain'],
                    );
                }
            }else {
                $domain_log[] = array(
                    'from_domain'=>'',
                    'domain'=>'',
                );
            }
            DomainPromotionChange::model()->addChangeLogs($id, $domain_log,0);
            //新增和修改之后的动作
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 获取统计js代码
     * @param $p_idsite
     * @return mixed
     * author: yjh
     */
    private
    function getPiwikJS($p_idsite)
    {
        $config = Yii::app()->params['basic'];
        $url = $config['piwik_url'] . '?module=API&method=SitesManager.getJavascriptTag&idSite=' . $p_idsite . '&format=JSON&token_auth=' . $config['piwik_token'];
        $contents = json_decode(file_get_contents($url))->value;
        return $contents;
    }

    /**
     * 获取idsite,如果域名已添加则不需要新增，如果没有则新增
     * @param $p_domain
     * @return mixed|null
     * author: yjh
     */
    private
    function getIdSite($p_domain)
    {
        if (!$p_domain) {
            $this->msg(array('state' => 0, 'msgwords' => '未选择推广域名'));
        }
        CActiveRecord::$db = Yii::app()->getPiwikDb();
        $PiwikSiteInfo = PiwikSite::model()->find('main_url=:main_url', array(':main_url' => $p_domain));
        if ($PiwikSiteInfo) $idSite = $PiwikSiteInfo->idsite;
        else {
            $PiwikSite = new PiwikSite();
            $PiwikSite->name = $p_domain;
            $PiwikSite->main_url = $p_domain;
            $PiwikSite->ts_created = date('Y-m-d H:i:s', time());
            $PiwikSite->timezone = 'Asia/Shanghai';
            $PiwikSite->currency = 'USD';
            $PiwikSite->type = 'website';
            $PiwikSite->save();
            $idSite = $PiwikSite->primaryKey;
        }
        CActiveRecord::$db = $db = Yii::app()->getDb();
        return $idSite;
    }

    /**
     * 修改推广
     * author: yjh
     * 推广多域名修改 lxj 2018-12-28
     */
    public
    function actionEdit()
    {
        $page = array();
        $id = $this->get('id');
        $info = Promotion::model()->findByPk($id);
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }

        //显示表单
        if (!$_POST) {

            $page['info'] = $this->toArr($info);
            $financePayInfo = InfancePay::model()->findByPk($info->finance_pay_id);
            $page['info']['partner'] = Partner::model()->findByPk($financePayInfo->partner_id)->name;
            $page['info']['channel_code'] = Channel::model()->findByPk($financePayInfo->channel_id)->channel_code;
            $page['info']['channel_name'] = Channel::model()->findByPk($financePayInfo->channel_id)->channel_name;
            $page['info']['online_date'] = date("Y-m-d", $financePayInfo->online_date);
            $page['info']['charging_type'] = vars::get_field_str('charging_type', $financePayInfo->charging_type);
            $page['info']['unit_price'] = $financePayInfo->unit_price;
            $page['info']['formula'] = vars::get_field_str('charging_formula', $financePayInfo->charging_type);
            $page['info']['wechat_group'] = WeChatGroup::model()->findByPk($financePayInfo->weixin_group_id)->wechat_group_name;
            $page['info']['tg_name'] = AdminUser::model()->getUserNameByPK($financePayInfo->sno);
            $page['info']['sno'] = $financePayInfo->sno;
            $domain_ids = PromotionDomain::model()->getPromotionDomains($id);
            $domain_list = array();
            if ($domain_ids) {
                $domain_list = Dtable::toArr(DomainList::model()->findAll('id in ('.implode(',',$domain_ids).')'));
            }
            $page['info']['domain_list'] = $domain_list;
            //推广扣量地址设置
            $deduction_address_result = PromotionDeductionAddressRel::model()->find('promotion_id=' . $id);
            if ($deduction_address_result) {
                $address_info = array();
                $address_info_result = explode(';', $deduction_address_result['province_city_rel']);
                $address_info['province_num'] = count($address_info_result);
                foreach ($address_info_result as $val) {
                    $lin_array = explode('-', $val);
                    $address_info['provinces'][] = $lin_array[0];
                    $address_info['citys'][] = $lin_array[1];
                }
            }
            $province_data = Linkage::model()->findAll('parent_id = 1');

            $this->render('update', array('page' => $page, 'province_data' => $province_data, 'address_info' => $address_info));
            exit;
        }
        $last_domain =trim($this->post('last_domain'),',') ? explode(',',trim($this->post('last_domain'))):0;
        $domain_ids = trim($this->post('domain_ids'),',') ? explode(',',trim($this->post('domain_ids'))):0;
        asort($last_domain);
        asort($domain_ids);
//        $cnzz_code_id = $this->post('cnzz_code_id');
//        $ret = DomainList::model()->findByPk($last_domain);
        $url_rule = $this->post('rule');
        $last_goto_domain = $this->post('last_goto_domain');
        $last_white_domain = $this->post('last_white_domain');
        $last_promotion_type = $info->promotion_type;
        //不使用白域名 lxj 2018-12-28
        $white_domain_id = 0;
//        $info->domain_id = $this->post('domain_id');
        $info->promotion_type = $this->post('promotion_type');
        $info->goto_domain_id = $this->post('goto_domain_id');
        $info->independent_cnzz = $this->post('indCnzz');
//        $info->cnzz_code_id = $cnzz_code_id;
        $info->total_cnzz = $this->post('totalCnzz');
        $article_id = $this->post('article_id');
        $info->minus_proportion = $this->post('minus_proportion');
        $info->is_pc_show = $this->post('is_pc_show');
        $info->pc_url = $this->post('pc_url');
        $info->url_rule = $url_rule;
        //不使用白域名 lxj 2018-12-28
        $info->is_white_domain = 1;
        $info->white_domain_id = $white_domain_id;
        $address_provinces = $this->post('provinces');
        $address_citys = $this->post('citys');
        $info->line_type = $this->post('line_type');
        if ($info->is_pc_show == 1) {
            if ($info->pc_url == '') $this->msg(array('state' => 0, 'msgwords' => '未填写PC端访问网址'));
        }
        if (is_int($info->minus_proportion)) {
            $this->msg(array('state' => 0, 'msgwords' => '扣量比例不是数字'));
        }

        //表单验证
//        if ($info->promotion_type != 1) {
//            if (!$domain_ids) $this->msg(array('state' => 0, 'msgwords' => '未选择推广域名'));
//                //获取统计piwik 域名id
//                $info->idsite = $this->getIdSite($domain);
//                $info->three_cnzz = $this->getPiwikJS($info->idsite);
//            }
//        }
        //当链接类型为普通时
        if ($info->promotion_type == 0 || $info->promotion_type == 3) {
            if ($info->goto_domain_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择跳转域名'));
        }

        if ($info->line_type == 0) {
            if ($article_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择素材'));

            $onlineArticleInfo = OnlineMaterialManage::model()->find('promotion_id = :promotion_id AND is_main_page=1', array(":promotion_id" => $id));
            if ($onlineArticleInfo == null) {
                $onlineArticleInfo = new OnlineMaterialManage;
            }

        if ($info->origin_template_id != $article_id ) {
            //如果有修改模板，则需要重新覆盖
            $articleInfo = $this->toArr(MaterialArticleTemplate::model()->findByPk($article_id));
            if (empty($articleInfo)) $this->msg(array('state' => 0, 'msgwords' => '不存在该模板'));
            //如果article_type不等于3
            if ($articleInfo['article_type'] != 3) {
                $onlineArticleInfo->promotion_id = $id;
                $onlineArticleInfo->channel_id = $info->channel_id;
                $onlineArticleInfo->partner_id = Channel::model()->findByPk($info->channel_id)->partner_id;
                $onlineArticleInfo->origin_template_id = $article_id;
                $onlineArticleInfo->article_title = $articleInfo['article_title'];
                $onlineArticleInfo->content = $articleInfo['content'];
                $onlineArticleInfo->tag = $articleInfo['tag'];
                $onlineArticleInfo->cat_id = $articleInfo['cat_id'];
                $onlineArticleInfo->suspension_text = $articleInfo['suspension_text'];
                $onlineArticleInfo->support_staff_id = $articleInfo['support_staff_id'];
                $onlineArticleInfo->article_type = $articleInfo['article_type'];
                $onlineArticleInfo->top_img = $articleInfo['top_img'];
                $onlineArticleInfo->avater_img = $articleInfo['avater_img'];
                $onlineArticleInfo->idintity = $articleInfo['idintity'];
                $onlineArticleInfo->first_audio = $articleInfo['first_audio'];
                $onlineArticleInfo->second_audio = $articleInfo['second_audio'];
                $onlineArticleInfo->third_audio = $articleInfo['third_audio'];
                $onlineArticleInfo->top_text = $articleInfo['top_text'];
                $onlineArticleInfo->addfans_type = $articleInfo['addfans_type'];
                $onlineArticleInfo->addfans_text = $articleInfo['addfans_text'];
                $onlineArticleInfo->psq_id = $articleInfo['psq_id'];
                $onlineArticleInfo->article_code = $articleInfo['article_code'];
                $onlineArticleInfo->review_id = $articleInfo['review_id'];
                $onlineArticleInfo->bottom_type = $articleInfo['bottom_type'];
                $onlineArticleInfo->top_color = $articleInfo['top_color'];
                $onlineArticleInfo->avater_tag = $articleInfo['avater_tag'];
                $onlineArticleInfo->level_tag = $articleInfo['level_tag'];
                $onlineArticleInfo->xingxiang = $articleInfo['xingxiang'];
                $onlineArticleInfo->is_vote = $articleInfo['is_vote'];
                $onlineArticleInfo->vote_id = $articleInfo['vote_id'];
                $onlineArticleInfo->is_order = $articleInfo['is_order'];
                $onlineArticleInfo->order_id = $articleInfo['order_id'];
                $onlineArticleInfo->pop_time = $articleInfo['pop_time'];
                $onlineArticleInfo->char_intro = $articleInfo['char_intro'];
                $onlineArticleInfo->chat_content = $articleInfo['chat_content'];
                $onlineArticleInfo->is_hide_title = $articleInfo['is_hide_title'];
                $onlineArticleInfo->is_fill = $articleInfo['is_fill'];
                $onlineArticleInfo->release_date = $articleInfo['release_date'];
                $onlineArticleInfo->descriptive_statement = $articleInfo['descriptive_statement'];
                $onlineArticleInfo->cover_url = $articleInfo['cover_url'];
                $onlineArticleInfo->update_time = time();
                $onlineArticleInfo->save();
                $onlineArticleId = $onlineArticleInfo->primaryKey;
                $info->article_id = $onlineArticleId;
            } else {
                $result = MaterialArticleTemplate::model()->findAll('article_code=:article_code', array(":article_code" => $articleInfo['article_code']));
                foreach ($result as $value) {
                    $update_info = array();
                    $update_info['promotion_id'] = $id;
                    $update_info['origin_template_id'] = $article_id;
                    $update_info['article_title'] = $value['article_title'];
                    $update_info['content'] = $value['content'];
                    $update_info['tag'] = $value['tag'];
                    $update_info['suspension_text'] = $value['suspension_text'];
                    $update_info['review_id'] = $value['review_id'];
                    $update_info['cat_id'] = $value['cat_id'];
                    $update_info['update_time'] = time();
                    $update_info['support_staff_id'] = $value['support_staff_id'];
                    $update_info['article_code'] = $value['article_code'];
                    $update_info['article_type'] = $value['article_type'];
                    $update_info['top_img'] = $value['top_img'];
                    $update_info['avater_img'] = $value['avater_img'];
                    $update_info['idintity'] = $value['idintity'];
                    $update_info['first_audio'] = $value['first_audio'];
                    $update_info['second_audio'] = $value['second_audio'];
                    $update_info['third_audio'] = $value['third_audio'];
                    $update_info['top_text'] = $value['top_text'];
                    $update_info['addfans_type'] = $value['addfans_type'];
                    $update_info['addfans_text'] = $value['addfans_text'];
                    $update_info['psq_id'] = $value['psq_id'];
                    $update_info['top_color'] = $value['top_color'];
                    $update_info['avater_tag'] = $value['avater_tag'];
                    $update_info['level_tag'] = $value['level_tag'];
                    $update_info['bottom_type'] = $value['bottom_type'];
                    $update_info['xingxiang'] = $value['xingxiang'];
                    $update_info['is_vote'] = $value['is_vote'];
                    $update_info['vote_id'] = $value['vote_id'];
                    $update_info['is_order'] = $value['is_order'];
                    $update_info['order_id'] = $value['order_id'];
                    $update_info['pop_time'] = $value['pop_time'];
                    $update_info['char_intro'] = $value['char_intro'];
                    $update_info['chat_content'] = $value['chat_content'];
                    $update_info['cover_url'] = $value['cover_url'];
                    $update_info['payment'] = $value['payment'];
                    $update_info['is_hide_title'] = $value['is_hide_title'];
                    $update_info['is_fill'] = $value['is_fill'];
                    $update_info['link'] = $value['link'];
                    $update_info['descriptive_statement'] = $value['descriptive_statement'];
                    $update_info['release_date'] = $value['release_date'];
                    $res = OnlineMaterialManage::model()->updateAll($update_info, "`promotion_id`={$id} AND `is_main_page`={$value['is_main_page']}");
                    if (!$res) {
                        $onlineMaterialManage = new OnlineMaterialManage();
                        $onlineMaterialManage->promotion_id = $id;
                        $onlineMaterialManage->channel_id = $info->channel_id;
                        $onlineMaterialManage->partner_id = Channel::model()->findByPk($info->channel_id)->partner_id;
                        $onlineMaterialManage->origin_template_id = $article_id;
                        $onlineMaterialManage->article_title = $value['article_title'];
                        $onlineMaterialManage->content = $value['content'];
                        $onlineMaterialManage->tag = $value['tag'];
                        $onlineMaterialManage->review_id = $value['review_id'];
                        $onlineMaterialManage->cat_id = $value['cat_id'];
                        $onlineMaterialManage->suspension_text = $value['suspension_text'];
                        $onlineMaterialManage->article_code = $value['article_code'];
                        $onlineMaterialManage->support_staff_id = $value['support_staff_id'];
                        $onlineMaterialManage->article_type = $value['article_type'];
                        $onlineMaterialManage->top_img = $value['top_img'];
                        $onlineMaterialManage->avater_img = $value['avater_img'];
                        $onlineMaterialManage->idintity = $value['idintity'];
                        $onlineMaterialManage->first_audio = $value['first_audio'];
                        $onlineMaterialManage->second_audio = $value['second_audio'];
                        $onlineMaterialManage->third_audio = $value['third_audio'];
                        $onlineMaterialManage->top_text = $value['top_text'];
                        $onlineMaterialManage->addfans_type = $value['addfans_type'];
                        $onlineMaterialManage->addfans_text = $value['addfans_text'];
                        $onlineMaterialManage->psq_id = $value['psq_id'];
                        $onlineMaterialManage->top_color = $value['top_color'];
                        $onlineMaterialManage->avater_tag = $value['avater_tag'];
                        $onlineMaterialManage->level_tag = $value['level_tag'];
                        $onlineMaterialManage->bottom_type = $value['bottom_type'];
                        $onlineMaterialManage->xingxiang = $value['xingxiang'];
                        $onlineMaterialManage->is_vote = $value['is_vote'];
                        $onlineMaterialManage->vote_id = $value['vote_id'];
                        $onlineMaterialManage->order_id = $value['order_id'];
                        $onlineMaterialManage->is_order = $value['is_order'];
                        $onlineMaterialManage->pop_time = $value['pop_time'];
                        $onlineMaterialManage->char_intro = $value['char_intro'];
                        $onlineMaterialManage->chat_content = $value['chat_content'];
                        $onlineMaterialManage->cover_url = $value['cover_url'];
                        $onlineMaterialManage->payment = $value['payment'];
                        $onlineMaterialManage->is_hide_title = $value['is_hide_title'];
                        $onlineMaterialManage->is_fill = $value['is_fill'];
                        $onlineMaterialManage->is_main_page = $value['is_main_page'];
                        $onlineMaterialManage->link = $value['link'];
                        $onlineMaterialManage->descriptive_statement = $value['descriptive_statement'];
                        $onlineMaterialManage->release_date = $value['release_date'];
                        $onlineMaterialManage->update_time = time();
                        $onlineMaterialManage->create_time = time();
                        $onlineMaterialManage->save();
                        if ($value['is_main_page'] == 1) {
                            $onlineArticleId = $onlineMaterialManage->primaryKey;
                            $info->article_id = $onlineArticleId;
                        }
                    }
                }
            }
        }

        }

        //cnzz扣量地区设置
        $address_string = $linkage_ids_string = '';
        foreach($address_provinces as $key=>$value){
            $province_id = intval($value);
            if($province_id > 0){
                $city_id = intval($address_citys[$key]);
                if($city_id > 0){
                    $address_string .= $province_id.'-'.$city_id.';';
                    $linkage_ids_string .= $province_id.','.$city_id.',';
                }else{
                    $province_citys = Linkage::model()->findAll('parent_id='.$province_id);
                    if($province_citys){
                        $address_string .= $province_id.'-0;';
                        foreach($province_citys as $k=>$v){
                            $linkage_ids_string .= $v['linkage_id'].',';
                        }
                    }
                }
            }
        }
        $address_string = rtrim($address_string, ';');
        $linkage_ids_string = rtrim($linkage_ids_string, ',');
        $update_info = array();
        $update_info['province_city_rel'] = $address_string;
        $update_info['province_city_ids'] = $linkage_ids_string;
        $update_info['update_time'] = time();
        $addressResult = PromotionDeductionAddressRel::model()->updateAll($update_info, "`promotion_id`={$id}");
        if(!$addressResult){
            $DeductionAddressRel = new PromotionDeductionAddressRel();
            $DeductionAddressRel->promotion_id = $id;
            $DeductionAddressRel->province_city_rel = $address_string;
            $DeductionAddressRel->province_city_ids = $linkage_ids_string;
            $DeductionAddressRel->create_time = $DeductionAddressRel->update_time = time();
            $DeductionAddressRel->save();
        }
        Yii::app()->redis->deleteValue('city_ids:'.$id);

        $redis_flag = Yii::app()->params['basic']['is_redis'];
        if ($redis_flag == 1) {
            $ret = Yii::app()->redis->getValue('article:' . $id);
            if ($ret) Yii::app()->redis->deleteValue('article:' . $id);
            $ret = Yii::app()->redis->getValue('promotion:' . $id);
            if ($ret) Yii::app()->redis->deleteValue('promotion:' . $id);
            $ret = Yii::app()->redis->getValue('psq:' . $id);
            if ($ret) Yii::app()->redis->deleteValue('psq:' . $id);
        }
        if ($info->line_type == 0) {
            $info->origin_template_id = $article_id;
        }
        $dbresult = $info->save();
        $id = $info->primaryKey;
        //清除redis
        //更新推广-域名关联表
        PromotionDomain::model()->updateProDomains($id,$domain_ids);

        /***修改其他的状态 域名状态***/
        //如果有修改域名需要将域名改为备用状态
        if (in_array($info->promotion_type,array(0,3)) || in_array($last_promotion_type,array(0,3))) {
            if ($last_goto_domain != $this->post('goto_domain_id') || $last_promotion_type!=$info->promotion_type) {
                if ($info->promotion_type == 0 || $info->promotion_type == 3) {
                    $gotoDomainInfo = DomainList::model()->findByPk($info->goto_domain_id);
                    //已下线推广不修改域名状态为上线
                    if ($gotoDomainInfo && $gotoDomainInfo->status == 0 && $info->status !=1) {
                        $gotoDomainInfo->status = 1;
                        $gotoDomainInfo->update_time = time();
                        $gotoDomainInfo->save();
                    }
                }

                if ($last_promotion_type == 0 || $last_promotion_type == 3) {
                    $lastGotoDomainInfo = DomainList::model()->findByPk($last_goto_domain);
                    $result = Promotion::model()->find('goto_domain_id='.$last_goto_domain.' and status!=1');
                    if (!$result && $lastGotoDomainInfo->status == 1) {
                        $lastGotoDomainInfo->status = 0;//改为备用
                        $lastGotoDomainInfo->update_time = time();
                        $lastGotoDomainInfo->save();
                    }
                }
            }
            if ($last_white_domain != $white_domain_id) {
                if ($info->promotion_type == 0 || $info->promotion_type == 3) {
                    if ($info->white_domain_id != 0) {
                        $whiteDomainInfo = DomainList::model()->findByPk($info->white_domain_id);
                        //已下线推广不修改域名状态为上线
                        if ($whiteDomainInfo->status == 0 && $info->status !=1) {
                            $whiteDomainInfo->status = 1;
                            $whiteDomainInfo->update_time = time();
                            $whiteDomainInfo->save();
                        }
                    }
                }
                if ($last_promotion_type == 0 || $last_promotion_type == 3) {
                    if ($last_white_domain != 0) {
                        $lastWhiteDomainInfo = DomainList::model()->findByPk($last_white_domain);
                        $result = Promotion::model()->find('white_domain_id='.$last_goto_domain.' and status!=1 and is_white_domain=0');
                        //已下线推广不修改域名状态为上线
                        if (!$result && $lastWhiteDomainInfo->status == 1) {
                            $lastWhiteDomainInfo->status = 0;//改为备用
                            $lastWhiteDomainInfo->update_time = time();
                            $lastWhiteDomainInfo->save();
                        }
                    }
                }
            }
        }
        //推广域名状态修改
        if (!is_array($last_domain)) $last_domain = array();
        if (!is_array($domain_ids)) $domain_ids = array();
        if ($last_domain != $domain_ids || $last_promotion_type != $this->post('promotion_type')) {
            $time = time();
            //需修改为上线状态的域名 $domain_ids,$last_domain都必须为数组
            $online_domain = array_diff($domain_ids,$last_domain);
            //需修改为备用状态的域名
            $down_domain = array_diff($last_domain,$domain_ids);
            $change_domain = array_merge($online_domain,$down_domain);
            $domain_info = array();
            if ($change_domain) {
                $domain_info = Dtable::toArr(DomainList::model()->findAll(' id in ('.implode(',',$change_domain).')'));
                $domain_info = array_combine(array_column($domain_info,'id'),array_column($domain_info,'domain'));
            }
            //已下线推广不修改域名状态为上线
            if ($domain_ids && $info->status !=1) {
                $online_data = array(
                    'status'=>1,
                    'promotion_type'=>$this->post('promotion_type'),
                    'update_time'=>$time,
                );
                DomainList::model()->updateDomains($domain_ids,$online_data,1);
            }

            if ($down_domain) {
                $down_data = array(
                    'status'=>0,
                    'promotion_type'=>0,
                    'update_time'=>$time,
                );
                DomainList::model()->updateDomains($down_domain,$down_data,0);
            }
            //添加域名使用记录
            //优先添加新、旧域名都有的记录；有剩余旧域名，则新域名为空；有剩余新域名，则旧域名为空
            $domain_change = array();
            //已取消的旧域名记录
            foreach ($down_domain as $key=>$value) {
                $new_domain = '';
                if (isset($online_domain[$key])) {
                    $new_domain = $domain_info[$online_domain[$key]];
                    unset($online_domain[$key]);
                }
                $domain_change[] = array(
                    'from_domain'=>$domain_info[$value],
                    'domain'=>$new_domain,
                );
            }
            //没有旧域名可替换的新域名记录
            if ($online_domain) {
                foreach ($online_domain as $value) {
                    $domain_change[] = array(
                        'from_domain'=>'',
                        'domain'=>$domain_info[$value],
                    );
                }
            }
            DomainPromotionChange::model()->addChangeLogs($id,$domain_change,0);
//            $domainInfo->status = 1;//改为上线
//            $domainInfo->uid = $operator;
//            $domainInfo->promotion_type = $this->post('promotion_type');
//            $domainInfo->update_time = time();
//
//            $lastDomainInfo = DomainList::model()->findByPk($last_domain);
//
//            $financePayInfo = InfancePay::model()->findByPk($info->finance_pay_id);
//
//
//
//
//            $operator = $financePayInfo->sno;
//            if ($lastDomainInfo) {
//                if ($lastDomainInfo->status == 1) {
//                    $lastDomainInfo->status = 0;
//                }
//                $lastDomainInfo->uid = 0;
//                $lastDomainInfo->promotion_type = 0;
//                $lastDomainInfo->update_time = time();
//                $lastDomainInfo->save();
//            }
//
//            if ($this->post('domain_id')) {
//                $domainInfo = DomainList::model()->findByPk($this->post('domain_id'));
//                $domainInfo->status = 1;//改为上线
//                $domainInfo->uid = $operator;
//                $domainInfo->promotion_type = $this->post('promotion_type');
//                $domainInfo->update_time = time();
//                $domainInfo->save();
//                DomainPromotionChange::model()->change($id, $domainInfo->domain);
//            } else {
//                DomainPromotionChange::model()->change($id, '');
//
//            }
        }
        $msgarr = array('state' => 1, 'url' => $this->get('backurl')); //保存的话，跳转到之前的列表
        $logs = "修改了推广信息：" . $id;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            //新增和修改之后的动作
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 删除推广
     * author: yjh
     */
    public
    function actionDelete()
    {
        if ($this->get('id') == '') $this->msg(array('state' => 0, 'msgwords' => '未传入数据'));
        $id = $this->get('id');

        $info = Promotion::model()->findByPk($id);
        $goto_domain_id = $info->goto_domain_id;
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }
        //无论什么状态都能删除 域名改成备用状态 不管打款
        if ($info->status == 1) $this->msg(array('state' => 0, 'msgwords' => '此推广正在上线不能删除'));
        $info->delete();

        OnlineMaterialManage::model()->deleteAll('promotion_id = :promotion_id', array(':promotion_id' => $id));
        //删除推广-域名关联表 lxj 2019-01-02
        PromotionDomain::model()->deleteProDomains($id);

        //判断跳转域名有没有在使用 如果没有在使用改为备用
        $gotoDomainInfo = DomainList::model()->findByPk($goto_domain_id);

        $result = Promotion::model()->findByAttributes(array('goto_domain_id' => $goto_domain_id));
        if (!$result && $gotoDomainInfo->status == 1) {
            $gotoDomainInfo->status = 0;//改为备用
            $gotoDomainInfo->update_time = time();
            $gotoDomainInfo->save();
        }

        $this->logs("删除了推广：" . $id);
        $this->msg(array('state' => 1, 'msgwords' => '删除推广成功！', 'url' => $this->get('url')));
    }

    /**
     * 暂停和启动推广
     * author: yjh
     */
    public
    function actionStop()
    {
        if ($this->get('id') == '') $this->msg(array('state' => 0, 'msgwords' => '未传入数据'));
        $id = $this->get('id');
        $status = $this->get('status');
        $info = Promotion::model()->findByPk($id);
        if ($status == 0) {
            $info->status = 2;
            $log = "暂停了推广：" . $id;
            $msg = "暂停推广：$id 成功";
        } else {
            $info->status = 0;
            $log = "启动了推广：" . $id;
            $msg = "启动推广：$id 成功";
        }
        $redis_flag = Yii::app()->params['basic']['is_redis'];
        if ($redis_flag == 1) {
            $ret = Yii::app()->redis->getValue('article:' . $id);
            if ($ret) Yii::app()->redis->deleteValue('article:' . $id);
            $ret = Yii::app()->redis->getValue('promotion:' . $id);
            if ($ret) Yii::app()->redis->deleteValue('promotion:' . $id);
            $ret = Yii::app()->redis->getValue('psq:' . $id);
            if ($ret) Yii::app()->redis->deleteValue('psq:' . $id);
        }
        $info->save();
        $this->logs($log);
        $this->msg(array('state' => 1, 'msgwords' => $msg));
    }


    /**
     * AJAX获取城市
     * author: hlc
     */
    public function actionGetProvince(){
        $parent_id = intval($this->post('id'));
        if($parent_id < 1){
            $result = array();
            $result['IsSuccess'] = false;
            $result['Message'] = '参数错误';
            echo json_encode($result);
            exit;
        }
        $data = Linkage::model()->findAll('parent_id=:parent_id', array(':parent_id'=>$parent_id));
        $result = array();
        $result['IsSuccess'] = true;
        $result['Message'] = '成功';
        $result['data'] = CJSON::decode(CJSON::encode($data));
        echo json_encode($result);
        exit;
    }

    /**
     * AJAX获取城市
     * author: hlc
     */
    public function actionCity(){
//        if(isset($_POST['province_id'])){
//           $data = Linkage::model()->find('linkage_id = '.$_POST['province_id']);
// my_print($data);
//        }
        $province_id = $this->post('province_id');
        if(isset($province_id)) {
            $longcode = Linkage::model()->findBySql("select * from linkage where parent_id =".$province_id);

            $data = Linkage::model()->findAllBySql("select linkage_id,linkage_name from `linkage` where longcode like '" . $longcode['longcode'] . "%'");
            foreach ($data as $key => $val) {
                echo CHtml::tag('option', array('value' => $val['linkage_id']), CHtml::encode($val['linkage_name']), true);
            }
        }
        else
        {
            echo CHtml::tag("option", array("value" => ''), '城市', true);
        }
    }

    /**
     * AJAX获取合作商对应渠道
     * author: yjh
     */
    public
    function actionGetChannel()
    {
        if ($this->post('partnerId')) {
            $data = Channel::model()->findAll('partner_id = :partner_id and business_type != 1', array(':partner_id' => $this->post('partnerId')));
            if (empty($data)) echo CHtml::tag('option', array('value' => ''), CHtml::encode('没有渠道信息，请选择其他合作商'), true);

            foreach ($data as $key => $val) {
                echo CHtml::tag('option', array('value' => $val['id']), CHtml::encode($val['channel_name']), true);
            }
        }
    }

    /**
     * AJAX获取合作商对应渠道  渠道数据表
     * author: fang
     */
    public
    function actionGetChannelData()
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
     * AJAX获取跳转域名
     * author: hlc
     */
    public function actionGetGotoDomain(){
        $application_type = $_POST['application_type'];
        $sno = $_POST['tg_id'];
        $old_goto_id = $_POST['old_goto_id'];
        $array = array();

        if($application_type != '' && $sno){
            $result = DomainList::model()->getGotoDomains($sno,$application_type);
            foreach ($result as $value){
                $array[] = array('id'=>$value['id'],'domain'=>$value['domain'],'is_public_domain'=>$value['is_public_domain'],'is_https'=>$value['is_https'],'application_type'=>$value['application_type']);
            }
            echo json_encode($array);
            }
    }

    /**
     * AJAX获取合作商对应渠道  渠道数据表
     * author: fang
     */
    public
    function actionGetOnlineChannelDate()
    {
        if ($this->post('channel_id')) {
            if ($this->post('spcl') == 1)
                $data = InfancePay::model()->findAll('channel_id = :channel_id and type=1', array(':channel_id' => $this->post('channel_id')));
            else
                $data = InfancePay::model()->findAll('channel_id = :channel_id and type!=1 order by online_date desc', array(':channel_id' => $this->post('channel_id')));
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
    public
    function actionGetOnlineDate()
    {
        if ($this->post('channel_id')) {
            if ($this->post('spcl') == 1)
                $data = InfancePay::model()->findAll('channel_id = :channel_id and type=1', array(':channel_id' => $this->post('channel_id')));
            else
                $data = InfancePay::model()->findAll('channel_id = :channel_id and type=0', array(':channel_id' => $this->post('channel_id')));
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
     * AJAX获取其他信息
     * 打款id
     * 渠道编码
     * 业务类型
     * 计费方式
     * 单价
     * 微信号小组
     * 推广人员
     * 跳转域名和白域名
     * author: yjh
     */
    public
    function actionGetOtherData()
    {
        if ($this->post('channel_id') && $this->post('onlineDate')) {
            $channelData = Channel::model()->findByPk($this->post('channel_id'));
            $date = InfancePay::model()->find('channel_id = :channel_id and online_date = :online_date', array(':channel_id' => $this->post('channel_id'), 'online_date' => $this->post('onlineDate')));
            if (empty($date)) {
                $result = array('fpay_id' => '', 'channelCode' => $channelData->channel_code, 'chgId' => '无数据', 'unitPrice' => '无数据', 'formula' => '无数据', 'wechat_group' => '无数据', 'business_type' => '无数据');
                echo json_encode($result);
            }
            if ($date->sno) {
                $line_type = $this->get('line_type') ? $this->get('line_type'):0;
                $gotoDomains = DomainList::model()->getGotoDomains($date->sno,$line_type);
                if (empty($gotoDomains)) $goto_domains = CHtml::tag('option', array('value' => ''), CHtml::encode('该推广人员没有跳转域名'), true);
                else {
                    $goto_domains = CHtml::tag('option', array('value' => ''), CHtml::encode('请选择跳转域名'), true);
                    foreach ($gotoDomains as $key => $val) {
                        $goto_domains .= CHtml::tag('option', array('value' => $val['id']), CHtml::encode($val['domain']), true);
                    }
                }

                $whiteDomains = DomainList::model()->getWhiteDomains($date->sno);
                if (empty($whiteDomains)) $white_domains = CHtml::tag('option', array('value' => ''), CHtml::encode('该推广人员没有白域名'), true);
                else {
                    $white_domains = CHtml::tag('option', array('value' => ''), CHtml::encode('请选择白域名'), true);
                    foreach ($whiteDomains as $key => $val) {
                        $white_domains .= CHtml::tag('option', array('value' => $val['id']), CHtml::encode($val['domain']), true);
                    }
                }
            } else {
                $goto_domains = CHtml::tag('option', array('value' => ''), CHtml::encode('没有推广人员'), true);
                $white_domains = CHtml::tag('option', array('value' => ''), CHtml::encode('没有推广人员'), true);
            }
            $result = array(
                'fpay_id' => $date->id,
                'channelCode' => $channelData->channel_code,
                'chgId' => vars::get_field_str('charging_type', $date->charging_type),
                'unitPrice' => $date->unit_price,
                'business_type' => BusinessTypes::model()->findByPk($date->business_type)->bname,
                'formula' => vars::get_field_str('charging_formula', $date->charging_type),
                'wechat_group' => WeChatGroup::model()->findByPk($date->weixin_group_id)->wechat_group_name,
                'tg_name' => AdminUser::model()->getUserNameByPK($date->sno),
                'tg_id' => $date->sno,
                'goto_domains' => $goto_domains,
                'white_domains' => $white_domains
            );
            echo json_encode($result);
        } else {
            $goto_domains = CHtml::tag('option', array('value' => ''), CHtml::encode('请选择'), true);
            $white_domains = CHtml::tag('option', array('value' => ''), CHtml::encode('请选择'), true);
            $result = array('fpay_id' => '', 'channelCode' => '', 'chgId' => '', 'unitPrice' => '', 'formula' => '', 'wechat_group' => '', 'business_type' => '', 'tg_name' => '', 'tg_id' => '', 'goto_domains' => $goto_domains, 'white_domains' => $white_domains);
            echo json_encode($result);
        }
        exit;
    }

    /**
     * 获取图文列表
     * author: yjh
     */
    public
    function actionGetArticleList()
    {
        $params['where'] = '1';
        if ($this->get('search_type') == 'keys' && $this->get('search_txt')) {
            $params['where'] .= " and(article_title like '%" . $this->get('search_txt') . "%' and group_id != 0) ";
            $params['where'] .= " or(article_code like '%" . $this->get('search_txt') . "%' and group_id != 0) ";
        } else {
            $params['where'] .= " and(group_id != 0) ";
        }
        $sql = "select id,article_title,article_code from material_article_template where " . $params['where'] . " order by id desc";
        $page['list'] = Yii::app()->db->createCommand($sql)->queryAll();
        if (isset($_GET['jsoncallback'])) {
            $data['list'] = $page['list'];
            $this->msg(array('state' => 1, 'data' => $data, 'type' => 'jsonp'));
        }
    }

    /**
     * 获取合适的域名
     * author: yjh
     */
    public
    function actionGetSuitableDomains()
    {
        $application_type = $_POST['application_type']?$_POST['application_type']:"0";
        $cnzz_code_id = $_POST['cnzz_code_id'];
        $domain_id = $_POST['domain_id'];
        //非短域名推广时，域名类型为推广
        $promotion_type = $_POST['promotion_type']==3?3:0;
        if ($cnzz_code_id) {
            if($domain_id){
                $data = DomainList::model()->findAll('(status=0 or id='.$domain_id.') and domain_type='.$promotion_type.' and application_type='.$application_type.' and cnzz_code_id=' . $cnzz_code_id);
            }else{
                $data = DomainList::model()->findAll('status=0  and domain_type='.$promotion_type.' and application_type='.$application_type.' and cnzz_code_id=' . $cnzz_code_id);
            }

            if (empty($data)) {
                echo CHtml::tag('option', array('value' => ''), CHtml::encode('该组别没有域名，请选择其他组别'), true);
            }else{
                foreach ($data as $key => $val) {
                    if($val['is_public_domain'] == 1 && $val['is_https'] == 1){
                        $val['domain'] = $val['domain'] . '(https、公众号)';
                    } elseif ($val['is_https'] == 1) {
                        $val['domain'] = $val['domain'] . '(https)';
                    }elseif($val['is_public_domain'] == 1){
                        $val['domain'] = $val['domain'] . '(公众号)';
                    }
                    echo CHtml::tag('option', array('value' => $val['id']), CHtml::encode($val['domain']), true);
                }
            }

        } else {
            echo CHtml::tag('option', array('value' => ''), CHtml::encode('未选择组别'), true);
        }

    }

    /**
     * 根据推广id获取其他数据
     * author: yjh
     */
    public
    function actionGetDataById()
    {
        $promotion_id = $this->get('promotion_id');
        if (!$promotion_id) exit;
        $info = Promotion::model()->findByPk($promotion_id);
        if (!$info) exit;
        $channelInfo = Channel::model()->findByPk($info->channel_id);
        $partnerInfo = Partner::model()->findByPk($channelInfo->partner_id);
        $infanceInfo = InfancePay::model()->findByPk($info->finance_pay_id);
        $ret = array(
            'partner_name' => $partnerInfo->name,
            'channel_name' => $channelInfo->channel_name,
            'stat_date' => date('Y-m-d', $infanceInfo->online_date),
        );
        echo json_encode($ret);
        exit;
    }

    /**
     * 导出
     */
    public
    function actionExport()
    {
        $data = $this->getExportData(1);
        $headlist = array('ID', '上线日期', '域名', '合作商', '渠道名称', '渠道编码', '素材标题', '图文编码', '下单标题', '推广人员', '跳转链接', '文章链接', '计费方式', '单价', '微信号小组', '状态', '类型');
        $flie_name = '推广列表' . date('ymd');
        $temp_array = array();
        $data = $data['listdata']['list'];
        $count = count($data);

        for ($i = 0; $i < $count; $i++) {
            $line = $i;
            if ($data[$i]['order_id'] == 0) {
                $data[$i]['order_id'] = "无";
            }
            $status = vars::get_field_str('promotion_status', $data[$i]['status']);//状态

            $goto_url = helper::build_goto_link($data[$i]);
            if ($data[$i]['status'] == 2) $goto_url = '暂停中';
            elseif ($goto_url == 2) $goto_url = "被拦截";
            $all_domain='';
            foreach ($data[$i]['domain_list'] as $k=>$domain){
                if ($data[$i]['domain_list'][$k+1]) {
                    $all_domain.=$domain['domain']."\n";
                }else {
                    $all_domain.=$domain['domain'];
                }
            }

            $link_url = helper::build_tg_link($data[$i]);
            $all_link = '';
            if ($data[$i]['status'] == 2) {
                $all_link = "暂停中";
            }else {
                if (!$link_url) $link_url = "无";
                foreach ($link_url as $k=>$link){
                    $status_str = '';
                    if ($link['domain_status']!=0 && $link['domain_status']!=1) {
                        $status_str = '('.vars::get_field_str('domain_status', $link['domain_status']).')';
                    }
                    if ($data[$i]['domain_list'][$k+1]) {
                        $all_link.=$status_str.$link['domain']."\n";
                    }else{
                        $all_link.=$link['domain'].$status_str;
                    }

                }
            }

            $data[$i]['charging_type'] = vars::get_field_str('charging_type', $data[$i]['charging_type']);
            $data[$i]['promotion_type'] = vars::get_field_str('promotion_types', $data[$i]['promotion_type']);
            $temp_array[$line] = array(
                $data[$i]['id'],//ID
                date('Y-m-d', $data[$i]['online_date']),//上线日期
                $all_domain,//域名
                $data[$i]['partner_name'],//合作商
                $data[$i]['channel_name'],//渠道名称
                $data[$i]['channel_code'],//渠道编码
                $data[$i]['article_title'],//素材标题
                $data[$i]['article_code'],//图文编码
                $data[$i]['order_id'],//下单标题
                $data[$i]['csname_true'],//推广人员
                $goto_url,//跳转链接
                $all_link,//文章链接
                $data[$i]['charging_type'],//计费方式
                $data[$i]['unit_price'],//单价
                $data[$i]['wechat_group_name'],//微信号小组
                $status,//状态
                $data[$i]['promotion_type'],//类型
            );
            foreach ($temp_array[$line] as $key => $value) {
                $temp_array[$line][$key] = iconv('utf-8', 'gbk', $value);
            }
        }

        helper::downloadCsv($headlist, $temp_array, $flie_name);
    }

    public
    function getExportData($is_export = 0)
    {
        //搜索
        $params['where'] = '';
        //$params['where'] .= " and(a.status!=2) ";
        //渠道查询
        if ($this->get('channel_code') != '') {
            $params['where'] .= " and(c.channel_code like '%" . $this->get('channel_code') . "%') ";
        }
        if ($this->get('channel_name') != '') {
            $params['where'] .= " and(c.channel_name like '%" . $this->get('channel_name') . "%') ";
        }

        //合作商查询
        if ($this->get('partner') != '') {
            $params['where'] .= " and(p.name like '%" . $this->get('partner') . "%') ";
        }
        //ID
        if ($this->get('id') != '') $params['where'] .= " and(a.id = '" . $this->get('id') . "') ";
        //域名查询修改 lxj 2019-01-01
        if (trim($this->get('domain') != '')) {
            $domain = trim($this->get('domain'));
            $promotions = Yii::app()->db->createCommand()
                ->select('p.promotion_id')
                ->from('domain_list d')
                ->join('promotion_domain_rel  p', 'd.id=p.domain_id')
                ->where("d.domain like '%".$domain."%'")
                ->queryAll();
            if ($promotions) {
                $pro_ids = array_column($promotions,'promotion_id');
                $params['where'] .= " and (a.id in (".implode(',',$pro_ids).") )";
            }else {
                $params['where'] .= " and (a.id=0) ";
            }

        }
        //跳转域名
        $goto_domain = trim($this->get('goto_domain'));
        if (!empty($goto_domain)){
            $goto_ids = array();
            $goto_ids_result = DomainList::model()->findAll("(domain like '%" . $goto_domain . "%')");
            foreach($goto_ids_result as $val){
                $goto_ids[] = $val['id'];
            }
            if(count($goto_ids)){
                $params['where'] .= " and a.goto_domain_id IN (".implode(',', $goto_ids).")";
            }else{
                $params['where'] .= " and a.goto_domain_id=-1";
            }
        }
        //白域名
        $white_domain_name = trim($this->get('white_domain_name'));
        if (!empty($white_domain_name)){
            $white_ids = array();
            $white_ids_result = DomainList::model()->findAll("(domain like '%" . $white_domain_name . "%')");
            foreach($white_ids_result as $val){
                $white_ids[] = $val['id'];
            }
            if(count($white_ids)){
                $params['where'] .= " and a.white_domain_id IN (".implode(',', $white_ids).")";
            }else{
                $params['where'] .= " and a.white_domain_id=-1";
            }
        }
        //文章名称
        if ($this->get('artName') != '') $params['where'] .= " and(o.article_title like '%" . $this->get('artName') . "%') ";
        //计费方式
        if ($this->get('chgId') != '') $params['where'] .= " and(f.charging_type = '" . $this->get('chgId') . "') ";
        //业务
        if ($this->get('business') != '' && $this->get('business') == "2") {
            $params['where'] .= " and(g.bid = " . Yii::app()->params['basic']['dx_bid'] . ") ";
        } elseif ($this->get('business') != '' && $this->get('business') == "1") {
            $params['where'] .= " and(g.bid != " . Yii::app()->params['basic']['dx_bid'] . ") ";
        }
        //操作用户
        if ($this->get('user_id') != '') $params['where'] .= " and(f.sno = '" . $this->get('user_id') . "') ";
        //状态
        if ($this->get('status') != '') $params['where'] .= " and(a.status = '" . $this->get('status') . "') ";
        //白域名
        if ($this->get('white_domain') != '') $params['where'] .= " and(a.is_white_domain = '" . ($this->get('white_domain')-1) . "') ";
        //类型搜索
        if ($this->get('promotion_type') != '' && $this->get('promotion_type') !== 0) {
            $params['where'] .= " and(a.promotion_type = '" . $this->get('promotion_type') . "') ";
        }
        if ($this->get('article_code') != '') $params['where'] .= " and(m.article_code like '%" . $this->get('article_code') . "%') ";
        //微信号小组搜索
        if ($this->get('wechat_group_name') != '') $params['where'] .= " and(w.wechat_group_name  like '%" . $this->get('wechat_group_name') . "%') ";

        //链接类型
        if ($this->get('line_type') != '')  $params['where'] .= " and(a.line_type = '" . ($this->get('line_type')-1) . "') ";

        //查看人员权限
        $result = $this->data_authority(1);
        if ($result != 0) {
            $params['where'] .= " and(f.sno in ($result)) ";
        }
        $params['where'] .= " and (o.is_main_page=1 or a.line_type=1)";
        $params['order'] = "  order by a.id desc    ";
        $params['pagesize'] = 1 == $is_export ? 10000 : Yii::app()->params['management']['pagesize'];
        $params['join'] = "
		left join finance_pay as f on f.id=a.finance_pay_id
		left join channel as c on c.id=f.channel_id
		left join partner as p on p.id=f.partner_id
		left join online_material_manage as o on o.id=a.article_id
		left join material_article_template as m on m.id=o.origin_template_id
		left join wechat_group as w on w.id=f.weixin_group_id
		left join cservice as b on b.csno=f.sno
	    left join business_types as g on g.bid=w.business_type
		";
        $params['pagebar'] = 1;
        $params['select'] = "a.finance_pay_id,a.id,a.url_rule,a.goto_domain_id,a.white_domain_id,a.promotion_type,a.status,a.is_white_domain,a.line_type,m.article_code,c.channel_name,c.channel_code,p.name as partner_name,b.csname_true,w.wechat_group_name,w.business_type,f.sno as tg_uid,f.online_date,g.bname,f.partner_id,f.channel_id,o.article_title,o.order_id,o.article_type,f.sno,f.charging_type,f.unit_price,f.weixin_group_id,g.bname as type_name";
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(Promotion::model()->tableName())->listdata($params);
        //查询推广域名
        $pro_ids = array_column($page['listdata']['list'],'id');
        $pro_domain = array();
        if ($pro_ids){
            $pro_domain = PromotionDomain::model()->getPromotionsDomains($pro_ids);
        }
        foreach ($page['listdata']['list'] as $key=>$value){
            $value['domain_list'] = $pro_domain[$value['id']];
            $page['listdata']['list'][$key] = $value;
        }
        $page['listdata']['url'] = urlencode('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"]);

        return $page;
    }

    //随机推广域名选择
    public function actionSelectDomains()
    {
        $page = $this->getDomains();
        $selected_id = $this->get('selected_id');
        $pro_id = trim($this->get('promotion_id'));
        //原推广域名
        if ($pro_id){
            $ids = PromotionDomain::model()->getPromotionDomains($pro_id);
            if ($ids) {
                $promotion_domain = Dtable::toArr(DomainList::model()->findAll(' id in ('.implode(',',$ids).') '));
                $page['promotion_domain'] = $promotion_domain;
            }
        }
        if ($selected_id) {
            $select_domain = Dtable::toArr(DomainList::model()->findAll(' id in ('.$selected_id.')'));
            $page['select_domain'] = $select_domain;
        }

        $this->render('selectDomains',array('page'=>$page));
    }

    //转换为json格式
    public function actionFreshDomains()
    {
        $page = $this->getDomains();
        echo json_encode($page) ;
    }

    public function getDomains()
    {
        $application_type = $this->get('application_type')?$this->get('application_type'):0;
        $promotion_type = $this->get('promotion_type')==3?3:0;
        $tg_uid = $this->get('tg_uid')? $this->get('tg_uid'):0;
        $cnzz_code_id = $this->get('cnzz_code_id');
        $domain = trim($this->get('domain'));
        //原推广域名
        $pro_id = trim($this->get('promotion_id'));
        //原推广域名
        $pro_domains = array();
        if ($pro_id){
            $pro_domains = PromotionDomain::model()->getPromotionDomains($pro_id);
        }
        $params['where'] = '';
        if ($cnzz_code_id) {
            $params['where']  .=' and cnzz_code_id='.$cnzz_code_id;
        }
        if ($domain) {
            $params['where']  .= " and domain like '%".$domain."%' ";
        }
        if (!$pro_domains) {
            $params['where'] .= " and status=0 and application_type=".$application_type." and domain_type=".$promotion_type." and uid=".$tg_uid;
        }else {
            $params['where'] .= " and (status=0 or id in (".implode(',',$pro_domains).")) and application_type=".$application_type." and domain_type=".$promotion_type." and uid=".$tg_uid;
        }
        $params['order'] = "  order by a.id desc    ";
        $params['pagesize'] = 40;
        $params['pagebar'] = 1;
        $params['select'] = "a.id,a.domain";
        $params['smart_order'] = 1;
        $page = Dtable::model(DomainList::model()->tableName())->listdata($params);
        return $page;
    }
}