<?php
/**
 * 友盟统计列表
 * author: yjh
 */
class StatCnzzController extends AdminController
{
    public function actionIndex()
    {
        $_GET['id'] = isset($_GET['id']) ? $_GET['id'] : '';
        //搜索
        $params['where'] = '';
        if ($this->get('search_type') == 'domain' && $this->get('search_txt')) {
            $params['where'] .= " and(a.domain like '%" . $this->get('search_txt') . "%') ";
        } else if ($this->get('search_type') == 'partner' && $this->get('search_txt')) {
            $params['where'] .= " and(d.name like '%" . $this->get('search_txt') . "%') ";
        } else if ($this->get('search_type') == 'channel_code' && $this->get('search_txt')) {
            $params['where'] .= " and(c.channel_code like '%" . $this->get('search_txt') . "%') ";
        } else if ($this->get('search_type') == 'channel_name' && $this->get('search_txt')) {
            $params['where'] .= " and(c.channel_name like '%" . $this->get('search_txt') . "%') ";
        }
        //推广类型
        if ($this->get('promotion_type') != '') {
            $params['where'] .= " and( p.promotion_type = " . $this->get('promotion_type') . ") ";
        }

        if ($this->get('stat_start_time') && $this->get('stat_end_time')) { //
            $start_time = strtotime($this->get('stat_start_time'));
            $end_time = strtotime($this->get('stat_end_time'));
            $params['where'] .= " and(a.stat_date>=$start_time  and a.stat_date<=$end_time) ";
        } elseif ($this->get('stat_start_time')) { //
            $start_time = strtotime($this->get('stat_start_time'));
            $params['where'] .= " and(a.stat_date>=$start_time) ";
        } elseif ($this->get('stat_end_time')) {
            $end_time = strtotime($this->get('stat_end_time'));
            $params['where'] .= " and(a.stat_date<=$end_time) ";
        }
        //操作用户
        if ($this->get('user_id') != '') $params['where'] .= " and(a.promotion_staff_id = " . $this->get('user_id') . ") ";

        //查看人员权限
        $result = $this->data_authority();
        if ($result != 0) {
            $params['where'] .= " and(a.promotion_staff_id in ($result)) ";
        }

        $params['order'] = "  order by a.id desc      ";
        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['join'] = "
        left join stat_channel as b on b.id=a.stat_channel_id
        left join channel as c on c.id=b.channel_id
        left join partner as d on d.id=b.partner_id
        left join promotion_manage as p on p.id=a.promotion_id
        left join cservice as m on m.csno=a.promotion_staff_id 
        ";
        $params['pagebar'] = 1;
        //$params['select']=" a.*,b.*,c.channel_name,c.channel_code,d.name as partner_name ";
        $params['select'] = " b.*,p.promotion_type,c.channel_name,c.channel_code,d.name as partner_name,m.csname_true,a.* ";
        $params['smart_order'] = 1;
        //$params['debug']=1;
        $page['listdata'] = Dtable::model(StatCnzzFlow::model()->tableName())->listdata($params);
        $sql = "SELECT SUM(a.ip) AS ip,SUM(a.uv) AS uv,SUM(a.pv) AS pv FROM stat_cnzz_flow AS a " . $params['join'] . " where 1 " . $params['where'];
        $sumInfo = Yii::app()->db->createCommand($sql)->queryAll();
        $page['listdata']['ip'] = $sumInfo[0]['ip'];
        $page['listdata']['uv'] = $sumInfo[0]['uv'];
        $page['listdata']['pv'] = $sumInfo[0]['pv'];

        $this->render('index', array('page' => $page));

    }

    /**
     * 添加统计
     * author: yjh
     * 2017-8-30
     */
    public function actionAdd()
    {
        $page = array();
        //显示表单
        if (!$_POST) {
            $this->render('update', array('page' => $page));
            exit();
        }
        $promotion_id = $this->post('promotion_id');//推广id
        $stat_date = strtotime($this->post('stat_date'));//上线日期
        $ip = $this->post('ip');
        $uv = $this->post('uv');
        $pv = $this->post('pv');
        $outlint_date = strtotime(date('Ymd', time())) - 86400;
        if ($stat_date == '') $this->msg(array('state' => 0, 'msgwords' => '日期不能为空'));
        if ($ip == '') $this->msg(array('state' => 0, 'msgwords' => '未填写IP'));
        if (!is_numeric($ip)) $this->msg(array('state' => 0, 'msgwords' => 'IP要填数字！'));
        if ($uv == '') $this->msg(array('state' => 0, 'msgwords' => '未填写UV'));
        if (!is_numeric($uv)) $this->msg(array('state' => 0, 'msgwords' => 'UV要填数字！'));
        if ($pv == '') $this->msg(array('state' => 0, 'msgwords' => '未填写PV'));
        if (!is_numeric($pv)) $this->msg(array('state' => 0, 'msgwords' => 'PV要填数字！'));

        //只能到40天内的数据
        if ($stat_date <= $outlint_date - 86400 * 39)
            $this->msg(array('state' => 0, 'msgwords' => '太久远的友盟数据不能导入！'));
        //1.先判断添加的友盟是否在上下线日期内
        $promotionInfo = Promotion::model()->findByPk($promotion_id);
        $promotion_type = $promotionInfo->promotion_type;
        if ($promotion_type!=1 && $promotion_id == '') $this->msg(array('state' => 0, 'msgwords' => '请选择受访域名'));
        $channel_id = $promotionInfo->channel_id;
        $InfanceInfo = InfancePay::model()->findByPk($promotionInfo->finance_pay_id);
        $online_date = $InfanceInfo->online_date;
        if ($promotionInfo->status == 1) $outlint_date = $promotionInfo->outline_date;
        if ($stat_date < $online_date || $stat_date > $outlint_date)
            $this->msg(array('state' => 0, 'msgwords' => '选择的导入日期超出该推广的可导入的日期！'));
        //2.判断友盟中是否有这条友盟数据，没有的话才能导入
        $condition = array('stat_date' => $stat_date, 'promotion_id' => $promotion_id);
        $statCnzzInfo = StatCnzzFlow::model()->findByAttributes($condition);
        if ($statCnzzInfo)
            $this->msg(array('state' => 0, 'msgwords' => '选择的导入日期已经有该条友盟数据！'));
        //3.还要判断成本明细，修正成本，合作商费用日志是否存在,存在终止操作
        $m1 = StatCostDetail::model()->find('channel_id=' . $channel_id . ' and stat_date =' . $stat_date);
        if ($m1) $this->msg(array('state' => 0, 'msgwords' => '选择的导入日期已经有该条成本明细！'));
        $m2 = PartnerCost::model()->find('channel_id=' . $channel_id . ' and date =' . $stat_date);
        if ($m2) $this->msg(array('state' => 0, 'msgwords' => '选择的导入日期已经有该条合作商费用日志！'));
        $m3 = FixedCost::model()->find('channel_id=' . $channel_id . ' and stat_date =' . $stat_date);
        if ($m3) $this->msg(array('state' => 0, 'msgwords' => '选择的导入日期已经有该条修正成本！'));
        //准备导入，处理需要的字段
        $domains = PromotionDomain::model()->getPromotionsDomains($promotion_id);
        $domains = array_column($domains[$promotion_id],'domain');
        $domain_str = implode(',',$domains);
        $info = new StatCnzzFlow();
        $info->stat_date = $stat_date;
        $info->promotion_id = $promotion_id;
        $info->domain = $domain_str;
        $info->promotion_staff_id = $InfanceInfo->sno;
        $info->ip = $ip;
        $info->uv = $uv;
        $info->pv = $pv;
        $info->create_time = time();
        $statChannel = new StatChannel();
        $statChannel->domain = $domain_str;
        $statChannel->partner_id = $InfanceInfo->partner_id;
        $statChannel->channel_id = $channel_id;
        $statChannel->create_time = time();
        $statChannel->save();
        $id = $statChannel->id;
        $info->stat_channel_id = $id;
        $dbresult = $info->save();

        if ($dbresult === false) $this->msg(array('state' => 0, 'msgwords' => '生成友盟记录失败！'));
        //记录域名导入
        foreach ($domains as $domain) {
            $statDomainRecord = new StatDomainRecord();
            $statDomainRecord->stat_date = $stat_date;
            $statDomainRecord->domain = $domain;
            $statDomainRecord->cnzz_id = $info->id;
            $statDomainRecord->save();
        }

        //生成成本明细，修正成本和合作商费用日志
        if($promotion_type==1){
            $ret = StatCostDetail::model()->nonDomainCreate($promotion_id, $stat_date);
        }else{
            $ret = StatCostDetail::model()->create($promotion_id, $stat_date);
        }
        if ($ret['state'] < 1) $this->msg(array('state' => 0, 'msgwords' => '生成成本明细失败！'));
        $ret = FixedCost::model()->create($promotion_id, $stat_date);
        if ($ret['state'] < 1) $this->msg(array('state' => 0, 'msgwords' => '生成修正成本失败！'));
        if($promotion_type==1){
            $ret = PartnerCost::model()->nonDomainCreate($promotion_id, $stat_date);
        }else{
            $ret = PartnerCost::model()->create($promotion_id, $stat_date);

        }
        if ($ret['state'] < 1) $this->msg(array('state' => 0, 'msgwords' => '生成合作商费用日志失败！'));

        //刷新合作商费用日志
//        PartnerCost::model()->refreshPartnerCost($channel_id, $stat_date);
//
        $msgarr = array('state' => 1, 'url' => $this->createUrl('statCnzz/index') . '?p=' . $_GET['p'] . ''); //保存的话，跳转到之前的列表
        $logs = "人工添加了一天友盟统计，日期： " . $this->post('stat_date') . ';推广id：' . $promotion_id;

        $this->logs($logs);
        //成功跳转提示
        $this->msg($msgarr);

    }

    /**
     * 友盟统计导入
     * 2016 这段代码只有我和上帝知道
     * 2017.09.26 现在只剩下上帝了
     * author: yjh
     */
    public function actionImport()
    {
        if (isset($_POST['submit'])) {
            $file = CUploadedFile::getInstanceByName('filename');//
            if (!$file) $this->msg(array('state' => 0, 'msgwords' => '未选择文件！'));
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
                    $total_column = 'F';
                    $list = array();
                    $domain_list = array();
                    for ($row = 7; $row <= $total_line; $row++) {
                        $data = array();
                        for ($column = 'A'; $column <= $total_column; $column++) {
                            $data[] = trim($phpexcel->getCell($column . $row)->getValue());
                        }
                        $domain_list[] = $data[0];
                        $list[] = $data;
                    }
                    $datestr = $phpexcel->getCell('A4');
                    //echo $datestr;
                    $start_date = '';
                    $end_date = '';
                    if (preg_match_all('~\d{4}-\d{2}-\d{2}~', $datestr, $result)) {
                        $start_date = $result[0][0];
                        $end_date = $result[0][1];
                    }

                    if (!$start_date || !$end_date) {
                        echo 'Excel里的日期错误';
                        die();
                    }
                    if ($start_date != $end_date) {
                        echo '从cnzz里导出来的时候不能跨日期选择噢 <br> ';
                        die();
                    }
                    $stat_date = strtotime($start_date);
                   

                    //免域推广处理（查找免域推广->友盟是否存在->是否添加数据）
                    $mInfo = $this->toArr(Promotion::model()->findAll("promotion_type=1 and status=0 and domain_id!=0 "));
                    if ($mInfo) {
                        foreach ($mInfo as $val) {
                            $financeInfo = InfancePay::model()->findByPk($val['finance_pay_id']);
                            if($financeInfo->online_date>$stat_date) continue;
                            $addArr = array();
                            $temp = StatCnzzFlow::model()->find("stat_date = $stat_date and promotion_id=" . $val['id']);
                            if ($temp) continue;
                            if (!$val['domain_id']) continue;
                            $addArr[0] = DomainList::model()->getDomainByPk($val['domain_id']);
                            $addArr[1] = 0;
                            $addArr[2] = 0;
                            $addArr[3] = 0;
                            $list[] = $addArr;
                        }
                    }

                    echo '<div class="msgbox0009">';
                    
                    $promotionArr = array();
                    //记录未有当天cnzz统计记录的推广id,避免生成同一日期多条无域名推广的合作商费用日志等记录
                    $noneStatCnzz = array();
                    foreach ($list as $r) {
                        $domain = trim($r[0]);
                        if (!$domain) {
                            continue;
                        }
                        $pv = $r[1];
                        $uv = $r[2];
                        $ip = $r[3];
                        if (preg_match('~\s~', $domain)) {
                            continue;
                        }
                        //$domainModel=DomainList::model()->findByAttributes(array('domain'=>$domain));
                        //获取域名对应时间对应的推广
                        $promotion_id = DomainPromotionChange::model()->getPromotionID($domain, $stat_date);
                        if ($promotion_id == -1) {
                            echo $domain . ' ,未找到该域名<br>';
                            continue;
                        } elseif ($promotion_id == -2) {
                            echo $domain . ' ,未找到该域名对应的推广<br>';
                            continue;
                        }
                        //储存推广id
                        if (!in_array($promotion_id, $promotionArr)) {
                            $promotionArr[] = $promotion_id;
                        }
                        $promotion = Promotion::model()->findByPk($promotion_id);
                        $promotion_type = vars::get_field_str('promotion_types', $promotion->promotion_type);

                        $channel_id = $promotion->channel_id;
                        $channel = Channel::model()->findByPk($channel_id);
                        if (!$channel) {
                            echo $domain . ' ,该域名的推广渠道无法找到<br>';
                            continue;
                        }
                        $partner_id = $channel->partner_id;

                        $statDomainRecord = StatDomainRecord::model()->findByAttributes(array('stat_date' => $stat_date, 'domain' => $domain));
                        if ($statDomainRecord) {
                            echo $domain . ' ,该域名已导入过,无须再次导入<br>';
                            continue;
                        }
                        //是否已有该推广的cnzz记录
                        $pro_cnzz = StatCnzzFlow::model()->find("stat_date = $stat_date and promotion_id=" . $promotion_id);
                        if (!$pro_cnzz) {
                            $noneStatCnzz[] = $promotion_id;
                        }
                        $domains =  PromotionDomain::model()->getPromotionsDomains($promotion_id);
                        $pro_domains = array_column($domains[$promotion_id],'domain');;
                        //要查看是否已有此条推广 有则更新 无则新增
                        if ($pro_cnzz) {
                            $pro_cnzz->ip += $ip;
                            $pro_cnzz->uv += $uv;
                            $pro_cnzz->pv += $pv;
                            $result = $pro_cnzz->save();
                            $cid = $pro_cnzz->primaryKey;
                        } else {
                            $statCnzz = new StatCnzzFlow();
                            $statCnzz->domain = implode(',',$pro_domains);
                            $statCnzz->stat_date = $stat_date;
                            $statCnzz->ip = $ip;
                            $statCnzz->uv = $uv;
                            $statCnzz->pv = $pv;
                            $statCnzz->promotion_id = $promotion_id;
                            $promotion_staff_id = InfancePay::model()->findByPk($promotion->finance_pay_id)->sno;
                            $statCnzz->promotion_staff_id = $promotion_staff_id;
                            $statCnzz->create_time = time();

                            $statChannel = new StatChannel();
                            $statChannel->domain = implode(',',$pro_domains);
                            $statChannel->partner_id = $partner_id;
                            $statChannel->channel_id = $channel_id;
                            $statChannel->create_time = time();
                            $statChannel->save();
                            $id = $statChannel->id;
                            $statCnzz->stat_channel_id = $id;
                            $result = $statCnzz->save();
                            $cid = $statCnzz->primaryKey;
                        }
                        //记录域名导入
                        $statDomainRecord = new StatDomainRecord();
                        $statDomainRecord->stat_date = $stat_date;
                        $statDomainRecord->domain = $domain;
                        $statDomainRecord->cnzz_id = $cid;
                        $statDomainRecord->save();

                        if ($result) {
                            echo $domain . '(' . $promotion_type . '),导入成功! <br/>';
                        } else {
                            echo $domain . ' ,导入失败! <br/> ';
                        }
                    }
                     //导入无数据的标准推广
                    $sql = "SELECT a.id,a.finance_pay_id,a.channel_id FROM promotion_manage as a LEFT JOIN finance_pay as c ON c.id=a.finance_pay_id WHERE a.status=0 and a.promotion_type in(0,2,3) and c.online_date<=".$stat_date;
                    $totalArrs = Yii::app()->db->createCommand($sql)->queryAll();
                    $pro_ids = array_column($totalArrs,'id');
                    $pro_domains = array();
                    if ($pro_ids) {
                        $pro_domains = PromotionDomain::model()->getPromotionsDomains($pro_ids);
                    }
                    foreach ($totalArrs as $k => $v) {
                        $is_miss = in_array($v['id'], $promotionArr);
                        $p_domain = array_column($pro_domains[$v['id']],'domain');
                        if ($is_miss == false) {
                            $temp = StatCnzzFlow::model()->find("stat_date = $stat_date and promotion_id=" . $v['id']);
                            if ($temp) continue;
                             $channel = Channel::model()->findByPk($v['channel_id']);
                            if (!$channel) {
                                echo '推广'.$v['id'].'的渠道无法找到<br>';
                                continue;
                            }
                            $promotionArr[] = $v['id'];
                            $partner_id = $channel->partner_id;
                            $statCnzz = new StatCnzzFlow();
                            $statCnzz->domain = implode(',',$p_domain);
                            $statCnzz->stat_date = $stat_date;
                            $statCnzz->ip = 0;
                            $statCnzz->uv = 0;
                            $statCnzz->pv = 0;
                            $statCnzz->promotion_id = $v['id'];
                            $promotion_staff_id =  InfancePay::model()->findByPk($v['finance_pay_id'])->sno;
                            $statCnzz->promotion_staff_id = $promotion_staff_id;
                            $statCnzz->create_time = time();

                            $statChannel = new StatChannel();
                            $statChannel->domain = implode(',',$p_domain);
                            $statChannel->partner_id = $partner_id;
                            $statChannel->channel_id = $v['channel_id'];
                            $statChannel->create_time = time();
                            $statChannel->save();
                            $id = $statChannel->id;
                            $statCnzz->stat_channel_id = $id;
                            $result = $statCnzz->save();
                            $cid = $statCnzz->primaryKey;
                            if ($result) {
                                echo '推广'.$v['id'] . '(标准),导入成功! <br/>';
                            } else {
                                echo $domain . ' ,导入失败! <br/> ';
                            }
                        }
                    }

                    //导入域名为空的免域推广
                    $nonDomainPromotion = array();
                    //无域名的推广
                    $allNonDomain = array();
                    $fInfo = $this->toArr(Promotion::model()->findAll("promotion_type=1 and status=0 and domain_id=0"));
                    if ($fInfo) {
                        foreach ($fInfo as $val) {
                            if (!in_array($val['id'], $allNonDomain)) {
                                $allNonDomain[] = $val['id'];
                            }
                            $temp = StatCnzzFlow::model()->find("stat_date = $stat_date and promotion_id=" . $val['id']);
                            if ($temp) continue;
                            //储存推广id
                            if (!in_array($val['id'], $nonDomainPromotion)) {
                                $nonDomainPromotion[] = $val['id'];
                            }
                            $statCnzz = new StatCnzzFlow();
                            $statCnzz->domain = "无";
                            $statCnzz->stat_date = $stat_date;
                            $statCnzz->ip = 0;
                            $statCnzz->uv = 0;
                            $statCnzz->pv = 0;
                            $statCnzz->promotion_id = $val['id'];
                            $promotion_staff_id = InfancePay::model()->findByPk($val['finance_pay_id'])->sno;
                            $statCnzz->promotion_staff_id = $promotion_staff_id;
                            $statCnzz->create_time = time();

                            $statChannel = new StatChannel();
                            $statChannel->domain = "无";
                            $statChannel->channel_id = $val['channel_id'];
                            $statChannel->partner_id = Channel::model()->findByPk($val['channel_id'])->partner_id;
                            $statChannel->create_time = time();
                            $statChannel->save();
                            $id = $statChannel->id;
                            $statCnzz->stat_channel_id = $id;
                            $result = $statCnzz->save();
                            $cid = $statCnzz->primaryKey;
                            if ($result) {
                                echo "推广" . $val['id'] . '(标准)生成友盟记录成功! <br/>';
                            } else {
                                echo "推广" . $val['id'] . '(标准)生成友盟记录失败! <br/>';
                            }
                        }
                    }
                    //有域名的推广生成成本明细
                    //删除无域名的推广id
                    $promotion_has_domain = array_diff($promotionArr,$allNonDomain);
                    //无域名的推广
                    $promotion_none_domain = array_intersect($noneStatCnzz,$allNonDomain);
                    foreach ($promotion_has_domain as $r) {
                        // $promotionInfo = Dtable::toArr(Promotion::model()->findByPk($r));
                        $ret = StatCostDetail::model()->create($r, $stat_date);
                        if ($ret['state'] < 1) {
                            echo 'ID: ' . $r . ' 的推广 创建成本明细失败[' . $ret['msgwords'] . ']<br>';
                        } else {
                            echo 'ID: ' . $r . ' 的推广 ' . $ret['msgwords'] . '<br>';
                        }
                        $ret = FixedCost::model()->create($r, $stat_date);
                        if ($ret['state'] < 1) {
                            echo 'ID: ' . $r . ' 的推广 创建修正成本失败[' . $ret['msgwords'] . ']<br>';
                        } else {
                            echo 'ID: ' . $r . ' 的推广 ' . $ret['msgwords'] . '<br>';
                        }
                        $ret = PartnerCost::model()->create($r, $stat_date);
                        if ($ret['state'] < 1) {
                            echo 'ID: ' . $r . ' 的推广 创建合作商费用日志失败[' . $ret['msgwords'] . ']<br>';
                        } else {
                            echo 'ID: ' . $r . ' 的推广 ' . $ret['msgwords'] . '<br>';
                        }
                    }
                    //免域推广无域名生成成本明细
                    $allNonDomainPromotion = array_merge($nonDomainPromotion,$promotion_none_domain);
                    foreach ($allNonDomainPromotion as $r) {
                        $ret = StatCostDetail::model()->nonDomainCreate($r, $stat_date);
                        if ($ret['state'] < 1) {
                            echo 'ID: ' . $r . ' 的推广 创建成本明细失败[' . $ret['msgwords'] . ']<br>';
                        } else {
                            echo 'ID: ' . $r . ' 的推广 ' . $ret['msgwords'] . '<br>';
                        }
                        $ret = FixedCost::model()->create($r, $stat_date);
                        if ($ret['state'] < 1) {
                            echo 'ID: ' . $r . ' 的推广 创建修正成本失败[' . $ret['msgwords'] . ']<br>';
                        } else {
                            echo 'ID: ' . $r . ' 的推广 ' . $ret['msgwords'] . '<br>';
                        }
                        $ret = PartnerCost::model()->nonDomainCreate($r, $stat_date);
                        if ($ret['state'] < 1) {
                            echo 'ID: ' . $r . ' 的推广 创建合作商费用日志失败[' . $ret['msgwords'] . ']<br>';
                        } else {
                            echo 'ID: ' . $r . ' 的推广 ' . $ret['msgwords'] . '<br>';
                        }
                    }
                    echo '</div><script>setTimeout("parent.show_frame_infos();",2000)</script>';
                    exit;
                }

            } else {
                $this->msg(array('state' => 0, 'msgwords' => '文件不存在'));
            }
        }
    }

    /**
     * 批量删除友盟统计
     * author: yjh
     * 8-28修改
     * 删除友盟需要同时删除合作商费用日志和成本明细
     */
    public function actionDelete()
    {

        $idstr = $this->get('ids');
        $ids = explode(',', $idstr);
     
        foreach ($ids as $id) {
            $m = StatCnzzFlow::model()->findByPk($id);
            $stat_date = $m->stat_date;
            $cnzz_id = $m->id;
            $m->delete();
            $m2 = StatChannel::model()->findByPk($m->stat_channel_id);
            $channel_id=$m2->channel_id;
            if (!$m2) continue;
            $m2->delete();
            $m3 = $this->toArr(StatDomainRecord::model()->findAll('stat_date=:stat_date and cnzz_id = :cnzz_id', array(':stat_date' => $stat_date, ':cnzz_id' => $cnzz_id)));
            if ($m3){
                foreach ($m3 as $k => $r) {
                    $m4 = StatDomainRecord::model()->findByPk($r['id']);
                    $m4->delete();
                }
            }
            //删除友盟数据,相应删除对应的成本明细，对应的合作商费用日志，对应的修正成本中的相关数据一同删除
            //成本明细 条件（日期，渠道id,）
            $statCostDetails=StatCostDetail::model()->findAll('channel_id='.$channel_id. ' and stat_date ='.$stat_date);
            foreach ($statCostDetails as $v) $v->delete();
            //合作商费用日志
            $partnerCosts = PartnerCost::model()->findAll('channel_id='.$channel_id. ' and date ='.$stat_date);
            foreach ($partnerCosts as $v) $v->delete();
            //修正成本
            $fixedCosts = FixedCost::model()->findAll('channel_id='.$channel_id. ' and stat_date ='.$stat_date);
            foreach ($fixedCosts as $v) $v->delete();
            PartnerCost::model()->refreshPartnerCost($channel_id, $stat_date);
        }
        $this->logs('删除了统计ID（' . $idstr . '）');
        $this->msg(array('state' => 1));
    }

}
