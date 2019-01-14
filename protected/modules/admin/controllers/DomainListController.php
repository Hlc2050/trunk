<?php

/**
 * 域名列表控制器
 * User: fang
 * Date: 2016/11/4
 * Time: 14:15
 */
class DomainListController extends AdminController
{
    public function actionIndex()
    {
        $page = $this->getExportData();
        $this->render('index', array('page' => $page));
    }

    /**
     * 添加域名
     */
    public function actionAdd()
    {
        $page = array();
        $id = $this->get('id');
        //显示表单
        if (!$_POST) {
            $this->render('update', array('page' => $page));
            exit();
        }
        //处理需要的字段
        foreach ($_POST['domain'] as $k => $v) {
            $info = new DomainList();
            $info->domain = $v;
            $info->money = $_POST['money'][$k];
            $info->is_https = $_POST['is_https'][$k];
            $info->is_public_domain = $_POST['is_public_domain'][$k];
            $info->domain_type = $_POST['domain_type'][$k];
            $info->application_type = $_POST['application_type'][$k];

            if ($info->domain_type == 0 || $info->domain_type == 3 ) {
                $info->cnzz_code_id = $_POST['cnzz_code_id'][$k];
            } else {
                $info->cnzz_code_id = 0;
            }
            if ($info->domain == '' || $info->domain_type == '') continue;
            if ($info->money == '') $info->money = 0;
            $info->uid = $_POST['promotion_staff_id'][$k];
            if ($info->uid == '') $this->msg(array('state' => 0, 'msgwords' => '推广人员未填完整！'));
            if ($info->domain_type == 0 || $info->domain_type == 3) {
                if ($info->cnzz_code_id == '') {
                    $this->msg(array('state' => 0, 'msgwords' => '未选择总统计组别！'));
                }
                $m = DomainList::model()->count('cnzz_code_id =' . $info->cnzz_code_id);
                if ($m >= 500) {
                    $this->msg(array('state' => 0, 'msgwords' => '该总统计组别域名已经满了！'));
                }
            }
            if($info->application_type == ' '){
                $this->msg(array('state' => 0, 'msgwords' => '未选择应用类型！'));
            }

            $k001 = DomainList::model()->domainAddCount($v);
            if ($k001 > 0) {
                $this->msg(array('state' => 0, 'msgwords' => '该域名已存在！'));
            }
            $info->create_time = time();
            $info->update_time = time();
            $dbresult = $info->save();
            $id = $info->primaryKey;
            $logs = "添加了域名：" . $info->domain;
            $this->logs($logs);
        }
        $msgarr = array('state' => 1, 'url' => $this->createUrl('domainList/index') . '?p=' . $_GET['p'] . '');  //新增的话跳转会添加的页面
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            //成功跳转提示
            $this->msg($msgarr);
        }

    }

    /**
     * 修改域名
     * author: yjh
     */
    public function actionEdit()
    {
        $page = array();
        $id = $this->get('id');
        $from_domain = $this->get('from_domain');

        $info = DomainList::model()->findByPk($id);

        //显示表单
        if (!$_POST) {
            $info = $this->toArr($info);
            if (!$info) {
                $this->msg(array('state' => 0, 'msgwords' => '域名不存在'));
            }
            $page['info'] = $info;
            $this->render('update', array('page' => $page));
            exit();
        }
        //处理需要的字段
        $last_type = $info->domain_type;
        $last_status = $info->status;
        $last_cnzz_id = $info->cnzz_code_id;
//        $last_is_https = $info->is_https;
        $last_application_type = $info->application_type;
        $last_uid = $info->uid;
        $status = $this->get('domain_status')==''?1:$this->get('domain_status');
        $id = $this->get('id');
        $info->money = $this->get('money');
        $info->cnzz_code_id = $this->get('cnzz_code_id');
        $info->domain_type = $this->get('domain_type');
        $info->is_https = $this->get('is_https');
        $info->is_public_domain = $this->get('is_public_domain');
        $info->application_type = $this->get('application_type');
        $info->mark = $this->get('mark');

        if ($info->money == '') {
            $this->msg(array('state' => 0, 'msgwords' => '费用不能为空'));
        }
        if ($info->domain_type == '') {
            $this->msg(array('state' => 0, 'msgwords' => '域名类型未选'));
        }

        if ($last_status == 0 && ($info->domain_type == 0 || $info->domain_type == 3)) {
            if ($info->cnzz_code_id == '') {
                $this->msg(array('state' => 0, 'msgwords' => '总统计组别未选'));
            }
        }

        if($info->application_type == ''){
            $this->msg(array('state' => 0, 'msgwords' => '未选择应用类型！'));
        }

        if($status == 1  && $last_application_type != $info->application_type){
            $this->msg(array('state' => 0, 'msgwords' => '该域名不是备用状态,不能修改应用类型！'));
        }


        if ($last_status != 0 && $last_type != $info->domain_type) {
            $this->msg(array('state' => 0, 'msgwords' => '暂时不能修改域名类型'));
        } elseif ($last_status == 0 && $last_type != $info->domain_type) {
            $info->uid = 0;
        }

        //正在使用的跳转域名白域名不能变更推广人员
        if ($last_status != 0  && $last_uid != $this->get('promotion_staff_id')) {
            $this->msg(array('state' => 0, 'msgwords' => '该域名正在使用不能修改推广人员！'));
        }
        if ($last_cnzz_id != 0 && $last_cnzz_id != $info->cnzz_code_id && $last_status != 0) {
            $this->msg(array('state' => 0, 'msgwords' => '该域名不是备用状态,不能修改总统计组别'));
        }
        if($last_status!=$status){
            $info->status=$status;
        }

        //保存推广人员
        $info->uid = $this->get('promotion_staff_id');

        $info->update_time = time();
        $dbresult = $info->save();
        $msgarr = array('state' => 1, 'url' => $this->get('backurl'));
        $logs = '修改了域名 ID:' . $id . '' . $info->domain . ' ';
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            //新增和修改之后的动作
            if ($info['status'] == 1) {
                DomainPromotionChange::model()->domainchange($info->domain, $from_domain, $info['uid']);
            }
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 删除域名
     */
    public function actionDelete()
    {
        $ids = isset($_GET['ids']) && $_GET['ids'] != '' ? $_GET['ids'] : '';
        $ids = explode(',', $ids);
        $logs = "删除了域名：";
        foreach ($ids as $id) {
            $id = intval($id);
            if ($id == 0) continue;
            $m = DomainList::model()->findByPk($id);
            $domain = $m->domain;
            if ($m->status == "1" || $m->status == "2") {
                $this->msg(array('state' => 0, 'msgwords' => '域名使用中不能删除'));
            };
            $m->delete();
            $logs .= $domain . ",";
        }
        //die();
        $this->logs($logs);
        $this->msg(array('state' => 1));
    }

    /**
     * 重新检测被拦截的域名
     * author: yjh
     */
    public function actionRecheckDomain()
    {
        $domainInfo = DomainList::model()->findByPk($this->get('id'));
        $status = DomainList::model()->checkDomain($domainInfo->domain);
        if ($status == 2) {
            sleep(1);
            $status = DomainList::model()->checkDomain($domainInfo->domain);
        }
        if ($status == 2) {
            $msg = array('status' => 1, 'msg' => '<b>域名确实被拦截，不做任何处理</b>');
        } elseif ($status == 0) {
            //判断是否有非下线推广在使用该域名
            $new_status = 0;
            //域名为跳转域名或白域名
            if ($domainInfo->domain_type ==1 || $domainInfo->domain_type == 2) {
                $condition = ' status!=1 and (promotion_type=0 or promotion_type=3) ';
                if ($domainInfo->domain_type ==1) {
                    $condition .= ' and goto_domain_id='.$domainInfo->id;
                }else {
                    $condition .= ' and is_white_domain=1 and white_domain_id='.$domainInfo->id;
                }
                $online_promotion = Dtable::toArr(Promotion::model()->find($condition));
            }else {
                $online_promotion = PromotionDomain::model()->getPromotionByDomain($domainInfo->id,'p.status!=1','p.id');
            }
            if ($online_promotion) {
                $new_status = 1;
            }
            $update = array(
                'status'=>$new_status,
                'update_time'=>time(),

            );
            DomainList::model()->updateDomains($domainInfo->id,$update,0,0);
            $msg = array('status' => 2, 'msg' => '<b>域名被误拦截，已重置</b>');
            $logs = "域名" . $domainInfo->domain . "重新检测判断为误拦截，改为备用。";
            $this->logs($logs);

        } elseif ($status == 1) {
            $msg = array('status' => 1, 'msg' => '<b>403服务器错误</b>');
        } elseif ($status == 3) {
            $msg = array('status' => 1, 'msg' => '<b>检测失败</b>');
        } else {
            $msg = array('status' => 1, 'msg' => '<b>404</b>');
        }
        die(json_encode($msg));
    }

    /**
     * 下载域名列表模板
     */
    public function actionTemplate()
    {
        $headlist = array('域名', '域名费用', '类型' ,'推广人员', '总统计组别', '支持Https','公众号域名','应用类型');
        $txt="类型为：推广、跳转、白域名；类型为推广的的域名可不填推广人员";
        $file_name = '域名列表模版.xls';
        helper::downloadExcel($headlist,array(),$txt,$file_name);
    }

    /**
     * 导入域名列表
     */
    public function actionLoad()
    {
        if (isset($_POST['submit'])) {
            $file = CUploadedFile::getInstanceByName('filename');//获取上传的文件实例
            if ($file) {
                if ($file->getExtensionName() != 'xls') $this->msg(array('state' => 0, 'msgwords' => '请导入.xls文件！'));
                if ($file->getType() == 'application/octet-stream' || $file->getType() == 'application/vnd.ms-excel' ) {
                    $excelFile = $file->getTempName();//获取文件名
                    //这里就是导入PHPExcel包了，要用的时候就加这么两句，方便吧
                    Yii::$enableIncludePath = false;
                    Yii::import('application.extensions.PHPExcel.PHPExcel', 1);
                    $phpexcel = new PHPExcel();
                    $excelReader = PHPExcel_IOFactory::createReader('Excel5');
                    $phpexcel = $excelReader->load($excelFile)->getActiveSheet(0);//载入文件并获取第一个sheet
                    $total_line = $phpexcel->getHighestRow();
                    $total_column = $phpexcel->getHighestColumn();
                    echo '<div class="msgbox0009">';
                    for ($row = 3; $row <= $total_line; $row++) {
                        $data = array();
                        for ($column = 'A'; $column <= $total_column; $column++) {
                            $data[] = trim($phpexcel->getCell($column . $row)->getValue());
                        }
                        if (empty($data[0])) {
                            echo '第' . $row . '行的域名为空！';
                            continue;
                        }
                        if (empty($data[1])) {
                            echo '第' . $row . '行的域名费用为空！';
                            continue;
                        }
                        if (empty($data[2])) {
                            echo '第' . $row . '行的类型为空！';
                            continue;
                        }
                        if(empty($data[7])){
                            echo '第' . $row . '行的应用类型为空！';
                            continue;
                        }
                        if (empty($data[3])) {
                            echo '第' . $row . '行的推广人员为空！';
                            continue;
                        }
                        if ($data[2] == '推广' || $data[2] == '短域名') {
                            if (empty($data[4])) {
                                echo '第' . $row . '行的总统计组别为空！';
                                continue;
                            }
                        }

                        $info = new DomainList();
                        $info->domain = $data[0];
                        $info->money = $data[1];
                        if ($info->domain == '') continue;
                        $k001 = $this->query(" select count(1) as total from domain_list where domain='{$data[0]}'");
                        if ($k001[0]['total'] > 0) {
                            echo $info->domain . ' ,该域名已导入过,无须再次导入<br>';
                            continue;
                        }
                        if (!is_numeric($data[1])) {
                            echo $info->domain . ' ,该域名费用不是数字<br>';
                            continue;
                        }
                        $info->domain_type = vars::get_value('domain_types', $data[2]);
                        if ($info->domain_type === null) {
                            echo $info->domain . ' ,该域名类型不对,未导入<br>';
                            continue;
                        }
                        //推广、短域名需判断总统计组别
                        if ($data[2] == '推广' || $data[2] == '短域名') {
                            $cnzz_code = CnzzCodeManage::model()->find("name='" . $data[4] . "'");
                            if (!$cnzz_code) {
                                echo $info->domain . ' ,该总统计组别不存在,未导入<br>';
                                continue;
                            }
                            $cnzz_code_id = $cnzz_code->id;
                            $m = DomainList::model()->count('cnzz_code_id=' . $cnzz_code_id);
                            if ($m >= 500) {
                                $this->msg(array('state' => 0, 'msgwords' => '该总统计组别域名已经满了！'));
                            }
                            $info->cnzz_code_id = $cnzz_code_id;
                        }
                        //推广人员判断
                        $promotionInfo = PromotionStaff::model()->find('name="' . $data[3] . '"');
                        if (!$promotionInfo) {
                            echo $info->domain . ' ,推广人员 ' . $data[3] . ' 不存在<br>';
                            continue;
                        }
                        $info->uid = $promotionInfo->user_id;
                        if ($data[5] == '是') {
                            $info->is_https = 1;
                        } elseif ($data[5] == '' || $data[5] == '否') {
                            $info->is_https = 0;
                        } else {
                            echo '无法判断 ' . $data[5] . '是否支持https<br>';
                            continue;
                        }
                        if ($data[6] == '是') {
                            $info->is_public_domain = 1;
                        } elseif ($data[6] == '' || $data[6] == '否') {
                            $info->is_public_domain = 0;
                        } else {
                            echo '无法判断 ' . $data[6] . '是否是公众号<br>';
                            continue;
                        }
                        if ($data[7] == '静态应用') {
                            $info->application_type = 1;
                        } elseif ($data[7] == '' || $data[7] == '普通应用') {
                            $info->application_type = 0;
                        } else {
                            echo '无法判断 ' . $data[7] . '的应用类型<br>';
                            continue;
                        }
                        $info->create_time = time();
                        $result = $info->save();
                        if ($result) {
                            echo $info->domain . ' ,导入成功! <br>';
                        } else {
                            echo $info->domain . ' ,导入失败! <br>';
                        }
                        //$id=$info->primaryKey;
                        $logs = "添加了域名：" . $info->domain;
                        //新增和修改之后的动作
                        $this->logs($logs);
                    }
                    echo '</div><script>setTimeout("parent.show_frame_infos();",500)</script>';
                    exit;
                    //$this->msg(array('state'=>1));

                }
            } else {
                $this->msg(array('state' => 0, 'msgwords' => '文件不存在'));
            }

        }
    }

    /**
     * 导出域名列表
     */
    public function actionExport()
    {
        $data = $this->getExportData(1);
        $data = $data['listdata']['list'];

        $temp_array = array();
        $count = count($data);
        for ($i = 0; $i < $count; $i++) {
            //更换is_https对应的数值
            $is_https = $data[$i]['is_https'] == 1?"支持":"不支持";
            $is_public_domain =$data[$i]['is_public_domain'] ==1?"是":"否";
            $application_type =$data[$i]['application_type'] == 1?"静态应用":"普通应用";
            $data[$i]['status'] = vars::get_field_str('domain_status', $data[$i]['status']);
            $data[$i]['domain_type'] = vars::get_field_str('domain_types', $data[$i]['domain_type']);

            $line = $i;
            //把数据放入新的数组，对应表头排序输出
            $temp_array[$line] = array(
                $data[$i]['id'],//ID
                $data[$i]['domain'],//域名
                $is_https,//支持Https
                $is_public_domain,//公众号
                $data[$i]['money'],//域名费用
                $data[$i]['csname_true'],//推广人员
                $data[$i]['status'],//状态
                $data[$i]['domain_type'],//类型
                $data[$i]['name'],//总统计组别
                $application_type,//应用类型
                date('Y-m-d H:i', $data[$i]['create_time']),//添加时间
                $data[$i]['mark'],//备注
            );
            foreach ($temp_array[$line] as $key => $value) {
                $temp_array[$line][$key] = iconv('utf-8', 'gbk', $value);
            }
        }
        $headlist = array('ID', '域名', '支持Https', '公众号','域名费用', '推广人员', '状态', '类型', '总统计组别','应用类型', '添加时间', '备注');
        $file_name = '域名列表-' . date("Ymd");
        helper::downloadCsv($headlist, $temp_array, $file_name);
    }

    /**
     * 导出无推广人员域名列表
     */
    public function actionNoUserDomain()
    {
        $headlist = array('域名', '推广人员');
        $doamin_list = Dtable::toArr(DomainList::model()->findAll(
                array(
                    'select'=>'domain',
                    'condition'=>' status=0 and uid=0',
                )));
        $data = array();
        foreach ($doamin_list as $value) {
            $data[] = array($value['domain'],'');
        }
        $file_name = '无推广人员域名列表.xls';
        helper::downloadExcel($headlist,$data,'',$file_name);
    }

    /**
     * 导入推广人员
     */
    public function actionImportDomainUser()
    {
        $file = CUploadedFile::getInstanceByName('filename');//获取上传的文件实例
        if ($file) {
            if ($file->getExtensionName() != 'xls') $this->msg(array('state' => 0, 'msgwords' => '请导入.xls文件！'));
            if ($file->getType() == 'application/octet-stream' || $file->getType() == 'application/vnd.ms-excel' ) {
                $excelFile = $file->getTempName();//获取文件名
                //这里就是导入PHPExcel包了，要用的时候就加这么两句，方便吧
                Yii::$enableIncludePath = false;
                Yii::import('application.extensions.PHPExcel.PHPExcel', 1);
                $phpexcel = new PHPExcel();
                $excelReader = PHPExcel_IOFactory::createReader('Excel5');
                $phpexcel = $excelReader->load($excelFile)->getActiveSheet(0);//载入文件并获取第一个sheet
                $total_line = $phpexcel->getHighestRow();
                $total_column = $phpexcel->getHighestColumn();
                echo '<div class="msgbox0009">';
                for ($row = 2; $row <= $total_line; $row++) {
                    $data = array();
                    for ($column = 'A'; $column <= $total_column; $column++) {
                        $data[] = trim($phpexcel->getCell($column . $row)->getValue());
                    }
                    if (empty($data[0])) {
                        echo '第' . $row . '行的域名为空！<br>';
                        continue;
                    }
                    if (empty($data[1])) {
                        echo '第' . $row . '行的推广为空！<br>';
                        continue;
                    }
                    $domain = trim($data[0]);
                    $tg_name = trim($data[1]);
                    $info = DomainList::model()->find('domain=\''.$domain.'\'');
                    if (!$info) {
                        echo $domain . ' 域名不存在<br>';
                        continue;
                    }
                    if ($info->status != 0) {
                        echo $domain . ' 不为备用域名,不能修改推广人员<br>';
                        continue;
                    }
                    if ($info->uid) {
                        echo $domain . ' 已设置推广人员<br>';
                        continue;
                    }
                    //推广人员判断
                    $promotionInfo = PromotionStaff::model()->find('name="' . $tg_name . '"');
                    if (!$promotionInfo) {
                        echo $tg_name.' 推广人员不存在<br>';
                        continue;
                    }
                    $info->uid = $promotionInfo->user_id;
                    $info->update_time = $promotionInfo->user_id;
                    $result = $info->save();
                    if ($result) {
                        echo $info->domain . ' ,导入成功! <br>';
                    } else {
                        echo $info->domain . ' ,导入失败! <br>';
                    }
                    //$id=$info->primaryKey;
                    $logs = "修改了域名：" . $info->domain;
                    //新增和修改之后的动作
                    $this->logs($logs);
                }
                echo '</div><script>setTimeout("parent.show_frame_infos();",500)</script>';
                exit;
                //$this->msg(array('state'=>1));

            }
        } else {
            $this->msg(array('state' => 0, 'msgwords' => '文件不存在'));
        }
    }

    /**
     * 获取列表数据
     */
    public function getExportData($is_export = 0)
    {
        $page = array();
        $params['where'] = '';
        if ($this->get('search_type') == 'domain' && $this->get('search_txt')) {
            $params['where'] .= " and(domain like '%" . $this->get('search_txt') . "%') ";
        } else if ($this->get('search_type') == 'uid' && $this->get('search_txt')) {
            $params['where'] .= " and(csname_true like '%" . $this->get('search_txt') . "%') ";
        }
        if ($this->get('search_type2') != '') {
            $params['where'] .= " and(status=" . intval($this->get('search_type2')) . ") ";
        }
        if ($this->get('search_domain_types') != '') {
            $params['where'] .= " and(domain_type=" . intval($this->get('search_domain_types')) . ") ";
        }
        if ($this->get('search_public_domain') != '') {
            $params['where'] .= " and(is_public_domain=" . intval($this->get('search_public_domain')) . ") ";
        }

        $params['order'] = "  order by id desc    ";
        $params['join'] = "  LEFT JOIN cservice ON a.uid=cservice.csno  
                             LEFT JOIN cnzz_code_manage as d ON a.cnzz_code_id=d.id
        ";
        $params['pagesize'] = 1 == $is_export ? 10000 : Yii::app()->params['management']['pagesize'];
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $params['select'] = " a.*,cservice.csname_true,d.name";
        $page['listdata'] = Dtable::model('domain_list')->listdata($params);
        $page['listdata']['url'] = urlencode('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"]);

        return $page;
    }

}