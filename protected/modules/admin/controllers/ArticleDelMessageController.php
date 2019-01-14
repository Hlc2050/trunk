<?php

/**
 * 文案删除统计
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/12/23
 * Time: 17:53
 */
class ArticleDelMessageController extends AdminController
{
    /**
     * 文案删除信息列表
     * author: yjh
     */
    public function actionIndex()
    {
        $params['where'] = '';
        if ($this->get('search_type') == 'article_title' && $this->get('search_txt')) {
            $params['where'] .= " and(a.article_title like '%" . $this->get('search_txt') . "%') ";
        } else if ($this->get('search_type') == 'article_code' && $this->get('search_txt')) {
            $params['where'] .= " and(a.article_code like '%" . $this->get('search_txt') . "%') ";
        }

        if ($this->get('start_time') && $this->get('end_time')) {
            $params['where'] .= " and(a.create_time between " . strtotime($this->get('start_time')) . " and ".strtotime($this->get('end_time')).") ";
        }elseif($this->get('start_time')){
            $params['where'] .= " and(a.create_time >= " . strtotime($this->get('start_time')) .") ";

        }elseif($this->get('end_time')){
            $params['where'] .= " and(a.create_time <= " . strtotime($this->get('end_time')) .") ";

        }
      
        if ($this->get('article_type') != '') {
            $params['where'] .= " and(article_type=" . intval($this->get('article_type')) . ") ";
        }
        //操作用户
        if ($this->get('user_id') != '') $params['where'] .= " and(a.uid = " . intval($this->get('user_id')) . ") ";
        //查看人员权限
        $result = $this->data_authority();
        if ($result != 0) {
            $params['where'] .= " and(f.sno in ($result)) ";
        }
        $params['order'] = "  order by id desc    ";
        $params['join'] = "  
                           LEFT JOIN channel as c ON c.id=a.channel_id 
                           LEFT JOIN partner as d ON d.id=a.partner_id
                           LEFT JOIN business_types as f ON f.bid=a.business_type
                           LEFT JOIN cservice as g ON g.csno=a.uid
                           LEFT JOIN linkage as b ON b.linkage_id=a.cat_id
                        
                           ";
        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['pagebar'] = 1;
        $params['select'] = "a.*,c.channel_name,c.channel_code,d.name,f.bname,g.csname_true,b.linkage_name";
        $page['listdata'] = Dtable::model('material_article_message')->listdata($params);
        $page['listdata']['url'] = urlencode('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"]);

        $this->render('index', array('page' => $page));

    }

    /**
     * 编辑图文删除信息
     * author: yjh
     */
    public function actionEdit(){
        $page = array();
        $id= $this->get('id');
        $info=MaterialArticleMessage::model()->findByPk($id);

        //显示表单
        if (!$_POST) {
            $page['info']=$this->toArr($info);
            $this->render('../material/addMessage', array('page' => $page));
            exit;
        }
        if ($this->post('online_date') == '') $this->msg(array('state' => 0, 'msgwords' => '未填上线日期！'));
        if ($this->post('del_date') == '') $this->msg(array('state' => 0, 'msgwords' => '未填删除日期！'));

        $info->del_date = strtotime($this->post('del_date'));
        $info->online_date = strtotime($this->post('online_date'));
        $info->mark = $this->post('mark');
        $dbresult = $info->save();
        $msgarr = array('state' => 1, 'jscode' => "<script>setTimeout(function(){artDialog.close();}, 500)</script>");  //新增的话跳转会添加的页面
        $logs = "修改了ID：（".$id."）的文案删除信息" ;
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
     * 批量删除信息
     * author: yjh
     */
    public function actionDelete(){
        $ids = $this->get('ids');
        $idArr = explode(',', $ids);
        foreach ($idArr as $val) {
            $info = MaterialArticleMessage::model()->findByPk($val);
            if($info) $info->delete();
        }
        $this->logs("已删除选择的文案信息" . $ids);
        //成功跳转提示
        $this->msg(array('state' => 1, 'msgwords' => "删除文案删除信息成功！"));
    }

    /**
     * 导出
     * author: yjh
     */
    public function actionExport(){
//         $this->createUrl('articleDelMessage/export') . '?search_type=' . $this->get('search_type') . '&user_id=' . $this->get('user_id') . '&start_time=' . $this->get('start_time') . '&end_time=' . $this->get('end_time') . '&article_type=' . $this->get('article_type') . '\'
        $params['where'] = '1';
        if ($this->get('search_type') == 'article_title' && $this->get('search_txt')) {
            $params['where'] .= " and(b.article_title like '%" . $this->get('search_txt') . "%') ";
        } else if ($this->get('search_type') == 'article_code' && $this->get('search_txt')) {
            $params['where'] .= " and(b.article_code like '%" . $this->get('search_txt') . "%') ";
        }
        if ($this->get('start_time') && $this->get('end_time')) {
            $params['where'] .= " and(a.create_time between " . strtotime($this->get('start_time')) . " and ".strtotime($this->get('end_time')).") ";
        }elseif($this->get('start_time')){
            $params['where'] .= " and(a.create_time >= " . strtotime($this->get('start_time')) .") ";

        }elseif($this->get('end_time')){
            $params['where'] .= " and(a.create_time <= " . strtotime($this->get('end_time')) .") ";

        }
        if ($this->get('article_type') != '') {
            $params['where'] .= " and(article_type=" . intval($this->get('article_type')) . ") ";
        }
        //操作用户
        if ($this->get('user_id') != '') $params['where'] .= " and(a.uid = " . intval($this->get('user_id')) . ") ";
        //查看人员权限
        $result = $this->data_authority();
        if ($result != 0) {
            $params['where'] .= " and(f.sno in ($result)) ";
        }
        $params['order'] = "  order by id desc    ";
        $params['join'] = "  
                           LEFT JOIN channel as c ON c.id=a.channel_id 
                           LEFT JOIN partner as d ON d.id=a.partner_id
                           LEFT JOIN business_types as f ON f.bid=a.business_type
                           LEFT JOIN cservice as g ON g.csno=a.uid
                           LEFT JOIN linkage as b ON b.linkage_id=a.cat_id
                        
                           ";
        $params['select'] = " a.*,c.channel_name,c.channel_code,d.name,f.bname,g.csname_true,b.linkage_name";
        $sql = "select". $params['select'] . " from material_article_message as a ". $params['join'] ."where ". $params['where'].$params['order'];
        $data = Yii::app()->db->createCommand($sql)->queryAll();

        $temp_array = array();
        $count = count($data);
        for($i = 0;$i<$count;$i++){
            $data[$i]['article_type'] = vars::get_field_str('article_types',$data[$i]['article_type']);
            $exist = helper::getTimeDiff($data[$i]['online_date'],$data[$i]['del_date']);
            $temp_array[$i] = array(
                $data[$i]['id'],//ID
                date('Y-m-d H:i',$data[$i]['online_date']),//上线日期
                $data[$i]['name'],//合作商
                $data[$i]['channel_name'],//渠道
                $data[$i]['bname'],//业务类型
                $data[$i]['article_code'],//文案编码
                $data[$i]['article_title'],//文案标题
                $data[$i]['article_type'],//文案类型
                $data[$i]['linkage_name'],//商品类型
                $data[$i]['csname_true'],//推广人员
                $data[$i]['article_info'],//文案备注
                date('Y-m-d H:i',$data[$i]['del_date']),//删除日期
                $exist,//存在时长
                date('Y-m-d H:i',$data[$i]['create_time']),//添加时间
                $data[$i]['mark'],//信息备注
            );
            foreach ($temp_array[$i] as $key => $value){
                $temp_array[$i][$key] = iconv('utf-8', 'gbk', $value);
            }
        }
        $headlist = array('ID','上线日期','合作商','渠道','业务类型','文案编码','文案标题','文案类型','商品类型','推广人员','文案备注','删除日期','存在时长','添加时间','信息备注');
        $file_name = '文案删除信息-'.date("Ymd");
        helper::downloadCsv($headlist,$temp_array,$file_name);
    }
}