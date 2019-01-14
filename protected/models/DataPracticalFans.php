<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/24
 * Time: 11:46
 */

class DataPracticalFans extends CActiveRecord
{
    public function tableName()
    {
        return '{{data_practical_fans}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    /**
     * 发货订单统计更新
     * @param $data array
     */
    public function updatePracticalFans($data)
    {
        $tg_uids = array_column($data,'tg_uid');
        //排除推广id为空数据
        foreach ($tg_uids as $key=>$value) {
            if (!trim($value)) {
                unset($tg_uids[$key]);
            }
        }
        if ($tg_uids) {
            $tg_uids = array_column($data,'tg_uid');
            $sql = " SELECT sno,groupid  FROM cservice_groups WHERE sno in (".implode(',',$tg_uids).") group by  sno";
            $user_groups = Yii::app()->db->createCommand($sql)->queryAll($sql);
            $tg_group = array_combine(array_column($user_groups,'sno'),array_column($user_groups,'groupid'));
            foreach ($data as $value) {
                if (trim($value['tg_uid'])) {
                    $where = 'addfan_date='.$value['addfan_date'].' and customer_service_id='.$value['customer_service_id'].' and tg_uid='.$value['tg_uid'] ;
                    $table = 'fans_input_manage';
                    $sql = " SELECT SUM(addfan_count) AS addfan_count  FROM ".$table.' WHERE '.$where;
                    $fan_count = Yii::app()->db->createCommand($sql)->queryAll($sql);
                    $fan = $this::model()->find('service_id='.$value['customer_service_id'].' and tg_uid='.$value['tg_uid'].' and date='.$value['addfan_date']);
                    if (!$fan) {
                        $fan = new DataPracticalFans();
                        $fan->service_id = $value['customer_service_id'];
                        $fan->tg_uid = $value['tg_uid'];
                        $fan->date = $value['addfan_date'];
                        $fan->add_time = time();
                    }
                    $fan->fans = $fan_count[0]['addfan_count'] ? $fan_count[0]['addfan_count']:0;
                    $fan->group_id = $tg_group[$value['tg_uid']];
                    $fan->add_time = time();
                    $fan->save();
                }
            }
        }

    }

    /**
     * 编辑发货订单时更新统计信息
     * @param $old_data
     * @param $new_data
     * @param $order_type
     */

    public function editFans($old_data,$new_data)
    {
        $total_data = array();
        //修改发货日期、客服部
        if ($old_data['addfan_date'] != $new_data['addfan_date'] || $old_data['customer_service_id'] != $new_data['customer_service_id']) {
            $total_data[] = $old_data;
            $total_data[] = $new_data;
        } else {
            $total_data[] = $new_data;
        }
        $this->updatePracticalFans($total_data);
    }
}