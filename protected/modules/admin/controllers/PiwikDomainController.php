<?php
/**
 * Created by PhpStorm.
 * User: fang
 * Date: 2017/1/20
 * Time: 11:29
 */
class PiwikDomainController extends AdminController{
    public function actionIndex(){
        $params['where'] = '';
        //strtotime($_GET['pay_date']);
        if ($this->get('start_date') != '' && $this->get('end_date') != '') {
            $start = strtotime($this->get('start_date'));
            $end = strtotime($this->get('end_date'))+86400-1;
            $params['where'] .= " and(a.stat_date between $start and $end ) ";
        }else{
            $start = strtotime(date('Ymd', strtotime('-1 day')));
            $end = strtotime(date('Ymd'))+86400-1;
            $params['where'] .= " and(a.stat_date between $start and $end ) ";
        }
        if ($this->get('domain') != '' ) {
            $params['where'] .= " and(a.domain like '%" . $this->get('domain') . "%') ";
        }
        if ($this->get('user_id') != '') {
            $params['where'] .= " and(c.sno=" . intval($this->get('user_id')) . ") ";
        }
        if ($this->get('chgId') != '') {
            $params['where'] .= " and(c.charging_type=" . intval($this->get('chgId')) . ") ";
        }
        if ($this->get('search_type') == 'partner_name' && $this->get('search_txt')) {
            $params['where'] .= " and(e.name like '%" . $this->get('search_txt') . "%') ";
        } else if ($this->get('search_type') == 'channel_name' && $this->get('search_txt')) {
            $params['where'] .= " and(d.channel_name like '%" . $this->get('search_txt') . "%') ";
        } else if ($this->get('search_type') == 'channel_code' && $this->get('search_txt')) {
            $params['where'] .= " and(d.channel_code like '%" . $this->get('search_txt') . "%') ";
        }
        //查看人员权限
        $result = $this->data_authority();
        if ($result != 0) {
            $params['where'] .= " and(c.sno in ($result)) ";
        }
        //$params['group'] = " group by a.domain_id ";
        $params['join'] = "  left join promotion_manage as b on a.promotion_id=b.id
                             left join finance_pay as c on b.finance_pay_id=c.id
                             left join channel as d on b.channel_id=d.id
                             left join partner as e on c.partner_id=e.id     ";
        $params['select']="  a.*,c.unit_price,c.charging_type,c.sno,c.weixin_group_id,d.channel_name,d.channel_code,e.name,b.outline_date,b.status,b.create_time ";
        $params['order'] = "  order by a.id desc    ";
        $params['pagesize'] = 10000;
        //$params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(PiwikHour::model()->tableName())->listdata($params);
        $pages=array();
        foreach ($page['listdata']['list'] as $r){
            $pages[$r['domain_id']][]=$r;
        }
        foreach ($pages as $r){
            foreach ($r as $v){
                $date = date('Ymd', $v['stat_date']);
                if (isset($_data[$date])) {
                    $_data[$date]['pv'] += $v['pv'];
                    $_data[$date]['uv'] += $v['uv'];
                    $_data[$date]['ip'] += $v['ip'];
                    $_data[$date]['wechat_touch'] += $v['wechat_touch'];
                    $_data[$date]['qr_code_click'] += $v['qr_code_click'];
                } else {
                    $_data[$date] = $v;
                }
            }
            if (isset($lists)) {
                $_list = array_merge($_data);
                $lists = array_merge($_list, $lists);

            } else {
                $lists = array_merge($_data);

            }
            unset($_data);

        }
        uasort($lists, $this->array_sort('stat_date'));
        $this->render('index', array('list' => $lists));
    }
    /*
     * 导出
     */
    public function actionExport()
    {
        $objectPHPExcel = new PHPExcel();
        $objectPHPExcel->setActiveSheetIndex(0);
        $page_size = 2;
        //读出数据
        $current_page = 0;
        $n = 0;
        $data = $this->GetPiwikDomainData();
        foreach ($data as $key => $val) {
            if ($n % $page_size === 0) {
                $current_page = $current_page + 1;
                //表格头的输出
                $objectPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('A1','上线日期');
                $objectPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('B1','推广人员');
                $objectPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('C1','域名');
                $objectPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('D1','合作商');
                $objectPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('E1','渠道名称');
                $objectPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('F1','渠道编码');
                $objectPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('G1','计费单价');
                $objectPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('H1','计费方式');
                $objectPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('I1','pv');
                $objectPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('J1','uv');
                $objectPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('K1','独立ip');
                $objectPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('L1','微信号长按次数');
                $objectPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('M1','二维码长按次数');
                $objectPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('N1','第三方投入金额');
                $objectPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('O1','微信号小组');
                //设置居中
                $objectPHPExcel->getActiveSheet()->getStyle('A1:O1')
                    ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                //设置边框
                $objectPHPExcel->getActiveSheet()->getStyle('A1:O1')
                    ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objectPHPExcel->getActiveSheet()->getStyle('A1:O1')
                    ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objectPHPExcel->getActiveSheet()->getStyle('A1:O1')
                    ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objectPHPExcel->getActiveSheet()->getStyle('A1:O1')
                    ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objectPHPExcel->getActiveSheet()->getStyle('A1:O1')
                    ->getBorders()->getVertical()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                //设置颜色
                $objectPHPExcel->getActiveSheet()->getStyle('A1:O1')->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF66CCCC');
            }
            //设置居中
            $objectPHPExcel->getActiveSheet()->getStyle('A:O')
                ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //明细的输出
            $objectPHPExcel->getActiveSheet()->setCellValue('A' . ($n + 2), $val['stat_date']);
            $objectPHPExcel->getActiveSheet()->setCellValue('B' . ($n + 2), $val['csname_true']);
            $objectPHPExcel->getActiveSheet()->setCellValue('C' . ($n + 2), $val['domain']);
            $objectPHPExcel->getActiveSheet()->setCellValue('D' . ($n + 2), $val['name']);
            $objectPHPExcel->getActiveSheet()->setCellValue('E' . ($n + 2), $val['channel_name']);
            $objectPHPExcel->getActiveSheet()->setCellValueExplicit('F' . ($n + 2), $val['channel_code'],PHPExcel_Cell_DataType::TYPE_STRING);
            $objectPHPExcel->getActiveSheet()->setCellValueExplicit('G' . ($n + 2), $val['unit_price'],PHPExcel_Cell_DataType::TYPE_STRING);
            $objectPHPExcel->getActiveSheet()->setCellValue('H' . ($n + 2), $val['charging_type']);
            $objectPHPExcel->getActiveSheet()->setCellValue('I' . ($n + 2), $val['pv']);
            $objectPHPExcel->getActiveSheet()->setCellValue('J' . ($n + 2), $val['uv']);
            $objectPHPExcel->getActiveSheet()->setCellValue('K' . ($n + 2),  $val['ip']);
            $objectPHPExcel->getActiveSheet()->setCellValue('L' . ($n + 2), $val['wechat_touch']);
            $objectPHPExcel->getActiveSheet()->setCellValue('M' . ($n + 2), $val['qr_code_click']);
            $objectPHPExcel->getActiveSheet()->setCellValue('N' . ($n + 2), $val['cost']);
            $objectPHPExcel->getActiveSheet()->setCellValue('O' . ($n + 2), $val['wechat_group_name']);
            //设置边框
            $currentRowNum = $n + 2;
            $objectPHPExcel->getActiveSheet()->getStyle('A' . ($n + 2) . ':O' . $currentRowNum)
                ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objectPHPExcel->getActiveSheet()->getStyle('A' . ($n + 2) . ':O' . $currentRowNum)
                ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objectPHPExcel->getActiveSheet()->getStyle('A' . ($n + 2) . ':O' . $currentRowNum)
                ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objectPHPExcel->getActiveSheet()->getStyle('A' . ($n + 2) . ':O' . $currentRowNum)
                ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objectPHPExcel->getActiveSheet()->getStyle('A' . ($n + 2) . ':O' . $currentRowNum)
                ->getBorders()->getVertical()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $n = $n + 1;
        }
        $objectPHPExcel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
        $objectPHPExcel->getActiveSheet()->getPageSetup()->setVerticalCentered(false);

        ob_end_clean();
        ob_start();

        header('Content-Type : application/vnd.ms-excel');
        header('Content-Disposition:attachment;filename="' . '第三方统计-域名统计-' . date("Ymj") . '.xls"');
        $objWriter = PHPExcel_IOFactory::createWriter($objectPHPExcel, 'Excel5');
        $objWriter->save('php://output');

    }
    /**
     * 获取第三方域名统计数据
     * author:
     */
    private function GetPiwikDomainData()
    {
        $params['where'] = '';
        //strtotime($_GET['pay_date']);
        if ($this->get('start_date') != '' && $this->get('end_date') != '') {
            $start = strtotime($this->get('start_date'))+1;
            $end = strtotime($this->get('end_date'))+86400;
            $params['where'] .= " and(a.stat_date between $start and $end ) ";
        }else{
            $start = strtotime(date('Ymd', strtotime('-1 day')));
            $end = strtotime(date('Ymd'))+86400-1;
            $params['where'] .= " and(a.stat_date between $start and $end ) ";
        }
        if ($this->get('user_id') != '') {
            $params['where'] .= " and(c.sno=" . intval($this->get('user_id')) . ") ";
        }
        if ($this->get('chgId') != '') {
            $params['where'] .= " and(c.charging_type=" . intval($this->get('chgId')) . ") ";
        }
        if ($this->get('search_type') == 'partner_name' && $this->get('search_txt')) {
            $params['where'] .= " and(e.name like '%" . $this->get('search_txt') . "%') ";
        } else if ($this->get('search_type') == 'channel_name' && $this->get('search_txt')) {
            $params['where'] .= " and(d.channel_name like '%" . $this->get('search_txt') . "%') ";
        } else if ($this->get('search_type') == 'channel_code' && $this->get('search_txt')) {
            $params['where'] .= " and(d.channel_code like '%" . $this->get('search_txt') . "%') ";
        }else if ($this->get('search_type') == 'domain' && $this->get('search_txt')) {
            $params['where'] .= " and(a.domain like '%" . $this->get('search_txt') . "%') ";
        }
        $params['join'] = "  left join promotion_manage as b on a.promotion_id=b.id
                             left join finance_pay as c on b.finance_pay_id=c.id
                             left join channel as d on b.channel_id=d.id
                             left join partner as e on c.partner_id=e.id     ";
        $params['select']="  a.*,c.unit_price,c.charging_type,c.sno,c.weixin_group_id,d.channel_name,d.channel_code,e.name,b.outline_date,b.status,b.create_time ";
        $params['order'] = "  order by a.id desc    ";
        $sql="select count(id) as count from piwik_hour_data";
        $count = Yii::app()->db->createCommand($sql)->queryAll();
        $params['pagesize'] = $count[0]['count'];
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(PiwikHour::model()->tableName())->listdata($params);
        $pages=array();
        foreach ($page['listdata']['list'] as $r){
            $pages[$r['domain_id']][]=$r;
        }
        foreach ($pages as $r){
            foreach ($r as $v){
                $date = date('Ymd', $v['stat_date']);
                if (isset($_data[$date])) {
                    $_data[$date]['pv'] += $v['pv'];
                    $_data[$date]['uv'] += $v['uv'];
                    $_data[$date]['ip'] += $v['ip'];
                    $_data[$date]['wechat_touch'] += $v['wechat_touch'];
                    $_data[$date]['qr_code_click'] += $v['qr_code_click'];
                } else {
                    $_data[$date] = $v;
                }
            }
            if (isset($lists)) {
                $_list = array_merge($_data);
                $lists = array_merge($_list, $lists);

            } else {
                $lists = array_merge($_data);

            }
            unset($_data);

        }
        uasort($lists, $this->array_sort('stat_date'));
        $data = $lists;
        foreach ($data as $key => $val) {
            $data[$key]['stat_date'] = date('Y-m-d', $val['stat_date']);
            $data[$key]['csname_true'] = AdminUser::model()->findByPk($val['sno'])->csname_true;
            $data[$key]['domain'] = $val['domain'];
            $data[$key]['name'] = $val['name'];
            $data[$key]['channel_name'] = $val['channel_name'];
            $data[$key]['channel_code'] = $val['channel_code'];
            $data[$key]['unit_price'] = $val['unit_price'];
            $data[$key]['charging_type'] = vars::get_field_str('charging_type',$val['charging_type']);
            $data[$key]['pv'] = $val['pv'];
            $data[$key]['uv'] = $val['uv'];
            $data[$key]['ip'] = $val['ip'];
            $data[$key]['wechat_touch'] = $val['wechat_touch'];
            $data[$key]['qr_code_click'] = $val['qr_code_click'];
            $data[$key]['cost'] = PiwikHour::model()->piwikHourCost($val['charging_type'],$val['ip'],$val['pv'],$val['uv'],$val['unit_price']);
            $data[$key]['wechat_group_name'] = WeChatGroup::model()->findByPk($val['weixin_group_id'])->wechat_group_name;
        }
        return $data;
    }
    /*
     *排序
     */
    function array_sort($key)
    {
        return function ($a, $b) use ($key) {
            if ($a[$key] == $b[$key]) {
                return 0;
            }
            return ($a[$key] < $b[$key]) ? +1 : -1;
        };

    }
}