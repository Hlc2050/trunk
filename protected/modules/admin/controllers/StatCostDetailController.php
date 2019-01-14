<?php
/**
 *成本明细控制器
 */
class StatCostDetailController extends AdminController {

	public function actionIndex() {
		$_GET['id'] = isset($_GET['id']) ? $_GET['id'] : '';
		//搜索
		$params['where'] = '';
		if ($this->get('search_type') == 'partner_name' && $this->get('search_txt')) {
			$params['where'] .= " and(d.name  like '%" . $this->get('search_txt') . "%') ";
		} else if ($this->get('search_type') == 'channel_name' && $this->get('search_txt')) {
			$params['where'] .= " and(c.channel_name like '%" . ($this->get('search_txt')) . "%') ";
		} else if ($this->get('search_type') == 'channel_code' && $this->get('search_txt') != '') {
			$params['where'] .= " and(c.channel_code like '%" . ($this->get('search_txt')) . "%') ";
		} else if ($this->get('search_type') == 'weixin_id') {
			//正常显示
			$params['where'] .= " and(e.wechat_id like '%" . ($this->get('search_txt')) . "%') ";
		}

		if ($this->get('user_id')) {
			//正常显示
			$params['where'] .= " and(a.tg_uid =" . intval($this->get('user_id')) . ")";
		}
		//客服部搜索
		if ($this->get('csid')) {
			$params['where'] .= " and(a.customer_service_id =" . intval($this->get('csid')) . ")";
		}
        // 商品搜索
        if ($this->get('goods_id')) {
            $params['where'] .= " and(a.goods_id =" . intval($this->get('goods_id')) . ")";
        }
		if ($this->get('pay_start_time') && $this->get('pay_end_time')) {
			$start_time = strtotime($this->get('pay_start_time'));
			$end_time = strtotime($this->get('pay_end_time'));
			$params['where'] .= " and(a.pay_date>=$start_time  and a.pay_date<=$end_time) ";
		} elseif ($this->get('pay_start_time')) {
			$start_time = strtotime($this->get('pay_start_time'));
			$params['where'] .= " and(a.pay_date>=$start_time) ";
		} elseif ($this->get('pay_end_time')) {
			$end_time = strtotime($this->get('pay_end_time'));
			$params['where'] .= " and(a.pay_date<=$end_time) ";
		}

        //业务搜索
        if($this->get('bs_id'))  $params['where'] .=" and i.bid = ".$this->get('bs_id')." ";

		if ($this->get('online_start_time') && $this->get('online_end_time')) {
			//
			$start_time = strtotime($this->get('online_start_time'));
			$end_time = strtotime($this->get('online_end_time'));
			$params['where'] .= " and(a.stat_date>=$start_time  and a.stat_date<=$end_time) ";
		} elseif ($this->get('online_start_time')) {
			//
			$start_time = strtotime($this->get('online_start_time'));
			$params['where'] .= " and(a.stat_date>=$start_time) ";
		} elseif ($this->get('online_end_time')) {
            $end_time = strtotime($this->get('online_end_time'));
			$params['where'] .= " and(a.stat_date<=$end_time) ";
		}
		//查看人员权限
		$result = $this->data_authority();
		if ($result != 0) {
			$params['where'] .= " and(a.tg_uid in ($result)) ";
		}

		$params['order'] = "  order by a.id desc      ";
		$params['pagesize'] = Yii::app()->params['management']['pagesize'];
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
		$params['select'] = "*,a.id as cid,a.customer_service_id,a.create_time,a.business_type,a.charging_type,d.name as partner_name,m.linkage_name,n.cname";
		$params['pagebar'] = 1;
		$params['smart_order'] = 1;
		//$params['debug']=1;
		$page['listdata'] = Dtable::model(StatCostDetail::model()->tableName())->listdata($params);

		$sql = "select sum(a.money) as money from stat_cost_detail as a " . $params['join'] . " where 1 " . $params['where'];
		$totalMoney = Yii::app()->db->createCommand($sql)->queryAll();
		$page['listdata']['money'] = $totalMoney[0]['money'];
		$page['listdata']['url'] = urlencode('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"]);
		$this->render('index', array('page' => $page));

	}

	/**
	 * 编辑成本明细
	 * author: yjh
	 */
	public function actionEdit() {
		$page = array();
		$id = $this->get('id');
		$info = StatCostDetail::model()->findByPk($id);

		if (!$info) {
			$this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
		}
		if (!$_POST) {
			$info = $this->toArr($info);
			$page['info'] = array();
			$page['info']['id'] = $info['id'];
			$page['info']['stat_date'] = date('Y-m-d', $info['stat_date']);
			$page['info']['pay_date'] = date('Y-m-d', $info['pay_date']);
			$tgInfo = PromotionStaff::model()->find('user_id=' . $info['tg_uid']);
			$page['info']['uname'] = $tgInfo->name;
			$page['info']['tg_group'] = Linkage::model()->PromotionGroupById($tgInfo->promotion_group_id);
			$page['info']['partner'] = Partner::model()->getNameById($info['partner_id']);
			$page['info']['channel_code'] = Channel::model()->getChannelCode($info['channel_id']);
			$page['info']['channel_name'] = Channel::model()->getChannelName($info['channel_id']);
			$page['info']['cname'] = CustomerServiceManage::model()->getCSName($info['customer_service_id']);
			$page['info']['goods_name'] = Goods::model()->getGoodsName($info['goods_id']);
			$page['info']['business_type'] = $info['business_type'];
			$page['info']['charging_type'] = $info['charging_type'];
			$page['info']['money'] = $info['money'];
			$page['info']['third_money'] = $info['third_money'];
			$page['info']['chargingTypeList'] = Dtable::toArr(BusinessTypeRelation::model()->findAll('bid=:bid', array(':bid' => $page['info']['business_type'])));
			foreach ($page['info']['chargingTypeList'] as $k => $v) {
				$page['info']['chargingTypeList'][$k]['cname'] = vars::get_field_str('charging_type', $v['charging_type']);
			}
			$this->render('update', array('page' => $page));
			exit;
		}
		$info->business_type = $this->get('business_type');
		if ($info->business_type == '') {
			$this->msg(array('state' => 0, 'msgwords' => '未选择业务类型！'));
		}

        $info->charging_type = $this->get('charging_type');
        if ($info->charging_type == '') {
			$this->msg(array('state' => 0, 'msgwords' => '未选择计费方式！'));
		}

		$info->money = $this->get('money');
		if ($info->money && !is_numeric($info->money)) {
			$this->msg(array('state' => 0, 'msgwords' => '友盟金额要填数字！'));
		}

		$info->third_money = $this->get('third_money');
		if ($info->third_money && !is_numeric($info->third_money)) {
			$this->msg(array('state' => 0, 'msgwords' => '第三方金额要填数字！'));
		}

		$info->update_time = time();
		$dbresult = $info->save();
		$id = $info->primaryKey;
		$msgarr = array('state' => 1, 'url' => $this->get('backurl')); //保存的话，跳转到之前的列表
		$logs = "修改了成本明细信息ID：" . $id;
		if ($dbresult === false) {
			//错误返回
			$this->msg(array('state' => 0));
		} else {
			//新增和修改之后的动作
			$this->logs($logs);
			$this->msg($msgarr);
		}

	}

    /**
     * 导入
     */
	public function actionImport() {
		if (isset($_POST['submit'])) {
			$file = CUploadedFile::getInstanceByName('filename'); //获取上传的文件实例
			if (!$file) {
				$this->msg(array('state' => 0, 'msgwords' => '未选择文件！'));
			}

			if ($file) {
				echo $file->getType();
				if ($file->getType() == 'application/octet-stream') {
					$excelFile = $file->getTempName(); //获取文件名
					//这里就是导入PHPExcel包了，要用的时候就加这么两句，方便吧
					Yii::$enableIncludePath = false;
					Yii::import('application.extensions.PHPExcel.PHPExcel', 1);
					$phpexcel = new PHPExcel();
					$excelReader = PHPExcel_IOFactory::createReader('Excel5');
					$phpexcel = $excelReader->load($excelFile)->getActiveSheet(0); //载入文件并获取第一个sheet
					$total_line = $phpexcel->getHighestRow();
					$total_column = 'F';
					$list = array();
					$i = 0;
					for ($row = 7; $row <= $total_line; $row++) {
						$i++;
						$data = array();
						for ($column = 'A'; $column <= $total_column; $column++) {
							$data[] = trim($phpexcel->getCell($column . $row)->getValue());
							$list[$i] = $data;
						}

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
					$promotionData = array();
					//print_r($list);
					//die('c2');
					echo '<div class="msgbox0009">';
					foreach ($list as $r) {
						$domain = trim($r[0]);
						$pv = $r[1];
						$uv = $r[2];
						$ip = $r[3];
						if (preg_match('~\s~', $domain)) {
							continue;
						}
						$domainModel = DomainList::model()->findByAttributes(array('domain' => $domain));
						if (!$domainModel) {
							echo $domain . ' ,该域名在域名列表不存在<br>
							';
							continue;
						}
						if ($domainModel['status'] != 1) {
							//echo $domain.' ,该域名的状态是未使用<br>';
						}
						$promotion = Promotion::model()->findByAttributes(array('domain_id' => $domainModel->id));
						if (!$promotion) {
							echo $domain . ' ,该域名未在推广列表<br>
							';
							continue;
						}
						if ($promotion->status != 1) {
							//echo $domain.' ,该域名的推广状态不正常<br>';
						}

						$channel_id = $promotion->channel_id;
						$channel = Channel::model()->findByPk($channel_id);
						if (!$channel) {
							echo $domain . ' ,该域名的推广渠道无法找到<br>';
							continue;
						}
						$partner_id = $channel->partner_id;

						$statCnzz = StatCostDetail::model()->findByAttributes(array('stat_date' => $stat_date, 'domain' => $domain));
						if ($statCnzz) {
							echo $domain . ' ,该域名已导入过,无须再次导入<br>
                            ';
							continue;
						}
						$promotionData[] = Dtable::toArr($promotion);

						$statCnzz = new StatCostDetail();
						$statCnzz->domain = $domain;
						$statCnzz->stat_date = $stat_date;
						$statCnzz->ip = $ip;
						$statCnzz->uv = $uv;
						$statCnzz->pv = $pv;
						$statCnzz->create_time = time();

						$statChannel = new StatChannel();
						$statChannel->domain = $domain;
						$statChannel->partner_id = $partner_id;
						$statChannel->channel_id = $channel_id;
						$statChannel->create_time = time();
						$statChannel->save();
						$id = $statChannel->id;
						$statCnzz->stat_channel_id = $id;
						$result = $statCnzz->save();
						if ($result) {
							echo $domain . ' ,导入成功! <br>
                            ';
						} else {
							echo $domain . ' ,导入失败! <br>
                            ';
						}

					}

					foreach ($promotionData as $r) {
						$ret = StatCostDetail::model()->create($r['id'], $stat_date);
						if ($ret['state'] < 1) {
							echo 'ID: ' . $r['id'] . ' 的推广 创建成本明细失败[' . $ret['msgwords'] . ']<br>';
						} else {
							echo 'ID: ' . $r['id'] . ' 的推广 ' . $ret['msgwords'] . '<br>';
						}
						$ret = PartnerCost::model()->create($r['id'], $stat_date);
						if ($ret['state'] < 1) {
							echo 'ID: ' . $r['id'] . ' 的推广 创建合作商费用日志失败[' . $ret['msgwords'] . ']<br>';
						} else {
							echo 'ID: ' . $r['id'] . ' 的推广 ' . $ret['msgwords'] . '<br>';
						}
					}

					echo '</div><script>setTimeout("parent.show_frame_infos();",500)</script>';

					exit;

				}

			} else {
				$this->msg(array('state' => 0, 'msgwords' => '文件不存在'));
			}

		}
	}

    /**
     * 删除数据
     */
	public function actionDelete() {

		$idstr = $this->get('ids');
		$ids = explode(',', $idstr);
		foreach ($ids as $id) {
			$m = StatCostDetail::model()->findByPk($id);
			if (!$m) {
				continue;
			}

			$m->delete();
			$m2 = StatChannel::model()->findByPk($m->stat_channel_id);
			if (!$m2) {
				continue;
			}

			$m2->delete();

		}
		$this->logs('删除了统计ID（' . $idstr . '）');
		$this->msg(array('state' => 1));
	}

	/**
	 * 批量删除数据
	 * author: yjh
	 */
	public function actionDel() {
		$ids = $this->get('ids');
		$idArr = explode(',', $ids);
		$log = "删除了：";
		foreach ($idArr as $val) {
			$info = StatCostDetail::model()->findByPk(intval($val));
			if (!$info) {
				continue;
			}

			$info->delete();
			$log .= "上线日期为" . date('Y-m-d', $info->stat_date) . ",微信号为：" . WeChat::model()->findByPk($info->weixin_id)->wechat_id . "的成本明细,";
		}
		$this->logs($log);
		$this->msg(array('state' => 1, 'msgwords' => '批量删除成本明细成功！'));
	}

	/**
	 * 导出成本明细
	 * author: yjh
	 */
	public function actionExport() {
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="成本明细-' . date('Ymd', time()) . '.csv"');
		header('Cache-Control: max-age=0');

		//打开PHP文件句柄,php://output 表示直接输出到浏览器
		$fp = fopen('php://output', 'a');
		$headlist = array('上线日期', '推广人员', '推广小组', '归属客服部', '商品', '合作商', '渠道名称', '渠道编码', '微信号', '业务', '计费方式', '友盟金额', '付款日期');
		//输出Excel列名信息
		foreach ($headlist as $key => $value) {
			//CSV的Excel支持GBK编码，一定要转换，否则乱码
			$headlist[$key] = iconv('utf-8', 'gbk', $value);
		}

		//将数据通过fputcsv写到文件句柄
		fputcsv($fp, $headlist);

		//计数器
		$num = 0;

		//每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
		$limit = 10000;

		//逐行取出数据，不浪费内存
		$data = $this->GetStatCostDetaiData();
		$count = count($data);
		for ($i = 0; $i < $count; $i++) {

			$num++;

			//刷新一下输出buffer，防止由于数据过多造成问题
			if ($limit == $num) {
				ob_flush();
				flush();
				$num = 0;
			}

			$row = array(date('Y-m-d', $data[$i]['stat_date']), $data[$i]['csname_true'], $data[$i]['linkage_name'], $data[$i]['cname'], $data[$i]['goods_name'], $data[$i]['partner_name'], $data[$i]['channel_name'], $data[$i]['channel_code'], $data[$i]['wechat_id'], $data[$i]['business_type'], vars::get_field_str('charging_type', $data[$i]['charging_type']), $data[$i]['money'], date('Y-m-d', $data[$i]['pay_date']));
			foreach ($row as $key => $value) {
				$row[$key] = iconv('utf-8', 'gbk', $value);
			}

			fputcsv($fp, $row);
		}
	}

	/**
	 * 获取成本明细数据
	 * author: yjh
	 */
	private function GetStatCostDetaiData() {
		ini_set('max_execution_time', '0');
		$params['where'] = 'where 1';
		if ($this->get('search_type') == 'partner_name' && $this->get('search_txt')) {
			$params['where'] .= " and(d.name  like '%" . $this->get('search_txt') . "%') ";
		} else if ($this->get('search_type') == 'channel_name' && $this->get('search_txt')) {
			$params['where'] .= " and(c.channel_name like '%" . ($this->get('search_txt')) . "%') ";
		} else if ($this->get('search_type') == 'channel_code' && $this->get('search_txt') != '') {
			$params['where'] .= " and(c.channel_code like '%" . ($this->get('search_txt')) . "%') ";
		} else if ($this->get('search_type') == 'weixin_id' && $this->get('search_txt') != '') {
			//
			$params['where'] .= " and(e.wechat_id like '%" . ($this->get('search_txt')) . "%') ";
		}

		if ($this->get('user_id')) {
			//正常显示
			$params['where'] .= " and(a.tg_uid =" . intval($this->get('user_id')) . ")";
		}
		//客服部搜索
		if ($this->get('csid')) {
			$params['where'] .= " and(a.customer_service_id =" . intval($this->get('csid')) . ")";
		}
        // 商品搜索
        if ($this->get('goods_id')) {
            $params['where'] .= " and(a.goods_id =" . intval($this->get('goods_id')) . ")";
        }
		if ($this->get('pay_start_time') && $this->get('pay_end_time')) {
			//
			$start_time = strtotime($this->get('pay_start_time'));
			$end_time = strtotime($this->get('pay_end_time'));
			$params['where'] .= " and(a.pay_date>=$start_time  and a.pay_date<=$end_time) ";
		}
		if ($this->get('online_start_time') && $this->get('online_end_time')) {
			//
			$start_time = strtotime($this->get('online_start_time'));
			$end_time = strtotime($this->get('online_end_time'));
			$params['where'] .= " and(a.stat_date>=$start_time  and a.stat_date<=$end_time) ";
		}
		if ($this->get('create_start_time') && $this->get('create_end_time')) {
			//
			$start_time = strtotime($this->get('create_start_time'));
			$end_time = strtotime($this->get('create_end_time'));
			$params['where'] .= " and(a.create_time>=$start_time  and a.create_time<=$end_time) ";
		}

		//查看人员权限
		$result = $this->data_authority();
		if ($result != 0) {
			$params['where'] .= " and(a.tg_uid in ($result)) ";
		}

		$params['order'] = "  order by a.id desc      ";
		//$params['pagesize'] = Yii::app()->params['management']['pagesize'];
		$params['join'] = "
		left join channel as c on c.id=a.channel_id
		left join partner as d on d.id=a.partner_id
		left join wechat as e on e.id=a.weixin_id
		left join cservice as f on f.csno=a.tg_uid
		left join promotion_staff_manage as g on g.user_id=a.tg_uid
		left join goods as h on h.id=a.goods_id
		left join customer_service_manage as i on i.id=a.customer_service_id
		left join business_types as j on j.bid=a.business_type
		left join linkage as k on k.linkage_id=g.promotion_group_id

		";
		$params['select'] = "select a.stat_date,a.charging_type,a.pay_date,a.money,a.third_money,k.linkage_name,j.bname as business_type,f.csname_true,i.cname,h.goods_name,d.name as partner_name,c.channel_name,c.channel_code,e.wechat_id";

		$sql = $params['select'] . " from stat_cost_detail as a " . $params['join'] . $params['where'] . $params['order'];
		$data = Yii::app()->db->createCommand($sql)->queryAll();

		return $data;
	}

}

?>