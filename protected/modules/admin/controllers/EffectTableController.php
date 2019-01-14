
<?php

/**
 * 效果表控制器
 * Class EffectTableController
 */
class EffectTableController extends AdminController
{
    public function actionIndex()
    {
        $uid=Yii::app()->admin_user->uid;
        ini_set('memory_limit', -1);
        $cache = Yii::app()->cache;
        $ret = $cache->get('allData_'.$uid);
        $page = array();
        $groupId = $this->get('group_id');
        $groupId = !empty($groupId) ? $groupId : 0;
        if (!$ret) {
            $page = array();
            $page['cache_start_date'] = '';
            $page['cache_end_date'] = '';
            $page['date'] = '无缓存';
        } else {
            switch ($groupId) {
                case 0://整体表
                    $page = $this->integralData();
                    break;
                case 1://推广人员
                    $page = $this->promotionStaffData();
                    break;
                case 2://合作商
                    $page = $this->partnerData();
                    break;
                case 3://渠道
                    $page = $this->channelData();
                    break;
                case 4://客服部
                    $page = $this->cSData();
                    break;
                case 5://计费方式
                    $page = $this->cTData();
                    break;
                case 6://图文
                    $page = $this->articleData();
                    break;
                default:
                    break;
            }
            if ($this->get('start_online_date') < $this->get('end_online_date') && $this->get('start_online_date') && $this->get('end_online_date')) {
                if ($this->get('start_online_date') >= $page['cache_start_date'] && $this->get('start_online_date') <= $page['cache_end_date']) {
                    $page['cache_start_date'] = $this->get('start_online_date');
                }

                if ($this->get('end_online_date') >= $page['cache_start_date'] && $this->get('end_online_date') <= $page['cache_end_date']) {
                    $page['cache_end_date'] = $this->get('end_online_date');
                }
            }
            if ($this->get('start_online_date') == $this->get('end_online_date')) {
                $page['cache_start_date'] = $this->get('start_online_date');
                $page['cache_end_date'] = $this->get('end_online_date');
            }
        }
        $page['params_groups'] = vars::$fields['effect_tables'];

        $this->render('index', array('page' => $page));
    }

    /**
     * 整体表数据
     * author: yjh
     */
    private function integralData()
    {
        $dataArr = $this->getAllEffectData();
        $page = $this->get('p');
        //分页
        $pagesize = Yii::app()->params['management']['effect_pagesize'];
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

    /**
     * 推广效果表
     * author: yjh
     */
    private function promotionStaffData()
    {
        $cache = Yii::app()->cache;
        $uid=Yii::app()->admin_user->uid;
        $ret = $cache->get('allData_'.$uid);
        //过滤数据 缓存在session的整体表

        $records = $this->foreachFilter(unserialize($ret));

        $group_by_fields = array(
            'tg_uid' => function ($value) {
                return $value;
            }
        );
        $group_by_value = array(
            'tg_uid',
            'csname_true',
            'uvs' => function ($data) {
                return array_sum(array_column($data, 'uvs'));
            },
            'fans' => function ($data) {
                return array_sum(array_column($data, 'fans'));
            },
            'money' => function ($data) {
                return array_sum(array_column($data, 'money'));
            },
            'fans_count' => function ($data) {
                return array_sum(array_column($data, 'fans_count'));
            },
            'estimate_money' => function ($data) {
                return array_sum(array_column($data, 'estimate_money'));
            },
            'estimate_count' => function ($data) {
                return array_sum(array_column($data, 'estimate_count'));
            },
        );
        Yii::import('application.extensions.ArrayGroupBy.ArrayGroupBy', 1);
        $data['cache_start_date'] = $records['cache_start_date'];
        $data['cache_end_date'] = $records['cache_end_date'];
        $data['date'] = $records['date'];
        $data['list'] = ArrayGroupBy::groupBy($records['list'], $group_by_fields, $group_by_value);

        return $data;
    }

    /**
     * 合作商效果表
     * author: yjh
     */
    private function partnerData()
    {
        $cache = Yii::app()->cache;
        $uid=Yii::app()->admin_user->uid;
        $ret = $cache->get('allData_'.$uid);
        $records = $this->foreachFilter(unserialize($ret));
        $group_by_fields = array(
            'partner_id' => function ($value) {
                return $value;
            }
        );
        $group_by_value = array(
            'partner_id',
            'partner_name',
            'uvs' => function ($data) {
                return array_sum(array_column($data, 'uvs'));
            },
            'fans' => function ($data) {
                return array_sum(array_column($data, 'fans'));
            },
            'money' => function ($data) {
                return array_sum(array_column($data, 'money'));
            },
            'fans_count' => function ($data) {
                return array_sum(array_column($data, 'fans_count'));
            },
            'estimate_money' => function ($data) {
                return array_sum(array_column($data, 'estimate_money'));
            },
            'estimate_count' => function ($data) {
                return array_sum(array_column($data, 'estimate_count'));
            },
        );
        Yii::import('application.extensions.ArrayGroupBy.ArrayGroupBy', 1);
        $data['cache_start_date'] = $records['cache_start_date'];
        $data['cache_end_date'] = $records['cache_end_date'];
        $data['date'] = $records['date'];
        $data['list'] = ArrayGroupBy::groupBy($records['list'], $group_by_fields, $group_by_value);
        return $data;

    }

    /**
     * 渠道效果表
     * author: yjh
     */
    private function channelData()
    {
        $cache = Yii::app()->cache;
        $uid=Yii::app()->admin_user->uid;
        $ret = $cache->get('allData_'.$uid);
        $records = $this->foreachFilter(unserialize($ret));
        $mtime = explode(' ', microtime());
        $round_2 = $mtime[1] + $mtime[0];
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
            'channel_name',
            'stat_date',
            'partner_id',
            'channel_code',
            'uvs' => function ($data) {
                return array_sum(array_column($data, 'uvs'));
            },
            'fans' => function ($data) {
                return array_sum(array_column($data, 'fans'));
            },
            'money' => function ($data) {
                return array_sum(array_column($data, 'money'));
            },
            'fans_count' => function ($data) {
                return array_sum(array_column($data, 'fans_count'));
            },
            'estimate_money' => function ($data) {
                return array_sum(array_column($data, 'estimate_money'));
            },
            'estimate_count' => function ($data) {
                return array_sum(array_column($data, 'estimate_count'));
            },
        );

        Yii::import('application.extensions.ArrayGroupBy.ArrayGroupBy', 1);
        $data['cache_start_date'] = $records['cache_start_date'];
        $data['cache_end_date'] = $records['cache_end_date'];
        $data['date'] = $records['date'];
        $data['list'] = ArrayGroupBy::groupBy($records['list'], $group_by_fields, $group_by_value);

        return $data;
    }

    /**
     * 客服部效果表
     * author: yjh
     */
    private function cSData()
    {
        $cache = Yii::app()->cache;
        $uid=Yii::app()->admin_user->uid;
        $ret = $cache->get('allData_'.$uid);
        $records = $this->foreachFilter(unserialize($ret));
        $group_by_fields = array(
            'customer_service_id' => function ($value) {
                return $value;
            }
        );
        $group_by_value = array(
            'customer_service_id',
            'cname',
            'uvs' => function ($data) {
                return array_sum(array_column($data, 'uvs'));
            },
            'fans' => function ($data) {
                return array_sum(array_column($data, 'fans'));
            },
            'money' => function ($data) {
                return array_sum(array_column($data, 'money'));
            },
            'fans_count' => function ($data) {
                return array_sum(array_column($data, 'fans_count'));
            },
            'estimate_money' => function ($data) {
                return array_sum(array_column($data, 'estimate_money'));
            },
            'estimate_count' => function ($data) {
                return array_sum(array_column($data, 'estimate_count'));
            },
        );
        Yii::import('application.extensions.ArrayGroupBy.ArrayGroupBy', 1);
        $data['cache_start_date'] = $records['cache_start_date'];
        $data['cache_end_date'] = $records['cache_end_date'];
        $data['date'] = $records['date'];
        $data['list'] = ArrayGroupBy::groupBy($records['list'], $group_by_fields, $group_by_value);
        return $data;


    }

    /**
     * 计费方式效果表
     * author: yjh
     */
    private function cTData()
    {
        $cache = Yii::app()->cache;
        $uid=Yii::app()->admin_user->uid;
        $ret = $cache->get('allData_'.$uid);
        $records = $this->foreachFilter(unserialize($ret));
        $group_by_fields = array(
            'bname' => function ($value) {
                return $value;
            },
            'charging_type' => function ($value) {
                return $value;
            }
        );
        $group_by_value = array(
            'charging_type',
            'bname',
            'uvs' => function ($data) {
                return array_sum(array_column($data, 'uvs'));
            },
            'fans' => function ($data) {
                return array_sum(array_column($data, 'fans'));
            },
            'money' => function ($data) {
                return array_sum(array_column($data, 'money'));
            },
            'fans_count' => function ($data) {
                return array_sum(array_column($data, 'fans_count'));
            },
            'estimate_money' => function ($data) {
                return array_sum(array_column($data, 'estimate_money'));
            },
            'estimate_count' => function ($data) {
                return array_sum(array_column($data, 'estimate_count'));
            },
        );
        Yii::import('application.extensions.ArrayGroupBy.ArrayGroupBy', 1);
        $data['cache_start_date'] = $records['cache_start_date'];
        $data['cache_end_date'] = $records['cache_end_date'];
        $data['date'] = $records['date'];
        $data['list'] = ArrayGroupBy::groupBy($records['list'], $group_by_fields, $group_by_value);
        return $data;

    }

    /**
     * 图文效果表
     * author: yjh
     */
    private function articleData()
    {
        $cache = Yii::app()->cache;
        $uid=Yii::app()->admin_user->uid;
        $ret = $cache->get('allData_'.$uid);
        $records = $this->foreachFilter(unserialize($ret));

        $group_by_fields = array(
            'article_code' => function ($value) {
                return $value;
            }
        );
        $group_by_value = array(
            'article_code',
            'article_type',
            'uvs' => function ($data) {
                return array_sum(array_column($data, 'uvs'));
            },
            'fans' => function ($data) {
                return array_sum(array_column($data, 'fans'));
            },
            'money' => function ($data) {
                return array_sum(array_column($data, 'money'));
            },
            'fans_count' => function ($data) {
                return array_sum(array_column($data, 'fans_count'));
            },
            'estimate_money' => function ($data) {
                return array_sum(array_column($data, 'estimate_money'));
            },
            'estimate_count' => function ($data) {
                return array_sum(array_column($data, 'estimate_count'));
            },
        );
        Yii::import('application.extensions.ArrayGroupBy.ArrayGroupBy', 1);
        $data['cache_start_date'] = $records['cache_start_date'];
        $data['cache_end_date'] = $records['cache_end_date'];
        $data['date'] = $records['date'];
        $data['list'] = ArrayGroupBy::groupBy($records['list'], $group_by_fields, $group_by_value);
        return $data;

    }

    /**
     * 获取效果整体表所有数据
     * author: yjh
     */
    private function getAllEffectData()
    {

        $cache = Yii::app()->cache;
        $uid=Yii::app()->admin_user->uid;
        $ret = $cache->get('allData_'.$uid);
        $records = unserialize($ret);
        $new_arr = array();
        $new_arr['cache_start_date'] = $records['cache_start_date'];
        $new_arr['cache_end_date'] = $records['cache_end_date'];
        $new_arr['date'] = $records['date'];
        $new_arr['list'] = array();
        $date_flag = 0;//没填写时间
        //过滤时间
        if ($this->get('start_online_date') || $this->get('end_online_date')) {
            if ($this->get('start_online_date') && $this->get('end_online_date')) {
                if ($this->get('start_online_date') <= $this->get('end_online_date')) {
                    $start_stat_date = strtotime($this->get('start_online_date'));
                    $end_stat_date = strtotime($this->get('end_online_date'));
                    $date_flag = 1;//起始，结束时间都有输入
                }
            } elseif ($this->get('start_online_date')) {
                $start_stat_date = strtotime($this->get('start_online_date'));
                $date_flag = 2;//只输入起始时间
            } elseif ($this->get('end_online_date')) {
                $end_stat_date = strtotime($this->get('end_online_date'));
                $date_flag = 3;//只输入结束时间
            }
        }

        $cs_flag = $tg_flag = $bs_flag = $chg_flag = $goods_flag = 0;
        //客服部
        if ($this->get('csid')) {
            $cs_flag = 1;
            $csid = $this->get('csid');
        }
        //商品
        if ($this->get('goods_id') != 0) {
            $goods_flag = 1;
            $goods_id = $this->get('goods_id');
        }
        //推广人员
        if ($this->get('user_id')) {
            $tg_flag = 1;
            $tgid = $this->get('user_id');
        }
        //业务类型
        if ($this->get('bsid') != '') {
            $bs_flag = 1;
            $bsid = $this->get('bsid');
        }
        //计费方式
        if ($this->get('chgId') != '') {
            $chg_flag = 1;
            $chgid = $this->get('chgId');
        }
        foreach ($records['list'] as $k => $r) {
            if ($date_flag == 1) {
                if ($r['stat_date'] < $start_stat_date || $r['stat_date'] > $end_stat_date) continue;
            } elseif ($date_flag == 2) {
                if ($r['stat_date'] < $start_stat_date) continue;
            } elseif ($date_flag == 3) {
                if ($r['stat_date'] > $end_stat_date) continue;
            }
            if ($this->get('partner_name')) {
                if (strpos($r['partner_name'], $this->get('partner_name')) === false) continue;
            }
            if ($this->get('channel_name')) {
                if (strpos($r['channel_name'], $this->get('channel_name')) === false) continue;
            }
            if ($this->get('wechat_id')) {
                if (stripos($r['wechat_id'], $this->get('wechat_id')) === false) continue;
            }

            if ($cs_flag === 1) {
                if ($r['customer_service_id'] != $csid) continue;
            }
            if ($goods_flag === 1) {
                if ($r['goods_id'] != $goods_id) continue;
            }
            if ($tg_flag === 1) {
                if ($r['tg_uid'] != $tgid) continue;
            }
            if ($bs_flag === 1) {
                if ($r['business_type'] != $bsid) continue;
            }
            if ($chg_flag === 1) {
                if ($r['charging_type'] != $chgid) continue;
            }
            $new_arr['list'][] = $r;
        }
        $total_fans = array_sum(array_column($new_arr['list'], 'fans'));
        $total_uv = array_sum(array_column($new_arr['list'], 'uvs'));
        $new_arr['total_money'] = array_sum(array_column($new_arr['list'], 'money'));
        $new_arr['total_estimate_money'] = array_sum(array_column($new_arr['list'], 'estimate_money'));
        $new_arr['total_estimate_count'] = array_sum(array_column($new_arr['list'], 'estimate_count'));
        $new_arr['total_fans_count'] = array_sum(array_column($new_arr['list'], 'fans_count'));
        $new_arr['total_ROI'] = $new_arr['total_money'] ? round($new_arr['total_estimate_money'] * 100 / $new_arr['total_money']) : 0;
        $new_arr['total_fans_cost'] = $new_arr['total_fans_count'] ? round($new_arr['total_money'] / $new_arr['total_fans_count'], 2) : 0;
        $new_arr['total_channel_transform'] = $new_arr['total_fans_count'] ? round($new_arr['total_estimate_count'] * 100 / $new_arr['total_fans_count'], 1) : 0;
        $new_arr['total_uv_transform'] = $total_fans ? round($total_uv * 100 / $total_fans, 1) : 0;
        $new_arr['total_article_transform'] = $total_uv ? round($new_arr['total_fans_count'] * 100 / $total_uv, 1) : 0;
        return $new_arr;
    }

    /**
     * 新.生成缓存
     * author: yjh
     */
    public function actionCache()
    {
        ini_set('memory_limit', -1);
        $mtime = explode(' ', microtime());
        $start = $mtime[1] + $mtime[0];
        $i = 0;

        if ($this->post('cache_start_date') && $this->post('cache_end_date')) {
            //清除session 重新缓存
            $cache = Yii::app()->cache;
            $uid=Yii::app()->admin_user->uid;
            $cache->delete('allData');
            $cache->delete('allData_'.$uid);
            //unset(Yii::app()->session['allData']);
            $page = array();
            $page['cache_start_date'] = $this->post('cache_start_date');
            $page['cache_end_date'] = $this->post('cache_end_date');
            $start_date = strtotime($this->post('cache_start_date'));
            $end_date = strtotime($this->post('cache_end_date'));
            $dx_pid = !isset(Yii::app()->params['basic']['dx_bid']) ? 8:intval(Yii::app()->params['basic']['dx_bid']);

            $page['date'] = $page['cache_start_date'] . "至" . $page['cache_end_date'];
            $param = " where a.stat_date between " . $start_date . " and " . $end_date ." and a.business_type!=".$dx_pid;
            //$param .= " and j.status = 0"; //加入独立订单

            //查看人员权限
            $result = $this->data_authority(1);
            if ($result != 0) {
                $param .= " and a.tg_uid in ($result) ";
            }
            //成本明细
            $statCostDetail = StatCostDetail::model()->getEffectTableData($param);
            $mtime = explode(' ', microtime());
            $round_1 = $mtime[1] + $mtime[0];
            $i++;

            $temp_normOrderInfo = $temp_fansCountInfo = $temp_channelData = $temp_fixedCost = array();
            //获取订单下单信息
            $condition = " addfan_date between " . $start_date . " and " . $end_date . "  group by addfan_date,weixin_id,customer_service_id";
            $normOrderInfo = PlaceNormOrderManage::model()->getEstimateInfoTypeThree($condition);
            $i++;
            if ($normOrderInfo) {
                foreach ($normOrderInfo as $value) {
                    $key = $value['addfan_date'] . "_" . $value['weixin_id'];
                    $temp_normOrderInfo[$key] = $value;
                }
                $normOrderInfo = $temp_normOrderInfo;
            }
            $mtime = explode(' ', microtime());
            $round_2 = $mtime[1] + $mtime[0];
            //进粉量 阅读量
            $condition = " addfan_date between " . $start_date . " and " . $end_date . "  group by addfan_date,weixin_id";
            $fansCountInfo = FansInputManage::model()->getFansTypeThree($condition);
            $i++;
            if ($fansCountInfo) {
                foreach ($fansCountInfo as $value) {
                    $key = $value['addfan_date'] . "_" . $value['weixin_id'];
                    $temp_fansCountInfo[$key] = $value;
                }
                $fansCountInfo = $temp_fansCountInfo;
            }
            $mtime = explode(' ', microtime());
            $round_3 = $mtime[1] + $mtime[0];

            $condition = " online_date between " . $start_date . " and " . $end_date . "  group by online_date,channel_id";
            $channelData = ChannelData::model()->getChannelData($condition);

            $i++;
            if ($channelData) {
                foreach ($channelData as $value) {
                    $key = $value['online_date'] . "_" . $value['channel_id'];
                    $temp_channelData[$key] = $value;
                }
                $channelData = $temp_channelData;
            }
            $mtime = explode(' ', microtime());
            $round_4 = $mtime[1] + $mtime[0];
            //获取修正成本
            $condition = " stat_date between " . $start_date . " and " . $end_date . "  group by stat_date,channel_id,weixin_id,business_type,charging_type,goods_id,customer_service_id";
            $fixCostInfo = FixedCost::model()->getFixedCost($condition);
            if ($fixCostInfo) {
                foreach ($fixCostInfo as $value) {
                    $key = $value['stat_date'] . "_" . $value['weixin_id'] . "_" . $value['channel_id'];
                    $temp_fixedCost[$key] = $value;
                }
                $fixCostInfo = $temp_fixedCost;
            }
            $mtime = explode(' ', microtime());
            $round_5 = $mtime[1] + $mtime[0];
            //数据处理
            $Tempdata = $temp_Info = array();

            foreach ($statCostDetail as $k => $r) {
                $Tempdata[$k] = $r;
                $key = $r['stat_date'] . "_" . $r['weixin_id'];
                if (!array_key_exists($key, $temp_Info)) {
                    $temp_Info[$key] = $k;
                }
                $key_1 = $r['stat_date'] . "_" . $r['channel_id'];
                $key_2 = $r['stat_date'] . "_" . $r['weixin_id'] . "_" . $r['channel_id'];
                $Tempdata[$k]['fixed_cost'] = array_key_exists($key_2, $fixCostInfo) ? $fixCostInfo[$key_2]['fixed_cost'] : 0;
                $Tempdata[$k]['money'] = round($Tempdata[$k]['money'] + $Tempdata[$k]['fixed_cost'], 2);
                if ($r['cstatus'] == 0) {
                    if (array_key_exists($key, $normOrderInfo)) {
                        $Tempdata[$k]['estimate_money'] = round($normOrderInfo[$key]['estimate_money'] * 0.01, 2);
                        $Tempdata[$k]['estimate_count'] = round($normOrderInfo[$key]['estimate_count'] * 0.01, 2);

                        unset($normOrderInfo[$key]);
                    } else {
                        $Tempdata[$k]['estimate_money'] = 0;
                        $Tempdata[$k]['estimate_count'] = 0;
                    }

                } elseif ($r['cstatus'] == 1) {
                    $Tempdata[$k]['estimate_money'] = 0;
                    $Tempdata[$k]['estimate_count'] = 0;
                }

                if (array_key_exists($key, $fansCountInfo)) {
                    $Tempdata[$k]['fans_count'] = $fansCountInfo[$key]['fans_count'];
                    unset($fansCountInfo[$key]);
                } else {
                    $Tempdata[$k]['fans_count'] = 0;

                }
                if ($r['business_type'] == 1) {
                    if (array_key_exists($key_1, $channelData)) {
                        $wcount = $channelData[$key_1]['wcount'];
                        $Tempdata[$k]['fans'] = $channelData[$key_1]['fan'] ? round($channelData[$key_1]['fan'] / $wcount, 2) : 0;
                        $Tempdata[$k]['uvs'] = $channelData[$key_1]['uv'] ? round($channelData[$key_1]['uv'] / $wcount, 2) : 0;
                        $Tempdata[$k]['article_code'] = $channelData[$key_1]['article_code'] ? $channelData[$key_1]['article_code'] : '';
                        $Tempdata[$k]['article_type'] = $channelData[$key_1]['article_type'] !== '' ? $channelData[$key_1]['article_type'] : '';
                    } else {
                        $Tempdata[$k]['fans'] = 0;
                        $Tempdata[$k]['uvs'] = 0;
                    }
                } else {
                    //非订阅号 无粉丝量
                    $Tempdata[$k]['uvs'] = $r['uv'];
                    $Tempdata[$k]['fans'] = 0;
                }
            }
            if ($normOrderInfo) {
                foreach ($normOrderInfo as $key => $value) {
                    if (array_key_exists($key, $fansCountInfo)) {
                        $fans_count = $fansCountInfo[$key]['fans_count'];
                        unset($fansCountInfo[$key]);
                    } else {
                        $fans_count = 0;
                    }

                    $keyInfo = explode('_', $key);
                    $temp_date = intval($keyInfo[0]) - 86400;
                    for ($d = $temp_date; $d >= ($temp_date - 86400 * 10); $d = $d - 86400) {
                        $key = $d . "_" . $keyInfo[1];
                        if (array_key_exists($key, $temp_Info)) {
                            $k = $temp_Info[$key];
                            $Tempdata[$k]['estimate_money'] += round($value['estimate_money'] * 0.01, 2);
                            $Tempdata[$k]['estimate_count'] += round($value['estimate_count'] * 0.01, 2);
                            $Tempdata[$k]['fans_count'] += $fans_count;
                            break;
                        }
                    }
                }
            }
            if ($fansCountInfo) {
                foreach ($fansCountInfo as $key => $value) {
                    $keyInfo = explode('_', $key);
                    $temp_date = intval($keyInfo[0]) - 86400;
                    for ($d = $temp_date; $d >= ($temp_date - 86400 * 10); $d = $d - 86400) {
                        $key = $d . "_" . $keyInfo[1];
                        if (array_key_exists($key, $temp_Info)) {
                            $k = $temp_Info[$key];
                            $Tempdata[$k]['fans_count'] += $value['fans_count'];
                            break;
                        }
                    }
                }
            }

            $page['list'] = $Tempdata;
            $uid=Yii::app()->admin_user->uid;
            $a = $cache->set('allData_'.$uid, serialize($page));
            $mtime = explode(' ', microtime());
            $end = $mtime[1] + $mtime[0];
            echo "缓存成功！";
            echo $a;
            echo "\n成本明细:" . ($round_1 - $start);
            echo "\n订单下单:" . ($round_2 - $round_1);
            echo "\n进粉量:" . ($round_3 - $round_2);
            echo "\n渠道数据:" . ($round_4 - $round_3);
            echo "\n修正成本:" . ($round_5 - $round_4);
            echo "\n数据整合:" . ($end - $round_5);
            echo "\n" . ($end - $start);
            echo "\n" . ($i);

            exit;
        }
        echo "noData";
        exit;
    }


    /**
     * 遍历数组过滤取值
     * author: yjh
     */
    private function foreachFilter($records)
    {
        $new_arr = array();
        $new_arr['cache_start_date'] = $records['cache_start_date'];
        $new_arr['cache_end_date'] = $records['cache_end_date'];
        $new_arr['date'] = $records['date'];
        $new_arr['list'] = array();
        $date_flag = 0;//没填写时间
        //过滤时间
        if ($this->get('start_online_date') || $this->get('end_online_date')) {
            if ($this->get('start_online_date') && $this->get('end_online_date')) {
                if ($this->get('start_online_date') <= $this->get('end_online_date')) {
                    $start_stat_date = strtotime($this->get('start_online_date'));
                    $end_stat_date = strtotime($this->get('end_online_date'));
                    $date_flag = 1;//起始，结束时间都有输入
                }
            } elseif ($this->get('start_online_date')) {
                $start_stat_date = strtotime($this->get('start_online_date'));
                $date_flag = 2;//只输入起始时间
            } elseif ($this->get('end_online_date')) {
                $end_stat_date = strtotime($this->get('end_online_date'));
                $date_flag = 3;//只输入结束时间
            }
        }

        $cs_flag = $tg_flag = $bs_flag = $chg_flag = $goods_flag = 0;
        //客服部
        if ($this->get('csid')) {
            $cs_flag = 1;
            $csid = $this->get('csid');
        }
        //商品
        if ($this->get('goods_id') != 0) {
            $goods_flag = 1;
            $goods_id = $this->get('goods_id');
        }
        //推广人员
        if ($this->get('user_id')) {
            $tg_flag = 1;
            $tgid = $this->get('user_id');
        }
        //业务类型
        if ($this->get('bsid') != '') {
            $bs_flag = 1;
            $bsid = $this->get('bsid');
        }
        //计费方式
        if ($this->get('chgId') != '') {
            $chg_flag = 1;
            $chgid = $this->get('chgId');
        }
        foreach ($records['list'] as $k => $r) {
            if ($date_flag == 1) {
                if ($r['stat_date'] < $start_stat_date || $r['stat_date'] > $end_stat_date) continue;
            } elseif ($date_flag == 2) {
                if ($r['stat_date'] < $start_stat_date) continue;
            } elseif ($date_flag == 3) {
                if ($r['stat_date'] > $end_stat_date) continue;
            }
            if ($this->get('partner_name')) {
                if (strpos($r['partner_name'], $this->get('partner_name')) === false) continue;
            }
            if ($this->get('channel_name')) {
                if (strpos($r['channel_name'], $this->get('channel_name')) === false) continue;
            }
            if ($this->get('wechat_id')) {
                if (stripos($r['wechat_id'], $this->get('wechat_id')) === false) continue;
            }
            if ($this->get('article_code') && strpos($r['article_code'], $this->get('article_code')) === false) continue;

            if ($cs_flag === 1) {
                if ($r['customer_service_id'] != $csid) continue;
            }
            if ($goods_flag === 1) {
                if ($r['goods_id'] != $goods_id) continue;
            }
            if ($tg_flag === 1) {
                if ($r['tg_uid'] != $tgid) continue;
            }
            if ($bs_flag === 1) {
                if ($r['business_type'] != $bsid) continue;
            }
            if ($chg_flag === 1) {
                if ($r['charging_type'] != $chgid) continue;
            }
            $new_arr['list'][] = $r;
        }
        return $new_arr;
    }

    /**
     * 导出整体表
     * author: yjh
     */
    public function actionExport()
    {
        $file_name = '整体表-' . date('Ymd', time());
        $headlist = array('上线日期', '合作商', '渠道名称', '微信号', '业务类型', '计费方式', '推广人员', '客服部', '商品', '投入金额', '预估发货金额', 'ROI', '进粉量', '预估发货量', '进粉成本', '渠道转化', '图文转化', 'uv转化');
        $data = $this->getAllEffectData();
        $row = array();
        $row[0] = array('-', '-', '-', '-', '-', '-', '-', '-', iconv('utf-8', 'gbk', '合计'), round($data['total_money'], 2), round($data['total_estimate_money'], 2), $data['total_ROI'] . '%', $data['total_fans_count'], round($data['total_estimate_count'], 2), $data['total_fans_cost'], $data['total_channel_transform'] . '%', $data['total_article_transform'] . '%', $data['total_uv_transform'] . '%');
        $data = $data['list'];
        $count = count($data);
        for ($i = 0; $i < $count; $i++) {
            $k = $i + 1;
            $row[$k] = array(
                date('Y-m-d', $data[$i]['stat_date']),
                $data[$i]['partner_name'],
                $data[$i]['channel_name'],
                $data[$i]['wechat_id'],
                $data[$i]['bname'],
                vars::get_field_str('charging_type', $data[$i]['charging_type']),
                $data[$i]['csname_true'],
                $data[$i]['cname'],
                $data[$i]['goods_name'],
                $data[$i]['money'],
                $data[$i]['estimate_money'],
                $data[$i]['money'] ? round($data[$i]['estimate_money'] * 100 / $data[$i]['money']) . "%" : "0%",
                $data[$i]['fans_count'],
                $data[$i]['estimate_count'],
                $data[$i]['fans_count'] ? round($data[$i]['money'] / $data[$i]['fans_count'], 2) : 0,
                $data[$i]['fans_count'] ? round($data[$i]['estimate_count'] / $data[$i]['fans_count'], 1) . "%" : "0%",
                $data[$i]['uvs'] ? round($data[$i]['fans_count'] * 100 / $data[$i]['uvs'], 2) . "%" : "0%",
                $data[$i]['fans'] ? round($data[$i]['uvs'] * 100 / $data[$i]['fans'], 2) . "%" : "0%"
            );
            foreach ($row[$k] as $key => $value) {
                $row[$k][$key] = iconv('utf-8', 'gbk', $value);
            }

        }
        helper::downloadCsv($headlist, $row, $file_name);

    }

}