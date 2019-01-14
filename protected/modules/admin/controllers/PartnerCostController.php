<?php

/**
 * 合作商费用日志控制器
 * User: fang
 * Date: 2016/12/13
 * Time: 14:41
 */
class PartnerCostController extends AdminController {
	public function actionIndex() {
        $page=$this->getData();
		$this->render('index', array('page' => $page));
	}

	/**
     * 删除合作商费用
     */
    public function actionDelete() {
		$ids = isset($_GET['ids']) && $_GET['ids'] != '' ? $_GET['ids'] : '';
		$ids = explode(',', $ids);
		$logs = "删除了合作商费用日志：";
		foreach ($ids as $id) {
			$id = intval($id);
			if ($id == 0) {
				continue;
			}

			$m = PartnerCost::model()->findByPk($id);
			$m->delete();
			$logs .= $id . ",";
		}
		$this->logs($logs);
		$this->msg(array('state' => 1));
	}

    /**
     *编辑合作商费用
     */
	public function actionEdit() {
		$this->actionToEdit();
	}

    /**
     *编辑合作商费用
     */
	public function actionToEdit() {
		$page = array();
		$id = $this->get('id');
		$info = PartnerCost::model()->findByPk($id);
		//显示表单
		if (!$_POST) {
			$info = $this->toArr($info);

			if (!$info) {
				$this->msg(array('state' => 0, 'msgwords' => '费用日志不存在'));
			}
			$page['info'] = $info;
			$this->render('update', array('page' => $page));
			exit();
		}
		//处理需要的字段
		$info->partner_cost = $this->post('partner_cost') === '' ? null : $this->post('partner_cost');
		if ($info->partner_cost && !is_numeric($info->partner_cost)) {
			$this->msg(array('state' => 0, 'msgwords' => '合作商费用日志要填数字！'));
		}

		//之前的合作商费用必须要先填
		$lastData = PartnerCost::model()->find("channel_id = " . $info->channel_id . "   and date < $info->date order by date desc ");
		if ($lastData) {
		    if($lastData->partner_cost===null) {
                $this->msg(array('state' => 0, 'msgwords' => date('Y年m月d日', $lastData->date) . '合作商费用日志未填'));
            }
            $balance_prior=$lastData->channel_balance;
		}else{
            $balance_prior = 0;

        }
		$system_cost = $info->system_cost;

//		$balance_all = PartnerCost::model()->findAll("partner_id = " . $info->partner_id . " and channel_id = " . $info->channel_id . " order by date desc ");
//		foreach ($balance_all as $k => $v) {
//			if ($v['date'] > $info->date) {
//				unset($balance_all[$k]);
//			}
//
//		}
//		$balance_all = array_merge($balance_all);
//		if ($balance_all[1]['date']) {
//			$balance_prior = $balance_all[1]['channel_balance'];
//		} else {
//			$balance_prior = 0;
//		}
		$infance_money = InfancePay::model()->findByAttributes(array('partner_id' => $info->partner_id, 'channel_id' => $info->channel_id, 'online_date' => $info->date))->pay_money;
		if (!$infance_money) {
			$infance_money = 0;
		}

		$info->channel_balance = $balance_prior + $infance_money - $info->partner_cost;
		$info->update_time = time();
		$dbresult = $info->update();
		$id = $info->primaryKey;
		//如果修改中间日期的合作商费用日志需要刷新之后日期的合作商费用日志
		PartnerCost::model()->refreshPartnerCost($info->channel_id , $info->date);
		//刷新修正成本
		$fixcostInfo = $this->refreshFixCost($info->channel_id, $info->date, $info->partner_cost - $system_cost);
		$msgarr = array('state' => 1, 'url' => $this->get('backurl')); //保存的话，跳转到之前的列表
		$logs = "编辑了合作商费用ID：$id" . $info->sno . "; " . $fixcostInfo;
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
	 * 修改合作商费用日志自动修改修正成本
	 * @param $pid 推广id
	 * @param $stat_date 上线日期
	 * author: yjh
	 */
	private function refreshFixCost($channel_id, $stat_date, $partner_cost) {

		$info = FixedCost::model()->findAll('channel_id=' . $channel_id . ' and stat_date=' . $stat_date);
		$num = count($this->toArr($info));
		if ($num == 0) {
			return '修正成本不存在';
		}

		$ids = '';
		foreach ($info as $value) {
			$ids .= $value->id . ",";
			$value->fixed_cost = round($partner_cost / $num, 2);
			$value->fixed_date = strtotime(date('Ymd'));
			$value->update_time = time();
			$value->update();
		}
		return '修正成本同步修改成功（' . $ids . '）';
	}

    /**
     * 导出
     * author: yjh
     */
	public function actionExport() {
        $file_name = '合作商费用日志-' . date('Ymd', time());
        $headlist = array('ID','日期', '合作商', '渠道名称', '渠道编码', '计费方式', '打款金额', '合作商提供费用', '友盟生成费用', '费用相比（相减）', '渠道余额', '类型', '推广人员');
        $data = $this->getData(1);
        $row = array();
        $row[0] = array('-','-', '-', '-',  iconv('utf-8', 'gbk', '合计'),
            $data['listdata']['pay_money'],
            $data['listdata']['partner_cost'],
            $data['listdata']['system_cost'],
            round($data['listdata']['partner_cost']-$data['listdata']['system_cost'],2),
            round($data['listdata']['pay_money']-$data['listdata']['partner_cost'],2),
            '-', '-','-');
        $data = $data['listdata']['list'];
        $count = count($data);
        for ($i = 0; $i < $count; $i++) {
            $k = $i + 1;
            $promotion_type = $data[$i]['promotion_type']==''?'-':vars::get_field_str('promotion_types', $data[$i]['promotion_type']);//状态

            $row[$k] = array(
                $data[$i]['id'],//ID
                date('Y-m-d', $data[$i]['date']),//日期
                $data[$i]['name'],//合作商
                $data[$i]['channel_name'],//渠道名称
                $data[$i]['channel_code'],//渠道编码
                vars::get_field_str('charging_type', $data[$i]['charging_type']),//计费方式
                $data[$i]['pay_money'],//打款金额
                $data[$i]['partner_cost'],//合作商提供费用
                $data[$i]['system_cost'],//友盟生成费用
                $data[$i]['partner_cost']-$data[$i]['system_cost'],//费用相比（相减）
                $data[$i]['channel_balance'],//渠道余额
                $promotion_type, //类型
                $data[$i]['csname_true']//操作人员
            );
            foreach ($row[$k] as $key => $value) {
                $row[$k][$key] = iconv('utf-8', 'gbk', $value);
            }
        }
        helper::downloadCsv($headlist, $row, $file_name);
	}

	private function getData($is_export=0){
        $system_cost = '';
        $params['where'] = '';

        //搜索时间
        if ($this->get('date_s') != '' && $this->get('date_e') != '') {
            $start = strtotime($this->get('date_s'));
            $end = strtotime($this->get('date_e')) + 3600 * 24 - 1;
            $params['where'] .= " and(date between $start and $end ) ";
        }
        //计费方式
        if ($this->get('chgId') != '')
            $params['where'] .= " and(d.charging_type = " . $this->get('chgId') . ") ";
        if ($this->get('search_type') == 'channel_name' && $this->get('search_txt'))
            $params['where'] .= " and(b.channel_name like '%" . $this->get('search_txt') . "%') ";
         else if ($this->get('search_type') == 'channel_code' && $this->get('search_txt'))
            $params['where'] .= " and(b.channel_code like '%" . $this->get('search_txt') . "%') ";
        if ($this->get('partner_name'))
            $params['where'] .= " and(c.name like '%" . $this->get('partner_name') . "%') ";
        //推广类型
        if ($this->get('promotion_type') != '')
            $params['where'] .= " and(a.promotion_type = " . $this->get('promotion_type') . ") ";
        //操作用户
        if ($this->get('user_id') != '')
            $params['where'] .= " and(a.sno = " . $this->get('user_id') . ") ";
        //查看人员权限
        $result = $this->data_authority();
        if ($result != 0)
            $params['where'] .= " and(a.sno in ($result)) ";

        $params['join'] = " LEFT JOIN channel as b ON a.channel_id = b.id
                            LEFT JOIN partner as c ON a.partner_id=c.id
                            LEFT JOIN finance_pay  as d ON a.infance_id=d.id
                            LEFT JOIN cservice  as e ON a.sno=e.csno";
        $params['select'] = " a.id,a.promotion_type,a.channel_id,a.partner_id,a.infance_id,a.partner_cost,a.system_cost,a.channel_balance,e.csname_true,a.date,c.name,b.channel_name,b.channel_code,d.charging_type,d.pay_money,b.business_type,a.update_time ";
        $params['order'] = "  order by a.id desc ";
        $params['pagesize'] = 1 == $is_export ? 20000 : Yii::app()->params['management']['pagesize'];
        $params['pagebar'] = 1;
        $page['listdata'] = Dtable::model(PartnerCost::model()->tableName())->listdata($params);

        $sql = "select sum(d.pay_money) as pay_moneys from partner_cost_log as a  LEFT JOIN partner as c ON a.partner_id=c.id   LEFT JOIN channel as b ON a.channel_id = b.id  left join finance_pay as d on a.channel_id=d.channel_id and a.partner_id=d.partner_id and a.date=d.online_date  where 1 " . $params['where'];
        $pay_money = Yii::app()->db->createCommand($sql)->queryAll();

        $sql = "select sum(a.partner_cost) as partner_cost,sum(a.system_cost) as system_cost,sum(a.channel_balance) as channel_balance  from partner_cost_log as a " . $params['join'] . " where 1 " . $params['where'];
        $totalMoney = Yii::app()->db->createCommand($sql)->queryAll();
        $page['listdata']['url'] = urlencode('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"]);

        //业务类型为手赚、硬广，计费方式为 cpt cpa cps 时  系统生成费用为0
        foreach ($page['listdata']['list'] as $key=>$value){
            if(in_array($value['business_type'],array(2,8))  && in_array($value['charging_type'],array(3,4,5))){
                $system_cost  += $page['listdata']['list'][$key]['system_cost'];
                $page['listdata']['list'][$key]['system_cost'] = 0;
            }
        }
        //打款金额总和
        $page['listdata']['pay_moneys'] = $pay_money[0]['pay_moneys'];
        //合作商费用总和
        $page['listdata']['partner_cost'] = $totalMoney[0]['partner_cost'];
        //友盟生成费用 = 系统生成费用
        //费用相比（相减）=系统生成费用-系统不需要生成费用的
        $page['listdata']['system_cost'] = $totalMoney[0]['system_cost']- $system_cost;
        //渠道余额总和
        $page['listdata']['channel_balance'] = $totalMoney[0]['channel_balance'];

        return $page;
	}

}