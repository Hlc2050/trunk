<?php

/**
 * 素材管理控制器
 */
class MaterialController extends AdminController
{

    public function actionIndex()
    {
        $page = array();
        $groupId = $this->get('group_id');
        $groupId = !empty($groupId) ? $groupId : 0;
        switch ($groupId) {
            case 0://图文组别
                if ($this->get('type') == 1) {
                    $params['where'] = '';
                    $params['where'] .= " and is_main_page = 1" ;
                    if ($this->get('gid') != '') $params['where'] .= " and group_id=" . $this->get('gid');
                    if ($this->get('create_date') != '') {
                        $date = strtotime($this->get('create_date'));
                        $params['where'] .= " and(update_time between " . $date . " and " . ($date + 86399) . ") ";
                    }
                    if ($this->get('article_title') != '') $params['where'] .= " and(article_title like '%" . trim($this->get('article_title')) . "%') ";
                    if ($this->get('article_code') != '') $params['where'] .= " and(article_code like '%" . trim($this->get('article_code')) . "%') ";
                    if ($this->get('article_info') != '') $params['where'] .= " and(article_info like '%" . trim($this->get('article_info')) . "%') ";
                    if ($this->get('article_type') != '') $params['where'] .= " and(article_type=" . $this->get('article_type') . ") ";
                    $params['order'] = "  order by id desc    ";
                    $params['pagesize'] = 12;//每页显示12图文
                    $params['pagebar'] = 1;
                    $params['smart_order'] = 1;
                    $params['select'] = "id,article_title,cover_url,update_time,support_staff_id,article_code,article_type,article_info";
                    $page['listdata'] = Dtable::model(MaterialArticleTemplate::model()->tableName())->listdata($params);
                    foreach ($page['listdata']['list'] as $key => $val) {
                        if (!$val['cover_url']) continue;
                        $img = Yii::app()->basePath . '/..' . $val['cover_url'];
                        if (!file_exists(iconv('UTF-8', 'GB2312', $img)))
                            $page['listdata']['list'][$key]['cover_url'] = '';
                    }
                    $page['listdata']['gid'] = intval($this->get('gid'));
                    $page['listdata']['group_name'] = MaterialArticleGroup::model()->findByPk($this->get('gid'))->group_name;
                } else {
                    $params['where'] = '';
                    if ($this->get('artGroupName') != '') $params['where'] .= " and(group_name like '%" . $this->get('artGroupName') . "%') ";
                    if ($this->get('artGroupCode') != '') $params['where'] .= " and(group_code like '%" . $this->get('artGroupCode') . "%') ";
                    if ($this->get('cat_id') != 0) $params['where'] .= " and(a.cat_id =" . $this->get('cat_id') . ") ";
                    $params['pagesize'] = Yii::app()->params['management']['pagesize'];//每页显示12图文
                    $params['pagebar'] = 1;
                    $params['smart_order'] = 1;
                    $params['join'] = "left join material_article_template as b on b.group_id=a.id
                                   left join linkage as c on c.linkage_id=a.cat_id
                                   ";
                    $params['select'] = "a.*,c.linkage_name as cat_name,count(b.id) as article_count";
                    $params['group'] = " group by a.id ";
                    $page['listdata'] = Dtable::model(MaterialArticleGroup::model()->tableName())->listdata($params);
                    $noGroupArticles = MaterialArticleTemplate::model()->count('group_id=0');
                    if ($noGroupArticles > 0) {
                        $list = array();
                        $list['id'] = 0;
                        $list['group_name'] = "过渡组别";
                        $list['group_code'] = "null";
                        $list['cat_name'] = "无";
                        $list['article_count'] = $noGroupArticles;
                        $page['listdata']['list'][] = $list;
                    }

                }
                break;
            case 1://图片
                //判断组别，取出数据（图片地址，图片名字，图片id，图片组别）
                $params['where'] = '';
                if ($this->get('pic_name') != '') $params['where'] .= " and(name like '%" . $this->get('pic_name') . "%') ";
                if ($this->get('pic_group_id') != '') $params['where'] .= " and(group_id = " . $this->get('pic_group_id') . ") ";
                $params['order'] = "  order by id desc    ";
                $params['pagesize'] = 12;//每页显示12张图片
                $params['pagebar'] = 1;
                $params['smart_order'] = 1;
                $page['listdata'] = Dtable::model(MaterialPicRelation::model()->tableName())->listdata($params);
                break;
            case 2://视频
                $params['order'] = "  order by id desc    ";
                $params['pagesize'] = Yii::app()->params['management']['pagesize'];
                $params['pagebar'] = 1;
                $params['smart_order'] = 1;
                $page['listdata'] = Dtable::model(MaterialVideo::model()->tableName())->listdata($params);
                break;
            case 3://语音
                $params['join'] = "
                 left JOIN resource_list as r ON a.audio_id=r.resource_id
                 ";
                $params['order'] = "  order by a.id desc    ";
                $params['selected'] = "a.*,r.resource_url";
                $params['pagesize'] = Yii::app()->params['management']['pagesize'];
                $params['pagebar'] = 1;
                $params['smart_order'] = 1;
                $page['listdata'] = Dtable::model(MaterialAudio::model()->tableName())->listdata($params);
                break;
            case 4://问卷
                $params['where'] = '';
                $params['order'] = "  order by id desc    ";
                $params['pagesize'] = 12;//每页显示12张图片
                $params['pagebar'] = 1;
                $params['smart_order'] = 1;
                $page['listdata'] = Dtable::model(Questionnaire::model()->tableName())->listdata($params);
                break;
            case 5://评论
                $params['where'] = '';
                $params['order'] = "  order by id desc    ";
                $params['pagesize'] = 12;//每页显示12张图片
                $params['pagebar'] = 1;
                $params['smart_order'] = 1;
                $page['listdata'] = Dtable::model(MaterialReview::model()->tableName())->listdata($params);
                break;
            default:
                break;
        }
        $page['listdata']['params_groups'] = vars::$fields['material_group'];
        $page['listdata']['url'] = urlencode('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"]);

        $this->render('index', array('page' => $page));
    }
    /************************图文处理begin*******************************/

    /**
     * 新增图文
     * author: yjh
     */
    public function actionAddArticle()
    {

        $page = array();
        //显示表单
        if (!$_POST) {
            $page['info']['article_type'] = $this->get('article_type');
            $this->render('updateArticle', array('page' => $page));
            exit;
        }
        if ($this->get('article_type') == 0) {
            $dbresult = $this->addNormArticle();
        } else if ($this->get('article_type') == 1) {
            $dbresult = $this->addAudioArticle();
        } else if ($this->get('article_type') == 2) {
            $dbresult = $this->addForumArticle();
        } else if ($this->get('article_type') == 3) {
            $dbresult = $this->addWeChatArticle();
        } else $dbresult = false;


        $msgarr = array('state' => 1, 'url' => $this->createUrl('material/index') . '?group_id=0&p=' . $_GET['p'] . '');
        $logs = "添加了新的图文素材：" . $this->post('articleTitle');
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
     * 添加标准图文
     * @return bool
     * author: yjh
     */
    private function addNormArticle()
    {
        $info = new MaterialArticleTemplate();
        $info->article_title = $this->post('articleTitle');
        $content = $this->post('info_body');
        if ($content == '') $this->msg(array('state' => 0, 'msgwords' => '文章内容为空'));
        $content = str_replace("class=\"lazy\" src", "class=\"lazy\" data-original", $content);
        $info->content = $content;
        $info->tag = $this->post('tag');
        $info->is_fill = $this->post('is_fill');
        $info->is_hide_title = $this->post('is_hide_title');
        $info->cat_id = $this->post('cat_id');
        $info->support_staff_id = $this->post('support_staff_id');
        $info->article_type = 0;
        $info->xingxiang = $this->post('xingxiang');
        $info->cover_url = $this->post('cover_url');
        $info->group_id = $this->post('group_id');
        $info->psq_id = $this->post('psq_id');
        $info->article_info = $this->post('article_info');
        $info->top_img = $this->post('top_img');
        $info->level_tag = $this->post('level_tag') ? 1 : 0;
        $info->is_vote = $this->post('is_vote');
        $info->vote_id = $this->post('vote_id');
        if ($this->post('review_title') == '') $info->review_id = 0;
        else $info->review_id = $this->post('review_id');
        $info->order_id = $this->post('order_id');
        $info->is_order = $this->post('is_order');
        $info->descriptive_statement = $this->get('descriptive_statement');
        $info->release_date = helper::getReleaseDate($this->get('release_date'),$this->get('release_date1'));

        if ($info->is_vote == 0) $info->vote_id = 0;
        if ($info->is_vote == 1) {
            if ($info->vote_id == '')
                $this->msg(array('state' => 0, 'msgwords' => '未选择投票页！'));
        }
        if ($info->is_order == 0) {
            $info->order_id = 0;
        }
        if ($info->is_order == 1) {
            if ($info->order_id == 0) {
                $this->msg(array('state' => 0, 'msgwords' => '未选择下单模板！'));
            }
            $payment = vars::$fields['payment'];
            $tempPayment = array();
            foreach ($payment as $k => $value) {
                $key = 'payment_' . $value['value'];
                $payments = $this->post($key) ? 1 : 0;
                $tempPayment[$k] = $payments;
            }
            $str = helper::bindec_digit($tempPayment, count($payment));
            if (0 == $str) $this->msg(array('state' => 0, 'msgwords' => '未选择支付方式！'));
            $info->payment = $str;
        }
        $info->pop_time = $this->post('pop_time');//弹出聊天框时间
        $info->char_intro = $this->post('char_intro');//人物介绍
        $info->chat_content = $this->post('chat_content');//聊天内容

        if ($info->pop_time === '') $info->pop_time = -1;
        if ($info->pop_time >= 0) {
            if ($info->char_intro == '') $this->msg(array('state' => 0, 'msgwords' => '未填写人物介绍'));
            if ($info->chat_content == '') $this->msg(array('state' => 0, 'msgwords' => '未填写聊天内容'));
        }

        //底部样式
        $info->bottom_type = $this->post('bottom_type');
        if ($info->group_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择组别！'));
        if ($info->bottom_type == 0) {//标准样式
            $info->suspension_text = $this->post('suspension_text');
            if ($info->suspension_text == '') $this->msg(array('state' => 0, 'msgwords' => '未填写悬浮描述'));
        } elseif ($info->bottom_type == 1) {//头像样式
            $info->avater_img = $this->post('avater_img');
            $info->suspension_text = $this->post('suspension_text');
            $info->addfans_type = $this->post('addfans_type');
            $info->addfans_text = $this->post('addfans_text');
            if ($info->avater_img == '') $this->msg(array('state' => 0, 'msgwords' => '头像未添加'));
            if ($info->suspension_text == '') $this->msg(array('state' => 0, 'msgwords' => '固定悬浮描述未填写'));
        }elseif($info->bottom_type == 3){//标准样式2
            $info->suspension_text = $this->post('suspension_text');
            if ($info->suspension_text == '') $this->msg(array('state' => 0, 'msgwords' => '未填写悬浮描述'));
        }elseif($info->bottom_type == 4){//标准样式2
            $info->suspension_text = $this->post('suspension_text');
            if ($info->suspension_text == '') $this->msg(array('state' => 0, 'msgwords' => '未填写悬浮描述'));
        }

        if ($info->article_title == '') $this->msg(array('state' => 0, 'msgwords' => '文章标题未填写'));
        if ($info->support_staff_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择支持人员'));
        if ($info->content == '') $this->msg(array('state' => 0, 'msgwords' => '文章内容为空'));

        if ($info->cat_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择商品类型'));
        $info->article_code = MaterialArticleGroup::model()->createArticleCode($info->group_id);

        $info->update_time = time();
        $info->create_time = time();
        $dbresult = $info->save();
        return $dbresult;
    }

    /**
     * 添加语音问卷图文
     * @return bool
     * author: yjh
     */
    private function addAudioArticle()
    {
        $info = new MaterialArticleTemplate();

        $info->article_type = 1;
        $info->tag = $this->post('tag');
        $info->cat_id = $this->post('cat_id');
        $info->psq_id = $this->post('psq_id');
        $content = $this->post('info_body');
        if ($content == '') $this->msg(array('state' => 0, 'msgwords' => '文章内容为空'));
        $content = str_replace("class=\"lazy\" src", "class=\"lazy\" data-original", $content);
        $info->content = $content;
        $info->top_img = $this->post('top_img');
        $info->top_text = $this->post('top_text');
        $info->cover_url = $this->post('cover_url');
        $info->idintity = $this->post('idintity');
        $info->top_color = $this->post('top_color');
        $info->avater_img = $this->post('avater_img');
        $info->xingxiang = $this->post('xingxiang');
        $info->first_audio = $this->post('first_audio');
        $info->third_audio = $this->post('third_audio');
        $info->level_tag = $this->post('level_tag') ? 1 : 0;
        $info->avater_tag = $this->post('avater_tag') ? 1 : 0;
        $info->second_audio = $this->post('second_audio');
        $info->addfans_type = $this->post('addfans_type');
        $info->addfans_text = $this->post('addfans_text');
        $info->article_title = $this->post('articleTitle');
        $info->suspension_text = $this->post('suspension_text');
        $info->support_staff_id = $this->post('support_staff_id');
        if ($this->post('review_title') == '') $info->review_id = 0;
        else $info->review_id = $this->post('review_id');
        $info->bottom_type = $this->post('bottom_type');
        $info->group_id = $this->post('group_id');
        $info->article_info = $this->post('article_info');
        $info->is_vote = $this->post('is_vote');
        $info->vote_id = $this->post('vote_id');
        $info->descriptive_statement = $this->get('descriptive_statement');

        if ($info->is_vote == 0) $info->vote_id = 0;
        if ($info->is_vote == 1) {
            if ($info->vote_id == '')
                $this->msg(array('state' => 0, 'msgwords' => '未选择投票页！'));
        }
        if ($info->group_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择组别！'));

        if ($info->bottom_type == 1) {
            if ($info->suspension_text == '') $this->msg(array('state' => 0, 'msgwords' => '固定悬浮描述未填写'));
        }
        $info->pop_time = $this->post('pop_time');//弹出聊天框时间
        $info->char_intro = $this->post('char_intro');//人物介绍
        $info->chat_content = $this->post('chat_content');//聊天内容

        if ($info->pop_time === '') $info->pop_time = -1;
        if ($info->pop_time >= 0) {
            if ($info->char_intro == '') $this->msg(array('state' => 0, 'msgwords' => '未填写人物介绍'));
            if ($info->chat_content == '') $this->msg(array('state' => 0, 'msgwords' => '未填写聊天内容'));
        }
        $info->article_code = MaterialArticleGroup::model()->createArticleCode($info->group_id);

        if ($info->article_title == '') $this->msg(array('state' => 0, 'msgwords' => '文章标题未填写'));
        if ($info->support_staff_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择支持人员'));
        if ($info->content == '') $this->msg(array('state' => 0, 'msgwords' => '文章内容为空'));
        if ($info->top_img == '') $this->msg(array('state' => 0, 'msgwords' => '顶部背景图未添加'));
        if ($info->avater_img == '') $this->msg(array('state' => 0, 'msgwords' => '头像未添加'));
        if ($info->idintity == '') $this->msg(array('state' => 0, 'msgwords' => '身份未填写'));
        if ($info->top_text == '') $this->msg(array('state' => 0, 'msgwords' => '顶部按钮描述未填写'));
        if ($info->cat_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择商品类型'));

        $info->update_time = time();
        $info->create_time = time();
        $dbresult = $info->save();
        return $dbresult;
    }

    /**
     * 添加论坛问答图文
     * @return bool
     * author: yjh
     */
    private function addForumArticle()
    {
        $info = new MaterialArticleTemplate();

        $info->article_type = 2;
        $info->tag = $this->post('tag');
        $info->cat_id = $this->post('cat_id');
        $content = $this->post('info_body');
        if ($content == '') $this->msg(array('state' => 0, 'msgwords' => '文章内容为空'));
        $content = str_replace("class=\"lazy\" src", "class=\"lazy\" data-original", $content);
        $info->content = $content;
        $info->top_img = $this->post('top_img');
        $info->xingxiang = $this->post('xingxiang');
        $info->top_text = $this->post('top_text');//文章导航
        $info->cover_url = $this->post('cover_url');
        $info->idintity = $this->post('idintity');//时间
        $info->article_title = $this->post('articleTitle');
        $info->support_staff_id = $this->post('support_staff_id');
        $info->review_id = $this->post('review_id');
        $info->bottom_type = $this->post('bottom_type');
        $info->suspension_text = $this->post('suspension_text');
        $info->addfans_type = $this->post('addfans_type');
        $info->addfans_text = $this->post('addfans_text');
        $info->avater_img = $this->post('avater_img');
        $info->group_id = $this->post('group_id');
        $info->article_info = $this->post('article_info');
        $info->level_tag = $this->post('level_tag') ? 1 : 0;
        $info->pop_time = $this->post('pop_time');//弹出聊天框时间
        $info->char_intro = $this->post('char_intro');//人物介绍
        $info->chat_content = $this->post('chat_content');//聊天内容
        $info->descriptive_statement =  $this->post('descriptive_statement');

        if ($info->pop_time === '') $info->pop_time = -1;
        if ($info->pop_time >= 0) {
            if ($info->char_intro == '') $this->msg(array('state' => 0, 'msgwords' => '未填写人物介绍'));
            if ($info->chat_content == '') $this->msg(array('state' => 0, 'msgwords' => '未填写聊天内容'));
        }
        if ($info->group_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择组别！'));
        if ($info->article_title == '') $this->msg(array('state' => 0, 'msgwords' => '文章标题未填写'));
        if ($info->support_staff_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择支持人员'));
        if ($info->content == '') $this->msg(array('state' => 0, 'msgwords' => '文章内容为空'));
        //if ($info->top_img == '') $this->msg(array('state' => 0, 'msgwords' => '顶部图片未添加'));
        if ($info->idintity == '') $this->msg(array('state' => 0, 'msgwords' => '时间未填写'));
        //if ($info->top_text == '') $this->msg(array('state' => 0, 'msgwords' => '文章导航未填写'));
        if ($info->cat_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择商品类型'));
        $info->article_code = MaterialArticleGroup::model()->createArticleCode($info->group_id);

        $info->update_time = time();
        $info->create_time = time();
        $dbresult = $info->save();
        return $dbresult;
    }

    /**
     * 添加返回公众号图文
     * @return bool
     * author: hlc
     */
    private function addWeChatArticle()
    {
        //主页面
        if ($this->get('main_title') == '') $this->msg(array('state' => 0, 'msgwords' => '主页面图文标题未填写'));
        $group_id = $this->get('group_id');
        if($group_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择所在组别'));
        $article_code = MaterialArticleGroup::model()->createArticleCode($group_id);
        if ($this->get('support_staff_id') == '') $this->msg(array('state' => 0, 'msgwords' => '未选择支持人员'));
        if ($this->get('info_body1') == '') $this->msg(array('state' => 0, 'msgwords' => '主页面正文内容未填写'));
        if ($this->get('cat_id') == '') $this->msg(array('state' => 0, 'msgwords' => '未选择商品类型'));
        $thumb_up = $this->get('thumb_up') ? $this->get('thumb_up') : "";
        $read_num = $this->get('read_num') ? $this->get('read_num') : "";
        $author = $this->get('author') ? $this->get('author') : "";
        if (is_int($thumb_up) && $thumb_up) $this->msg(array('state' => 0, 'msgwords' => '点赞数不是数字'));
        if (is_int($read_num && $author)) $this->msg(array('state' => 0, 'msgwords' => '阅读数不是数字'));

        //返回页面
        if ($this->get('rtn_Title') == '') $this->msg(array('state' => 0, 'msgwords' => '返页面图文标题未填写'));
        if ($this->get('info_body2') == '') $this->msg(array('state' => 0, 'msgwords' => '返回页面正文内容未填写'));

        //作者页面
        if ($this->get('au_title') == '') $this->msg(array('state' => 0, 'msgwords' => '作者页面图文标题未填写'));
        if ($this->get('info_body4') == '') $this->msg(array('state' => 0, 'msgwords' => '作者页面正文内容未填写'));

        //阅读页面
        if ($this->get('read_title') == '') $this->msg(array('state' => 0, 'msgwords' => '阅读页面图文标题未填写'));
        if ($this->get('info_body3') == '') $this->msg(array('state' => 0, 'msgwords' => '阅读页面正文内容未填写'));

        //发布日期
        $release_date = helper::getReleaseDate($this->get('release_date'),$this->get('release_date1'));

        //主页面
        $info = new MaterialArticleTemplate();
        $info->article_title = $this->get('main_title');
        $info->group_id = $group_id;
        $info->support_staff_id = $this->get('support_staff_id');
        $info->cat_id = $this->get('cat_id');
        $content = $this->get('info_body1');
        $content = stripslashes(str_replace("class=\"lazy\" src", "class=\"lazy\" data-original", $content));
        $info->content = $content;
        $info->cover_url = $this->get('cover_url1');
        $info->top_img = $this->get('cover_url1');
        $info->tag = $this->get('tag_1');
        $info->xingxiang = $this->get('xingxiang');
        $info->article_info = $this->get('article_info');
        $info->suspension_text = $author;
        $info->first_audio = $thumb_up;
        $info->second_audio = $read_num;
        $info->is_hide_title = $this->get('is_rtn');
        $info->is_fill = $this->get('is_au_article');
        $info->article_code = $article_code;
        $info->article_type = 3;
        $info->is_main_page = 1;
        $info->descriptive_statement = $this->get('descriptive_statement');
        $info->release_date = $release_date;
        $info->update_time = time();
        $info->create_time = time();
        $info->save();
        //返回页面
        $info = new MaterialArticleTemplate();
        $info->article_title = $this->get('rtn_Title');
        $content = $this->get('info_body2');
        $content = stripslashes(str_replace("class=\"lazy\" src", "class=\"lazy\" data-original", $content));
        $info->content = $content;
        $info->suspension_text = $author;
        $info->cover_url = $this->get('cover_url2');
        $info->tag = $this->get('tag_2');
        $info->article_code = $article_code;
        $info->link = trim($this->get('link1'));
        $info->first_audio = $thumb_up;
        $info->second_audio = $read_num;
        $info->xingxiang = $this->get('xingxiang');
        $info->article_type = 3;
        $info->is_main_page = 21;
        $info->release_date =$release_date;
        $info->update_time = time();
        $info->create_time = time();
        $info->save();
        //作者页面
        $info = new MaterialArticleTemplate();
        $info->article_title = $this->get('au_title');
        $content = $this->get('info_body3');
        $content = stripslashes(str_replace("class=\"lazy\" src", "class=\"lazy\" data-original", $content));
        $info->content = $content;
        $info->suspension_text = $author;
        $info->cover_url = $this->get('cover_url3');
        $info->tag = $this->get('tag_3');
        $info->article_code = $article_code;
        $info->first_audio = $thumb_up;
        $info->second_audio = $read_num;
        $info->link = trim($this->get('link2'));
        $info->xingxiang = $this->get('xingxiang');
        $info->article_type = 3;
        $info->is_main_page = 22;
        $info->release_date = $release_date;
        $info->update_time = time();
        $info->create_time = time();
        $info->save();
        //阅读页面
        $info = new MaterialArticleTemplate();
        $info->article_title = $this->get('read_title');
        $content = $this->get('info_body4');
        $content = stripslashes(str_replace("class=\"lazy\" src", "class=\"lazy\" data-original", $content));
        $info->content = $content;
        $info->suspension_text = $author;
        $info->cover_url = $this->get('cover_url4');
        $info->tag = $this->get('tag_4');
        $info->article_code = $article_code;
        $info->first_audio = $thumb_up;
        $info->second_audio = $read_num;
        $info->link = trim($this->get('link3'));
        $info->xingxiang = $this->get('xingxiang');
        $info->release_date = $release_date;
        $info->article_type = 3;
        $info->is_main_page = 23;
        $info->update_time = time();
        $info->create_time = time();
        $dbresult = $info->save();

        return $dbresult;
    }

    public function actionGetPSQByCatId()
    {
        if (isset($_POST['cat_id'])) {
            $data = Questionnaire::model()->getPSQByCatId($_POST['cat_id']);
            if (empty($data)) echo CHtml::tag('option', array('value' => ''), CHtml::encode('没有问卷'), true);
            echo CHtml::tag('option', array('value' => ''), CHtml::encode('请选择'), true);
            foreach ($data as $key => $val) {
                echo CHtml::tag('option', array('value' => $val['id']), CHtml::encode($val['vote_title']), true);
            }
        }
    }

    /**
     * 修改图文
     * author: yjh
     */
    public function actionEditArticle()
    {

        $page = array();
        $id = $this->get('id');
        $weChat = $this->get('weChat_id');


        //判断图文类型是否为微信图文
        if ($weChat != null) {
            //主页面
            if ($this->get('main_title') == '') $this->msg(array('state' => 0, 'msgwords' => '主页面图文标题未填写'));
            $group_id = $this->get('group_id');
            if($group_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择所在组别'));
//            $article_code = MaterialArticleGroup::model()->createArticleCode($group_id);
            if ($this->get('support_staff_id') == '') $this->msg(array('state' => 0, 'msgwords' => '未选择支持人员'));
            if ($this->get('info_body1') == '') $this->msg(array('state' => 0, 'msgwords' => '主页面正文内容未填写'));
            if ($this->get('cat_id') == '') $this->msg(array('state' => 0, 'msgwords' => '未选择商品类型'));
            $thumb_up = $this->get('thumb_up') ? $this->get('thumb_up') : "";
            $read_num = $this->get('read_num') ? $this->get('read_num') : "";
            $author = $this->get('author') ? $this->get('author') : "";
            if (is_int($thumb_up) && $thumb_up) $this->msg(array('state' => 0, 'msgwords' => '点赞数不是数字'));
            if (is_int($read_num && $author)) $this->msg(array('state' => 0, 'msgwords' => '阅读数不是数字'));

            //返回页面
            if ($this->get('rtn_Title') == '') $this->msg(array('state' => 0, 'msgwords' => '返页面图文标题未填写'));
            if ($this->get('info_body2') == '') $this->msg(array('state' => 0, 'msgwords' => '返回页面正文内容未填写'));

            //作者页面
            if ($this->get('au_title') == '') $this->msg(array('state' => 0, 'msgwords' => '作者页面图文标题未填写'));
            if ($this->get('info_body4') == '') $this->msg(array('state' => 0, 'msgwords' => '作者页面正文内容未填写'));

            //阅读页面
            if ($this->get('read_title') == '') $this->msg(array('state' => 0, 'msgwords' => '阅读页面图文标题未填写'));
            if ($this->get('info_body3') == '') $this->msg(array('state' => 0, 'msgwords' => '阅读页面正文内容未填写'));

            //发布日期
            $release_date = helper::getReleaseDate($this->get('release_date'),$this->get('release_date1'));

            //主页面
            $weChat_id = explode(',', $weChat);
            $info = MaterialArticleTemplate::model()->findByPk($weChat_id[0]);
            $info->article_title = $this->get('main_title');
            $info->group_id = $this->get('group_id');
            $info->support_staff_id = $this->get('support_staff_id');
            $info->cat_id = $this->get('cat_id');
            $content = $this->get('info_body1');
            $content = stripslashes(str_replace("class=\"lazy\" src", "class=\"lazy\" data-original", $content));
            $info->content = $content;
            $info->cover_url = $this->get('cover_url1');
            $info->top_img = $this->get('cover_url1');
            $info->tag = $this->get('tag_1');
            $info->xingxiang = $this->get('xingxiang');
            $info->article_info = $this->get('article_info');
            $info->suspension_text = $author;
            $info->first_audio = $thumb_up;
            $info->second_audio = $read_num;
            $info->is_hide_title = $this->get('is_rtn');
            $info->is_fill = $this->get('is_au_article');
            $info->descriptive_statement = $this->get('descriptive_statement');
            $info->release_date = $release_date;
            $info->update_time = time();
            $info->save();

            //返回页面
            $info = MaterialArticleTemplate::model()->findByPk($weChat_id[1]);
            $info->article_title = $this->get('rtn_Title');
            $content = $this->get('info_body2');
            $content = stripslashes(str_replace("class=\"lazy\" src", "class=\"lazy\" data-original", $content));
            $info->content = $content;
            $info->suspension_text = $author;
            $info->cover_url = $this->get('cover_url2');
            $info->tag = $this->get('tag_2');
            $info->xingxiang = $this->get('xingxiang');
            $info->first_audio = $thumb_up;
            $info->second_audio = $read_num;
            $info->link = trim($this->get('link1'));
            $info->release_date = $release_date;
            $info->update_time = time();
            $info->save();

            //作者页面
            $info = MaterialArticleTemplate::model()->findByPk($weChat_id[2]);
            $info->article_title = $this->get('au_title');
            $content = $this->get('info_body3');
            $content = stripslashes(str_replace("class=\"lazy\" src", "class=\"lazy\" data-original", $content));
            $info->content = $content;
            $info->suspension_text = $author;
            $info->cover_url = $this->get('cover_url3');
            $info->xingxiang = $this->get('xingxiang');
            $info->tag = $this->get('tag_3');
            $info->first_audio = $thumb_up;
            $info->second_audio = $read_num;
            $info->link = trim($this->get('link2'));
            $info->release_date = $release_date;
            $info->update_time = time();
            $info->save();

            //阅读页面
            $info = MaterialArticleTemplate::model()->findByPk($weChat_id[3]);
            $info->article_title = $this->get('read_title');
            $content = $this->get('info_body4');
            $content = stripslashes(str_replace("class=\"lazy\" src", "class=\"lazy\" data-original", $content));
            $info->content = $content;
            $info->suspension_text = $author;
            $info->cover_url = $this->get('cover_url4');
            $info->xingxiang = $this->get('xingxiang');
            $info->tag = $this->get('tag_4');
            $info->link = trim($this->get('link3'));
            $info->first_audio = $thumb_up;
            $info->second_audio = $read_num;
            $info->release_date = $release_date;
            $info->update_time = time();
            $info->save();

            $url = $this->get('backurl');
            $this->msg(array('state' => 1, 'msgwords' => '修改成功', 'url' => $url));
        } else {
            $info = MaterialArticleTemplate::model()->findByPk($id);

            if (!$info) {
                $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
            }
            if (!$_POST) {
                $page['info'] = $this->toArr($info);
                $content = str_replace("class=\"lazy\" data-original", "class=\"lazy\" src", $page['info']['content']);
                $page['info']['content'] = $content;
                $this->render('updateArticle', array('page' => $page));
                exit;
            }
            $content = $this->post('info_body');
            if ($content == '') $this->msg(array('state' => 0, 'msgwords' => '文章内容为空'));
            $content = str_replace("class=\"lazy\" src", "class=\"lazy\" data-original", $content);
            $last_group_id = $info->group_id;
            $info->tag = $this->post('tag');
            $info->cat_id = $this->post('cat_id');
            $info->psq_id = $this->post('psq_id');
            $info->content = $content;
            $info->top_img = $this->post('top_img');
            $info->top_text = $this->post('top_text');
            $info->idintity = $this->post('idintity');
            $info->top_color = $this->post('top_color');
            $info->avater_img = $this->post('avater_img');
            $info->first_audio = $this->post('first_audio');
            $info->xingxiang = $this->post('xingxiang');
            $info->third_audio = $this->post('third_audio');
            $info->level_tag = $this->post('level_tag') ? 1 : 0;
            $info->avater_tag = $this->post('avater_tag') ? 1 : 0;
            $info->second_audio = $this->post('second_audio');
            $info->addfans_type = $this->post('addfans_type');
            $info->addfans_text = $this->post('addfans_text');
            $info->article_title = $this->post('articleTitle');
            $info->suspension_text = $this->post('suspension_text');
            $info->support_staff_id = $this->post('support_staff_id');
            $info->review_id = $this->post('review_id');
            $info->bottom_type = $this->post('bottom_type');
            $info->group_id = $this->post('group_id');
            $info->article_info = $this->post('article_info');
            $info->is_vote = $this->post('is_vote');
            $info->vote_id = $this->post('vote_id');
            $info->pop_time = $this->post('pop_time');//弹出聊天框时间
            $info->char_intro = $this->post('char_intro');//人物介绍
            $info->chat_content = $this->post('chat_content');//聊天内容
            $info->descriptive_statement = $this->get('descriptive_statement');
            $info->release_date = helper::getReleaseDate($this->get('release_date'),$this->get('release_date1'));

            if ($info->is_vote == 0) $info->vote_id = 0;
            if ($info->is_vote == 1) {
                if ($info->vote_id == '')
                    $this->msg(array('state' => 0, 'msgwords' => '未选择投票页！'));
            }
            if ($info->pop_time === '') $info->pop_time = -1;
            if ($info->pop_time >= 0) {
                if ($info->char_intro == '') $this->msg(array('state' => 0, 'msgwords' => '未填写人物介绍'));
                if ($info->chat_content == '') $this->msg(array('state' => 0, 'msgwords' => '未填写聊天内容'));
            }
            if ($info->group_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择组别！'));
            if ($info->article_type == 0) {
                $info->is_fill = $this->post('is_fill');
                $info->is_hide_title = $this->post('is_hide_title');
                $info->order_id = $this->post('order_id');
                if ($this->post('review_title') == '') $info->review_id = 0;
                else $info->review_id = $this->post('review_id');
                $info->cover_url = $this->post('cover_url');
                if ($info->article_title == '') $this->msg(array('state' => 0, 'msgwords' => '文章标题未填写'));
                if ($info->support_staff_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择支持人员'));
                if ($info->tag == '') $this->msg(array('state' => 0, 'msgwords' => '未填写浏览器Title'));
                //if ($info->suspension_text == '') $this->msg(array('state' => 0, 'msgwords' => '未填写悬浮描述'));
                if ($info->cat_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择商品类型'));
                if ($info->bottom_type == 0) {//标准样式
                    if ($info->suspension_text == '') $this->msg(array('state' => 0, 'msgwords' => '未填写悬浮描述'));
                } elseif ($info->bottom_type == 1) {//头像样式
                    if ($info->avater_img == '') $this->msg(array('state' => 0, 'msgwords' => '头像未添加'));
                    if ($info->suspension_text == '') $this->msg(array('state' => 0, 'msgwords' => '固定悬浮描述未填写'));
                }elseif($info->bottom_type == 3){//标准样式2
                    if ($info->suspension_text == '') $this->msg(array('state' => 0, 'msgwords' => '固定悬浮描述未填写'));
                }
                $info->is_order = $this->post('is_order');
                $info->order_id = $this->post('order_id');
                if ($info->order_id == 0) {
                    $info->order_id = 0;
                }
                if ($info->is_order == 1) {
                    if ($info->order_id == 0) {
                        $this->msg(array('state' => 0, 'msgwords' => '未选择下单模板！'));
                    }
                    $payment = vars::$fields['payment'];
                    $tempPayment = array();
                    foreach ($payment as $k => $value) {
                        $key = 'payment_' . $value['value'];
                        $payments = $this->post($key) ? 1 : 0;
                        $tempPayment[$k] = $payments;
                    }
                    $str = helper::bindec_digit($tempPayment, count($payment));
                    if (0 == $str) $this->msg(array('state' => 0, 'msgwords' => '未选择支付方式！'));
                    $info->payment = $str;
                }
            } elseif ($info->article_type == 1) {
                if ($this->post('review_title') == '') $info->review_id = 0;
                else $info->review_id = $this->post('review_id');
                $info->cover_url = $this->post('cover_url');
                if ($info->article_title == '') $this->msg(array('state' => 0, 'msgwords' => '文章标题未填写'));
                if ($info->support_staff_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择支持人员'));
                if ($info->content == '') $this->msg(array('state' => 0, 'msgwords' => '文章内容为空'));
                if ($info->top_img == '') $this->msg(array('state' => 0, 'msgwords' => '顶部背景图未添加'));
                if ($info->avater_img == '') $this->msg(array('state' => 0, 'msgwords' => '头像未添加'));
                if ($info->idintity == '') $this->msg(array('state' => 0, 'msgwords' => '身份未填写'));
                if ($info->top_text == '') $this->msg(array('state' => 0, 'msgwords' => '顶部按钮描述未填写'));
                if ($info->suspension_text == '') $this->msg(array('state' => 0, 'msgwords' => '固定悬浮描述未填写'));
                if ($info->cat_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择商品类型'));
            } elseif ($info->article_type == 2) {
                $info->cover_url = $this->post('cover_url');
                if ($info->article_title == '') $this->msg(array('state' => 0, 'msgwords' => '文章标题未填写'));
                if ($info->support_staff_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择支持人员'));
                if ($info->content == '') $this->msg(array('state' => 0, 'msgwords' => '文章内容为空'));
                //if ($info->top_img == '') $this->msg(array('state' => 0, 'msgwords' => '顶部图片未添加'));
                if ($info->idintity == '') $this->msg(array('state' => 0, 'msgwords' => '时间未填写'));
                //if ($info->top_text == '') $this->msg(array('state' => 0, 'msgwords' => '文章导航未填写'));
                if ($info->cat_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择商品类型'));
            }

            if ($last_group_id != $info->group_id) {
                //创造图文编码
                $info->article_code = MaterialArticleGroup::model()->createArticleCode($info->group_id);
            }

            $info->update_time = time();
            $dbresult = $info->save();
            $msgarr = array('state' => 1, 'url' => $this->get('backurl')); //保存的话，跳转到之前的列表
            $logs = "修改了图文素材：" . $info->article_title;
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

    }

    /**
     * 删除图文 真删
     * author: yjh
     */
    public function actionDeleteArticle()
    {
        $id = $this->get('id');
        if ($id == '') $this->msg(array('state' => 0, 'msgwords' => '未传入数据'));
        $info = MaterialArticleTemplate::model()->findByPk($id);
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }
        if($info['article_type'] == 3){
            $sql = "select * FROM material_article_template WHERE article_code= '" . $info['article_code'] . "'order by id";
            $data = Yii::app()->db->createCommand($sql)->queryAll();
            for($i=0;$i<4;$i++){
                $m = MaterialArticleTemplate::model()->findByPk($data[$i]['id']);
                $m->delete();
            }
        }else{
            $info->delete();
        }

        $this->msg(array('state' => 1, 'msgwords' => '删除图文成功！', 'url' => $this->get('url')));
    }

    public function actionShowPreview()
    {
        $page = array();
        //显示表单
        if (!$this->get('id')) $this->msg(array('state' => 1, 'msgwords' => '没有传入数据！'));
        $info = MaterialArticleTemplate::model()->findByPk($this->get('id'));
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }

        $page['info'] = Dtable::toArr($info);
        if ($page['info']['article_type'] == 0)
            $this->render('showNormPreview', array('page' => $page));
        else
            $this->render('showAudioPreview', array('page' => $page));

    }

    /**
     * 往文章中插入素材库图片列表
     * author: yjh
     */
    public function actionAddMaterialPics()
    {
        $page = array();
        $num = $this->get('num') ? $this->get('num') : null;
        $params['where'] = '';
        if ($this->get('pic_name') != '') $params['where'] .= " and(name like '%" . $this->get('pic_name') . "%') ";
        if ($this->get('pic_group_id') != '') $params['where'] .= " and(group_id = " . $this->get('pic_group_id') . ") ";
        $params['order'] = "  order by id asc    ";
        $params['pagesize'] = 10;//每页显示10张图片
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(MaterialPicRelation::model()->tableName())->listdata($params);
        if ($this->get('id')) $page['listdata']['sign'] = $this->get('id');
        $this->render('addMaterialPic', array('page' => $page, 'num' => $num));
    }

    /**
     * 往文章中插入素材库图片列表
     * author: yjh
     */
    public function actionAddMaterialVideos()
    {
        $page = array();
        $num = $this->get('num') ? $this->get('num') : null;
        $params['where'] = '';
        $params['order'] = "  order by id desc    ";
        $params['pagesize'] = 10;//每页显示10条视频
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(MaterialVideo::model()->tableName())->listdata($params);
        $this->render('addMaterialVideo', array('page' => $page, 'num' => $num));
    }

    /**
     * 显示大图
     * author: yjh
     */
    public function actionShowPic()
    {
        $imgId = $this->get('img_id');
        if (!$imgId) $this->msg(array('state' => 0, 'msgwords' => "未传入参数"));
        $imgUrl = Resource::model()->findByPk($imgId)->resource_url;
        $img = Yii::app()->basePath . '/..' . $imgUrl;
        if (file_exists(iconv('UTF-8', 'GB2312', $img))) $imgURL = $imgUrl;
        else $this->msg(array('state' => 0, 'msgwords' => "图片损坏"));

        $page = array();
        $page['name'] = MaterialPicRelation::model()->find('img_id=' . $imgId)->name;
        $page['imgURL'] = $imgURL;
        $this->render('showPic', array('page' => $page));

    }
    /************************图文处理end*********************************/
    /************************图片处理begin*******************************/
    /**
     * 批量上传图片
     * author: yjh
     */
    public function actionUploadImgs()
    {
        $this->render('uploadImgs');
    }

    /**
     * 修改选中图片分组
     * author: yjh
     */
    public function actionChangePicsGroup()
    {
        $page = $info = array();
        $ids = $this->get('ids');
        $idArr = explode(',', $ids);
        foreach ($idArr as $val) {
            $info[] = $this->toArr(MaterialPicRelation::model()->findByPk($val));
        }
        // var_dump($info);die;
        if (!$_POST) {
            $page['info'] = $info;
            $page['ids'] = $ids;
            $this->render('changePicsGroup', array('page' => $page));
            exit;
        }
        foreach ($idArr as $val) {
            $info = MaterialPicRelation::model()->findByPk($val);
            $info->group_id = $this->post('picGroupId');
            $dbresult = $info->save();
        }
        $msgarr = array('state' => 1, 'jscode' => "<script>setTimeout(function(){artDialog.close();}, 500)</script>");  //新增的话跳转会添加的页面
        $logs = "将多张图片移动到新的组别（'.$ids.'）";
        $this->logs($logs);
        //成功跳转提示
        $this->msg($msgarr);
    }

    /**
     * 批量删除选择图片
     * author: yjh
     */
    public function actionDeletePics()
    {
        $ids = $this->get('ids');
        $idArr = explode(',', $ids);
        foreach ($idArr as $val) {
            $info = MaterialPicRelation::model()->findByPk($val);
            $imgId = $info->img_id;
            $imgUrl = Resource::model()->findByPk($imgId)->resource_url;
            $img = Yii::app()->basePath . '/..' . $imgUrl;
            if (file_exists(iconv('UTF-8', 'GB2312', $img))) unlink(iconv('UTF-8', 'GB2312', $img));
            Resource::model()->findByPk($imgId)->delete();
            MaterialPicRelation::model()->findByPk($val)->delete();
        }
        $this->logs("已删除选择的图片" . $ids);
        //成功跳转提示
        $this->msg(array('state' => 1, 'msgwords' => "删除图片成功！"));
    }

    /**
     * 修改图片名字
     * author: yjh
     */
    public function actionEditPicName()
    {
        $page = array();
        $id = $this->get('id');
        $picInfo = MaterialPicRelation::model()->findByPk($id);
        $oldName = $picInfo->name;
        if (!$picInfo) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }
        if (!$_POST) {
            $page['info'] = $this->toArr($picInfo);
            $this->render('editPicName', array('page' => $page));
            exit;
        }
        $picInfo->name = $this->post('pic_name');
        $result = MaterialPicRelation::model()->count('name=:name and id != :id', array(':name' => $picInfo->name, ':id' => $id));
        if ($picInfo->name == '') $this->msg(array('state' => 0, 'msgwords' => '未填图片名称！'));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '此名称已存在，请重新输入！'));
        $dbresult = $picInfo->save();
        $msgarr = array('state' => 1, 'jscode' => "<script>setTimeout(function(){artDialog.close();}, 500)</script>");
        $logs = "修改了图片名字：$oldName->" . $picInfo->name;
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
     * 单张图片分组
     * author: yjh
     */
    public function actionChangePicGroup()
    {
        $page = array();
        $id = $this->get('id');
        $picInfo = MaterialPicRelation::model()->findByPk($id);
        if (!$picInfo) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }
        if (!$_POST) {
            $page['info'] = $this->toArr($picInfo);
            $this->render('changePicGroup', array('page' => $page));
            exit;
        }
        $picInfo->group_id = $this->post('picGroupId');
        $dbresult = $picInfo->save();
        $msgarr = array('state' => 1, 'jscode' => "<script>setTimeout(function(){artDialog.close();}, 500)</script>");
        $logs = "将图片移动新分组：" . MaterialPicGroup::model()->findByPk($picInfo->group_id)->group_name;
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
     * 删除一张图片
     * 需要删除资源库的数据及真正的图片
     * 1.先删图片文件2，删资源库数据3.图片素材库数据，均真删
     * author: yjh
     */
    public function actionDeletePic()
    {
        $id = $this->get('id');
        //1.获取路径删图片
        $picInfo = MaterialPicRelation::model()->findByPk($id);
        $picName = $picInfo->name;
        $imgId = $picInfo->img_id;
        $imgUrl = Resource::model()->findByPk($imgId)->resource_url;
        $img = Yii::app()->basePath . '/..' . $imgUrl;
        if (file_exists(iconv('UTF-8', 'GB2312', $img))) unlink(iconv('UTF-8', 'GB2312', $img));
        else
            $this->msg(array('state' => 0, 'msgwords' => "图片不存在！"));
        //2.删除Resource记录
        try {
            Resource::model()->findByPk($imgId)->delete();
        } catch (Exception $e) {
            print_r($e->getMessage());
            echo '删除图片资源失败！';
        }
        try {
            MaterialPicRelation::model()->findByPk($id)->delete();
        } catch (Exception $e) {
            print_r($e->getMessage());
            echo '删除素材图片失败！';
        }
        $this->logs("已删除图片" . $picName);
        //成功跳转提示
        $this->msg(array('state' => 1, 'msgwords' => "删除图片【" . $picName . "】成功！"));
    }

    /**
     * 图片组别管理
     * author: yjh
     */
    public function actionPicGroupManage()
    {
        $params['where'] = '';
        $params['order'] = "  order by id asc    ";
        $params['pagesize'] = 10000;//取出全部组别
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(MaterialPicGroup::model()->tableName())->listdata($params);
        $this->render('picGroupManage', array('page' => $page));
    }

    /**
     * 新增图片组别
     * author: yjh
     */
    public function actionAddPicGroup()
    {
        $page = array();
        //显示表单
        if (!$_POST) {
            $this->render('updatePicGroup', array('page' => $page));
            exit;
        }
        //表单验证
        $info = new MaterialPicGroup();
        $info->group_name = $this->post('group_name');
        $result = MaterialPicGroup::model()->count('group_name=:group_name', array(':group_name' => $info->group_name));
        if ($info->group_name == '') $this->msg(array('state' => 0, 'msgwords' => '未填组别名称！'));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '此组别已存在，请重新输入！'));
        $dbresult = $info->save();
        $msgarr = array('state' => 1, 'jscode' => "<script>setTimeout(function(){artDialog.close();}, 500)</script>");  //新增的话跳转会添加的页面
        $logs = "添加了新的图片组别：" . $info->group_name;
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
     * 修改图片组别
     * author: yjh
     */
    public function actionEditPicGroup()
    {
        $page = array();
        $id = $this->get('id');
        $info = MaterialPicGroup::model()->findByPk($id);
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }
        //显示表单
        if (!$_POST) {
            $page['info'] = $this->toArr($info);
            $this->render('updatePicGroup', array('page' => $page));

            exit;
        }
        $info->group_name = $this->post('group_name');
        $result = MaterialPicGroup::model()->count('group_name=:group_name and id != :id', array(':group_name' => $info->group_name, ':id' => $id));
        if ($info->group_name == '') $this->msg(array('state' => 0, 'msgwords' => '未填组别名称！'));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '此组别已存在，请重新输入！'));
        $dbresult = $info->save();
        $msgarr = array('state' => 1, 'jscode' => "<script>setTimeout(function(){artDialog.close();}, 500)</script>");  //新增的话跳转会添加的页面
        $logs = "修改了图片组别：" . $info->group_name;
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
     * 删除图片组别
     * 注意：同时将其组别下的所有图片还原成未分组：group_Id=1
     * author: yjh
     */
    public function actionDeletePicGroup()
    {
        $id = $this->get('id');
        $info = $this->toArr(MaterialPicRelation::model()->findAll('group_id = :group_id', array(':group_id' => $id)));
        if (count($info) > 0) {
            //将该组别下的所有图片修改为未分组
            foreach ($info as $key => $val) {
                $picInfo = MaterialPicRelation::model()->findByPk($val['id']);
                $picInfo->group_id = 1;
                $picInfo->save();
            }
        }
        $m = MaterialPicGroup::model()->findByPk($id);
        $group_name = $m->group_name;
        $m->delete();
        $this->logs("删除了图片组别：" . $group_name);
        $this->msg(array('state' => 1, 'msgwords' => '删除【' . $group_name . '】成功！'));
    }
    /************************图片处理end*******************************/


    /************************问卷处理start*******************************/
    /**
     * 新增问卷
     * author: fang
     */
    public function actionAddQuestionnaire()
    {
        $page = array();
        //显示表单
        if (!$_POST) {
            $this->render('addQuestionnaire', array('page' => $page));
            exit;
        }
        $info = new Questionnaire();
        $info->vote_title = $this->post('vote_title');
        $info->cat_id = $this->post('cat_id');
        $info->support_staff_id = $this->post('support_staff_id');
        $info->is_vote = $this->post('is_vote');
        if ($this->post('is_vote') == 1) {
            $info->vote_page = $this->post('vote_page');
            $info->top_img = $this->post('top_img');
            $info->tip = $this->post('tip');
            $info->bottom_tip = $this->post('bottom_tip');
        }
        if ($info->vote_title == '') $this->msg(array('state' => 0, 'msgwords' => '投票标题未填写'));
        if ($info->cat_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择商品类别'));
        if ($info->support_staff_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择支持人员'));


        for ($i = 0; $i <= 4; $i++) {
            if (isset($this->post('quest_title')[$i])) {
                if ($this->post('quest_title')[$i] == '') $this->msg(array('state' => 0, 'msgwords' => '未选择问题标题'));
                if ($this->post('tab_a')[$i] == '') $this->msg(array('state' => 0, 'msgwords' => '未选择问题选项A'));
                if ($this->post('tab_b')[$i] == '') $this->msg(array('state' => 0, 'msgwords' => '未选择问题选项B'));
                if ($this->post('tab_c')[$i] == '') $this->msg(array('state' => 0, 'msgwords' => '未选择问题选项C'));
                if ($this->post('tab_d')[$i] == '') $this->msg(array('state' => 0, 'msgwords' => '未选择问题选项D'));
            }
        }
        $info->create_date = time();
        $dbresult = $info->save();
        $id = $info->primaryKey;
        $msgarr = array('state' => 1, 'url' => $this->createUrl('material/index') . '?group_id=4&p=' . $_GET['p'] . '');
        $logs = "添加了新的问卷：" . $info->vote_title;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            //新增和修改之后的动作
            //将问题存入题库
            foreach ($this->post('quest_title') as $k => $v) {
                $data = new Quest();
                $data->qus_id = $id;
                $data->quest_title = $v;
                if ($data->quest_title == '') continue;
                $data->tab_a = $this->post('tab_a')[$k];
                $data->tab_b = $this->post('tab_b')[$k];
                $data->tab_c = $this->post('tab_c')[$k];
                $data->tab_d = $this->post('tab_d')[$k];
                $data->create_date = time();
                $data->save();
                $logs_quest = "添加了新的问题库：" . $data->quest_title;
                $this->logs($logs_quest);
            }
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }


    /**
     * 修改问卷
     * author: fang
     */
    public function actionEditQuestion()
    {
        $page = array();
        $id = $this->get('id');
        $info = Questionnaire::model()->findByPk($id);
        $data = Quest::model()->findAllByAttributes(array('qus_id' => $id));
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '问卷不存在'));
        }
        if (!$_POST) {
            $page['info'] = $this->toArr($info);
            $this->render('updateQuestion', array('page' => $page, 'data' => $data));
            exit;
        }
        $info->vote_title = $this->post('vote_title');
        $info->cat_id = $this->post('cat_id');
        $info->support_staff_id = $this->post('support_staff_id');
        $info->is_vote = $this->post('is_vote');
        if ($this->post('is_vote') == 1) {
            $info->vote_page = $this->post('vote_page');
            $info->top_img = $this->post('top_img');
            $info->tip = $this->post('tip');
            $info->bottom_tip = $this->post('bottom_tip');
        } else {
            $info->vote_page = '';
            $info->top_img = '';
            $info->tip = '';
            $info->bottom_tip = '';
        }

        if ($info->vote_title == '') $this->msg(array('state' => 0, 'msgwords' => '投票标题未填写'));
        if ($info->cat_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择商品类别'));
        if ($info->support_staff_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择支持人员'));
        if ($this->post('quest_title')[1] == '') $this->msg(array('state' => 0, 'msgwords' => '未选择问题标题'));
        if ($this->post('tab_a')[1] == '') $this->msg(array('state' => 0, 'msgwords' => '未选择问题选项A'));
        if ($this->post('tab_b')[1] == '') $this->msg(array('state' => 0, 'msgwords' => '未选择问题选项B'));
        for ($i = 2; $i <= 5; $i++) {
            if (isset($this->post('quest_title')[$i])) {
                if ($this->post('quest_title')[$i] == '') $this->msg(array('state' => 0, 'msgwords' => '未选择问题标题'));
                if ($this->post('tab_a')[$i] == '') $this->msg(array('state' => 0, 'msgwords' => '未选择问题选项A'));
                if ($this->post('tab_b')[$i] == '') $this->msg(array('state' => 0, 'msgwords' => '未选择问题选项B'));
            }
        }


        $info->update_date = time();
        $dbresult = $info->save();
        $msgarr = array('state' => 1); //保存的话，跳转到之前的列表
        $logs = "修改了问卷：" . $info->vote_title;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            //新增和修改之后的动作
            //修改问题存入题库
            foreach ($this->post('quest_title') as $k => $v) {
                $data = Quest::model()->findByPk($this->post('q_id')[$k]);
                if (!$data) {
                    $data = new Quest();
                    $data->create_date = time();
                }

                $data->qus_id = $id;
                $data->quest_title = $v;
                if ($data->quest_title == '') continue;
                $data->tab_a = $this->post('tab_a')[$k];
                $data->tab_b = $this->post('tab_b')[$k];
                $data->tab_c = $this->post('tab_c')[$k];
                $data->tab_d = $this->post('tab_d')[$k];
                $data->update_date = time();
                $data->save();
                $logs_quest = "修改了问题：" . $data->quest_title;
                $this->logs($logs_quest);
            }
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }


    /**
     * 删除问卷
     * author: fang
     */
    public function actionDeleteQuestion()
    {
        $id = $this->get('id');
        if ($id == '') $this->msg(array('state' => 0, 'msgwords' => '选择删除的问卷'));
        $info = Questionnaire::model()->findByPk($id);
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '问卷不存在'));
        }
        $inlineQuestList = MaterialArticleTemplate::model()->findAllByAttributes(array('psq_id' => $id));
        if ($inlineQuestList) {
            $this->msg(array('state' => 0, 'msgwords' => '问卷在使用中，不能删除！'));
        }
        $info->delete();
        $this->msg(array('state' => 1, 'msgwords' => '删除问卷成功！'));
    }


    /**
     * 删除问题
     * author: fang
     */
    public function actionDeleteQuestBank()
    {
        $id = $_POST['id'];
        $info = Quest::model()->findByPk($id);
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '问题不存在'));
        }
        $info->delete();
        //$this->msg(array('state' => 1, 'msgwords' => '删除问题成功！'));
    }

    /************************问卷处理end*******************************/


    /************************视频处理start*******************************/

    /**
     * 添加视频素材
     * author: yjh
     */
    public function actionVideoAdd()
    {
        if (!$_POST) {
            MaterialVideo::model()->delExcessVideos();
            //删除多余的视频及记录
            $this->render('uploadVideo');
            exit;
        }
        if ($this->get('video_id') == '') $this->msg(array('state' => 0, 'msgwords' => '未上传视频文件！'));
        if ($this->get('video_name') == '') $this->msg(array('state' => 0, 'msgwords' => '未填写视频名称！'));
        $result = MaterialVideo::model()->count('video_name=:video_name', array(':video_name' => $this->get('video_name')));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '视频名称不能相同！'));
        if ($this->get('support_staff_id') == '') $this->msg(array('state' => 0, 'msgwords' => '未选择支持人员！'));
        $info = new MaterialVideo();
        $info->video_name = $this->get('video_name');
        $info->video_id = $this->get('video_id');
        $info->support_staff_id = $this->get('support_staff_id');
        $info->o_name = $this->get('rname');
        $info->video_size = $this->get('video_size');
        $info->create_time = time();
        $info->update_time = time();
        $dbresult = $info->save();
        $msgarr = array('state' => 1, 'jscode' => "<script>setTimeout(function(){artDialog.close();}, 500)</script>");  //新增的话跳转会添加的页面
        $logs = "添加了新的视频素材：" . $info->video_name;
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
     * 修改视频素材
     * author: yjh
     */
    public function actionVideoEdit()
    {
        $page = array();
        $id = $this->get('id');
        $info = MaterialVideo::model()->findByPk($id);
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }
        //显示表单
        if (!$_POST) {
            $page['info'] = $this->toArr($info);
            $this->render('uploadVideo', array('page' => $page));
            exit;
        }
        if ($this->get('video_name') == '') $this->msg(array('state' => 0, 'msgwords' => '未填写视频名称！'));
        $result = MaterialVideo::model()->count('video_name=:video_name', array(':video_name' => $this->get('video_name')));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '视频名称不能相同！'));
        if ($this->get('support_staff_id') == '') $this->msg(array('state' => 0, 'msgwords' => '未选择支持人员！'));

        $info->video_name = $this->get('video_name');
        $info->support_staff_id = $this->get('support_staff_id');
        $info->update_time = time();
        $dbresult = $info->save();
        $msgarr = array('state' => 1, 'jscode' => "<script>setTimeout(function(){artDialog.close();}, 500)</script>");  //新增的话跳转会添加的页面
        $logs = "修改了视频素材：" . $info->video_name;
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
     * 删除视频
     * author: yjh
     */
    public function actionVideoDelete()
    {
        $id = $this->get('id');
        //1.获取路径删视频
        $info = MaterialVideo::model()->findByPk($id);
        $videoName = $info->video_name;
        $videoId = $info->video_id;
        $videoUrl = Yii::app()->basePath . '/..' . Resource::model()->findByPk($videoId)->resource_url;
        if (file_exists($videoUrl)) {
            try {
                unlink($videoUrl);
            } catch (Exception $e) {
                print_r($e->getMessage());
                echo '删除视频资源失败！';
            }
        }
        Resource::model()->findByPk($videoId)->delete();
        MaterialVideo::model()->findByPk($id)->delete();
        $this->logs("已删除视频素材" . $videoName);
        //成功跳转提示
        $this->msg(array('state' => 1, 'msgwords' => "删除视频素材【" . $videoName . "】成功！"));
    }

    /************************视频处理end**********************************/

    /************************语音处理begin********************************/

    /**
     * 新增语音素材
     * author: yjh
     */
    public function actionAudioAdd()
    {
        if (!$_POST) {
            MaterialAudio::model()->delExcessAudios();
            //删除多余的语音及记录
            $this->render('uploadAudio');
            exit;
        }
        if ($this->get('audio_id') == '') $this->msg(array('state' => 0, 'msgwords' => '未上传语音文件！'));
        if ($this->get('audio_name') == '') $this->msg(array('state' => 0, 'msgwords' => '未填写名称！'));
        $result = MaterialAudio::model()->count('audio_name=:audio_name', array(':audio_name' => $this->get('audio_name')));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '语音名称不能相同！'));
        if ($this->get('support_staff_id') == '') $this->msg(array('state' => 0, 'msgwords' => '未选择支持人员！'));
        $info = new MaterialAudio();
        $info->audio_name = $this->get('audio_name');
        $info->audio_id = $this->get('audio_id');
        $info->support_staff_id = $this->get('support_staff_id');
        $info->o_name = $this->get('rname');
        $info->play_time = $this->get('playtime');
        $info->audio_size = $this->get('audio_size');
        $info->create_time = time();
        $info->update_time = time();
        $dbresult = $info->save();
        $msgarr = array('state' => 1, 'jscode' => "<script>setTimeout(function(){artDialog.close();}, 500)</script>");  //新增的话跳转会添加的页面
        $logs = "添加了新的语音素材：" . $info->audio_name;
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
     * 修改语音素材
     * author: yjh
     */
    public function actionAudioEdit()
    {
        $page = array();
        $id = $this->get('id');
        $info = MaterialAudio::model()->findByPk($id);
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }
        //显示表单
        if (!$_POST) {
            $page['info'] = $this->toArr($info);
            $this->render('uploadAudio', array('page' => $page));
            exit;
        }
        if ($this->get('audio_name') == '') $this->msg(array('state' => 0, 'msgwords' => '未填写名称！'));
        if ($this->get('support_staff_id') == '') $this->msg(array('state' => 0, 'msgwords' => '未选择支持人员！'));
        $info->audio_name = $this->get('audio_name');
        $info->support_staff_id = $this->get('support_staff_id');
        $info->update_time = time();
        $dbresult = $info->save();
        $msgarr = array('state' => 1, 'jscode' => "<script>setTimeout(function(){artDialog.close();}, 500)</script>");  //新增的话跳转会添加的页面
        $logs = "修改了语音素材：" . $info->audio_name;
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
     * 删除语音
     * author: yjh
     */
    public function actionAudioDelete()
    {
        $id = $this->get('id');
        //1.获取路径语音
        $info = MaterialAudio::model()->findByPk($id);
        $audioName = $info->audio_name;
        $audioId = $info->audio_id;
        $audioUrl = Yii::app()->basePath . '/..' . Resource::model()->findByPk($audioId)->resource_url;
        if (file_exists($audioUrl)) {
            try {
                unlink($audioUrl);
            } catch (Exception $e) {
                print_r($e->getMessage());
                echo '删除语音资源失败！';
            }
        }
        Resource::model()->findByPk($audioId)->delete();
        MaterialAudio::model()->findByPk($id)->delete();
        $this->logs("已删除语音素材" . $audioName);
        //成功跳转提示
        $this->msg(array('state' => 1, 'msgwords' => "删除语音素材【" . $audioName . "】成功！"));
    }

    /************************语音处理end**********************************/

    /************************评论处理start*******************************/
    /**
     * 新增评论
     * author: fang
     */
    public function actionAddReview()
    {
        $page = array();
        //显示表单
        if (!$_POST) {
            $this->render('addReview', array('page' => $page));
            exit;
        }
        $avatar_id = $this->get('avatar_id');

        $info = new MaterialReview();
        $info->review_title = $this->post('review_title');
        $info->review_type = $this->post('review_type');
        $info->support_staff_id = $this->post('support_staff_id');
        $info->avatar_id = $avatar_id;
        if ($info->review_title == '') $this->msg(array('state' => 0, 'msgwords' => '未填写评论标题'));
        if ($info->review_type == '') $this->msg(array('state' => 0, 'msgwords' => '未选择评论类型'));
        if ($info->support_staff_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择支持人员'));
        if ($avatar_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择头像素材库'));
        for ($i = 0; $i <= 7; $i++) {
            if (isset($this->post('review_name')[$i])) {
                if ($this->post('review_name')[$i] == '') $this->msg(array('state' => 0, 'msgwords' => '未填写评论名称'));
                if ($this->post('review_content')[$i] == '') $this->msg(array('state' => 0, 'msgwords' => '未填写评论内容'));
            }
        }
        $info->create_date = time();
        $dbresult = $info->save();
        $id = $info->primaryKey;
        $msgarr = array('state' => 1, 'url' => $this->createUrl('material/index') . '?group_id=5&p=' . $_GET['p'] . '');
        $logs = "添加了新的评论：" . $info->review_title;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            //新增和修改之后的动作
            //将评论存入题库
            $review_name = $this->get('review_name');
            $nameNum = count($review_name);

            $sql = "SELECT resource_url FROM material_pic_relation  as a LEFT JOIN resource_list as b on img_id=resource_id WHERE group_id= ".$avatar_id." ORDER BY rand() LIMIT $nameNum";
            $avatarArr = Yii::app()->db->createCommand($sql)->queryAll();

            foreach ($review_name as $k => $v) {
                $data = new MaterialReviewDetail();
                $data->review_id = $id;
                $data->review_name = $v;
                if ($data->review_name == '') continue;
                $data->review_content = $this->post('review_content')[$k];
                $data->review_date = $this->post('review_date')[$k];
                $data->avatar_url = $avatarArr[$k]['resource_url'];
                $data->create_date = time();
                $data->save();
                $logs_quest = "添加了新的评论库：" . $data->review_name;
                $this->logs($logs_quest);
            }
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 修改评论
     * author: fang
     */
    public function actionEditReview()
    {
        $page = array();
        $id = $this->get('id');
        $info = MaterialReview::model()->findByPk($id);
        $data = MaterialReviewDetail::model()->findAllByAttributes(array('review_id' => $id));
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '评论不存在'));
        }
        if (!$_POST) {
            $page['info'] = $this->toArr($info);
            $this->render('updateReview', array('page' => $page, 'data' => $data));
            exit;
        }
        $avatar_id = $this->get('avatar_id');

        $info->review_title = $this->post('review_title');
        $info->review_type = $this->post('review_type');
        $info->support_staff_id = $this->post('support_staff_id');
        $info->avatar_id = $avatar_id;
        if ($info->review_title == '') $this->msg(array('state' => 0, 'msgwords' => '未填写评论标题'));
        if ($info->review_type == '') $this->msg(array('state' => 0, 'msgwords' => '未选择评论类型'));
        if ($info->support_staff_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择支持人员'));
        if ($avatar_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择头像素材库'));
        for ($i = 0; $i <= 7; $i++) {
            if (isset($this->post('review_name')[$i])) {
                if ($this->post('review_name')[$i] == '') $this->msg(array('state' => 0, 'msgwords' => '未填写评论名称'));
                if ($this->post('review_content')[$i] == '') $this->msg(array('state' => 0, 'msgwords' => '未填写评论内容'));
            }
        }
        $info->update_date = time();
        $dbresult = $info->save();
        $msgarr = array('state' => 1); //保存的话，跳转到之前的列表
        $logs = "修改了评论：" . $info->review_title;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            //新增和修改之后的动作
            //将评论存入题库
            $review_name = $this->get('review_name');
            $nameNum = count($review_name);

            $sql = "SELECT resource_url FROM material_pic_relation  as a LEFT JOIN resource_list as b on img_id=resource_id WHERE group_id= ".$avatar_id." ORDER BY rand() LIMIT $nameNum";
            $avatarArr = Yii::app()->db->createCommand($sql)->queryAll();

            foreach ($this->post('review_name') as $k => $v) {
                $data = MaterialReviewDetail::model()->findByPk($this->post('r_id')[$k]);
                if (!$data) {
                    $data = new MaterialReviewDetail();
                    $data->create_date = time();
                }
                $data->review_id = $id;
                $data->review_name = $v;
                if ($data->review_name == '') continue;
                $data->review_content = $this->post('review_content')[$k];
                $data->avatar_url = $avatarArr[$k]['resource_url'];
                $data->review_date = $this->post('review_date')[$k];
                $data->update_date = time();
                $data->save();
                $logs_review = "修改了评论：" . $data->review_name;
                $this->logs($logs_review);
            }
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 删除评论
     * author: fang
     */
    public function actionDeleteReview()
    {
        $id = $this->get('id');
        if ($id == '') $this->msg(array('state' => 0, 'msgwords' => '选择删除的评论'));
        $info = MaterialReview::model()->findByPk($id);
        $onlineRiviewList = MaterialArticleTemplate::model()->findAllByAttributes(array('review_id' => $id));
        if ($onlineRiviewList) {
            $this->msg(array('state' => 0, 'msgwords' => '评论在使用中，不能删除！'));
        }
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '评论不存在'));
        }
        $info->delete();
        $this->msg(array('state' => 1, 'msgwords' => '删除评论成功！'));
    }

    /**
     * 新增论坛评论
     * author: yjh
     */
    public function actionAddForumReview()
    {
        $page = array();
        //显示表单
        if (!$_POST) {
            $this->render('updateForumReview', array('page' => $page));
            exit;
        }
        //验证数据
        //评论名称 支持人员 是否分页 每页条数 头像组别
        $review_title = $this->get('review_title');
        $landlord = $this->get('landlord');
        $support_staff_id = $this->get('support_staff_id');
        $is_page = $this->get('is_page');
        $page_size = $this->get('page_size');
        $avatar_id = $this->get('avatar_id');
        $reply_to = $this->get('reply_to');
        $review_date = $this->get('review_date');
        $review_content = $this->get('review_content');
        $avatar_url = $this->get('avater_img');
        $review_type = 1;
        if ($avatar_url == '') $this->msg(array('state' => 0, 'msgwords' => '未选择楼主头像'));
        if ($support_staff_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择支持人员'));
        if ($avatar_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择头像素材库'));

        //名称和头像处理
        $review_name = $this->get('review_name');
        $nameArr = array();
        foreach ($review_name as $key => $val) {
            $nameArr = array_merge($nameArr, $val);
        }
        if (in_array('', $nameArr)) $this->msg(array('state' => 0, 'msgwords' => '某些楼未写名称'));
        array_push($nameArr, $landlord);
        $nameArr = array_unique($nameArr);
        $nameNum = count($nameArr);
        $sql = "SELECT resource_url FROM material_pic_relation  as a LEFT JOIN resource_list as b on img_id=resource_id WHERE group_id=$avatar_id  ORDER BY rand() LIMIT $nameNum";
        $avatarArr = Yii::app()->db->createCommand($sql)->queryAll();
        if (count($avatarArr) < $nameNum) {
//            array_pad($avatarArr,$nameNum,array('resource_url' => '/static/img/default_avatar.jpg'));
            for ($i = count($avatarArr); $i < $nameNum; $i++) {
                array_push($avatarArr, array('resource_url' => '/static/img/default_avatar.jpg'));
            }
        }
        $avatarArr = array_combine($nameArr, $avatarArr);
        $avatarArr[$landlord] = array('resource_url' => $avatar_url);
        $data = array();
        foreach ($review_name as $key => $val) {
            foreach ($val as $k => $v) {
                //判断条件
                $data[$key . '_' . $k]['floor'] = $key;
                $data[$key . '_' . $k]['review_name'] = $v;
                $data[$key . '_' . $k]['reply_to'] = $reply_to[$key][$k];
                $data[$key . '_' . $k]['review_date'] = $review_date[$key][$k];
                if ($data[$key . '_' . $k]['review_date'] == '') $this->msg(array('state' => 0, 'msgwords' => ($key + 1) . '楼' . ($k + 1) . '评论未写时间'));
                $data[$key . '_' . $k]['avatar_url'] = $avatarArr[$v]['resource_url'];
                $data[$key . '_' . $k]['review_content'] = $review_content[$key][$k];
                if ($data[$key . '_' . $k]['review_content'] == '') $this->msg(array('state' => 0, 'msgwords' => ($key + 1) . '楼' . ($k + 1) . '评论未填写内容'));

            }
        }
        $info = new MaterialReview();
        $info->review_title = $review_title;
        $info->review_type = $review_type;
        $info->is_page = $is_page;
        $info->landlord = $landlord;
        $info->avatar_url = $avatarArr[$landlord]['resource_url'];
        $info->page_size = $page_size;
        $info->avatar_id = $avatar_id;
        $info->support_staff_id = $support_staff_id;
        $info->create_date = time();
        $info->update_date = time();
        $dbresult = $info->save();
        $review_id = $info->primaryKey;
        $msgarr = array('state' => 1, 'url' => $this->createUrl('material/index') . '?group_id=5&p=' . $_GET['p'] . '');
        $logs = "添加了新的论坛评论：" . $review_title;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            foreach ($data as $value) {
                $m = new MaterialReviewDetail();
                $m->review_id = $review_id;
                $m->review_name = $value['review_name'];
                $m->review_content = $value['review_content'];
                $m->review_date = $value['review_date'];
                $m->floor = $value['floor'];
                $m->reply_to = $value['reply_to'];
                $m->avatar_url = $value['avatar_url'];
                $m->create_date = time();
                $m->update_date = time();
                $m->save();
            }
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 修改论坛评论
     * author: yjh
     */
    public function actionEditForumReview()
    {
        $page = array();
        $id = $this->get('id');
        $info = Dtable::toArr(MaterialReview::model()->findByPk($id));
        $data = Dtable::toArr(MaterialReviewDetail::model()->findAll('review_id=' . $id . " order by id asc"));

        $numArr = array_count_values(array_column($data, 'floor'));
        $num = 0;
        $ret = array();
        foreach ($numArr as $key => $val) {
            $ret[] = array_slice($data, $num, $val);
            $num += $val;
        }
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '评论不存在'));
        }
        if (!$_POST) {
            $page['info'] = $info;
            $page['review_data'] = $ret;
            $this->render('updateForumReview', array('page' => $page));
            exit;
        }
        //先删除原有评论重新添加
        //验证数据
        //评论名称 支持人员 是否分页 每页条数 头像组别
        $review_id = $this->get('id');
        $review_title = $this->get('review_title');
        $support_staff_id = $this->get('support_staff_id');
        $is_page = $this->get('is_page');
        $page_size = $this->get('page_size');
        $landlord = $this->get('landlord');
        $avatar_id = $this->get('avatar_id');
        $reply_to = $this->get('reply_to');
        $review_date = $this->get('review_date');
        $review_content = $this->get('review_content');
        $avatar_url = $this->get('avater_img');
        $review_type = 1;
        if ($avatar_url == '') $this->msg(array('state' => 0, 'msgwords' => '未选择楼主头像'));
        if ($support_staff_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择支持人员'));
        if ($avatar_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择头像素材库'));

        //名称和头像处理
        $review_name = $this->get('review_name');
        $nameArr = array();
        foreach ($review_name as $key => $val) {
            $nameArr = array_merge($nameArr, $val);
        }
        if (in_array('', $nameArr)) $this->msg(array('state' => 0, 'msgwords' => '某些楼未写名称'));
        $nameArr[] = $landlord;
        $nameArr = array_unique($nameArr);
        $nameNum = count($nameArr);
        $sql = "SELECT resource_url FROM material_pic_relation  as a LEFT JOIN resource_list as b on img_id=resource_id WHERE group_id=$avatar_id  ORDER BY rand() LIMIT $nameNum";
        $avatarArr = Yii::app()->db->createCommand($sql)->queryAll();
        if (count($avatarArr) < $nameNum) {
            //array_pad($avatarArr,$nameNum,array('resource_url' => '/static/img/default_avatar.jpg'));
            for ($i = count($avatarArr); $i < $nameNum; $i++) {
                array_push($avatarArr, array('resource_url' => '/static/img/default_avatar.jpg'));
            }
        }
        $avatarArr = array_combine($nameArr, $avatarArr);
        $avatarArr[$landlord] = array('resource_url' => $avatar_url);

        $data = array();
        foreach ($review_name as $key => $val) {
            foreach ($val as $k => $v) {
                //判断条件
                $data[$key . '_' . $k]['floor'] = $key;
                $data[$key . '_' . $k]['review_name'] = $v;
                $data[$key . '_' . $k]['reply_to'] = $reply_to[$key][$k];
                $data[$key . '_' . $k]['review_date'] = $review_date[$key][$k];
                if ($data[$key . '_' . $k]['review_date'] == '') $this->msg(array('state' => 0, 'msgwords' => ($key + 1) . '楼' . ($k + 1) . '评论未写时间'));
                $data[$key . '_' . $k]['avatar_url'] = $avatarArr[$v]['resource_url'];
                $data[$key . '_' . $k]['review_content'] = $review_content[$key][$k];
                if ($data[$key . '_' . $k]['review_content'] == '') $this->msg(array('state' => 0, 'msgwords' => ($key + 1) . '楼' . ($k + 1) . '评论未填写内容'));

            }
        }
        $info = MaterialReview::model()->findByPk($review_id);
        $info->review_title = $review_title;
        $info->review_type = $review_type;
        $info->is_page = $is_page;
        $info->page_size = $page_size;
        $info->landlord = $landlord;
        $info->avatar_url = $avatarArr[$landlord]['resource_url'];
        $info->avatar_id = $avatar_id;
        $info->support_staff_id = $support_staff_id;
        $info->update_date = time();
        $dbresult = $info->save();
        $msgarr = array('state' => 1, 'url' => $this->createUrl('material/index') . '?group_id=5&p=' . $_GET['p'] . '');
        $logs = "修改了论坛评论：ID:" . $review_type . $review_title;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            MaterialReviewDetail::model()->deleteAll("review_id=" . $review_id);
            foreach ($data as $value) {
                $m = new MaterialReviewDetail();
                $m->review_id = $review_id;
                $m->review_name = $value['review_name'];
                $m->review_content = $value['review_content'];
                $m->review_date = $value['review_date'];
                $m->floor = $value['floor'];
                $m->reply_to = $value['reply_to'];
                $m->avatar_url = $value['avatar_url'];
                $m->create_date = time();
                $m->update_date = time();
                $m->save();
            }
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 新增精选评论
     * author: yjh
     */
    public function actionAddSelectReview()
    {
        $page = array();
        //显示表单
        if (!$_POST) {
            $this->render('updateSelectReview', array('page' => $page));
            exit;
        }
        //验证数据
        //评论名称 支持人员  头像组别
        $review_title = $this->get('review_title');
        $support_staff_id = $this->get('support_staff_id');
        $avatar_id = $this->get('avatar_id');
        $reply_to = $this->get('reply_to');
        $review_date = $this->get('review_date');
        $review_content = $this->get('review_content');

        $review_type = 2;
        if ($support_staff_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择支持人员'));
        if ($avatar_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择头像素材库'));

        //名称和头像处理
        $review_name = $this->get('review_name');
        $nameArr = array();
        foreach ($review_name as $key => $val) {
            $nameArr = array_merge($nameArr, $val);
        }
        if (in_array('', $nameArr)) $this->msg(array('state' => 0, 'msgwords' => '某些楼未写名称'));
        $nameArr = array_unique($nameArr);
        $nameNum = count($nameArr);
        $sql = "SELECT resource_url FROM material_pic_relation  as a LEFT JOIN resource_list as b on img_id=resource_id WHERE group_id=$avatar_id  ORDER BY rand() LIMIT $nameNum";
        $avatarArr = Yii::app()->db->createCommand($sql)->queryAll();
        if (count($avatarArr) < $nameNum) {
//            array_pad($avatarArr,$nameNum,array('resource_url' => '/static/img/default_avatar.jpg'));
            for ($i = count($avatarArr); $i < $nameNum; $i++) {
                array_push($avatarArr, array('resource_url' => '/static/img/default_avatar.jpg'));
            }
        }
        $avatarArr = array_combine($nameArr, $avatarArr);
        $data = array();
        foreach ($review_name as $key => $val) {
            foreach ($val as $k => $v) {
                //判断条件
                $data[$key . '_' . $k]['floor'] = $key;
                $data[$key . '_' . $k]['review_name'] = $v;
                $data[$key . '_' . $k]['reply_to'] = $reply_to[$key][$k];
                $data[$key . '_' . $k]['review_date'] = $review_date[$key][$k];
                if ($data[$key . '_' . $k]['review_date'] == '') $this->msg(array('state' => 0, 'msgwords' => ($key + 1) . '楼' . ($k + 1) . '评论未写时间'));
                $data[$key . '_' . $k]['avatar_url'] = $avatarArr[$v]['resource_url'];
                $data[$key . '_' . $k]['review_content'] = $review_content[$key][$k];
                if ($data[$key . '_' . $k]['review_content'] == '') $this->msg(array('state' => 0, 'msgwords' => ($key + 1) . '楼' . ($k + 1) . '评论未填写内容'));
            }
        }
        $info = new MaterialReview();
        $info->review_title = $review_title;
        $info->review_type = $review_type;
        $info->avatar_id = $avatar_id;
        $info->support_staff_id = $support_staff_id;
        $info->create_date = time();
        $info->update_date = time();
        $dbresult = $info->save();
        $review_id = $info->primaryKey;
        $msgarr = array('state' => 1, 'url' => $this->createUrl('material/index') . '?group_id=5&p=' . $_GET['p'] . '');
        $logs = "添加了新的精选评论：" . $review_title;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            foreach ($data as $value) {
                $m = new MaterialReviewDetail();
                $m->review_id = $review_id;
                $m->review_name = $value['review_name'];
                $m->review_content = $value['review_content'];
                $m->review_date = $value['review_date'];
                $m->floor = $value['floor'];
                $m->reply_to = $value['reply_to'];
                $m->avatar_url = $value['avatar_url'];
                $m->create_date = time();
                $m->update_date = time();
                $m->save();
            }
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 修改精选评论
     * author: yjh
     */
    public function actionEditSelectReview()
    {
        $page = array();
        $id = $this->get('id');
        $info = Dtable::toArr(MaterialReview::model()->findByPk($id));
        $data = Dtable::toArr(MaterialReviewDetail::model()->findAll('review_id=' . $id . " order by id asc"));

        $numArr = array_count_values(array_column($data, 'floor'));
        $num = 0;
        $ret = array();
        foreach ($numArr as $key => $val) {
            $ret[] = array_slice($data, $num, $val);
            $num += $val;
        }
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '评论不存在'));
        }
        if (!$_POST) {
            $page['info'] = $info;
            $page['review_data'] = $ret;
            $this->render('updateSelectReview', array('page' => $page));
            exit;
        }
        //先删除原有评论重新添加
        //验证数据
        //评论名称 支持人员 是否分页 每页条数 头像组别
        $review_id = $this->get('id');
        $review_title = $this->get('review_title');
        $support_staff_id = $this->get('support_staff_id');
        $avatar_id = $this->get('avatar_id');
        $reply_to = $this->get('reply_to');
        $review_date = $this->get('review_date');
        $review_content = $this->get('review_content');
        $review_type = 2;
        if ($support_staff_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择支持人员'));
        if ($avatar_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择头像素材库'));

        //名称和头像处理
        $review_name = $this->get('review_name');
        $nameArr = array();
        foreach ($review_name as $key => $val) {
            $nameArr = array_merge($nameArr, $val);
        }
        if (in_array('', $nameArr)) $this->msg(array('state' => 0, 'msgwords' => '某些楼未写名称'));
        $nameArr = array_unique($nameArr);
        $nameNum = count($nameArr);
        $sql = "SELECT resource_url FROM material_pic_relation  as a LEFT JOIN resource_list as b on img_id=resource_id WHERE group_id=$avatar_id  ORDER BY rand() LIMIT $nameNum";
        $avatarArr = Yii::app()->db->createCommand($sql)->queryAll();
        if (count($avatarArr) < $nameNum) {
            //array_pad($avatarArr,$nameNum,array('resource_url' => '/static/img/default_avatar.jpg'));
            for ($i = count($avatarArr); $i < $nameNum; $i++) {
                array_push($avatarArr, array('resource_url' => '/static/img/default_avatar.jpg'));
            }
        }
        $avatarArr = array_combine($nameArr, $avatarArr);

        $data = array();
        foreach ($review_name as $key => $val) {
            foreach ($val as $k => $v) {
                //判断条件
                $data[$key . '_' . $k]['floor'] = $key;
                $data[$key . '_' . $k]['review_name'] = $v;
                $data[$key . '_' . $k]['reply_to'] = $reply_to[$key][$k];
                $data[$key . '_' . $k]['review_date'] = $review_date[$key][$k];
                if ($data[$key . '_' . $k]['review_date'] == '') $this->msg(array('state' => 0, 'msgwords' => ($key + 1) . '楼' . ($k + 1) . '评论未写时间'));
                $data[$key . '_' . $k]['avatar_url'] = $avatarArr[$v]['resource_url'];
                $data[$key . '_' . $k]['review_content'] = $review_content[$key][$k];
                if ($data[$key . '_' . $k]['review_content'] == '') $this->msg(array('state' => 0, 'msgwords' => ($key + 1) . '楼' . ($k + 1) . '评论未填写内容'));

            }
        }
        $info = MaterialReview::model()->findByPk($review_id);
        $info->review_title = $review_title;
        $info->review_type = $review_type;
        $info->avatar_id = $avatar_id;
        $info->support_staff_id = $support_staff_id;
        $info->update_date = time();
        $dbresult = $info->save();
        $msgarr = array('state' => 1, 'url' => $this->createUrl('material/index') . '?group_id=5&p=' . $_GET['p'] . '');
        $logs = "修改了精选评论：" . $review_title;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            MaterialReviewDetail::model()->deleteAll("review_id=" . $review_id);
            foreach ($data as $value) {
                $m = new MaterialReviewDetail();
                $m->review_id = $review_id;
                $m->review_name = $value['review_name'];
                $m->review_content = $value['review_content'];
                $m->review_date = $value['review_date'];
                $m->floor = $value['floor'];
                $m->reply_to = $value['reply_to'];
                $m->avatar_url = $value['avatar_url'];
                $m->create_date = time();
                $m->update_date = time();
                $m->save();
            }
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /*************问卷处理*************************/
    /**
     * 删除问题
     * author: fang
     */
    public function actionDeleteReviewDetail()
    {
        $id = $_POST['id'];
        $info = MaterialReviewDetail::model()->findByPk($id);
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '评论不存在'));
        }
        $info->delete();
        //$this->msg(array('state' => 1, 'msgwords' => '删除问题成功！'));
    }

    /************************评论处理end*******************************/

    /************************图文组别管理*******************************/

    /**
     * 新增图文组别
     * author: yjh
     */
    public function actionAddArticleGroup()
    {
        $page = array();
        //显示表单
        if (!$_POST) {
            $this->render('updateArticleGroup', array('page' => $page));
            exit;
        }
        //表单验证
        $info = new MaterialArticleGroup();
        $info->group_name = $this->post('group_name');
        $info->group_code = $this->post('group_code');
        $info->cat_id = $this->post('cat_id');
        if ($info->group_name == '') $this->msg(array('state' => 0, 'msgwords' => '未填组别名称！'));
        if ($info->group_code == '') $this->msg(array('state' => 0, 'msgwords' => '未填组别编码！'));
        if ($info->cat_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择商品类型！'));
        $result = MaterialArticleGroup::model()->count('group_name=:group_name', array(':group_name' => $info->group_name));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '此组别名称已存在，请重新输入！'));
        $result = MaterialArticleGroup::model()->count('group_code=:group_code', array(':group_code' => $info->group_code));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '此组别编码已存在，请重新输入！'));
        $info->create_time = time();
        $info->update_time = time();
        $dbresult = $info->save();
        $msgarr = array('state' => 1, 'jscode' => "<script>setTimeout(function(){artDialog.close();}, 500)</script>");  //新增的话跳转会添加的页面
        $logs = "添加了新的图文组别：" . $info->group_name;
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

    public function actionAddMessage()
    {
        $page = array();
        $article_id = $this->get('article_id');
        if (!$article_id) {
            $this->msg(array('state' => 0, 'msgwords' => '参数错误！'));
        }
        //显示表单
        if (!$_POST) {
            $page['info']['article_id'] = $article_id;
            $this->render('addMessage', array('page' => $page));
            exit;
        }
        //表单验证
        $info = new MaterialArticleMessage();
        $info->channel_id = $this->post('channel_id');
        $info->partner_id = $this->post('partner_id');
        $info->business_type = $this->post('business_type');
        $info->article_id = $this->post('article_id');
        $info->mark = $this->post('mark');
        $info->uid = Yii::app()->admin_user->id;
        if ($this->post('online_date') == '') $this->msg(array('state' => 0, 'msgwords' => '未填上线日期！'));
        if ($this->post('del_date') == '') $this->msg(array('state' => 0, 'msgwords' => '未填删除日期！'));

        $info->del_date = strtotime($this->post('del_date'));
        $info->online_date = strtotime($this->post('online_date'));

        $articleInfo = MaterialArticleTemplate::model()->findByPk($info->article_id);
        $info->article_code = $articleInfo->article_code;
        $info->cat_id = MaterialArticleGroup::model()->findByPk($articleInfo->group_id)->cat_id;
        $info->article_title = $articleInfo->article_title;
        $info->article_info = $articleInfo->article_info;
        $info->article_type = $articleInfo->article_type;
        $info->create_time = time();
        $dbresult = $info->save();
        $id = $info->primaryKey;
        $msgarr = array('state' => 1, 'jscode' => "<script>setTimeout(function(){artDialog.close();}, 500)</script>");  //新增的话跳转会添加的页面
        $logs = "新增了图文（" . $article_id . "）的删除信息：" . $id;
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
     * 修改选中图片分组
     * author: yjh
     */
    public function actionChangeArticlesGroup()
    {
        $page = $info = array();
        $ids = $this->get('ids');
        $idArr = explode(',', $ids);
        foreach ($idArr as $val) {
            $info[] = $this->toArr(MaterialArticleTemplate::model()->findByPk($val));
        }
        // var_dump($info);die;
        if (!$_POST) {
            $page['info'] = $info;
            $page['ids'] = $ids;
            $this->render('changeArticlesGroup', array('page' => $page));
            exit;
        }
        $suss_flag = 0;
        foreach ($idArr as $val) {
            $info = MaterialArticleTemplate::model()->findByPk($val);
            if ($info->group_id == $this->post('group_id')) {
                $this->msg(array('state' => 0, 'msgwords' => '你选择了相同组别！'));
                $suss_flag = 1;
                break;
            }
            $info->group_id = $this->post('group_id');
            $info->article_code = MaterialArticleGroup::model()->createArticleCode($info->group_id);
            $info->save();
        }
        if ($suss_flag == 0) {
            $msgarr = array('state' => 1, 'jscode' => "<script>setTimeout(function(){artDialog.close();}, 500)</script>");  //新增的话跳转会添加的页面

            $logs = "将多个图文（$ids）移动到新的组别";
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 删除图片组别
     * 注意：同时将其组别下的所有图片还原成未分组：group_Id=1
     * author: yjh
     */
    public function actionDeleteArticleGroup()
    {
        $id = $this->get('id');
        $info = $this->toArr(MaterialArticleTemplate::model()->findAll('group_id = :group_id', array(':group_id' => $id)));
        if (count($info) > 0) {
            $this->msg(array('state' => 0, 'msgwords' => '组别内有文案不能删除组别！'));
        }
        $m = MaterialArticleGroup::model()->findByPk($id);
        $group_name = $m->group_name;
        $m->delete();
        $this->logs("删除了图文组别：" . $group_name);
        $this->msg(array('state' => 1, 'msgwords' => '删除图文组别【' . $group_name . '】成功！'));
    }

    /**
     * 图文另存为缓存数据
     * author: yjh
     */
    public function actionSaveTempData()
    {
        $m = MaterialTempArticle::model()->findAll();
        if ($m) {
            foreach ($m as $v) {
                $v->delete();
            }
        }

        $weChat = $this->post('weChat_id');
        if ($weChat == null) {
            $info = new MaterialTempArticle();
            $info->article_type = $this->post('article_type');
            $info->tag = $this->post('tag');
            $info->cat_id = $this->post('cat_id');
            $info->psq_id = $this->post('psq_id');
            $info->content = $this->post('info_body');
            $info->top_img = $this->post('top_img');
            $info->top_text = $this->post('top_text');
            $info->cover_url = $this->post('cover_url');
            $info->idintity = $this->post('idintity');
            $info->top_color = $this->post('top_color');
            $info->avater_img = $this->post('avater_img');
            $info->xingxiang = $this->post('xingxiang');
            $info->first_audio = $this->post('first_audio');
            $info->third_audio = $this->post('third_audio');
            $info->level_tag = $this->post('level_tag') ? 1 : 0;
            $info->avater_tag = $this->post('avater_tag') ? 1 : 0;
            $info->second_audio = $this->post('second_audio');
            $info->addfans_type = $this->post('addfans_type');
            $info->addfans_text = $this->post('addfans_text');
            $info->suspension_text = $this->post('suspension_text');
            $info->support_staff_id = $this->post('support_staff_id');
            $info->review_id = $this->post('review_id');
            $info->bottom_type = $this->post('bottom_type');
            $info->group_id = $this->post('group_id');
            $info->article_info = $this->post('article_info');
            $info->is_vote = $this->post('is_vote');
            $info->pop_time = $this->post('pop_time');
            $info->char_intro = $this->post('char_intro');
            $info->chat_content = $this->post('chat_content');
            if ($info->is_vote == 1) {
                $info->vote_id = $this->post('vote_id');
                if ($info->vote_id == '') {
                    echo json_encode(array('state' => 0, 'msgwords' => '投票页未选！'));
                    exit;
                }
            }
            if ($info->group_id == '') {
                echo json_encode(array('state' => 0, 'msgwords' => '未选择组别！'));
                exit;
            }
            if ($info->support_staff_id == '') {
                echo json_encode(array('state' => 0, 'msgwords' => '未选择支持人员'));
                exit;
            }
            if ($info->content == '') {
                echo json_encode(array('state' => 0, 'msgwords' => '文章内容为空'));
                exit;
            }
            if ($info->cat_id == '') {
                echo json_encode(array('state' => 0, 'msgwords' => '未选择商品类型'));
                exit;
            }
            if ($info->pop_time === '') $info->pop_time = -1;
            if ($info->pop_time >= 0) {
                if ($info->char_intro == '') $this->msg(array('state' => 0, 'msgwords' => '未填写人物介绍'));
                if ($info->chat_content == '') $this->msg(array('state' => 0, 'msgwords' => '未填写聊天内容'));
            }
            if ($info->article_type == 0) {

                $info->cover_url = $this->post('cover_url');
                if ($info->bottom_type == 0) {//标准样式
                    if ($info->suspension_text == '') {
                        echo json_encode(array('state' => 0, 'msgwords' => '固定悬浮描述未填写'));
                        exit;
                    }
                } elseif ($info->bottom_type == 1) {//头像样式
                    if ($info->avater_img == '') {
                        echo json_encode(array('state' => 0, 'msgwords' => '头像未添加'));
                        exit;
                    }
                    if ($info->suspension_text == '') {
                        echo json_encode(array('state' => 0, 'msgwords' => '固定悬浮描述未填写'));
                        exit;
                    }
                }
            } elseif ($info->article_type == 1) {
                $info->cover_url = $this->post('cover_url');
                if ($info->top_img == '') {
                    echo json_encode(array('state' => 0, 'msgwords' => '顶部背景图未添加'));
                    exit;
                }
                if ($info->avater_img == '') {
                    echo json_encode(array('state' => 0, 'msgwords' => '头像未添加'));
                    exit;
                }
                if ($info->idintity == '') {
                    echo json_encode(array('state' => 0, 'msgwords' => '身份未填写'));
                    exit;
                }
                if ($info->suspension_text == '') {
                    echo json_encode(array('state' => 0, 'msgwords' => '固定悬浮描述未填写'));
                    exit;
                }
                if ($info->top_text == '') {
                    echo json_encode(array('state' => 0, 'msgwords' => '顶部按钮描述未填写'));
                    exit;
                }

            } elseif ($info->article_type == 2) {
                $info->cover_url = $this->post('cover_url');
                if ($info->idintity == '') {
                    echo json_encode(array('state' => 0, 'msgwords' => '时间未填写'));
                    exit;
                }
            }
            $info->save();
        } else {
            //主页面
            if ($this->get('main_title') == '') {
                echo json_encode(array('state' => 0, 'msgwords' => '主页面图文标题未填写'));
                exit;
            }
            $group_id = $this->get('group_id');

            if ($group_id == '') {
                echo json_encode(array('state' => 0, 'msgwords' => '未选择所在组别'));
                exit;
            }
            $article_code = MaterialArticleGroup::model()->createArticleCode($group_id);
            $thumb_up = $this->get('thumb_up') ? $this->get('thumb_up') : "";
            $read_num = $this->get('read_num') ? $this->get('read_num') : "";
            $author = $this->get('author') ? $this->get('author') : "";

            if ($this->get('support_staff_id') == '') {
                echo json_encode(array('state' => 0, 'msgwords' => '未选择支持人员'));
                exit;
            }
            if ($this->get('info_body1') == '') {
                echo json_encode(array('state' => 0, 'msgwords' => '主页面正文内容未填写'));
                exit;
            }
            if ($this->get('cat_id') == '') {
                echo json_encode(array('state' => 0, 'msgwords' => '未选择商品类型'));
                exit;
            }
            if (is_int($thumb_up) && $thumb_up) {
                echo json_encode(array('state' => 0, 'msgwords' => '点赞数不是数字'));
                exit;
            }
            if (is_int($read_num && $author)) {
                echo json_encode(array('state' => 0, 'msgwords' => '阅读数不是数字'));
                exit;
            }

            //返回页面
            if ($this->get('rtn_Title') == '') {
                echo json_encode(array('state' => 0, 'msgwords' => '返页面图文标题未填写'));
                exit;
            }
            if ($this->get('info_body2') == '') {
                echo json_encode(array('state' => 0, 'msgwords' => '返回页面正文内容未填写'));
                exit;
            }

            //作者页面
            if ($this->get('au_title') == '') {
                echo json_encode(array('state' => 0, 'msgwords' => '作者页面图文标题未填写'));
                exit;
            }
            if ($this->get('info_body4') == '') {
                echo json_encode(array('state' => 0, 'msgwords' => '作者页面正文内容未填写'));
                exit;
            }

            //阅读页面
            if ($this->get('read_title') == '') {
                echo json_encode(array('state' => 0, 'msgwords' => '阅读页面图文标题未填写'));
                exit;
            }

            if ($this->get('info_body3') == '') {
                echo json_encode(array('state' => 0, 'msgwords' => '阅读页面正文内容未填写'));
                exit;
            }

            //发布日期
            $release_date = helper::getReleaseDate($this->get('release_date'),$this->get('release_date1'));;

            //主页面
            $info = new MaterialTempArticle();
            $info->article_title = $this->get('main_title');
            $info->idintity = $group_id;
            $info->support_staff_id = $this->get('support_staff_id');
            $info->cat_id = $this->get('cat_id');
            $content = $this->get('info_body1');
            $content = stripslashes(str_replace("class=\"lazy\" src", "class=\"lazy\" data-original", $content));
            $info->content = $content;
            $info->cover_url = $this->get('cover_url1');
            $info->top_img = $this->get('cover_url1');
            $info->tag = $this->get('tag_1');
            $info->xingxiang = $this->get('xingxiang');
            $info->article_info = $this->get('article_info');
            $info->suspension_text = $author;
            $info->first_audio = $thumb_up;
            $info->second_audio = $read_num;
            $info->is_hide_title = $this->get('is_rtn');
            $info->is_fill = $this->get('is_au_article');
            $info->article_code = $article_code;
            $info->article_type = 3;
            $info->is_main_page = 1;
            $info->descriptive_statement = $this->get('descriptive_statement');
            $info->release_date = $release_date;
            $info->update_time = time();
            $info->create_time = time();
            $info->save();

            //返回页面
            $info = new MaterialTempArticle();
            $info->article_title = $this->get('rtn_Title');
            $content = $this->get('info_body2');
            $content = stripslashes(str_replace("class=\"lazy\" src", "class=\"lazy\" data-original", $content));
            $info->content = $content;
            $info->suspension_text = $author;
            $info->cover_url = $this->get('cover_url2');
            $info->xingxiang = $this->get('xingxiang');
            $info->tag = $this->get('tag_2');
            $info->first_audio = $thumb_up;
            $info->second_audio = $read_num;
            $info->link = trim($this->get('link1'));
            $info->article_code = $article_code;
            $info->article_type = 3;
            $info->is_main_page = 21;
            $info->release_date =$release_date;
            $info->update_time = time();
            $info->create_time = time();
            $info->save();

            //作者页面
            $info = new MaterialTempArticle();
            $info->article_title = $this->get('au_title');
            $content = $this->get('info_body3');
            $content = stripslashes(str_replace("class=\"lazy\" src", "class=\"lazy\" data-original", $content));
            $info->content = $content;
            $info->suspension_text = $author;
            $info->first_audio = $thumb_up;
            $info->second_audio = $read_num;
            $info->cover_url = $this->get('cover_url3');
            $info->xingxiang = $this->get('xingxiang');
            $info->tag = $this->get('tag_3');
            $info->link = trim($this->get('link2'));
            $info->article_code = $article_code;
            $info->article_type = 3;
            $info->is_main_page = 22;
            $info->release_date = $release_date;
            $info->update_time = time();
            $info->create_time = time();
            $info->save();

            //阅读页面
            $info = new MaterialTempArticle();
            $info->article_title = $this->get('read_title');
            $content = $this->get('info_body4');
            $content = stripslashes(str_replace("class=\"lazy\" src", "class=\"lazy\" data-original", $content));
            $info->content = $content;
            $info->suspension_text = $author;
            $info->cover_url = $this->get('cover_url4');
            $info->xingxiang = $this->get('xingxiang');
            $info->tag = $this->get('tag_4');
            $info->link = trim($this->get('link3'));
            $info->first_audio = $thumb_up;
            $info->second_audio = $read_num;
            $info->article_code = $article_code;
            $info->article_type = 3;
            $info->is_main_page = 23;
            $info->release_date = $release_date;
            $info->update_time = time();
            $info->create_time = time();
            $info->save();
        }

        $id = $info->primaryKey;
        echo json_encode(array('state' => 1, 'id' => $id, 'msgwords' => '成功'));
        exit;
    }

    /**
     * 图文另存为
     * author: yjh
     */
    public function actionSaveAs()
    {
        $id = $this->get('id');

        if (!$id) {
            $this->msg(array('state' => 0, 'msgwords' => '未传id！'));
        }
        if (!$_POST) {
            $page['id'] = $id;
            $this->render('saveAs', array('page' => $page));
            exit;
        }

        $info = new MaterialArticleTemplate();
        $info->article_title = $this->post('article_title');
        if ($info->article_title == '') {
            $this->msg(array('state' => 0, 'msgwords' => '未填写标题！'));
        }
        $m = MaterialTempArticle::model()->findByPk($id);
        if($m['article_type'] != 3){
            $info->article_type = $m->article_type;
            $info->tag = $m->tag;
            $info->cat_id = $m->cat_id;
            $info->psq_id = $m->psq_id;
            $info->content = $m->content;
            $info->top_img = $m->top_img;
            $info->top_text = $m->top_text;
            $info->cover_url = $m->cover_url;
            $info->idintity = $m->idintity;
            $info->top_color = $m->top_color;
            $info->avater_img = $m->avater_img;
            $info->xingxiang = $m->xingxiang;
            $info->first_audio = $m->first_audio;
            $info->third_audio = $m->third_audio;
            $info->level_tag = $m->level_tag;
            $info->avater_tag = $m->avater_tag;
            $info->second_audio = $m->second_audio;
            $info->addfans_type = $m->addfans_type;
            $info->addfans_text = $m->addfans_text;
            $info->suspension_text = $m->suspension_text;
            $info->support_staff_id = $m->support_staff_id;
            $info->review_id = $m->review_id;
            $info->bottom_type = $m->bottom_type;
            $info->group_id = $m->group_id;
            $info->article_info = $m->article_info;
            $info->is_vote = $m->is_vote;
            $info->vote_id = $m->vote_id;
            $info->is_order = $m->is_order;
            $info->order_id = $m->order_id;
            if ($info->order_id == 0) $info->order_id = 0;
            if ($info->is_vote == 0) $info->vote_id = 0;
            $info->pop_time = $m->pop_time;//弹出聊天框时间
            $info->char_intro = $m->char_intro;//人物介绍
            $info->chat_content = $m->chat_content;//聊天内容
            if ($info->pop_time === '') $info->pop_time = -1;
            $info->article_code = MaterialArticleGroup::model()->createArticleCode($info->group_id);//
            $info->release_date = $m->release_date;
            $info->update_time = time();
            $info->create_time = time();
            $dbresult = $info->save();
            $m->delete();
            $id = $info->primaryKey;
            $url = $this->createUrl('material/index') . '?type=1&gid=' . $info->group_id;
        }else{
            for($i=3;$i>=0;$i--) {
                $n = $id - $i;
                $m = MaterialTempArticle::model()->findByPk($n);
                $info = new MaterialArticleTemplate();
                if(3 == $i){
                    $info->article_title = $this->post('article_title');
                }else{
                    $info->article_title = $m->article_title;
                }
                $info->content = $m->content;
                $info->cover_url = $m->cover_url;
                $info->tag = $m->tag;
                $info->suspension_text = $m->suspension_text;
                $info->review_id =  $m->review_id;
                $info->cat_id =  $m->cat_id;
                $info->update_time = time();
                $info->create_time = time();
                $info->support_staff_id = $m->support_staff_id;
                $info->article_code =  $m->article_code;
                $info->article_type = $m->article_type;
                $info->top_img = $m->top_img;
                $info->avater_img = $m->avater_img;
                $info->idintity = null;
                $info->first_audio = $m->first_audio;
                $info->second_audio = $m->second_audio;
                $info->third_audio = $m->third_audio;
                $info->top_text =  $m->top_text;
                $info->addfans_type = $m->addfans_type;
                $info->addfans_text =  $m->addfans_text;
                $info->psq_id =  $m->psq_id;
                $info->top_color =  $m->top_color;
                $info->avater_tag =  $m->avater_tag;
                $info->level_tag =  $m->level_tag;
                $info->bottom_type =  $m->bottom_type;
                $info->group_id = $m->idintity;
                $info->article_info = $m->article_info;
                $info->xingxiang =  $m->xingxiang;
                $info->is_vote = $m->is_vote;
                $info->vote_id =  $m->vote_id;
                $info->order_id = $m->order_id;
                $info->is_order =  $m->is_order;
                $info->payment = $m->payment;
                $info->pop_time =  $m->pop_time;
                $info->char_intro =  $m->char_intro;
                $info->chat_content =  $m->chat_content;
                $info->is_hide_title = $m->is_hide_title;
                $info->is_fill =  $m->is_fill;
                $info->is_main_page =  $m->is_main_page;
                $info->link = $m->link;
                $info->descriptive_statement = $m->descriptive_statement;
                $info->release_date = $m->release_date;
                $info->save();
                $m->delete();
            }
            $id = $info->primaryKey;
            $url = $this->createUrl('material/index') . '?type=1&gid=' . $info->group_id.'&is_main_page='.$info->is_main_page;
        }

        $msgarr = array('state' => 1, 'jscode' => "<script>setTimeout(function(){art.dialog.open.origin.location.href='" . $url . "';artDialog.close();}, 200);</script>");  //新增的话跳转会添加的页面
        $logs = "另存了新的图文模板ID：$id" . $info->article_code;
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
     * 获取投票页数据
     * author: yjh
     */
    public function actionGetVotePage()
    {
        if ($this->get('search_type') == 'keys' && $this->get('search_txt')) {
            $where = " and vote_page like '%" . $this->get('search_txt') . "%' ";
        }

        $sql = "select * from material_questionnaire where is_vote=1 " . $where;
        $temp_data = Yii::app()->db->createCommand($sql)->queryAll();
        if (isset($_GET['jsoncallback'])) {
            $data['list'] = $temp_data;
            $this->msg(array('state' => 1, 'data' => $data, 'type' => 'jsonp'));
        }
    }

    /**
     * 获取图片组别
     * author: yjh
     */
    public function actionGetPicGroups()
    {
        if ($this->get('search_type') == 'keys' && $this->get('search_txt')) {
            $where = " AND group_name like '%" . $this->get('search_txt') . "%' ";
        }

        $sql = "select * from material_pic_group where 1 " . $where;
        $temp_data = Yii::app()->db->createCommand($sql)->queryAll();
        if (isset($_GET['jsoncallback'])) {
            $data['list'] = $temp_data;
            $this->msg(array('state' => 1, 'data' => $data, 'type' => 'jsonp'));
        }
    }

    /**
     * 获取评论（普通+精选）
     * author: yjh
     */
    public function actionGetReviewList()
    {
        $where = '';
        if ($this->get('search_type') == 'keys' && $this->get('search_txt')) {
            $where = " and review_title like '%" . $this->get('search_txt') . "%' ";
        }


        $temp_data = $this->toArr(MaterialReview::model()->getReviewList($where));
        foreach ($temp_data as $k => $v) {
            if ($v['review_type'] == 2) {
                $temp_data[$k]['reviewType'] = '精选留言';
            } else {
                $temp_data[$k]['reviewType'] = Linkage::model()->get_name($v['review_type']);
            }
        }
        if (isset($_GET['jsoncallback'])) {
            $data['list'] = $temp_data;
            $this->msg(array('state' => 1, 'data' => $data, 'type' => 'jsonp'));
        }
    }

    /**
     * 添加领取人数样式插件
     * author: yjh
     */
    public function actionAddReceiveStyle()
    {
        $page = array();
        $num = $this->get('num') ? $this->get('num') : null;
        $this->render('addReceiveStyle', array('page' => $page, 'num' => $num));
    }

}