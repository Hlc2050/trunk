<?php

/**
 * 上线素材管理
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/10
 * Time: 11:26
 */
class OnlineMaterialController extends AdminController
{
    /**
     * 上线素材列表
     * author: yjh
     */
    public function actionIndex()
    {
        $params['where'] = '';
        $params['where'] .= "and a.is_main_page=1";
        if ($this->get('search_type') == 'partner' && $this->get('search_txt')) {
            $params['where'] .= " and(p.name like '%" . $this->get('search_txt') . "%') ";
        } else if ($this->get('search_type') == 'channel' && $this->get('search_txt')) { //网点ID
            $params['where'] .= " and(c.channel_name like '%" . $this->get('search_txt') . "%') ";
        } else if ($this->get('search_type') == 'channel_code' && $this->get('search_txt')) {
            $params['where'] .= " and(c.channel_code like '%" . $this->get('search_txt') . "%') ";
        } else if ($this->get('search_type') == 'id' && $this->get('search_txt')) {
            $params['where'] .= " and(promotion_id=" . intval($this->get('search_txt')) . ") ";
        }
        if ($this->get('cat_id') != '') $params['where'] .= " and(a.cat_id=" . $this->get('cat_id') . ") ";
        if ($this->get('article_code') != '') $params['where'] .= " and(m.article_code like '%" . $this->get('article_code') . "%') ";
        if ($this->get('status') != '') {
            $params['where'] .= " and(b.status=" . $this->get('status') . ") ";
        }
        //推广类型
        if($this->get('promotion_type') !=''){
            $params['where'] .= " and( b.promotion_type = " . $this->get('promotion_type') . ") ";
        }
        //操作用户
        if ($this->get('user_id') != '') $params['where'] .= " and(a.promotion_staff_id = " . $this->get('user_id') . ") ";

        //查看人员权限
        $result = $this->data_authority(1);
        if ($result != 0) {
            $params['where'] .= " and(a.promotion_staff_id in ($result))";
        }
        $params['where'] .= " and b.line_type=0";
        $params['order'] = "  order by a.id desc    ";
        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['join'] = "
		left join promotion_manage as b on b.id=a.promotion_id
		left join material_article_template as m on m.id=a.origin_template_id
		left join partner as p on p.id=a.partner_id
		left join channel as c on c.id=a.channel_id
		left join linkage as l on l.linkage_id=a.cat_id
		left join cservice as n on n.csno=a.promotion_staff_id
		";
        $params['pagebar'] = 1;
        $params['select'] = "a.id,a.promotion_id,a.promotion_staff_id,a.is_main_page,a.article_title,a.article_type,a.update_time,a.promotion_staff_id,a.cat_id,m.article_code,c.channel_name,c.channel_code,b.promotion_type,p.name as partner_name,b.status,b.line_type,l.linkage_name,n.csname_true";

        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(OnlineMaterialManage::model()->tableName())->listdata($params);
        $page['listdata']['url'] = urlencode('http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"]);

        $this->render('index', array('page' => $page));
    }

    /**
     * 上线素材编辑
     * author: yjh
     */
    public function actionEdit()
    {
        $page = array();
        $id = $this->get('id');
        $weChat = $this->get('weChat_id');
        //判断图文类型是否为微信图文
        if ($weChat != null) {
            //主页面
            if ($this->get('main_title') == '') $this->msg(array('state' => 0, 'msgwords' => '主页面图文标题未填写'));
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
            $info = OnlineMaterialManage::model()->findByPk($weChat_id[0]);
            $promotion_id =  $info->promotion_id;
            $info->article_title = $this->get('main_title');
            $info->cat_id = $this->get('cat_id');
            $content = $this->get('info_body1');
            $content = stripslashes(str_replace("class=\"lazy\" src", "class=\"lazy\" data-original", $content));
            $info->content = $content;
            $info->cover_url = $this->get('cover_url1');
            $info->top_img = $this->get('cover_url1');
            $info->tag = $this->get('tag_1');
            $info->xingxiang = $this->get('xingxiang');
            $info->suspension_text = $author;
            $info->first_audio = $thumb_up;
            $info->second_audio = $read_num;
            $info->is_hide_title = $this->get('is_rtn');
            $info->is_fill = $this->get('is_au_article');
            $info->is_main_page = 1;
            $info->descriptive_statement = $this->get('descriptive_statement');
            $info->release_date =$release_date;
            $info->update_time = time();
            $info->save();

            //返回页面
            $info = OnlineMaterialManage::model()->findByPk($weChat_id[1]);
            $info->article_title = $this->get('rtn_Title');
            $content = $this->get('info_body2');
            $content = stripslashes(str_replace("class=\"lazy\" src", "class=\"lazy\" data-original", $content));
            $info->content = $content;
            $info->suspension_text = $author;
            $info->cover_url = $this->get('cover_url2');
            $info->xingxiang = $this->get('xingxiang');
            $info->first_audio = $thumb_up;
            $info->second_audio = $read_num;
            $info->link = trim($this->get('link1'));
            $info->tag = $this->get('tag_2');
            $info->is_main_page = 21;
            $info->release_date =$release_date;
            $info->update_time = time();
            $info->save();

            //作者页面
            $info = OnlineMaterialManage::model()->findByPk($weChat_id[2]);
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
            $info->is_main_page = 22;
            $info->release_date =$release_date;
            $info->update_time = time();
            $info->save();

            //阅读页面
            $info = OnlineMaterialManage::model()->findByPk($weChat_id[3]);
            $info->article_title = $this->get('read_title');
            $content = $this->get('info_body4');
            $content = stripslashes(str_replace("class=\"lazy\" src", "class=\"lazy\" data-original", $content));
            $info->content = $content;
            $info->suspension_text = $author;
            $info->first_audio = $thumb_up;
            $info->second_audio = $read_num;
            $info->cover_url = $this->get('cover_url4');
            $info->xingxiang = $this->get('xingxiang');
            $info->tag = $this->get('tag_4');
            $info->link = trim($this->get('link3'));
            $info->is_main_page = 23;
            $info->release_date =$release_date;
            $info->update_time = time();
            $info->save();
            $redis_flag = Yii::app()->params['basic']['is_redis'];
            if($redis_flag==1)
            {
                $ret = Yii::app()->redis->getValue('article:'.$promotion_id);
                if($ret)Yii::app()->redis->deleteValue('article:'.$promotion_id);
                $ret = Yii::app()->redis->getValue('promotion:'.$promotion_id);
                if($ret)Yii::app()->redis->deleteValue('promotion:'.$promotion_id);
                $ret = Yii::app()->redis->getValue('psq:'.$promotion_id);
                if($ret)Yii::app()->redis->deleteValue('psq:'.$promotion_id);
            }
            $url = $this->get('backurl');
            $this->msg(array('state' => 1, 'msgwords' => '修改成功', 'url' => $url));
        }else{
        $info = OnlineMaterialManage::model()->findByPk($id);
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }
        if (!$_POST) {
            $page['info'] = $this->toArr($info);
            $content=  str_replace("class=\"lazy\" data-original","class=\"lazy\" src",$page['info']['content']);
            $page['info']['content'] = $content;
            $this->render('update', array('page' => $page));
            exit;
        }
        $info->xingxiang =  $this->post('xingxiang');
        $content = $this->post('info_body');
        if ($content == '') $this->msg(array('state' => 0, 'msgwords' => '文章内容为空'));
        $content=  str_replace("class=\"lazy\" src","class=\"lazy\" data-original",$content);
        $info->content = $content;
        $info->cover_url = $this->post('cover_url');

        if($info->article_type==0) {
            $info->article_title = $this->post('articleTitle');
            $info->tag = $this->post('tag');
            $info->is_fill = $this->post('is_fill');
            $info->is_hide_title = $this->post('is_hide_title');
            $info->top_img = $this->post('top_img');
            $info->level_tag = $this->post('level_tag')?1:0;
            $info->suspension_text = $this->post('suspension_text');
            $info->cat_id = $this->post('cat_id');
            if($this->post('review_title')=='')  $info->review_id=0;
            else $info->review_id = $this->post('review_id');
            $info->bottom_type = $this->post('bottom_type');
            $info->avater_img = $this->post('avater_img');
            $info->addfans_text = $this->post('addfans_text');
            $info->vote_id = $this->post('vote_id');
            $info->is_vote = $this->post('is_vote');
            $info->descriptive_statement = $this->post('descriptive_statement');
            $info->release_date = helper::getReleaseDate($this->get('release_date'),$this->get('release_date1'));
            //问卷
            $info->psq_id = $this->post('psq_id');

            if( $info->is_vote==0)$info->vote_id=0;
            if($info->is_vote==1){
                if($info->vote_id=='')
                    $this->msg(array('state' => 0, 'msgwords' => '未选择投票页！'));
            }
            $info->order_id = $this->post('order_id');
            $info->is_order = $this->post('is_order');

            if( $info->is_order==0)$info->order_id=0;
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
            if ($info->article_title == '') $this->msg(array('state' => 0, 'msgwords' => '文章标题未填写'));
            if ($info->tag == '') $this->msg(array('state' => 0, 'msgwords' => '未填写浏览器Title'));
            if ($info->cat_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择推广类型'));
            if($info->bottom_type==0){//标准样式
                if ($info->suspension_text == '') $this->msg(array('state' => 0, 'msgwords' => '未填写悬浮描述'));
            }elseif ($info->bottom_type==1){//头像样式
                $info->addfans_type = $this->post('addfans_type');
                if ($info->avater_img == '') $this->msg(array('state' => 0, 'msgwords' => '头像未添加'));
                if ($info->suspension_text == '') $this->msg(array('state' => 0, 'msgwords' => '固定悬浮描述未填写'));
            }elseif ($info->bottom_type == 3){//标准样式2
                if ($info->suspension_text == '') $this->msg(array('state' => 0, 'msgwords' => '未填写悬浮描述'));
            }

        }elseif($info->article_type==1){
            $info->tag = $this->post('tag');
            $info->cat_id = $this->post('cat_id');
            $info->psq_id = $this->post('psq_id');
            $info->top_img = $this->post('top_img');
            $info->top_text = $this->post('top_text');
            $info->idintity = $this->post('idintity');
            $info->top_color = $this->post('top_color');
            $info->avater_img = $this->post('avater_img');
            $info->first_audio = $this->post('first_audio');
            $info->third_audio = $this->post('third_audio');
            $info->level_tag = $this->post('level_tag')?1:0;
            $info->avater_tag = $this->post('avater_tag')?1:0;
            $info->second_audio = $this->post('second_audio');
            $info->addfans_type = $this->post('addfans_type');
            $info->addfans_text = $this->post('addfans_text');
            $info->article_title = $this->post('articleTitle');
            $info->suspension_text = $this->post('suspension_text');
            $info->bottom_type = $this->post('bottom_type');
            $info->review_id = $this->post('review_id');
            $info->vote_id = $this->post('vote_page');
            $info->descriptive_statement = $this->post('descriptive_statement');

            if( $info->is_vote==0)$info->vote_id=0;
            if($info->is_vote==1){
                if($info->vote_id=='')
                    $this->msg(array('state' => 0, 'msgwords' => '未选择投票页！'));
            }
            if ($info->article_title == '') $this->msg(array('state' => 0, 'msgwords' => '文章标题未填写'));
            if ($info->support_staff_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择支持人员'));
            if ($info->top_img == '') $this->msg(array('state' => 0, 'msgwords' => '顶部背景图未添加'));
            if ($info->avater_img == '') $this->msg(array('state' => 0, 'msgwords' => '头像未添加'));
            if ($info->idintity == '') $this->msg(array('state' => 0, 'msgwords' => '身份未填写'));
            if ($info->cat_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择推广类型'));
            if($info->bottom_type==1){//头像
                if ($info->avater_img == '') $this->msg(array('state' => 0, 'msgwords' => '头像未添加'));
                if ($info->suspension_text == '') $this->msg(array('state' => 0, 'msgwords' => '固定悬浮描述未填写'));
            }
        }elseif($info->article_type==2){
            $info->tag = $this->post('tag');
            $info->cat_id = $this->post('cat_id');
            $info->top_img = $this->post('top_img');
            $info->top_text = $this->post('top_text');
            $info->idintity = $this->post('idintity');
            $info->article_title = $this->post('articleTitle');
            $info->suspension_text = $this->post('suspension_text');
            $info->review_id = $this->post('review_id');
            $info->bottom_type = $this->post('bottom_type');
            $info->avater_img = $this->post('avater_img');
            $info->addfans_type = $this->post('addfans_type');
            $info->addfans_text = $this->post('addfans_text');
            $info->level_tag = $this->post('level_tag')?1:0;
            $info->descriptive_statement = $this->post('descriptive_statement');

            if ($info->article_title == '') $this->msg(array('state' => 0, 'msgwords' => '文章标题未填写'));
            if ($info->support_staff_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择支持人员'));
//            if ($info->top_img == '') $this->msg(array('state' => 0, 'msgwords' => '顶部图片未添加'));
            if ($info->idintity == '') $this->msg(array('state' => 0, 'msgwords' => '时间未填写'));
            if ($info->top_text == '') $this->msg(array('state' => 0, 'msgwords' => '文章导航未填写'));
            if ($info->cat_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择推广类型'));
            if($info->bottom_type==1){//头像
                if ($info->avater_img == '') $this->msg(array('state' => 0, 'msgwords' => '头像未添加'));
                if ($info->suspension_text == '') $this->msg(array('state' => 0, 'msgwords' => '固定悬浮描述未填写'));
            }
        }

        $info->pop_time = $this->post('pop_time');//弹出聊天框时间
        $info->char_intro = $this->post('char_intro');//人物介绍
        $info->chat_content = $this->post('chat_content');//聊天内容

        if($info->pop_time==='')$info->pop_time=-1;
        if($info->pop_time>=0){
            if($info->char_intro == '')  $this->msg(array('state' => 0, 'msgwords' => '未填写人物介绍'));
            if($info->chat_content == '')  $this->msg(array('state' => 0, 'msgwords' => '未填写聊天内容'));
        }
        $info->update_time = time();
        $dbresult = $info->save();
        $redis_flag = Yii::app()->params['basic']['is_redis'];
        if($redis_flag==1)
        {
            $promotion_id =  $info->promotion_id;
            var_dump($promotion_id);
            $ret = Yii::app()->redis->getValue('article:'.$promotion_id);
            if($ret)Yii::app()->redis->deleteValue('article:'.$promotion_id);
            $ret = Yii::app()->redis->getValue('promotion:'.$promotion_id);
            if($ret)Yii::app()->redis->deleteValue('promotion:'.$promotion_id);
            $ret = Yii::app()->redis->getValue('psq:'.$promotion_id);
            if($ret)Yii::app()->redis->deleteValue('psq:'.$promotion_id);
        }
        $msgarr = array('state' => 1, 'url' => $this->get('backurl')); //保存的话，跳转到之前的列表
        $logs = "修改了上线素材：" . $info->article_title;
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
}