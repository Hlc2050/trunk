<?php

/**
 * 微信小组处理
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/1
 * Time: 10:13
 */
class WeChatGroupController extends AdminController
{
    /**
     * 微信号小组列表
     * author: yjh
     */
    public function actionIndex()
    {
        //搜索
        $params['where'] = '';
        //$params['where'] .= " and(status!=2) ";
        if ($this->get('from') && $this->get('search_type') == 'keys' && $this->get('search_txt')) {
            $params['where'] = " and(a.wechat_group_name = '" . $this->get('search_txt')."') ";
        }
        if ($this->get('search_type') == 'keys' && $this->get('search_txt') ) { //微信号小组名称
            $params['where'] = " and(a.wechat_group_name like '%" . $this->get('search_txt') . "%') ";
        } else if ($this->get('search_type') == 'id' && $this->get('search_txt')) { //ID
            $params['where'] = " and(a.id=" . intval($this->get('search_txt')) . ") ";
        } else if ($this->get('search_type') == 'wechat_id' && $this->get('search_txt')) { //微信号ID
            $sql = "select a.wechat_group_id from wechat_relation as a LEFT JOIN wechat as b on b.id=a.wid where b.wechat_id like '%" . $this->get('search_txt') . "%'";
            $ret = Yii::app()->db->createCommand($sql)->queryAll();
            $wgroupArr = array_unique(array_column($ret, 'wechat_group_id'));
            $wgroupStr = implode(',', $wgroupArr);
            if(!$wgroupStr) $wgroupStr=0;
            $params['where'] = " and(a.id in (" . $wgroupStr . ")) ";
        }

        if ($this->get('bs_id') != '') $params['where'] .= " and(a.business_type = '" . $this->get('bs_id') . "') ";

        if ($this->get('user_id')) $params['where'] .= " and(a.operator_id='" . $this->get('user_id') . "') ";
        // 每个账号只展示本账号下的小组信息，上级可查看到下级
        $result = $this->data_authority(1);
        if ($result != 0) {
            $params['where'] .= " and(a.operator_id in ($result)) ";
        }
        $params['order'] = "  order by id desc    ";
        $params['join'] = "
		left join business_types as b on b.bid=a.business_type
		left join cservice as c on c.csno=a.operator_id
		";
        $params['select'] = " a.*,b.bname,c.csname_true as operator";

        $params['pagesize'] = Yii::app()->params['management']['pagesize'];
        $params['pagebar'] = 1;
        $params['smart_order'] = 1;
        $page['listdata'] = Dtable::model(WeChatGroup::model()->tableName())->listdata($params);
        $page['listdata']['uid'] = yii::app()->admin_user->uid;

        foreach ($page['listdata']['list'] as $key => $val) {
            $wechatList = $this->toArr(WeChatRelation::model()->findAll('wechat_group_id=:wid', array(':wid' => $val['id'])));
            $page['listdata']['list'][$key]['wechatList'] = WeChat::model()->getWeChatNameByIds($wechatList);//微信号
            $page['listdata']['list'][$key]['cname'] = vars::get_field_str('charging_type', $val['charging_type']);//微信号
            $page['listdata']['list'][$key]['update_time'] = date('Y-m-d', $val['update_time']);//更新时间
            $page['listdata']['list'][$key]['status'] = vars::get_field_str('weChatGroup_status', $val['status']);//状态
        }
        $page['listdata']['url'] = urlencode('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"]);
        $this->render('index', array('page' => $page));
    }

    /**
     * 微信号小组添加，表单处理
     *    订阅号：添加的微信号类型必须为订阅号的。
     * 微信号必须是推广的状态
     * 如果微信号在其他小组里上线了，仍可以添加
     * 非订阅号：添加的微信号类型必须为非订阅号的。
     * 微信号必须是推广状态
     * 如果微信号在其他小组里上线了，则不可以添加
     * author: yjh
     */
    public function actionAdd()
    {
        $page = array();
        //显示表单
        if (!$_POST) {
            $page['info']['wechat_group_name'] = $this->creatWeChatGroupName();
            $page['info']['id'] = '';
            $page['info']['user_id'] = '';
            $page['info']['promotion_staff_id'] = '';
            $page['info']['weChat_id'] = array();
            $page['info']['wechat_ids'] = '';
            $this->render('update', array('page' => $page));
            exit;
        }
        switch ($this->get('bid')) {
            //订阅号
            case 1:
                $this->wechatGroupAdd(1, 3);
                break;
            //特殊手赚
            case 0:
                $this->wechatGroupAdd(2, 4);
                break;
            //非订阅号
            default:
                $this->wechatGroupAdd($this->get('bid'), 0);
                break;
        }

    }

    /**
     * 添加微信号小组处理
     * @param $p_bid int 业务类型
     * @param $p_status int 状态
     * author: yjh
     */
    private function wechatGroupAdd($p_bid, $p_status)
    {
        //表单验证
        $info = new WeChatGroup();
        /*微信号ID*/
        $info->wechat_group_name = $this->post('name');
        if ($info->wechat_group_name == '') $this->msg(array('state' => 0, 'msgwords' => '未填微信号小组名称！'));

        $result = WeChatGroup::model()->count('wechat_group_name=:wechat_group_name', array(':wechat_group_name' => $info->wechat_group_name));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '此微信号小组已存在，请重新输入！'));

        $info->charging_type = $this->post('charging_type');
        if ($info->charging_type == '') $this->msg(array('state' => 0, 'msgwords' => '未选择计费方式！'));
        $weChatIds = implode(',', $this->post('weChat_list'));
        if ($weChatIds == '') $this->msg(array('state' => 0, 'msgwords' => '未选择微信号！'));

        $info->business_type = $p_bid;
        $info->operator_id = yii::app()->admin_user->uid;
        $info->status = $p_status;
        $info->update_time = time();
        $info->create_time = time();
        $dbresult = $info->save();
        $id = $info->primaryKey;
        foreach ($this->post('weChat_list') as $val) {
            $wInfo = new WeChatRelation();
            $wInfo->wid = $val;
            $wInfo->wechat_group_id = $id;
            $wInfo->create_time = time();
            $wInfo->save();
        }
        $msgarr = array('state' => 1, 'url' => $this->createUrl('weChatGroup/index') . '?p=' . $_GET['p'] . ''); //保存的话，跳转到之前的列表
        $logs = "添加了新的微信号小组：" . $info->wechat_group_name;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            //新增和修改之后的动作
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }


    /**
     * 自动生成微信号小组名称
     * author: yjh
     */
    private function creatWeChatGroupName()
    {
        //获取微信小组名转成拼音
        $cn2pinyin = new cn2pinyin();
        $usrName = $cn2pinyin->get(yii::app()->admin_user->uname_true);
        $result = $this->toArr(WeChatGroup::model()->find("wechat_group_name like '" . $usrName . "%' order by id desc"));

        //自动生成微信小组名称
        if (empty($result)) {
            $wechat_group_name = $usrName . "1";
        } else {
            $groupName = $result['wechat_group_name'];
            $groupNum = (int)ltrim($groupName, $usrName) + 1;
            $wechat_group_name = $usrName . $groupNum;
        }
        return $wechat_group_name;
    }

    /**
     * 微信号小组修改，表单处理
     * author: yjh
     */
    public function actionEdit()
    {
        $page = array();
        $id = $this->get('id');
        $info = WeChatGroup::model()->findByPk($id);
        if (!$info) $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));

        //显示表单
        if (!$_POST) {
            $charging_type = $info->charging_type;
            $bid = $info->business_type;
            $page['info'] = $this->toArr(WeChatGroup::model()->find("id=:id", array(":id" => $id)));
            $page['info']['wechat_id'] = WeChatRelation::model()->getWeChatIds($id);//explode(',', $page['info']['wechat_ids']);
            $page['info']['wechat_ids'] = implode(',', $page['info']['wechat_id']);
            $page['info']['promotion_staff_id'] = '';
            //微信号显示没用过的 不管上没上线
            //查看人员权限
            $ret = $this->data_authority();
            $condition = '';
            if ($ret != 0) {
                $condition = " and(promotion_staff_id in ($ret)) ";
            }
            $page['weChatList'] = $this->toArr(WeChat::model()->findAll("status = 0 and charging_type=" . $charging_type . " and business_type=" . $bid . " and id not in (" . $page['info']['wechat_ids'] . ")" .$condition ));//只显示推广的
            $this->render('update', array('page' => $page));
            exit;
        }

        //表单验证
        /*微信号ID*/
        $info->wechat_group_name = $this->post('name');
        $result = WeChatGroup::model()->count('wechat_group_name=:wechat_group_name and id!=:id', array(':wechat_group_name' => $info->wechat_group_name, 'id' => $id));
        if ($info->wechat_group_name == '') $this->msg(array('state' => 0, 'msgwords' => '未填微信号小组名称！'));
        if ($result > 0) $this->msg(array('state' => 0, 'msgwords' => '此微信号小组已存在，请重新输入！'));
        /*商品ID*/
        if ($this->post('weChat_list') == '') $this->msg(array('state' => 0, 'msgwords' => '未选择微信号！'));
//        $info->operator_id = yii::app()->admin_user->uid;
        $info->update_time = time();
        $dbresult = $info->save();

        $id = $info->primaryKey;
        WeChatRelation::model()->deleteAll('wechat_group_id=:wechat_group_id', array(':wechat_group_id' => $id));
        foreach ($this->post('weChat_list') as $val) {
            $wInfo = new WeChatRelation();
            $wInfo->wid = $val;
            $wInfo->wechat_group_id = $id;
            $wInfo->create_time = time();
            $wInfo->save();
        }
        $msgarr = array('state' => 1, 'url' => $this->get('backurl')); //保存的话，跳转到之前的列表
        $logs = "修改了微信号小组：" . $info->wechat_group_name;
        if ($dbresult === false) {
            //错误返回
            $this->msg(array('state' => 0));
        } else {
            //新增和修改之后的动作
            $this->logs($logs);
            //成功跳转提示
            $this->msg($msgarr);
        }
    }

    /**
     * 删除微信号小组
     * author: yjh
     */
    public function actionDelete()
    {
        if ($this->get('id') == '') $this->msg(array('state' => 0, 'msgwords' => '未传入数据'));
        $id = $this->get('id');
        $info = WeChatGroup::model()->findByPk($id);
        if (!$info) $this->msg(array('state' => 0, 'msgwords' => '数据不存在'));
        if ($info->status == 1) $this->msg(array('state' => 0, 'msgwords' => '已上线微信小组不能删除'));
        WeChatRelation::model()->deleteAll('wechat_group_id=:wechat_group_id', array(':wechat_group_id' => $id));
        $info->delete();
        $logs = "删除了微信号小组：" . $info->wechat_group_name;
        $this->logs($logs);
        $this->msg(array('state' => 1, 'msgwords' => '删除微信号小组【' . $info->wechat_group_name . '】成功！'));
    }

    /**
     * 获取适合添加的微信号Id
     * author: yjh
     */
    public function actionGetSuitableWechatIds()
    {
        if (Yii::app()->request->isAjaxRequest) {
            $params['where'] = '';
            if ($this->post('charging_type') == '') {
                echo '';
                exit;
            }

            $params['where'] .= 'status=0 and charging_type=' . $this->post('charging_type');
            if ($this->post('bid') == 0) $params['where'] .= ' and business_type=2';
            else $params['where'] .= ' and business_type=' . $this->post('bid');

            //普通非订阅号小组、非电销不能添加已经添加过的微信号（备用，上线）除了订阅号1都是非订阅号
            if ($this->post('bid') != 1 && $this->post('bid') != yii::app()->params['basic']['dx_bid']) {
                $weChatGroupArr = $onlineWeChatArr = array();
                $onlineWechatGroup = $this->toArr(WeChatGroup::model()->findAll("status in (0,1) and business_type=" . $this->post('bid')));
                if ($onlineWechatGroup) {
                    foreach ($onlineWechatGroup as $value) {
                        $weChatGroupArr[] = $value['id'];
                    }
                    $weChatGroupStr = implode(',', $weChatGroupArr);
                    //2.找出这些小组用的所有微信号
                    $onlineWechat = $this->toArr(WeChatRelation::model()->findAll(" wechat_group_id in (" . $weChatGroupStr . ")"));
                    foreach ($onlineWechat as $value) {
                        $onlineWeChatArr[] = $value['wid'];
                    }
                    $onlineWeChatStr = implode(',', $onlineWeChatArr);
                    $params['where'] .= " and id not in (" . $onlineWeChatStr . ") ";
                }
            }
            //查看人员权限
            $ret = $this->data_authority();
            if ($ret != 0) {
                $params['where'] .= " and(promotion_staff_id in ($ret)) ";
            }


            $weChatIds = $this->toArr(WeChat::model()->findAll($params['where']));
            $content = '';
            $i = 0;
            foreach ($weChatIds as $key => $val) {
                $content .= $i == 0 ? "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" : "";
                $content .= " <input onclick=\"weChatIdsSelect(this)\" style='width: 20px' type='checkbox'  value='" . $val['id'] . "' attr-val='" . $val['wechat_id'] . "'/><span  id='" . $val['id'] . "'>" . $val['wechat_id'] . "</span>";
                $i++;
                if ($i == 5) {
                    $content .= "<br/>";
                    $i = 0;
                }
            }
            echo $content;

        }
    }

    /**
     * 编辑搜索
     * author: yjh
     */
    public function actionSearchHandler()
    {
        if (Yii::app()->request->isAjaxRequest) {
            //搜索
            $params['where'] = '';
            $params['params'] = array();
            //选择状态为推广的微信号
            $params['where'] .= 'status=0 and charging_type=' . $this->get('charging_type');
            if ($this->get('bid') == 0) $params['where'] .= ' and business_type=2';
            else $params['where'] .= ' and business_type=' . intval($this->get('bid'));

            if ($this->get('wid') == 0 && $this->get('bid') != 1 && $this->get('bid') != 0) {
                $weChatGroupArr = $onlineWeChatArr = array();
                $onlineWechatGroup = $this->toArr(WeChatGroup::model()->findAll("status in (0,1) and business_type=" . $this->get('bid')));
                foreach ($onlineWechatGroup as $value) {
                    $weChatGroupArr[] = $value['id'];
                }
                if (!empty($weChatGroupArr)) {
                    $weChatGroupStr = implode(',', $weChatGroupArr);
                    //2.找出这些小组用的所有微信号
                    $onlineWechat = $this->toArr(WeChatRelation::model()->findAll(" wechat_group_id in (" . $weChatGroupStr . ")"));
                    foreach ($onlineWechat as $value) {
                        $onlineWeChatArr[] = $value['wid'];
                    }
                    $onlineWeChatStr = implode(',', $onlineWeChatArr);
                    $params['where'] .= " and id not in (" . $onlineWeChatStr . ") ";
                }
            }

            if ($this->get('ids') != '') $params['where'] .= " and id not in (" . $this->get('ids') . ") ";
            if ($this->get('gid') != '') {
                $goodsArr = array();
                $result = $this->toArr(Goods::model()->findAll("goods_name like '%" . $this->get('gid') . "%'"));
                if (!empty($result)) {
                    foreach ($result as $k => $v) {
                        $goodsArr[] = intval($v['id']);
                    }
                    $goodsStr = implode(',', $goodsArr);
                    $params['where'] .= " and goods_id in (" . $goodsStr . ")";
                } else $params['where'] .= " and goods_id = 0 ";
            }
            if ($this->get('catid') != '') {
                $characterArr = array();
                $result = $this->toArr(Linkage::model()->findAll("linkage_name like '%" . $this->get('catid') . "%'"));
                if (!empty($result)) {
                    foreach ($result as $k => $v) {
                        $characterArr[] = intval($v['linkage_id']);
                    }
                    $characterStr = implode(',', $characterArr);
                    $params['where'] .= " and character_id in (" . $characterStr . ") ";
                } else {
                    $params['where'] .= " and character_id = 0 ";
                }
            }
            if ($this->get('user_id') != '') {
                $params['where'] .= " and(promotion_staff_id =".$this->get('user_id').") ";
            } else {
                //查看人员权限
                $ret = $this->data_authority();
                if ($ret != 0) {
                    $params['where'] .= " and(promotion_staff_id in ($ret)) ";
                }
            }
            if ($this->get('csid') != '') {
                $params['where'] .= " and customer_service_id=:csid ";
                $params['params'][':csid'] = $this->get('csid');
            }
            if ($this->get('wechat_id') != '') {
                $params['where'] .= " and wechat_id like '%" . $this->get('wechat_id') . "%' ";
            }
            $weChatIds = $this->toArr(WeChat::model()->findAll($params['where'], $params['params']));
            $content = '';
            $i = 0;
            foreach ($weChatIds as $key => $val) {
                $content .= $i == 0 ? "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" : "";
                $content .= " <input onclick=\"weChatIdsSelect(this)\" style='width: 20px' type='checkbox'  value='" . $val['id'] . "' attr-val='" . $val['wechat_id'] . "'/><span  id='" . $val['id'] . "'>" . $val['wechat_id'] . "</span>";
                $i++;
                if ($i == 5) {
                    $content .= "<br/>";
                    $i = 0;
                }
            }
            echo $content;
        }
    }


}