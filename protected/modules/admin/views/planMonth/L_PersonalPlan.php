<?php require(dirname(__FILE__) . "/../common/head.php"); ?>

    <?php
    $uid = Yii::app()->admin_user->uid;
    $authority =AdminUser::model()->getUserAuthority($uid);
    ?>
    <input name="month" hidden value="<?php echo $next_month; ?>" id="next_month">
            <?php foreach($page['list'] as $key => $value){ ?>
                <table class="tb" style="width: 800px;border: solid 1px #CCCCCC;">
                <tr>
                  <td><span style="float: left;<?php if( $value['status'] == 1 || $value['status'] == 3){echo "color:red";} ?>">
                          <?php if ( $value['status'] == 1 ) {
                              if($authority == 2){
                                  echo "待修改";
                              }else{
                                  echo "组长审核不通过,待修改";
                              }
                          }if ( $value['status'] == 3) {
                              if($authority == 0){
                                  echo "待修改";
                              }elseif($authority == 2){
                                  echo "经理审核不通过,待修改";
                              }elseif($authority == 3){
                                  echo "经理审核不通过,待修改";
                              }
                          } else if ($value['status'] == 2) {
                              if ($value['through_time'] < $value['update_time'] && $value['through_time']>0) {
                                  if($authority == 0){
                                      echo "待审核";
                                  }elseif($authority == 2){
                                      echo "同意";
                                  }elseif($authority == 3){
                                      echo "待经理审核";
                                  }
                              }else{
                                  echo '组长审核通过';
                              }
                          } else if ($value['status'] == 4) {
                              echo '审核通过';
                          }else if ($value['status'] == 0) {
                              if ($value['through_time'] < $value['update_time'] && $value['through_time']>0) {
                                  if($authority == 0){
                                      echo "待组长审核";
                                  }elseif($authority == 2){
                                      echo "待审核";
                                  }elseif($authority == 3){
                                      echo "待组长审核";
                                  };
                              }else{
                                  echo '待审核';
                              }
                          }?>
                      </span></td>
                  <td colspan="2">
                      <div style="text-align: center;">
                          <span><?php echo $value['name'].'-';?></span>
                          <span id="month"><?php echo date('Y-m',$value['month']); ?></span>
                          <span><?php echo '月'.'-'.'进粉计划'; ?></span>
                      </div>
                  </td>
                    <td>
                        <?php if($value['tg_uid'] == $uid){ ?>
                            <a href="<?php echo $this->createUrl('planMonth/edit?id='.$value['id'].'&tg_uid='.$value['tg_uid']) ?>" style="float: right" class="but2">修改</a>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <th style="width: 200px;">客服部</th>
                    <th style="width: 200px;">微信号个数</th>
                    <th style="width: 200px;">计划进粉</th>
                    <th style="width: 200px;">计划产值</th>
                </tr>
                    <?php for ($i=0;$i<$value['num'];$i++){ ?>
                    <tr>
                    <td style="text-align: center">
                        <?php
                        echo helper::getServiceSelect2($value['data'][$i]['cs_id'],1);
                        ?>
                    </td>
                    <td style="text-align: center">
                         <span name="weChat_num[]"><?php echo $value['data'][$i]['weChat_num'];  ?></span>
                    </td>
                    <td style="text-align: center">
                        <span name="fans_count[]"><?php echo $value['data'][$i]['fans_plan'];  ?></span>
                    </td>
                    <td style="text-align: center">
                        <span name="output[]"><?php echo $value['data'][$i]['output_plan'];  ?></span>
                    </td>
                    </tr>
                    <?php } ?>
                    <tr >
                    <td colspan="4" style="text-align: left;" name="remark"><?php echo $value['remark']; ?></td>
                </tr>
                    <?php if($value['unthrough_msg'] && $value['status'] !=4){ ?>
                        <tr><td colspan="4" style="text-align: left;" name="unthrough_msg">拒绝理由:<?php echo $value['unthrough_msg']; ?></td> </tr>
                    <?php } ?>

                    <tr style="height: 10px;"></tr>
        </table>
                <?php } ?>

