<?php

/**
 * 微信处理
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/1
 * Time: 10:13
 */
class WeChatController extends AdminController
{
    /**
     * 微信号列表查询
     * author: yjh
     */
    public function actionIndex()
    {
        $page = $this->getWechatData();
        $this->render('index', array('page' => $page));
    }

    /**
     * 新增微信号
     * author: yjh
     */
    public function actionAdd()
    {
        $page = array();
        //显示表单
        if (!$_POST) {
            $this->render('update', array('page' => $page));
            exit;
        }
        //表单验证
        $info = new WeChat();
        /*微信号ID*/
        $info->wechat_id = trim($this->post('wechat_id'));
        $result = WeChat::model()->count('wechat_id=:wechat_id', array(':wechat_id' => $info->wechat_id));
        if ($info->wechat_id == '') $this->msg(array('state' => 0, 'msgwords' => '未填微信号！'));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '此微信号已存在，请重新输入！'));
        /*客服部ID*/
        $info->customer_service_id = $this->post('customer_service_id');
        if ($info->customer_service_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择客服部！'));
        /*商品ID*/
        $info->goods_id = $this->post('goods_id');
        if ($info->goods_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择商品！'));
        /*形象ID*/
        $info->character_id = $this->post('character_id');
        if ($info->character_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择形象！'));
        /*客服部ID*/
        $info->customer_service_id = $this->post('customer_service_id');
        if ($info->customer_service_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择归属客服部！'));
        /*推广人员ID*/
        $info->promotion_staff_id = $this->post('promotion_staff_id');
        if ($info->promotion_staff_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择推广人员！'));
        /*业务类型*/
        $info->business_type = $this->post('business_type');
        if ($info->business_type == '') $this->msg(array('state' => 0, 'msgwords' => '未选择业务类型！'));
        /*计费方式*/
        $info->charging_type = $this->post('charging_type');
        if ($info->charging_type == '') $this->msg(array('state' => 0, 'msgwords' => '未选择计费方式！'));
        /*部门ID*/
        $info->department_id = $this->post('department_id');
        if ($info->department_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择部门！'));
        /*状态*/
        $info->status = $this->post('status');
        if ($info->status == '') $this->msg(array('state' => 0, 'msgwords' => '未选择状态！'));
        $info->land_url = $this->post('land_url');
        $info->update_time = time();
        $info->create_time = time();
        $dbresult = $info->save();
        $id = $info->primaryKey;
        $msgarr = array('state' => 1, 'url' => $this->createUrl('weChat/index') . '?p=' . $_GET['p'] . ''); //保存的话，跳转到之前的列表
        $logs = "添加了新的微信号：ID:$id," . $info->wechat_id;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            // 创建微信号使用记录 lxj
            $data = array(
                'wx_id'=>$info->id,
                'wechat_id'=>trim($this->post('wechat_id')),
                'customer_service_id'=>$this->post('customer_service_id'),
                'goods_id'=>$this->post('goods_id'),
                'character_id'=>$this->post('character_id'),
                'business_type'=>$this->post('business_type'),
                'department_id'=>$this->post('department_id'),
                'promotion_staff_id'=>$this->post('promotion_staff_id'),
                'charging_type'=>$this->post('charging_type'),
            );
            $this->updateWechatUseLog($data);
            //新增和修改之后的动作
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 修改微信号
     * author: yjh
     */
    public function actionEdit()
    {
        $page = array();
        $id = $this->get('id');
        $info = WeChat::model()->findByPk($id);

        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }

        //修改前的数据
        $old_wechat_id = $info->wechat_id;
        $old_goods_id = $info->goods_id;
        $old_character_id = $info->character_id;
        $old_customer_service_id = $info->customer_service_id;
        $old_promotion_staff_id = $info->promotion_staff_id;
        $old_department_id = $info->department_id;
        $old_business_type = $info->business_type;
        $old_charging_type = $info->charging_type;
        $old_status = $info->status;
        $old_land_url = $info->land_url;
        $wlogs = '';
        //显示表单
        if (!$_POST) {
            $page['info'] = $this->toArr($info);
            $goodsInfo = $this->toArr(Goods::model()->findByPk($page['info']['goods_id']));
            $characterIds = explode(',', $goodsInfo['characters']);
            foreach ($characterIds as $val) {
                $page['info']['characterList'][] = array(
                    'id' => $val,
                    'name' => Linkage::model()->getCharacterById($val)
                );
            }
            $departmentList = AdminUser::model()->get_user_group($page['info']['promotion_staff_id']);
            $goodsList = CustomerServiceRelation::model()->getGoodsList($page['info']['customer_service_id']);
            $page['info']['departmentList'] = $departmentList;
            $page['info']['goodsList'] = $goodsList;
            $page['info']['chargingTypeList'] = Dtable::toArr(BusinessTypeRelation::model()->findAll('bid=:bid', array(':bid' => $page['info']['business_type'])));
            foreach ($page['info']['chargingTypeList'] as $k => $v) {
                $page['info']['chargingTypeList'][$k]['cname'] = vars::get_field_str('charging_type', $v['charging_type']);
            }

            $this->render('update', array('page' => $page));
            exit;
        }
        //表单验证
        // 需插入微信号使用记录的数据
        $log_data = array();
        $log_data['wx_id'] = $id;
        /*微信号ID*/
        $info->wechat_id = trim($this->post('wechat_id'));
        $result = WeChat::model()->count('wechat_id=:wechat_id and id!=:id', array(':wechat_id' => $info->wechat_id, 'id' => $id));
        if ($info->wechat_id == '') $this->msg(array('state' => 0, 'msgwords' => '未填微信号！'));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '此微信号已存在，请重新输入！'));
        $wlogs .= $old_wechat_id == $info->wechat_id ? '' : "微信号id：'$old_wechat_id' 变更为 '$info->wechat_id';<br/>";
        // 微信号变更，则插入微信号使用记录表
        $log_data['wechat_id'] = $info->wechat_id;
        $old_data['wechat_id'] = $old_wechat_id;
        /*客服部ID*/
        $info->customer_service_id = $this->post('customer_service_id');
        if ($info->customer_service_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择客服部！'));
        if ($old_customer_service_id != $info->customer_service_id) {
            $old_cname = CustomerServiceManage::model()->getCSName($old_customer_service_id);
            $cname = CustomerServiceManage::model()->getCSName($info->customer_service_id);
            $wlogs .= "客服部：'$old_cname' 变更为 '$cname';<br/>";
        }
        $log_data['customer_service_id'] = $info->customer_service_id;
        $old_data['customer_service_id'] = $old_customer_service_id;
        /*商品ID*/
        $info->goods_id = $this->post('goods_id');
        if ($info->goods_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择商品！'));
        if ($old_goods_id != $info->goods_id) {
            $old_good_name = Goods::model()->getGoodsName($old_goods_id);
            $good_name = Goods::model()->getGoodsName($info->goods_id);
            $wlogs .= "商品：'$old_good_name' 变更为 '$good_name';<br/>";
        }
        $log_data['goods_id'] = $info->goods_id;
        $old_data['goods_id'] = $old_goods_id;
        /*形象ID*/
        $info->character_id = $this->post('character_id');
        if ($info->character_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择形象！'));
        if ($old_character_id != $info->character_id) {
            $old_character = Linkage::model()->get_name($old_character_id);
            $character = Linkage::model()->get_name($info->character_id);
            $wlogs .= "形象：'$old_character' 变更为 '$character';<br/>";
        }
        $log_data['character_id'] = $info->character_id;
        $old_data['character_id'] = $old_character_id;
        /*推广人员ID*/
        $info->promotion_staff_id = $this->post('promotion_staff_id');
        if ($info->promotion_staff_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择推广人员！'));
        if ($old_promotion_staff_id != $info->promotion_staff_id) {
            $old_promotion_staff = PromotionStaff::model()->find(array('condition' => 'user_id=:user_id', 'params' => array(':user_id' => $old_promotion_staff_id)))->name;
            $promotion_staff = PromotionStaff::model()->find(array('condition' => 'user_id=:user_id', 'params' => array(':user_id' => $info->promotion_staff_id)))->name;
            $wlogs .= "推广人员：'$old_promotion_staff' 变更为 '$promotion_staff';<br/>";
        }
        $log_data['promotion_staff_id'] = $info->promotion_staff_id;
        $old_data['promotion_staff_id'] = $old_promotion_staff_id;
        /*业务类型*/
        $info->business_type = $this->post('business_type');
        if ($info->business_type == '') $this->msg(array('state' => 0, 'msgwords' => '未选择业务类型！'));
        if ($old_business_type != $info->business_type) {
            $old_business = BusinessTypes::model()->findByPk($old_business_type)->bname;
            $business_type = BusinessTypes::model()->findByPk($info->business_type)->bname;
            $wlogs .= "业务：'$old_business' 变更为 '$business_type';<br/>";
        }
        $log_data['business_type'] = $info->business_type;
        $old_data['business_type'] = $old_business_type;
        /*计费方式*/
        $info->charging_type = $this->post('charging_type');
        if ($info->charging_type == '') $this->msg(array('state' => 0, 'msgwords' => '未选择计费方式！'));
        if ($old_charging_type != $info->charging_type) {
            $old_charging = vars::get_field_str('charging_type', $old_charging_type);
            $charging_type = vars::get_field_str('charging_type', $info->charging_type);
            $wlogs .= "计费方式：'$old_charging' 变更为 '$charging_type';<br/>";
        }
        $log_data['charging_type'] = $info->charging_type;
        $old_data['charging_type'] = $old_charging_type;
        /*部门ID*/
        $info->department_id = $this->post('department_id');
        if ($info->department_id == '') $this->msg(array('state' => 0, 'msgwords' => '未选择部门！'));
        if ($old_department_id != $info->department_id) {
            $old_department_name = AdminGroup::model()->findByPk($old_department_id)->groupname;
            $department_name = AdminGroup::model()->findByPk($info->department_id)->groupname;
            $wlogs .= "部门：'$old_department_name' 变更为 '$department_name';<br/>";
        }
        $log_data['department_id'] = $info->department_id;
        $old_data['department_id'] = $old_department_id;
        /*状态*/
        $info->status = $this->post('status');
        if ($info->status == '') $this->msg(array('state' => 0, 'msgwords' => '未选择状态！'));
        if ($old_status != $info->status) {
            $old_status = vars::get_field_str('weChat_status', $old_status);
            $status = vars::get_field_str('weChat_status', $info->status);
            $wlogs .= "状态：'$old_status' 变更为 '$status';";
        }
        /*落地页*/
        $info->land_url = $this->post('land_url');
        if ($old_land_url != $info->land_url) {
            $wlogs .= "落地页：'$old_land_url' 变更为 '$info->land_url';";
        }
        $info->update_time = time();
        $info->create_time = time();
        $dbresult = $info->save();
        $id = $info->primaryKey;
        $msgarr = array('state' => 1, 'url' => $this->get('backurl')); //保存的话，跳转到之前的列表
        $logs = "修改了微信号信息：" . $info->wechat_id;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            //新增和修改之后的动作
            $this->logs($logs);
            if ($wlogs != '')  {
                $this->wLogs($wlogs, $id);
                // 更新微信号使用记录表 lxj
                $this->createOldLog($old_data,$id);
                $this->updateWechatUseLog($log_data,1);
            }
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 仅删除二维码
     * author: yjh
     */
    public function actionDeleteQR()
    {
        if ($this->get('id') == '') $this->msg(array('state' => 0, 'msgwords' => '未传入数据'));
        $id = $this->get('id');
        $info = WeChat::model()->findByPk($id);
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }
        //判断是否有二维码删除原图片及图片资源信息
        if ($info->qrcode_id != 0) {
            $qrCodeInfo = Resource::model()->findByPk($info->qrcode_id);
            $qrCodeUrl = $qrCodeInfo->resource_url;
            $img = Yii::app()->basePath . '/..' . $qrCodeUrl;
            if (file_exists(iconv('UTF-8', 'GB2312', $img))) unlink(iconv('UTF-8', 'GB2312', $img));
            $qrCodeInfo->delete();
        }
        $info->qrcode_id = 0;
        $info->save();
        $this->logs("删除了微信号" . $info->wechat_id . "的二维码");
        $this->msg(array('state' => 1, 'msgwords' => '删除微信号【' . $info->wechat_id . '】的二维码成功！'));
    }

    /**
     * 删除微信号
     * 删除微信号图片
     * 删除图片地址
     * author: yjh
     */
    public function actionDelete()
    {
        if ($this->get('id') == '') $this->msg(array('state' => 0, 'msgwords' => '未传入数据'));
        $id = $this->get('id');
        $info = WeChat::model()->findByPk($id);
        if (!$info) {
            $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        }
        $wResult = $this->toArr(WeChatRelation::model()->findAll('wid=:wid', array(':wid' => $id)));
        if (count($wResult) > 0) $this->msg(array('state' => 0, 'msgwords' => '此微信正在使用不能删除'));
        $result = StatCostDetail::model()->count("weixin_id=:weixin_id", array(":weixin_id" => $id));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '成本明细中用到该w信息不能删除！'));

        //判断是否有二维码删除原图片及图片资源信息
        if ($info->qrcode_id != 0) {
            $qrCodeInfo = Resource::model()->findByPk($info->qrcode_id);
            $qrCodeUrl = $qrCodeInfo->resource_url;
            $img = Yii::app()->basePath . '/..' . $qrCodeUrl;
            if (file_exists(iconv('UTF-8', 'GB2312', $img))) unlink(iconv('UTF-8', 'GB2312', $img));
            $qrCodeInfo->delete();
        }

        $info->delete();
        $this->logs("删除了微信号：" . $info->wechat_id);
        $this->msg(array('state' => 1, 'msgwords' => '删除微信号【' . $info->wechat_id . '】成功！', 'url' => $this->get('url')));
    }

    /**
     * 删除微信号s
     * 删除微信号图片s
     * 删除图片地址s
     * author: yjh
     */
    public function actionDel()
    {
        $ids = $this->get('ids');
        $idArr = explode(',', $ids);
        $log = '';
        $msgwords = '批量删除微信号成功</br>';
        foreach ($idArr as $val) {
            $info = WeChat::model()->findByPk($val);
            $wResult = $this->toArr(WeChatRelation::model()->findAll('wid=:wid', array(':wid' => $val)));
            if (!$info) continue;
            if (count($wResult) > 0) {
                $log .= "微信" . $info->wechat_id . "正在使用不能删除，";
                $msgwords .= "微信" . $info->wechat_id . "正在使用不能删除</br>";
                continue;
            }
            //判断是否有二维码删除原图片及图片资源信息
            if ($info->qrcode_id != 0) {
                $qrCodeInfo = Resource::model()->findByPk($info->qrcode_id);
                $qrCodeUrl = $qrCodeInfo->resource_url;
                $img = Yii::app()->basePath . '/..' . $qrCodeUrl;
                if (file_exists(iconv('UTF-8', 'GB2312', $img))) unlink(iconv('UTF-8', 'GB2312', $img));
                $qrCodeInfo->delete();
            }
            $info->delete();
            $log .= "删除了微信号：" . $info->wechat_id . ",";
            $msgwords .= "删除了微信号：" . $info->wechat_id . "</br>";
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
        $colums = array('微信号ID', '客服部', '商品', '形象', '业务', '计费方式', '推广人员' , '部门', '状态');
        $file_name = '微信号导入模板.xls';
        $txt = "导入注意事项：直接用此模板填写导入，除了微信号ID,其他字段必须在后台已经存在，如果不存在，请在后台添加后再进行导入！并且注意对应关系！";
        helper::downloadExcel($colums, array(), $txt, $file_name);
        exit;
    }

    /**
     * excel批量导入微信号
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
     * 数据为空
     * 判断微信号是否已存在
     * 判断商品是否存在，取出id
     * 判断形象是否存在，是否在商品中，取出id
     * 判断客服部是否存在，取出id
     * 判断推广人员是否存在
     * 判断部门是否存在，推广人员是否有这个部门
     * 判断每个字段是否都有填写
     * 每个数字代表：0：微信号id 1：商品 2：形象 3：客服部 4：推广人员 5：部门 6：状态
     */
    private function dataFilter(array $data, $row)
    {
        static $rightData = array();
        /******1、判断数据是否为空********/
        if (empty($data[0])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的微信号ID为空！'));
        if (empty($data[1])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的商品为空！'));
        if (empty($data[2])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的形象为空！'));
        if (empty($data[3])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的客服部为空！'));
        if (empty($data[6])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的推广人员为空！'));
        if (empty($data[7])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的部门为空！'));
        if (empty($data[8])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的状态为空！'));
        if (empty($data[4])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的业务为空！'));
        if (empty($data[5])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的计费方式为空！'));
        /******2、判断数据冲突情况**********/
        //微信号判断，还要判断和之前的数据不重复
        $weChatInfo = $this->toArr(WeChat::model()->find('wechat_id = :wechat_id', array(':wechat_id' => $data[0])));
        if (count($weChatInfo) > 0) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的微信号ID' . $data[0] . '已存在！'));
        foreach ($rightData as $key => $val) {
            if ($data[0] == $val['wechat_id']) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的微信号ID' . $data[0] . '和' . ($key + 3) . '行的微信号重复了！'));
        }
        //客服部判断
        $customerServiceInfo = $this->toArr(CustomerServiceManage::model()->find('cname = :name', array(':name' => $data[1])));
        if (count($customerServiceInfo) == 0) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的客服部' . $data[1] . '不存在！请在后台添加'));

        //商品判断
        $goodsList = $this->toArr(CustomerServiceRelation::model()->findAllByAttributes(array('cs_id' => $customerServiceInfo['id'])));
        $goodsArr = array();
        foreach ($goodsList as $key => $val) {
            $goodsArr[] = $val['goods_id'];
        }
        $goodsInfo = $this->toArr(Goods::model()->find('goods_name = :goods_name', array(':goods_name' => $data[2])));
        if (count($goodsInfo) == 0) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的商品' . $data[2] . '不存在！请在后台先添加'));
        if (!in_array($goodsInfo['id'], $goodsArr)) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的商品并未添加在相应的客服部中！请在后台添加'));
        //形象判断
        $characterList = explode(',', $goodsInfo['characters']);
        $characterInfo = $this->toArr(Linkage::model()->find('linkage_type_id=19 and linkage_name = :character', array(':character' => $data[3])));
        if (count($characterInfo) == 0) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的形象' . $data[3] . '不存在！请在后台添加'));
        if (!in_array($characterInfo['linkage_id'], $characterList)) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的形象并未添加在相应的商品中！请在后台添加'));
        //推广人员判断
        $promotionStaffInfo = $this->toArr(PromotionStaff::model()->find('name = :name', array(':name' => $data[6])));
        if (count($promotionStaffInfo) == 0) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的推广人员' . $data[6] . '不存在！请在后台添加'));
        $departmentTemp = AdminUser::model()->get_user_group($promotionStaffInfo['user_id']);//[0][1]
        $departmentList = array();
        foreach ($departmentTemp as $key => $value) {
            $departmentList[] = $value['groupname'];
        }
        //部门判断
        $departmentInfo = $this->toArr(AdminGroup::model()->find('groupname = :name', array(':name' => $data[7])));
        if (count($departmentInfo) == 0) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的部门' . $data[7] . '不存在！请在后台添加'));
        if (!in_array($data[7], $departmentList)) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的推广人员' . $data[6] . '并未在部门' . $data[5] . '中！请在后台添加'));
        //状态判断
        $r = vars::get_value('weChat_status', $data[8]);
        if ($r == '' && $r != 0) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的状态' . $data[8] . '错误！'));
        //业务判断
        $businessTypeInfo = $this->toArr(BusinessTypes::model()->find('bname=:bname', array(':bname' => $data[4])));
        if (count($businessTypeInfo) == 0) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的业务' . $data[4] . '不存在！请在后台添加'));
        //计费方式判断
        $charging_type = vars::get_value('charging_type', $data[5]);
        if ($charging_type == '' && $charging_type != 0) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的计费方式' . $data[5] . '不存在！'));
        $s = BusinessTypeRelation::model()->find('bid=:bid and charging_type=:charging_type', array(':bid' => $businessTypeInfo['bid'], 'charging_type' => $charging_type));
        if (!$s) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的计费方式' . $data[5] . '不在业务类型' . $data[4] . '中！'));


        $rightData[] = array(
            'wechat_id' => $data[0],
            'goods_id' => $goodsInfo['id'],
            'character_id' => $characterInfo['linkage_id'],
            'customer_service_id' => $customerServiceInfo['id'],
            'promotion_staff_id' => $promotionStaffInfo['user_id'],
            'department_id' => $departmentInfo['groupid'],
            'status' => vars::get_value('weChat_status', $data[8]),
            'business_type' => $businessTypeInfo['bid'],
            'charging_type' => $charging_type,
        );
        return $rightData;
    }

    /**
     * 循环插入插入微信号
     * author: yjh
     */
    private function dataInsert($data)
    {

        foreach ($data as $key => $val) {
            $info = new WeChat();
            $info->wechat_id = $val['wechat_id'];
            $info->goods_id = $val['goods_id'];
            $info->character_id = $val['character_id'];
            $info->customer_service_id = $val['customer_service_id'];
            $info->promotion_staff_id = $val['promotion_staff_id'];
            $info->department_id = $val['department_id'];
            $info->status = $val['status'];
            $info->business_type = $val['business_type'];
            $info->charging_type = $val['charging_type'];
            $info->update_time = time();
            $info->create_time = time();
            $info->save();
            // 创建微信号使用记录 lxj
            $val['wx_id'] = $info->id;
            $this->updateWechatUseLog($val);
        }
        $this->logs("批量导入微信号");
        //成功跳转提示
        $this->msg(array('state' => 1, 'msgwords' => "批量导入微信号成功"));

    }


    /**
     * 批量上传二维码
     * author: yjh
     */
    public function actionUploadQRCode()
    {
        $this->render('uploadQRCode');
    }

    /**
     * 导出微信号
     * author: yjh
     */
    public function actionExport()
    {

        $colums = array('微信号ID', '客服部', '商品', '形象', '业务', '计费方式', '推广人员', '部门', '修改日期', '状态');
        $file_name = '微信号列表-' . date("Ymj") . '.xls';
        $t_data = $this->getWechatData(1);
        $data = $t_data['listdata']['list'];
        $temp_array = array();
        $count = count($data);
        for ($i = 0; $i < $count; $i++) {
            $line = $i;
            $temp_array[$line] = array(
                $data[$i]['wechat_id'],
                $data[$i]['customer_service'],
                $data[$i]['goods_name'],
                $data[$i]['character_name'],
                $data[$i]['business_type'],
                $data[$i]['charging_type'],
                $data[$i]['promotion_staff'],
                $data[$i]['department_name'],
                $data[$i]['update_time'],
                $data[$i]['status'],
            );
            foreach ($temp_array[$line] as $key => $value) {
                $temp_array[$line][$key] = iconv('utf-8', 'gbk', $value);
            }
        }
        helper::downloadCsv($colums, $temp_array, $file_name);
    }

    /**
     * 导入公众号落地页
     * author: yjh
     */
    public function actionImportLandUrl()
    {
        if (!isset($_POST['submit'])) {
            $this->render('importLandUrl');
        } else {
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
                        /******1、判断数据是否为空********/
                        if (empty($data[0])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的微信号ID为空！'));
                        if (empty($data[1])) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的公众号落地页为空！'));
                        /******2、判断数据冲突情况**********/
                        $weChatInfo = WeChat::model()->find('wechat_id = :wechat_id', array(':wechat_id' => $data[0]));
                        if (!$weChatInfo) $this->msg(array('state' => 0, 'msgwords' => '第' . $row . '行的微信号ID' . $data[0] . '不存在！'));
                        $insertData[] = array(
                            'id' => $weChatInfo->id,
                            'land_url' => $data[1]
                        );
                    }
                    foreach ($insertData as $key => $val) {
                        $info = WeChat::model()->findByPk($val['id']);
                        $info->land_url = $val['land_url'];
                        $info->update_time = time();
                        $info->save();
                    }
                    $this->logs("微信号批量匹配公众号落地页");
                    $this->msg(array('state' => 1, 'jscode' => "<script>setTimeout(function(){artDialog.close();}, 1000);</script>", 'msgwords' => '导入成功！'));
                } else $this->msg(array('state' => 0, 'msgwords' => '导入文件没有内容！'));
            } else $this->msg(array('state' => 0, 'msgwords' => '请选择要导入的xls文件！'));
        }
    }

    /**
     * 导入公众号落地页模板
     * author: yjh
     */
    public function actionUrlTemplate()
    {
        $colums = array('微信号ID', '公众号落地页地址');
        $file_name = '公众号落地页导入模板.xls';
        $txt = "新的公众号链接会覆盖之前的链接";
        helper::downloadExcel($colums, array(), $txt, $file_name);
        exit;
    }

    /**
     * 获取微信号数据
     * @param int $type
     * author: yjh
     */
    private function getWechatData($type = 0)
    {
        //搜索
        $params['where'] = '';
        //$params['where'] .= " and(status!=4) ";
        if ($this->get('wechat_id') != ''){
            $wechats = str_replace(array("\r\n","\n","\r"),',',$this->get('wechat_id'));
            $wechat_arr = explode(',',$wechats);
            if (count($wechat_arr) == 1) {
                $params['where'] .= " and(a.wechat_id like '%" . $wechat_arr[0]. "%') ";
            }elseif(count($wechat_arr) > 1) {
                foreach ($wechat_arr as $key=>$value) {
                    $wechat_arr[$key] = "'".$value."'";
                }
                $params['where'] .= " and(a.wechat_id in (" . implode(',',$wechat_arr). ")) ";
            }
        }

        if ($this->get('dt_id') != '') $params['where'] .= " and(a.department_id = '" . $this->get('dt_id') . "') ";
        if ($this->get('cs_id') != '') $params['where'] .= " and(a.customer_service_id = '" . $this->get('cs_id') . "') ";
        if ($this->get('bs_id') != '') $params['where'] .= " and(a.business_type = " . $this->get('bs_id') . ") ";
        if ($this->get('status') !== null && $this->get('status') !== '') $params['where'] .= " and(a.status = '" . $this->get('status') . "') ";
        if ($this->get('goods_name') != '') $params['where'] .= " and(g.goods_name like '%" . $this->get('goods_name') . "%') ";
        if ($this->get('character') != '') $params['where'] .= " and(l.linkage_name like '%" . $this->get('character') . "%') ";
        if ($this->get('promotion_staff_id') != '') {
            $params['where'] .= " and(a.promotion_staff_id = '" . $this->get('promotion_staff_id') . "') ";
        }
        $params['order'] = "  order by id desc    ";
        $params['pagesize'] = $type == 0 ? Yii::app()->params['management']['pagesize'] : 10000;

        $params['join'] = "
		left join goods as g on g.id=a.goods_id
		left join linkage as l on l.linkage_id=a.character_id
		left join business_types as b on b.bid=a.business_type
		left join customer_service_manage as c on c.id=a.customer_service_id
		left join promotion_staff_manage as p on p.user_id=a.promotion_staff_id
		left join cservice_group as s on s.groupid=a.department_id
		";
        $params['pagebar'] = 1;

        $params['select'] = " a.*,s.groupname as department_name,g.goods_name,linkage_name as character_name,bname as business_type,c.cname as customer_service,p.name as promotion_staff";
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(WeChat::model()->tableName())->listdata($params);
        foreach ($page['listdata']['list'] as $key => $val) {
            $page['listdata']['list'][$key]['charging_type'] = vars::get_field_str('charging_type', $val['charging_type']);
            $page['listdata']['list'][$key]['qrcode_img'] = $val['qrcode_id'] != 0 ? '/static/img/qr.png' : '';//二维码
            $page['listdata']['list'][$key]['status'] = vars::get_field_str('weChat_status', $val['status']);//状态
            $page['listdata']['list'][$key]['update_time'] = date('Y-m-d', $val['update_time']);//更新时间
        }
        $page['listdata']['url'] = urlencode('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"]);

        return $page;
    }

    /**
     * AJAX获取商品对应形象列表
     * author: yjh
     */
    public function actionGetCharacter()
    {
        if ($this->get('goods_id')) {
            $data = Goods::model()->findByPk($this->get('goods_id'));
            $goodsInfo = $this->toArr($data);
            if (empty($goodsInfo)) echo CHtml::tag('option', array('value' => ''), CHtml::encode('没有形象信息，请选择其他商品'), true);
            $characterIds = explode(',', $goodsInfo['characters']);

            foreach ($characterIds as $key => $val) {
                echo CHtml::tag('option', array('value' => $val), CHtml::encode(Linkage::model()->getCharacterById($val)), true);
            }
        }
    }

    /**
     * AJAX获取业务类型对应计费方式
     * author: yjh
     */
    public function actionGetChargingType()
    {
        if ($this->get('business_type')) {
            $chargingTypes = Dtable::toArr(BusinessTypeRelation::model()->findAll('bid=:bid', array(':bid' => $this->get('business_type'))));
            if (empty($chargingTypes)) echo CHtml::tag('option', array('value' => ''), CHtml::encode('没有计费方式，请选择其他业务类型'), true);

            foreach ($chargingTypes as $key => $val) {
                echo CHtml::tag('option', array('value' => $val['charging_type']), CHtml::encode(vars::get_field_str('charging_type', $val['charging_type'])), true);
            }
        }
    }

    /**
     * 显示二维码
     * author: yjh
     */
    public function actionShowQRCode()
    {
        $id = $this->get('id');
        if (!$id) $this->msg(array('state' => 0, 'msgwords' => "未传入参数"));
        $wInfo = WeChat::model()->findByPk($id);
        $qrCodeId = $wInfo->qrcode_id;
        $qrCodeUrl = Resource::model()->findByPk($qrCodeId)->resource_url;
        $img = Yii::app()->basePath . '/..' . $qrCodeUrl;
        if (file_exists(iconv('UTF-8', 'GB2312', $img))) $imgURL = $qrCodeUrl;
        else $this->msg(array('state' => 0, 'msgwords' => "图片损坏"));
        $this->render('showQRCode', array('imgURL' => $imgURL, 'wechat_id' => $wInfo->wechat_id));
    }

    /**
     * AJAX获取推广人员对应部门
     * author: yjh
     */
    public function actionGetDepartment()
    {
        if ($this->get('promotion_staff_id')) {
            $data = AdminUser::model()->get_user_group($this->get('promotion_staff_id'));
            if (empty($data)) echo CHtml::tag('option', array('value' => ''), CHtml::encode('没有部门信息，请选择其他推广人员'), true);

            foreach ($data as $key => $val) {
                echo CHtml::tag('option', array('value' => $val['groupid']), CHtml::encode($val['groupname']), true);
            }
        }
    }

    /*
     * 添加/更新微信使用记录表记录
     * @author lxj
     * @param $data array 插入数据数组
     * @param $action 0:只插入数据,1插入+更新数据
     */
    public function updateWechatUseLog($data,$action=0){
        $inser_param = array('wx_id','wechat_id','customer_service_id','goods_id','character_id','business_type','department_id','promotion_staff_id','charging_type');
        foreach ($inser_param as $value) {
            if(!array_key_exists($value,$data)) {
                $this->msg(array('state' => 0, 'msgwords' => "未传入全部参数"));
                exit();
            }
        }
        if ($action == 1) {
            $use_log = WeChatUseLog::model()->find('wx_id='.$data['wx_id'].' and end_time=0 ');
            if ($use_log) {
                $use_log->end_time = strtotime(date('Y-m-d',time()));
                $use_log->save();
            }
        }
        $use_log = new WeChatUseLog();
        $use_log->wx_id = $data['wx_id'];
        $use_log->wechat_id = $data['wechat_id'];
        $use_log->customer_service_id = $data['customer_service_id'];
        $use_log->goods_id = $data['goods_id'];
        $use_log->character_id = $data['character_id'];
        $use_log->business_type = $data['business_type'];
        $use_log->department_id = $data['department_id'];
        $use_log->promotion_staff_id = $data['promotion_staff_id'];
        $use_log->charging_type = $data['charging_type'];
        $use_log->begin_time = strtotime(date('Y-m-d',time()));
        $use_log->save();
    }

    /*
     * 更新数据时微信号未有使用记录，创建使用记录
     * @param $data 更新前数据
     * @param $wx_id 微信id
     */
    public function createOldLog($data,$wx_id){
        $log = WeChatUseLog::model()->find('wx_id = '.$wx_id);
        if (!$log) {
            $inser_param = array('wechat_id','customer_service_id','goods_id','character_id','business_type','department_id','promotion_staff_id','charging_type');
            foreach ($inser_param as $value) {
                if(!array_key_exists($value,$data)) {
                    $this->msg(array('state' => 0, 'msgwords' => "未传入全部参数"));
                    exit();
                }
            }
            $log = new WeChatUseLog();
            $log->wx_id = $wx_id;
            $log->wechat_id = $data['wechat_id'];
            $log->customer_service_id = $data['customer_service_id'];
            $log->goods_id = $data['goods_id'];
            $log->character_id = $data['character_id'];
            $log->business_type = $data['business_type'];
            $log->department_id = $data['department_id'];
            $log->promotion_staff_id = $data['promotion_staff_id'];
            $log->charging_type = $data['charging_type'];
            $log->begin_time = strtotime('1997-01-01');
            $log->end_time = strtotime(date('Y-m-d',time()));
            $log->save();
        }
    }
}