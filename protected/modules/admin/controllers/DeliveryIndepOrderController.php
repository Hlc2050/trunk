<?php

/**
 * 独立订单发货
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/12/28
 * Time: 15:13
 */
class DeliveryIndepOrderController extends AdminController
{

    /**
     * 独立订单发货列表
     * author: yjh
     */
    public function actionIndex()
    {
        $page =  $this->getExportData();
        $this->render('index', array('page' => $page));
    }

    /**
     * 修改独立订单发货数据
     * author: yjh
     */
    public function actionEdit()
    {
        $page = array();
        $id = $this->get('id');
        $info = DeliveryIndepOrderManage::model()->findByPk($id);
        if (!$_POST) {
            $page['info'] = Dtable::toArr($info);
            $page['info']['chargingTypeList'] = Dtable::toArr(BusinessTypeRelation::model()->findAll('bid=:bid', array(':bid' => $page['info']['business_type'])));
            foreach ($page['info']['chargingTypeList'] as $k => $v) {
                $page['info']['chargingTypeList'][$k]['cname'] = vars::get_field_str('charging_type', $v['charging_type']);
            }
            $this->render('update', array('page' => $page));
            exit;
        }
        $old_date = $info->delivery_date ;
        $old_money = $info->delivery_money;
        $old_customer = $info->customer_service_id;
        //业务类型 计费方式 客服部 商品
        $info->business_type = $this->post('business_type');
        if (!$info->business_type) $this->msg(array('state' => 0, 'msgwords' => '没有选择业务类型！'));
        $info->charging_type = $this->post('charging_type');
        if (!$info->charging_type) $this->msg(array('state' => 0, 'msgwords' => '没有选择计费方式！'));
        $info->customer_service_id = $this->post('customer_service_id');
        if (!$info->customer_service_id) $this->msg(array('state' => 0, 'msgwords' => '没有选择客服部！'));
        $info->goods_id = $this->post('goods_id');
        if (!$info->goods_id) $this->msg(array('state' => 0, 'msgwords' => '没有选择商品！'));

        $info->delivery_date = $this->post('delivery_date');
        $info->delivery_count = $this->post('delivery_count');
        $info->delivery_money = $this->post('delivery_money');
        if (!$info->delivery_date) $this->msg(array('state' => 0, 'msgwords' => '没有填发货时间！'));
        $info->delivery_date = strtotime($info->delivery_date);
        if (!$info->delivery_money) $this->msg(array('state' => 0, 'msgwords' => '没有填发货金额！'));
        if ($info->delivery_count && !is_numeric($info->delivery_count)) $this->msg(array('state' => 0, 'msgwords' => '单量要填数字！'));
        if (!is_numeric($info->delivery_money)) $this->msg(array('state' => 0, 'msgwords' => '发货金额要填数字！'));

        $info->update_time = time();
        $dbresult = $info->save();
        $msgarr = array('state' => 1, 'url' => $this->get('backurl'));
        $logs = "修改了独立订单发货信息：" . $id;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            // 修改产值统计数据
            $new_data = array('delivery_date'=>$info->delivery_date,'customer_service_id'=>$info->customer_service_id, 'tg_uid'=>$info->tg_uid);
            $old_data = array('delivery_date'=>$old_date,'customer_service_id'=>$old_customer, 'tg_uid'=>$info->tg_uid);
            DataPracticalOutput::model()->editOutput($new_data,$old_data,2);
            $this->logs($logs);
            $this->msg($msgarr);
        }
    }

    /**
     * 批量删除订单发货
     * author: yjh
     */
    public function actionDel()
    {
        $ids = $this->get('ids');
        $idArr = explode(',', $ids);
        $log = '';
        $msgwords = '批量独立订单发货成功</br>';
        $del_arr = array();
        foreach ($idArr as $val) {
            $info = DeliveryIndepOrderManage::model()->findByPk($val);
            $del_arr[] = array(
                'customer_service_id' => $info->customer_service_id,
                'tg_uid' => $info->tg_uid,
                'delivery_date' => $info->delivery_date,
            );
            $info->delete();
            $log .= "删除了独立订单发货：ID:" . $info->id . ",";
            $msgwords .= "删除了独立订单发货：ID:" . $info->id . "</br>";
        }
        DataPracticalOutput::model()->updatePracticalOutput($del_arr,2);
        $this->logs($log);
        $this->msg(array('state' => 1, 'msgwords' => $msgwords));
    }

    /**
     * 模板下载
     * author: yjh
     */
    public function actionTemplate()
    {
        $colums=array('下单日期','微信号ID','单量','发货金额（元）');
        $file_name='独立订单发货导入模板.xls';
        $txt="导入注意事项：发货日期格式'" . date('Y/m/d', time()) . "',微信号ID必须后台已添加！";
        helper::downloadExcel($colums,array(),$txt,$file_name);
    }

    /**
     * excel批量导入独立发货列表
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
     */
    private function dataFilter(array $data, $row)
    {
        static $rightData = array();

        /******1、判断数据是否为空********/
        if (empty($data[0])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的发货日期为空！'));
        if (empty($data[1])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的微信号为空！'));
        if (empty($data[2])) $data[2] = 0;
        if (empty($data[3]) && $data[3] != '0' ) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的发货金额为空！'));
        /******2、判断数据冲突情况**********/
        //发货日期判断
        $delivery_date = strtotime($data[0]) ? strtotime($data[0]) : false;
        if ($delivery_date == false) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的日期' . $data[0] . '格式不正确！'));

        //微信号判断，还要判断和之前的数据不重复
        $weChatInfo = $this->toArr(WeChat::model()->find('wechat_id = :wechat_id', array(':wechat_id' => $data[1])));
        if (!$weChatInfo) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的微信号ID' . $data[1] . '不存在！'));

        if (CustomerServiceManage::model()->findByPk($weChatInfo['customer_service_id'])->status == 0)
            $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的微信号ID' . $data[1] . '不属于独立客服部！'));

        //单量判断
        $delivery_count = is_numeric($data[2]) ? $data[2] : false;
        if ($delivery_count === false) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的单量不是数字！'));

        //发货金额判断
        $delivery_money = is_numeric($data[3]) ? $data[3] : false;
        if ($delivery_money === false && $data[3] != '0') $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的发货金额不是数字！'));

        $rightData[] = array(
            'weixin_id' => $weChatInfo['id'],
            'wechat_id' => $weChatInfo['wechat_id'],
            'business_type' => $weChatInfo['business_type'],
            'charging_type' => $weChatInfo['charging_type'],
            'tg_uid' => $weChatInfo['promotion_staff_id'],
            'customer_service_id' => $weChatInfo['customer_service_id'],
            'department_id' => $weChatInfo['department_id'],
            'goods_id' => $weChatInfo['goods_id'],
            'delivery_count' => $delivery_count,
            'delivery_money' => $delivery_money,
            'delivery_date' => $delivery_date,
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
        $table_name='delivery_indep_order_manage';
        $rows=array_keys($data[0]);
        $ret=helper::batch_insert_data($table_name,$rows,$data);
        if($ret) {
            //写入发货金额统计表
            DataPracticalOutput::model()->updatePracticalOutput($data);
            $this->logs("批量导入独立订单发货成功");
            $this->msg(array('state' => 1, 'msgwords' => "批量导入独立订单发货成功"));
        }else{
            $this->msg(array('state' => 0, 'msgwords' => '批量导入独立订单发货失败'));
        }
//        foreach ($data as $key => $val) {
//            $info = new DeliveryIndepOrderManage();
//            $info->wechat_id = $val['wechat_id'];
//            $info->weixin_id = $val['weixin_id'];
//            $info->business_type = $val['business_type'];
//            $info->charging_type = $val['charging_type'];
//            $info->goods_id = $val['goods_id'];
//            $info->delivery_date = $val['delivery_date'];
//            $info->delivery_count = $val['delivery_count'];
//            $info->delivery_money = $val['delivery_money'];
//            $info->tg_uid = $val['tg_uid'];
//            $info->customer_service_id = $val['customer_service_id'];
//            $info->department_id = $val['department_id'];
//            $info->update_time = time();
//            $info->create_time = time();
//            $info->save();
//        }
//        $this->logs("批量导入独立订单发货");
//
//        $this->msg(array('state' => 1, 'msgwords' => "批量导入独立订单发货成功"));

    }


    /**
     * 导出
     */
    public function actionExport()
    {
        $file_name = '独立订单发货表-' . date('Ymd', time());
        $headlist = array('id','下单日期','微信号ID','客服部','业务','计费方式','商品','推广人员','单量','发货金额（元）');
        $page = $this->getExportData(1);
        $row = array();
        $row[0] = array('-','-','-','-','-','-','-',iconv('utf-8', 'gbk', '合计'),$page['listdata']['delivery_count'],$page['listdata']['delivery_money']);
        $data = $page['listdata']['list'];
        $count = count($data);
        for ($i = 0; $i < $count; $i++) {
            $k = $i + 1;
            $row[$k] = array(
                $data[$i]['id'],
                date('Y-m-d', $data[$i]['delivery_date']),
                $data[$i]['wechat_id'],
                $page['listdata']['bNames'][$data[$i]['business_type']],
                vars::get_field_str('charging_type', $data[$i]['charging_type']),
                $page['listdata']['csNames'][$data[$i]['customer_service_id']],
                $page['listdata']['goodsNames'][ $data[$i]['goods_id']],
                $page['listdata']['userNames'][$data[$i]['tg_uid']],
                $data[$i]['delivery_count'],
                $data[$i]['delivery_money'],

            );
            foreach ($row[$k] as $key => $value) {
                $row[$k][$key] = iconv('utf-8', 'gbk', $value);
            }

        }
        helper::downloadCsv($headlist, $row, $file_name);

    }
    private function getExportData($is_export=0)
    {
        $params['where'] = '';
        $params['where'] .= $this->getTimeIntervalSql('a.delivery_date', $this->get('start_delivery_date'), $this->get('end_delivery_date'));
        if ($this->get('wechat_id') != '') $params['where'] .= " and(a.wechat_id like '%" . $this->get('wechat_id') . "%') ";
        if ($this->get('delivery_count') != '') $params['where'] .= " and(a.delivery_count = " . intval($this->get('delivery_count')) . ") ";
        if ($this->get('delivery_money') != '') $params['where'] .= " and(a.delivery_money = " . intval($this->get('delivery_money')) . ") ";
        // 客服部查询
        if ($this->get('csid') != '') $params['where'] .= " and (a.customer_service_id = ".intval($this->get('csid')).") ";
        // 商品查询
        if ($this->get('goods_id') != '') $params['where'] .= " and (a.goods_id = ".intval($this->get('goods_id')).") ";
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
        //$params['debug']=1;
        $page['listdata'] = Dtable::model(DeliveryIndepOrderManage::model()->tableName())->listdata($params);
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
        $sql="select sum(a.delivery_money) as delivery_money,sum(a.delivery_count) as delivery_count from delivery_indep_order_manage as a ".$params['join']." where 1 ".$params['where'];
        $totalInfo = Yii::app()->db->createCommand($sql)->queryAll();
        $page['listdata']['delivery_money']=$totalInfo[0]['delivery_money'];
        $page['listdata']['delivery_count']=$totalInfo[0]['delivery_count'];
        return $page;
    }
}