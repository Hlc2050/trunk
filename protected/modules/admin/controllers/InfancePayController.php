<?php

/**
 * 财务打款控制器
 * User: fang
 * Date: 2016/11/15
 * Time: 15:28
 */
class InfancePayController extends AdminController
{
    public function actionIndex()
    {
        $page = $this->getExportData();
        $this->render('index', array('page' => $page));
    }

    /**
     * 添加打款
     */
    public function actionAdd()
    {
        $page = array();
        //显示表单
        if (!$_POST) {
            if ($this->get('spcl') == 1) $this->render('spclUpdate', array('page' => $page));
            else $this->render('update', array('page' => $page));
            exit();
        }
        $info = new InfancePay();
        $info->pay_date = strtotime($this->post('pay_date'));
        $info->online_date = strtotime($this->post('online_date'));
        $info->partner_id = intval($this->post('partner_id'));
        $info->channel_id = intval($this->post('channel_id'));
        $info->payee = trim($this->post('payee'));
        if ($info->partner_id == '') {
            $this->msg(array('state' => 0, 'msgwords' => '合作商不能为空'));
        }
        //判断该渠道是否在使用（正常或暂停）
        //不管自己还是别人都不能对已经上线的渠道再进行打款
        if ($this->post('bid') != 1) {
            $channel_id = $this->post('channel_id');
            if (!$channel_id) $this->msg(array('state' => 0, 'msgwords' => '未选择渠道'));
            $flag = Promotion::model()->isChannelOnline($channel_id);
            if ($flag === false) {
                $this->msg(array('state' => 0, 'msgwords' => '该渠道正在推广，不能进行打款'));
            }
        }
        $info->pay_money = $this->post('pay_money');

        $info->charging_type = $this->post('charging_type');
        $info->business_type = $this->post('bid');
        $info->unit_price = $this->post('unit_price');
        $info->weixin_group_id = intval($this->post('weixin_group_id'));
        if ($info->weixin_group_id < 1) {
            $this->msg(array('state' => 0, 'msgwords' => '微信号小组不能为空'));
            $this->msg(array('state' => 0, 'msgwords' => '微信号小组不能为空'));
        }
        if ($this->get('spcl') == 1) $info->type = 1;//特殊打款
        $info->sno = Yii::app()->admin_user->id;//推广人员
        $info->fans_cost = $this->post('fans_cost');
        $info->fans_input = $this->post('fans_input');
        $info->online_day = $this->post('online_day');
        $info->day_fans_input = $this->post('day_fans_input');
        if ($info->pay_money>0) {
            if ($info->fans_cost == '') {
                $this->msg(array('state' => 0, 'msgwords' => '请输入进粉成本！'));
            }
            if ($info->fans_input  == '') {
                $this->msg(array('state' => 0, 'msgwords' => '请输入预计进粉量！'));
            }
            if ($info->online_day == '') {
                $this->msg(array('state' => 0, 'msgwords' => '请输入预计上线天数！'));
            }
            if ($info->day_fans_input == '') {
                $this->msg(array('state' => 0, 'msgwords' => '请输入预计每日进粉量！'));
            }
        }
        if ($info->fans_cost != '') {
            if (!is_numeric($info->fans_cost)) {
                $this->msg(array('state' => 0, 'msgwords' => '进粉成本请输入数字'));
            }
        }
        if ($info->fans_input != '') {
            if (!is_numeric($info->fans_input) ) {
                $this->msg(array('state' => 0, 'msgwords' => '预计进粉量请输入数字'));
            }
        }
        if ($info->online_day != ''){
            if (!is_numeric($info->online_day)) {
                $this->msg(array('state' => 0, 'msgwords' => '预计上线天数请输入数字'));
            }
        }
        if ($info->day_fans_input != ''){
            if (!is_numeric($info->day_fans_input)) {
                $this->msg(array('state' => 0, 'msgwords' => '预计每日进粉量请输入数字'));
            }
        }
        $dbresult = $info->save();
        $id = $info->primaryKey;
        $msgarr = array('state' => 1, 'url' => $this->createUrl('infancePay/index') . '?p=' . $_GET['p'] . '&search_type=' . $this->get('search_type') . '&search_txt=' . $this->get('search_txt') . ''); //保存的话，跳转到之前的列表
        $logs = "添加了打款ID：$id";
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            //刷新合作商费用日志
//            PartnerCost::model()->refreshPartnerCost($info->channel_id,  $info->online_date);
            if ($this->get('spcl') != 1) {
                if ($info->business_type == 1) {
                    //订阅号直接生成成本明细 订阅号不用改状态
                    StatCostDetail::model()->createBusiness($info->weixin_group_id, $id);
                } else {
                    //新增之后把微信小组状态改成上线
                    WeChatGroup::model()->status($info->weixin_group_id, 1);
                }
            }
            $this->logs($logs);
            //更新客服部计划表
            FinanceServiceData::model()->updateServiceData($id);
            //先弹出操作成功提示，打印数据生成后台运行
            ob_end_clean();
            header("Connection: close");
            header("HTTP/1.1 200 OK");
            ob_start();#开始当前代码缓冲
            $params['msgwords']='添加成功';
            $params['icon']='succeed';
            $back_url = $this->createUrl('infancePay/index') . '?p=' . $_GET['p'] . '&search_type=' . $this->get('search_type') . '&search_txt=' . $this->get('search_txt') . '';
            $params['jscode']='<script>setTimeout(function (){window.location="'.$back_url.'";},1000)</script>';
            $this->renderPartial('/site/msg',array(
                'msg'=>$params,
            ));
            //下面输出http的一些头信息
            $size=ob_get_length();
            header("Content-Length: $size");
            ob_end_flush();#输出当前缓冲
            flush();#输出PHP缓冲
            //更新打印数据
            ignore_user_abort(true); // 后台运行
            set_time_limit(0); // 取消脚本运行时间的超时上限
            InfancePay::model()->createPrintData($id);
            exit();
            //成功跳转提示
        }
    }

    /**
     * 编辑打款
     */
    public function actionEdit()
    {
        $page = array();
        $id = $this->get('id');
        $info = InfancePay::model()->findByPk($id);
        $online_date = $info->online_date;
        $sql = "SELECT * FROM channel WHERE partner_id=(SELECT partner_id FROM finance_pay WHERE id='{$id}')";
        $channel = $this->query($sql);
        //显示表单
        if (!$_POST) {
            $info = $this->toArr($info);
            if (!$info) {
                $this->msg(array('state' => 0, 'msgwords' => '打款不存在'));
            }
            $page['info'] = $info;
            $page['channel']['id'] = $info['channel_id'];
            $page['channel']['partner_id'] = $info['partner_id'];
            $page['channel']['weixin_group_id'] = $info['weixin_group_id'];

            if ($this->get('spcl') == 1) $this->render('spclUpdate', array('page' => $page, 'channel' => $channel));
            else $this->render('update', array('page' => $page, 'channel' => $channel));
            exit();
        }
        //判断该渠道是否在使用（正常或暂停）
        //不管自己还是别人都不能对已经上线的渠道再进行打款
        $channel_id = $this->post('channel_id');
//        $infancePay_status = Promotion::model()->infancePayStatus($id);
//        if($infancePay_status==1){
//            $this->msg(array('state' => 0, 'msgwords' => '该打款已下线，不能修改打款'));
//        }

        $last_pay_date = $info->pay_date;
        $last_online_date = $info->online_date;
        $last_bid = $info->business_type;
        $last_pay_money = $info->pay_money;
        $last_type = $info->type;
        //处理需要的字段
        $info->pay_date = strtotime($this->post('pay_date'));
        $info->online_date = strtotime($this->post('online_date'));
        $info->partner_id = $this->post('partner_id');
        $info->payee = trim($this->post('payee'));

        $info->channel_id = $channel_id;
        if ($info->partner_id == '') {
            $this->msg(array('state' => 0, 'msgwords' => '合作商不能为空'));
        }
        $info->pay_money = $this->post('pay_money');
        $info->charging_type = $this->post('charging_type');
        $info->business_type = $this->post('bid');

        $info->unit_price = $this->post('unit_price');
        $info->weixin_group_id = $this->post('weixin_group_id');

        if ($info->weixin_group_id == '') {
            $this->msg(array('state' => 0, 'msgwords' => '微信号小组不能为空'));
        }
        $info->fans_cost = $this->post('fans_cost');
        $info->fans_input = $this->post('fans_input');
        $info->online_day = $this->post('online_day');
        $info->day_fans_input = $this->post('day_fans_input');
        //打款金额小于等于0时时可不输入进粉成本等信息
        if ($info->pay_money>0) {
            if ($info->fans_cost == '') {
                $this->msg(array('state' => 0, 'msgwords' => '请输入进粉成本！'));
            }
            if ($info->fans_input  == '') {
                $this->msg(array('state' => 0, 'msgwords' => '请输入预计进粉量！'));
            }
            if ($info->online_day == '') {
                $this->msg(array('state' => 0, 'msgwords' => '请输入预计上线天数！'));
            }
            if ($info->day_fans_input == '') {
                $this->msg(array('state' => 0, 'msgwords' => '请输入预计每日进粉量！'));
            }
        }
        if ($info->fans_cost != '') {
            if (!is_numeric($info->fans_cost)) {
                $this->msg(array('state' => 0, 'msgwords' => '进粉成本请输入数字'));
            }
        }
        if ($info->fans_input != '') {
            if (!is_numeric($info->fans_input) ) {
                $this->msg(array('state' => 0, 'msgwords' => '预计进粉量请输入数字'));
            }
        }
        if ($info->online_day != ''){
            if (!is_numeric($info->online_day)) {
                $this->msg(array('state' => 0, 'msgwords' => '预计上线天数请输入数字'));
            }
        }
        if ($info->day_fans_input != ''){
            if (!is_numeric($info->day_fans_input)) {
                $this->msg(array('state' => 0, 'msgwords' => '预计每日进粉量请输入数字'));
            }
        }
        //$info->sno=Yii::app()->admin_user->id;
        $dbresult = $info->save();
        $id = $info->primaryKey;
        $logs = "编辑了打款ID：$id";
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            //刷新合作商费用日志
            if ($last_online_date != $info->online_date || $last_pay_money != $info->pay_money) {
                if ($last_online_date >= $info->online_date) PartnerCost::model()->refreshPartnerCost($channel_id, $info->online_date);
                else PartnerCost::model()->refreshPartnerCost($channel_id, $last_online_date);
            }

            //新增和修改之后的动作
            if ($last_bid == $info->business_type && $last_bid == 1) {
                if ($last_pay_date != $info->pay_date || $last_online_date != $info->online_date) {
                    //修改成本明细时间
                    $condition = "  business_type=1 AND pay_date={$last_pay_date} AND stat_date={$last_online_date} AND charging_type={$info->charging_type} AND channel_id={$info->channel_id}";
                    $sql = "UPDATE stat_cost_detail SET pay_date={$info->pay_date},stat_date={$info->online_date} WHERE " . $condition;
                    Yii::app()->db->createCommand($sql)->execute();
                }
            }
            //修改微信号小组状态 订阅号不用改
            if ($info->weixin_group_id != $this->post('wechatId') && $this->get('spcl') != 1) {
                if ($last_bid == 2) {
                    WeChatGroup::model()->status($this->post('wechatId'), 0);//先
                    if ($info->business_type == 2)
                        WeChatGroup::model()->status($info->weixin_group_id, 1);//后
                } elseif ($info->business_type == 2)
                    WeChatGroup::model()->status($info->weixin_group_id, 1);
            }
            //修改上线日期 渠道数据的上线日期也要改变
            if ($online_date != strtotime($this->post('online_date'))) {
                $data = ChannelData::model()->findAllByAttributes(array('finance_pay_id' => $id));
                foreach ($data as $k => $v) {
                    $data_ = ChannelData::model()->findByPk($data[$k]->id);
                    $data_->online_date = strtotime($this->post('online_date'));
                    $data_->save();
                }

            }
            //更新客服部计划表
            FinanceServiceData::model()->updateServiceData($id);
            $this->logs($logs);
            //先弹出操作成功提示，打印数据生成后台运行
            ob_end_clean();
            header("Connection: close");
            header("HTTP/1.1 200 OK");
            ob_start();#开始当前代码缓冲
            $params['msgwords']='修改成功';
            $params['icon']='succeed';
            $params['jscode']='<script>setTimeout(function (){window.location="'.$this->get('backurl').'";},1000)</script>';
            $this->renderPartial('/site/msg',array(
                'msg'=>$params,
            ));
            //下面输出http的一些头信息
            $size=ob_get_length();
            header("Content-Length: $size");
            ob_end_flush();#输出当前缓冲
            flush();#输出PHP缓冲
            //更新打印数据
            ignore_user_abort(true); // 后台运行
            set_time_limit(0); // 取消脚本运行时间的超时上限
            InfancePay::model()->createPrintData($id);
            exit();
            //成功跳转提示
//            $this->msg($msgarr);
        }
    }

    /**
     * 微信号小组主页面数据处理
     * author: yjh
     */
    public function actionWechatGroup()
    {
        $params['where'] = '';
        $params['where'] .= " and(status=0) ";
        if ($this->get('search_type') == 'keys' && $this->get('search_txt')) {
            $params['where'] .= " and(wechat_group_name like '%" . $this->get('search_txt') . "%') ";
        }
        $params['order'] = "  order by id desc    ";
        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(WeChatGroup::model()->tableName())->listdata($params);

        if (isset($_GET['jsoncallback'])) {
            $data['list'] = $page['listdata']['list'];
            $this->msg(array('state' => 1, 'data' => $data, 'type' => 'jsonp'));
        }
        $this->render('WechatGroup', array('page' => $page));
    }

    /**
     * AJAX获取计费方式
     * author: yjh
     */
    public function actionGetChargingTypes()
    {
        $charging_type = array();
        if ($this->post('bid')) {
            $data = BusinessTypeRelation::model()->findAll('bid=:bid', array(":bid" => $this->post('bid')));
            if (empty($data)) {
                $charging_type[] = array('value' => '','txt'=>'没有计费方式');
            }else {
                $data = $this->toArr($data);
                foreach ($data as $key => $val) {
                    $charging_type[] = array('value' => $val['charging_type'],'txt'=> CHtml::encode(vars::get_field_str('charging_type', $val['charging_type'])));
                }
            }
        }
        $res=array(
            'charging_type'=>$charging_type,
        );
        echo json_encode($res);
    }

    /**
     * 微信号小组主页面数据处理
     * author: yjh
     */
    public function actionInputtip()
    {

        if (isset($_GET['search_txt'])) {
            $result = $_GET['search_txt'];
            $sql = "SELECT id,name FROM partner WHERE name LIKE '%{$result}%'";
            if ($result != '') {
                $query = $this->query($sql);
                $num = count($query);
                if ($num > 0) {
                    $str = json_encode($query);
                    echo $str;
                } else echo "";
            } else echo "";
        } elseif (isset($_GET['partner'])) {
            $result = $_GET['partner'];
            $sql = "SELECT * FROM channel as a LEFT JOIN business_types as b ON a.business_type = b.bid WHERE partner_id={$result}";
            if (isset($_GET['bid'])) $sql .= " and a.business_type=2";
            $query = $this->query($sql);
            $str = json_encode($query);
            echo $str;
        } elseif (isset($_GET['channel_id'])) {
            $result = $_GET['channel_id'];
            $sql = "SELECT * FROM channel WHERE partner_id={$result}";
            $query = $this->query($sql);
            $str = json_encode($query);
            echo $str;
        } elseif ($this->get('search_wechat_txt')) {
            $txt = $this->get('search_wechat_txt');
            if (empty($txt)) {
                echo '';
                exit;
            }
            $bid = intval($this->get('bid'));

            if (!$bid) {
                echo "未选择渠道！";
                exit;
            } elseif ($bid == 1 ) $this->getSubscriptionWechatGroup();
            else $this->getNonSubscriptionWechatGroup($bid);

        } elseif (isset($_GET['search_channel'])) {
            $result = $_GET['search_channel'];
            $sql = "SELECT * FROM channel WHERE channel_name LIKE '%{$result}%'";
            if ($result != '') {
                $query = $this->query($sql);
                $num = count($query);
                if ($num > 0) {
                    $str = json_encode($query);
                    echo $str;
                } else echo "";
            } else echo "";
        } elseif (isset($_GET['search_channel2'])) {
            $result = $_GET['search_channel2'];
            $sql = "SELECT * FROM channel WHERE channel_code LIKE '%{$result}%'";
            if ($result != '') {
                $query = $this->query($sql);
                $num = count($query);
                if ($num > 0) {
                    $str = json_encode($query);
                    echo $str;
                } else echo "";
            } else echo "";
        }
    }

    /**
     * 获取非订阅号微信号小组
     * $bid=2
     * author: yjh
     */
    private function getNonSubscriptionWechatGroup($bid)
    {
        $txt = $this->get('search_wechat_txt');
        $ctype = $this->get('charging_type');
        $status = $this->get('status');
        $sql = "SELECT `id`,`wechat_group_name` FROM `wechat_group` WHERE `wechat_group_name` LIKE '%{$txt}%' AND `business_type`=$bid AND `charging_type`=$ctype";
        if($bid != Yii::app()->params['basic']['dx_bid']){
            $sql .= $status ? " AND `status`=" . $status : " AND `status`=0";
        }
        $query = $this->query($sql);
        if (count($query) > 0) {
            $str = json_encode($query);
            echo $str;
        } else echo "";
    }

    /**
     * 获取订阅号微信号小组
     * $bid=1
     * 规则：一个微信号一天不能推多个合作商
     * 判断合作商是否一样，一样不做限制
     * author: yjh
     */
    private function getSubscriptionWechatGroup()
    {
        $id = $this->get('id');
        $txt = $this->get('search_wechat_txt');
        $ctype = $this->get('charging_type');
        $online_date = strtotime($this->get('online_date'));
        $partner_id = $this->get('partner_id');
        $weChatGroupList = array();
        //按照上线日期和合作商查找 其他合作商上线的微信号小组
        $infancePayList = $id ? Dtable::toArr(InfancePay::model()->findAll('online_date=:online_date and partner_id!=:partner_id and id!=' . $id, array('online_date' => $online_date, ':partner_id' => $partner_id))) :
            Dtable::toArr(InfancePay::model()->findAll('online_date=:online_date and partner_id!=:partner_id', array('online_date' => $online_date, ':partner_id' => $partner_id)));
        foreach ($infancePayList as $k => $v) {
            $weChatGroupList[] = $v['weixin_group_id'];
        }

        //查找符合条件的微信号小组
        $sql = "SELECT `id`,`wechat_group_name` FROM `wechat_group` WHERE `wechat_group_name` LIKE '%{$txt}%'  AND `business_type`=1 AND `charging_type`=$ctype";

        $query = $this->query($sql);
        if ($weChatGroupList) {
            $queryTemp = $query;
            $weChatArr = $intersectArr = $query = array();
            foreach ($weChatGroupList as $k => $v) {
                $weChatArr_Temp = WeChatRelation::model()->getWeChatIds($v);
                array_merge($weChatArr, $weChatArr_Temp);
            }
            $weChatArr = array_unique($weChatArr_Temp);//数组去重，获取当天打款的所有微信号
            foreach ($queryTemp as $k => $v) {
                $weChatArr_Temp = WeChatRelation::model()->getWeChatIds($v['id']);
                $intersectArr = array_intersect($weChatArr, $weChatArr_Temp);//判断两个数组是否有交集
                if (empty($intersectArr)) $query[] = $v;
            }
        }
        if (count($query) > 0) {
            $str = json_encode($query);
            echo $str;
        } else echo "";
    }

    /**
     * 添加续费
     */
    public function actionRenew()
    {
        $page = array();
        $id = $this->get('id');
        $info = InfancePay::model()->findByPk($id);
        $uid = $info->sno;
        if ($info->business_type != 1) {
            if (Yii::app()->admin_user->id != $uid) {
                $this->msg(array('state' => 0, 'msgwords' => '你不能对该打款进行续费'));
            }
        }
        //显示表单
        if (!$_POST) {
            $info = $this->toArr($info);
            if (!$info) {
                $this->msg(array('state' => 0, 'msgwords' => '域名不存在'));
            }
            $page['info'] = $info;
            $this->render('renew', array('page' => $page));
            exit();
        }
        //处理需要的字段
        $info = new InfancePay();
        $info->pay_date = strtotime($this->post('pay_date'));
        $info->online_date = strtotime($this->post('online_date'));
        $info->partner_id = intval($this->post('partner_id'));
        $info->channel_id = intval($this->post('channel_id'));
        $info->pay_money = $this->post('pay_money');
        $info->charging_type = $this->post('charging_type');
        $info->business_type = $this->post('business_type');
        $info->unit_price = $this->post('unit_price');
        $info->payee = $this->post('payee');
        $info->type = 2;
        $info->weixin_group_id = intval($this->post('weixin_group_id'));
        $info->sno = Yii::app()->admin_user->id;
        $info->fans_cost = $this->post('fans_cost');
        $info->fans_input = $this->post('fans_input');
        $info->online_day = $this->post('online_day');
        $info->day_fans_input = $this->post('day_fans_input');
        //打款金额小于等于0时可不输入进粉成本等信息
        if ($info->pay_money>0) {
            if ($info->fans_cost == '') {
                $this->msg(array('state' => 0, 'msgwords' => '请输入进粉成本！'));
            }
            if ($info->fans_input  == '') {
                $this->msg(array('state' => 0, 'msgwords' => '请输入预计进粉量！'));
            }
            if ($info->online_day == '') {
                $this->msg(array('state' => 0, 'msgwords' => '请输入预计上线天数！'));
            }
            if ($info->day_fans_input == '') {
                $this->msg(array('state' => 0, 'msgwords' => '请输入预计每日进粉量！'));
            }
        }
        if ($info->fans_cost != '') {
            if (!is_numeric($info->fans_cost)) {
                $this->msg(array('state' => 0, 'msgwords' => '进粉成本请输入数字'));
            }
        }
        if ($info->fans_input != '') {
            if (!is_numeric($info->fans_input) ) {
                $this->msg(array('state' => 0, 'msgwords' => '预计进粉量请输入数字'));
            }
        }
        if ($info->online_day != ''){
            if (!is_numeric($info->online_day)) {
                $this->msg(array('state' => 0, 'msgwords' => '预计上线天数请输入数字'));
            }
        }
        if ($info->day_fans_input != ''){
            if (!is_numeric($info->day_fans_input)) {
                $this->msg(array('state' => 0, 'msgwords' => '预计每日进粉量请输入数字'));
            }
        }

        if ($info->business_type != 1) {
            //判断该上线日期是否续过费
            $m = InfancePay::model()->isRenew($info->online_date, $info->channel_id);
            if ($m === true) {
                $this->msg(array('state' => 0, 'msgwords' => '该渠道已有相同上线日期的续费'));
            }
        }
        $dbresult = $info->save();
        $id = $info->primaryKey;
        $logs = "添加了续费ID：$id";
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            //刷新合作商费用日志
            PartnerCost::model()->refreshPartnerCost($info->channel_id, $info->online_date);
            //新增之后的动作
            $this->logs($logs);
            if ($info->business_type == 1) {
                //订阅号直接生成成本明细
                StatCostDetail::model()->createBusiness($info->weixin_group_id, $id);
            }
            //更新客服部计划表
            FinanceServiceData::model()->updateServiceData($id);
            //先弹出操作成功提示，打印数据生成后台运行
            ob_end_clean();
            header("Connection: close");
            header("HTTP/1.1 200 OK");
            ob_start();#开始当前代码缓冲
            $params['msgwords']='续费成功!';
            $params['icon']='succeed';
            $params['jscode']='<script>setTimeout(function (){window.location="'.$this->get('backurl').'";},1000)</script>';
            $this->renderPartial('/site/msg',array(
                'msg'=>$params,
            ));
            //下面输出http的一些头信息
            $size=ob_get_length();
            header("Content-Length: $size");
            ob_end_flush();#输出当前缓冲
            flush();#输出PHP缓冲
            //更新打印数据
            ignore_user_abort(true); // 后台运行
            set_time_limit(0); // 取消脚本运行时间的超时上限
            InfancePay::model()->createPrintData($id);
            exit();
        }
    }

    /**
     * 删除打款
     */
    public function actionDelete()
    {
        $id = isset($_GET['id']) && $_GET['id'] != '' ? $_GET['id'] : '';
        $id = intval($id);
        $m = InfancePay::model()->findByPk($id);
        $bid = $m->business_type;
        $pro = Promotion::model()->findByAttributes(array('finance_pay_id' => $m->id));
        $dbresult = $m->delete();

        $logs = "删除了打款ID：$id";
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            //刷新合作商费用日志
            PartnerCost::model()->refreshPartnerCost($m->channel_id, $m->online_date);
            //是否是订阅号打款，需要删除对应生成的成本明细和渠道数据表删除
            if ($bid == 1) {
                //删除成本明细
                $condition = " business_type=1 AND pay_date={$m->pay_date} AND stat_date={$m->online_date} AND charging_type={$m->charging_type} AND channel_id={$m->channel_id}";
                $statCostDetails = StatCostDetail::model()->findAll($condition);
                if ($statCostDetails) {
                    foreach ($statCostDetails as $r) {
                        $r->delete();
                    }
                }
                //删除渠道数据表
                $channelDatas = ChannelData::model()->findAll('finance_pay_id=' . $id);
                if ($channelDatas) {
                    foreach ($channelDatas as $r) {
                        $r->delete();
                    }
                }
            }
            //删除之后的动作，删除对应推广，修改微信号小组状态，修改域名状态
            Promotion::model()->ByInfanceDel($m->id);
            if ($bid == 2)
                WeChatGroup::model()->status($m->weixin_group_id, 0);
            //删除客服部计划数据
            FinanceServiceData::model()->deleteServiceData($id);
            $this->logs($logs);
            //成功跳转提示
            $this->msg(array('state' => 1, 'url' => $this->get('backurl')));
        }

    }

    /**
     * 导出
     */
    public function actionExport()
    {
        $data = $this->getExportData(1);
        $temp_array = array();
        $temp_array[0] = array('-', '-', '-', '-', '-', '-', '-', iconv('utf-8', 'gbk', '合计'), $data['listdata']['money'], '-', '-', '-', '-', '-');
        $data = $data['listdata']['list'];
        $count = count($data);
        for ($i = 0; $i < $count; $i++) {
            $data[$i]['business_type'] = BusinessTypes::model()->find('bid = "' . $data[$i]['business_type'] . '"')['bname'];
            $data[$i]['charging_type'] = vars::get_field_str('charging_type', $data[$i]['charging_type']);

            if ($data[$i]['type'] == 0) {
                $data[$i]['type'] = "普通";
            } elseif ($data[$i]['type'] == 1) {
                $data[$i]['type'] = "特殊";
            } else {
                $data[$i]['type'] = "续费";
            }
            $line = $i + 1;
            $temp_array[$line] = array(
                $data[$i]['id'],//ID
                date('Y-m-d', $data[$i]['online_date']),//上线日期
                $data[$i]['name'],//合作商
                $data[$i]['payee'],//收款人
                $data[$i]['channel_name'],//渠道名称
                $data[$i]['channel_code'],//渠道编码
                $data[$i]['business_type'],//业务类型
                $data[$i]['charging_type'],//计费方式
                $data[$i]['pay_money'],//打款金额
                $data[$i]['unit_price'],//计费单价
                $data[$i]['wechat_group_name'],//微信号小组
                date('Y-m-d', $data[$i]['pay_date']),//付款日期
                $data[$i]['csname_true'],//推广人员
                $data[$i]['type'],//打款方式
            );
            foreach ($temp_array[$line] as $key => $value) {
                $temp_array[$line][$key] = iconv('utf-8', 'gbk', $value);
            }
        }
        $headlist = array('ID', '上线日期', '合作商', '收款人', '渠道名称', '渠道编码', '业务类型', '计费方式', '打款金额', '	计费单价', '微信号小组', '付款日期', '推广人员', '打款方式');
        $file_name = '财务打款-' . date("Ymd");
        helper::downloadCsv($headlist, $temp_array, $file_name);
    }

    public function getExportData($is_export = 0)
    {

        $params['where'] = '';
        //strtotime($_GET['pay_date']);
        //上线日期搜索
        if ($this->get('stat_date_s') != '' && $this->get('stat_date_e') != '') {
            $start = strtotime($this->get('stat_date_s'));
            $end = strtotime($this->get('stat_date_e')) + 86400 - 1;
            $params['where'] .= " and(a.online_date between $start and $end ) ";
        }
        //付款日期搜索
        if ($this->get('pay_date_s') != '' && $this->get('pay_date_e') != '') {
            $start = strtotime($this->get('pay_date_s'));
            $end = strtotime($this->get('pay_date_e')) + 86400 - 1;
            $params['where'] .= " and(a.pay_date between $start and $end ) ";
        }
        //计费方式搜索
        if ($this->get('charging_type') != '') {
            $params['where'] .= " and(a.charging_type=" . intval($this->get('charging_type')) . ") ";
        }
        if ($this->get('business_type') != '') {
            $params['where'] .= " and(a.business_type=" . intval($this->get('business_type')) . ") ";

        }
        //打款类型搜索
        if ($this->get('fpay_type') != '') {
            $params['where'] .= " and(type=" . intval($this->get('fpay_type')) . ") ";
        }
        //收款人
        if ($this->get('payee') != '') $params['where'] .= " and(a.payee  like '%" . $this->get('payee') . "%') ";

        //操作用户
        if ($this->get('user_id') != '') $params['where'] .= " and(a.sno = " . $this->get('user_id') . ") ";
        //合作商，渠道名称，渠道编码搜索
        if ($this->get('search_type') == 'partner_name' && $this->get('search_txt')) {
            $params['where'] .= " and(partner.name like '%" . $this->get('search_txt') . "%') ";
        } else if ($this->get('search_type') == 'channel_name' && $this->get('search_txt')) {
            $params['where'] .= " and(channel.channel_name like '%" . $this->get('search_txt') . "%') ";
        } else if ($this->get('search_type') == 'channel_code' && $this->get('search_txt')) {
            $params['where'] .= " and(channel.channel_code like '%" . $this->get('search_txt') . "%') ";
        }
        if ($this->get('wechat_group_name') != '') $params['where'] .= " and(wechat_group.wechat_group_name  like '%" . $this->get('wechat_group_name') . "%') ";

        //查看人员权限
        $result = $this->data_authority();
        if ($result != 0) {
            $params['where'] .= " and(a.sno in ($result)) ";
        }
        $params['join'] = " LEFT JOIN channel ON a.channel_id = channel.id 
                            LEFT JOIN partner ON a.partner_id=partner.id 
                            LEFT JOIN wechat_group ON a.weixin_group_id=wechat_group.id 
                            LEFT JOIN cservice ON a.sno=cservice.csno
                            LEFT JOIN business_types ON a.business_type=business_types.bid";
        $params['order'] = "  order by a.id desc    ";
        $params['select'] = " a.payee,a.type,a.partner_id,a.id,a.online_date,a.pay_date,a.pay_money,a.charging_type,a.unit_price,cservice.csname_true,partner.name,channel.channel_code,a.business_type,channel.channel_name,wechat_group.wechat_group_name,business_types.bname";
        $params['pagesize'] = 1 == $is_export ? 20000 : Yii::app()->params['management']['pagesize'];
//        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model('finance_pay')->listdata($params);
        //判断打款的推广是否已下线
        if (!$is_export) {
            $pay_ids = array_column($page['listdata']['list'],'id');
            if ($pay_ids) {
                $sql = "select finance_pay_id from promotion_manage where status=1 and finance_pay_id in (".implode(',',$pay_ids).")";
                $down_pro = Yii::app()->db->createCommand($sql)->queryAll();
                $pro_ids = array_column($down_pro,'finance_pay_id');
                foreach ($page['listdata']['list'] as $key=>$value) {
                    $page['listdata']['list'][$key]['is_down'] = 0;
                    if (in_array($value['id'],$pro_ids)) {
                        $page['listdata']['list'][$key]['is_down'] = 1;
                    }
                }
            }
        }

        $sql = "select sum(a.pay_money) as pay_money from finance_pay as a " . $params['join'] . " where 1 " . $params['where'];
        $totalMoney = Yii::app()->db->createCommand($sql)->queryAll();
        $page['listdata']['money'] = $totalMoney[0]['pay_money'];

        $page['listdata']['url'] = urlencode('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"]);
        return $page;
    }

    /**
     * 根据打款id获取其他数据
     * author: yjh
     */
    public function actionGetDataById()
    {
        $fpay_id = $this->get('fpay_id');
        if (!$fpay_id) exit;
        $info = InfancePay::model()->findByPk($fpay_id);
        if (!$info) {
            echo '';
            exit;
        }
        $wechatGroupInfo = WeChatGroup::model()->findByPk($info->weixin_group_id);


        $ret = array(
            'wechat_group_name' => $wechatGroupInfo->wechat_group_name,
            'wechat_group_id' => $info->weixin_group_id
        );
        echo json_encode($ret);
        exit;
    }

    public function actionPrint()
    {
        $pay_id = $this->get('id');
        $model = new InfancePay();
        $pay_info = $model->findByPk($pay_id);
        $partner_info = Partner::model()->findByPk($pay_info->partner_id);
        $channel_info = Channel::model()->findByPk($pay_info->channel_id);
        $business_info = BusinessTypes::model()->findByPk($pay_info->business_type);
        $channel_type = Dtable::toArr(ChannelType::model()->findByPk($channel_info->type_id));
        $user = AdminUser::model()->findByPk($pay_info->sno);
        $sevice_group = WeChatGroup::model()->getServiceWechat($pay_info->weixin_group_id);
        $service_id = array_unique(array_keys($sevice_group));
        $service_names='';
        if ($service_id) {
            $service = Dtable::toArr(CustomerServiceManage::model()->findAll('id in ('.implode(',',$service_id).')'));
            $names = array_column($service,'cname');
            $service_names = implode(',',$names);
        }
        //查询微信号个数
        $wechat_count = 0 ;
        if ($pay_info->weixin_group_id){
            $wechat_count = WeChatRelation::model()->count('wechat_group_id='.$pay_info->weixin_group_id);
        }
        //渠道余额
        $channel_balance = Yii::app()->db->createCommand()
            ->select('c.channel_balance')
            ->from('partner_cost_log c')
            ->join('finance_pay  p', 'c.infance_id=p.id')
            ->where("c.channel_id=".$pay_info->channel_id.' and p.sno='.$pay_info->sno)
            ->order(' date desc')
            ->limit(1)
            ->queryRow();
        $balance = $channel_balance['channel_balance']?$channel_balance['channel_balance']:0;
        $pay_info = array(
            'pay_date'=>date('Y-m-d',$pay_info->pay_date),
            'online_date'=>date('Y-m-d',$pay_info->online_date),
            'pay_money'=>$pay_info->pay_money,
            'wechat_count'=>$wechat_count,
            'partner'=>$partner_info->name,
            'channel'=>$channel_info->channel_name,
            'business_type'=>$business_info->bname,
            'channel_balance'=>$balance,
            'channel_type'=>$channel_type['type_name'],
            'charging_type'=>vars::get_field_str('charging_type', $pay_info->charging_type),
            'fans_cost'=>$pay_info->fans_cost,
            'fans_input'=>$pay_info->fans_input,
            'online_day'=>$pay_info->online_day,
            'day_fans_input'=>$pay_info->day_fans_input,
            'service_names'=>$service_names,
            'user_name'=>$user->csname_true,
        );
        $cache = Yii::app()->cache;
        $printData = $cache->get('printData');
        $records = unserialize($printData);
        $print_data = $records['printData_'.$pay_id];
        $page = array(
            'create_time'=>$print_data['create_time'],
            'pay_info'=>$pay_info,
            'print_channel_data'=>$print_data['channel_data'],
            'print_partner_data'=>$print_data['partner_data'],
            'channel_type'=>$channel_type,
        );
        $this->render('print', array('page' => $page));
        exit();
    }
}