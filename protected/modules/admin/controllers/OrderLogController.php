<?php
/**
 * 下单日志控制器
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/12/1
 * Time: 09:26
 */
class OrderLogController extends  AdminController{
	public function actionIndex(){
        $File=new File();
        $info=$File->getFiles('orderLogs/');
        rsort($info);//从最近倒序
        $page['list']=array_slice($info,0,20);
        $this->render('index', array('page' => $page));

	}
    public function actionDownload(){
	    $name=$this->get('name');
	    if(!$name) $this->msg(array('state' => 0, 'msgwords' => '未传入文件名'));
        $filename='orderLogs/'.$name;
        $fileinfo = pathinfo($filename);

        header('Content-type: application/x-'.$fileinfo['extension']);
        header('Content-Disposition: attachment; filename='.$fileinfo['basename']);
        header('Content-Length: '.filesize($filename));
        readfile($filename);
        exit;

    }


}
?>