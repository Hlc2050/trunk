<?php

/**
 * 文章分块
 * Created by PhpStorm.
 * User: yjh
 * Date: 2017/8/16
 * Time: 09:26
 */
class ArticleBlockController extends AdminController
{
    public function actionIndex(){
        $page=array();
        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['pagebar'] = 1;
        $params['select'] = "a.*";
        $page['listdata'] = Dtable::model(ArticleBlock::model()->tableName())->listdata($params);
        $page['listdata']['url'] = urlencode('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"]);
        $this->render('index', array('page' => $page));
        exit;
    }

    public function actionAdd(){
        $page=array();
        if (!$_POST) {
            $this->render('update', array('page' => $page));
            exit;
        }
        $info = new ArticleBlock();
        $info->block_name = $this->get('block_name');
        $info->blank_content = $this->get('blank_content');
        $block_content = $this->get('block_content');
        if($info->block_name=='') $this->msg(array('state' => 0, 'msgwords' => '文章名称未填写'));
        if($info->blank_content=='') $this->msg(array('state' => 0, 'msgwords' => '空白位置显示未填写内容'));
        if($block_content[0]=='') $this->msg(array('state' => 0, 'msgwords' => '分块一未填写内容'));
        if($block_content[1]=='') $this->msg(array('state' => 0, 'msgwords' => '分块二未填写内容'));
        if($block_content[2]=='') $this->msg(array('state' => 0, 'msgwords' => '分块三未填写内容'));
        $info->block_one = $block_content[0];
        $info->block_two = $block_content[1];
        $info->block_three = $block_content[2];
        $info->block_four = $block_content[3];
        $info->block_five = $block_content[4];
        $info->create_time = time();
        $dbresult = $info->save();
        $id = $info->primaryKey;
        $msgarr = array('state' => 1, 'url' => $this->createUrl('articleBlock/index') . '?p=' . $_GET['p'] . '');
        $logs = "添加了新的文章分块：$id 、" . $info->block_name;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }

    }
    public function actionEdit(){
        $page=array();
        $id = $this->get('id');
        $info = ArticleBlock::model()->findByPk($id);
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }
        if (!$_POST) {
            $page['info'] = $this->toArr($info);
            $this->render('update', array('page' => $page));
            exit;
        }
   
        $info->block_name = $this->get('block_name');
        $info->blank_content = $this->get('blank_content');
        $block_content = $this->get('block_content');
        if($info->block_name=='') $this->msg(array('state' => 0, 'msgwords' => '文章名称未填写'));
        if($info->blank_content=='') $this->msg(array('state' => 0, 'msgwords' => '空白位置显示未填写内容'));
        if($block_content[0]=='') $this->msg(array('state' => 0, 'msgwords' => '分块一未填写内容'));
        if($block_content[1]=='') $this->msg(array('state' => 0, 'msgwords' => '分块二未填写内容'));
        if($block_content[2]=='') $this->msg(array('state' => 0, 'msgwords' => '分块三未填写内容'));
        $info->block_one = $block_content[0];
        $info->block_two = $block_content[1];
        $info->block_three = $block_content[2];
        $info->block_four = $block_content[3];
        $info->block_five = $block_content[4];
        $info->update_time = time();
        $dbresult = $info->save();
        $id = $info->primaryKey;
        $msgarr = array('state' => 1, 'url' => $this->createUrl('articleBlock/index') . '?p=' . $_GET['p'] . '');
        $logs = "修改了文章分块：$id、" . $info->block_name;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    public function actionDelete(){
        if($this->get('id') =='')  $this->msg(array('state' => 0, 'msgwords' => '未传入数据'));
        $id = $this->get('id');
        $info = ArticleBlock::model()->findByPk($id);
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }

        $info->delete();
        $this->logs("删除了文章分块：".$info->block_name);
        $this->msg(array('state' => 1, 'msgwords' => '删除了文章分块：【'.$info->block_name.'】成功！'));
    }
}