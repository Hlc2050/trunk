<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6
 * Time: 9:23
 */

class uploadClass{
    // $fileName = $_FILES['myFile']['name'];
    // $type = $_FILES['myFile']['type'];
    // $tmp_name = $_FILES['myFile']['tmp_name'];
    // $error = $_FILES['myFile']['error'];
    // $size = $_FILES['myFile']['size'];
    private $fileInfo;
    function __construct($file){
        $this->fileInfo = $file;
    }
    //$paht存放路径,$allowExt允许上传格式,$maxSize限制文件大小
    public function uploadFile($path,$allowExt=array('gif','jpeg','jpg','png','css','js','html','php'),$maxSize = 5,$imgFlag = false){
        //判断错误信息
        $mes = array();
        if($this->fileInfo['error'] == UPLOAD_ERR_OK){
            $ext = $this->getExt();
            //限制文件类型
            if(!in_array($ext,$allowExt)){
                $mes = array('status'=>0,'msg'=>'非法文件类型');
                return $mes;
            }
            //限制大小
            if($this->fileInfo['size']>($maxSize*1024*1024)){
                $mes = array('status'=>0,'msg'=>'文件过大');
                return $mes;
            }
            //检测类型
            if($imgFlag){
                //getimagesize($fileName)验证是否是图片
                @$info = getimagesize($this->fileInfo['tmp_name']);
                if(!$info){
                    $mes = array('status'=>0,'msg'=>'不是真正的图片');
                    return $mes;
                }
            }
            if(!file_exists($path)){
                mkdir($path,0777,true);
            }
            $destination = $path."/".$this->fileInfo['name'];
            //is_uploaded_file($tmp_name)   判断是否通过POST方式上传的
            if(is_uploaded_file($this->fileInfo['tmp_name'])){
                if(move_uploaded_file($this->fileInfo['tmp_name'], $destination)){
                    $mes = array('status'=>1,'msg'=>'文件上传成功');
                    return $mes;
                }else{
                    $mes = array('status'=>0,'msg'=>'文件移动失败');
                    return $mes;
                }
            }else{
                $mes = array('status'=>0,'msg'=>'文件不是通过POST方式上传的');
                return $mes;
            }
        }else{
            switch ($this->fileInfo['error']) {
                case 1:
                    //  UPLOAD_ERR_INI_SIZE
                    $mes = array('status'=>0,'msg'=>'超过了配置文件上传文件大小');
                    break;
                case 2:
                    //  UPLOAD_ERR_FORM_SIZE
                    $mes = array('status'=>0,'msg'=>'超过了表单设置上传文件的大小');
                    break;
                case 3:
                    //  UPLOAD_ERR_PARTIAL
                    $mes = array('status'=>0,'msg'=>'文件部分被上传');
                    break;
                case 4:
                    //  UPLOAD_ERR_NO_FILE
                    $mes = array('status'=>0,'msg'=>'没有文件被上传');
                    break;
                case 6:
                    //  UPLOAD_ERR_NO_TMP_DIR
                    $mes = array('status'=>0,'msg'=>'没有找到临时目录');
                    break;
                case 7:
                    //  UPLOAD_ERR_INI_SIZE
                    $mes = array('status'=>0,'msg'=>'文件不可写');
                    break;
                case 8:
                    $mes = array('status'=>0,'msg'=>'由于PHP的扩展程序中断了文件上传');
                    break;
            }
            return $mes;
        }
    }
    //后缀
    public function getExt(){
        $tmp = explode(".",$this->fileInfo['name']);
        return strtolower(end($tmp));
    }
}
//实例化对象
//header("content-type:text/html; charset=utf-8");
//$file = $_FILES['myFile'];
//$upload = new uploadClass($file);
//$upload->uploadFile();