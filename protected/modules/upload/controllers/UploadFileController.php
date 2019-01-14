<?php
/** 所有的上传均在这里处理
*  @auther Fei
*  @date 2014-5-23 
*/
class UploadFileController extends CController {
	public function actionIndex(){
		if(!isset($_GET['params'])){
			die('<script>alert("参数未定义")</script>');
		}
		$params=$_GET['params'];
		$params=preg_replace('~(\\\")~','"',$params);
		//die('<script> alert('.$params.');</script>');
			
		$json_params2=json_decode($params,1);	
		if(!isset($json_params2['thumb']['width']) || !isset($json_params2['thumb']['height'])){
			$json_params2['thumb']['width']=250;
			$json_params2['thumb']['height']=250;
		}
		$json_params=json_decode(json_encode($json_params2));
		
		//1.验证请求安全性
		$verify_arr=Yii::app()->params['upload_server'];
		$secret =$verify_arr['secret'];
		$key = $verify_arr['key'];
		$verify=isset($_GET['v'])?$_GET['v']:'';
		if($verify=='') die('<script>alert("No Access 001");</script>');
		$verify=helper::decrypt($verify,$key);
		$gsc=substr($verify,0,strlen($secret));
		if($gsc!=$secret) die('<script>alert("No Access 002 '.$gsc.'");</script>');
		if(!preg_match('~(\d{10})~',substr($verify,strlen($secret)))) die('<script>alert("No Access 003'.$verify.'");</script>');
		
		//2.接收图片上传
		$domain_save_path='uploadfile/'.date('Y/m/d').'/';   //域下的路径 ，前面不要加 /
		$save_path=dirname(__FILE__).'/../../../../uploadfile/'.date('Y/m/d').'/';
		$upload_config=array();/*die('<script>alert("'.$save_path.'")</script>');*/
		$upload_config['savePath']=$save_path;//图片保存路径
		$upload_config['maxSize'] = 100*1024*1024;   //允许上传最大为 100M
		$upload_config['allowExts']=array('gif','jpg','jpeg','bmp','png','flv','zip','rar','JPG','GIF','JPEG','BMP','PNG','ZIP','RAR','pdf','PDF','doc','docx','mp4');
		//是否打水印
		if(Yii::app()->params['water_mark']['watermarkenabled']){
			if(isset($json_params->water) && $json_params->water == 1){
				//要打水印
				//开始收集参数
				$arr_water_water = array();
				$arr_water["source"] = ''; //被打水印的图片
				$arr_water["savename"] = null; //添加水印后的图片名
				$arr_water["watermarktype"] = Yii::app()->params['water_mark']['watermarktype'];//水印类型（图片1或文字0）
				$arr_water["watermarkwidth"] =Yii::app()->params['water_mark']['watermarkwidth'] ;//被打水印的图片的最小宽度 少于该尺寸则不加水印
				$arr_water["watermarkheight"] =Yii::app()->params['water_mark']['watermarkheight'];//被打水印的图片的最小高度 少于该尺寸则不加水印
				$arr_water["watermarkimg"] =Yii::app()->params['water_mark']['watermarkimg'];//水印图片
				$arr_water["watermarktext"] =Yii::app()->params['water_mark']['watermarktext'];//水印文字
				$arr_water["water_mark_font_family"] =Yii::app()->params['water_mark']['water_mark_font_family']; //水印文字字体
				$arr_water["watermarkfontsize"] =Yii::app()->params['water_mark']['watermarkfontsize'];//水印文字大小
				$arr_water["watermarkfontcolor"] =Yii::app()->params['water_mark']['watermarkfontcolor'];//水印文字颜色
				$arr_water["watermarkpct"] =Yii::app()->params['water_mark']['watermarkpct'] ; //水印透明度
				$arr_water["watermarkquality"] =Yii::app()->params['water_mark']['watermarkquality']; //JPEG水印图像质量
				$arr_water["watermarkpos"] =Yii::app()->params['water_mark']['watermarkpos'];//水印位置
				$upload_config['water'] = $arr_water;
			}
		}
		
		// 是否生成缩略图
		if (isset($json_params -> thumb)) {
			$upload_config['thumb'] = true; //是否生成缩略图
			$upload_config['thumbMaxWidth'] = $json_params -> thumb -> width; // 缩略图最大宽度
			$upload_config['thumbMaxHeight'] = $json_params -> thumb -> height; // 缩略图最大高度
			$upload_config['thumbPrefix'] = 'thumb_'; // 缩略图前缀
			$upload_config['thumbPath'] = $save_path; // 缩略图保存路径
		}
		/*
		die('<script>alert(\''.json_encode($upload_config).'\');</script>');
		*/
		//判断图片保存文件夹是否存在，不存在则创建
		if(!is_dir($upload_config['savePath'])) helper::mkdirs($upload_config['savePath']);
		//开始上传
		try{
		$upload = new UploadFile($upload_config);
		}catch(Exception $e){print_r($e->getMessage());echo 'haha';}
		//返回结果
		$result=array();
		if (!$upload->upload()) {  
			$result = $upload->getErrorMsg();  
		}else{  
			$result = $upload->getUploadFileInfo();
		}
		/*
		die('<script>alert(\''.json_encode($result).'\');</script>');
		*/
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
			$r_extension = $file1['extension']=$f['extension'];
			array_push($files,$file1);
			switch($f['extension']){
				case 'zip':
				case 'rar':
					$resource_type=3;break;
				case 'swf':
				case 'flv':
					$resource_type=2;break;
				default:
					$resource_type=1;
			}
			
			//3.插入数据库
		    $data=array();
			$data['resource_url']=$resource_url;
			$data['resource_type']=$resource_type;
			$data['r_width']=$r_width;
			$data['r_height']=$r_height;
			$data['r_size']=$r_size;
			$data['r_name']=$r_oname;
			$data['resource_order']=50;
			$sql=helper::get_sql('resource_list','insert',$data);
		    Yii::app()->db->createCommand($sql)->execute();
		}
		
		//没得到数组，打印错误信息
		if(count($files)<=0){
			die('<script>alert(\''.json_encode($result).'\');</script>');
		}		
		//4.跳转到之前的域名进行参数回调
		$params=urlencode($params);
		$port=$_SERVER['SERVER_PORT']=='80'?'':':'.$_SERVER['SERVER_PORT'];
		if(!preg_match('~'.$port.'~',$_SERVER['PHP_SELF'])){
			$port='';
		}
		die('<script>window.location.href="http://'.$json_params->domain.$port.'/index.php/upload/uploadCallback/index?params={\"params\":'.$params.',\"files\":'.urlencode(json_encode($files)).'}";</script>');

	}
}