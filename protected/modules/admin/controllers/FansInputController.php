<?php

/**
 * 进粉录入
 * Created by PhpStorm.
 * User: yjh
 * Date: 2017/01/05
 * Time: 15:13
 */
class FansInputController extends AdminController
{

    /**
     * 进粉录入列表
     * author: yjh
     */
    public function actionIndex()
    {
        $page = $this->getExportData();
        $this->render('index', array('page' => $page));
    }

    /**
     * 修改进粉
     * author: yjh
     */
    public function actionEdit()
    {
        $page = array();
        $id = $this->get('id');
        $info = FansInputManage::model()->findByPk($id);
        if (!$_POST) {
            $page['info'] = Dtable::toArr($info);
            $page['info']['chargingTypeList'] = Dtable::toArr(BusinessTypeRelation::model()->findAll('bid=:bid', array(':bid' => $page['info']['business_type'])));
            foreach ($page['info']['chargingTypeList'] as $k => $v) {
                $page['info']['chargingTypeList'][$k]['cname'] = vars::get_field_str('charging_type', $v['charging_type']);
            }
            $this->render('update', array('page' => $page));
            exit;
        }
        $old_date = $info->addfan_date;
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

        $info->addfan_date = $this->post('addfan_date');
        $info->addfan_count = ($this->post('addfan_count'));
        $info->total_fans = $this->post('total_fans') === '' ? null : ($this->post('total_fans'));
        $info->del_black = $this->post('del_black');
        $info->brush_fans = $this->post('brush_fans');
        $info->gender_dif_fans = $this->post('gender_dif_fans');
        $info->not_reply_fans = $this->post('not_reply_fans');
        $info->age_dif_fans = $this->post('age_dif_fans');
        $info->disease_fans = $this->post('disease_fans');

        if (!$info->addfan_date) $this->msg(array('state' => 1, 'msgwords' => '没有填进粉时间！'));
        $info->addfan_date = strtotime($info->addfan_date);
        if (!$info->addfan_count) $this->msg(array('state' => 1, 'msgwords' => '没有填进粉量！'));
        if ($info->total_fans && !is_numeric($info->total_fans)) $this->msg(array('state' => 0, 'msgwords' => '累计进粉量要填数字！'));
        if (!is_numeric($info->del_black)) $this->msg(array('state' => 0, 'msgwords' => '删除拉黑要填数字！'));
        if (!is_numeric($info->brush_fans)) $this->msg(array('state' => 0, 'msgwords' => '刷粉要填数字！'));
        if (!is_numeric($info->gender_dif_fans)) $this->msg(array('state' => 0, 'msgwords' => '性别不符合粉要填数字！'));
        if (!is_numeric($info->not_reply_fans)) $this->msg(array('state' => 0, 'msgwords' => '不回复粉要填数字！'));
        if (!is_numeric($info->age_dif_fans)) $this->msg(array('state' => 0, 'msgwords' => '年龄不符合要填数字！'));
        if (!is_numeric($info->disease_fans)) $this->msg(array('state' => 0, 'msgwords' => '疾病粉要填数字！'));
        $info->update_time = time();
        $dbresult = $info->save();
        $msgarr = array('state' => 1, 'url' => $this->get('backurl'));
        $logs = "修改了进粉信息：" . $id;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            // 修改进粉统计数据
            $new_data = array('addfan_date'=>$info->addfan_date,'customer_service_id'=>$info->customer_service_id, 'tg_uid'=>$info->tg_uid);
            $old_data = array('addfan_date'=>$old_date,'customer_service_id'=>$old_customer, 'tg_uid'=>$info->tg_uid);
            DataPracticalFans::model()->editFans($new_data,$old_data);
            $this->logs($logs);
            $this->msg($msgarr);
        }
    }

    /**
     * 批量删除进粉
     * author: yjh
     */
    public function actionDel()
    {
        $ids = $this->get('ids');
        $idArr = explode(',', $ids);
        $log = '';
        $msgwords = '批量删除进粉成功</br>';
        $del_arr = array();
        foreach ($idArr as $val) {
            $info = FansInputManage::model()->findByPk($val);
            $del_arr[] = array(
                'customer_service_id' => $info->customer_service_id,
                'tg_uid' => $info->tg_uid,
                'addfan_date' => $info->addfan_date,
            );
            $info->delete();
            $log .= "删除了进粉信息：ID:" . $info->id . ",";
            $msgwords .= "删除了进粉信息：ID:" . $info->id . "</br>";
        }
        DataPracticalFans::model()->updatePracticalFans($del_arr);
        $this->logs($log);
        $this->msg(array('state' => 1, 'msgwords' => $msgwords));
    }

    /**
     * 模板下载
     * author: yjh
     */
    public function actionTemplate()
    {
        $colums = array('加粉日期', '微信号ID', '进粉量', '累计进粉量', '删除拉黑', '刷粉', '性别不符合粉', '不回复粉', '年龄不符合', '疾病粉');
        $file_name = '进粉录入模板.xls';
        $txt = "导入注意事项：加粉日期格式'" . date('Y/m/d', time()) . "',微信号ID必须后台已添加！";
        helper::downloadExcel($colums, array(), $txt, $file_name);
    }

    /**
     * excel批量导入进粉列表
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
                        if ($data[0] != null) {
                            //数据过滤
                            $insertData = $this->dataFilter($data, $row);
                        } else {
                            continue;
                        }
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
//        my_print($data);die;
        /******1、判断数据是否为空********/
        if (empty($data[0])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的加粉日期为空！'));
        if (empty($data[1])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的微信号为空！'));
        if (empty($data[2]) && $data[2] != '0') $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的进粉量为空！'));
        if (empty($data[3])) $data[3] = 0;

        /******2、判断数据冲突情况**********/
        //加粉日期判断
        $addfan_date = strtotime($data[0]) ? strtotime($data[0]) : false;
        if ($addfan_date === false) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的日期' . $data[0] . '格式不正确！'));

        //微信号判断
        $weChatInfo = $this->toArr(WeChat::model()->find('wechat_id = :wechat_id', array(':wechat_id' => $data[1])));
        if (!$weChatInfo) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的微信号ID' . $data[1] . '不存在！'));

        //进粉量判断
        $addfan_count = is_numeric($data[2]) ? $data[2] : false;
        if ($addfan_count === false && $data[2] != '0') $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的进粉量不是数字！'));

        //累计粉丝量判断
        $total_fans = is_numeric($data[3]) ? $data[3] : false;
        if ($total_fans === false) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的累计粉丝量不是数字！'));
        //删除拉黑
        if ($data[4] == null) {
            $data[4] = "0";
        } else {
            $del_black = is_numeric($data[4]) ? $data[4] : false;
            if ($del_black === false) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的删除拉黑不是数字！'));
        }
        //刷粉
        if ($data[5] == null) {
            $data[5] = "0";
        } else {
            $brush_fans = is_numeric($data[5]) ? $data[5] : false;
            if ($brush_fans === false) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的刷粉不是数字！'));
        }
        //性别不符合粉
        if ($data[6] == null) {
            $data[6] = "0";
        } else {
            $gender_dif_fans = is_numeric($data[6]) ? $data[6] : false;
            if ($gender_dif_fans === false) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的性别不符合粉不是数字！'));
        }
        //不回复粉
        if ($data[7] == null) {
            $data[7] = "0";
        } else {
            $not_reply_fans = is_numeric($data[7]) ? $data[7] : false;
            if ($not_reply_fans === false) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的不回复粉不是数字！'));
        }
        //年龄不符合粉
        if ($data[8] == null) {
            $data[8] = "0";
        } else {
            $age_dif_fans = is_numeric($data[8]) ? $data[8] : false;
            if ($age_dif_fans === false) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的年龄不符合粉不是数字！'));
        }
        //疾病粉
        if ($data[9] == null) {
            $data[9] = "0";
        } else {
            $disease_fans = is_numeric($data[9]) ? $data[9] : false;
            if ($disease_fans === false) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的疾病粉不是数字！'));
        }

        $rightData[] = array(
            'weixin_id' => $weChatInfo['id'],//ID
            'wechat_id' => $weChatInfo['wechat_id'],//微信号ID
            'business_type' => $weChatInfo['business_type'],//业务
            'charging_type' => $weChatInfo['charging_type'],//计费
            'customer_service_id' => $weChatInfo['customer_service_id'],//客服部
            'tg_uid' => $weChatInfo['promotion_staff_id'],//推广人员
            'department_id' => $weChatInfo['department_id'],//部门
            'goods_id' => $weChatInfo['goods_id'],//商品
            'total_fans' => $total_fans,//累计粉丝量
            'addfan_date' => $addfan_date,//加粉日期
            'addfan_count' => $addfan_count,//进粉量
            'del_black' => $del_black,//删除拉黑
            'brush_fans' => $brush_fans,//刷粉
            'gender_dif_fans' => $gender_dif_fans, //性别不符合粉
            'not_reply_fans' => $not_reply_fans,//不回复粉
            'age_dif_fans' => $age_dif_fans,//年龄不符合
            'disease_fans' => $disease_fans,//疾病粉
            'update_time' => time(),
            'create_time' => time(),

        );
        return $rightData;
    }

    /**
     * 循环插入插入进粉
     * author: yjh
     */
    private function dataInsert($data)
    {
        $table_name = 'fans_input_manage';
        $rows = array_keys($data[0]);
        $ret = helper::batch_insert_data($table_name, $rows, $data);
        if ($ret) {
            DataPracticalFans::model()->updatePracticalFans($data);
            $this->logs("批量导入进粉成功");
            $this->msg(array('state' => 1, 'msgwords' => "批量导入进粉成功"));
        } else {
            $this->msg(array('state' => 0, 'msgwords' => '批量导入进粉失败'));
        }
//        foreach ($data as $key => $val) {
//            $info = new FansInputManage();
//            $info->tg_uid = $val['tg_uid'];
//            $info->department_id = $val['department_id'];
//            $info->wechat_id = $val['wechat_id'];
//            $info->weixin_id = $val['weixin_id'];
//            $info->business_type = $val['business_type'];
//            $info->charging_type = $val['charging_type'];
//            $info->customer_service_id = $val['customer_service_id'];
//            $info->goods_id = $val['goods_id'];
//            $info->total_fans = $val['total_fans'];
//            $info->addfan_date = $val['addfan_date'];
//            $info->addfan_count = $val['addfan_count'];
//            $info->update_time = time();
//            $info->create_time = time();
//            $info->save();
//        }
//        $this->logs("批量导入进粉");
//        $this->msg(array('state' => 1, 'msgwords' => "批量导入进粉成功"));
    }

    /**
     * 导出进粉
     */
    public function actionExport()
    {

        $file_name = '进粉录入表-' . date('Ymd', time());
        $headlist = array('id', '加粉日期', '微信号ID', '客服部', '业务', '商品', '计费方式', '推广人员', '进粉量', '累计粉丝量', '删除拉黑', '刷粉', '性别不符合粉', '不回复粉', '年龄不符合', '疾病粉', '有效粉');
        $page = $this->getExportData(1);
        $row = array();
        $row[0] = array('-', '-', '-', '-', '-', '-', '-', iconv('utf-8', 'gbk', '合计'), $page['listdata']['addfan_count'], '-', '-', '-', '-', '-', '-', '-', '-');
        $data = $page['listdata']['list'];
        $count = count($data);
        for ($i = 0; $i < $count; $i++) {
            $valid_fans = $data[$i]['addfan_count'] - $data[$i]['del_black'] - $data[$i]['brush_fans'] - $data[$i]['gender_dif_fans'] - $data[$i]['not_reply_fans'] - $data[$i]['age_dif_fans'] - $data[$i]['disease_fans'];
            $k = $i + 1;
            $row[$k] = array(
                $data[$i]['id'],//id
                date('Y-m-d', $data[$i]['addfan_date']),//加粉日期
                $data[$i]['wechat_id'],//微信号ID
                $page['listdata']['csNames'][$data[$i]['customer_service_id']],//客服部
                $page['listdata']['bNames'][$data[$i]['business_type']],//业务
                $page['listdata']['goodsNames'][ $data[$i]['goods_id']],//商品
                vars::get_field_str('charging_type', $data[$i]['charging_type']),//计费方式
                $page['listdata']['userNames'][$data[$i]['tg_uid']],//推广人员
                $data[$i]['addfan_count'],//进粉量
                $data[$i]['total_fans'],//累计粉丝量
                $data[$i]['del_black'],//删除拉黑
                $data[$i]['brush_fans'],//刷粉
                $data[$i]['gender_dif_fans'],//性别不符合粉
                $data[$i]['not_reply_fans'],//不回复粉
                $data[$i]['age_dif_fans'],//年龄不符合
                $data[$i]['disease_fans'],//疾病粉
                $valid_fans//有效粉
            );
            foreach ($row[$k] as $key => $value) {
                $row[$k][$key] = iconv('utf-8', 'gbk', $value);
            }
        }
        helper::downloadCsv($headlist, $row, $file_name);
    }

    /**
     * 获取导出数据
     */
    private function getExportData($is_export = 0)
    {
        $params['where'] = '';
        $params['where'] .= $this->getTimeIntervalSql('a.addfan_date', $this->get('start_addfan_date'), $this->get('end_addfan_date'));

        if ($this->get('wechat_id') != '') $params['where'] .= " and(a.wechat_id like '%" . $this->get('wechat_id') . "%') ";
        if ($this->get('csid') != '') $params['where'] .= " and(a.customer_service_id = '" . $this->get('csid') . "') ";
        //推广人员
        $get_promotion_id = $this->data_authority();
        if ($get_promotion_id !== 0) {
            $params['where'] .= " and(a.tg_uid in (" . $get_promotion_id . ")) ";
        }
        if ($this->get('user_id') != '') {
            $params['where'] .= " and(a.tg_uid = '" . $this->get('user_id') . "') ";
        }
        if ($this->get('goods_id') != '') $params['where'] .= " and(a.goods_id = '" . $this->get('goods_id') . "') ";
        $params['order'] = "  order by a.id desc      ";

        $params['pagesize'] = 1 == $is_export ? 20000 : Yii::app()->params['management']['pagesize'];
        $params['pagebar'] = 1;
        $params['select'] = "a.*";
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(FansInputManage::model()->tableName())->listdata($params);
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

        $page['listdata']['url'] = urlencode('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"]);


        $condition = $params['where'];
        $join = $params['join'];
        //进粉量
        $page['listdata']['addfan_count'] = FansInputManage::model()->getTotal('addfan_count',$condition,$join);
        //累计粉丝量
        $page['listdata']['total_fans'] = FansInputManage::model()->getTotal('total_fans',$condition,$join);
        //删除拉黑
        $page['listdata']['del_black'] = FansInputManage::model()->getTotal('del_black',$condition,$join);
        //刷粉
        $page['listdata']['brush_fans'] = FansInputManage::model()->getTotal('brush_fans',$condition,$join);
        //性别不符合粉
        $page['listdata']['gender_dif_fans'] = FansInputManage::model()->getTotal('gender_dif_fans',$condition,$join);
        //不回复粉
        $page['listdata']['not_reply_fans'] = FansInputManage::model()->getTotal('not_reply_fans',$condition,$join);
        //年龄不符合
        $page['listdata']['age_dif_fans'] = FansInputManage::model()->getTotal('age_dif_fans',$condition,$join);
        //疾病粉
        $page['listdata']['disease_fans'] = FansInputManage::model()->getTotal('disease_fans',$condition,$join);
        //有效粉
        $page['listdata']['valid_fans'] = $page['listdata']['addfan_count'] - $page['listdata']['del_black'] - $page['listdata']['brush_fans'] - $page['listdata']['gender_dif_fans'] - $page['listdata']['not_reply_fans'] - $page['listdata']['age_dif_fans'] - $page['listdata']['disease_fans'];
        return $page;
    }
}