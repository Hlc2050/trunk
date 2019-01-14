<?php

/**
 * 素材视频
 * Created by PhpStorm.
 * User: yjh
 * Date: 2017/2/23
 * Time: 14:18
 */
class MaterialVideo extends CActiveRecord
{
    public function tableName()
    {
        return '{{material_video}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * 删除多余的视频
     * author: yjh
     */
    public function delExcessVideos()
    {
        $mVideoNum = $this::model()->count();
        $rVideoNum = Resource::model()->count("fromid='video'");
        if ($mVideoNum == $rVideoNum) {
            return true;
        }
        $i=0;
        $excessNum = $rVideoNum-$mVideoNum;
        $videoList = Resource::model()->findAll("fromid='video' order by resource_id desc");
        foreach ($videoList as $value){
            $video_id = $value->resource_id;
            $result = $this->model()->find("video_id=$video_id");
            if($result) continue;
            $i=$i+1;
            $video_url = Yii::app()->basePath . '/..' . $value->resource_url;
            if (file_exists($video_url))
            {
                try {
                    unlink($video_url);
                } catch (Exception $e) {
                    print_r($e->getMessage());
                    echo '删除视频资源失败！';
                }
            }
            Resource::model()->findByPk($video_id)->delete();
            if($i == $excessNum) break;
        }
        return true;
    }

}