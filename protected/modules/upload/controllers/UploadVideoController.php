
<?php

/**上传视频,音频均在这里处理
 * @auther yjh
 * @date 2014-5-23
 */
class UploadVideoController extends CController
{
    public function actionIndex()
    {
        if (isset($_REQUEST["name"])) {
            $fileName = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
            $fileName = $_FILES["file"]["name"];
        } else {
            $fileName = uniqid("file_");
        }
        $resource_type=0;
        $allowExts=array();
        if (isset($_REQUEST["fromid"]))
        {
            switch ($_REQUEST["fromid"]) {
                case 'video':
                    $domain_save_path = 'uploadfile/materialVideos/';   //域下的路径 ，前面不要加 /
                    $save_path = dirname(__FILE__) . '/../../../../uploadfile/materialVideos/';
                    $allowExts = array('mp4', 'mgp', 'm4v', 'rmvb', 'avi', 'mkv', 'wmv', 'mov', 'flv', '3gp');
                    $resource_type=3;
                    break;
                case 'audio':
                    $domain_save_path = 'uploadfile/materialAudios/';   //域下的路径 ，前面不要加 /
                    $save_path = dirname(__FILE__) . '/../../../../uploadfile/materialAudios/';
                    $allowExts = array('mp3','ogg');
                    $resource_type=4;
                    break;
                default:
                    $domain_save_path='uploadfile/'.date('Y/m/d').'/';   //域下的路径 ，前面不要加 /
                    $save_path=dirname(__FILE__).'/../../../../uploadfile/'.date('Y/m/d').'/';
                    break;
            }
        }else{
            $domain_save_path='uploadfile/'.date('Y/m/d').'/';   //域下的路径 ，前面不要加 /
            $save_path=dirname(__FILE__).'/../../../../uploadfile/'.date('Y/m/d').'/';
        }

        $upload_config = array();
        $upload_config['savename'] = $fileName;//名称
        $upload_config['savePath'] = $save_path;//保存路径
        $upload_config['maxSize'] = 20 * 1024 * 1024;   //允许上传最大为 20M
        $upload_config['allowExts'] = $allowExts;


        //判断图片保存文件夹是否存在，不存在则创建
        if (!is_dir($upload_config['savePath'])) helper::mkdirs($upload_config['savePath']);
        //开始上传
        try {
            $upload = new UploadFile($upload_config);
        } catch (Exception $e) {
            print_r($e->getMessage());
            echo 'haha';
        }
        //返回结果
        if (!$upload->upload()) {
            $result = $upload->getErrorMsg();
        } else {
            $result = $upload->getUploadFileInfo();
        }

        //拼装回调参数
        $files=array();
        $result=is_array($result)?$result:array();
        $upload_server_arr=Yii::app()->params['upload_server'];
        $upload_server=$upload_server_arr['s1'];

        foreach($result as $f){
            $resource_url=$file1['original']=$upload_server.$domain_save_path.$f['savename'];
            $r_size=$file1['size']=$f['size'];
            $r_width=$file1['width']=0;
            $r_height=$file1['height']=0;
            $r_oname = $file1['oname']=substr($f['name'],0,strrpos($f['name'],'.'));
            if (isset($_REQUEST["fromid"])) {
                $fromid = $_REQUEST["fromid"];
            }else $fromid =0;
            array_push($files,$file1);

            include_once $_SERVER['DOCUMENT_ROOT'] . "/protected/models/Resource.php";
            $resourceArr = Dtable::toArr(Resource::model()->find(('r_name = :rname and r_size = :rsize and fromid = :fromid and resource_type = :resource_type'),array(':rname'=>$r_oname,'rsize'=>$r_size,':fromid'=>$fromid,':resource_type'=>$resource_type)));
            if(count($resourceArr) == 0) {
                //3.插入数据库
                $data = array();
                $data['resource_url'] = $resource_url;
                $data['fromid'] = $fromid;
                $data['resource_type'] = $resource_type;
                $data['r_width'] = $r_width;
                $data['r_height'] = $r_height;
                $data['r_size'] = $r_size;
                $data['r_name'] = $r_oname;
                $data['resource_order'] = 50;
                $sql = helper::get_sql('resource_list', 'insert', $data);
                Yii::app()->db->createCommand($sql)->execute();
                $insertNum = Yii::app()->db->getLastInsertID();

                if($_REQUEST["fromid"] == 'audio'){
                    include_once $_SERVER['DOCUMENT_ROOT'] . "/protected/extensions/getid3/getid3.php";
                    $getID3 = new getID3(); //实例化类
                    $ThisFileInfo = $getID3->analyze(dirname(__FILE__).'/../../../..'.$resource_url);//分析文件
                    $time = $ThisFileInfo['playtime_string']; //获取mp3的长度信息
                    die(json_encode(array('status'=>1,'msg'=>'上传成功！','result'=>$insertNum,'time'=>$time)));
                }
                die(json_encode(array('status'=>1,'msg'=>'上传成功！','result'=>$insertNum)));
            }else {
                unlink(dirname(__FILE__).'/../../../..'.$resource_url);
                die(json_encode(array('status'=>0,'msg'=>'视频已存在！')));
            }
        }
        //没得到数组，打印错误信息
        if(count($files)<=0){
            die(json_encode(array('status'=>0,'msg'=>'上传失败！')));
        }


    }
}