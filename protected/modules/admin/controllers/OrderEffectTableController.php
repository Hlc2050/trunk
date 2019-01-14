<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/2
 * Time: 9:16
 */

class OrderEffectTableController extends AdminController
{

    public function actionIndex()
    {
        $uid=Yii::app()->admin_user->uid;
        ini_set('memory_limit', -1);
        $group_id = $this->get('group_id') ? $this->get('group_id') : 0;
        $cache = Yii::app()->cache;
        $cache_data = $cache->get('orderEffectData_'.$uid);
        if (!$cache_data) {
            $allData = array();
            $allData['cache_start_date'] = '';
            $allData['cache_end_date'] = '';
            $allData['date'] = '无缓存';
        }else {
            switch ($group_id) {
                case 0: // 整体表
                    $allData = $this->integralData();
                    break;
                case 1: // 推广表
                    $allData = $this->promotonData();
                    break;
                case 2: // 合作商表
                    $allData = $this->partnerData();
                    break;
                case 3: // 渠道
                    $allData = $this->channelData();
                    break;
                case 4: // 客服部
                    $allData = $this->serviceData();
                    break;
                case 5: // 计费方式
                    $allData = $this->chargeData();
                    break;
                case 6: // 图文
                    $allData = $this->articleData();
                    break;
            }
            if ($this->get('start_online_date') < $this->get('end_online_date') && $this->get('start_online_date') && $this->get('end_online_date')) {
                if ($this->get('start_online_date') >= $allData['cache_start_date'] && $this->get('start_online_date') <= $allData['cache_end_date']) {
                    $allData['cache_start_date'] = $this->get('start_online_date');
                }

                if ($this->get('end_online_date') >= $allData['cache_start_date'] && $this->get('end_online_date') <= $allData['cache_end_date']) {
                    $allData['cache_end_date'] = $this->get('end_online_date');
                }
            }
            if ($this->get('start_online_date') == $this->get('end_online_date')) {
                $allData['cache_start_date'] = $this->get('start_online_date');
                $allData['cache_end_date'] = $this->get('end_online_date');
            }
        }
//        $allData = $this ->getData();
        $allData['params_groups'] = vars::$fields['effect_tables'];
        $this->render('index',array('allData'=>$allData));
    }
    /**
     * 生成缓存
     *
     */
    public function actionCache()
    {
        ini_set('memory_limit', -1);
        if($this->post('cache_start_date') && $this->post('cache_end_date')) {
            //清除session 重新缓存
            $cache = Yii::app()->cache;
            $uid=Yii::app()->admin_user->uid;
            $cache->delete('orderEffectData_'.$uid);
            $page['cache_start_date'] = $this->post('cache_start_date');
            $page['cache_end_date'] = $this->post('cache_end_date');
            $page['date'] = $page['cache_start_date'] . "至" . $page['cache_end_date'];
            $start_date = strtotime($this->post('cache_start_date'));
            $end_date = strtotime($this->post('cache_end_date'));
            $dx_pid = !isset(Yii::app()->params['basic']['dx_bid']) ? 8:intval(Yii::app()->params['basic']['dx_bid']);
            $param = " where a.stat_date between " . $start_date . " and " . $end_date . " and a.business_type = ".$dx_pid;

            // 查看人员权限
            $result = $this->data_authority();
            if ($result != 0) {
                $param .= " and a.tg_uid in ($result) ";
            }
            // 成本明细
            $statCostDetail = StatCostDetail::model()->getEffectTableData($param);
            // 获取修正成本
            $condition = " stat_date between " . $start_date . " and " . $end_date . "  group by stat_date,channel_id,weixin_id,business_type,charging_type,goods_id,customer_service_id";
            $fixCostInfo = FixedCost::model()->getFixedCost($condition);
            foreach ($fixCostInfo as $value)
            {
                $key = $value['stat_date'].'_'.$value['weixin_id'].'_'.$value['channel_id'];
                $fixCostInfo[$key] = $value;
            }
            // 渠道数据
            $condition = " online_date between " . $start_date . " and " . $end_date . "  group by online_date,channel_id";
            $channelData = ChannelData::model()->getChannelData($condition);
            foreach ($channelData as $value) {
                $key = $value['online_date'].'_'.$value['channel_id'];
                $channelData[$key] = $value;
            }
            // 获取缓存进线数据
            $where = " stat_date between " . $start_date . " and " . $end_date . "  group by stat_date,channel_id,weixin_id";
            $orderInfo = OrdersSortByDay::model()->getOrderCache($where);
            $new_orderInfo = array();
            foreach ($orderInfo as $value) {
                $key = $value['stat_date'].'_'.$value['weixin_id'].'_'.$value['channel_id'];
                $new_orderInfo[$key] = $value;
            }
            $allData = $temp_Info = array();
            foreach ($statCostDetail as $key=>$value) {
                $allData[$key] = $value;
                $temp_Info_key = $value['stat_date'] . "_" . $value['weixin_id'];
                if (!array_key_exists($temp_Info_key, $temp_Info)) {
                    $temp_Info[$temp_Info_key] = $key;
                }
                $key_2 = $value['stat_date'].'_'.$value['weixin_id'].'_'.$value['channel_id'];
                $allData[$key]['fixed_cost'] = array_key_exists($key_2, $fixCostInfo) ? $fixCostInfo[$key_2]['fixed_cost'] : 0;
                // 投入
                $allData[$key]['money'] = round($allData[$key]['money'] + $allData[$key]['fixed_cost'], 2);
                $allData[$key]['in_count'] = array_key_exists($key_2, $new_orderInfo) ? $new_orderInfo[$key_2]['in_count'] : 0;
                $allData[$key]['sout_count'] = array_key_exists($key_2, $new_orderInfo) ? $new_orderInfo[$key_2]['sout_count'] : 0;
                $allData[$key]['sdelivery_money'] = array_key_exists($key_2, $new_orderInfo) ? $new_orderInfo[$key_2]['sdelivery_money'] : 0;
                unset($new_orderInfo[$key_2]);
            }
            if (!empty($new_orderInfo)) {
                foreach ($new_orderInfo as $key => $value) {
                    $keyInfo = explode('_', $key);
                    $temp_date = intval($keyInfo[0]) - 86400;
                    for ($d = $temp_date; $d >= ($temp_date - 86400 * 10); $d = $d - 86400) {
                        $key = $d . "_" . $keyInfo[1];
                        if (array_key_exists($key, $temp_Info)) {
                            $k = $temp_Info[$key];
                            $allData[$k]['in_count'] += $value['in_count'];
                            $allData[$k]['sout_count'] += $value['sout_count'];
                            $allData[$k]['sdelivery_money'] += round($value['sdelivery_money'],2);
                            break;
                        }
                    }
                }
            }
            $page['list'] = $allData;
            $a = $cache->set('orderEffectData_'.$uid, serialize($page));
            echo "缓存成功！";
            echo $a;
            exit;
        }
        echo "noData";
        exit();
    }

    // 整体表导出
    public function actionExport()
    {
        $headlist = array('上线日期','合作商','渠道名称','渠道编码','微信号ID','计费方式','推广人员','客服部','商品','投入金额','发货金额','ROI','进线成本','均线产出','发货量','进线量','IP','UV','渠道转化','图文转化');
        $allData = $this->getAllEffectData();
        $export_row = array();
        // 统计行
        $total_row = array('-','-','-','-','-','-','-','-','合计',round($allData['total_money'],2),round($allData['total_deliver_money'],2),$allData['total_ROI'] .'%',$allData['total_in_cost'],
            $allData['total_output'],$allData['total_out_count'], $allData['total_in_count'],$allData['total_ip'],$allData['total_uv'],$allData['total_channel_transform'] .'%',$allData['total_article_transform'].'%');
        foreach ($total_row as $key=>$vale) {
            $total_row[$key] = iconv('utf-8','gbk',$vale);
        }
        $export_row[0] = $total_row;
        $i = 1;
        foreach ($allData['list'] as $key=>$value) {
            $ROI = $value['money'] ? round($value['sdelivery_money'] * 100 / $value['money'],2) : 0; // ROI
            $in_cost = $value['in_count'] ? round($value['money'] / $value['in_count'],2) : 0; // 进线成本
            $channel_transform = $value['in_count'] ? round($value['sout_count'] * 100 / $value['in_count'],2) : 0; // 渠道转化
            $output = $value['in_count'] ? round($value['sdelivery_money'] / $value['in_count'],2) : 0; // 均线产出
            $article_transform = $value['uv'] ? round($value['in_count'] * 100 / $value['uv'],2) : 0; // 图文转化
            $row = array(date('Y-m-d',$value['stat_date']),$value['partner_name'],$value['channel_name'],$value['channel_code'],$value['wechat_id'],vars::get_field_str('charging_type', $value['charging_type']),$value['csname_true'],$value['cname'],
                $value['goods_name'],$value['money'],round($value['sdelivery_money'],2),$ROI .'%',$in_cost,$output,$value['sout_count'],$value['in_count'],$value['ip'],$value['uv'],$channel_transform .'%',$article_transform .'%');
            foreach ($row as $k=>$v) {
                $row[$k] = iconv('utf-8','gbk',$v);
            }
            $export_row[$i] = $row;
            $i++;
        }
        helper::downloadCsv($headlist,$export_row,'电销整体表-' . date('Ymd', time()));
    }

    /**
     * 整体表数据
     */
    private function integralData()
    {
        $dataArr = $this->getAllEffectData();
        $page = $this->get('p');
        //分页
        $pagesize = Yii::app()->params['management']['pagesize'] ;
        $rows = count($dataArr['list']); //计算数组所得到记录总数
        ($page == "") ? $page = 1 : $page = $this->get('p'); //初始化页码
        $offset = $page - 1; //初始化分页指针
        $start = $offset * $pagesize; //初始化下限
        $data = $dataArr;
        $data['list'] = array_slice($dataArr['list'], $start, $pagesize);
        $pagearr = helper::pagehtml(array('total' => $rows, "pagesize" => $pagesize, "show" => 1));
        $data['pageInfo'] = $pagearr;
        return $data;
    }

    // 整体表数据
    private function getAllEffectData()
    {
        $cache = Yii::app()->cache;
        $uid=Yii::app()->admin_user->uid;
        $data = $cache->get('orderEffectData_'.$uid);
        $records = unserialize($data);
        $new_arr = array();
        $new_arr['cache_start_date'] = $records['cache_start_date'];
        $new_arr['cache_end_date'] = $records['cache_end_date'];
        $new_arr['date'] = $records['date'];
        $new_arr['list'] = array();
        // 过滤不符合查询条件数据
        // 时间条件
        $date_flag = 0;
        if ($this->get('start_online_date') || $this->get('end_online_date')) {
            if ($this->get('start_online_date') && $this->get('end_online_date')) {
                if ($this->get('start_online_date') <= $this->get('end_online_date')) {
                    $start_date = strtotime($this->get('start_online_date'));
                    $end_date = strtotime($this->get('end_online_date'));
                    $date_flag = 1; // 开始时+结束时间
                }
            } elseif ($this->get('start_online_date')) {
                $start_date = strtotime($this->get('start_online_date'));
                $date_flag = 2; // 开始时间
            } elseif ($this->get('end_online_date')) {
                $end_date = strtotime($this->get('end_online_date'));
                $date_flag = 3; // 结束时间
            }
        }
        foreach ($records['list'] as $key=>$value) {
            // 上线日期
            if ($date_flag == 1) {
                if ($value['stat_date'] < $start_date || $value['stat_date'] > $end_date) continue;
            } elseif ($date_flag == 2) {
                if ($value['stat_date'] < $start_date ) continue;
            } elseif ($date_flag == 2) {
                if ($value['stat_date'] > $end_date ) continue;
            }
            // 合作商
            if ($this->get('partner_name')) {
                if (strpos($value['partner_name'],$this->get('partner_name')) === false ) continue;
            }
            // 渠道名称
            if ($this->get('channel_name')) {
                if (strpos($value['channel_name'],$this->get('channel_name')) === false ) continue;
            }
            // 微信号ID
            if ($this->get('wechat_id')) {
                if (strpos($value['wechat_id'],$this->get('wechat_id')) === false ) continue;
            }
            // 客服部
            if ($this->get('csid') != '') {
                if ( $value['customer_service_id'] != $this->get('csid')) continue;
            }
            // 商品
            if ($this->get('goods_id') != '') {
                if ( $value['goods_id'] != $this->get('goods_id')) continue;
            }
            // 推广人员
            if ($this->get('user_id') != '') {
                if ( $value['tg_uid'] != $this->get('user_id')) continue;
            }
            //  计费方式
            if ($this->get('chgId') != '') {
                if ( $value['charging_type'] != $this->get('chgId')) continue;
            }
            $new_arr['list'][] = $value;
        }
        // 统计项
        // 总投入金额
        $new_arr['total_money'] = array_sum(array_column($new_arr['list'],'money'));
        // 总发货金额
        $new_arr['total_deliver_money'] = array_sum(array_column($new_arr['list'],'sdelivery_money'));
        // 总发货量
        $new_arr['total_out_count'] = array_sum(array_column($new_arr['list'],'sout_count'));
        // 总进线量
        $new_arr['total_in_count'] = array_sum(array_column($new_arr['list'],'in_count'));
        // ROI
        $new_arr['total_ROI'] = $new_arr['total_money'] ? round($new_arr['total_deliver_money'] *100 / $new_arr['total_money'],2) : 0;
        // 进线成本
        $new_arr['total_in_cost'] = $new_arr['total_in_count'] ? round($new_arr['total_money'] / $new_arr['total_in_count'],2) : 0;
        // 均线产出
        $new_arr['total_output'] = $new_arr['total_in_count'] ? round($new_arr['total_deliver_money'] / $new_arr['total_in_count'],2) : 0;
        // IP
        $new_arr['total_ip'] = array_sum(array_column($new_arr['list'],'ip'));
        // UV
        $new_arr['total_uv'] = array_sum(array_column($new_arr['list'],'uv'));
        // 渠道转化
        $new_arr['total_channel_transform'] = $new_arr['total_in_count'] ? round($new_arr['total_out_count']*100 / $new_arr['total_in_count'],2) : 0;
        // 图文转化
        $new_arr['total_article_transform'] = $new_arr['total_uv'] ? round($new_arr['total_in_count']*100 / $new_arr['total_uv'],2) : 0;
        return $new_arr;
    }

    // 推广表数据
    private function promotonData()
    {
        $cache = Yii::app()->cache;
        $uid=Yii::app()->admin_user->uid;
        $ret = $cache->get('orderEffectData_'.$uid);
        //过滤数据
        $records = $this->foreachFilter(unserialize($ret));
        $group_by_fields = array(
            'tg_uid' => function ($value) {
                return $value;
            }
        );
        $group_by_value = array(
            'tg_uid',
            'csname_true',
            'money' => function ($data) {
                return array_sum(array_column($data, 'money'));
            },
            'sdelivery_money' => function ($data) {
                return array_sum(array_column($data, 'sdelivery_money'));
            },
            'in_count' => function ($data) {
                return array_sum(array_column($data, 'in_count'));
            },
            'sout_count' => function ($data) {
                return array_sum(array_column($data, 'sout_count'));
            },
            'ip' => function ($data) {
                return array_sum(array_column($data, 'ip'));
            },
            'uv' => function ($data) {
                return array_sum(array_column($data, 'uv'));
            },
        );
        Yii::import('application.extensions.ArrayGroupBy.ArrayGroupBy', 1);
        $data['cache_start_date'] = $records['cache_start_date'];
        $data['cache_end_date'] = $records['cache_end_date'];
        $data['date'] = $records['date'];
        $data['list'] = ArrayGroupBy::groupBy($records['list'], $group_by_fields, $group_by_value);
        return $data;
    }

    // 合作商表数据
    private function partnerData()
    {
        $cache = Yii::app()->cache;
        $uid=Yii::app()->admin_user->uid;
        $ret = $cache->get('orderEffectData_'.$uid);
        //过滤数据
        $records = $this->foreachFilter(unserialize($ret));
        $group_by_fields = array(
            'partner_id' => function ($value) {
                return $value;
            }
        );
        $group_by_value = array(
            'partner_id',
            'partner_name',
            'money' => function ($data) {
                return array_sum(array_column($data, 'money'));
            },
            'sdelivery_money' => function ($data) {
                return array_sum(array_column($data, 'sdelivery_money'));
            },
            'in_count' => function ($data) {
                return array_sum(array_column($data, 'in_count'));
            },
            'sout_count' => function ($data) {
                return array_sum(array_column($data, 'sout_count'));
            },
            'ip' => function ($data) {
                return array_sum(array_column($data, 'ip'));
            },
            'uv' => function ($data) {
                return array_sum(array_column($data, 'uv'));
            },
        );
        Yii::import('application.extensions.ArrayGroupBy.ArrayGroupBy', 1);
        $data['cache_start_date'] = $records['cache_start_date'];
        $data['cache_end_date'] = $records['cache_end_date'];
        $data['date'] = $records['date'];
        $data['list'] = ArrayGroupBy::groupBy($records['list'], $group_by_fields, $group_by_value);
        return $data;
    }
    // 渠道数据
    private function channelData()
    {
        $cache = Yii::app()->cache;
        $uid=Yii::app()->admin_user->uid;
        $ret = $cache->get('orderEffectData_'.$uid);
        //过滤数据
        $records = $this->foreachFilter(unserialize($ret));
        $group_by_fields = array(
            'channel_id' => function ($value) {
                return $value;
            },
            'stat_date' => function ($value) {
                return $value;
            },
        );
        $group_by_value = array(
            'channel_id',
            'stat_date',
            'channel_name',
            'money' => function ($data) {
                return array_sum(array_column($data, 'money'));
            },
            'sdelivery_money' => function ($data) {
                return array_sum(array_column($data, 'sdelivery_money'));
            },
            'in_count' => function ($data) {
                return array_sum(array_column($data, 'in_count'));
            },
            'sout_count' => function ($data) {
                return array_sum(array_column($data, 'sout_count'));
            },
            'ip' => function ($data) {
                return array_sum(array_column($data, 'ip'));
            },
            'uv' => function ($data) {
                return array_sum(array_column($data, 'uv'));
            },
        );
        Yii::import('application.extensions.ArrayGroupBy.ArrayGroupBy', 1);
        $data['cache_start_date'] = $records['cache_start_date'];
        $data['cache_end_date'] = $records['cache_end_date'];
        $data['date'] = $records['date'];
        $data['list'] = ArrayGroupBy::groupBy($records['list'], $group_by_fields, $group_by_value);
        return $data;
    }

    // 客服部表数据
    private function serviceData()
    {
        $cache = Yii::app()->cache;
        $uid=Yii::app()->admin_user->uid;
        $ret = $cache->get('orderEffectData_'.$uid);
        //过滤数据
        $records = $this->foreachFilter(unserialize($ret));
        $group_by_fields = array(
            'customer_service_id' => function ($value) {
                return $value;
            },
        );
        $group_by_value = array(
            'customer_service_id',
            'cname',
            'money' => function ($data) {
                return array_sum(array_column($data, 'money'));
            },
            'sdelivery_money' => function ($data) {
                return array_sum(array_column($data, 'sdelivery_money'));
            },
            'in_count' => function ($data) {
                return array_sum(array_column($data, 'in_count'));
            },
            'sout_count' => function ($data) {
                return array_sum(array_column($data, 'sout_count'));
            },
            'ip' => function ($data) {
                return array_sum(array_column($data, 'ip'));
            },
            'uv' => function ($data) {
                return array_sum(array_column($data, 'uv'));
            },
        );
        Yii::import('application.extensions.ArrayGroupBy.ArrayGroupBy', 1);
        $data['cache_start_date'] = $records['cache_start_date'];
        $data['cache_end_date'] = $records['cache_end_date'];
        $data['date'] = $records['date'];
        $data['list'] = ArrayGroupBy::groupBy($records['list'], $group_by_fields, $group_by_value);
        return $data;
    }

    // 计费方式表数据
    private function chargeData()
    {
        $cache = Yii::app()->cache;
        $uid=Yii::app()->admin_user->uid;
        $ret = $cache->get('orderEffectData_'.$uid);
        //过滤数据
        $records = $this->foreachFilter(unserialize($ret));
        $group_by_fields = array(
            'charging_type' => function ($value) {
                return $value;
            },
        );
        $group_by_value = array(
            'charging_type',
            'money' => function ($data) {
                return array_sum(array_column($data, 'money'));
            },
            'sdelivery_money' => function ($data) {
                return array_sum(array_column($data, 'sdelivery_money'));
            },
            'in_count' => function ($data) {
                return array_sum(array_column($data, 'in_count'));
            },
            'sout_count' => function ($data) {
                return array_sum(array_column($data, 'sout_count'));
            },
            'ip' => function ($data) {
                return array_sum(array_column($data, 'ip'));
            },
            'uv' => function ($data) {
                return array_sum(array_column($data, 'uv'));
            },
        );
        Yii::import('application.extensions.ArrayGroupBy.ArrayGroupBy', 1);
        $data['cache_start_date'] = $records['cache_start_date'];
        $data['cache_end_date'] = $records['cache_end_date'];
        $data['date'] = $records['date'];
        $data['list'] = ArrayGroupBy::groupBy($records['list'], $group_by_fields, $group_by_value);
        return $data;
    }

    // 图文编码表数据
    private function articleData()
    {
        $cache = Yii::app()->cache;
        $uid=Yii::app()->admin_user->uid;
        $ret = $cache->get('orderEffectData_'.$uid);
        //过滤数据
        $records = $this->foreachFilter(unserialize($ret));
        $group_by_fields = array(
            'article_code' => function ($value) {
                return $value;
            },
        );
        $group_by_value = array(
            'article_code',
            'article_type',
            'money' => function ($data) {
                return array_sum(array_column($data, 'money'));
            },
            'sdelivery_money' => function ($data) {
                return array_sum(array_column($data, 'sdelivery_money'));
            },
            'in_count' => function ($data) {
                return array_sum(array_column($data, 'in_count'));
            },
            'sout_count' => function ($data) {
                return array_sum(array_column($data, 'sout_count'));
            },
            'ip' => function ($data) {
                return array_sum(array_column($data, 'ip'));
            },
            'uv' => function ($data) {
                return array_sum(array_column($data, 'uv'));
            },
        );
        Yii::import('application.extensions.ArrayGroupBy.ArrayGroupBy', 1);
        $data['cache_start_date'] = $records['cache_start_date'];
        $data['cache_end_date'] = $records['cache_end_date'];
        $data['date'] = $records['date'];
        $data['list'] = ArrayGroupBy::groupBy($records['list'], $group_by_fields, $group_by_value);
        return $data;
    }
    // 分表数据
    private function foreachFilter($records)
    {
        $new_arr = array();
        $new_arr['cache_start_date'] = $records['cache_start_date'];
        $new_arr['cache_end_date'] = $records['cache_end_date'];
        $new_arr['date'] = $records['date'];
        $new_arr['list'] = array();
        // 过滤不符合查询条件数据
        // 时间条件
        $date_flag = 0;
        if ($this->get('start_online_date') || $this->get('end_online_date')) {
            if ($this->get('start_online_date') && $this->get('end_online_date')) {
                if ($this->get('start_online_date') <= $this->get('end_online_date')) {
                    $start_date = strtotime($this->get('start_online_date'));
                    $end_date = strtotime($this->get('end_online_date'));
                    $date_flag = 1; // 开始时+结束时间
                }
            } elseif ($this->get('start_online_date')) {
                $start_date = strtotime($this->get('start_online_date'));
                $date_flag = 2; // 开始时间
            } elseif ($this->get('end_online_date')) {
                $end_date = strtotime($this->get('end_online_date'));
                $date_flag = 3; // 结束时间
            }
        }
        foreach ($records['list'] as $key=>$value) {
            // 上线日期
            if ($date_flag == 1) {
                if ($value['stat_date'] < $start_date || $value['stat_date'] > $end_date) continue;
            } elseif ($date_flag == 2) {
                if ($value['stat_date'] < $start_date ) continue;
            } elseif ($date_flag == 2) {
                if ($value['stat_date'] > $end_date ) continue;
            }
            // 合作商
            if ($this->get('partner_name')) {
                if (strpos($value['partner_name'],$this->get('partner_name')) === false ) continue;
            }
            // 渠道名称
            if ($this->get('channel_name')) {
                if (strpos($value['channel_name'],$this->get('channel_name')) === false ) continue;
            }
            // 微信号ID
            if ($this->get('wechat_id')) {
                if (strpos($value['wechat_id'],$this->get('wechat_id')) === false ) continue;
            }
            // 客服部
            if ($this->get('csid') != '') {
                if ( $value['customer_service_id'] != $this->get('csid')) continue;
            }
            // 商品
            if ($this->get('goods_id') != '') {
                if ( $value['goods_id'] != $this->get('goods_id')) continue;
            }
            // 推广人员
            if ($this->get('user_id') != '') {
                if ( $value['tg_uid'] != $this->get('user_id')) continue;
            }
            //  计费方式
            if ($this->get('chgId') != '') {
                if ( $value['charging_type'] != $this->get('chgId')) continue;
            }
            $new_arr['list'][] = $value;
        }
        return $new_arr;
    }
}