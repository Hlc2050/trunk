<?php

/**
 * 黑名单管理控制器
 * User: hlc
 * Date: 2017/11/2
 * Time: 10:09
 */
class BlackListController extends AdminController
{
    public function actionIndex()
    {
        $page = array();
        $groupId = $this->get('group_id');
        $groupId = !empty($groupId) ? $groupId : 0;
        switch ($groupId) {
            case 0:  //ip黑名单页面
                $page = $this->getIpBlackList();
                break;
            case 1://phone黑名单页面
                $page = $this->getPhoneBlackList();
                break;
            default:
                break;
        }

        //导航栏
        $page['params_groups'] = vars::$fields['blacklist_tables'];
        $this->render('index', array('page' => $page, 'url' => $page['listdata']['url']));
    }

    /**
     * 批量增加ip
     * author: hlc
     */
    public function actionAddIp()
    {
        if (!$_POST) {
            $this->render('addIpBlackList');
        } else {
            $arr = $temp = array();
            $i = 0;
            $blackIPList = BlackListIp::model()->getIpBlackList();

            foreach ($_POST['ipblacklist'] as $k => $ip) {
                //判断ip是否符合正则
                if (!$this->verify_ip($ip)) continue;
                //数据库查找是否存在重复数据
                if (in_array($ip, $blackIPList)) continue;
                //判断数组是否存在该ip
                if (in_array($ip, $temp['ip'])) continue;
                $temp['ip'][] = $ip;
                $arr[$i]['ip'] = $ip;
                $arr[$i]['remark'] = $_POST['ipremark'][$k];
                $i += 1;
            }

            foreach ($arr as $v) {
                $info = new BlackListIp();
                $info->ip_adress = $v['ip'];
                $info->remark = $v['remark'];
                $info->save();
            }

            $this->logs("批量增加ip完成");
            $this->msg(array('state' => 1, 'msgwords' => '批量增加ip成功'));
        }
    }

    /**
     * 批量添加手机号
     * author: hlc
     */
    public function actionAddPhone()
    {
        if (!$_POST) {
            //跳转到phone增加页面
            $this->render('addPhoneBlackList');
        } else {
            $arr = $temp = array();
            $i = 0;
            $blackPhoneList = BlackListPhone::model()->getPhoneBlackList();

            foreach ($_POST['phoneblacklist'] as $k => $ph) {
                //判断手机号是否符合正则
                if (!$this->verify_phone($ph)) continue;
                //数据库查找是否存在重复数据
                if (in_array($ph, $blackPhoneList)) continue;
                //判断数组是否存在该手机号
                if (in_array($ph, $temp['phone'])) continue;
                $temp['phone'][$i] = $ph;
                $arr[$i]['phone'] = $ph;
                $arr[$i]['remark'] = $_POST['phoneremark'][$k];
            }

            foreach ($arr as $v) {
                //循环添加数据
                $info = new BlackListPhone();
                $info->phone = $v['phone'];
                $info->remark = $v['remark'];
                $info->save();
            }

            $this->logs("批量增加手机号完成");
            $this->msg(array('state' => 1, 'msgwords' => '批量增加手机号成功'));
        }
    }

    /**
     * 修改ip
     * author: hlc
     */
    public function actionChangeIp()
    {
        $id = $this->get('id');
        $info = BlackListIp::model()->findByPk($id);
        if (!$_POST) {
            $this->render('updateIpBlackList', array('ip' => $info));
        } else {
            $ip = $this->get('ip');
            $remark = $this->get('remark');

            //判断是否符合ip正则
            if (!$this->verify_ip($ip)) $this->msg(array('state' => 0, 'msgwords' => 'ip格式不正确'));

            $info->ip_adress = $ip;
            $info->remark = $remark;
            $ret = $info->save();

            $this->logs("修改ip:" . $ip . "成功");
            $msgarr = array('state' => 1, 'jscode' => "<script>setTimeout(function(){artDialog.close();}, 500)</script>");
            $ret === false ? $this->msg(array('state' => 0, 'msgword' => '修改ip失败')) : $this->msg($msgarr);
        }
    }

    /**
     * 修改phone
     * author: hlc
     */
    public function actionChangePhone()
    {
        $id = $this->get('id');
        $info = BlackListPhone::model()->findByPk($id);
        if (!$_POST) {
            $this->render('updatePhoneBlackList', array('phone' => $info));
        } else {
            $ph = $this->get('phone');
            $remark = $this->get('remark');

            if (!$this->verify_phone($ph)) $this->msg(array('state' => '0', 'msgword' => '手机号格式不正确'));

            $info->phone = $ph;
            $info->remark = $remark;
            $ret = $info->save();

            $this->logs("修改手机号:" . $ph . "成功");
            $msgarr = array('state' => 1, 'jscode' => "<script>setTimeout(function(){artDialog.close();}, 500)</script>");
            $ret === false ? $this->msg(array('state' => 0, 'msgwords' => '修改手机号失败')) : $this->msg($msgarr);
        }
    }

    /**
     * 批量删除ip
     * author: hlc
     */
    public function actionDeleteSelectIp()
    {
        $ids = $this->get('id');

        BlackListIp::model()->deleteAll('id in ('.$ids.')');

        $this->logs("批量删除ip成功");
        $this->msg(array('state' => 1, 'msgwords' => '批量删除ip成功','url'=>$this->createUrl('blackList/index?group_id=0')));
    }

    /**
     * 批量删除手机号
     * author: hlc
     */
    public function actionDeleteSelectPhone()
    {
        $ids = $this->get('id');

        BlackListPhone::model()->deleteAll('id in ('.$ids.')');

        $this->logs("批量删除手机号成功");
        $this->msg(array('state' => 1, 'msgwords' => '批量删除手机号成功','url'=>$this->createUrl('blackList/index?group_id=1')));
    }

    /**
     * 删除ip
     * author: hlc
     */
    public function actionDeleteIp()
    {
        $id = $this->get('id');

        $ret = BlackListIp::mode()->findByPk($id);
        if (!$ret)  $this->msg(array('state' => 0, 'msgwords' => '该ip已删除'));
        $this->logs('删除ip：' . $ret['ip_adress'] . '成功');

        $ret->delete();

        $this->msg(array('state' => 1, 'msgwords' => '删除ip成功','url'=>$this->createUrl('blackList/index')));
    }

    /**
     * 删除手机号
     * author: hlc
     */
    public function actionDeletePhone()
    {
        $id = $this->get('id');
        $ret = BlackListPhone::model()->findByPk($id);
        if (!$ret)  $this->msg(array('state' => 0, 'msgwords' => '该手机号已删除'));
        $this->logs('删除手机号：' . $ret['phone'] . '成功');

        $ret->delete();

        $this->msg(array('state' => 1, 'msgwords' => '删除手机号成功','url'=>$this->createUrl('blackList/index?group_id=1')));
    }

    /**
     * 导入ip黑名单
     * author: hlc
     */
    public function actionLeadIpExcel()
    {
        if (isset($_POST['submit'])) {
            //获取上传的文件实例
            $file = CUploadedFile::getInstanceByName('filename');
            if (!$file) $this->msg(array('state' => 0, 'msgwords' => '未选择文件！'));
            //判断文件类型
            if ($file->getExtensionName() != 'xls') $this->msg(array('state' => 0, 'msgwords' => '请导入.xls文件！'));
            if ($file->getType() == 'application/octet-stream' || $file->getType() == 'application/vnd.ms-excel') {
                //获取文件名
                $excelFile = $file->getTempName();
                Yii::$enableIncludePath = false;
                Yii::import('application.extensions.PHPExcel.PHPExcel', 1);
                //加载文件
                $objPHPExcel = PHPExcel_IOFactory::load($excelFile);
                $sheet = $objPHPExcel->getSheet(0);
                //取得总行数
                $highestRow = $sheet->getHighestRow();
                //取得总列数
                $highestColumn = $sheet->getHighestColumn();
                //从第二行开始读取数据
                for ($j = 2; $j <= $highestRow; $j++) {
                    $str = "";
                    //从A列读取数据
                    for ($k = 'A'; $k <= $highestColumn; $k++) {
                        //读取单元格
                        $str .= $objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue() . '|*|';
                    }
                    //字符串分割，获取数据
                    $strs = explode("|*|", $str);
                    $ip = $strs[0];
                    //判断是否符合ip正则
                    if ($this->verify_ip($ip)) {
                        //取得与数据库相同的ip
                        $ret = BlackListIp::model()->find('ip_adress="' . $strs[0] . '"');
                        //数据库查找不到相同的ip时
                        if ($ret['ip_adress'] == null) {
                            Yii::app()->db->createCommand()->insert('black_list_ip', array('ip_adress' => $strs[0], 'remark' => $strs[1]));
                        } else {
                            //重复时跳过
                            continue;
                        }
                    } else {
                        //不符合ip正则时跳过
                        continue;
                    }
                }
            } else {
                //文件类型不符合是输出
                $this->msg(array('state' => 0, 'msgwords' => '请选择要导入的xls文件！'));
            }
            $this->logs("导入ip黑名单成功");
            $this->msg(array('state' => 1, 'msgwords' => '导入ip黑名单成功'));
        }
    }

    /**
     * 导入phone黑名单
     * author: hlc
     */
    public function actionLeadPhoneExcel()
    {
        if (isset($_POST['submit'])) {
            //获取上传的文件实例
            $file = CUploadedFile::getInstanceByName('filename');
            if (!$file) $this->msg(array('state' => 0, 'msgwords' => '未选择文件！'));
            //判断文件类型
            if ($file->getExtensionName() != 'xls') $this->msg(array('state' => 0, 'msgwords' => '请导入.xls文件！'));
            if ($file->getType() == 'application/octet-stream' || $file->getType() == 'application/vnd.ms-excel') {
                //获取文件名
                $excelFile = $file->getTempName();
                Yii::$enableIncludePath = false;
                Yii::import('application.extensions.PHPExcel.PHPExcel', 1);
                //加载文件
                $objPHPExcel = PHPExcel_IOFactory::load($excelFile);
                $sheet = $objPHPExcel->getSheet(0);
                //取得总行数
                $highestRow = $sheet->getHighestRow();
                //取得总列数
                $highestColumn = $sheet->getHighestColumn();
                //从第二行开始读取数据
                for ($j = 2; $j <= $highestRow; $j++) {
                    $str = "";
                    //从A列读取数据
                    for ($k = 'A'; $k <= $highestColumn; $k++) {
                        //读取单元格
                        $str .= $objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue() . '|*|';
                    }
                    //字符串分割，获取数据
                    $strs = explode("|*|", $str);
                    //取得与数据库相同的id
                    $ph = $strs[0];
                    //手机号不相等的情况进行下一步
                    //判断是否符合ip正则
                    if ($this->verify_phone($ph)) {
                        $ret = BlackListPhone::model()->find('phone="' . $strs[0] . '"');
                        if ($ret['phone'] == null) {
                            Yii::app()->db->createCommand()->insert('black_list_phone', array('phone' => $strs[0], 'remark' => $strs[1]));
                        } else {
                            //重复时跳过
                            continue;
                        }
                    } else {
                        //不符合时跳过
                        continue;
                    }
                }
            } else {
                //文件类型不符合是输出
                $this->msg(array('state' => 0, 'msgwords' => '请选择要导入的xls文件！'));
            }
            $this->logs("导入手机号黑名单成功");
            $this->msg(array('state' => 1, 'msgwords' => '导入手机号黑名单成功'));
        }
    }

    /**
     * 下载ip模版
     * author: hlc
     */
    public function actionDownLoadIpTemplate()
    {
        $colums = array('ip地址', '备注');
        $file_name = 'ip黑名单.xls';
        helper::downloadExcel($colums, array(), '', $file_name);
    }

    /**
     * 下载手机号模板
     * author: hlc
     */
    public function actionDownLoadPhoneTemplate()
    {
        $colums = array('手机号', '备注');
        $file_name = '手机号黑名单.xls';
        helper::downloadExcel($colums, array(), '', $file_name);
    }

    /**
     * 正则验证ip
     * author: hlc
     */
    function verify_ip($ip)
    {
        $pat = "/^(?:(?:2[0-4][0-9]\.)|(?:25[0-5]\.)|(?:1[0-9][0-9]\.)|(?:[1-9][0-9]\.)|(?:[0-9]\.)){3}(?:(?:2[0-5][0-5])|(?:25[0-5])|(?:1[0-9][0-9])|(?:[1-9][0-9])|(?:[0-9]))$/";
        if (preg_match($pat, $ip)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 正则验证手机号
     * author: hlc
     */
    function verify_phone($ph)
    {
        $pat = "/^(1(([35][0-9])|(47)|[8][01236789]))\d{8}$/";
        if (preg_match($pat, $ph)) {
            return true;
        } else {
            return false;
        }
    }

    private function getIpBlackList($type = 0)
    {
        //每页显示的条数
        $params['pagesize'] = $type == 0 ? Yii::app()->params['management']['pagesize'] : 10000;
        //翻页导航栏
        $params['pagebar'] = 1;
        //全选
        $params['select'] = "*";
        $params['order'] = " order by id desc";
        $page['listdata'] = Dtable::model(BlackListIp::model()->tableName())->listdata($params);
        return $page;
    }

    private function getPhoneBlackList($type = 0)
    {
        //每页显示的条数
        $params['pagesize'] = $type == 0 ? Yii::app()->params['management']['pagesize'] : 10000;
        //翻页导航栏
        $params['pagebar'] = 1;
        //全选
        $params['select'] = "*";
        $page['listdata'] = Dtable::model(BlackListPhone::model()->tableName())->listdata($params);
        return $page;
    }
}