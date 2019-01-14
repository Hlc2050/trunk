<?php
class PostController extends AdminController{
	public function filters(){
		return array(
				'accessControl',
		);
	}
	/*
	 * 控制匿名用户可以访问
	*/
	public function accessRules(){
		return array(
				array('allow','users'=>array('*'),),
		);
	}
	public function actionVerifyCode(){
		/** 初始化*/
		$border = 0; //是否要边框 1要:0不要
		$how = 4; //验证码位数
		$w = $how*20; //图片宽度
		$h = 24; //图片高度
		$fontsize = 5; //字体大小
		$alpha = "abcdefghijkmnpqrstuvwxyz"; //验证码内容1:字母
		$number = "23456789"; //验证码内容2:数字
		$randcode = ""; //验证码字符串初始化
		srand((double)microtime()*1000000); //初始化随机数种子
		$im = ImageCreate($w, $h); //创建验证图片
		/** 绘制基本框架*/
		$bgcolor = ImageColorAllocate($im, 255, 255, 255); //设置背景颜色
		ImageFill($im, 0, 0, $bgcolor); //填充背景色
		if($border){
			$black = ImageColorAllocate($im, 0, 0, 0); //设置边框颜色
			ImageRectangle($im, 0, 0, $w-1, $h-1, $black);//绘制边框
		}
		/** 逐位产生随机字符*/
		for($i=0; $i<$how; $i++){
			$alpha_or_number = mt_rand(0, 1); //字母还是数字
			$str = $alpha_or_number ? $alpha : $number;
			$which = mt_rand(0, strlen($str)-1); //取哪个字符
			$code = substr($str, $which, 1); //取字符
			$j = !$i ? 20 : $j+15; //绘字符位置
			$color3 = ImageColorAllocate($im, mt_rand(0,100), mt_rand(0,100), mt_rand(0,100)); //字符随即颜色
			ImageChar($im, $fontsize, $j, 3, $code, $color3); //绘字符
			$randcode .= $code; //逐位加入验证码字符串
		}
		/** 添加干扰*/
		for($i=0; $i<3; $i++){//绘背景干扰线
			$color1 = ImageColorAllocate($im, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255)); //干扰线颜色
			ImageArc($im, mt_rand(-5,$w), mt_rand(-5,$h), mt_rand(20,300), mt_rand(20,200), 55, 44, $color1); //干扰线
		}
		for($i=0; $i<$how*40; $i++){//绘背景干扰点
			$color2 = ImageColorAllocate($im, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255)); //干扰点颜色
			ImageSetPixel($im, mt_rand(0,$w), mt_rand(0,$h), $color2); //干扰点
		}
	
	
		//把验证码字符串写入session
		$type=isset($_GET['type'])?$_GET['type']:'';
		//特定的 session key
		$allow_type=array(
				'get_login_rancode'=>'login_rancode',
		);
		$is=0;
		foreach($allow_type as $k=>$v){
			if($type==$k){
				$is=1;
				$_SESSION[$v]=$randcode;
				break;
			}
		}	
		if($is==0){
			$_SESSION['rancode'] = $randcode;
		}
		//die(print_r($_SESSION));
		header("Content-type: image/gif");
		/*绘图结束*/
		Imagegif($im);
		ImageDestroy($im);
		/*绘图结束*/
		die();
	}
	

	public function actionSaveHttpImg(){
		$re=array();
		$code=isset($_POST['code'])?stripslashes($_POST['code']):'';
		$newcode=$code;
		$re['statecode']=1;
		$re['statewords']='';
		if(preg_match_all('~<img([^>]*)src=("|\')(http://.*?)\\2~i',$code,$result)){
			// save_img($img_url);
			$nimg=array_unique($result[3]);
			foreach($nimg as $r){
				if(stripos($r,Yii::app()->params['basic']['sitedomain'])){
					continue;
				}
				$newimg=helper::save_img($r);
				$newcode=str_ireplace($r,$newimg,$newcode);
					
					
			}
		}
		$re['code']=$newcode;
		die(json_encode($re));	
	}
	
	public function actionGetTags(){
		$kw=isset($_GET['kw'])?$_GET['kw']:'';
		$filter_words1 = array('志愿者', '北京市', '志愿服务');
		$filter_words2 = array('志愿', '同行', '一起');
		$a=helper::get_tags_baidu($kw, $filter_words1, $filter_words2);
		$re=array();
		$re['code']=1;
		$re['list']=array();
		foreach($a as $r){
			$re['list'][]=$r['k'];
		}
		die(json_encode($re));
	}
	
	//裁剪图片
	public function actionCutImage(){
		$table=$this->post('table');
		$idField=$this->post('idField');
		$id=$this->post('id');
		$imgField=$this->post('imgField');
		$m=Dtable::model($table)->findByPk($id);
		if(!$m){
			$this->msg(array('state'=>0,'msgwords'=>'信息不存在022','type'=>'json'));
		}
		$img=$this->post('img');
		$x=$this->post('x');
		$y=$this->post('y');
		$x2=$this->post('x2');
		$y2=$this->post('y2');
		$w=$x2-$x;
		$h=$y2-$y;
		$img_file=dirname(__FILE__)."/../../../..".$img;
		$endaddress=preg_replace('~\.(jpg|png|jpeg|gif|bmp)~i','_crop.$1',$img);
		$endaddress_file=preg_replace('~\.(jpg|png|jpeg|gif|bmp)~i','_crop.$1',$img_file);
		//die($endaddress);
		//die("$w,$h");
		$a=new ImageCrop($img_file,2,"$x,$y","$w,$h",$endaddress_file);
		$a->outimage();
		//再次生成缩略图
	
		$createthumb=new CreateThumb();
		$createthumb->SetVar($endaddress_file, 'file');
		$createthumb->PRorate(preg_replace('~([^\/]*\.(jpg|png|jpeg|bmp|gif))~i','thumb_$1',$endaddress_file),'300',300);
	
		//信息 修改封面
		$m->$imgField=$endaddress;
		$m->save();
	
		$this->logs('裁剪了封面图片,ID：'.$table.'-'.$id);
		$this->msg(array('state'=>1,'msgwords'=>'裁剪成功','type'=>'json'));
	}
	
	
}