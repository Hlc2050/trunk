<?php
/**
 * 电销渠道效果控制器
 * User: Administrator
 * Date: 2017/11/2
 * Time: 9:16
 */
class OrderGoodsEffectController extends AdminController
{

    public function actionIndex()
    {
        $group_id = $this->get('group_id') ? $this->get('group_id') : 0;
        switch ($group_id) {
            case 0:
                $allData = $this->getEffectTable();
                break;
            case 1:
                $allData = $this->getEffectChart();
                break;
            case 2:
                $allData = $this->getCompareChart();
                break;
        }
        $allData['params_groups'] = vars::$fields['goods_effect'];
        $this->render('index', array('allData' => $allData));
    }

    /**
     * 导出下单商品统计表
     */
    public function actionExport()
    {
        $headlist = array('日期', '下单商品', '进线量', '发货量', '发货金额', '订单转化');
        $exportData = $this->getEffectTable(1);
        // 统计行
        $totalrow = array('-', '合计', $exportData['total_in_count'], $exportData['total_out_count'], $exportData['total_delivery_money'], $exportData['total_order_transform'] . '%');
        foreach ($totalrow as $k => $v) {
            $totalrow[$k] = iconv('utf-8', 'gbk', $v);
        }
        $export_row = array();
        $export_row[0] = $totalrow;
        $i = 1;
        foreach ($exportData['list'] as $key => $value) {
            $order_transform = $value['in_count'] ? round($value['out_count'] * 100 / $value['in_count'], 2) : 0;
            $row = array(date('Y-m-d', $value['stat_date']), $value['package_name'], $value['in_count'], $value['out_count'], $value['delivery_money'], $order_transform . '%');
            foreach ($row as $k => $v) {
                $row[$k] = iconv('utf-8', 'gbk', $v);
            }
            $export_row[$i] = $row;
            $i++;
        }
        helper::downloadCsv($headlist, $export_row, '下单商品统计表-' . date('Ymd', time()));
    }

    /**
     * 根据商品组获取商品数据
     */
    public function actionGetGoodsByGroup()
    {
        if (!$this->get('package_group_id')) {
            echo CHtml::tag('option', array('value' => ''), CHtml::encode('先选择商品组'), true);
            die;
        }
        $package = PackageRelation::model()->getPackageList($this->get('package_group_id'));
        if (!$package) echo CHtml::tag('option', array('value' => ''), CHtml::encode('没有商品'), true);
        foreach ($package as $key => $v) {
            echo CHtml::tag('option', array('value' => $v['id']), CHtml::encode($v['name']), true);
        }

    }

    /**
     * 获取统计表数据
     * @param $data_typ int 0显示,1导出
     */
    private function getEffectTable($data_type = 0)
    {
        $params['where'] = ' ';
        $start_date = $end_date = '';
        if ($this->get('start_online_date') != '' || $this->get('end_online_date') != '') {
            if ($this->get('start_online_date') != '' && $this->get('end_online_date') != '') {
                if ($this->get('start_online_date') <= $this->get('end_online_date')) {
                    $start_date = $this->get('start_online_date');
                    $end_date = $this->get('end_online_date');
                    $params['where'] .= ' and ( a.stat_date between ' . strtotime($start_date) . ' and ' . strtotime($end_date) . ')';
                }
            } elseif ($this->get('start_online_date') != '') {
                $start_date = $this->get('start_online_date');
                $params['where'] .= ' and a.stat_date >= ' . $start_date;
            } elseif ($this->get('end_online_date') != '') {
                $end_date = $this->get('end_online_date');
                $params['where'] .= ' and a.stat_date <= ' . $end_date;
            }
        } else {
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-d');
            $params['where'] .= ' and ( a.stat_date between ' . strtotime($start_date) . ' and ' . strtotime($end_date) . ')';
        }
        if ($this->get('package')) $params['where'] .= ' and (p.name like \'%' . $this->get('package') . '%\')';
        if ($this->get('csid')) $params['where'] .= ' and (a.customer_service_id=' . $this->get('csid') . ')';
        $params['order'] = "  order by a.stat_date desc ";
        $params['join'] = " left join package_manage as p on a.package_id = p.id";
        $params['select'] = "a.package_id,a.stat_date,SUM(a.delivery_money) as delivery_money,SUM(a.out_count) as out_count,SUM(a.in_count) as in_count,p.name as package_name";
        $params['group'] = " group by a.stat_date,a.package_id  ";
        $listDate = array();
        if ($data_type == 0) {
            $params['pagesize'] = Yii::app()->params['management']['pagesize'];
            $params['pagebar'] = 1;
            $params['smart_order'] = 1;
            $listDate = Dtable::model(OrdersSortByPkg::model()->tablename())->listData($params);
        } else {
            $sql = 'select ' . $params['select'] . ' from orders_sort_by_pkg as a  ' . $params['join'] . ' where 1' . $params['where'] . $params['group'] . $params['order'];
            $listDate['list'] = Yii::app()->db->createCommand($sql)->queryAll();
        }
        $total = OrdersSortByPkg::model()->getPackageTotal($params['join'], $params['where']);
        $listDate['start_online_date'] = $start_date;
        $listDate['end_online_date'] = $end_date;
        $listDate['total_delivery_money'] = round($total[0]['delivery_money'], 2);
        $listDate['total_out_count'] = empty($total[0]['out_count']) ? 0 : $total[0]['out_count'];
        $listDate['total_in_count'] = empty($total[0]['in_count']) ? 0 : $total[0]['in_count'];
        $listDate['total_order_transform'] = $listDate['total_in_count'] ? round($listDate['total_out_count'] * 100 / $listDate['total_in_count'], 2) : 0;
        return $listDate;
    }

    /**
     * 下单商品模糊搜索
     * author: hlc
     */
    public function actionSearchPackage()
    {
        //下单商品模糊查询
        if (isset($_GET['jsoncallback'])) {
            $tet = $this->get('search_txt');
            if ($tet != null) {
                $data['list'] = $this->toArr(PackageManage::model()->findAll('name like"' . '%' . $this->get('search_txt') . '%' . '"'));
                $this->msg(array('state' => 1, 'data' => $data, 'type' => 'jsonp'));
            } else {
                $data['list'] = null;
            }
        }
    }

    /**
     * 获取商品统计图数据
     * 日期必选 日期未选显示空白 商品或者客服部未选展示空白
     * 选择商品组，下单商品 ，未选择客服部  展示该商品在各个客服部的情况
     * 选择客服部，未选择商品组，下单商品  展示该客服部各个商品的情况
     * @return array
     * author: yjh
     */
    private function getEffectChart()
    {
        $page = array();
        if ($this->get('start_date') || $this->get('end_date')) {
            if ($this->get('start_date') && $this->get('end_date')) {
                $start_date = $this->get('start_date');
                $end_date = $this->get('end_date');
            } elseif ($this->get('start_date')) {
                $start_date = $this->get('start_date');
                $end_date = date('Y-m-d', strtotime($start_date) + 6 * 24 * 60 * 60);
            } else {
                $end_date = $this->get('end_date');
                $start_date = date('Y-m-d', strtotime($end_date) - 6 * 24 * 60 * 60);
            }
        } else {
            $start_date = date('Y-m-1');
            $end_date = date('Y-m-d');
        }
        $page['start_date'] = $start_date;
        $page['end_date'] = $end_date;
        $condition = ' stat_date between ' . strtotime($start_date) . ' and ' . strtotime($end_date);

        if ($this->get('package_name') && $this->get('csid')) {
            $this->msg(array('state' => 0, 'msgwords' => '下单商品和客服部只能选一个'));
        }

        //通过下单商品获取客服部数据
        if ($this->get('package_name')) {
            $package_name = $this->get('package_name');
            $ret = PackageManage::model()->find("name='" . $package_name . "'");
            $package_id = $ret['id'];
            if ($package_id == null) {
            } else {
                $condition .= ' and  package_id = ' . $package_id;
                $page['list'] = array();
                $package_info = OrdersSortByPkg::model()->getCsInfoByPkgId($condition);
                if ($package_info == false) {
                    $this->msg(array('state' => 0, 'msgwords' => '没有该下单商品'));
                } else {
                    foreach ($package_info as $key => $value) {
                        $page['list'][$key] = $value;
                        $page['list'][$key]['order_transform'] = $value['in_count'] ? round($value['out_count'] * 100 / $value['in_count'], 2) : 0;
                    }
                    $page['type'] = 1;
                    $page['text'] = $package_name;
                }
            }
        }
        //通过客服部获取下单商品数据
        if ($this->get('csid')) {
            $csid = $this->get('csid');
            $ret = CustomerServiceManage::model()->find('id="' . $csid . '"');
            $condition .= ' and  customer_service_id = ' . $csid;
            $page['list'] = array();
            $package_info = OrdersSortByPkg::model()->getPkgInfoByCsId($condition);
            foreach ($package_info as $key => $value) {
                $page['list'][$key] = $value;
                $page['list'][$key]['order_transform'] = $value['in_count'] ? round($value['out_count'] * 100 / $value['in_count'], 2) : 0;
            }
            $page['type'] = 2;
            $page['text'] = $ret['cname'];
        }

        return $page;

    }

    /**
     *  获取商品对比图数据
     */
    private function getCompareChart()
    {
        $condition = '1 ';
        $package = array();

        if (!empty($this->get('package_id'))) {
            $package_ids = $this->get('package_id');
            $condition .= ' and  package_id in (' . implode(',', $package_ids) . ')';
            if ($this->get('start_online_date') || $this->get('end_online_date')) {
                if ($this->get('start_online_date') && $this->get('end_online_date')) {
                    $condition .= ' and ( stat_date between ' . strtotime($this->get('start_online_date')) . ' and ' . strtotime($this->get('end_online_date')) . ' )';
                } elseif ($this->get('start_online_date')) {
                    $condition .= ' and ( stat_date >= ' . strtotime($this->get('start_online_date')) . ' )';
                } elseif ($this->get('end_online_date')) {
                    $condition .= ' and ( stat_date <= ' . strtotime($this->get('end_online_date')) . ' )';
                }
            }

            $sql = 'select SUM(out_count) as out_count,SUM(in_count) as in_count,package_id from orders_sort_by_pkg  where ' . $condition . ' group by package_id order by package_id asc';
            $package_orders = Yii::app()->db->createCommand($sql)->queryAll();
            $package_orders = array_combine(array_column($package_orders, 'package_id'), $package_orders);
            $sql = 'select p.id,p.name from package_manage as p where p.id in (' . implode(',', $package_ids) . ')';
            $packages = Yii::app()->db->createCommand($sql)->queryAll();
            $packages = array_combine(array_column($packages, 'id'), $packages);
            foreach ($package_ids as $value) {
                if (!array_key_exists($value, $package_orders)) {
                    $package_orders[$value] = array('out_count' => 0, 'in_count' => 0, 'package_id' => $value);
                }
                $package_orders[$value]['package_name'] = array_key_exists($value, $packages) ? $packages[$value]['name'] : '';
            }
            $package['list'] = $package_orders;
            $package['ids'] = array_keys($package_orders);
        } else {
            if ($this->get('start_online_date') == null && $this->get('end_online_date') == null) {
                $start_date = date('Y-m-01', strtotime('now'));
                $end_date = date('Y-m-d', strtotime('now'));
                $condition .= ' and ( stat_date between ' . strtotime($start_date) . ' and ' . strtotime($end_date) . ')';
            }
            $package['start_online_date'] = $start_date;
            $package['end_online_date'] = $end_date;
        }

        return $package;
    }

    /**
     *  根据分组获取商品数据
     */
    public function actionGetGoodsCheckbox()
    {

        if ($this->get('package_group_id') == '') {
            $packages = PackageManage::model()->findAll();
        } else {
            $packages = PackageRelation::model()->getPackageList($this->get('package_group_id'));
        }
        foreach ($packages as $key => $val) {
            if (in_array($val['id'], $this->get('package_id'))) continue;
            echo CHtml::tag('input', array('type' => 'checkbox', 'name' => 'goods_check', 'value' => $val['id'], 'style' => 'margin-left:10px;', 'onclick' => 'goods_checked(this)'),
                '<span id="span_' . $val['id'] . '">' . CHtml::encode($val['name']) . '</span>', true);
        }
    }
}