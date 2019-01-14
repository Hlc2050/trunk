<?php
/**
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/14
 * Time: 13:56
 */
Class BatchUploadImgsController extends CController
{
    public function actionIndex()
    {
        // Get a file name
        if (isset($_REQUEST["name"])) {
            $fileName = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
            $fileName = $_FILES["file"]["name"];
        } else {
            $fileName = uniqid("file_");
        }

        $fileSuffix = substr(strrchr($fileName, '.'), 1);
        $weChatId = basename($fileName, "." . $fileSuffix);
        include_once  $_SERVER['DOCUMENT_ROOT']."/protected/modules/admin/components/AdminController.php";
        $adminController = new AdminController(0);
        if (isset($_REQUEST["fromid"]))
        {
            switch ($_REQUEST["fromid"]){
                //二维码处理
                case 'qr':
                    include_once  $_SERVER['DOCUMENT_ROOT']."/protected/models/WeChat.php";
                    $weChatInfo = $adminController->toArr(WeChat::model()->find('wechat_id = :weChatId', array(':weChatId'=>$weChatId)));
                    if(count($weChatInfo) == 0)  die('匹配不到对应的微信号！未上传');
                    $domain_save_path = 'uploadfile/QRImgs/';   //域下的路径 ，前面不要加 /
                    $save_path = dirname(__FILE__) . '/../../../../uploadfile/QRImgs/';
                    break;
                //素材图片处理
                case 'material_pic':
                    $domain_save_path = 'uploadfile/materialImgs/';   //域下的路径 ，前面不要加 /
                    $save_path = dirname(__FILE__) . '/../../../../uploadfile/materialImgs/';
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
        $upload_config['savename'] = $fileName;//图片名称
        $upload_config['savePath'] = $save_path;//图片保存路径
        $upload_config['maxSize'] = 10 * 1024 * 1024;   //允许上传最大为 10M
        $upload_config['allowExts'] = array('gif', 'jpg', 'jpeg', 'bmp', 'png', 'JPG', 'GIF', 'JPEG', 'BMP', 'PNG');
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
        $result = array();
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
            $img=Image::getImageInfo($f['savepath'].$f['savename']); //图片信息
            //$resource_url=$file1['original']='http://'.$_SERVER['HTTP_HOST'].'/upload/'.substr($f['savepath'],2).$f['savename'];
            $resource_url=$file1['original']=$upload_server.$domain_save_path.$f['savename'];
            $r_size=$file1['size']=$f['size'];
            $r_width=$file1['width']=isset($img['width'])?$img['width']:0;
            $r_height=$file1['height']=isset($img['height'])?$img['height']:0;
            $r_oname = $file1['oname']=substr($f['name'],0,strrpos($f['name'],'.'));
            if (isset($_REQUEST["fromid"])) {
                $fromid = $_REQUEST["fromid"];
            }else $fromid =0;
            array_push($files,$file1);
            $resource_type = 1;
            include_once  $_SERVER['DOCUMENT_ROOT']."/protected/models/Resource.php";
            $resourceInfo = Resource::model()->find(('r_name = :rname  and fromid = :fromid'),array(':rname'=>$r_oname,':fromid'=>$fromid));
            $resourceArr = $adminController->toArr($resourceInfo);
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
                //如果是二维码上传，需要将图片上传至微信管理
                if (isset($_REQUEST["fromid"]) && $_REQUEST["fromid"] == 'qr') {
                    //将二维码地址存入微信号
                    $id = $weChatInfo['id'];
                    $info = WeChat::model()->findByPk($id);
                    $info->qrcode_id = $insertNum;
                    $info->save();
                }
                //素材管理
                if (isset($_REQUEST["fromid"]) && $_REQUEST["fromid"] == 'material_pic') {
                    include_once $_SERVER['DOCUMENT_ROOT'] . "/protected/models/MaterialPicRelation.php";
                    //保存素材图片
                    $info = new MaterialPicRelation();
                    $info->img_id = $insertNum;
                    $info->group_id = $_REQUEST["picGroupId"];//未分组
                    $info->name = $r_oname;
                    $info->create_time = time();
                    $info->save();
                }
            }else {
                if($fromid=='qr'){
                    $last_img= $resourceInfo->resource_url;
                    unlink(iconv('UTF-8', 'GB2312', dirname(__FILE__) . '/../../../..' . $last_img));
                    $resourceInfo->resource_url=$resource_url;
                    $resourceInfo->r_size=$r_size;
                    $resourceInfo->r_width=$r_width;
                    $resourceInfo->r_height=$r_height;
                    $resourceInfo->save();
                    //将二维码地址存入微信号
                    $id = $weChatInfo['id'];
                    $info = WeChat::model()->findByPk($id);
                    $info->qrcode_id = $resourceInfo->resource_id;
                    $info->save();
                    die('二维码已覆盖');
                }else {
                    unlink(iconv('UTF-8', 'GB2312', dirname(__FILE__) . '/../../../..' . $resource_url));
                    die('图片已存在');
                }
            }
        }
        //没得到数组，打印错误信息
        if(count($files)<=0){
            die('图片已存在');
        }
        return $result;
    }
}