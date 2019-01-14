<?php

/**
 * 独立订单下单
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/12/28
 * Time: 15:13
 */
class PlaceIndepOrderController extends AdminController
{

    /**
     * 独立订单下单列表
     * author: yjh
     */
    public function actionIndex()
    {
        $page = $this->getExportData();
        $this->render('index', array('page' => $page));
    }

    /**
     * 修改独立下单数据
     * author: yjh
     */
    public function actionEdit()
    {
        $page = array();
        $id = $this->get('id');
        $info = PlaceIndepOrderManage::model()->findByPk($id);
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
        $info->business_type = $this->post('business_type');
        if (!$info->business_type) $this->msg(array('state' => 0, 'msgwords' => '没有选择业务类型！'));
        $info->charging_type = $this->post('charging_type');
        if (!$info->charging_type) $this->msg(array('state' => 0, 'msgwords' => '没有选择计费方式！'));
        $info->customer_service_id = $this->post('customer_service_id');
        if (!$info->customer_service_id) $this->msg(array('state' => 0, 'msgwords' => '没有选择客服部！'));
        $info->goods_id = $this->post('goods_id');
        if (!$info->goods_id) $this->msg(array('state' => 0, 'msgwords' => '没有选择商品！'));


        $info->order_date = $this->post('order_date');
        $info->order_count = $this->post('order_count');
        $info->order_money = $this->post('order_money');
        if (!$info->order_date) $this->msg(array('state' => 0, 'msgwords' => '没有填下单时间！'));
        $info->order_date = strtotime($info->order_date);
        if ($info->order_count=='') $this->msg(array('state' => 0, 'msgwords' => '没有填单量！'));
        if ($info->order_money=='') $this->msg(array('state' => 0, 'msgwords' => '没有填下单金额！'));
        if (!is_numeric($info->order_count)) $this->msg(array('state' => 0, 'msgwords' => '单量要填数字！'));
        if (!is_numeric($info->order_money)) $this->msg(array('state' => 0, 'msgwords' => '下单金额要填数字！'));
        $info->update_time = time();
        $dbresult = $info->save();
        $msgarr = array('state' => 1, 'url' => $this->get('backurl'));
        $logs = "修改了独立订单下单信息：" . $id;
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
        $msgwords = '批量删除独立订单下单成功</br>';
        foreach ($idArr as $val) {
            $info = PlaceIndepOrderManage::model()->findByPk($val);

            $info->delete();
            $log .= "删除了独立订单下单：ID:" . $info->id . ",";
            $msgwords .= "删除了独立订单下单：ID:" . $info->id . "</br>";
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
        $colums=array('下单日期','微信号ID','单量','下单金额（元）');
        $file_name='独立订单下单导入模板.xls';
        $txt="导入注意事项：下单日期格式'" . date('Y/m/d', time()) . "',微信号ID必须后台已添加！";
        helper::downloadExcel($colums,array(),$txt,$file_name);
    }

    /**
     * excel批量导入下单列表
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
                    if(!empty($insertData))
                        $this->dataInsert($insertData);
                } else $this->msg(array('state' => 0, 'msgwords' => '导入文件没有内容！'));
            } else $this->msg(array('state' => 0, 'msgwords' => '请选择要导入的xls文件！'));
        }
    }
    /**
     * 列表导出
     */
    public function actionExport() {
        $file_name = '独立下单表-' . date('Ymd', time());
        $headlist = array('id','下单日期','微信号ID','客服部','业务','计费方式','商品','推广人员','单量','下单金额（元）');
        $page = $this->getExportData(1);
        $row = array();
        $row[0] = array('-','-','-','-','-','-','-',iconv('utf-8', 'gbk', '合计'),$page['listdata']['order_count'],$page['listdata']['order_money']);
        $data = $page['listdata']['list'];
        $count = count($data);
        for ($i = 0; $i < $count; $i++) {
            $k = $i + 1;
            $row[$k] = array(
                $data[$i]['id'],
                date('Y-m-d', $data[$i]['order_date']),
                $data[$i]['wechat_id'],
                $page['listdata']['bNames'][$data[$i]['business_type']],
                vars::get_field_str('charging_type', $data[$i]['charging_type']),
                $page['listdata']['csNames'][$data[$i]['customer_service_id']],
                $page['listdata']['goodsNames'][ $data[$i]['goods_id']],
                $page['listdata']['userNames'][$data[$i]['tg_uid']],
                $data[$i]['order_count'],
                $data[$i]['order_money'],
            );
            foreach ($row[$k] as $key => $value) {
                $row[$k][$key] = iconv('utf-8', 'gbk', $value);
            }

        }
        helper::downloadCsv($headlist, $row, $file_name);
    }
    /**
     * 处理导入数据
     * 判断日期、单量、金额格式
     * 数据为空
     * 判断微信号是否已存在
     */
    private function dataFilter(array $data, $row)
    {
        static $rightData = array();
        /******1、判断数据是否为空********/
        if (empty($data[0])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的下单日期为空！'));
        if (empty($data[1])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的微信号为空！'));
        if (empty($data[2]) &&  $data[2] !='0') $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的单量为空！'));
        if (empty($data[3]) &&  $data[3] !='0') $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的下单金额为空！'));

        /******2、判断数据冲突情况**********/
        //下单日期判断
        $order_date = strtotime($data[0]) ? strtotime($data[0]) : false;;
        if ($order_date === false) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的日期' . $data[0] . '格式不正确！'));

        //微信号判断
        $weChatInfo = $this->toArr(WeChat::model()->find('wechat_id = :wechat_id', array(':wechat_id' => $data[1])));
        if (!$weChatInfo) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的微信号ID' . $data[1] . '不存在！'));

        if (CustomerServiceManage::model()->findByPk($weChatInfo['customer_service_id'])->status == 0)
            $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的微信号ID' . $data[1] . '不属于独立客服部！'));
        //单量判断
        $order_count = is_numeric($data[2]) ? $data[2] : false;
        if ($order_count === false  && $data[3] != '0') $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的单量不是数字！'));

        //下单金额判断
        $order_money = is_numeric($data[3]) ? $data[3] : false;
        if ($order_money === false  && $data[3] != '0') $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的下单金额不是数字！'));
        $rightData[] = array(
            'weixin_id' => $weChatInfo['id'],
            'wechat_id' => $weChatInfo['wechat_id'],
            'business_type' => $weChatInfo['business_type'],
            'charging_type' => $weChatInfo['charging_type'],
            'goods_id' => $weChatInfo['goods_id'],
            'tg_uid' => $weChatInfo['promotion_staff_id'],
            'customer_service_id' => $weChatInfo['customer_service_id'],
            'department_id' => $weChatInfo['department_id'],
            'order_date' => $order_date,
            'order_count' => $order_count,
            'order_money' => $order_money,
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
        $table_name='place_indep_order_manage';
        $rows=array_keys($data[0]);
        $ret=helper::batch_insert_data($table_name,$rows,$data);
        if($ret) {
            $this->logs("批量导入独立订单下单");
            $this->msg(array('state' => 1, 'msgwords' => "批量导入独立订单下单成功"));
        }else{
            $this->msg(array('state' => 0, 'msgwords' => '批量导入独立订单下单失败'));
        }

    }

    /**
     * 获取导出数据
     */
    private function getExportData ($is_export=0) {
        $mtime = explode(' ', microtime());
        $start = $mtime[1] + $mtime[0];
        $params['where'] = '';
        $params['where'] .= $this->getTimeIntervalSql('a.order_date', $this->get('start_order_date'), $this->get('end_order_date'));
        if ($this->get('wechat_id') != '') $params['where'] .= " and(a.wechat_id like '%" . $this->get('wechat_id') . "%') ";
        if ($this->get('order_count') != '') $params['where'] .= " and(a.order_count = " . $this->get('order_count') . ") ";
        if ($this->get('order_money') != '') $params['where'] .= " and(a.order_money = " . $this->get('order_money') . ") ";
        if ($this->get('csid') != '') $params['where'] .= " and(a.customer_service_id = ".$this->get('csid').")";
        if ($this->get('goods_id') != '') $params['where'] .= " and(a.goods_id = ".$this->get('goods_id').")";
        //推广人员
        $get_promotion_id = $this->data_authority();
        if ($get_promotion_id !== 0) {
            $params['where'] .= " and(a.tg_uid in (" . $get_promotion_id . ")) ";
        }
        if ($this->get('user_id') != '') {
            $params['where'] .= " and(a.tg_uid = '" . $this->get('user_id') . "') ";
        }

        $params['order'] = "  order by a.id desc      ";


        $params['pagesize'] = 1 == $is_export ? 20000 : Yii::app()->params['management']['pagesize'];
        $params['pagebar'] = 1;
        $params['select'] = "a.*";
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(PlaceIndepOrderManage::model()->tableName())->listdata($params);
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


        $page['listdata']['url'] = urlencode('http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"]);
        $sql="select sum(a.order_money) as order_money,sum(a.order_count) as order_count from place_indep_order_manage as a ".$params['join']." where 1 ".$params['where'];
        $totalInfo = Yii::app()->db->createCommand($sql)->queryAll();
        $page['listdata']['order_money']=$totalInfo[0]['order_money'];
        $page['listdata']['order_count']=$totalInfo[0]['order_count'];

        return $page;
    }
}