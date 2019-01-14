<?php
/**
 * Created by PhpStorm.
 * User: fang
 * Date: 2017/1/12
 * Time: 10:02
 */
class PiwikHourController extends AdminController{
    public function actionIndex(){
        $_GET['domain'] = isset($_GET['domain']) ? $_GET['domain'] : '';
        $_GET['date'] = isset($_GET['date']) ? $_GET['date'] : '';
        $params['where'] = '';
        //搜索时间日期
        if ($this->get('start_date') != '' && $this->get('end_date') != '') {
            if ($this->get('start_time') != '' && $this->get('end_time') != '') {
                $serch_day = (strtotime($this->get('end_date'))-strtotime($this->get('start_date')))/86400;
                $stat_date = '';
                for ( $i = 0;$i <= $serch_day;$i++ ){
                    $start_time = strtotime( $this->get( 'start_date' ).' '.$this->get( 'start_time' ) )+$i*86400+1;
                    $end_time = strtotime( $this->get( 'start_date' ).' '.$this->get( 'end_time' ) )+3600-1+$i*86400;

                    if ($i == $serch_day){
                        $stat_date .= " ( a.stat_date between $start_time and $end_time ) ";
                    }else{
                        $stat_date .= " ( a.stat_date between $start_time and $end_time ) or ";
                    }
                }
                $params['where'] .= " and ( $stat_date ) ";
            }
            $start = strtotime($this->get('start_date'));
            $end = strtotime($this->get('end_date'))+86400-1;
            $params['where'] .= " and(a.stat_date between $start and $end ) ";
        }
        //域名详情
        if ($_GET['domain'] != '' &&  $_GET['date'] != '' ) {
            $time_s = strtotime($_GET['date'])+1;
            $time_e = $time_s+86400;
            $params['where'] .= " and(a.domain='" . $_GET['domain'] . "') ";
            $params['where'] .= " and(a.stat_date between $time_s and $time_e ) ";
        }
        //搜索推管人员
        if ($this->get('user_id') != '') {
            $params['where'] .= " and(c.sno=" . intval($this->get('user_id')) . ") ";
        }
        //搜索计费方式
        if ($this->get('chgId') != '') {
            $params['where'] .= " and(c.charging_type=" . intval($this->get('chgId')) . ") ";
        }
        //搜索合作商、渠道编码、渠道名称
        if ($this->get('search_type') == 'partner_name' && $this->get('search_txt')) {

            $params['where'] .= " and(e.name like '%" . $this->get('search_txt') . "%') ";

        } else if ($this->get('search_type') == 'channel_name' && $this->get('search_txt')) {

            $params['where'] .= " and(d.channel_name like '%" . $this->get('search_txt') . "%') ";

        } else if ($this->get('search_type') == 'channel_code' && $this->get('search_txt')) {

            $params['where'] .= " and(d.channel_code like '%" . $this->get('search_txt') . "%') ";

        }else if ($this->get('search_type') == 'domain' && $this->get('search_txt')) {

            $params['where'] .= " and(a.domain like '%" . $this->get('search_txt') . "%') ";

        }
        //查看人员权限
        $result = $this->data_authority();
        if ($result != 0) {
            $params['where'] .= " and(c.sno in ($result)) ";
        }
        //$params['where'] .= " and( a.stat_date =< b.outline_date ) ";
        $params['join'] = "  left join promotion_manage as b on a.promotion_id=b.id
                             left join finance_pay as c on b.finance_pay_id=c.id
                             left join channel as d on a.channel_id=d.id
                             left join partner as e on a.partner_id=e.id     ";

        $params['select'] = "  a.*,c.unit_price,c.sno,c.weixin_group_id,d.channel_name,d.channel_code,e.name,b.outline_date,b.status,b.create_time  ";

        $params['order'] = "  order by a.id desc    ";

        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['pagesize'] = 24;
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(PiwikHour::model()->tableName())->listdata($params);
        $this->render('index', array('page' => $page));
    }
    /*
     * 导出
     */
    public function actionExport()
    {
        $objectPHPExcel = new PHPExcel();
        $objectPHPExcel->setActiveSheetIndex(0);
        $page_size = 2;
        //获取数据
        $current_page = 0;
        $n = 0;
        $data = $this->GetPiwikHourData();
        foreach ($data as $key => $val) {
            if ($n % $page_size === 0) {
                $current_page = $current_page + 1;
                //表格头的输出
                $objectPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
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
            $objectPHPExcel->getActiveSheet()->setCellValue('A' . ($n + 2), $val['stat_date'].'~'.$val['end_date']);
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
        header('Content-Disposition:attachment;filename="' . '第三方统计-小时统计-' . date("Ymj") . '.xls"');
        $objWriter = PHPExcel_IOFactory::createWriter($objectPHPExcel, 'Excel5');
        $objWriter->save('php://output');

    }
    /**
     * 获取第三方统计小时数据
     * author: 
     */
    private function GetPiwikHourData()
    {
        $_GET['domain'] = isset($_GET['domain']) ? $_GET['domain'] : '';
        $_GET['date'] = isset($_GET['date']) ? $_GET['date'] : '';
        $params['where'] = '';
        //搜索时间日期
        if ($this->get('start_date') != '' && $this->get('end_date') != '') {
            if ($this->get('start_time') != '' && $this->get('end_time') != '') {
                $serch_day = (strtotime($this->get('end_date'))-strtotime($this->get('start_date')))/86400;
                $stat_date = '';
                for ( $i = 0;$i <= $serch_day;$i++ ){
                    $start_time = strtotime( $this->get( 'start_date' ).' '.$this->get( 'start_time' ) )+$i*86400+1;
                    $end_time = strtotime( $this->get( 'start_date' ).' '.$this->get( 'end_time' ) )+3600-1+$i*86400;

                    if ($i == $serch_day){
                        $stat_date .= " ( a.stat_date between $start_time and $end_time ) ";
                    }else{
                        $stat_date .= " ( a.stat_date between $start_time and $end_time ) or ";
                    }
                }
                $params['where'] .= " and ( $stat_date ) ";
            }
            $start = strtotime($this->get('start_date'));
            $end = strtotime($this->get('end_date'))+86400-1;
            $params['where'] .= " and(a.stat_date between $start and $end ) ";
        }
        //域名详情
        if ($_GET['domain'] != '' &&  $_GET['date'] != '' ) {
            $time_s = strtotime($_GET['date'])+1;
            $time_e = $time_s+86400;
            $params['where'] .= " and(a.domain='" . $_GET['domain'] . "') ";
            $params['where'] .= " and(a.stat_date between $time_s and $time_e ) ";
        }
        //搜索推管人员
        if ($this->get('user_id') != '') {
            $params['where'] .= " and(c.sno=" . intval($this->get('user_id')) . ") ";
        }
        //搜索计费方式
        if ($this->get('chgId') != '') {
            $params['where'] .= " and(c.charging_type=" . intval($this->get('chgId')) . ") ";
        }
        //搜索合作商、渠道编码、渠道名称
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
        $params['select']="  a.*,c.unit_price,c.charging_type,c.sno,c.weixin_group_id,d.channel_name,d.channel_code,e.name  ";
        $params['order'] = "  order by a.id desc    ";
        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['pagesize'] = 1000;
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(PiwikHour::model()->tableName())->listdata($params);
        $data = $page['listdata']['list'];
        foreach ($data as $key => $val) {
            $data[$key]['end_date'] = date('H:i', $val['stat_date']-1);
            $data[$key]['stat_date'] = date('Y-m-d H:i', $val['stat_date']-3600);
            $data[$key]['csname_true'] = AdminUser::model()->findByPk($val['sno'])->csname_true;
            $data[$key]['domain'] = DomainList::model()->findByPk($val['domain_id'])->domain;
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
}