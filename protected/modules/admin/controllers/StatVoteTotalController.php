<?php
/**
 * Created by PhpStorm.
 * User: fang
 * Date: 2017/02/28
 * Time: 14:40
 */
class StatVoteTotalController extends AdminController{
    public function actionIndex(){

        $params['where'] = '';
        $params['join'] = "  left join promotion_manage as b on a.promotion_id=b.id ";
        $params['select'] = " *,b.id as pid,a.id as aid ";
        $params['order'] = "  order by a.id desc    ";
        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['pagesize'] = 24;
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(StatVoteTotal::model()->tableName())->listdata($params);
        $this->render('index', array('page' => $page));
    }
    /**
     * 删除问卷统计
     * author: fang
     */
    public function actionDelete()
    {
        $id = $this->get('id');
        if ($id == '') $this->msg(array('state' => 0, 'msgwords' => '选择删除的问卷统计'));
        $info = StatVoteTotal::model()->findByPk($id);
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '问卷统计不存在'));
        }
        $info->delete();
        $this->msg(array('state' => 1, 'msgwords' => '删除问卷统计成功！'));
    }

}