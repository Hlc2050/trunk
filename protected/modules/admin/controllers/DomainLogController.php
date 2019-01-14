<?php

/**
 * 监控日志控制器
 * User: fang
 * Date: 2016/11/7
 * Time: 9:15
 */
class DomainLogController extends AdminController
{
    public function actionIndex()
    {
        $data = $temp = array();
        $is_public = array('0'=>'','1'=>'(公众号)');

        $params['order'] = "  order by id desc    ";
        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model('domain_intercept_log')->listdata($params);
        $domain_list = DomainList::model()->getIsPublic();
        foreach ($page['listdata']['list'] as $value){
            if(array_key_exists($value['domain'],$domain_list)){
                $temp['domain'] = $value['domain'].$is_public[$domain_list[$value['domain']]];
            }
            if(array_key_exists($value['domain2'],$domain_list)){
                $temp['domain2'] = $value['domain2'].$is_public[$domain_list[$value['domain2']]];
            }

            $temp['domain_type'] = vars::get_field_str('domain_types', $value['domain_type']);
            $temp['detection_type'] = $value['detection_type']==0?'微信拦截':'掉备案';
            $temp['create_time'] = date("Y-m-d H:i",$value['create_time']);
            $temp['update_time'] = date("Y-m-d H:i",$value['update_time']);
            //如果域名类型是推广  使用天数 = 最后更新时间-域名添加时间
            $temp['useday'] = $value['domain_type'] == 0?ceil(($value['update_time'] - $value['create_time']) / (3600 * 24)):'-';
            $data[] = $temp;
        }

        $this->render('index', array('data' => $data));
    }

    /**
     * 导出
     */
    public function actionExport()
    {
        $headlist = array('ID','原域名', '替换域名', '类型', '监测内容', '域名添加时间', '最后更新时间', '使用天数');
        $file_name = '域名拦截日志' . date('ymd');
        $params['order'] = "  order by id desc    ";
        $params['pagesize'] = 10000;
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $data_list = $page['listdata'] = Dtable::model('domain_intercept_log')->listdata($params);
        $data = $data_list['list'];
        $temp_array = array();
        $count = count($data);
        for ($i = 0;$i<$count;$i++){
            $line = $i;
            $check = "微信拦截";
            $data[$i]['domain_type'] = vars::get_field_str('domain_types',$data[$i]['domain_type']);
            if ($data[$i]['detection_type'] == 1) $check='掉备案';
            $usedate =round(($data[$i]['update_time'] - $data[$i]['create_time'])/86400);
            if($data[$i]['domain_type'] == "跳转") $usedate = "-";
            $temp_array[$line] = array(
                $data[$i]['id'],
                $data[$i]['domain'],
                $data[$i]['domain2'],
                $data[$i]['domain_type'],
                $check,
                date('Y-m-d H:i',$data[$i]['create_time']),
                date('Y-m-d H:i',$data[$i]['update_time']),
                $usedate,
            );
            foreach ($temp_array[$line] as $key =>$value){
                $temp_array[$line][$key] = iconv('utf-8','gbk',$value);
            }
        }
        helper::downloadCsv($headlist,$temp_array,$file_name);
    }
}