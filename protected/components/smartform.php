<?php 
class smartform{
//取留言列表，返回数组
    public static function form_list($params=array()){
		//传入的是数组
		$ret=array(); //返回的数据
		$ret['list']=array();
		$ret['pagecode']='';
		$ret['total']='';
		$bak_cate_id=$params['cate_id'];
		$table=$params['table'];  //传入的直接是表名
		$params['pagesize']=isset($params['pagesize'])?intval($params['pagesize']):10;
		$params['ordersql']=isset($params['ordersql'])?$params['ordersql']:'';
		$params['andwhere']=isset($params['andwhere'])?$params['andwhere']:'';
		$params['joinsql']=isset($params['joinsql'])?$params['joinsql']:'';
		$params['selectsql']=isset($params['selectsql'])?$params['selectsql']:' l.* ';
		//处理
		$andwhere=' where is_check=1 ';
		$params['andwhere']=$andwhere.$params['andwhere'];	
		$params['ordersql']=$params['ordersql']?$params['ordersql']:" order by l.create_time desc ";
		
		$count=0;
		$suffix='';
        //设置分页大小和页码默认值
        if(!isset($params['pagesize']) || !is_numeric($params['pagesize'])) $params['pagesize']=10; //默认分页大小
        if(!isset($params['p']) || !is_numeric($params['p'])){$params['p']=1;}else{$count=1;} //默认页码，如果传入了该值，则会进行分页统计
        //拼接 order by 和 limit 语句
		$suffix.=" limit ".($params['p']-1)*$params['pagesize'].",".$params['pagesize'];
		$sql="select ".$params['selectsql']." from  ".$table."  l  ".$params['joinsql']." ".$params['andwhere']." ".$params['ordersql']."  ";
		if(isset($params['debug'])){
		    echo($sql);
		}
		$csql="select count(*) as total from  ".$table."  l   ".$params['andwhere']."  ";
		//echo($sql);
		$rsarrs['list']=Yii::app()->db->createCommand($sql)->queryAll();
		$crsarrs['list']=Yii::app()->db->createCommand($csql)->queryAll();
		foreach($rsarrs['list'] as $a){
			$ret['list'][]=$a;
		}
		if($count==1) {
			$pageurl='';
			if(url::$rewrite==1){
			    $pageurl=preg_replace('~(\d+)\.~','{p}.',Cms::model()->categorys[$bak_cate_id]['surl']);
			}
			if(isset($params['pageurl'])){
			    $pageurl=$params['pageurl'];	
			}
			$pagearr=helper::pagehtml(array('total'=>$crsarrs['list'][0]['total'],"pagesize"=>$params['pagesize'],'list_str'=>$pageurl));
		    $ret['pagecode']=$pagearr['pagecode'];
			$ret['total']=$pagearr['total'];
		}
		return $ret;
	}
}
?>