<?php

/**
 *套餐管理页面
 * author: hlc
 */
class PackageManageController extends AdminController
{
    public function actionIndex()
    {
        $page = $this->getPackageList();
        $this->render('index', array('page' => $page));
    }

    /**
     * 获取列表
     * author: hlc
     */
    private function getPackageList()
    {
        $params['where'] = $this->getCondition();
        $params['order'] = ' order by a.id desc ';
        $params['select'] = ' a.*,b.package_id,b.price,c.name as package_name';
        $params['join'] = " left join package_relation as b on b.package_group_id=a.id
                          left join package_manage as c on c.id=b.package_id
        ";
        $params['pagebar'] = 1;
        $params['pagesize'] = Yii::app()->params['management']['pagesize'];

        $page['listdata'] = Dtable::model(PackageGroupManage::model()->tableName())->listdata($params);
        $page['listdata']['url'] = urlencode('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"]);

        return $page;
    }

    /**
     * 搜索条件
     * author: hlc
     */
    private function getCondition()
    {
        $condition = '';

        if ($this->get('search_type') == 'group_name' && $this->get('search_txt')) {
            $condition .= " and(a.group_name like '%" . $this->get('search_txt') . "%') ";
        } else if ($this->get('search_type') == 'package_name' && $this->get('search_txt')) {
            $string = '';

            $data = PackageManage::model()->findAll("name like '%" . $this->get('search_txt') . "%'");
            if (empty($data)) $this->msg(array('state' => 0, 'msgwords' => '下单商品不存在'));

            //查询所有名字类似的商品
            $sql = "select a.package_group_id from package_relation as a left join package_manage as b on b.id=a.package_id where a.package_id=" . $data[0]['id'];
            $ret = Yii::app()->db->createCommand($sql)->queryAll();

            foreach ($ret as $value) {
                $string .= $value['package_group_id'] . ',';
            }
            $string = trim($string, ',');

            $condition .= " and(a.id in (" . $string . ")) ";
        }

        return $condition;
    }

    /**
     *下单商品
     * author: hlc
     */
    public function actionPackageManage()
    {
        $params['where'] = '';
        $params['order'] = ' order by id desc';
        $params['pagebar'] = 1;
        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $page['listdata'] = Dtable::model(PackageManage::model()->tableName())->listdata($params);

        $this->render('packageManage', array('page' => $page));
    }

    /**
     *添加商品组
     * author: hlc
     */
    public function actionAddPackageGroup()
    {
        if ($_POST) {
            $group_name = $this->get('group_name');

            $ret = PackageGroupManage::model()->find('group_name="' . $group_name . '"');
            if ($ret) $this->msg(array('state' => 0, 'msgwords' => '商品组已存在'));

            $info = new PackageGroupManage();
            $info->group_name = $group_name;
            $info->save();

            $this->logs("添加商品组完成");
            $this->msg(array('state' => 1, 'msgwords' => '提交成功', 'jscode' => "<script>setTimeout(function(){artDialog.close();}, 500)</script>"));
        } else {
            $this->render('addPackageGroup');
        }
    }

    /**
     *修改下单商品
     * author: hlc
     */
    public function actionUpdatePackage()
    {
        $id = $this->get('id');
        if ($_POST) {
            $new_name = $this->get('new_name');

            $result = PackageManage::model()->find('name="' . $new_name . '"');
            if ($result) $this->msg(array('state' => 0, 'msgwords' => '下单商品已存在'));

            $ret = PackageManage::model()->findByPk($id);
            $ret->name = $new_name;
            $ret->save();

            $this->logs("修改下单商品完成");
            $this->msg(array('state' => 1, 'msgwords' => '修改下单商品成功', 'jscode' => "<script>setTimeout(function(){artDialog.close();}, 500)</script>"));
        } else {
            $this->render('updatePackage', array('id' => $id));
        }
    }

    /**
     *删除下单商品
     * author: hlc
     */
    public function actionDelPackage()
    {
        $id = $this->get('id');

        $package_id = OrderPackageRelation::model()->find('package_id="' . $id . '"');
        if ($package_id) $this->msg(array('state' => 0, 'msgwords' => '下单商品正在使用,不能删除'));

        PackageManage::model()->deleteByPk($id);

        $this->logs("删除下单商品完成");
        $this->msg(array('state' => 1, 'msgwords' => '删除下单商品成功', 'url' => $this->createUrl('PackageManage/PackageManage')));
    }

    /**
     *删除商品组
     * author: hlc
     */
    public function actionDelPackageGroup()
    {
        $id = $this->get('id');

        //商品组不在使用
        $package_group_id = OrderTemplete::model()->find('package_gid="' . $id . '"');
        if ($package_group_id) $this->msg(array('state' => 0, 'msgwords' => '商品组正在使用,不能删除'));

        PackageGroupManage::model()->deleteByPk($id);
        PackageRelation::model()->deleteAll('package_group_id ="' . $id . '"');

        $this->logs("删除商品组完成");
        $this->msg(array('state' => 1, 'msgwords' => '删除商品组成功','url'=>$this->createUrl('PackageManage/index')));
    }

    /**
     *下单商品管理
     * author: hlc
     */
    public function actionAddPackage()
    {
        if ($_POST) {
            $temp = array();

            $str = $this->get('package_list');
            $ret = PackageManage::model()->getPackageNameList();
            $arr = array_unique(explode("\r\n", $str));

            foreach ($arr as $val) {
                //导入商品重复
                if(in_array($val,$temp)) continue;
                //数据库存在该商品
                if(in_array($val,$ret)) continue;

                $temp[] = $val;

                $info = new PackageManage();
                $info->name = $val;
                $info->save();
            }

            $this->logs("'添加下单商品完成");
            $this->msg(array('state' => 1, 'msgwords' => '添加下单商品成功', 'url' => 'packageManage/index'));
        } else {
            $this->render('addPackage');
        }
    }

    /**
     *修改商品组
     * author: hlc
     */
    public function actionUpdatePackGroup()
    {
        if (!$_POST) {
            //商品组id
            $id = $this->get('id');
            $ret = PackageGroupManage::model()->findByPk($id);
            $this->render('updatePackageGroup', array('group_name' => $ret['group_name']));
        } else {
            $group_name = $this->get('group_name');

            //商品组不存在，可修改
            $ret = PackageGroupManage::model()->find('group_name="' . $group_name . '"');
            if ($ret) $this->msg(array('state' => 0, 'msgwords' => '商品组名字已存在'));

            $ret->group_name = $group_name;
            $ret->save();
            $this->logs("修改商品组名完成");
            $this->msg(array('state' => 1, 'msgwords' => '修改商品组名成功', 'jscode' => "<script>setTimeout(function(){artDialog.close();}, 500)</script>"));
        }
    }

    /**
     *下单商品模糊查询
     * author: hlc
     */
    public function actionGetPackage(){
        if (isset($_GET['jsoncallback'])) {
            $data['list'] = $this->toArr(PackageManage::model()->findAll('name like"' . '%' . $this->get('search_txt') . '%' . '"'));
            $this->msg(array('state' => 1, 'data' => $data, 'type' => 'jsonp'));
        }
    }

    /**
     *下单商品
     * author: hlc
     */
    public function actionPackageGroupManage()
    {
        $package_group_id = $this->get('group_id');
        if(!$package_group_id) $this->msg(array('state' => 0, 'msgwords' => '无该下单商品组别', 'url' => 'index'));

        if (!$_POST) {
            $sql = 'select a.*,b.name from package_relation as a left join package_manage as b on a.package_id=b.id  where a.package_group_id=' . $package_group_id;
            $page = Yii::app()->db->createCommand($sql)->queryAll();
            $this->render('packageGroupManage', array('page' => $page, 'package_group_id' => $package_group_id));
        } else {
            //总行数
            $rowNum =count(array_filter($_POST['package_id']));
            //删除原表的所有值
            PackageRelation::model()->deleteAll('package_group_id=' . $package_group_id);

            for($i=0;$i<$rowNum;$i++){
                $info = new PackageRelation();
                $info->package_group_id = $package_group_id;
                $info->package_id = trim($_POST['package_id'][$i]);
                $info->price = trim($_POST['price'][$i]);
                $info->save();
            }

            $this->logs("下单商品添加完成");
            $this->msg(array('state' => 1, 'msgwords' => '下单商品添加成功', 'url' => 'index'));
        }
    }
}