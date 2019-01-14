<?php require(dirname(__FILE__)."/../common/head.php");
?>
<div class="main mhead" xmlns="http://www.w3.org/1999/html">
    <div class="snav">财务管理 »  人员投入	</div>

    <div class="mt10">
        <form action="<?php echo $this->createUrl('personPut/index'); ?>">日期：
            <input type="text" id="start_date" class="ipt" style="width: 120px;font-size: 15px;" name="start_date" value="<?php echo $this->get('start_date')==''?date('Y-m-d',strtotime("-9 day")):$this->get('start_date'); ?>"  placeholder="起始日期" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/> 一
            <input type="text" id="end_date" class="ipt" style="width: 120px;font-size: 15px;" name="end_date" value="<?php echo $this->get('end_date')==''?date('Y-m-d'):$this->get('end_date'); ?>"  placeholder="结束日期" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>

            业务类型：
            <?php
            $businessTypes = Dtable::toArr(BusinessTypes::model()->findAll());
            echo CHtml::dropDownList('business_type', $this->get('business_type'), CHtml::listData($businessTypes, 'bid', 'bname'),
                array(
                    'empty' => '请选择',
                )
            );
            ?>&nbsp;
            计费方式：
            <select id="chgId" name="chgId">
                <option value="">请选择</option>
                <?php $chargeList = vars::$fields['charging_type'];
                foreach ($chargeList as $key => $val) { ?>
                    <option
                        value="<?php echo $val['value']; ?>" <?php if ($_GET['chgId'] != '' && $_GET['chgId'] == $val['value']) echo 'selected'; ?>><?php echo $val['txt']; ?></option>
                <?php } ?>
            </select>
            <input type="submit" class="but" value="查询"  >

        </form>
        <div class="mt10 clearfix">
            <div class="l">
            </div>
            <div class="r">

            </div>
        </div>
    </div>
    <div class="main mbody">
        <form action="<?php echo $this->createUrl('personPut/saveOrder'); ?>" name="form_order" method="post">
            <table class="tb fixTh">
                <thead>
                <tr>
                    <th width="100">推广人员</th>
                    <?php
                    $start_m=date('m',strtotime($this->get('start_date')));
                    $end_m=date('m',strtotime($this->get('end_date')));
                    if ($start_m!=$end_m){
                        $total_show=date('d',strtotime($this->get('end_date')));
                    }else{
                        $total_show=$this->get('end_date')==''?'10':(strtotime($this->get('end_date'))-strtotime($this->get('start_date')))/(24*60*60)+1;
                    }
                    for ($i=0;$i<$total_show;$i++){
                        if ($this->get('end_date')==''){
                            $date_l=date('d',strtotime('-'.$i.' day'));
                        }else{
                            $date_l=date('d',strtotime($this->get('end_date'))-$i*24*60*60);
                        }
                        echo '<th width="80">'.$date_l.'日</th>';
                    }
                    ?>
                    <th width="100">合计</th>
                </tr>
                </thead>
                <?php
                $condition='';
                if($this->get('business_type')) $condition .= " AND business_type={$this->get('business_type')}";
                if($this->get('chgId')) $condition .= " AND charging_type={$this->get('chgId')}";

                foreach($page['listdata']['list'] as $r){
                    ?>
                    <tr>
                        <td>
                            <?php echo $r['name'];?>
                        </td>
                        <?php
                        if ($start_m!=$end_m){
                            $total_show=date('d',strtotime($this->get('end_date')));
                        }else{
                            $total_show=$this->get('end_date')==''?'10':(strtotime($this->get('end_date'))-strtotime($this->get('start_date')))/(24*60*60)+1;
                        }
                        $sum_cost=0;
                        for ($i=0;$i<$total_show;$i++){
                            if ($this->get('end_date')==''){
                                $date_l=date('d',strtotime('-'.$i.' day'));
                                $date_s=strtotime(date('Ymd',strtotime('-'.$i.' day')));
                                $date_e=$date_s+24*60*60-1;
                            }else{
                                $date_l=date('d',strtotime($this->get('end_date'))-$i*24*60*60);
                                $date_s=strtotime(date('Ymd',strtotime($this->get('end_date'))-$i*24*60*60));
                                $date_e=$date_s+24*60*60-1;
                            }
                            $sql_detail="select SUM(money) as money from stat_cost_detail WHERE tg_uid={$r['tg_uid']} AND stat_date BETWEEN $date_s AND $date_e".$condition;
                            $cost_detail=Yii::app()->db->createCommand($sql_detail)->queryAll();
                            $sql_fixed="select SUM(fixed_cost) as fixed_cost from fixed_cost_new WHERE tg_uid={$r['tg_uid']} AND stat_date BETWEEN $date_s AND $date_e".$condition;
                            $cost_fixed=Yii::app()->db->createCommand($sql_fixed)->queryAll();

                            $cost_person=0;
                            $cost_person = $cost_detail[0]['money'] + $cost_fixed[0]['fixed_cost'];
                            $cost_person=round($cost_person,2);
                            echo '<td>'.$cost_person.'</td>';
                            $sum_cost = $sum_cost+$cost_person;
                        }
                        ?>
                        <td><?php echo $sum_cost ?></td>
                    </tr>
                    <?php
                } ?>

            </table>
            <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
            <div class="clear"></div>
        </form>
    </div>

