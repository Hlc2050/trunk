<?php

/**
 * 合作商处理
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/1
 * Time: 10:13
 */
class PartnerController extends AdminController
{
    /**
     * 合作商列表主页面数据处理
     * author: yjh
     */
    public function actionIndex()
    {
        //搜索
        $params['where']='';
        //$params['where'] .= " and(delete_status!=1) ";//取出未删除的
        if($this->get('search_type')=='keys' && $this->get('search_txt')){
            $params['where'] =" and(name like '%".$this->get('search_txt')."%') ";
        }else if($this->get('search_type')=='id'  && $this->get('search_txt')){
            $params['where'] =" and(id=".intval($this->get('search_txt')).") ";
        }else if($this->get('search_type')=='channel'  && $this->get('search_txt')){
            $sql = "SELECT partner_id from channel WHERE channel_name like '%".$this->get('search_txt')."%'";
            $info = Yii::app()->db->createCommand($sql)->queryAll();
            $keyArr=array_column($info, 'partner_id');
            if(empty($keyArr)){
                $params['where'] =" and(id =0) ";

            }else{
                $keyStr = implode(',',array_unique($keyArr) );
                $params['where'] =" and(id in ($keyStr)) ";

            }

        }
        $params['order']="  order by a.id desc    ";
        $params['pagesize']=Yii::app()->params['management']['pagesize'];
        $params['pagebar']=1;
        $params['smart_order']=1;
        $page['listdata']=Dtable::model(Partner::model()->tableName())->listdata($params);
        $page['listdata']['url'] = urlencode('http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"]);

        $this->render('index',array('page'=>$page));
    }

    /**
     * 添加合作商或提交表单处理
     * author: yjh
     */
    public function actionAdd()
    {
        $page = array();
        //显示表单
        if (!$_POST) {
            $this->render('update',array('page'=>$page));
            exit;
        }
        //表单验证
        $partnerInfo = new Partner();
        $partnerInfo->name = trim($this->post('name'));
        $resultByName = Partner::model()->count('name=:name', array(':name'=>$partnerInfo->name));
        if($partnerInfo->name=='')$this->msg(array('state'=>0,'msgwords'=>'合作商名称不能为空'));
        if($resultByName > 0) $this->msg(array('state'=>0,'msgwords'=>'合作商名称和之前的重复了，请重新添加'));
        
        $partnerInfo->update_time = time();
        $partnerInfo->create_time = time();

        $dbresult=$partnerInfo->save();
        $id=$partnerInfo->primaryKey;
        $msgarr=array('state'=>1,'url'=>$this->createUrl('partner/index').'?p='.$_GET['p'].''); //保存的话，跳转到之前的列表
        $logs="添加了新的合作商信息：".$partnerInfo->name;
        if($dbresult===false){
            //错误返回
            $this->msg(array('state'=>0));
        }else{
            //新增和修改之后的动作
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 修改合作商表单处理
     * author: yjh
     */
    public function actionEdit(){
        $page = array();
        $id = $this->get('partner_id');
        $partnerInfo=Partner::model()->findByPk($id);
        if(!$partnerInfo){
            $this->msg(array('state'=>0,'msgwords'=>'数据不存在'));
        }
        //显示表单
        if (!$_POST) {
            //如果有get.id为修改，否则判断为新增;
            $page['info']=$this->toArr($partnerInfo);
            $this->render('update',array('page'=>$page));
            exit;
        }
        //表单验证
        $partnerInfo->name = trim($this->post('name'));
        $resultByname = Partner::model()->count('name=:name and id!=:id', array(':name'=>$partnerInfo->name,'id'=>$id));
        if($partnerInfo->name=='')$this->msg(array('state'=>0,'msgwords'=>'合作商名称不能为空'));
        if($resultByname > 0) $this->msg(array('state'=>0,'msgwords'=>'合作商名称和之前的重复了，请重新添加'));

        $partnerInfo->update_time = time();
        $dbresult=$partnerInfo->save();
        $msgarr = array('state' => 1, 'url' => $this->get('backurl')); //保存的话，跳转到之前的列表
        $logs="修改了合作商信息：".$partnerInfo->name;
        if($dbresult===false){
            //错误返回
            $this->msg(array('state'=>0));
        }else{
            //新增和修改之后的动作
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 删除合作商
     * 注意点：判断合作商有无在使用
     * 若有则无法删除
     * author: yjh
     */
    public function actionDelete(){
        if($this->get('partner_id') =='')  $this->msg(array('state' => 0, 'msgwords' => '未传入数据'));
        $id = $this->get('partner_id');
        $info = Partner::model()->findByPk($id);
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }
        $result = InfancePay::model()->count('partner_id=:partner_id', array(':partner_id' => $id));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '此合作商正在使用不能删除'));

        $result = StatCostDetail::model()->count("partner_id=:partner_id", array(":partner_id" => $id));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '成本明细中用到该合作商信息不能删除！'));

        //删除渠道
        $channelList = $this->toArr(Channel::model()->findAll("partner_id=:partner_id",array(":partner_id"=>$id)));
        if(!empty($channelList)){
            foreach ($channelList as $key=>$val){
                $channelInfo = Channel::model()->findByPk($val['id']);
                $channelInfo->delete();
            }
        }
        $info->delete();
        $this->logs("删除了合作商：".$info->name);
        $this->msg(array('state' => 1, 'msgwords' => '删除合作商【'.$info->name.'】成功！'));
    }

    /**
     * 合作商渠道列表主页面数据处理
     * author: yjh
     */
    public function actionChannelIndex()
    {
        $params['where']='';
        //$params['where'] .= " and(a.delete_status!=1) ";
       
        if($this->get('search_type')=='keys' && $this->get('search_txt')){
            $params['where'] =" and(channel_name like '%".$this->get('search_txt')."%') ";
        }else if($this->get('search_type')=='id'  && $this->get('search_txt')){
            $params['where'] =" and(channel_code like '%".$this->get('search_txt')."%') ";
        }
        $partner_id = $this->get('partner_id');
        if($partner_id) {
            $params['where'] .= " and( partner_id =" . $partner_id . " )";
        }
        if($this->get('search_type')=='keys' && $this->get('search_txt')){
            $params['where'] .=" and(channel_name like '%".$this->get('search_txt')."%') ";
            if(isset($_GET['jsoncallback'])) {
                $params['where'] .=" or(b.name like '%".$this->get('search_txt')."%') ";
            }
        }else if($this->get('search_type')=='id'  && $this->get('search_txt')){ //网点ID
            $params['where'] .=" and(channel_code like '%" . $this->get('channelId') . "%') ";
        }


        $params['order']="  order by a.id desc    ";
        $params['join']=' 
                        left join partner as b on b.id=a.partner_id 
                        left join business_types as c on c.bid=a.business_type 
                        left join channel_type as d on d.id=a.type_id 
        ';
        $params['select']=" a.*,c.bname,b.id as partner_id,b.name as  partnerName,d.type_name";
        $params['pagesize']=Yii::app()->params['management']['pagesize'];
        $params['pagebar']=1;
        $params['smart_order']=1;
        $page['listdata']=Dtable::model(Channel::model()->tableName())->listdata($params);
        $page['listdata']['url'] = urlencode('http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"]);

        if(!$partner_id) {
            $page['partnerId'] = 0;
            $page['partnerName'] ='';
        }else{
            //通过合作商ID获取合作商名称
            $partnerName = Partner::model()->find(array(
                'select' => 'name',
                'condition' => 'id=:id',
                'params' => array(':id' => $partner_id),
            ));
            $page['partnerId'] = $partner_id;
            $page['partnerName'] = $partnerName->name;
        }

        if(isset($_GET['jsoncallback'])){
            $data['list']=$page['listdata']['list'];
            $this->msg(array('state'=>1,'data'=>$data,'type'=>'jsonp'));
        }
        $this->render('channelIndex',array('page'=>$page));
    }

    /**
     * 导出
     */
    public function actionExport(){
        $page['listdata']=Dtable::model(Channel::model()->tableName())->listdata($params);
        $channel = Dtable::toArr(Channel::model()->findAll('type_id=0'));
        $file_name = '合作商列表-' . date('Ymd', time());
        $headlist = array('渠道编码','渠道名称','渠道类型');
        $row = array();
        $count = count($channel);
        for ($i = 0; $i < $count; $i++) {
            if($channel[$i]['type_name'] == ''){
                $row[$i] = array(
                    $channel[$i]['channel_code'],
                    $channel[$i]['channel_name'],
                    '',

                );
                foreach ($row[$i] as $key => $value) {
                    $row[$i][$key] = iconv('utf-8', 'gbk', $value);
                }
            }
        }
        helper::downloadCsv($headlist, $row, $file_name);
    }

    /**
     * 渠道类型导入
     */
    public function actionTypeImport(){
        if (isset($_POST['submit'])) {
            $file = CUploadedFile::getInstanceByName('typename');//获取上传的文件实例
            if(!$file) $this->msg(array('state' => 0, 'msgwords' => '未选择文件！'));
            if ($file->getExtensionName() != 'xls') $this->msg(array('state' => 0, 'msgwords' => '请导入.xls文件！'));
            if ($file->getType() == 'application/octet-stream' || $file->getType() == 'application/vnd.ms-excel') {
                $excelFile = $file->getTempName();//获取文件名
                //这里就是导入PHPExcel包了，要用的时候就加这么两句，方便吧
                Yii::$enableIncludePath = false;
                Yii::import('application.extensions.PHPExcel.PHPExcel', 1);
                $excelReader = PHPExcel_IOFactory::createReader('Excel5');
                $phpexcel = $excelReader->load($excelFile)->getActiveSheet(0);//载入文件并获取第一个sheet
                $total_line = $phpexcel->getHighestRow();
                $total_column = $phpexcel->getHighestColumn();

                //第二行开始处理数据
                if ($total_line > 1) {
                    $insertData = array();
                    for ($row = 2; $row <= $total_line; $row++) {
                        $data = array();
                        for ($column = 'A'; $column <= $total_column; $column++) {
                            $data[] = trim($phpexcel->getCell($column . $row)->getValue());
                        }
                        if(!empty($data)){
                            //数据过滤
                            $insertData = $this->dataTypeFilter($data, $row);
                        }

                    }
//                    //插入数据
                    $this->typeInsert($insertData);
                } else $this->msg(array('state' => 0, 'msgwords' => '导入文件没有内容！'));
            } else $this->msg(array('state' => 0, 'msgwords' => '请选择要导入的xls文件！'));
        }
    }
    /**
     * 渠道类型数据处理
     */
    private function dataTypeFilter(array $data,$row){
        static $rightData = array();
        /******1、判断数据是否为空********/
        if (empty($data[0])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的渠道编码为空！'));
        if (empty($data[1])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的渠道名称为空！'));
        if (empty($data[2])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的渠道类型为空！'));

        $temp = array();
        $result = ChannelType::model()->findAll();
        foreach ($result as $value){
            $temp[$value['type_name']] = $value['id'];
        }

        if(!isset($temp[$data[2]])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的渠道类型不存在！'));

        $res = Channel::model()->find("channel_code = '".$data[0]."' and channel_name = '".$data[1]."'");

        //渠道名称 渠道编码判断
        if(!$res) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的渠道不存在！'));

        $rightData[] = array(
            'id' => $res['id'],
            'type_id' => $temp[$data[2]],
        );
        return $rightData;
    }

    /**
     * 循环插入插入数据
     * author: hlc
     */
    private function typeInsert($data)
    {
        foreach ($data as $key => $val) {
            $info =  Channel::model()->findByPk($val['id']);
            $info->type_id = $val['type_id'];
            $info->save();
        }
        $this->logs("批量导入合作商渠道类型");
        //成功跳转提示
        $this->msg(array('state' => 1, 'msgwords' => "批量导入合作商渠道类型成功"));

    }

    /**
     * 添加渠道或提交表单处理
     * author: yjh
     */
    public function actionChannelAdd()
    {
        $page = array();
        //通过合作商ID获取合作商名称
        $partner_id = $this->get('partner_id');
        $partnerName=Partner::model()->find(array(
            'select'=>'name',
            'condition'=>'id=:id',
            'params'=>array(':id'=>$partner_id),
        ));
        $page['partnerId'] = $partner_id;
        $page['partnerName'] = $partnerName->name;
        //显示表单
        if (!$_POST) {
            $this->render('channelUpdate',array('page'=>$page));
            exit;
        }

        //表单验证
        $info = new Channel();
        $info->channel_code = trim($this->post('channel_code'));
        $info->channel_name = trim($this->post('channel_name'));
        $info->business_type = $this->post('business_type');
        $info->type_id = $this->post('channel_type');
        $resultByChannerId = Channel::model()->count('channel_code=:channel_code', array(':channel_code'=>$info->channel_code));
        $resultByChannerName = Channel::model()->count('channel_name=:channel_name', array(':channel_name'=>$info->channel_name));
        if($info->channel_code=='') $this->msg(array('state'=>0,'msgwords'=>'渠道编码不能为空'));
        if(!preg_match("/^[A-Za-z0-9]+$/",$info->channel_code)){  //不允许特殊字符
            $this->msg(array('state'=>0,'msgwords'=>'渠道编码不能包含特殊符号!'));
        }
        if($info->channel_name=='') $this->msg(array('state'=>0,'msgwords'=>'渠道名称不能为空'));
        if($resultByChannerId > 0) $this->msg(array('state'=>0,'msgwords'=>'渠道编码和之前的重复了，请重新添加'));
        if($resultByChannerName > 0) $this->msg(array('state'=>0,'msgwords'=>'渠道名称和之前的重复了，请重新添加'));
        if(!$info->business_type) $this->msg(array('state'=>0,'msgwords'=>'未选择业务类型！'));
        if(!$info->type_id) $this->msg(array('state'=>0,'msgwords'=>'未选择渠道类型！'));


        $info->remark = $this->post('remark');
        $info->partner_id = $this->post('partner_id');
        $info->update_time = time();
        $info->create_time = time();
        $dbresult=$info->save();
        //$id=$info->primaryKey;
        $msgarr=array('state'=>1,'url'=>$this->createUrl('partner/channelIndex').'?partner_id='.$partner_id.'&p='.$_GET['p'].''); //保存的话，跳转到之前的列表
        $logs="添加了渠道信息：".$info->channel_name;
        if($dbresult===false){
            //错误返回
            $this->msg(array('state'=>0));
        }else{
            //新增和修改之后的动作
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }
    /**
     * 修改渠道或提交表单处理
     * author: yjh
     */
    public function actionChannelEdit()
    {
        $page = array();
        //通过合作商ID获取合作商名称

        $partner_id = $this->get('partner_id');
        $partnerName=Partner::model()->findByPk($partner_id);
        $id = $this->get('id');
        $info=Channel::model()->findByPk($id);
        $page['partnerId'] = $partner_id;
        $page['partnerName'] = $partnerName->name;
        //显示表单
        if (!$_POST) {
            $page['info']=$this->toArr($info);
            $this->render('channelUpdate',array('page'=>$page));
            exit;
        }
        //表单验证

        $info->channel_code = trim($this->post('channel_code'));
        $info->channel_name = trim($this->post('channel_name'));
        $resultByChannerId = Channel::model()->count('channel_code=:channel_code and id!=:id', array(':channel_code'=>$info->channel_code,'id'=>$id));
        $resultByChannerName = Channel::model()->count('channel_name=:channel_name and id!=:id', array(':channel_name'=>$info->channel_name,'id'=>$id));
        if($info->channel_code=='') $this->msg(array('state'=>0,'msgwords'=>'渠道编码不能为空'));
        if(!preg_match("/^[A-Za-z0-9]+$/",$info->channel_code)){  //不允许特殊字符
            $this->msg(array('state'=>0,'msgwords'=>'渠道编码不能包含特殊符号!'));
        }
        if($info->channel_name=='') $this->msg(array('state'=>0,'msgwords'=>'渠道名称不能为空'));
        if($resultByChannerId > 0) $this->msg(array('state'=>0,'msgwords'=>'渠道编码和之前的重复了，请重新添加'));
        if($resultByChannerName > 0) $this->msg(array('state'=>0,'msgwords'=>'渠道名称和之前的重复了，请重新添加'));
        $info->business_type = $this->post('business_type');
        $info->type_id = $this->post('channel_type');
        $info->remark = $this->post('remark');
        $info->update_time = time();
        $dbresult=$info->save();
        $msgarr = array('state' => 1, 'url' => $this->get('backurl')); //保存的话，跳转到之前的列表

        $logs="修改了渠道信息：$dbresult".$info->channel_name;
        if($dbresult===false){
            //错误返回
            $this->msg(array('state'=>0));
        }else{
            //新增和修改之后的动作
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 删除渠道 
     * author: yjh
     */
    public function actionChannelDelete(){
        if($this->get('id') =='')  $this->msg(array('state' => 0, 'msgwords' => '未传入数据'));
        $id = $this->get('id');
        $info = Channel::model()->findByPk($id);
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }
        $result = InfancePay::model()->count('channel_id=:channel_id', array(':channel_id' => $id));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '此渠道正在使用不能删除'));
        $result = StatCostDetail::model()->count("channel_id=:channel_id", array(":channel_id" => $id));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '成本明细中用到该渠道信息不能删除！'));


        $info->delete();
        $this->logs("删除了渠道：".$info->channel_name);
        $this->msg(array('state' => 1, 'msgwords' => '删除渠道【'.$info->channel_name.'】成功！'));
    }

    /**
     * 模板下载
     * author: yjh
     */
    public function actionTemplate()
    {

        $colums=array('合作商','渠道名称','渠道编码','业务类型');
        $file_name='合作商渠道导入模板.xls';
        $txt="合作商可重复，渠道名称和渠道编码不能重复，业务类型为：订阅号、手赚和硬广等";
        helper::downloadExcel($colums,array(),$txt,$file_name);
        exit;
    }

    /**
     *  excel批量导入合作商渠道
     *  author: yjh
     */
    public function actionImport()
    {
        if (isset($_POST['submit'])) {
            $file = CUploadedFile::getInstanceByName('filename');//获取上传的文件实例
            if(!$file) $this->msg(array('state' => 0, 'msgwords' => '未选择文件！'));
            if ($file->getExtensionName() != 'xls') $this->msg(array('state' => 0, 'msgwords' => '请导入.xls文件！'));
            if ($file->getType() == 'application/octet-stream' || $file->getType() == 'application/vnd.ms-excel') {
                $excelFile = $file->getTempName();//获取文件名
                //这里就是导入PHPExcel包了，要用的时候就加这么两句，方便吧
                Yii::$enableIncludePath = false;
                Yii::import('application.extensions.PHPExcel.PHPExcel', 1);
                $excelReader = PHPExcel_IOFactory::createReader('Excel5');
                $phpexcel = $excelReader->load($excelFile)->getActiveSheet(0);//载入文件并获取第一个sheet
                $total_line = $phpexcel->getHighestRow();
                $total_column = $phpexcel->getHighestColumn();

                //第三行开始处理数据
                if ($total_line > 2) {
                    $insertData = array();
                    for ($row = 3; $row <= $total_line; $row++) {
                        $data = array();
                        for ($column = 'A'; $column <= $total_column; $column++) {
                            $data[] = trim($phpexcel->getCell($column . $row)->getValue());
                        }
                        //数据过滤
                        $insertData = $this->dataFilter($data, $row);
                    }
                    //插入数据
                    $this->dataInsert($insertData);
                } else $this->msg(array('state' => 0, 'msgwords' => '导入文件没有内容！'));
            } else $this->msg(array('state' => 0, 'msgwords' => '请选择要导入的xls文件！'));
        }
    }

    /**
     * 处理导入数据
     * 数据为空
     * 判断合作商是否已存在，取出id/新增
     * 判断渠道名称和渠道编码是否存在，存在报错
     * 每个数字代表：0：合作商 1：渠道名称 2：渠道编码 3：业务类型
     *
     */
    private function dataFilter(array $data, $row)
    {
        static $rightData = array();
        /******1、判断数据是否为空********/
        if (empty($data[0])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的合作商！'));
        if (empty($data[1])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的渠道名称为空！'));
        if (empty($data[2])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的渠道编码为空！'));
        if (empty($data[3])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的业务类型为空！'));

        //合作商判断 存在取出id 不存在新增
        $partner_id = Partner::model()->getIdByName($data[0]);
        if(!$partner_id){
            $pinfo = new Partner();
            $pinfo->name=$data[0];
            $pinfo->update_time=time();
            $pinfo->create_time=time();
            $pinfo->save();
            $partner_id = $pinfo->primaryKey;
        }

        //渠道名称 渠道编码判断
        if(Channel::model()->find("channel_name = '".$data[1]."'")) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的渠道名称重复了！'));
        if(Channel::model()->find("channel_code = '".$data[2]."'")) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的渠道编码重复了！'));
        foreach ($rightData as $key => $val) {
            if ($data[1] == $val['channel_name']) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的渠道名称'.$data[1].'和' . ($key + 3) . '行的渠道名称重复了！'));
            if ($data[2] == $val['channel_code']) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的渠道编码'.$data[2].'和' . ($key + 3) . '行的渠道编码重复了！'));
        }
        //业务判断
        $businessTypeInfo = $this->toArr(BusinessTypes::model()->find('bname=:bname',array(':bname' => $data[3])));
        if (count($businessTypeInfo) == 0) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的业务'. $data[3].'不存在！加'));


        $rightData[] = array(
            'partner_id' => $partner_id,
            'channel_name' => $data[1],
            'channel_code' => $data[2],
            'business_type' => $businessTypeInfo['bid'],
        );
        return $rightData;
    }

    /**
     * 循环插入插入数据
     * author: yjh
     */
    private function dataInsert($data)
    {

        foreach ($data as $key => $val) {
            $info = new Channel();
            $info->channel_name = $val['channel_name'];
            $info->channel_code = $val['channel_code'];
            $info->partner_id = $val['partner_id'];
            $info->business_type = $val['business_type'];
            $info->update_time=time();
            $info->create_time = time();
            $info->save();
        }
        $this->logs("批量导入合作商渠道");
        //成功跳转提示
        $this->msg(array('state' => 1, 'msgwords' => "批量导入合作商渠道成功"));

    }


}