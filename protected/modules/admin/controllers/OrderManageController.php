<?php

/**
 * 下单列表管理
 * Created by PhpStorm.
 * User: yjh
 * Date: 2017/7/5
 * Time: 10:13
 */
class OrderManageController extends AdminController
{
    /**
     * 下单列表
     * author: yjh
     */
    public function actionIndex()
    {
        $page = $this->getOrderList();
        $this->render('index', array('page' => $page));
    }

    /**
     * 编辑订单数据
     * author: yjh
     */
    public function actionEdit()
    {
        $page = $key = $key_pkg = array();
        $id = $this->get('id');
        $info = OrderManage::model()->findByPk($id);
        $assistant = OrderAssistant::model()->find("order_id=$id");
        $goodsInfo = OrderGoodsManage::model()->find("order_id=$id");
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }
        if (!$_POST) {
            $page['info'] = $this->toArr($info);
            $page['assistant'] = $this->toArr($assistant);
            $page['goods'] = $this->toArr($goodsInfo);
            $this->render('update', array('page' => $page));
            exit;
        }
        $last_order_status = $info->order_status;
        $last_delivery_date = $info->delivery_date;
        $info->corder_code = trim($this->get('corder_code'));
        $info->order_status = trim($this->get('order_status'));
        if ($info->order_status == 1 && $this->get('delivery_date') == '')
            $this->msg(array('state' => 0, 'msgwords' => '未填写发货时间！'));
        else {
            $delivery_date = strtotime(trim($this->get('delivery_date')));
            if ($delivery_date < strtotime($info->o_date)) {
                $this->msg(array('state' => 0, 'msgwords' => '发货日期不能早于下单日期！'));
            }
            $info->delivery_date = $delivery_date;
            $info->d_date = date('Ymd', $info->delivery_date);
        }
        $dbresult = $info->save();
        if (!$this->get('real_price'))
            $this->msg(array('state' => 0, 'msgwords' => '未填写实际金额！'));
        $goodsInfo->real_price = $this->get('real_price');
        $goodsInfo->save();
        if ($last_order_status == 1) {
            if ($last_delivery_date < strtotime(date('Ymd'))) {
                $key[] = $last_delivery_date . '_' . $assistant->weixin_id . '_' . $info->channel_id;
                $key_pkg[] = $last_delivery_date . '_' . $info->customer_service_id . '_' . $goodsInfo->package_id;
            }
        }
        if ($info->order_status == 1) {
            if ($info->delivery_date < strtotime(date('Ymd'))) {
                $key[] = $info->delivery_date . '_' . $assistant->weixin_id . '_' . $info->channel_id;
                $key_pkg[] = $info->delivery_date . '_' . $info->customer_service_id . '_' . $goodsInfo->package_id;
            }
        }
        if ($info->o_date < date('Ymd')) {
            $key[] = strtotime($info->o_date) . '_' . $assistant->weixin_id . '_' . $info->channel_id;
        }
        $key = array_unique($key);
        $key_pkg = array_unique($key_pkg);
        $this->updateDayOrders($key);
        $this->updatePkgOrders($key_pkg);

        $msgarr = array('state' => 1, 'url' => $this->get('backurl')); //保存的话，跳转到之前的列表
        $logs = "修改了订单信息：" . $info->order_code;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            //新增和修改之后的动作
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }

    }

    /**
     * 列表导出
     * author: yjh
     */
    public function actionExport()
    {
        $is_export = $this->get('ckunexport')?"1":"0";
        if ($is_export == 1) {//导出未导出过的订单
            $objectPHPExcel = new PHPExcel();
            $objectPHPExcel->setActiveSheetIndex(0);
            $objectPHPExcel->getActiveSheet()->getStyle('C')->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $objectPHPExcel->getActiveSheet()->getStyle('N')->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            //$objectPHPExcel->getActiveSheet()->getProtection()->setSheet( true);  // 为了使任何表保护，需设置为真
            //提示的输出
            $objectPHPExcel->getActiveSheet()->mergeCells('A1:N1');
            $objectPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(15);
            $objectPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
            $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', "PassBack");
            $objectPHPExcel->getActiveSheet()->mergeCells('A2:N2');
            $objectPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(15);
            $objectPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setWrapText(true);
            $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', "用此Excle回传，可修改对应订单的订单状态以及客服订单号");
            //设置字体颜色
            $objectPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
            //数据的取出
            $params['where'] = $this->getCondition();
            $params['order'] = "  order by id desc    ";
            $params['join'] = "left join order_assistant as b on b.order_id=a.id
                               left join order_goods_manage as c on c.order_id=a.id
                         ";

            $params['order'] = "  order by a.id desc    ";
            $params['pagesize'] = 1000;
            $params['select'] = "a.id,goods_id,c.package_id,c.real_price,delivery_date,is_export,add_time,order_code,c.package_name,c.package_price,real_name,mobile,province,city,region,detail_area,remark,channel_id,customer_service_id,article_code,corder_code,best_time,order_status";

            $data = Dtable::model('order_manage')->orderdata($params);
            if (empty($data['list'])) $this->msg(array('state' => 0, 'msgwords' => '没有未导出数据！'));
            $channelIds = implode(',', array_filter(array_unique(array_column($data['list'], 'channel_id'))));
            $cslIds = implode(',', array_filter(array_unique(array_column($data['list'], 'customer_service_id'))));
            $goodsIds = implode(',', array_filter(array_unique(array_column($data['list'], 'goods_id'))));

            $idArr = array_column($data['list'], 'id');
            $min_id = min($idArr);
            $max_id = max($idArr);
            if ($channelIds)
                $data['channelCodes'] = Channel::model()->getChannelCodes($channelIds);
            if ($cslIds)
                $data['csNames'] = CustomerServiceManage::model()->getCSNames($cslIds);
            if ($goodsIds)
                $data['goodsNames'] = Goods::model()->getGoodsNames($goodsIds);
            //将未导出状态改成已导出
            $sql = "update order_manage set is_export=1,export_date=" . time() . " where  is_export=0 and id>=$min_id and id<=$max_id";
            Yii::app()->order_db->createCommand($sql)->execute();
            $n = 0;
            //设置居中
            $objectPHPExcel->getActiveSheet()->getStyle('A:R')
                ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //表格头的输出
            $objectPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', 'ID');
            $objectPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(22);
            $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', '下单时间');
            $objectPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(17);
            $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('C3', '订单号');
            $objectPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
            $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('D3', '下单商品');
            $objectPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('E3', '商品');
            $objectPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('F3', '下单金额');
            $objectPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
            $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('G3', '实际金额');
            $objectPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
            $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('H3', '客户姓名');
            $objectPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
            $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('I3', '电话');
            $objectPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(45);
            $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('J3', '地址');
            $objectPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(45);
            $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('K3', '留言');
            $objectPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
            $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('L3', '渠道编码');
            $objectPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
            $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('M3', '客服部');
            $objectPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
            $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('N3', '文案编码');
            $objectPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
            $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('O3', '客服订单号');
            $objectPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
            $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('P3', '方便联系时间');
            $objectPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
            $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('Q3', '发货时间');
            $objectPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
            $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('R3', '订单状态');
            //设置边框
            $objectPHPExcel->getActiveSheet()->getStyle('A3:R3')
                ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objectPHPExcel->getActiveSheet()->getStyle('A3:R3')
                ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objectPHPExcel->getActiveSheet()->getStyle('A3:R3')
                ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objectPHPExcel->getActiveSheet()->getStyle('A3:R3')
                ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objectPHPExcel->getActiveSheet()->getStyle('A3:R3')
                ->getBorders()->getVertical()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

            //Q 列为文本
            $objectPHPExcel->getActiveSheet()->getStyle('B')->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $objectPHPExcel->getActiveSheet()->getStyle('Q')->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            //设置颜色
            $objectPHPExcel->getActiveSheet()->getStyle('A3:R3')->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF66CCCC');

            foreach ($data['list'] as $k => $v) {
                $best_time = vars::get_field_str('best_time', $v['best_time']);
                $delivery_date = $v['delivery_date'] > 1509465600 ? date('Y-m-d', $v['delivery_date']) : '-';
                //明细的输出
                $objectPHPExcel->getActiveSheet()->setCellValue('A' . ($n + 4), $v['id']);//ID
                $objectPHPExcel->getActiveSheet()->protectCells('A' . ($n + 4), 'PHPExcel');
                $objectPHPExcel->getActiveSheet()->setCellValue('B' . ($n + 4), date('Y-m-d H:i:s', $v['add_time']));//下单时间
                $objectPHPExcel->getActiveSheet()->setCellValue('C' . ($n + 4), ' ' . $v['order_code']);//订单号
                $objectPHPExcel->getActiveSheet()->setCellValue('D' . ($n + 4), $v['package_name']);//商品名
                $objectPHPExcel->getActiveSheet()->setCellValue('E' . ($n + 4), $data['goodsNames'][$v['goods_id']]);//商品
                $objectPHPExcel->getActiveSheet()->setCellValue('F' . ($n + 4), $v['package_price']);//下单金额
                $objectPHPExcel->getActiveSheet()->setCellValue('G' . ($n + 4), $v['real_price']);//实际金额
                $objectPHPExcel->getActiveSheet()->setCellValue('H' . ($n + 4), $v['real_name']);//客户姓名
                $objectPHPExcel->getActiveSheet()->setCellValue('I' . ($n + 4), $v['mobile']);//电话
                $objectPHPExcel->getActiveSheet()->setCellValue('J' . ($n + 4), $v['province'] . ' ' . $v['city'] . ' ' . $v['region'] . ' ' . $v['detail_area']);//地址
                $objectPHPExcel->getActiveSheet()->setCellValue('K' . ($n + 4), $v['remark']);//留言
                $objectPHPExcel->getActiveSheet()->setCellValue('L' . ($n + 4), $data['channelCodes'][$v['channel_id']]);//渠道编码
                $objectPHPExcel->getActiveSheet()->setCellValue('M' . ($n + 4), $data['csNames'][$v['customer_service_id']]);//客服部
                $objectPHPExcel->getActiveSheet()->setCellValue('N' . ($n + 4), $v['article_code']);//文案编码
                $objectPHPExcel->getActiveSheet()->setCellValue('O' . ($n + 4), $v['corder_code']);//客服订单号
                $objectPHPExcel->getActiveSheet()->setCellValue('P' . ($n + 4), $best_time);//方便联系时间
                $objectPHPExcel->getActiveSheet()->setCellValue('Q' . ($n + 4), $delivery_date);//发货时间
                $objectPHPExcel->getActiveSheet()->getCell('R' . ($n + 4))->getDataValidation()
                    ->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)
                    ->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)
                    ->setAllowBlank(false)
                    ->setShowInputMessage(true)
                    ->setShowErrorMessage(true)
                    ->setShowDropDown(true)
                    ->setErrorTitle('请选择状态')
                    ->setError('您输入的值不在下拉框列表内.')
                    ->setPromptTitle('订单状态')
                    ->setFormula1('"未处理,交易成功,拒收"');
                $objectPHPExcel->getActiveSheet()->setCellValue('R' . ($n + 4), vars::get_field_str('order_status', $v['order_status']));//订单状态
                $n = $n + 1;
            }

            $objectPHPExcel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
            $objectPHPExcel->getActiveSheet()->getPageSetup()->setVerticalCentered(false);

            ob_end_clean();
            ob_start();

            header('Content-Type : application/vnd.ms-excel');
            header('Content-Disposition:attachment;filename="' . '未导出订单列表-' . date("Ymj") . '.xls"');
            $objWriter = PHPExcel_IOFactory::createWriter($objectPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        } else {
            $file_name = '订单列表-' . date('Ymd', time());
            $headlist = array('id', '下单时间', '订单号', '下单商品', '商品', '下单金额', '实际金额', '客户姓名', '电话', '地址', '留言', '渠道编码', '客服部', '文案编码', '客服订单号', '方便联系时间', '发货时间', '订单状态', '导出时间');
            $page = $this->getOrderList(1);

            $data = $page['listdata'];
            $row = array();
            $count = count($data['list']);
            for ($i = 0; $i < $count; $i++) {
                if ($data['list'][$i]['is_export'] == 0) {
                    $export_date = "未导";
                } else {
                    $export_date = date('Y-m-d H:i', $data['list'][$i]['export_date']);
                }
                $row[$i] = array(
                    $data['list'][$i]['id'],//id
                    date('Y-m-d H:i:s', $data['list'][$i]['add_time']),//下单时间
                    $data['list'][$i]['order_code'] . "\t",//订单号
                    $data['packageNames'][$data['list'][$i]['package_id']],//下单商品
                    $data['goodsNames'][$data['list'][$i]['goods_id']],//商品
                    $data['list'][$i]['package_price'],//下单金额
                    $data['list'][$i]['real_price'],//实际金额
                    $data['list'][$i]['real_name'],//客户姓名
                    $data['list'][$i]['mobile'] . "\t",//电话
                    $data['list'][$i]['province'] . ' ' . $data['list'][$i]['province'] . ' ' . $data['list'][$i]['city'] . ' ' . $data['list'][$i]['region'] . ' ' . $data['list'][$i]['detail_area'],//地址
                    $data['list'][$i]['remark'],//留言
                    $data['channelCodes'][$data['list'][$i]['channel_id']],//渠道编码
                    $data['csNames'][$data['list'][$i]['customer_service_id']],//客服部
                    $data['list'][$i]['article_code'],//文案编码
                    $data['list'][$i]['corder_code'] . "\t",//客服订单号
                    vars::get_field_str('best_time', $data['list'][$i]['best_time']),//方便联系时间
                    $data['list'][$i]['delivery_date'] ? date('Y-m-d', $data['list'][$i]['delivery_date']) : '',//发货时间
                    vars::get_field_str('order_status', $data['list'][$i]['order_status']),//订单状态
                    $export_date,//导出时间
                );
                foreach ($row[$i] as $key => $value) {
                    $row[$i][$key] = iconv('utf-8', 'gbk', $value);
                }
            }
            helper::downloadCsv($headlist, $row, $file_name);
        }
    }

    /**
     * 回传导入
     * author: yjh
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
                if ($phpexcel->getCell('A1')->getValue() == 'PassBack') {
                    //第三行开始处理数据
                    if ($total_line > 3) {
                        $insertData = array();
                        for ($row = 4; $row <= $total_line; $row++) {
                            $data = array();
                            for ($column = 'A'; $column <= $total_column; $column++) {
                                $data[] = trim($phpexcel->getCell($column . $row)->getValue());
                            }
                            if ((empty($data[0]))) continue;
                            //数据过滤
                            $insertData = $this->dataFilter($data, $row);
                        }
                        //插入数据
                        $this->dataInsert($insertData);
                    } else $this->msg(array('state' => 0, 'msgwords' => '导入文件没有数据！'));
                } else $this->msg(array('state' => 0, 'msgwords' => '不是订单回传Excel！'));
            } else $this->msg(array('state' => 0, 'msgwords' => '请选择要导入的xls文件！'));

        }
    }

    /**
     * 导入数据过滤
     * @param array $data
     * @param $row
     * author: yjh
     */
    private function dataFilter(array $data, $row)
    {
        static $rightData = array();


        /******1、判断数据是否为空********/
        if (empty($data[0])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的订单号ID为空！'));
        if (empty($data[14])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的客服订单号未填写！'));
        if (empty($data[17])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的订单状态未选择！'));
        $r = vars::get_value('order_status', $data[17]);
        if ($r == '' && $r != 0) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的订单状态' . $data[11] . '不存在！'));
        if ($r == 1 && !strtotime($data[16])) {
            $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的发货时间未填写正确！'));
        } elseif ($r == 1 && strtotime($data[16]) > time()) {
            $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的发货时间超过当前时间！'));
        } elseif ($r == 1 && strtotime($data[16]) < strtotime(date('Ymd',strtotime($data[1])))) {
            $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '发货日期不能早于下单日期！'));
        } else {
            if (empty($data[16])) $data[16] = 0;
        }
        $rightData[] = array(
            'id' => $data[0],
            'real_price' => $data[6],
            'corder_code' => $data[14],
            'order_status' => $r,
            'delivery_date' => strtotime($data[16]),
        );
        return $rightData;

    }

    /**
     * 订单回传修改
     * @param $data
     * author: yjh
     */
    private function dataInsert($data)
    {
        $key = $key_pkg = array();
        foreach ($data as $val) {
            $info = OrderManage::model()->findByPk($val['id']);
            $last_order_status = $info->order_status;
            $last_delivery_date = $info->delivery_date;
            $aInfo = OrderAssistant::model()->find('order_id=' . $val['id']);
            $goodsInfo = OrderGoodsManage::model()->find('order_id=' . $val['id']);
            if (!$info || !$aInfo || !$goodsInfo) continue;
            $info->corder_code = $val['corder_code'];
            $info->order_status = $val['order_status'];
            $info->delivery_date = $val['delivery_date'];
            $info->d_date = date('Ymd', $val['delivery_date']);
            $info->is_back = 1;
            $info->save();
            if ($val['real_price'] === '') $goodsInfo->real_price = $info->package_price;
            else $goodsInfo->real_price = $val['real_price'];
            $goodsInfo->save();

            if ($last_order_status == 1) {
                if ($last_delivery_date < strtotime(date('Ymd'))) {
                    $key[] = $last_delivery_date . '_' . $aInfo->weixin_id . '_' . $info->channel_id;
                    $key_pkg[] = $last_delivery_date . '_' . $info->customer_service_id . '_' . $goodsInfo->package_id;
                }
            }
            if ($info->order_status == 1) {
                if ($info->delivery_date < strtotime(date('Ymd'))) {
                    $key[] = $info->delivery_date . '_' . $aInfo->weixin_id . '_' . $info->channel_id;
                    $key_pkg[] = $info->delivery_date . '_' . $info->customer_service_id . '_' . $goodsInfo->package_id;
                }
            }
            if ($info->o_date < date('Ymd')) {
                $key[] = strtotime($info->o_date) . '_' . $aInfo->weixin_id . '_' . $info->channel_id;
            }
        }
        $key = array_unique($key);
        $key_pkg = array_unique($key_pkg);
        $this->updateDayOrders($key);
        $this->updatePkgOrders($key_pkg);
        $this->logs("回传订单成功");
        //成功跳转提示
        $this->msg(array('state' => 1, 'msgwords' => "回传订单成功"));

    }

    /**
     * 批量修改按天缓存订单表
     * @param array $keyArr
     * author: yjh
     */
    private function updateDayOrders(array $keyArr)
    {
        foreach ($keyArr as $item) {
            if (empty($item)) continue;
            $t_arr = explode('_', $item);
            $stat_date = $t_arr[0];
            $weixin_id = $t_arr[1];
            $channel_id = $t_arr[2];
            $condition = ' stat_date=' . $stat_date . ' and weixin_id=' . $weixin_id . ' and channel_id=' . $channel_id;
            $info = OrdersSortByDay::model()->find($condition);
            $sql = 'SELECT b.promotion_id,b.partner_id,b.channel_id,b.customer_service_id,a.weixin_id,a.wechat_id,c.package_id,goods_id,business_type,charging_type,promotion_staff_id as tg_uid,count(*) AS out_count,sum(c.real_price) as delivery_money
                 from order_assistant as a left join order_manage as b on a.order_id=b.id left join order_goods_manage as c on a.order_id=c.order_id
                 where order_status=1 and  d_date= ' . date('Ymd', $stat_date) . ' and weixin_id=' . $weixin_id . ' and channel_id=' . $channel_id . ' group by a.weixin_id,b.channel_id';
            $outInfo = Yii::app()->order_db->createCommand($sql)->queryAll();
            $sql = 'SELECT b.promotion_id,b.partner_id,b.channel_id,b.customer_service_id,a.weixin_id,a.wechat_id,c.package_id,goods_id,business_type,charging_type,promotion_staff_id as tg_uid,count(*) AS sout_count,sum(c.real_price) as sdelivery_money
                 from order_assistant as a left join order_manage as b on a.order_id=b.id left join order_goods_manage as c on a.order_id=c.order_id
                 where order_status=1 and o_date = ' . date('Ymd', $stat_date) . ' and weixin_id=' . $weixin_id . ' and channel_id=' . $channel_id . ' group by a.weixin_id,b.channel_id';
            $sInfo = Yii::app()->order_db->createCommand($sql)->queryAll();
            if (!$outInfo && !$sInfo) continue;
            if ($outInfo) {
                $outInfo = $outInfo[0];
            } else {
                $outInfo = $sInfo[0];
                $outInfo['out_count'] = 0;
                $outInfo['delivery_money'] = 0;
            }
            if ($sInfo) {
                $sInfo = $sInfo[0];
            } else {
                $sInfo['sout_count'] = 0;
                $sInfo['sdelivery_money'] = 0;
            }

            if ($info) {
                $info->out_count = $outInfo['out_count'];
                $info->delivery_money = $outInfo['delivery_money'];
                $info->sout_count = $sInfo['sout_count'];
                $info->sdelivery_money = $sInfo['sdelivery_money'];
                $info->save();
            } else {
                $info = new OrdersSortByDay();
                $info->promotion_id = $outInfo['promotion_id'];
                $info->partner_id = $outInfo['partner_id'];
                $info->channel_id = $outInfo['channel_id'];
                $info->customer_service_id = $outInfo['customer_service_id'];
                $info->weixin_id = $outInfo['weixin_id'];
                $info->wechat_id = $outInfo['wechat_id'];
                $info->package_id = $outInfo['package_id'];
                $info->goods_id = $outInfo['goods_id'];
                $info->business_type = $outInfo['business_type'];
                $info->charging_type = $outInfo['charging_type'];
                $info->tg_uid = $outInfo['tg_uid'];
                $info->in_count = 0;
                $info->sout_count = 0;
                $info->sdelivery_money = 0;
                $info->stat_date = $stat_date;
                $info->out_count = $outInfo['out_count'];
                $info->delivery_money = $outInfo['delivery_money'];
                $info->save();
            }
        }
        return true;
    }

    /**
     * 批量修改按下单商品缓存订单表
     * @param array $keyArr
     * author: yjh
     */
    private function updatePkgOrders(array $keyArr)
    {
        foreach ($keyArr as $item) {

            if (empty($item)) continue;
            $t_arr = explode('_', $item);
            $stat_date = $t_arr[0];
            $customer_service_id = $t_arr[1];
            $package_id = $t_arr[2];
            $condition = ' stat_date=' . $stat_date . ' and package_id=' . $package_id . ' and customer_service_id=' . $customer_service_id;
            $info = OrdersSortByPkg::model()->find($condition);

            $sql = 'SELECT a.package_id,b.customer_service_id,count(*) AS out_count,sum(a.real_price) as delivery_money
                 from order_goods_manage as a  left join order_manage as b on a.order_id=b.id
                 where  order_status=1 and d_date = ' . date('Ymd', $stat_date) . ' and package_id=' . $package_id . ' and customer_service_id=' . $customer_service_id . '
                 group by a.package_id,b.customer_service_id';
            $outInfo = Yii::app()->order_db->createCommand($sql)->queryAll();
            if ($outInfo) {
                $outInfo = $outInfo[0];
            } else {
                $outInfo['out_count'] = 0;
                $outInfo['delivery_money'] = 0;
                $outInfo['package_id'] = $package_id;
                $outInfo['customer_service_id'] = $customer_service_id;
            }
            if ($info) {
                $info->out_count = $outInfo['out_count'];
                $info->delivery_money = $outInfo['delivery_money'];
                $info->save();
            } else {
                $info = new OrdersSortByPkg();
                $info->package_id = $outInfo['package_id'];
                $info->customer_service_id = $outInfo['customer_service_id'];
                $info->in_count = 0;
                $info->stat_date = $stat_date;
                $info->out_count = $outInfo['out_count'];
                $info->delivery_money = $outInfo['delivery_money'];
                $info->save();
            }
        }
        return true;
    }

    /**
     * 获取订单列表
     * @return mixed
     * author: yjh
     */
    private function getOrderList($is_download = 0)
    {
        $params['where'] = $this->getCondition();

        $params['join'] = "left join order_assistant as b on b.order_id=a.id
                           left join order_goods_manage as c on c.order_id=a.id
                         ";
        $params['order'] = "  order by a.id desc    ";
        $params['pagesize'] = $is_download == 0 ? 200 : 10000;
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $params['select'] = "a.id,goods_id,c.package_id,c.real_price,delivery_date,is_export,add_time,order_code,c.package_name,c.package_price,real_name,mobile,province,city,region,detail_area,remark,channel_id,customer_service_id,article_code,corder_code,best_time,order_status,export_date";

        $page['listdata'] = Dtable::model('order_manage')->orderdata($params);
        $page['listdata']['url'] = urlencode('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"]);
        $channelIds = implode(',', array_filter(array_unique(array_column($page['listdata']['list'], 'channel_id'))));
        $cslIds = implode(',', array_filter(array_unique(array_column($page['listdata']['list'], 'customer_service_id'))));
        $goodsIds = implode(',', array_filter(array_unique(array_column($page['listdata']['list'], 'goods_id'))));
        $packageIds = implode(',', array_filter(array_unique(array_column($page['listdata']['list'], 'package_id'))));

        if ($channelIds)
            $page['listdata']['channelCodes'] = Channel::model()->getChannelCodes($channelIds);
        if ($cslIds)
            $page['listdata']['csNames'] = CustomerServiceManage::model()->getCSNames($cslIds);
        if ($goodsIds)
            $page['listdata']['goodsNames'] = Goods::model()->getGoodsNames($goodsIds);
        if ($packageIds)
            $page['listdata']['packageNames'] = PackageManage::model()->getPackageNames($packageIds);
        return $page;
    }

    /**
     * 搜索条件
     * @return string
     * author: yjh
     */
    private function getCondition()
    {
        $condition = '';
        //导出勾选订单
        if ($this->get('ids') != '') {
            $condition .= "and(a.id in (" . $this->get('ids') . "))";
        } else {

            if ($this->get('start_date') || $this->get('end_date')) {
                if ($this->get('start_date') && $this->get('end_date')) {
                    if ($this->get('start_date') <= $this->get('end_date')) {
                        $start_date = strtotime($this->get('start_date'));
                        $end_date = strtotime($this->get('end_date'));
                        //起始，结束时间都有输入
                        $condition .= " and a.add_time between $start_date and $end_date ";
                    }
                } elseif ($this->get('start_date')) {
                    $start_date = strtotime($this->get('start_date'));
                    //只输入起始时间
                    $condition .= " and(a.add_time >= $start_date)";

                } elseif ($this->get('end_date')) {
                    $end_date = strtotime($this->get('end_date'));
                    // $end_date =  str_replace('-','',$this->get('end_date'));
                    //只输入结束时间
                    $condition .= " and(a.add_time <= $end_date)";
                }
            } else {
                $start_date = date('Ymd', time());
                $condition .= " and(a.o_date = $start_date)";
            }


            if($_SERVER['HTTP_HOST'] == yii::app()->params['customer_config']['domain']){
                $uid=Yii::app()->admin_user->uid;
                $ret = AdminUser::model()->find('csno='.$uid);
                $condition .= "and a.customer_service_id = " . $ret['csdepartment'] . "";
            }else{
                if ($this->get('csid') != '') $condition .= "and(a.customer_service_id = " . $this->get('csid') . ")";
            }

            if ($this->get('search_type') == 'channel_code' && $this->get('search_txt')) {
                $sql = "select id from `channel` where channel_code like '%" . $this->get('search_txt') . "%'";
                $channelIds = $this->query($sql);
                if ($channelIds) {
                    $channelIds = array_column($channelIds, 'id');
                    $condition .= " and(a.channel_id in (" . implode(',', $channelIds) . ")) ";
                } else
                    $condition .= " and(a.channel_id =0) ";
            } else if ($this->get('search_type') == 'article_code' && $this->get('search_txt')) {
                $condition .= " and(article_code like '%" . $this->get('search_txt') . "%') ";
            } else if ($this->get('search_type') == 'order_code' && $this->get('search_txt')) {
                $condition .= " and(order_code like '%" . $this->get('search_txt') . "%') ";
            } else if ($this->get('search_type') == 'corder_code' && $this->get('search_txt')) {
                $condition .= " and(corder_code like '%" . $this->get('search_txt') . "%') ";
            }

            if ($this->get('search_type2') == 'real_name' && $this->get('search_txt2')) {
                $condition .= " and(a.real_name like '%" . $this->get('search_txt2') . "%') ";
            } else if ($this->get('search_type2') == 'mobile' && $this->get('search_txt2')) {
                $condition .= " and(mobile like '%" . $this->get('search_txt2') . "%') ";
            }

            //筛选未导出订单
            if ($this->get('ckunexport')) {
                $condition .= " and a.is_export = 0  ";
            } else {
                //订单状态
                if ($this->get('is_export2') == 'unexport1' && $this->get('is_export2')) {
                    $condition .= " and a.is_export = 0  ";
                } else if ($this->get('is_export2') == 'export1' && $this->get('is_export2')) {
                    $condition .= " and a.is_export = 1  ";
                }
                if ($this->get('goods_name')) {
                    $sql = "select id from `goods` where goods_name like '%" . $this->get('goods_name') . "%'";
                    $goodsIds = $this->query($sql);
                    if ($goodsIds) {
                        $goodsIds = array_column($goodsIds, 'id');
                        $condition .= " and(a.goods_id in (" . implode(',', $goodsIds) . ")) ";
                    } else
                        $condition .= " and(a.goods_id =0) ";
                }
                if ($this->get('package_name')) {
                    $sql = "select id from `package_manage` where name like '%" . $this->get('package_name') . "%'";
                    $packageIds = $this->query($sql);
                    if ($packageIds) {
                        $packageIds = array_column($packageIds, 'id');
                        $condition .= " and(c.package_id in (" . implode(',', $packageIds) . ")) ";
                    } else
                        $condition .= " and(c.package_id =0) ";
                }
                if ($this->get('de_sdate') || $this->get('de_edate')) {
                    if ($this->get('de_sdate') && $this->get('de_edate')) {
                        if ($this->get('de_sdate') <= $this->get('de_edate')) {
                            $de_sdate = str_replace('-', '', $this->get('de_sdate'));
                            $de_edate = str_replace('-', '', $this->get('de_edate'));
                            //起始，结束时间都有输入
                            $condition .= " and(a.d_date between $de_sdate and $de_edate)";
                        }
                    } elseif ($this->get('de_sdate')) {
                        $de_sdate = str_replace('-', '', $this->get('de_sdate'));
                        //只输入起始时间
                        $condition .= " and(a.d_date >= $de_sdate)";

                    } elseif ($this->get('de_edate')) {
                        $de_edate = str_replace('-', '', $this->get('de_edate'));
                        //只输入结束时间
                        $condition .= " and(a.d_date <= $de_edate)";
                    }
                }
            }
        }

        return $condition;
    }

}