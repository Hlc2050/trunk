<?php

/**
 * 渠道域名统计控制器
 * User: hlc
 * Date: 2018/12/28
 * Time: 11:24
 */
class DomainChannelStatsController extends AdminController
{
    public function actionIndex()
    {
        $data = $this->getDataList();
        //分页
        $page = helper::getPage($this->get('p'), 20, $data);

        $this->render('index', array('list' => $page));
    }

    /**
     * 导出渠道域名统计
     * author: hlc
     */
    public function actionExport()
    {

        $temp_array = array();
        $colums = array('推广ID','渠道名称', '渠道编码', '状态', '推广人员', '总替换域名个数', '今日替换域名个数', '昨日替换域名个数', '掉备案域名');
        $file_name = '渠道域名统计列表-' . date("Ymj") . '.xls';
        $data = $this->getDataList();
        foreach ($data as $key=>$value) {
            $temp_array[] = array(
                iconv('utf-8', 'gbk', $value['promotion_id']),
                iconv('utf-8', 'gbk', $value['channel_name']),
                iconv('utf-8', 'gbk', $value['channel_code']),
                iconv('utf-8', 'gbk', $value['status']),
                iconv('utf-8', 'gbk', $value['name']),
                iconv('utf-8', 'gbk', $value['all_num']),
                iconv('utf-8', 'gbk', $value['today_num']),
                iconv('utf-8', 'gbk', $value['yseterday_num']),
                iconv('utf-8', 'gbk', $value['detection']),
            );
            }

        helper::downloadCsv($colums, $temp_array, $file_name);
    }

    private function getDataList(){

        $temp = array();
        $statusArr = array('0'=>'正常','1'=>'下线','2'=>'暂停');
        $params = '';

        $now = time();
        $yesterday = strtotime(date('Y-m-d 00:00:00', strtotime('-1 day')));
        $today_begin = strtotime(date('Y-m-d 00:00:00', $now));
        $tomorrow = strtotime(date('Y-m-d 00:00:00', strtotime('+1 day')));

        //搜索条件
        if ($this->get('promotion_id')) {
            $params .= " and(a.promotion_id  =" . $this->get('promotion_id') . ") ";
        }
        if ($this->get('user_id')) {
            $params .= " and(a.uid =" . $this->get('user_id') . ") ";
        }
        if ($this->get('channel_name')) {
            $params .= " and(b.channel_name like '%" . trim($this->get('channel_name')) . "%') ";
        }
        if ($this->get('status') != '') {
            $params .= " and a.status =".$this->get('status');
        }
        //推广管理表
        $sql = 'select a.id,a.status,b.channel_code,b.channel_name,c.time,c.detection_type,c.mark,f.name from promotion_manage as a left join channel as b on b.id=a.channel_id left join domain_intercept_detail as c on c.promotion_id=a.id left join finance_pay as d on d.id=a.finance_pay_id left join promotion_staff_manage as f on f.user_id=d.sno where a.id !=0 ' . $params . ' order by a.id desc';
        $table_list = Yii::app()->db->createCommand($sql)->queryAll();

        //总替换域名个数、今日替换域名个数、昨日替换域名个数统计替换成功的且检测类型为：0：拦截检测，掉备案域名统计所有的且检测类型：1：备案检测
        foreach ($table_list as $value) {
            $key = $value['id'];
            //推广ID
            $temp[$key]['promotion_id'] = $value['id'];
            //渠道名称
            $temp[$key]['channel_name'] = $value['channel_name'];
            //渠道编码
            $temp[$key]['channel_code'] = $value['channel_code'];
            //推广状态
            $status = $statusArr[$value['status']]?$statusArr[$value['status']]:'';
            $temp[$key]['status'] = $status;
            //推广人员
            $temp[$key]['name'] = $value['name'];

            if (strstr($value['mark'], '成功') && $value['detection_type'] == 0) {
                //总替换域名个数
                $temp[$key]['all_num'] += 1;
                //今日替换域名个数
                if ($today_begin < $value['time'] && $value['time'] < $tomorrow) {
                    $temp[$key]['today_num'] += 1;
                } else {
                    $temp[$key]['today_num'] += 0;
                }
                //昨日替换域名个数
                if ($yesterday < $value['time'] && $value['time'] < $today_begin ) {
                    $temp[$key]['yseterday_num'] += 1;
                } else {
                    $temp[$key]['yseterday_num'] += 0;
                }
            }else{
                $temp[$key]['all_num'] = 0;
                $temp[$key]['today_num'] = 0;
                $temp[$key]['yseterday_num'] = 0;
            }
            //掉备案域名
            if ($value['detection_type'] == 1) {
                $temp[$key]['detection'] += 1;
            } else {
                $temp[$key]['detection'] += 0;
            }
        }

        return $temp;
    }

}