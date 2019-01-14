<?php
/**
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/7
 * Time: 11:28
 */
//推广人员管理
class PromotionStaffController extends AdminController
{
    /**
     * 推广人员列表
     * author: yjh
     */
    public function actionIndex() {
        //搜索
        $params['where']='';
        if($this->get('search_type')=='keys' && $this->get('search_txt')){
            $params['where'] =" and(name like '%".$this->get('search_txt')."%') ";
        }else if($this->get('search_type')=='id'  && $this->get('search_txt')){
            $params['where'] =" and(id=".intval($this->get('search_txt')).") ";
        }
        $params['order']="  order by id desc    ";
        $params['pagesize']=Yii::app()->params['management']['pagesize'];
        $params['pagebar']=1;
        $params['smart_order']=1;
        $page['listdata']=Dtable::model(PromotionStaff::model()->tableName())->listdata($params);
        foreach ($page['listdata']['list'] as $key => $val) {
            $page['listdata']['list'][$key]['promotion_group_name'] = Linkage::model()->PromotionGroupById($val['promotion_group_id']);//推广小组名称
            $page['listdata']['list'][$key]['update_time'] = date('Y-m-d',$val['update_time']);//更新时间
        }
        $this->render('index',array('page'=>$page));
    }

    /**
     * 添加推广人员
     * author: yjh
     */
    public function actionAdd() {
        $page = array();
        //显示表单
        if (!$_POST) {
            $this->render('update',array('page'=>$page));
            exit;
        }
        //表单验证
        $info = new PromotionStaff();
        $info->name = $this->post('name');
        $info->user_id = $this->post('user_id');
        $info->promotion_group_id = $this->post('promotion_group_id');

        $resultByUId = PromotionStaff::model()->count('user_id=:user_id', array(':user_id'=>$info->user_id));
        if($info->user_id=='')$this->msg(array('state'=>0,'msgwords'=>'未选择关联用户'));
        if($resultByUId > 0) $this->msg(array('state'=>0,'msgwords'=>'此用户已是推广人员，请重新选择'));
        if($info->promotion_group_id=='')$this->msg(array('state'=>0,'msgwords'=>'未选择推广组别'));

        $info->name=AdminUser::model()->getUserNameByPK($info->user_id);
        $info->update_time = time();
        $info->create_time = time();
        $dbresult=$info->save();
        $id=$info->primaryKey;
        $msgarr = array('state' => 1, 'url' => $this->createUrl('promotionStaff/index') . '?p=' . $_GET['p'] . '');         $logs="添加了新的推广人员：".$info->name;
        if($dbresult===false){
            //错误返回
            $this->msg(array('state'=>0));
        }else{
            //新增和修改之后的动作
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 编辑推广人员
     * author: yjh
     */
    public function actionEdit() {
        $page = array();
        $id = $this->get('id');
        $info=PromotionStaff::model()->findByPk($id);
        if(!$info){
            $this->msg(array('state'=>0,'msgwords'=>'数据不存在'));
        }
        //显示表单
        if (!$_POST) {
            //如果有get.id为修改，否则判断为新增;
            $page['info']=$this->toArr($info);
            $this->render('update',array('page'=>$page));
            exit;
        }
        $info->name = $this->post('name');
        $info->user_id = $this->post('user_id');
        $info->promotion_group_id = $this->post('promotion_group_id');
        $resultByUId = PromotionStaff::model()->count('user_id=:user_id and id!=:id', array(':user_id'=>$info->user_id,':id'=>$id));
        if($info->user_id=='')$this->msg(array('state'=>0,'msgwords'=>'未选择关联用户'));
        if($resultByUId > 0) $this->msg(array('state'=>0,'msgwords'=>'此用户已是推广人员，请重新选择'));
        if($info->promotion_group_id=='')$this->msg(array('state'=>0,'msgwords'=>'未选择推广组别'));

        $info->name=AdminUser::model()->getUserNameByPK($info->user_id);
        $info->update_time = time();
        $dbresult=$info->save();
        $id=$info->primaryKey;
        $msgarr = array('state' => 1, 'url' => $this->createUrl('promotionStaff/index') . '?p=' . $_GET['p'] . ''); //保存的话，跳转到之前的列表
        $logs="修改了推广人员：".$info->name;
        if($dbresult===false){
            //错误返回
            $this->msg(array('state'=>0));
        }else{
            //新增和修改之后的动作
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 删除推广人员
     * author: yjh
     */
    public function actionDelete()
    {
        if ($this->get('id') == '') $this->msg(array('state' => 0, 'msgwords' => '未传入数据'));
        $id = $this->get('id');
        $user_id = $this->get('user_id');
        $info = PromotionStaff::model()->findByPk($id);
        if (!$info) $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        $wResult = $this->toArr(WeChat::model()->findAll('promotion_staff_id=:promotion_staff_id',array(':promotion_staff_id'=>$info->user_id)));
        if(count($wResult)>0) $this->msg(array('state' => 0, 'msgwords' => '此推广人员有与微信号绑定不能删除'));

        $m = PromotionStaff::model()->findByPk($id);
        $name = $m->name;
        $m->delete();
        PromotionUserRelation::model()->deleteAll('promotion_user_id='.$user_id);
        PromotionStaffRelation::model()->deleteAll('test_staff_id='.$user_id);

        $logs = "删除了推广人员：".$name;
        $this->logs($logs);
        $this->msg(array('state' => 1, 'msgwords' => '删除推广人员成功！'));
    }


}