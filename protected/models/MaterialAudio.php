<?php

/**
 * 素材音频
 * Created by PhpStorm.
 * User: yjh
 * Date: 2017/2/23
 * Time: 14:18
 */
class MaterialAudio extends CActiveRecord
{
    public function tableName()
    {
        return '{{material_audio}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * 删除多余的音频
     * author: yjh
     */
    public function delExcessAudios()
    {
        $mAudioNum = $this::model()->count();
        $rAudioNum = Resource::model()->count("fromid='audio'");
        if ($mAudioNum == $rAudioNum) {
            return true;
        }
        $i=0;
        $excessNum = $rAudioNum-$mAudioNum;
        $audioList = Resource::model()->findAll("fromid='audio' order by resource_id desc");
        foreach ($audioList as $value){
            $audio_id = $value->resource_id;
            $result = $this->model()->find("audio_id=$audio_id");
            if($result) continue;
            $i=$i+1;
            $audio_url = Yii::app()->basePath . '/..' . $value->resource_url;
            if (file_exists($audio_url))
            {
                try {
                    unlink($audio_url);
                } catch (Exception $e) {
                    print_r($e->getMessage());
                    echo '删除语音资源失败！';
                }
            }
            Resource::model()->findByPk($audio_id)->delete();
            if($i == $excessNum) break;
        }
        return true;
    }

    /**
     * 获取语音列表
     * @return array|mixed|null
     * author: yjh
     */
    public function getAudioList(){
        $data = $this->findAll();
        return $data;
    }

    /**
     * 获取音频地址及长度
     * @param $pk
     * @return array
     * author: yjh
     */
    public function getUrlByPk($pk){
        $data=array();
        $result=Dtable::toArr($this->model()->findAllByPk($pk));
        $data['time']=$result[0]['play_time'];
        $data['url']=Resource::model()->findByPk($result[0]['audio_id'])->resource_url;
        return $data;
    }

}