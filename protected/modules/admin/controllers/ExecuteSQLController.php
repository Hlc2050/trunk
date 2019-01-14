<?php

/**
 * 执行SQL页面
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/12/1
 * Time: 09:26
 */
class ExecuteSQLController extends AdminController
{
    public function actionIndex(){
        $page = array();
        //显示表单
        if (!$_POST) {
            $this->render('index', array('page' => $page));
            exit;
        }
        $sql = $this->post('sql');
        if(!$sql) $this->msg(array('state' => 0, 'msgwords' => '未传入SQL语句'));
        $this->render('index');
    }

    /**
     * 自己写全局打印函数
     * @param $param
     * author: yjh
     */
    function my_print($param){
        $type = gettype($param);
        echo '<pre>';
        if(in_array($type,array('resource','object','unknow type','boolean'))){
            var_dump($param);
        }else if(in_array($type, array('array'))){
            print_r($param);
        }else{
            echo $param;
        }
        echo '</pre>';
    }

    public function actionAjaxExecute(){
        if (isset($_POST['sql'])) {
            $sql = $this->post('sql');
            if(!$sql){
                echo "未传入SQL语句！<br/>";
                die;
            }
            $sql=$this->post('sql');
            $db=$this->post('db');
            echo "<strong>正在执行...</strong><br/>".$sql."<br/>";
            try{
                $data=$db==0?Yii::app()->db->createCommand($sql)->queryAll():Yii::app()->order_db->createCommand($sql)->queryAll();
                $this->my_print($data) ;
                echo "<br/>";
            }
            catch(Exception $e){     // 如果有一条查询失败，则会抛出异常
                try{
                    $db==0?Yii::app()->db->createCommand($sql)->execute():Yii::app()->order_db->createCommand($sql)->execute();
                    echo "<strong>执行成功</strong><br/>";
                }catch (Exception $e){
                    echo "<strong>SQL语句不正确</strong><br/>";
                }
            }
        }
    }
}