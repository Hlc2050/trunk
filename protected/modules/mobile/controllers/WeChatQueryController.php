<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/27
 * Time: 18:12
 */
class WeChatQueryController extends AdminController{
    public function actionIndex(){
        $this->render('index');
    }

    public function actionSearch(){
        $arr = explode("\n",$_POST['content']);
        $arr_num = count($arr);
        $temp = array();
        $ret = array();
        $sql = 'select a.wechat_id,a.promotion_staff_id,b.csname_true from wechat as a left join cservice as b on b.csno=a.promotion_staff_id';
        $data = Yii::app()->db->createCommand($sql)->queryAll();
        foreach ($data as $val){
            $temp[$val['wechat_id']] =  $val['csname_true'];
        }

        for ($i=0;$i<$arr_num;$i++){
            if(array_key_exists(trim($arr[$i]),$temp)){
                $ret[]= array('id'=>$arr[$i],'name'=>$temp[trim($arr[$i])]);
            }elseif(trim($arr[$i]) == ''){
                continue;
            }else {
               $this->mobileMsg(array('status' => 0, 'content' => '第'.($i+1).'行微信号不存在'));
            }
        }

        $this->render('index',array('page'=>$ret));
    }
}