<?php
class FrameController extends AdminController{
	public function actionIndex(){
		
		$this->render('index');
	}
	public function actionTop(){
		$this->render('top');
	}
	public function actionLeft(){
		$this->render('left');
	}
	public function actionWelcome(){
		$start_time = strtotime(date('Y-m-d'))-86400;
		$end_time = strtotime(date('Y-m-d'))-1;
		$params['where'] = "";
		//搜索合作商
		if ($this->get('partner') != '') {
			$params['where'] .= " and(p.name ) like '%" . $this->get('partner') . "%'";
		}
		//搜索渠道编码
		if ($this->get('chlId') != '') {
			$params['where'] .= " and(c.channel_code ) like '%" . $this->get('chlId') . "%'";
		}
		//搜索渠道名称
		if ($this->get('chlName') != '') {
			$params['where'] .= " and(c.channel_name ) like '%" . $this->get('chlName') . "%'";

		}
		//推广人员
		if($this->get('user_id') != ''){
			$params['where'] .= " and(f.sno = ".$this->get('user_id')." ) ";

		}
		//类型搜索
		if($this->get('promotion_type')!='' && $this->get('promotion_type')!==0){
			$params['where'] .= " and(a.promotion_type = ".$this->get('promotion_type')." ) ";

		}


		$params['where'] .= "  and ( a.status=0 )   ";
		$params['order'] = "  order by a.id desc      ";
		$params['pagesize'] = Yii::app()->params['management']['pagesize'];
		//查看人员权限
		$result = $this->data_authority();
		if ($result != 0) {
			$params['where'] .= " and(f.sno in ($result)) ";
		}
		$params['join']="
		                    left join finance_pay as f on f.id=a.finance_pay_id
							left join channel as c on c.id=f.channel_id
							left join partner as p on p.id=f.partner_id
		                    LEFT JOIN stat_cnzz_flow as s ON s.promotion_id=a.id and stat_date=$start_time
							LEFT JOIN cservice as b ON b.csno=f.sno
							";
		$params['select']="  a.id,a.promotion_type,s.ip,a.domain_id,f.channel_id as cid,f.partner_id,p.name as partner_name,c.channel_name,c.channel_code,b.csname_true";
		$params['pagebar']=1;
		$params['smart_order']=1;
		$page['listdata']=Dtable::model(Promotion::model()->tableName())->listdata($params);
		
		if ($this->get('ip') !='' ) {
			$sql="select".$params['select']." from promotion_manage as a ".$params['join']." where 1 ".$params['where'].$params['order'];
			$temp_data=Yii::app()->db->createCommand($sql)->queryAll();
			$dataArr = array();
			foreach ($temp_data as $k => $v) {
				if(!is_numeric($this->get('ip'))) break;
				if(intval($v['ip'])<=$this->get('ip'))
					$dataArr[]=$v;
			}
			$p = $this->get('p');
			$pagesize = Yii::app()->params['management']['pagesize'];
			$rows = count($dataArr); //计算数组所得到记录总数
			($p == "") ? $page = 1 : $p = $this->get('p'); //初始化页码
			$offset = $p - 1; //初始化分页指针
			$start = $offset * $pagesize; //初始化下限
			$data = array();
			$data['list'] = array_slice($dataArr, $start, $pagesize);
			$pagearr = helper::pagehtml(array('total' => $rows, "pagesize" => $pagesize, "show" => 1));
			$data['pagearr'] = $pagearr;
			$page['listdata']=$data;
		}

		$this->render('welcome',array('page'=>$page));
	}



	public function actionDown(){

		$promotion_id=$_GET['promotion_id'];
		$promotion=Promotion::model()->findByPk($promotion_id);
        if ($promotion->status == 1) {
            $this->msg(array('state' =>0, 'msgwords' => '该推广已下线！'));
        }
		$promotion->status =1;
		$promotion->outline_date=strtotime(date('Ymd'));
		$promotion->save();
		$infancePay_id = $promotion->finance_pay_id;
		WeChatGroup::model()->status(InfancePay::model()->getWechatId($infancePay_id),0);
		//修改推广域名状态
        $pro_domains = PromotionDomain::model()->getPromotionDomains($promotion_id);
        $update_data = array(
            'status'=>0,
            'promotion_type'=>0,
            'update_time'=>time(),
        );
        if ($pro_domains) {
            DomainList::model()->updateDomains($pro_domains,$update_data,0);
            $domains = Dtable::toArr(DomainList::model()->findAll(' id in ('.implode(',',$pro_domains).')'));
            $change_domain = array();
            foreach ($domains as $d){
                $change_domain[] = array(
                    'from_domain' => $d['domain'],
                    'domain' => '',
                );
            }
            DomainPromotionChange::model()->addChangeLogs($promotion_id,$change_domain,0);
        }

		//判断白域名和跳转域名是否还有在用
		$white_domain_id=$promotion->white_domain_id;
		if($white_domain_id!=0){
			$wPromotions= Promotion::model()->find("white_domain_id=".$white_domain_id." and status!=1 and is_white_domain=0");
			if(!$wPromotions)
				DomainList::model()->ByIdStatus($white_domain_id, 0);
		}
		$goto_domain_id=$promotion->goto_domain_id;
		if($goto_domain_id!=0){
			$gPromotions= Promotion::model()->find("goto_domain_id=".$goto_domain_id." and status!=1");
			if(!$gPromotions)
				DomainList::model()->ByIdStatus($goto_domain_id, 0);
		}
		//操作日志
		$logs = "下线了推广：" . $promotion_id;
		$this->logs($logs);
		$this->msg(array('state' => 1, 'msgwords' => '下线成功！'));

	}
	
	
}