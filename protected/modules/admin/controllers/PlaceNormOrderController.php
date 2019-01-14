<?php

/**
 * 订单下单
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/12/28
 * Time: 15:13
 */
class PlaceNormOrderController extends AdminController
{

    /**
     * 订单下单列表
     * author: yjh
     */
    public function actionIndex()
    {
        $page = $this->getExportData();
        $has_un_belong = 0;
        $un_belong = PlaceNormOrderManage::model()->find('tg_uid = 0');
        if ($un_belong) $has_un_belong = 1;
        $this->render('index', array('page' => $page,'has_un_belong'=>$has_un_belong));
    }

    /**
     * 修改订单下单数据
     * author: yjh
     * 为未归属数据时业务类型、计费方式、商品等值可为空 lxj
     */
    public function actionEdit()
    {
        $page = array();
        $id = $this->get('id');
        $info = PlaceNormOrderManage::model()->findByPk($id);
        if (!$_POST) {
            $page['info'] = Dtable::toArr($info);
            $page['info']['chargingTypeList'] = Dtable::toArr(BusinessTypeRelation::model()->findAll('bid=:bid', array(':bid' => $page['info']['business_type'])));
            foreach ($page['info']['chargingTypeList'] as $k => $v) {
                $page['info']['chargingTypeList'][$k]['cname'] = vars::get_field_str('charging_type', $v['charging_type']);
            }
            $this->render('update', array('page' => $page));
            exit;
        }
        //业务类型 计费方式 客服部 商品
//        $info->business_type = $this->post('business_type');
//        if (!$info->business_type && $info['tg_uid'] != 0) $this->msg(array('state' => 0, 'msgwords' => '没有选择业务类型！'));
//        $info->charging_type = $this->post('charging_type');
//        if (!$info->charging_type && $info['tg_uid'] != 0 ) $this->msg(array('state' => 0, 'msgwords' => '没有选择计费方式！'));
//        $info->customer_service_id = $this->post('customer_service_id');
//        if (!$info->customer_service_id && $info['tg_uid'] != 0) $this->msg(array('state' => 0, 'msgwords' => '没有选择客服部！'));
//        $info->goods_id = $this->post('goods_id');
//        if (!$info->goods_id && $info['tg_uid'] != 0) $this->msg(array('state' => 0, 'msgwords' => '没有选择商品！'));


        $info->customer = $this->post('customer');
        $info->order_id = $this->post('order_id');
        $info->order_date = $this->post('order_date');
        $info->addfan_date = $this->post('addfan_date');
        $info->order_money = $this->post('order_money');
        if (!$info->order_id) $this->msg(array('state' => 0, 'msgwords' => '没有填订单编号！'));
        if (!$info->order_date) $this->msg(array('state' => 0, 'msgwords' => '没有填下单日期！'));
        if (!$info->addfan_date) $this->msg(array('state' => 0, 'msgwords' => '没有填加粉日期！'));
        if (!$info->order_money) $this->msg(array('state' => 0, 'msgwords' => '没有填订单金额！'));

        if (!is_numeric($info->order_money)) $this->msg(array('state' => 0, 'msgwords' => '订单金额要填数字！'));


//        $result=PlaceIndepOrderManage::model()->find('order_date=:order_date and weixin_id=:weixin_id and id!=:id',array(':order_date'=>$info->order_date,':weixin_id'=>$info->weixin_id,':id'=>$id));
//        if($result) $this->msg(array('state'=>0,'msgwords' => '同个微信号不能在同一天有多个订单！'));
        $info->order_date = strtotime($info->order_date);
        $info->addfan_date = strtotime($info->addfan_date);
        $info->update_time = time();
        $dbresult = $info->save();
        $msgarr = array('state' => 1, 'url' => $this->get('backurl'));
        $logs = "修改了订单下单信息：" . $id;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            $this->logs($logs);
            $this->msg($msgarr);
        }
    }

    /**
     * 批量删除订单下单
     * author: yjh
     */
    public function actionDel()
    {
        $ids = $this->get('ids');
        $idArr = explode(',', $ids);
        $log = '';
        $msgwords = '批量删除订单下单成功</br>';
        foreach ($idArr as $val) {
            $info = PlaceNormOrderManage::model()->findByPk($val);

            $info->delete();
            $log .= "删除了订单下单：ID:" . $info->id . ",";
            $msgwords .= "删除了订单下单：ID:" . $info->id . "</br>";
        }
        $this->logs($log);
        $this->msg(array('state' => 1, 'msgwords' => $msgwords));
    }

    /**
     * 模板下载
     * author: yjh
     */
    public function actionTemplate()
    {
        $colums = array('下单日期', '订单编号', '客户姓名', '订单价格', '微信号ID', '加粉日期');
        $file_name = '订单下单导入模板.xls';
        $txt = "导入注意事项：下单日期和加粉日期格式'" . date('Y/m/d', time()) . "',微信号ID必须后台已添加！";
        helper::downloadExcel($colums, array(), $txt, $file_name);
    }

    /**
     * excel批量导入订单下单
     *  author: yjh
     */
    public function actionImport()
    {
        if (isset($_POST['submit'])) {
            $file = CUploadedFile::getInstanceByName('filename');//获取上传的文件实例
            if (!$file) $this->msg(array('state' => 0, 'msgwords' => '未选择文件！'));
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
     * 判断日期、单量、金额格式
     * 数据为空
     * 判断微信号是否已存在
     * 1.7.4添加功能 根据微信id及加粉日期匹配该微信推广人员等信息 lxj
     */
    private function dataFilter(array $data, $row)
    {
        static $rightData = array();
        /******1、判断数据是否为空********/
        if (empty($data[0])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的下单日期为空！'));
        if (empty($data[1])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的订单编号为空！'));
        //if (empty($data[2])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的客户姓名为空！'));
        if (empty($data[3]) && $data[3] != '0') $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的订单价格为空！'));
        if (empty($data[4])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的微信号ID为空！'));
        if (empty($data[5])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的加粉日期为空！'));

        /******2、判断数据冲突情况**********/
        //下单日期判断
        $order_date = strtotime($data[0]) ? strtotime($data[0]) : false;
        if ($order_date === false) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的下单日期' . $data[0] . '格式不正确！'));

        //下单金额判断
        $order_money = is_numeric($data[3]) ? $data[3] : false;
        if ($order_money === false && $data[3] != '0') $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的订单价格不是数字！'));

        //微信号判断
        $weChatInfo = $this->toArr(WeChat::model()->find('wechat_id = :wechat_id', array(':wechat_id' => $data[4])));
        if (!$weChatInfo) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的微信号ID' . $data[4] . '不存在！'));

        if (CustomerServiceManage::model()->findByPk($weChatInfo['customer_service_id'])->status == 1)
            $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的微信号ID' . $data[4] . '属于独立客服部！'));

        //加粉日期判断
        $addfan_date = strtotime($data[5]) ? strtotime($data[5]) : false;
        if ($addfan_date == false) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的加粉日期' . $data[5] . '格式不正确！'));

        // 根据微信id及加粉日期匹配该微信推广人员等信息
        $new_wechatInfo = WeChatUseLog::model()->getWechatInfo($weChatInfo['id'],$addfan_date);
        $rightData[] = array(
            'order_id' => $data[1],
            'customer' => $data[2],
            'goods_id' => empty($new_wechatInfo) ? '':$new_wechatInfo['goods_id'],
            'weixin_id' => $weChatInfo['id'],
            'wechat_id' => $weChatInfo['wechat_id'],
            'order_date' => $order_date,
            'addfan_date' => $addfan_date,
            'order_money' => $order_money,
            'business_type' => empty($new_wechatInfo) ? '':$new_wechatInfo['business_type'],
            'charging_type' => empty($new_wechatInfo) ? '':$new_wechatInfo['charging_type'],
            'tg_uid' =>empty($new_wechatInfo) ? '':$new_wechatInfo['promotion_staff_id'],
            'department_id' => empty($new_wechatInfo) ? '':$new_wechatInfo['department_id'],
            'customer_service_id' => empty($new_wechatInfo) ? '':$new_wechatInfo['customer_service_id'],
            'update_time'=>time(),
            'create_time'=>time(),
        );
        return $rightData;
    }

    /**
     * 循环插入插入订单
     * author: yjh
     */
    private function dataInsert($data)
    {
        $table_name='place_norm_order_manage';
        $rows=array_keys($data[0]);
        $ret=helper::batch_insert_data($table_name,$rows,$data);
        if($ret) {
            $this->logs("批量导入订单下单成功");
            $this->msg(array('state' => 1, 'msgwords' => "批量导入订单下单成功"));
        }else{
            $this->msg(array('state' => 0, 'msgwords' => '批量导入订单下单失败'));
        }
//        foreach ($data as $key => $val) {
//            $info = new PlaceNormOrderManage();
//            $info->tg_uid = $val['tg_uid'];
//            $info->department_id = $val['department_id'];
//            $info->customer = $val['customer'];
//            $info->order_id = $val['order_id'];
//            $info->goods_id = $val['goods_id'];
//            $info->wechat_id = $val['wechat_id'];
//            $info->weixin_id = $val['weixin_id'];
//            $info->order_date = $val['order_date'];
//            $info->addfan_date = $val['addfan_date'];
//            $info->order_money = $val['order_money'];
//            $info->business_type = $val['business_type'];
//            $info->charging_type = $val['charging_type'];
//            $info->customer_service_id = $val['customer_service_id'];
//            $info->update_time = time();
//            $info->create_time = time();
//            $info->save();
//        }
//        $this->logs("批量导入订单下单");
//
//        $this->msg(array('state' => 1, 'msgwords' => "批量导入订单下单成功"));

    }

    /**
     * 导出订单下单
     *
     */
    public function actionExport()
    {
        $file_name = '订单下单表-' . date('Ymd', time());
        $headlist = array('id', '下单日期', '客户姓名', '订单编号', '微信号ID', '业务', '计费方式', '客服部', '商品', '推广人员', '下单金额', '加粉日期');
        $page = $this->getExportData(1);
        $row = array();
        $row[0] = array('-', '-', '-', '-', '-', '-', '-', '-', '-', iconv('utf-8', 'gbk', '合计'), $page['listdata']['total_money'], '-', '-');
        $data = $page['listdata']['list'];
        $count = count($data);
        for ($i = 0; $i < $count; $i++) {
            $k = $i + 1;
            $row[$k] = array(
                $data[$i]['id'],
                date('Y-m-d', $data[$i]['order_date']),
                $data[$i]['customer'],
                $data[$i]['order_id'],
                $data[$i]['wechat_id'],
                $page['listdata']['bNames'][$data[$i]['business_type']],
                vars::get_field_str('charging_type', $data[$i]['charging_type']),
                $page['listdata']['csNames'][$data[$i]['customer_service_id']],
                $page['listdata']['goodsNames'][ $data[$i]['goods_id']],
                $page['listdata']['userNames'][$data[$i]['tg_uid']],
                $data[$i]['order_money'],
                date('Y-m-d', $data[$i]['addfan_date']),

            );
            foreach ($row[$k] as $key => $value) {
                $row[$k][$key] = iconv('utf-8', 'gbk', $value);
            }
        }
        helper::downloadCsv($headlist, $row, $file_name);
    }

    /**
     * 获取导出数据
     *
     */
    private function getExportData($is_export = 0)
    {
        $page = array();
        $params['where'] = '';
        $params['where'] .= $this->getTimeIntervalSql('a.order_date', $this->get('start_order_date'), $this->get('end_order_date'));
        $params['where'] .= $this->getTimeIntervalSql('a.addfan_date', $this->get('start_addfan_date'), $this->get('end_addfan_date'));
        if ($this->get('wechat_id') != '') $params['where'] .= " and(a.wechat_id like '%" . $this->get('wechat_id') . "%') ";
        if ($this->get('order_money') != '') $params['where'] .= " and(a.order_money = " . $this->get('order_money') . ") ";
        // 客服部查询
        if ($this->get('csid') != '') $params['where'] .= " and a.customer_service_id = " . intval($this->get('csid'));
        // 商品查询
        if ($this->get('goods_id') != '') $params['where'] .= " and a.goods_id = " . intval($this->get('goods_id'));
        if ($this->get('customer') != '') $params['where'] .= " and(a.customer like '%" . $this->get('customer') . "%') ";
        if ($this->get('order_id') != '') $params['where'] .= " and(a.order_id like '%" . $this->get('order_id') . "%') ";
        //推广人员
        $get_promotion_id = $this->data_authority();
        if ($get_promotion_id !== 0) {
            //查询用户是否为组长以上级别
            $is_special = AdminUser::model()->user_high_permission(Yii::app()->admin_user->uid);
            //组长以上级别可查看未归属数据
            if ($is_special == 1) {
                $params['where'] .= " and(a.tg_uid in (" . $get_promotion_id . ") or a.tg_uid=0) ";
            } else {
                $params['where'] .= " and(a.tg_uid in (" . $get_promotion_id . ")) ";
            }
        }
        if ($this->get('user_id') != '') {
            $params['where'] .= " and(a.tg_uid = '" . $this->get('user_id') . "') ";
        }

        $params['order'] = "  order by a.id desc      ";

        $params['pagesize'] = 1 == $is_export ? 20000 : Yii::app()->params['management']['pagesize'];
        $params['pagebar'] = 1;
        $params['select'] = "a.*";
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(PlaceNormOrderManage::model()->tableName())->listdata($params);

        $bIds = implode(',', array_filter(array_unique(array_column($page['listdata']['list'], 'business_type'))));
        $cslIds = implode(',', array_filter(array_unique(array_column($page['listdata']['list'], 'customer_service_id'))));
        $goodsIds = implode(',', array_filter(array_unique(array_column($page['listdata']['list'], 'goods_id'))));
        $userIds = implode(',', array_filter(array_unique(array_column($page['listdata']['list'], 'tg_uid'))));
        if ($bIds)
            $page['listdata']['bNames'] = BusinessTypes::model()->getBNames($bIds);
        if ($cslIds)
            $page['listdata']['csNames'] = CustomerServiceManage::model()->getCSNames($cslIds);
        if ($goodsIds)
            $page['listdata']['goodsNames'] = Goods::model()->getGoodsNames($goodsIds);
        if ($userIds)
            $page['listdata']['userNames'] = AdminUser::model()->getUserNames($userIds);

        //$params['debug']=1;
        $page['listdata']['url'] = urlencode('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"]);
        $sql = "select sum(a.order_money) as order_money from place_norm_order_manage as a " . $params['join'] . " where 1 " . $params['where'];
        $totalInfo = Yii::app()->db->createCommand($sql)->queryAll();
        $page['listdata']['order_money'] = $totalInfo[0]['order_money'];

        return $page;
    }

    /*
     * 下载未归属数据
     *  author lxj
     */
    public function actionUnDataExport()
    {
        $file_name = '订单下单未归属数据表.xls' ;
        $headlist = array('id', '下单日期', '客户姓名', '订单编号', '微信号ID', '业务', '计费方式', '客服部', '商品', '推广人员', '下单金额', '加粉日期');
        $data = $this->getUnBelongData();
        $row = array();
        $count = count($data);
        if ($count <= 0) $this->msg(array('state' => 0, 'msgwords' => '没有未归属订单数据'));
        for ($i = 0; $i < $count; $i++) {
            $row[$i] = array(
                $data[$i]['id'],
                date('Y/m/d', $data[$i]['order_date']),
                $data[$i]['customer'],
                $data[$i]['order_id'],
                $data[$i]['wechat_id'],
                '',
                '',
                '',
                '',
                '',
                $data[$i]['order_money'],
                date('Y/m/d', $data[$i]['addfan_date']),
            );
        }
        helper::downloadExcel($headlist, $row,'', $file_name);
    }

    /*
     * 获取未归属数据
     * author lxj
     */
    private function getUnBelongData()
    {
        $unKnownData = PlaceNormOrderManage::model()->findAll('tg_uid = 0');
        return Dtable::toArr($unKnownData);
    }

    /*
     * 导入未归属数据
     * author lxj
     */
    public function actionUnDataImport()
    {
        if (isset($_POST['submit'])) {
            $file = CUploadedFile::getInstanceByName('filename');//获取上传的文件实例
            if (!$file) $this->msg(array('state' => 0, 'msgwords' => '未选择文件！'));
            if ($file->getExtensionName() !='xls') $this->msg(array('state' => 0, 'msgwords' => '请导入.xls文件！'));
            if ($file->getType() == 'application/octet-stream' || $file->getType() == 'application/vnd.ms-excel') {
                $excelFile = $file->getTempName();//获取文件名
                //这里就是导入PHPExcel包了，要用的时候就加这么两句，方便吧
                Yii::$enableIncludePath = false;
                Yii::import('application.extensions.PHPExcel.PHPExcel', 1);
                $excelReader = PHPExcel_IOFactory::createReader('Excel5');
                $phpexcel = $excelReader->load($excelFile)->getActiveSheet(0);//载入文件并获取第一个sheet
                $total_line = $phpexcel->getHighestRow();
                $total_column = $phpexcel->getHighestColumn();

                //第2行开始处理数据
                $updateData = array();
                if ($total_line >= 2) {
                    for ($row = 2; $row <= $total_line; $row++) {
                        $data = array();
                        for ($column = 'A'; $column <= $total_column; $column++) {
                            $data[] = trim($phpexcel->getCell($column . $row)->getValue());
                        }
                        //数据过滤
                        $updateData = $this->unBelongFilter($data, $row);
                    }
                    foreach ($updateData as $value) {
                        // 更新数据
                        $order = PlaceNormOrderManage::model()->findByPk($value['order_id']);
                        $order->business_type = $value['business_type'];
                        $order->charging_type = $value['charging_type'];
                        $order->goods_id = $value['goods_id'];
                        $order->customer_service_id = $value['customer_service_id'];
                        $order->department_id = $value['department_id'];
                        $order->tg_uid = $value['tg_uid'];
                        $result = $order->save();
                        if (!$result) break;
                    }
                    if($result) {
                        $order_ids = array_column($updateData,'order_id');
                        $this->logs("批量导入订单下单未归属数据,订单id:".implode(',',$order_ids));
                        $this->msg(array('state' => 1, 'msgwords' => "批量导入订单下单未归属数据成功"));
                    }else{
                        $this->msg(array('state' => 0, 'msgwords' => '批量导入订单下单未归属数据失败'));
                    }
                } else $this->msg(array('state' => 0, 'msgwords' => '导入文件没有内容！'));
            } else $this->msg(array('state' => 0, 'msgwords' => '请选择要导入的xls文件！'));
        }
    }

    /*
     * 验证导入未归属数据
     * author lxj
     */
    private function unBelongFilter($data,$row)
    {
        static $rightData = array();
        /******1、判断数据是否为空********/
        if (empty($data[0])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的订单id为空！'));
        if (empty(trim($data[5]))) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的业务为空！'));
        if (empty(trim($data[6]))) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的计费方式为空！'));
        if (empty(trim($data[7]))) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的客服部为空！'));
        if (empty(trim($data[8]))) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的商品为空！'));
        if (empty(trim($data[9]))) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的推广人员为空！'));

        /******2、判断数据冲突情况**********/

        //订单id判断
        $order_money = is_numeric($data[0]) ? $data[0] : false;
        if ($order_money === false) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的订单id不是数字！'));

        /******3、验证订单数据**********/
        $order_id = $data[0];
        $order_info = PlaceNormOrderManage::model()->findByPk($order_id);
        if (!$order_info) $this->msg(array('state' => 0, 'msgwords' =>'第' . $row . '行的订单id' . $data[0] . '不存在！'));
        if ($order_info['tg_uid'] != 0) $this->msg(array('state' => 0, 'msgwords' =>'第' . $row . '行不为未归属数据'));

        /******4、验证输入的归属数据**********/
        // 业务类型
        $type_info = BusinessTypes::model()->find('bname = \''.trim($data[5]).'\'');
        if (!$type_info) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的业务类型' . $data[5] .'不存在！'));
        // 计费方式
        $charge_type = vars::get_value('charging_type', $data[6]);
        if ( $charge_type === null ) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的计费方式' . $data[6] .'不存在！'));
        // 客服部
        $cs_info = CustomerServiceManage::model()->find('cname = \''.trim($data[7]).'\'');
        if (!$cs_info) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的客服部' . $data[7] .'不存在！'));
        // 商品
        $gs_info = Goods::model()->find('goods_name = \''.trim($data[8]).'\'');
        if (!$gs_info) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的商品' . $data[8] .'不存在！'));
        // 客服部+商品
        $cs_goods = CustomerServiceRelation::model()->find('cs_id='.$cs_info['id'].' and goods_id='.$gs_info['id']);
        if (!$cs_goods) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的商品' . $data[8] .'不属于该行客服部！'));
        // 推广人员
        $tg_info = AdminUser::model()->find('csname_true = \''.trim($data[9]).'\'');
        if (!$tg_info) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的推广人员' . $data[9] .'不存在！'));
        $department = AdminUserGroup::model()->find('sno='.$tg_info['csno']);
        $rightData[] = array(
            'order_id' => $data[0],
            'goods_id' => $gs_info['id'],
            'business_type' => $type_info['bid'],
            'charging_type' => $charge_type,
            'tg_uid' =>$tg_info['csno'],
            'department_id' => $department['groupid'],
            'customer_service_id' => $cs_info['id'],
        );
        return $rightData;
    }

}