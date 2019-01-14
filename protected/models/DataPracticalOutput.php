<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/24
 * Time: 11:46
 */

class DataPracticalOutput extends CActiveRecord
{
    public function tableName()
    {
        return '{{data_practical_output}}';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    /**
     * 发货订单统计更新
     * @param $data array
     * $data['customer_service_id']
     * $data['tg_uid']
     * $data['delivery_date']
     * @param  $order_type int 1:普通订单发货2:独立订单发货
     */
    public function updatePracticalOutput($data,$order_type=2)
    {
        $tg_uids = array_column($data,'tg_uid');
        //排除推广id为空数据
        foreach ($tg_uids as $key=>$value) {
            if (!trim($value)) {
                unset($tg_uids[$key]);
            }
        }
        if ($tg_uids) {
            $sql = " SELECT sno,groupid  FROM cservice_groups WHERE sno in (".implode(',',$tg_uids).") group by  sno";
            $user_groups = Yii::app()->db->createCommand($sql)->queryAll($sql);
            $tg_group = array_combine(array_column($user_groups,'sno'),array_column($user_groups,'groupid'));
            foreach ($data as $value) {
                if (trim($value['tg_uid'])) {
                    $where = 'delivery_date='.$value['delivery_date'].' and customer_service_id='.$value['customer_service_id'].' and tg_uid='.$value['tg_uid'];
                    if ($order_type == 1) {
                        $where .=' and delivery_status=1';
                    }
                    if ($order_type == 1) {
                        $table = 'delivery_norm_order_manage';
                    }else {
                        $table = 'delivery_indep_order_manage';
                    }
                    $sql = " SELECT SUM(delivery_money) AS delivery_money  FROM ".$table.' WHERE '.$where;
                    $deliver = Yii::app()->db->createCommand($sql)->queryAll($sql);
                    $out = $this::model()->find('service_id='.$value['customer_service_id'].' and tg_uid='.$value['tg_uid'].' and date='.$value['delivery_date']);
                    if (!$out) {
                        $out = new DataPracticalOutput();
                        $out->service_id = $value['customer_service_id'];
                        $out->tg_uid = $value['tg_uid'];
                        $out->date = $value['delivery_date'];
                    }
                    $out->output = $deliver[0]['delivery_money'] ? $deliver[0]['delivery_money']:0;
                    $out->group_id = $tg_group[$value['tg_uid']];
                    $out->add_time = time();
                    $out->save();
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

    public function editOutput($old_data,$new_data,$order_type=2)
    {
        $total_data = array();
        //修改发货日期、客服部
        if ($old_data['delivery_date'] != $new_data['delivery_date'] || $old_data['customer_service_id'] != $new_data['customer_service_id']) {
            $total_data[] = $old_data;
            $total_data[] = $new_data;
        } else {
            $total_data[] = $new_data;
        }
        $this->updatePracticalOutput($total_data,$order_type);
    }
}