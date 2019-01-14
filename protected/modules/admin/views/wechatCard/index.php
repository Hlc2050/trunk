<?php require(dirname(__FILE__)."/../common/head.php");
?>
<div class="main mhead" xmlns="http://www.w3.org/1999/html">
    <div class="snav">推广管理 »  微信号打卡	</div>
     
     <div class="mt10">
    <form action="<?php echo $this->createUrl('wechatCard/index'); ?>">日期：
        <input type="text" id="start_date" class="ipt" style="width: 120px;font-size: 15px;" name="start_date" value="<?php echo $this->get('start_date')==''?date('Y-m-d',strtotime("-9 day")):$this->get('start_date'); ?>"  placeholder="起始日期" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/> 一
        <input type="text" id="end_date" class="ipt" style="width: 120px;font-size: 15px;" name="end_date" value="<?php echo $this->get('end_date')==''?date('Y-m-d'):$this->get('end_date'); ?>"  placeholder="结束日期" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
            客服部：
            <?php
            $params['htmlOptions'] = array('empty' => '请选择', 'id' => 'csid');
            helper::getServiceSelect('csid',$params);
            ?>&nbsp;
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
<form action="<?php echo $this->createUrl('wechatCard/saveOrder'); ?>" name="form_order" method="post">
<table class="tb fixTh">
    <thead>
        <tr>
            <th width="100">微信号</th>
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
        </tr>
    </thead>
   <?php
   foreach($page['listdata']['list'] as $r){
       $infanceList = '';
       //var_dump($r['business_type']);
       if ($r['business_type']!=1){
           $sql="select c.online_date,c.id as f_id from wechat_relation as a left join wechat_group as b on b.id=a.wechat_group_id 
              left join finance_pay as c on c.weixin_group_id=b.id where a.wid = {$r['id']}";
           $infanceList = Yii::app()->db->createCommand($sql)->queryAll();
           //var_dump($infanceList);
           //if ($infanceList)echo 1;
           //echo "<br>";
           if ($infanceList){
               $online_date=array();
               foreach ($infanceList as $k=>$v){
                   //$infance=InfancePay::model()->findByPk($infanceList[$k]['f_id']);
                   $promotion=Promotion::model()->findByAttributes(array('finance_pay_id'=>$infanceList[$k]['f_id']));
                   if(!$promotion) continue;
                   if ($promotion->outline_date != 0){
                       $online_date[] = array ($infanceList[$k]['online_date'],$promotion->outline_date);
                   }else{
                       $online_date[] = array ($infanceList[$k]['online_date']);
                   }
               }
               /*echo "<pre>";
               var_dump($online_date);
               echo "</pre>";*/
           }else{
               $flag = 0;
           }
       }
   ?>
    <tr>
        <td>
            <?php echo $r['wechat_id']?>
        </td>
        <?php
        if ($start_m!=$end_m){
            $total_show=date('d',strtotime($this->get('end_date')));
        }else{
            $total_show=$this->get('end_date')==''?'10':(strtotime($this->get('end_date'))-strtotime($this->get('start_date')))/(24*60*60)+1;
        }        $sum_cost=0;
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
            if ($r['business_type']==1){
                /*$sql="select * from stat_cost_detail WHERE weixin_id={$r['id']} AND stat_date BETWEEN $date_s AND $date_e";
                $a=Yii::app()->db->createCommand($sql)->queryAll();
                if (count($a)>0){
                    echo '<td>1</td>';
                }else{
                    echo '<td>0</td>';
                }*/
                $flag_=0;
                foreach ( $page['data']['cnzz'] as $k=>$v ){
                    if ($v['weixin_id']==$r['id'] && $v['stat_date']==$date_s)$flag_=1;
                }
                echo $flag_?'<td>1</td>':'<td>0</td>';
            } else{
                $flag =0;
                foreach ($online_date as $v){
                    if(count($v) == 2){
                        $start_date = $v[0];
                        $end_date = $v[1];
                        if($date_s >= $start_date && $date_s <= $end_date-24*3600) {
                            $flag = 1;
                            break;
                        }
                    }elseif(count($v) == 1){
                        $start_date = $v[0];
                        if($date_s >= $start_date) {
                            $flag = 1;
                            break;
                        }
                    }

                }
                echo $flag?'<td>1</td>':'<td>0</td>';

            }

        }
        ?>
    </tr>
   <?php
   } ?>

    
</table>
  <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
  <div class="clear"></div>
</form>
</div>

