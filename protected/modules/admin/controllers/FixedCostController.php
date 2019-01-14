<?php

/**
 * 修正成本控制器
 * User: fang
 * Date: 2016/12/15
 * Time: 13:54
 */
class FixedCostController extends AdminController
{
    public function actionIndex()
    {
        $params = $this->getCondition();

        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;

        $page['listdata'] = Dtable::model(FixedCost::model()->tableName())->listdata($params);
        $page['listdata']['url'] = urlencode('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"]);
        $sql = "SELECT SUM(a.fixed_cost) AS fixed_cost FROM fixed_cost_new AS a " . $params['join'] . " where 1 " . $params['where'];
        $sumInfo = Yii::app()->db->createCommand($sql)->queryAll();
        $page['listdata']['fixed_cost'] = $sumInfo[0]['fixed_cost'];

        $this->render('index', array('page' => $page));
    }

    /**
     * 添加修正成本
     */
    public function actionAdd()
    {
        if (!$_POST) {
            $page = array();
            $this->render('update', array('page' => $page));
        }else{
            $info = new FixedCost();
            if ($info->channel_id == '') $this->msg(array('state' => 0, 'msgwords' => '渠道不能为空'));
            if ($info->weixin_id == '') $this->msg(array('state' => 0, 'msgwords' => '微信号不能为空'));
            if ($info->tg_uid == '') $this->msg(array('state' => 0, 'msgwords' => '推广人员不能为空'));
            if ($info->goods_id == '') $this->msg(array('state' => 0, 'msgwords' => '商品不能为空'));
            if ($info->customer_service_id == '')
                $this->msg(array('state' => 0, 'msgwords' => '客服不能为空'));

            $info->fixed_date = strtotime(date('Ymd'));
            $info->partner_id = $this->post('partner_id');
            $info->channel_id = $this->post('channel_id');
            $info->weixin_id = $this->post('wechat_id');
            $info->stat_date = strtotime($this->post('stat_date'));
            $info->fixed_cost = $this->post('fixed_cost');
            $info->tg_uid = $this->get('tg_uid');
            $info->business_type = $this->get('business_id');
            $info->charging_type = $this->get('charging_type');
            $info->goods_id = $this->get('goods_id');
            $info->customer_service_id = $this->get('customer_service_id');
            $info->fixed_cost = $this->get('fixed_cost');
            $info->fixed_piwik_cost = $this->get('fixed_piwik_cost');
            $info->update_time = time();
            $info->create_time = time();
            $dbresult = $info->save();
            $id = $info->primaryKey;

            //保存判断
            if($dbresult){
                $msgarr = array('state' => 1, 'url' => $this->createUrl('fixedCost/index') . '?p=' . $_GET['p'] . '&search_type=' . $this->get('search_type') . '&search_txt=' . $this->get('search_txt') . '');
                $this->logs("添加修正ID：$id 成功");
                $this->msg($msgarr);
            }else{
                $this->logs("添加修正ID失败");
                $this->msg(array('state' => 0));
            }
        }
    }

    /**
     * 编辑修正成本
     */
    public function actionEdit()
    {
        $id = $this->get('id');
        $info = FixedCost::model()->findByPk($id);
        if (!$info) $this->msg(array('state' => 0, 'msgwords' => '修正成本不存在'));

        if (!$_POST) {
            $page = array();

            $info = $this->toArr($info);
            $page['info'] = $info;
            $page['channel']['id'] = $info['channel_id'];
            $page['channel']['partner_id'] = $info['partner_id'];
            $page['channel']['wechat_id'] = $info['wechat_id'];

            $this->render('update', array('page' => $page));
        } else {
            $info->fixed_date = strtotime(date('Ymd'));
            $info->partner_id = $this->post('partner_id');
            $info->channel_id = $this->post('channel_id');
            if ($info->channel_id == '') $this->msg(array('state' => 0, 'msgwords' => '渠道不能为空'));
            $info->weixin_id = $this->post('wechat_id');
            $info->stat_date = strtotime($this->post('stat_date'));
            $info->fixed_cost = $this->post('fixed_cost');
            $info->tg_uid = $this->get('tg_uid');
            $info->business_type = $this->get('business_id');
            $info->charging_type = $this->get('charging_type');
            $info->goods_id = $this->get('goods_id');
            $info->customer_service_id = $this->get('customer_service_id');
            $info->fixed_cost = $this->get('fixed_cost');
            $info->fixed_piwik_cost = $this->get('fixed_piwik_cost');
            $info->update_time = time();
            $dbresult = $info->save();
            $id = $info->primaryKey;

            //保存判断
            if($dbresult){
                $msgarr = array('state' => 1,'msgwords'=>"编辑修正ID：$id 成功",'url' => $this->get('backurl'));
                $this->logs("编辑修正ID：$id 成功");
                $this->msg($msgarr);
            }else{
                $this->logs("编辑修正ID失败");
                $this->msg(array('state' => 0));
            }
        }
    }

    /**
     * 微信号ID主页面数据处理
     * author: yjh
     */
    public function actionWechatIndex()
    {
        $params['where'] = '';

        if ($this->get('search_type') == 'keys' && $this->get('search_txt')) {
            $params['where'] = " and(wechat_id like '%" . $this->get('search_txt') . "%') ";
        }

        $params['order'] = "  order by id desc    ";
        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(WeChat::model()->tableName())->listdata($params);

        if (isset($_GET['jsoncallback'])) {
            $data['list'] = $page['listdata']['list'];
            $this->msg(array('state' => 1, 'data' => $data, 'type' => 'jsonp'));
        }

        $this->render('wechatIndex', array('page' => $page));
    }

    /**
     * 删除修正成本
     */
    public function actionDelete()
    {
        $ids = isset($_GET['ids']) && $_GET['ids'] != '' ? $_GET['ids'] : '';
        $ids = explode(',', $ids);
        $logs = "删除了修正成本：";
        foreach ($ids as $id) {
            $id = intval($id);
            if ($id == 0) continue;
            $m = FixedCost::model()->findByPk($id);
            $m->delete();
            $logs .= $id . ",";
        }
        //die();
        $this->logs($logs);
        $this->msg(array('state' => 1));
    }

    /**
     * 导出
     */
    public function actionExport()
    {

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="修正成本-' . date('Ymd', time()) . '.csv"');
        header('Cache-Control: max-age=0');
        //打开PHP文件句柄,php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'a');
        $top_row = array('上线日期', '推广人员', '推广小组', '归属客服部', '商品', '合作商', '渠道名称', '渠道编码', '微信号', '业务', '计费方式', '修正友盟金额', '修正日期');
        foreach ($top_row as $k => $v) {
            $top_row[$k] = iconv('utf-8', 'gbk', $v);
        }
        fputcsv($fp, $top_row);
        $num = 0;
        $limit = 1000;
        $data = $this->GetFixedCostData();
        foreach ($data as $key => $val) {
            $num++;
            if ($num == $limit) {
                ob_flush();
                flush();
                $num = 0;
            }
            $row = array($val['stat_date'], $val['csname_true'], $val['promotion_group'], $val['customer_service'], $val['goods_name'], $val['partner_name'],
                $val['channel_name'], $val['channel_code'], $val['wechat_id'], $val['business_name'], vars::get_field_str('charging_type', $val['charging_type']), $val['fixed_cost'], $val['fixed_date']);
            foreach ($row as $k => $v) {
                $row[$k] = iconv('utf-8', 'gbk', $v);
            }
            fputcsv($fp, $row);
        }
    }

    /**
     * 获取搜索条件
     */
    private function getCondition(){
        $params['where'] = '';
        //修正日期
        if ($this->get('fixed_date_s') != '' && $this->get('fixed_date_e') != '') {
            $start = strtotime($this->get('fixed_date_s'));
            $end = strtotime($this->get('fixed_date_e')) + 3600 * 24 - 1;
            $params['where'] .= " and(a.fixed_date between $start and $end ) ";
        }
        //上线日期
        if ($this->get('stat_date_s') != '' && $this->get('stat_date_e') != '') {
            $start_online = strtotime($this->get('stat_date_s'));
            $end_online = strtotime($this->get('stat_date_e')) + 3600 * 24 - 1;
            $params['where'] .= " and(a.stat_date between $start_online and $end_online ) ";
        }
        //客服部搜索
        if ($this->get('csid')) {
            $params['where'] .= " and(a.customer_service_id =" . intval($this->get('csid')) . ")";
        }
        // 商品搜索
        if ($this->get('goods_id')) {
            $params['where'] .= " and(a.goods_id =" . intval($this->get('goods_id')) . ")";
        }
        //合作商	渠道名称	渠道编码
        $type = $this->get('search_type');
        if($this->get('search_txt') && $type){
            switch ($type){
                case 'partner_name':
                    $params['where'] .= " and(d.name like '%" . $this->get('search_txt') . "%') ";
                    break;
                case 'channel_name':
                    $params['where'] .= " and(c.channel_name like '%" . $this->get('search_txt') . "%') ";
                    break;
                case 'channel_code':
                    $params['where'] .= " and(c.channel_code like '%" . $this->get('search_txt') . "%') ";
                    break;
                default:
                    break;
            }
        }
        //业务搜索
        if ($this->get('bs_id')) $params['where'] .= " and i.bid = " . $this->get('bs_id') . " ";
        // 微信搜索
        if ($this->get('wechat_id')) {
            $params['where'] .= " and(e.wechat_id like '%" . $this->get('wechat_id') . "%' )";
        }
        //操作用户
        if ($this->get('user_id') != '') $params['where'] .= " and(a.tg_uid = " . $this->get('user_id') . ") ";
        //查看人员权限
        $result = $this->data_authority();
        if ($result != 0) {
            $params['where'] .= " and(a.tg_uid in ($result)) ";
        }
        $params['join'] = "
            left join channel as c on c.id=a.channel_id
            left join partner as d on d.id=a.partner_id
            left join wechat as e on e.id=a.weixin_id
            left join cservice as f on f.csno=a.tg_uid
            left join promotion_staff_manage as g on g.user_id=a.tg_uid
            left join goods as h on h.id=a.goods_id
            left join business_types as i on i.bid=a.business_type
            left join linkage as m on m.linkage_id=g.promotion_group_id
            left join customer_service_manage as n on n.id=a.customer_service_id
		";
        $params['order'] = "  order by a.id desc    ";
        $params['select'] = "a.stat_date,a.fixed_cost,a.fixed_date,f.csname_true,e.wechat_id,i.bname,h.goods_name,c.channel_name,c.channel_code,g.promotion_group_id,a.id as cid,a.customer_service_id,a.create_time,a.business_type,a.charging_type,d.name as partner_name,m.linkage_name,n.cname";

        return $params;
    }

    /**
     * 获取修正成本数据
     */
    private function GetFixedCostData()
    {
        $params = $this->getCondition();

        $sql = " SELECT " . $params['select'] . " FROM fixed_cost_new as a " . $params['join'] . ' WHERE 1 ' . $params['where'] . $params['order'];
        $data = Yii::app()->db->createCommand($sql)->queryAll();
        // 获取推广组列表
        $promotion_group_list = Linkage::model()->getPromotionGroupList();
        // 客服部列表
        foreach ($data as $key => $val) {
            $data[$key]['stat_date'] = date('Y-m-d', $val['stat_date']);
            $data[$key]['csname_true'] = $val['csname_true'];
            $data[$key]['promotion_group'] = isset($promotion_group_list[$val['promotion_group_id']]) ? $promotion_group_list[$val['promotion_group_id']]['linkage_name'] : " ";
            $data[$key]['goods_name'] = $val['goods_name'];
            $data[$key]['partner_name'] = $val['partner_name'];
            $data[$key]['channel_name'] = $val['channel_name'];
            $data[$key]['channel_code'] = $val['channel_code'];
            $data[$key]['wechat_id'] = $val['wechat_id'];
            $data[$key]['fixed_cost'] = $val['fixed_cost'];
            $data[$key]['fixed_piwik_cost'] = $val['fixed_piwik_cost'];
            $data[$key]['fixed_date'] = date('Y-m-d', $val['fixed_date']);
            $data[$key]['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
        }

        return $data;
    }

    /**
     *下载修正成本列表模板
     */
    public function actionTemplate()
    {
        $headlist = array('上线日期', '推广人员', '推广小组', '归属客服部', '商品', '合作商', '渠道名称', '渠道编码', '微信号', '业务类型', '计费方式', '修正友盟金额', '修正第三方金额');
        $file_name = '修正成本列表模板.xls';
        $text = '';
        helper::downloadExcel($headlist, array(), $text, $file_name);
    }

    /**
     * 导入
     */
    public function actionLoad()
    {
        if (isset($_POST['submit'])) {
            $file = CUploadedFile::getInstanceByName('filename');//获取上传的文件实例
            if ($file) {
                if ($file->getExtensionName() != 'xls') $this->msg(array('state' => 0, 'msgwords' => '请导入.xls文件！'));
                if ($file->getType() == 'application/octet-stream' || $file->getType() == 'application/vnd.ms-excel') {
                    $excelFile = $file->getTempName();//获取文件名
                    //这里就是导入PHPExcel包了，要用的时候就加这么两句，方便吧
                    Yii::$enableIncludePath = false;
                    Yii::import('application.extensions.PHPExcel.PHPExcel', 1);
                    $phpexcel = new PHPExcel();
                    $excelReader = PHPExcel_IOFactory::createReader('Excel5');
                    $phpexcel = $excelReader->load($excelFile)->getActiveSheet(0);//载入文件并获取第一个sheet
                    $total_line = $phpexcel->getHighestRow();
                    $total_column = $phpexcel->getHighestColumn();
                    echo '<div class="msgbox0009" title="Excel导入结果">';
                    for ($row = 2; $row <= $total_line; $row++) {
                        $data = array();
                        for ($column = 'A'; $column <= $total_column; $column++) {
                            $data[] = trim($phpexcel->getCell($column . $row)->getValue());
                        }
                        $info = new FixedCost();
                        $info->stat_date = strtotime($data[0]);
                        $cservice = AdminUser::model()->findByAttributes(array('csname_true' => $data[1]));
                        $info->tg_uid = $cservice->csno;
                        $partner = Partner::model()->findByAttributes(array('name' => $data[5]));
                        $info->partner_id = $partner->id;
                        $channel = Channel::model()->findByAttributes(array('channel_code' => $data[7], 'channel_name' => $data[6]));
                        $info->channel_id = $channel->id;
                        $goods = Goods::model()->findByAttributes(array('goods_name' => $data[4]));
                        $info->goods_id = $goods->id;
                        $customer = CustomerServiceManage::model()->findByAttributes(array('cname' => $data[3]));
                        $info->customer_service_id = $customer->id;
                        $info->update_time = time();
                        $info->create_time = time();
                        $wechat = WeChat::model()->findByAttributes(array('wechat_id' => $data[8]));
                        $info->weixin_id = $wechat->id;
                        $business_type = BusinessTypes::model()->findByAttributes(array('bname' => $data[9]));
                        $info->business_type = $business_type->bid;
                        $charging = vars::get_value('charging_type', $data[10]);
                        $info->charging_type = $charging;
                        $info->fixed_cost = $data[11];
                        $info->fixed_piwik_cost = $data[12];
                        $info->fixed_date = strtotime(date('Ymd'));
                        if ($info->partner_id == '' && $info->channel_id == '') continue;
                        $result = $info->save();
                        $id = $info->primaryKey;
                        if ($result) {
                            echo "修正成本：" . $id . ',导入成功! <br>';
                        } else {
                            echo "修正成本：" . $id . ',导入失败! <br>';
                        }

                        $logs = "添加了修正成本：" . $id;
                        //新增和修改之后的动作
                        $this->logs($logs);
                    }
                    echo '</div><script>setTimeout("parent.show_frame_infos();",500)</script>';
                    exit;
                }
            } else {
                $this->msg(array('state' => 0, 'msgwords' => '文件不存在'));
            }

        }
    }
}