<?php

/**
 * 进粉观察表
 * User: hlc
 * Date: 2018/1/17
 * Time: 10:26
 */
class FansInputStaticController extends AdminController
{
    public function actionIndex()
    {
        $page = $this->getFanInput(0);
        $this->render('index', array('page' => $page));
    }

    /**
     * 数据处理
     */
    private function getFanInput($is_export)
    {
        $fans_list = array();

        $page = $this->getData($is_export);

        $all_addfan_count = $all_del_black = $all_brush_fans = $all_gender_dif_fans = $all_not_reply_fans = $all_age_dif_fans = $all_disease_fans = $all_valid_fans = 0;
        foreach ($page['listdata']['list'] as $key => $val) {
            $fans_list[$key]['csname_true'] = $val['csname_true'];
            $fans_list[$key]['addfan_count'] = $val['addfan_count'];
            $fans_list[$key]['del_black'] = $val['del_black'];
            $fans_list[$key]['brush_fans'] = $val['brush_fans'];
            $fans_list[$key]['gender_dif_fans'] = $val['gender_dif_fans'];
            $fans_list[$key]['not_reply_fans'] = $val['not_reply_fans'];
            $fans_list[$key]['age_dif_fans'] = $val['age_dif_fans'];
            $fans_list[$key]['disease_fans'] = $val['disease_fans'];
            $fans_list[$key]['valid_fans'] = $val['addfan_count'] - $val['del_black'] - $val['brush_fans'] - $val['gender_dif_fans'] - $val['not_reply_fans'] - $val['age_dif_fans'] - $val['disease_fans'];
            $all_addfan_count += $val['addfan_count'];
            $all_del_black += $val['del_black'];
            $all_brush_fans += $val['brush_fans'];
            $all_gender_dif_fans += $val['gender_dif_fans'];
            $all_not_reply_fans += $val['not_reply_fans'];
            $all_age_dif_fans += $val['age_dif_fans'];
            $all_disease_fans += $val['disease_fans'];
            $all_valid_fans += $fans_list[$key]['valid_fans'];

            $fans_list[$key]['black_rate'] = round($val['del_black'] * 100 / $val['addfan_count'], 2);
            $fans_list[$key]['brush_rate'] = round($val['brush_fans'] * 100 / $val['addfan_count'], 2);
            $fans_list[$key]['gender_rate'] = round($val['gender_dif_fans'] * 100 / $val['addfan_count'], 2);
            $fans_list[$key]['not_reply_rate'] = round($val['not_reply_fans'] * 100 / $val['addfan_count'], 2);
            $fans_list[$key]['age_dif_rate'] = round($val['age_dif_fans'] * 100 / $val['addfan_count'], 2);
            $fans_list[$key]['disease_rate'] = round($val['disease_fans'] * 100 / $val['addfan_count'], 2);
            $fans_list[$key]['valid_rate'] = round($fans_list[$key]['valid_fans'] * 100 / $val['addfan_count'], 2);
        }
        $page['listdata']['list'] = $fans_list;

        //总数计算
        $page['total']['all_addfan_count'] = $all_addfan_count;
        $page['total']['all_del_black'] = $all_del_black;
        $page['total']['all_brush_fans'] = $all_brush_fans;
        $page['total']['all_gender_dif_fans'] = $all_gender_dif_fans;
        $page['total']['all_not_reply_fans'] = $all_not_reply_fans;
        $page['total']['all_age_dif_fans'] = $all_age_dif_fans;
        $page['total']['all_disease_fans'] = $all_disease_fans;
        $page['total']['all_valid_fans'] = $all_valid_fans;
        //总比率
        $page['total']['all_black_rate'] = round($all_del_black * 100 / $all_addfan_count, 2);
        $page['total']['all_brush_rate'] = round($all_brush_fans * 100 / $all_addfan_count, 2);
        $page['total']['all_gender_rate'] = round($all_gender_dif_fans * 100 / $all_addfan_count, 2);
        $page['total']['all_not_reply_rate'] = round($all_not_reply_fans * 100 / $all_addfan_count, 2);
        $page['total']['all_age_dif_rate'] = round($all_age_dif_fans * 100 / $all_addfan_count, 2);
        $page['total']['all_disease_rate'] = round($all_disease_fans * 100 / $all_addfan_count, 2);
        $page['total']['all_valid_rate'] = round($all_valid_fans * 100 / $all_addfan_count, 2);

        return $page;
    }

    /**
     * 获取数据
     */
    private function getData($is_export)
    {
        $params['where'] = '';
        // 未选择日期时统计当月数据
        if (!$this->get('start_date') && !$this->get('end_date')) {
            $start_time = date("Y-m-d", mktime(0, 0, 0, date('m'), 1, date('Y')));
            $end_time = date("Y-m-d", time());
        } else {
            $start_time = $this->get('start_date');
            $end_time = $this->get('end_date');
        }
        //加粉日期
        if ($start_time && $end_time) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time);
            $params['where'] .= " and(a.addfan_date>=$start_time  and a.addfan_date<=$end_time) ";
        } elseif ($start_time && !$end_time) { //
            $start_time = strtotime($start_time);
            $params['where'] .= " and(a.addfan_date>=$start_time) ";
        } elseif ($end_time && !$start_time) {
            $end_time = strtotime($end_time);
            $params['where'] .= " and(a.addfan_date<=$end_time) ";
        }
        if ($this->get('user_id') != '') $params['where'] .= " and(g.csno = " . $this->get('user_id') . ") ";
        if ($this->get('wechat_id') != '') $params['where'] .= " and(b.wechat_id like '%" . $this->get('wechat_id') . "%') ";
        if ($this->get('csid') != '') $params['where'] .= " and(f.id = " . $this->get('csid') . ") ";
        if ($this->get('goods_id') != '') $params['where'] .= " and(a.goods_id = " . $this->get('goods_id') . ") ";

        $params['order'] = "  order by a.id desc      ";
        $params['group'] = 'group by a.tg_uid';
        $params['join'] = "
		left join wechat as b on b.id=a.weixin_id
		left join goods as c on c.id=a.goods_id
		left join business_types as d on d.bid=a.business_type
		left join customer_service_manage as f on f.id=a.customer_service_id
		left join cservice as g on g.csno=a.tg_uid
		";
        $params['pagesize'] = 1 == $is_export ? 20000 : Yii::app()->params['management']['pagesize'];
        $params['pagebar'] = 1;
        $params['select'] = "sum(a.addfan_count) as addfan_count,sum(a.del_black) as del_black,sum(a.brush_fans) as brush_fans,sum(a.gender_dif_fans) as gender_dif_fans,sum(a.not_reply_fans) as not_reply_fans,sum(a.age_dif_fans) as age_dif_fans,sum(a.disease_fans) as disease_fans,g.csname_true";

        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(FansInputManage::model()->tableName())->listdata($params);

        return $page;
    }

    /**
     * 导出进粉观察表
     */
    public function actionExport()
    {
        $file_name = '进粉观察表-' . date('Ymd', time());
        $headlist = array('推广人员', '进粉量', '删除拉黑', '刷粉', '性别不符合粉', '不回复粉', '年龄不符合', '疾病粉', '有效粉', '删除拉黑占比', '刷粉占比', '性别不符合粉占比', '不回复粉占比', '年龄不符合占比', '疾病粉占比', '有效粉占比');
        $data = $this->getFanInput(1);
        $total = $data['total_info'];
        $row = array();
        $row[0] = array(iconv('utf-8', 'gbk', '合计'), $total['addfan_count'], $total['del_black'], $total['brush_fans'], $total['gender_dif_fans'], $total['not_reply_fans'],
            $total['age_dif_fans'], $total['disease_fans'], $total['valid_fans'], $total['black_rate'] . '%', $total['brush_rate'] . '%', $total['gender_rate'] . '%', $total['not_reply_rate'] . '%', $total['age_dif_rate'] . '%', $total['disease_rate'] . '%', $total['valid_rate'] . '%');
        $data = $data['listdata']['list'];
        $count = count($data);
        for ($i = 0; $i < $count; $i++) {
            $k = $i + 1;
            $row[$k] = array(
                $data[$i]['csname_true'],
                $data[$i]['addfan_count'],
                $data[$i]['del_black'],
                $data[$i]['brush_fans'],
                $data[$i]['gender_dif_fans'],
                $data[$i]['not_reply_fans'],
                $data[$i]['age_dif_fans'],
                $data[$i]['disease_fans'],
                $data[$i]['valid_fans'],
                $data[$i]['black_rate'] . '%',
                $data[$i]['brush_rate'] . '%',
                $data[$i]['gender_rate'] . '%',
                $data[$i]['not_reply_rate'] . '%',
                $data[$i]['age_dif_rate'] . '%',
                $data[$i]['disease_rate'] . '%',
                $data[$i]['valid_rate'] . '%'
            );
            foreach ($row[$k] as $key => $value) {
                $row[$k][$key] = iconv('utf-8', 'gbk', $value);
            }
        }
        helper::downloadCsv($headlist, $row, $file_name);
    }

}