<?php
/**
 * 微信进粉预估控制器
 * User: lxj
 * Date: 2017/12/2
 * Time: 10:46
 */
class FansInputPredictController extends AdminController
{

    public function actionIndex()
    {
        $page = $this->getPredictdata();
        $this->render('index',array('page'=>$page));
    }

    /**
     * 导出微信进粉预估表
     */
    public function actionExport()
    {
        $headlist = array('日期','合作商','渠道名称','渠道编码','客服部','推广人员','微信号','合计');
        $allData = $this->getPredictdata(1);
        $export_row = array();
        // 统计行
        $total_row = array('-','-','-','-','-','-','-',$allData['list']['total_num']);
        for ($i = 0; $i <= 23; $i++) {
            $key = $i;
            if ($i<10) {
                $key = 0+$i;
            }
            $total_row[] = isset($allData['list']['hours_total_num'][$key]) ? $allData['list']['hours_total_num'][$key]:0;
            $headlist[] = $i.'点';
        }
        $export_row[0] = $total_row;
        $i = 1;
        foreach ($allData['list']['listdata'] as $key=>$value) {
            $row = array(date('Y-m-d',$value['date']),$value['p_name'],$value['channel_name'],$value['channel_code']
            ,$value['cname'],$value['csname_true'],$value['wechat_id'],$value['date_num']);
            for ($j = 0; $j <= 23; $j++) {
                $date = date('Ymd',$value['date']);
                $key = $j<10 ? ($date.'0'.$j):($date.$j);
                $row[] = isset($value['hours_num'][$key]) ? $value['hours_num'][$key]:0;
            }
            foreach ($row as $k=>$v) {
                $row[$k] = iconv('utf-8','gbk',$v);
            }
            $export_row[$i] = $row;
            $i++;
        }
        helper::downloadCsv($headlist,$export_row,'微信进粉预估-' . date('Ymd', time()));
    }

    /**
     * 获取预估数据列表
     * @param $action_type int 操作类型：0显示列表,1导出
     */
    private function getPredictdata($action_type=0)
    {
        $page = array();
        $params['where'] = '';
        $params['join'] = '
                left join partner as p on a.partner_id=p.id
                left join channel as c on a.channel_id=c.id
		         left join cservice as g on g.csno=a.tg_uid
                left join wechat as w on a.weixin_id=w.id
                left join customer_service_manage as cs on w.customer_service_id=cs.id
                ';
        if ($this->get('start_date') || $this->get('end_date')) {
            if ($this->get('start_date') && $this->get('end_date')) {
                if ($this->get('start_date') <= $this->get('end_date') ) {
                    $params['where'] .= ' and a.date between '.strtotime($this->get('start_date')).' and '.strtotime($this->get('end_date'));
                }
                $page['date']['start_date']=$this->get('start_date');
                $page['date']['end_date']=$this->get('end_date');
            } elseif($this->get('start_date')) {
                $params['where'] .= ' and a.date = '.strtotime($this->get('start_date'));
                $page['date']['start_date']=$this->get('start_date');
                $page['date']['end_date']=$this->get('start_date');
            } elseif ($this->get('end_date')) {
                $params['where'] .= ' and a.date = '.strtotime($this->get('end_date'));
                $page['date']['start_date']=$this->get('end_date');
                $page['date']['end_date']=$this->get('end_date');
            }
        }else{
            $params['where'] .= ' and a.date = '.strtotime(date('Ymd',time()));
            $page['date']['start_date']=date('Y-m-d');
            $page['date']['end_date']=date('Y-m-d');
        }
        if ($this->get('partner_name')!= '') $params['where'] .= " and p.name like '%".$this->get('partner_name')."%'" ;
        if ($this->get('channel_name')!= '') $params['where'] .= " and c.channel_name like '%".$this->get('channel_name')."%'" ;
        if ($this->get('channel_code')!= '') $params['where'] .= " and c.channel_code like '%".$this->get('channel_code')."%'" ;
        if ($this->get('user_id') != '') $params['where'] .= ' and g.csno = '.$this->get('user_id');
        if ($this->get('wechat_id') != '') $params['where'] .= " and w.wechat_id like '%".$this->get('wechat_id')."%'" ;
        if ($this->get('csid') != '') $params['where'] .= " and cs.id = ".$this->get('csid') ;
        $params['select'] = " a.weixin_id,a.promotion_id,a.date,SUM(a.num) as date_num,c.channel_name,c.channel_code,g.csname_true,w.wechat_id,cs.cname,p.name as p_name";
        $params['order'] = "  order by date desc,weixin_id asc ";
        $params['group'] = "  group by a.weixin_id,a.date,a.promotion_id";
        if ($action_type == 0) {
            $params['pagesize'] =  15;
            $params['pagebar'] = 1;
            $params['smart_order'] = 1;
            // 获取日期等基本数据
            $infos = Dtable::model(FansInputPredict::model()->tableName())->listData($params);
            $page['list']['pagearr'] = $infos['pagearr'];
            $page['list']['total'] = $infos['total'];
        } elseif($action_type == 1) {
            $sql = 'select '.$params['select'].' from fans_input_predict as a '.$params['join'].' where 1  '. $params['where'] .$params['group']  ;
            $infos['list'] = Yii::app()->db->createCommand($sql)->queryAll();
        }
        $new_info = array();
        if (!empty($infos["list"])) {
            $dates = array_column($infos['list'],'date');
            $wechats = array_column($infos['list'],'weixin_id');
            $promotions = array_column($infos['list'],'promotion_id');
            $condition = ' date in ('.implode(',',$dates).') ';
            $condition .= ' and promotion_id in ('.implode(',',$promotions).') ';
            $condition .= ' and weixin_id in ('.implode(',',$wechats).') ';
            foreach ($infos["list"] as $value) {
                $key = $value['promotion_id'].'_'.$value['weixin_id'].'_'.$value['date'];
                $new_info[$key] = $value;
            }
            $hour_dates = FansInputPredict::model()->findAll(
                array(
                    'select'=>'date,promotion_id,weixin_id,num,d_hour',
                    'condition'=>$condition
                )
            );
            // 获取每小时预估数据
            foreach ($hour_dates as $k=>$value)
            {
                $key = $value['promotion_id'].'_'.$value['weixin_id'].'_'.$value['date'];
                $hour = intval($value['d_hour']);
                if (array_key_exists($key,$new_info)) {
                    $new_info[$key]['hours_num'][$hour] = $value['num'];
                }
            }
        }
        $sql = 'select num,d_hour from fans_input_predict as a where 1 '.$params['where'];
        $hour_num = Yii::app()->db->createCommand($sql)->queryAll();
        $hours_total_num = array();
        foreach ($hour_num as $hour) {
            $key = substr($hour['d_hour'],-2);

            if (!array_key_exists($key,$hours_total_num)) $hours_total_num[$key] = 0;
            $hours_total_num[$key] += $hour['num'];
        }
        $page['list']['hours_total_num'] = $hours_total_num;
        $page['list']['total_num'] = array_sum($hours_total_num);
        $page['list']['listdata'] = $new_info;
        return $page;
    }

}