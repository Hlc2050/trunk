<?php

/**
 * 总统计代码管理
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/12/23
 * Time: 17:53
 */
class CnzzCodeManageController extends AdminController
{
    /**
     * 总统计组别列表
     * author: yjh
     */
    public function actionIndex()
    {
        $params = array();
        $params['order'] = "  order by id desc    ";
        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['join'] = "  
                           LEFT JOIN domain_list as c ON c.cnzz_code_id=a.id 
                           ";
        $params['group']= " group by a.id";
        $params['select'] = "a.*,count(c.id) as domain_num";

        $page['listdata'] = Dtable::model('cnzz_code_manage')->listdata($params);

        $this->render('index', array('page' => $page));
    }

    /**
     * 新增总统计
     * author: yjh
     */
    public function actionAdd()
    {
        $page = array();

        //显示表单
        if (!$_POST) {
            $this->render('update', array('page' => $page));
            exit;
        }
        //表单验证
        $info = new CnzzCodeManage();
        $info->name = $this->post('name');
        $m = CnzzCodeManage::model()->find("name='".$this->post('name')."'");
        if($m)  $this->msg(array('state' => 0, 'msgwords' => '总统计代码名称重复了！'));
        $info->limit_num = 500;
        $info->total_cnzz = trim($this->post('total_cnzz'));
        $info->update_time = time();
        $info->create_time = time();
        $dbresult = $info->save();
        $id = $info->primaryKey;
        $msgarr = array('state' => 1, 'jscode' => "<script>setTimeout(function(){artDialog.close();}, 500)</script>");  //新增的话跳转会添加的页面
        $logs = "新增了总统计代码ID:$id-" . $info->name;
        if ($dbresult === false) {
            $this->msg(array('state' => 0));
        } else {
            //新增和修改之后的动作
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 修改总统计
     * author: yjh
     */
    public function actionEdit()
    {
        $page = array();
        $id= $this->get('id');
        $info=CnzzCodeManage::model()->findByPk($id);
        //显示表单
        if (!$_POST) {
            $page['info']=$this->toArr($info);
            $this->render('update', array('page' => $page));
            exit;
        }
        //表单验证
        $info->name = $this->post('name');
        $m = CnzzCodeManage::model()->find("id != $id and name='".$this->post('name')."'");
        if($m)  $this->msg(array('state' => 0, 'msgwords' => '总统计代码名称重复了！'));
        $info->limit_num = 500;
        $info->total_cnzz = $this->post('total_cnzz');
        $info->update_time = time();
        $dbresult = $info->save();
        $msgarr = array('state' => 1, 'jscode' => "<script>setTimeout(function(){artDialog.close();}, 500)</script>");  //新增的话跳转会添加的页面
        $logs = "修改了总统计代码ID:$id-" . $info->name;
        if ($dbresult === false) {
            $this->msg(array('state' => 0));
        } else {
            //新增和修改之后的动作
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 删除总统计
     * author: yjh
     */
    public function actionDelete(){
        $id= $this->get('id');
        $m = DomainList::model()->findAll('cnzz_code_id='.$id);
        if($m) $this->msg(array('state' => 0, 'msgwords' => '该组别有域名正在使用不能删除！'));
        $info=CnzzCodeManage::model()->findByPk($id);
        $info->delete();
        $this->logs("删除了总统计组别ID:" .$info->id." ". $info->name);
        //成功跳转提示
        $this->msg(array('state' => 1, 'msgwords' => "删除文案删除信息成功！"));
    }

}