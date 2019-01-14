<?php
/**
 * 小程序调用接口
 * Class MiniProgramsApiController
 */

class MiniProgramsApiController extends HomeController
{


    public function actionIndex()
    {

    }

    /**
     * 获取文章信息
     * 下一步骤：加Redis
     * author: yjh
     */
    public function actionGetInfo()
    {
        $id = $this->get('id');
        if (!$id) {
            helper::json(10040);
        }
        $ret = array();
        //不是上线状态的数据不能获取数据
        //先从redis中获取，没有再去数据库中找
        $redis_flag = Yii::app()->params['basic']['is_redis'];
        if ($redis_flag == 1){
            $info = Yii::app()->redis->getValue('mininAppsInfo:id:' . $id);
            if(!$info){
                $TTL = Yii::app()->params['miniApps']['redis_time'];//缓存时长
                $info = $this->toArr(MiniAppsManage::model()->find('status=2 and id=' . $id));
                Yii::app()->redis->setValue('mininAppsInfo:id:' . $id, $info, $TTL);
            }
        }else{
            $info = $this->toArr(MiniAppsManage::model()->find('status=2 and id=' . $id));
        }
        if ($info) {
            $ret['page_one']['content'] = $info['content_one'];
            $ret['page_one']['is_show'] = $info['is_consult_one'];
            $ret['page_two']['content'] = $info['content_two'];
            $ret['page_two']['is_show'] = $info['is_consult_two'];
            $ret['page_three']['content'] = $info['content_three'];
            $ret['page_three']['is_show'] = $info['is_consult_three'];
            helper::json(0,$ret);
        } else {
            helper::json(10042);
        }

    }

    /**
     * 统计点击数
     * author: yjh
     */
    public function actionClicks(){
        $id = $this->get('id');
        if (!$id) {
            helper::json(10040);
        }
        $info = MiniAppsManage::model()->findByPk($id);
        if ($info) {
            $info->click_num =$info->click_num+1;
            $info->save();
            helper::json(0);
        } else {
            helper::json(10042);
        }
    }


}